<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $sqlFile = base_path('agence.sql');

        if (!File::exists($sqlFile)) {
            throw new \Exception("El archivo agence.sql no se encontró en la raíz del proyecto");
        }

        $sql = File::get($sqlFile);

        // Ejecutar sin transacción (el SQL ya maneja sus propias transacciones)
        DB::unprepared($sql);
    }

    public function down()
    {
        // Obtener todas las tablas para hacer rollback completo
        $tables = DB::select('SELECT name FROM sqlite_master WHERE type="table"');

        DB::statement('PRAGMA foreign_keys = OFF');
        foreach ($tables as $table) {
            if ($table->name !== 'sqlite_sequence') {  // Ignorar tabla de secuencias
                DB::statement("DROP TABLE IF EXISTS {$table->name}");
            }
        }
        DB::statement('PRAGMA foreign_keys = ON');
    }
};
