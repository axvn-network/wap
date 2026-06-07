<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\SystemConfig;

class CheckSafeDomain
{
    public function handle($request, Closure $next)
    {
        $domain = SystemConfig::getValue('safe_domain');
        if ($domain) {
            $domain = explode(',', $domain);
            $referer = $request->headers->get('referer');
            if (!in_array($referer, $domain)) {
                return response()->json(['code' => 401, 'message' => '认证失败']);
            }
        }

        return $next($request);
    }
}
