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
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs');
            $table->foreignId('periode_id')->constrained('periodes');
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('kode');
            $table->string('nama_kegiatan');
            $table->decimal('pagu')->default(0);
            $table->enum('status', ['Dibuka', 'Dikunci']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatans');
    }
};
