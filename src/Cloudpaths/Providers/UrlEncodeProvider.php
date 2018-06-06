<?php

namespace Cloudpaths\Providers;

use Closure;

class UrlEncodeProvider
{
    /**
     * Handle the collection on the pipeline.
     *
     * @param  mixed  $collection
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($collection, Closure $next)
    {
        return $next(collect($collection)->map(function ($item) {
            return rawurlencode($item);
        }));
    }
}
