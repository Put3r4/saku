<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criteria', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // Contoh: C1, C2, C3
            $table->string('nama_kriteria'); // Contoh: Kandungan Kalori, Protein
            $table->enum('tipe', ['benefit', 'cost']); // Keuntungan (makin besar makin baik) atau Biaya (makin kecil makin baik)
            $table->decimal('bobot', 5, 2); // Nilai persentase/desimal bobot, contoh: 0.30 untuk 30%
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criteria');
    }
};