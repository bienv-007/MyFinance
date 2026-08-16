<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revenus', function (Blueprint $table): void {
            $table->foreignId('id_budget')->nullable()->after('id_utilisateur')->constrained('budgets', 'id_budget')->nullOnDelete();
            $table->foreignId('id_budget_historique')->nullable()->after('id_budget')->constrained('budget_historiques', 'id_budget_historique')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('revenus', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('id_budget_historique');
            $table->dropConstrainedForeignId('id_budget');
        });
    }
};
