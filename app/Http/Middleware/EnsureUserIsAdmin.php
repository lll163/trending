<?php

namespace App\Http\Middleware;

// AI-GEN-BEGIN
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 限制仅 is_admin 用户访问后台路由。
 */
class EnsureUserIsAdmin
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ! $user->is_admin) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
// AI-GEN-END
