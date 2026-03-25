<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectsTableSeeder extends Seeder
{
    public function run()
    {
        // Usuarios de prueba
        $users = [
            ['clerk_id' => 'user_devrobots', 'name' => 'Dev Robots', 'email' => 'devrobots@example.com'],
            ['clerk_id' => 'user_iamyare', 'name' => 'Yare García', 'email' => 'iamyare@example.com'],
            ['clerk_id' => 'user_qwarkdev', 'name' => 'Quark Dev', 'email' => 'qwarkdev@example.com'],
            ['clerk_id' => 'user_3BOxOCtA4jCHZhkVrMbi5fgauIn', 'name' => 'Jordi Bort', 'email' => 'jbortweb@gmail.com'],
        ];

        $userIds = [];
        foreach ($users as $user) {
            $existingUser = DB::table('users')->where('email', $user['email'])->first();
            
            if ($existingUser) {
                $userIds[] = $existingUser->id;
            } else {
                $userIds[] = DB::table('users')->insertGetId([
                    'clerk_id' => $user['clerk_id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Proyectos de prueba
        $projects = [
            [
                'author' => 'Dev Robots',
                'title' => 'Key Leap',
                'description' => 'Un juego interactivo de seguridad informática donde debes descifrar contraseñas para avanzar niveles.',
                'technologies' => 'Vue.js,TensorFlow.js,TailwindCSS',
                'images' => json_encode(['https://hackmidudev.com/img/proyectos/keyleap.webp']),
                'project_url' => 'https://keyleap.vercel.app/',
                'repo_url' => 'https://github.com/devRobots/keyleap',
                'year' => 2025,
                'winner' => 1,
                'likes' => 150,
                'comments_count' => 5,
                'user_index' => 0
            ],
            [
                'author' => 'Yare García',
                'title' => 'Finanzz',
                'description' => 'Gestión de finanzas personales simplificada con IA.',
                'technologies' => 'React,Node.js,OpenAI',
                'images' => json_encode(['https://res.cloudinary.com/dtixd04j3/image/upload/v1747625291/main-finanzz-green_wot2qj.jpg']),
                'project_url' => 'https://finanzz.vercel.app/',
                'repo_url' => 'https://github.com/iamyare',
                'year' => 2025,
                'winner' => 2,
                'likes' => 120,
                'comments_count' => 3,
                'user_index' => 1
            ],
            [
                'author' => 'Jordi Bort',
                'title' => 'Lencería Maduixa',
                'description' => 'E-commerce especializado en lencería de diseño local.',
                'technologies' => 'Astro,Shopify,Svelte,TailwindCSS',
                'images' => json_encode(['https://hackmidudev.com/img/proyectos/lenceria.webp']),
                'project_url' => 'https://www.lenceriamaduixa.com/',
                'repo_url' => 'https://github.com/jbortweb/lenceria-maduixa',
                'year' => 2025,
                'winner' => 0,
                'likes' => 85,
                'comments_count' => 2,
                'user_index' => 3
            ],
        ];

        foreach ($projects as $project) {
            $existingProject = DB::table('projects')->where('title', $project['title'])->first();
            
            if (!$existingProject) {
                $userIndex = $project['user_index'];
                unset($project['user_index']);
                
                $project['user_id'] = $userIds[$userIndex];
                $project['created_at'] = now();
                $project['updated_at'] = now();
                DB::table('projects')->insert($project);
            }
        }
    }
}
