<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->integer('numeroCiclo');
            $table->integer('ano');
            $table->integer('numeroGrupo');
            $table->boolean('abierto')->default(false);
            $table->foreignId('curso_id')->nullable()->constrained('cursos')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('grupo_docente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();
            $table->foreignId('docente_id')->constrained('docentes')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupo_docente');
        Schema::dropIfExists('grupos');
    }
};
