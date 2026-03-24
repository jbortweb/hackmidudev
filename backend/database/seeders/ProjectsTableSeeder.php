<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectsTableSeeder extends Seeder
{
    public function run()
    {
        $users = [
            ['clerk_id' => 'user_devrobots', 'name' => 'Dev Robots', 'email' => 'devrobots@example.com'],
            ['clerk_id' => 'user_iamyare', 'name' => 'Yare García', 'email' => 'iamyare@example.com'],
            ['clerk_id' => 'user_qwarkdev', 'name' => 'Quark Dev', 'email' => 'qwarkdev@example.com'],
            ['clerk_id' => 'user_jbortweb', 'name' => 'Jordi Bort', 'email' => 'jbortweb@example.com'],
            ['clerk_id' => 'user_cosmoart', 'name' => 'Cosmo Art', 'email' => 'cosmoart@example.com'],
            ['clerk_id' => 'user_zclut', 'name' => 'ZC Luth', 'email' => 'zclut@example.com'],
        ];

        $userIds = [];
        foreach ($users as $user) {
            $userIds[] = DB::table('users')->insertGetId([
                'clerk_id' => $user['clerk_id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'avatar_url' => null,
                'github_url' => 'https://github.com/' . strtolower(str_replace(' ', '', $user['name'])),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $projects = [
            [
                'author' => 'devRobots',
                'title' => 'Key Leap',
                'description' => 'Un juego interactivo de seguridad informática donde debes descifrar contraseñas para avanzar niveles. El proyecto utiliza la cámara para detectar gestos del usuario.',
                'technologies' => 'Vue.js,TensorFlow.js,TailwindCSS',
                'images' => json_encode(['/img/proyectos/keyleap.webp']),
                'project_url' => 'https://keyleap.vercel.app/',
                'repo_url' => 'https://github.com/devRobots/keyleap',
                'year' => 2025,
                'winner' => 1,
            ],
            [
                'author' => 'iamyare',
                'title' => 'Finanzz',
                'description' => 'Gestión de finanzas personales simplificada. Conexión automática con bancos y categorización de gastos mediante inteligencia artificial.',
                'technologies' => 'React,Node.js,OpenAI',
                'images' => json_encode(['https://res.cloudinary.com/dtixd04j3/image/upload/v1747625291/main-finanzz-green_wot2qj.jpg']),
                'project_url' => 'https://finanzz.vercel.app/',
                'repo_url' => 'https://github.com/iamyare',
                'year' => 2025,
                'winner' => 2,
            ],
            [
                'author' => 'qwarkdev',
                'title' => 'SnippetLab',
                'description' => 'La navaja suiza para desarrolladores. Guarda, organiza y comparte fragmentos de código con tus compañeros de equipo de forma privada.',
                'technologies' => 'Next.js,Prisma,PostgreSQL',
                'images' => json_encode(['/img/proyectos/snippetlab.webp']),
                'project_url' => 'https://snippetlab.app/',
                'repo_url' => 'https://github.com/qwarkdev/snippetlab',
                'year' => 2025,
                'winner' => 3,
            ],
            [
                'author' => 'jbortweb',
                'title' => 'Lencería Maduixa',
                'description' => 'E-commerce especializado en lencería de diseño local. Experiencia de compra fluida con integración de pagos en tiempo real.',
                'technologies' => 'Astro,Shopify,Svelte',
                'images' => json_encode(['/img/proyectos/lenceria.webp']),
                'project_url' => 'https://www.lenceriamaduixa.com/',
                'repo_url' => 'https://github.com/jbortweb',
                'year' => 2025,
                'winner' => 0,
            ],
            [
                'author' => 'cosmoart',
                'title' => 'Atomox',
                'description' => 'Plataforma para el intercambio de componentes de UI. Los desarrolladores pueden publicar sus creaciones y otros pueden instalarlas vía CLI.',
                'technologies' => 'Rust,WebAssembly,TypeScript',
                'images' => json_encode(['https://raw.githubusercontent.com/cosmoart/Atomox/refs/heads/main/readme/home.webp']),
                'project_url' => 'https://atomox.vercel.app',
                'repo_url' => 'https://github.com/cosmoart/Atomox',
                'year' => 2025,
                'winner' => 0,
            ],
            [
                'author' => 'zclut',
                'title' => 'SoulPixel',
                'description' => 'Experimento social en el que miles de personas dibujan en un lienzo infinito. Incluye un sistema de chat basado en proximidad espacial.',
                'technologies' => 'Socket.io,Canvas API,Express',
                'images' => json_encode(['/img/proyectos/soulpixel.webp']),
                'project_url' => 'https://soulpixel.klasinky.com/',
                'repo_url' => 'https://github.com/zclut',
                'year' => 2025,
                'winner' => 0,
            ],
        ];

        $comments = [
            '¡Increíble proyecto! Me encanta la idea.',
            'Muy bien hecho, enhorabuena.',
            'Esto va a革命ar el sector.',
            'Gran trabajo, enhorabuena.',
            'Me ha encantado, enhorabuena.',
            'Increíble, felicidades.',
            'Esto está muy bien pensado.',
            'Gran job, felicidades.',
            'Me ha encantado, seguir así.',
            'Esto mola mucho.',
            '¡Buen trabajo!',
            '¡Genial!',
        ];

        foreach ($projects as $index => $project) {
            $project['user_id'] = $userIds[$index];
            $project['likes'] = rand(50, 200);
            $project['comments_count'] = rand(1, 3);
            $project['created_at'] = now();
            $project['updated_at'] = now();
            $pId = DB::table('projects')->insertGetId($project);

            $numComments = min(3, $project['comments_count']);
            for ($i = 0; $i < $numComments; $i++) {
                $randomUserId = $userIds[array_rand($userIds)];
                DB::table('comments')->insert([
                    'user_id' => $randomUserId,
                    'project_id' => $pId,
                    'content' => $comments[array_rand($comments)],
                    'created_at' => now()->addMinutes(rand(1, 100)),
                    'updated_at' => now()->addMinutes(rand(1, 100)),
                ]);
            }
        }
    }
}
