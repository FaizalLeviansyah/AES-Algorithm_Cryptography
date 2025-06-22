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
        Schema::create('files', function (Blueprint $table) {
            $table->id('id_file'); // Menggunakan 'id_file' sebagai primary key
            $table->string('file_name_source');
            $table->string('file_name_finish');
            $table->string('file_path');
            $table->string('file_size');
            $table->text('password')->nullable(); // Ini adalah kolom 'keterangan' Anda
            $table->timestamp('tgl_upload')->nullable();
            $table->string('username');
            $table->string('status')->default('1');
            $table->string('bit'); // (128 atau 256)

            // Kolom 'kunci' sengaja tidak dibuat untuk alasan keamanan.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
