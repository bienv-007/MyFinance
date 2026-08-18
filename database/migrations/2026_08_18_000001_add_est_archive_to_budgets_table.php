<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('budgets', function (Blueprint $table): void {
            $table->boolean('est_archive')->default(false)->after('solde');
            $table->index(['id_utilisateur', 'est_archive'], 'budgets_user_archive_index');
        });

        Schema::table('budgets', function (Blueprint $table): void {
            $table->dropUnique('budgets_id_utilisateur_unique');
        });
    }

    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table): void {
            $table->unique('id_utilisateur', 'budgets_id_utilisateur_unique');
        });

        Schema::table('budgets', function (Blueprint $table): void {
            $table->dropIndex('budgets_user_archive_index');
            $table->dropColumn('est_archive');
        });
    }
};
