<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('role_permiso', function (Blueprint $table) {
            // Claves foráneas
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('permiso_id')->constrained('permisos')->onDelete('cascade');

            // Timestamps (opcional)
            $table->timestamps();

            // Clave primaria compuesta
            $table->primary(['role_id', 'permiso_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('role_permiso');
    }
};
