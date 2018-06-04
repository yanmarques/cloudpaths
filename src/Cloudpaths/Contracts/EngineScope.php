<?php

namespace Cloudpaths\Contracts;

use Cloudpaths\DirectoryCollection;

interface EngineScope
{
    /**
     * Set the directory collection of the search engine.
     *
     * @param  Cloudpaths\DirectoryCollection $directories
     * @return this
     */
    public function setDirectoryCollection(DirectoryCollection $directories);

    /**
     * Get the directory collection of the scope.
     *
     * @return Cloudpaths\DirectoryCollection
     */
    public function getDirectoryCollection();
}
