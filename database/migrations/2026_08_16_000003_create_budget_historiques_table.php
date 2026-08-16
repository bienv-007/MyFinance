<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budget_historiques', function (Blueprint $table): void {
            $table->id('id_budget_historique');
            $table->foreignId('id_budget')->constrained('budgets', 'id_budget')->cascadeOnDelete();
            $table->foreignId('id_utilisateur')->constrained('utilisateurs', 'id_utilisateur')->cascadeOnDelete();
            $table->string('periode');
            $table->decimal('montant_total', 12, 2);
            $table->decimal('solde_final', 12, 2);
            $table->decimal('montant_depense', 12, 2)->default(0);
            $table->date('date_debut');
            $table->date('date_fin');
            $table->timestamp('date_archivage')->useCurrent();
        });

        Schema::table('depenses', function (Blueprint $table): void {
            $table->foreignId('id_budget')->nullable()->after('id_utilisateur')->constrained('budgets', 'id_budget')->nullOnDelete();
            $table->foreignId('id_budget_historique')->nullable()->after('id_budget')->constrained('budget_historiques', 'id_budget_historique')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('depenses', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('id_budget_historique');
            $table->dropConstrainedForeignId('id_budget');
        });
        Schema::dropIfExists('budget_historiques');
    }
};
