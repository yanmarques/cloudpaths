<?php

namespace Cloudpaths\Providers;

use Closure;
use Cloudpaths\Directory;
use Cloudpaths\DirectoryCollection;

class UrlBuilderProvider
{
    /**
     * Handle the collection on the pipeline.
     *
     * @param  Cloudpaths\DirectoryCollection $collection
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(DirectoryCollection $directories, Closure $next)
    {
        return $next(
            collect($directories)->map(function ($directory) {
                return $this->build($directory);
            })
        );
    }

    /**
     * Build the directory path of a directory.
     *
     * @param  Cloudpaths\Directory $directory
     * @return string
     */
    public function build(Directory $directory)
    {
        // Get all directory parent history.
        $directories = $directory->getParentHistory();

        return collect($directories)->map
            ->getName()
            ->implode('/');
    }
}
