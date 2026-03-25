<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectsTableSeeder extends Seeder
{
    public function run()
    {
        // LISTA COMPLETA DE USUARIOS
        $users = [
            ['clerk_id' => 'user_devrobots', 'name' => 'Dev Robots', 'email' => 'devrobots@example.com'],
            ['clerk_id' => 'user_iamyare', 'name' => 'Yare García', 'email' => 'iamyare@example.com'],
            ['clerk_id' => 'user_qwarkdev', 'name' => 'Quark Dev', 'email' => 'qwarkdev@example.com'],
            ['clerk_id' => 'user_3BOxOCtA4jCHZhkVrMbi5fgauIn', 'name' => 'Jordi Bort', 'email' => 'jbortweb@gmail.com'],
            ['clerk_id' => 'user_cosmoart', 'name' => 'Cosmo Art', 'email' => 'cosmoart@example.com'],
            ['clerk_id' => 'user_zclut', 'name' => 'ZC Luth', 'email' => 'zclut@example.com'],
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

        // LISTA COMPLETA DE PROYECTOS
        $projects = [
            [
                'author' => 'Dev Robots',
                'title' => 'Key Leap',
                'description' => 'Un juego interactivo de seguridad informática donde debes descifrar contraseñas para avanzar niveles. El proyecto utiliza la cámara para detectar gestos del usuario.',
                'technologies' => 'Vue.js,TensorFlow.js,TailwindCSS',
                'images' => json_encode(['https://hackmidudev.com/img/proyectos/keyleap.webp']),
                'project_url' => 'https://keyleap.vercel.app/',
                'repo_url' => 'https://github.com/devRobots/keyleap',
                'year' => 2025,
                'winner' => 1,
                'user_index' => 0,
            ],
            [
                'author' => 'Yare García',
                'title' => 'Finanzz',
                'description' => 'Gestión de finanzas personales simplificada. Conexión automática con bancos y categorización de gastos mediante inteligencia artificial.',
                'technologies' => 'React,Node.js,OpenAI',
                'images' => json_encode(['https://res.cloudinary.com/dtixd04j3/image/upload/v1747625291/main-finanzz-green_wot2qj.jpg']),
                'project_url' => 'https://finanzz.vercel.app/',
                'repo_url' => 'https://github.com/iamyare',
                'year' => 2025,
                'winner' => 2,
                'user_index' => 1,
            ],
            [
                'author' => 'Quark Dev',
                'title' => 'SnippetLab',
                'description' => 'La navaja suiza para desarrolladores. Guarda, organiza y comparte fragmentos de código con tus compañeros de equipo de forma privada.',
                'technologies' => 'Next.js,Prisma,PostgreSQL',
                'images' => json_encode(['https://hackmidudev.com/img/proyectos/snippetlab.webp']),
                'project_url' => 'https://snippetlab.app/',
                'repo_url' => 'https://github.com/qwarkdev/snippetlab',
                'year' => 2025,
                'winner' => 3,
                'user_index' => 2,
            ],
            [
                'author' => 'Jordi Bort',
                'title' => 'Lencería Maduixa',
                'description' => 'E-commerce especializado en lencería de diseño local. Experiencia de compra fluida con integración de pagos en tiempo real y gestión de inventario.',
                'technologies' => 'Astro,Shopify,Svelte,TailwindCSS',
                'images' => json_encode(['https://hackmidudev.com/img/proyectos/lenceria.webp']),
                'project_url' => 'https://www.lenceriamaduixa.com/',
                'repo_url' => 'https://github.com/jbortweb/lenceria-maduixa',
                'year' => 2025,
                'winner' => 0,
                'user_index' => 3,
            ],
            [
                'author' => 'Cosmo Art',
                'title' => 'Atomox',
                'description' => 'Plataforma para el intercambio de componentes de UI. Los desarrolladores pueden publicar sus creaciones y otros pueden instalarlas vía CLI.',
                'technologies' => 'Rust,WebAssembly,TypeScript',
                'images' => json_encode(['https://raw.githubusercontent.com/cosmoart/Atomox/refs/heads/main/readme/home.webp']),
                'project_url' => 'https://atomox.vercel.app',
                'repo_url' => 'https://github.com/cosmoart/Atomox',
                'year' => 2025,
                'winner' => 0,
                'user_index' => 4,
            ],
            [
                'author' => 'ZC Luth',
                'title' => 'SoulPixel',
                'description' => 'Experimento social en el que miles de personas dibujan en un lienzo infinito. Incluye un sistema de chat basado en proximidad espacial.',
                'technologies' => 'Socket.io,Canvas API,Express',
                'images' => json_encode(['https://hackmidudev.com/img/proyectos/soulpixel.webp']),
                'project_url' => 'https://soulpixel.klasinky.com/',
                'repo_url' => 'https://github.com/zclut',
                'year' => 2025,
                'winner' => 0,
                'user_index' => 5,
            ],
        ];

        foreach ($projects as $project) {
            $existingProject = DB::table('projects')->where('title', $project['title'])->first();
            if (!$existingProject) {
                $userIndex = $project['user_index'];
                unset($project['user_index']);
                
                $project['user_id'] = $userIds[$userIndex];
                $project['likes'] = rand(50, 200);
                $project['comments_count'] = rand(1, 3);
                $project['created_at'] = now();
                $project['updated_at'] = now();
                DB::table('projects')->insert($project);
            }
        }
    }
}
