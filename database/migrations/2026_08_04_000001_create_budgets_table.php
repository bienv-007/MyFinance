<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table): void {
            $table->id('id_budget');
            $table->foreignId('id_utilisateur')->constrained('utilisateurs', 'id_utilisateur')->cascadeOnDelete();
            $table->string('periode');
            $table->decimal('montant_total', 12, 2);
            $table->date('date_debut');
            $table->date('date_fin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
