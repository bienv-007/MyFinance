<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table): void {
            $table->id('id_notification');
            $table->foreignId('id_utilisateur')->constrained('utilisateurs', 'id_utilisateur')->cascadeOnDelete();
            $table->string('type', 80);
            $table->string('titre');
            $table->text('contenu');
            $table->boolean('est_lue')->default(false);
            $table->timestamp('date_notification')->useCurrent();
            $table->index(['id_utilisateur', 'est_lue']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
