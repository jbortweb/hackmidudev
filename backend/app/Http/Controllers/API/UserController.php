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
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'avatar_url' => 'nullable|string|max:500',
            'github_url' => 'nullable|string|max:500',
            'linkedin_url' => 'nullable|string|max:500',
            'website_url' => 'nullable|string|max:500',
        ]);

        if (!empty($validatedData['avatar_url'])) {
            $incomingAvatar = $validatedData['avatar_url'];
            $isStorageAvatar = str_starts_with($incomingAvatar, '/storage/');
            
            if ($isStorageAvatar) {
                $user->avatar_url = $incomingAvatar;
            } elseif (empty($user->avatar_url)) {
                $user->avatar_url = $incomingAvatar;
            }
        }

        if (!empty($validatedData['name'])) {
            $user->name = $validatedData['name'];
        }
        if (!empty($validatedData['email'])) {
            $user->email = $validatedData['email'];
        }
        if (!empty($validatedData['github_url'])) {
            $user->github_url = $validatedData['github_url'];
        }
        if (!empty($validatedData['linkedin_url'])) {
            $user->linkedin_url = $validatedData['linkedin_url'];
        }
        if (!empty($validatedData['website_url'])) {
            $user->website_url = $validatedData['website_url'];
        }
        
        $user->save();

        return response()->json([
            'message' => 'Usuario sincronizado correctamente',
            'user' => $user
        ]);
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
