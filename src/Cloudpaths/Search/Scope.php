<?php

namespace Cloudpaths\Search;

use Cloudpaths\DirectoryCollection;
use Cloudpaths\Contracts\EngineScope;

class Scope implements EngineScope
{
    /**
     * The directories collection.
     *
     * @var Cloudpaths\DirectoryCollection
     */
    protected $directories;

    /**
     * Set the directory collection of the search engine.
     *
     * @param  Cloudpaths\DirectoryCollection $directories
     * @return this
     */
    public function setDirectoryCollection(DirectoryCollection $directories)
    {
        $this->directories = $directories;

        return $this;
    }

    /**
     * Get the directory collection of the scope.
     *
     * @return Cloudpaths\DirectoryCollection
     */
    public function getDirectoryCollection()
    {
        return $this->directories;
    }
}
