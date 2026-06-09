<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsTeacher
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $type = $request->user()->type;

        if ($type === 'teacher' || $type === 'admin') {
            return $next($request);
        }

        return redirect()->route('student.home');
    }
}
