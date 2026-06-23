<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Majors
        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., RPL, TKJ
            $table->string('name'); // e.g., Rekayasa Perangkat Lunak
            $table->timestamps();
        });

        // 2. Classes
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('class_name'); // e.g., X RPL 1
            $table->foreignId('major_id')->constrained('majors')->onDelete('cascade');
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 3. Students
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nisn')->unique();
            $table->string('name');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('parent_name');
            $table->string('parent_phone'); // WhatsApp number
            $table->integer('current_points')->default(0);
            $table->string('status')->default('aktif'); // aktif, skorsing, drop_out, lulus
            $table->string('tahun_ajaran')->default('2025/2026');
            $table->timestamps();
        });

        // 4. Violations
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->string('violation_name');
            $table->string('category'); // ringan, sedang, berat
            $table->integer('points');
            $table->timestamps();
        });

        // 5. Violation Logs
        Schema::create('violation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('violation_id')->constrained('violations')->onDelete('cascade');
            $table->integer('points_added');
            $table->date('date_occurred');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // recording teacher
            $table->timestamps();
        });

        // 6. WhatsApp Queue
        Schema::create('wa_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('violation_log_id')->constrained('violation_logs')->onDelete('cascade');
            $table->string('phone_number');
            $table->text('message_body');
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wa_queue');
        Schema::dropIfExists('violation_logs');
        Schema::dropIfExists('violations');
        Schema::dropIfExists('students');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('majors');
    }
};
