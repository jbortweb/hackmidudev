<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ClerkAuthMiddleware
{
    protected string $jwksUrl;

    public function __construct()
    {
        $publishableKey = config('services.clerk.publishable_key') ?? env('CLERK_PUBLISHABLE_KEY');
        $encodedDomain = explode('_', $publishableKey)[2] ?? 'unknown';
        $domain = rtrim(trim(base64_decode($encodedDomain)), '$');
        $this->jwksUrl = 'https://' . $domain . '/.well-known/jwks.json';
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token no proporcionado'], 401);
        }

        try {
            $keys = $this->getJwks();
            $decoded = JWT::decode($token, $keys);

            $clerkId = $decoded->sub;

            // Buscamos por clerk_id. Si no existe, creamos con valores mínimos no nulos.
            // Usamos una cadena vacía en lugar de null por si la migración aún no se ha ejecutado.
            $user = User::firstOrCreate(
                ['clerk_id' => $clerkId],
                [
                    'name' => '', 
                    'email' => $clerkId . '@no-email.temp', // Email único temporal
                ]
            );

            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            return $next($request);

        } catch (\Exception $e) {
            \Log::error('Clerk auth failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Token inválido o expirado',
                'error' => $e->getMessage(),
                'jwks_url' => $this->jwksUrl
            ], 401);
        }
    }

    protected function getJwks(): array
    {
        $cacheKey = 'clerk_jwks';
        $jwks = cache()->get($cacheKey);

        if (!$jwks) {
            $response = Http::timeout(10)->get($this->jwksUrl);
            
            if ($response->failed()) {
                throw new \RuntimeException('No se pudo obtener JWKS de Clerk');
            }

            $jwks = $response->json();
            cache()->put($cacheKey, $jwks, now()->addHours(1));
        }

        return JWK::parseKeySet($jwks);
    }
}
