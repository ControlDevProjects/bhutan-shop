<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use Illuminate\Support\Facades\Auth;
class RoleMiddleware {
    public function handle(Request $request, Closure $next, string ...$roles) {
        if (!Auth::check()) return redirect()->route('login')->with('error','Please login.');
        if (!in_array(Auth::user()->role, $roles)) abort(403,'Access Denied.');
        if (!Auth::user()->is_active) { Auth::logout(); return redirect()->route('login')->with('error','Account deactivated.'); }
        return $next($request);
    }
}
