<?php

use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí registramos las rutas API del repositorio de proyectos.
|
*/

// Rutas Públicas (Sin autenticación)
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/{id}', [ProjectController::class, 'show']);

Route::get('/db-check', function () {
    $path = config('database.connections.sqlite.database');
    return response()->json([
        'database_path' => $path,
        'exists' => file_exists($path),
        'writable' => is_writable(dirname($path)),
        'size' => file_exists($path) ? filesize($path) : 0,
        'projects_count' => file_exists($path) ? \App\Models\Project::count() : 0,
        'env_db' => env('DB_DATABASE'),
    ]);
});

// Rutas Protegidas (Requieren autenticación de Clerk)
Route::middleware('clerk.auth')->group(function () {
    // Usuario
    Route::post('/user/sync', [UserController::class, 'sync']);
    Route::get('/user/profile', [UserController::class, 'profile']);
    Route::delete('/user/destroy', [UserController::class, 'destroy']);

    // Proyectos
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::put('/projects/{id}', [ProjectController::class, 'update']);
    Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);
    Route::post('/projects/{id}/like', [ProjectController::class, 'toggleLike']);
    
    // Comentarios
    Route::post('/projects/{id}/comments', [CommentController::class, 'store']);
    
    // Imágenes
    Route::post('/images/upload', [ImageController::class, 'upload']);
    Route::delete('/images/delete', [ImageController::class, 'delete']);
});
