const { default: makeWASocket, useMultiFileAuthState } = require("@whiskeysockets/baileys");
const path = require("path");
const pino = require("pino");

async function run() {
    console.log("Menghubungkan ke WhatsApp untuk tes...");
    const { state } = await useMultiFileAuthState(path.join(__dirname, 'auth_info_baileys'));
    const sock = makeWASocket({
        auth: state,
        logger: pino({ level: "silent" })
    });
    
    sock.ev.on("connection.update", async (update) => {
        const { connection, lastDisconnect } = update;
        if (connection === "open") {
            console.log("Terhubung! Mengirim pesan tes...");
            const jid = "6287798882268@s.whatsapp.net";
            try {
                await sock.sendMessage(jid, { text: "Halo! Ini adalah pesan tes diagnosa dari sistem Poin Pelanggaran SMKN 2 Jakarta." });
                console.log("Pesan berhasil terkirim ke " + jid);
                setTimeout(() => process.exit(0), 2000);
            } catch (e) {
                console.error("Gagal mengirim:", e);
                process.exit(1);
            }
        } else if (connection === "close") {
            console.log("Koneksi ditutup.");
            const statusCode = lastDisconnect?.error?.output?.statusCode;
            console.log("Status code ditutup:", statusCode);
            if (statusCode !== 401 && statusCode !== 403) {
                // Reconnect if not logged out
            } else {
                process.exit(1);
            }
        }
    });
}
run();
