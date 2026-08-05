<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revenu_previsions', function (Blueprint $table): void {
            $table->id('id_revenu_prevision');
            $table->foreignId('id_utilisateur')->constrained('utilisateurs', 'id_utilisateur')->cascadeOnDelete();
            $table->decimal('montant_previsionnel', 12, 2);
            $table->string('source_previsionnelle');
            $table->date('date_previsionnelle');
            $table->text('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenu_previsions');
    }
};
