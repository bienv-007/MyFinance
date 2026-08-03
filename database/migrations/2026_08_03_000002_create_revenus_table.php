<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revenus', function (Blueprint $table) {
            $table->id('id_revenu');
            $table->foreignId('id_utilisateur')->constrained('utilisateurs', 'id_utilisateur')->cascadeOnDelete();
            $table->decimal('montant', 12, 2);
            $table->string('source');
            $table->date('date_revenu');
            $table->text('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revenus');
    }
};
