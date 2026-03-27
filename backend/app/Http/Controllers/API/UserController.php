<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Sincroniza los datos del usuario de Clerk con nuestra DB local.
     * 
     * POST /api/user/sync
     */
    public function sync(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'avatar_url' => 'nullable|string|max:500',
            'github_url' => 'nullable|string|max:500',
            'linkedin_url' => 'nullable|string|max:500',
            'website_url' => 'nullable|string|max:500',
        ]);

        try {
            if (isset($validatedData['name'])) {
                $user->name = $validatedData['name'];
            }
            
            if (isset($validatedData['email'])) {
                // Solo actualizamos el email si no pertenece a otro usuario con distinto clerk_id
                $exists = User::where('email', $validatedData['email'])
                              ->where('clerk_id', '!=', $user->clerk_id)
                              ->exists();
                if (!$exists) {
                    $user->email = $validatedData['email'];
                }
            }

            if (!empty($validatedData['avatar_url'])) {
                $user->avatar_url = $validatedData['avatar_url'];
            }
            
            if (isset($validatedData['github_url'])) {
                $user->github_url = $validatedData['github_url'];
            }
            
            if (isset($validatedData['linkedin_url'])) {
                $user->linkedin_url = $validatedData['linkedin_url'];
            }
            
            if (isset($validatedData['website_url'])) {
                $user->website_url = $validatedData['website_url'];
            }
            
            $user->save();

            return response()->json([
                'message' => 'Perfil actualizado correctamente',
                'user' => $user
            ]);
        } catch (\Exception $e) {
            \Log::error('Error sync perfil: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al guardar perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene el perfil del usuario actual con sus proyectos.
     * 
     * GET /api/user/profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        return response()->json($user->load('projects'));
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        $user->projects()->delete();
        $user->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }
}
