<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProspectosSeeder extends Seeder
{
    public function run(): void
    {
        // Usamos Faker en español (Perú) para nombres latinos
        $faker = Faker::create('es_PE');

        // ==========================================
        // GRUPO 1: Asignados al Asesor ID 2
        // ==========================================
        for ($i = 0; $i < 10; $i++) {
            Cliente::create([
                'nombre'           => $faker->firstName,
                'apellido'         => $faker->lastName,
                'email'            => $faker->unique()->safeEmail,
                'telefono'         => '9' . $faker->numerify('########'), // Celular Perú
                'tipo_documento'   => 'DNI',
                'numero_documento' => $faker->unique()->numerify('########'), // 8 dígitos
                'direccion'        => $faker->address,
                'fecha_nacimiento' => $faker->date('Y-m-d', '2005-01-01'), // Mayores de edad
                
                // ESTADO CLAVE: PROSPECTO (Aún no es alumno)
                'estado'           => 'Prospecto',
                
                // SIN USUARIO TODAVÍA (Se crea al firmar contrato)
                'user_id'          => null, 
                
                // ASIGNACIÓN AL ASESOR 1
                'creado_por_vendedor_id' => 2, 
            ]);
        }

        // ==========================================
        // GRUPO 2: Asignados al Asesor ID 3
        // ==========================================
        for ($i = 0; $i < 10; $i++) {
            Cliente::create([
                'nombre'           => $faker->firstName,
                'apellido'         => $faker->lastName,
                'email'            => $faker->unique()->safeEmail,
                'telefono'         => '9' . $faker->numerify('########'),
                'tipo_documento'   => 'DNI',
                'numero_documento' => $faker->unique()->numerify('########'),
                'direccion'        => $faker->address,
                'fecha_nacimiento' => $faker->date('Y-m-d', '2005-01-01'),
                
                'estado'           => 'Prospecto',
                'user_id'          => null,
                
                // ASIGNACIÓN AL ASESOR 2
                'creado_por_vendedor_id' => 3, 
            ]);
        }
    }
}