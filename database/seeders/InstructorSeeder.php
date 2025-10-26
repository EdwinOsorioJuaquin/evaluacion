<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InstructorSeeder extends Seeder
{
    public function run(): void
    {
        // Insertar usuarios instructores - ESTRUCTURA EXACTA
        $users = [
            [
                'first_name' => 'Edwin',
                'last_name' => 'Osorio',
                'full_name' => 'Edwin Osorio Juaquin',
                'dni' => '44556607',
                'document' => '44556607',
                'email' => 'edwin.osorio@incadev.com',
                'email_verified_at' => now(),
                'phone_number' => '+51 987 654 100',
                'password' => Hash::make('password'),
                'role' => json_encode(["admin"]),
                'gender' => 'male',
                'country' => 'Perú',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'first_name' => 'Rodrigo',
                'last_name' => 'Trejo',
                'full_name' => 'Rodrigo Trejo',
                'dni' => '88990012',
                'document' => '88990012',
                'email' => 'rodrigo.trejo@incadev.com',
                'email_verified_at' => now(),
                'phone_number' => '+51 987 654 101',
                'password' => Hash::make('password'),
                'role' => json_encode(['auditor']),
                'gender' => 'male',
                'country' => 'Perú',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        // Insertar usuarios instructores
        DB::table('users')->insert($users);

        // Obtener los IDs de los usuarios recién insertados
        $userIds = DB::table('users')
            ->whereIn('email', [
                'edwin.osorio@incadev.com',
                'rodrigo.trejo@incadev.com'
            ])
            ->pluck('id', 'email')
            ->toArray();



        $this->command->info('✅ 2 instructores creados exitosamente!');
        $this->command->info('📧 Emails: roberto.gonzalez@email.com, laura.hernandez@email.com');
        $this->command->info('🔑 Contraseña para todos: password');
    }
}