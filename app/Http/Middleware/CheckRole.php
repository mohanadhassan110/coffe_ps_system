<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * التحقق من دور المستخدم
 */
class CheckRole
{
    /**
     * @param string ...$roles الأدوار المسموح بها
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            abort(403, __('messages.errors.unauthorized'));
        }

        return $next($request);
    }
}
