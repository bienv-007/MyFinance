<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depenses', function (Blueprint $table) {
            $table->id('id_depense');
            $table->foreignId('id_utilisateur')->constrained('utilisateurs', 'id_utilisateur')->cascadeOnDelete();
            $table->foreignId('id_categorie')->constrained('categories', 'id_categorie')->restrictOnDelete();
            $table->decimal('montant', 12, 2);
            $table->date('date_depense');
            $table->text('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};
