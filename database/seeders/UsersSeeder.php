<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'user_id' => 1,
                'name' => 'Sistem Administrator',
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$E.5Glj3Yp8wBVLQx4i7Vre7f8dCyZRx4ZlPwXI5aS2VKDo4xafaVO',
                'role' => 'admin',
                'status' => 'active',
                'is_verified' => 1,
                'created_at' => '2025-12-25 15:33:40',
                'updated_at' => '2025-12-24 03:20:12',
            ],
            [
                'user_id' => 6,
                'name' => 'Bayu Satrio Wibowo',
                'username' => 'baysatriow',
                'email' => 'bayusatriowid@gmail.com',
                'password' => '$2y$10$E.5Glj3Yp8wBVLQx4i7Vre7f8dCyZRx4ZlPwXI5aS2VKDo4xafaVO',
                'role' => 'seller',
                'status' => 'active',
                'is_verified' => 1,
                'created_at' => '2025-12-10 16:36:46',
                'updated_at' => '2025-12-23 21:56:55',
            ],
            [
                'user_id' => 7,
                'name' => 'Fathan Fardian Sanum',
                'username' => 'fathansanum',
                'email' => 'fathanfs19@gmail.com',
                'password' => '$2y$12$7TRrKjB8.bZpvQWrIqRMpuc.ExO8LRNUBaWht4J.EXWFZFtupy88C',
                'role' => 'seller',
                'status' => 'active',
                'is_verified' => 1,
                'created_at' => '2025-12-10 21:18:16',
                'updated_at' => '2025-12-23 17:58:06',
            ],
            [
                'user_id' => 18,
                'name' => 'Annisa Nabila',
                'username' => 'Ansa',
                'email' => 'ansa@gmail.com',
                'password' => '$2y$12$HMJGmca5lnJ3.xAxs9ci0u2EOi5439pDYqgszpbM28nmz74eKrATy',
                'role' => 'seller',
                'status' => 'active',
                'is_verified' => 1,
                'created_at' => '2025-12-23 13:05:29',
                'updated_at' => '2025-12-23 13:37:54',
            ],
            [
                'user_id' => 22,
                'name' => 'SuperM Store',
                'username' => 'marioDB',
                'email' => 'supermstore@gmail.com',
                'password' => '$2y$12$CoSgmlVywDW9awOK1p99temCtmZwHY4dWmFCCt7gNHagEIm.vvMfG',
                'role' => 'seller',
                'status' => 'active',
                'is_verified' => 1,
                'created_at' => '2025-12-23 14:10:21',
                'updated_at' => '2025-12-23 21:15:13',
            ],
        ];

        DB::table('users')->insert($users);
    }
}
