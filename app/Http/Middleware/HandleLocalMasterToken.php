<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\TransientToken;
use Symfony\Component\HttpFoundation\Response;

class HandleLocalMasterToken
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isLocal = (app()->isLocal() || config('app.env') === 'local')
            && ! app()->isProduction()
            && config('app.env') !== 'production';

        if (! $isLocal) {
            return $next($request);
        }

        $masterToken = config('auth.master_token');

        if (! empty($masterToken) && $request->bearerToken() === $masterToken) {
            $user = User::query()->firstOrCreate(
                ['email' => 'master@taskflow.local'],
                [
                    'name' => 'Master Developer',
                    'password' => 'master123',
                ]
            );

            $user->withAccessToken(new TransientToken);

            Auth::guard('sanctum')->setUser($user);
            $request->setUserResolver(fn () => $user);
        }

        return $next($request);
    }
}
