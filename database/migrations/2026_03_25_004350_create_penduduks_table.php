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
        Schema::create('penduduks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kartu_keluarga_id')->constrained('kartu_keluargas')->onDelete('cascade');
            $table->string('nik', 16)->unique();
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('agama', ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha', 'Konghucu', 'Lainnya']);
            $table->enum('status_perkawinan', ['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati']);
            $table->string('pekerjaan')->nullable();
            $table->enum('pendidikan_terakhir', ['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'Diploma', 'S1', 'S2', 'S3']);
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O'])->nullable();
            $table->enum('status_dalam_keluarga', ['Kepala Keluarga', 'Suami', 'Istri', 'Anak', 'Menantu', 'Cucu', 'Orang Tua', 'Mertua', 'Famili Lain', 'Pembantu', 'Lainnya']);
            $table->enum('kewarganegaraan', ['WNI', 'WNA'])->default('WNI');
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['Aktif', 'Meninggal', 'Pindah'])->default('Aktif');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('nik');
            $table->index('nama');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penduduks');
    }
};
