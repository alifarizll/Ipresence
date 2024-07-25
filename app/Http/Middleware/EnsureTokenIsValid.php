<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\GenericUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authUser = $this->validateToken($request->bearerToken());

        if ($authUser) {
            $request->setUserResolver(fn () => new GenericUser($authUser));
            $request->attributes->set('auth_token', $request->bearerToken());
        }

        return $next($request);
    }

    protected function validateToken($token)
    {
        try {
            $url = env('AUTHENTICATION_SERVICE').'/api/validate-token';
            $response = Http::asForm()->post($url, ['token' => $token]);

            if ($response->successful()) {
                $responseData = $response->json();
                if (isset($responseData['data'])) {
                    return $responseData['data'];
                }
            }

            throw new \Illuminate\Http\Client\RequestException($response);
        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error("Error validating token: {$e->getMessage()}", [
                'response' => $e->response->json(),
                'status_code' => $e->getCode(),
            ]);

            abort(403, 'Unauthorized');
        } catch (\Throwable $th) {
            Log::error("Unexpected error during token validation: {$th->getMessage()}");

            abort(500, 'Internal Server Error');
        }
    }
}
