<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depense_previsions', function (Blueprint $table): void {
            $table->id('id_depense_prevision');
            $table->foreignId('id_utilisateur')->constrained('utilisateurs', 'id_utilisateur')->cascadeOnDelete();
            $table->foreignId('id_categorie')->constrained('categories', 'id_categorie')->restrictOnDelete();
            $table->decimal('montant_previsionnel', 12, 2);
            $table->date('date_previsionnelle');
            $table->text('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depense_previsions');
    }
};
