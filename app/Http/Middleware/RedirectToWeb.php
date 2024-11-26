<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToWeb
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ตรวจสอบว่า URL ไม่ได้เริ่มต้นด้วย /web
        // if (!str_starts_with($request->path(), 'web')) {
        //     return redirect('/web/');
        // }
        // return $next($request);
        return redirect('/web/');
    }
}
