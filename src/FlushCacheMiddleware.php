<?php

namespace SlyDeath\NestedCaching;

use Cache;

/**
 * Class FlushCacheMiddleware
 *
 * @package SlyDeath\NestedCaching
 */
class FlushCacheMiddleware
{
    /**
     * ПО сброса кэша
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, $next)
    {
        Cache::tags(config('nested_caching.cache_tag'))->flush();
        
        return $next($request);
    }
}