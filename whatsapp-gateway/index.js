const {
    default: makeWASocket,
    DisconnectReason,
    useMultiFileAuthState,
    fetchLatestBaileysVersion
} = require("@whiskeysockets/baileys");
const pino = require("pino");
const express = require("express");
const cors = require("cors");
const qrcodeTerminal = require("qrcode-terminal");
const path = require("path");
const fs = require("fs");

const app = express();
app.use(cors());
app.use(express.json());

let sock = null;
let connectionStatus = "disconnected"; // disconnected, connecting, connected
let qrCodeString = "";

async function connectToWhatsApp() {
    try {
        const { state, saveCreds } = await useMultiFileAuthState(path.join(__dirname, 'auth_info_baileys'));
        
        // Fetch the latest WhatsApp Web version to prevent protocol/disconnection loops
        console.log("Mendapatkan versi WhatsApp Web terbaru...");
        const { version, isLatest } = await fetchLatestBaileysVersion().catch(() => {
            return { version: [2, 3000, 1017578276], isLatest: false }; // fallback version
        });
        console.log(`Menggunakan WA Web v${version.join('.')}, isLatest: ${isLatest}`);

        sock = makeWASocket({
            version,
            auth: state,
            printQRInTerminal: false,
            logger: pino({ level: "warn" })
        });

        sock.ev.on("creds.update", saveCreds);

        sock.ev.on("messages.update", (updates) => {
            console.log("Update status pesan:", JSON.stringify(updates));
        });

        sock.ev.on("connection.update", async (update) => {
            const { connection, lastDisconnect, qr } = update;
            
            if (qr) {
                qrCodeString = qr;
                connectionStatus = "disconnected";
                console.log("\n=======================================================");
                console.log("SILAKAN SCAN QR CODE BERIKUT DENGAN WHATSAPP ANDA:");
                console.log("=======================================================\n");
                qrcodeTerminal.generate(qr, { small: true });
                console.log("\n=======================================================\n");
            }

            if (connection === "close") {
                qrCodeString = "";
                connectionStatus = "disconnected";
                
                const statusCode = lastDisconnect?.error?.output?.statusCode;
                const errorDetail = lastDisconnect?.error;
                
                console.log(`Koneksi terputus dengan status code: ${statusCode}`);
                console.error("Detail Error:", errorDetail);
                
                // Always reconnect to allow new QR scans (even if logged out)
                console.log("Menghubungkan ulang dalam 5 detik...");
                setTimeout(connectToWhatsApp, 5000);
            } else if (connection === "open") {
                qrCodeString = "";
                connectionStatus = "connected";
                console.log("\n>>> WHATSAPP GATEWAY BERHASIL TERHUBUNG! <<<");
                console.log(`Connected user: ${JSON.stringify(sock.user)}\n`);
            } else if (connection === "connecting") {
                connectionStatus = "connecting";
                console.log("Menghubungkan ke WhatsApp...");
            }
        });
    } catch (e) {
        console.error("Gagal menginisialisasi socket WhatsApp:", e);
        setTimeout(connectToWhatsApp, 5000);
    }
}

// Start WhatsApp Client
connectToWhatsApp();

// HTTP Routes
// 1. Get Connection Status & QR Code (for Laravel dashboard integration)
app.get("/status", (req, res) => {
    res.json({
        status: connectionStatus,
        qr: qrCodeString
    });
});

app.get("/check/:phone", async (req, res) => {
    try {
        if (connectionStatus !== "connected") {
            return res.status(503).json({ success: false, error: "WhatsApp Gateway is not connected" });
        }
        let phone = req.params.phone.replace(/[^0-9]/g, "");
        if (phone.startsWith("0")) {
            phone = "62" + phone.slice(1);
        }
        const result = await sock.onWhatsApp(phone);
        res.json({ success: true, result: result });
    } catch (error) {
        res.status(500).json({ success: false, error: error.message });
    }
});

// 2. Logout (disconnect session and delete credentials to connect another number)
app.post("/logout", async (req, res) => {
    console.log("Menerima permintaan logout...");
    try {
        if (sock) {
            await sock.logout().catch(() => {});
        }
        
        // As a failsafe, delete the credentials file manually
        const credsPath = path.join(__dirname, 'auth_info_baileys', 'creds.json');
        if (fs.existsSync(credsPath)) {
            fs.unlinkSync(credsPath);
            console.log("File credentials berhasil dihapus.");
        }

        connectionStatus = "disconnected";
        qrCodeString = "";
        
        res.json({ success: true, message: "Berhasil logout, siap untuk scan ulang." });
    } catch (error) {
        console.error("Gagal melakukan logout:", error);
        res.status(500).json({ success: false, error: error.message });
    }
});

// 3. Send WhatsApp Message
app.post("/send", async (req, res) => {
    const { phone, message } = req.body;

    if (!phone || !message) {
        return res.status(400).json({ success: false, error: "Phone number and message are required" });
    }

    if (connectionStatus !== "connected") {
        return res.status(503).json({ success: false, error: "WhatsApp Gateway is not connected" });
    }

    try {
        // Format phone number to international format
        let formattedPhone = phone.replace(/[^0-9]/g, "");
        if (formattedPhone.startsWith("0")) {
            formattedPhone = "62" + formattedPhone.slice(1);
        } else if (formattedPhone.startsWith("8")) {
            formattedPhone = "62" + formattedPhone;
        }

        const jid = `${formattedPhone}@s.whatsapp.net`;
        
        console.log(`Mengirim pesan ke: ${jid}`);
        const result = await sock.sendMessage(jid, { text: message });
        console.log("Hasil sendMessage:", JSON.stringify(result));
        res.json({ success: true, message: "Pesan terkirim" });
    } catch (error) {
        console.error("Gagal mengirim pesan:", error);
        res.status(500).json({ success: false, error: error.message });
    }
});

const PORT = 3000;
// Bind explicitly to 127.0.0.1 to avoid IPv6/IPv4 address resolution mismatches
app.listen(PORT, '127.0.0.1', () => {
    console.log(`WhatsApp Gateway Service running on http://127.0.0.1:${PORT}`);
});
