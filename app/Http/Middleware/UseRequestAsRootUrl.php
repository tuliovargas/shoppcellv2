<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

/**
 * Quando APP_DYNAMIC_URL=true: url(), redirects e Mix usam Host e porta do pedido atual
 * (útil para preview por IP antes do DNS/domínio apontarem para a VPS).
 */
class UseRequestAsRootUrl
{
    public function handle(Request $request, Closure $next)
    {
        if (config('app.dynamic_url')) {
            URL::forceRootUrl($request->getSchemeAndHttpHost());
        }

        return $next($request);
    }
}
