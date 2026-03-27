<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectLike;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::with('user');

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        if ($request->has('winner')) {
            if ($request->winner == 1) {
                $query->where('winner', '>', 0);
            } else {
                $query->where('winner', 0);
            }
        }

        $perPage = $request->input('per_page', 12);
        
        // ORDEN: Primero ganadores (1, 2, 3), luego por fecha (más nuevos primero)
        // Usamos una expresión raw para que winner > 0 vaya arriba, y luego ordenamos por el valor de winner y fecha.
        $projects = $query->orderByRaw('CASE WHEN winner > 0 THEN 0 ELSE 1 END')
                          ->orderByRaw('CASE WHEN winner > 0 THEN winner ELSE 0 END', 'ASC')
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);
        
        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $projectCount = Project::where('user_id', $user->id)->count();
        if ($projectCount >= 5) {
            return response()->json([
                'message' => 'Has alcanzado el límite de 5 proyectos por usuario.'
            ], 422);
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'technologies' => 'nullable|string',
            'images' => 'nullable|array',
            'project_url' => 'nullable|url|max:255',
            'repo_url' => 'nullable|url|max:255',
            'year' => 'required|integer',
            'winner' => 'nullable|integer|between:0,3',
        ]);

        $project = new Project($validatedData);
        $project->user_id = $user->id;
        $project->author = $user->name ?? 'Usuario Anónimo';
        $project->likes = 0;
        $project->save();

        return response()->json([
            'message' => 'Proyecto creado correctamente',
            'project' => $project
        ], 201);
    }

    public function show(string $id)
    {
        $project = Project::with(['user', 'comments.user'])->findOrFail($id);
        return response()->json($project);
    }

    public function update(Request $request, string $id)
    {
        $user = $request->user();
        $project = Project::findOrFail($id);

        if ($project->user_id !== $user->id) {
            return response()->json(['message' => 'No tienes permiso para editar este proyecto'], 403);
        }

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'technologies' => 'nullable|string',
            'images' => 'nullable|array',
            'project_url' => 'nullable|url|max:255',
            'repo_url' => 'nullable|url|max:255',
            'year' => 'sometimes|required|integer',
            'winner' => 'nullable|integer|between:0,3',
        ]);

        $project->update($validatedData);

        return response()->json([
            'message' => 'Proyecto actualizado correctamente',
            'project' => $project
        ]);
    }

    public function destroy(Request $request, string $id)
    {
        $user = $request->user();
        $project = Project::findOrFail($id);

        if ($project->user_id !== $user->id) {
            return response()->json(['message' => 'No tienes permiso para borrar este proyecto'], 403);
        }

        $project->delete();
        return response()->json(['message' => 'Proyecto eliminado correctamente']);
    }

    public function toggleLike(Request $request, string $id)
    {
        $user = $request->user();
        $project = Project::findOrFail($id);

        $existingLike = ProjectLike::where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $project->decrement('likes');
            $liked = false;
        } else {
            ProjectLike::create([
                'user_id' => $user->id,
                'project_id' => $project->id,
            ]);
            $project->increment('likes');
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes' => $project->fresh()->likes,
        ]);
    }
}
