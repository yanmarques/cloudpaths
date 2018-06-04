<?php

namespace Cloudpaths\Contracts;

use Cloudpaths\DirectoryCollection;

interface Searcher
{
    /**
     * Set collection scope to use be used on search.
     *
     * @param  Cloudpaths\DirectoryCollection
     * @return this
     */
    public function setScope(DirectoryCollection $directories);

    /**
     * Get the current scope of the search engine.
     *
     * @return Cloudpaths\Contracts\EngineScope
     */
    public function getScope();
    
    /**
     * Search a directory by name on the current scope.
     *
     * @param  string $dirName
     * @return Cloudpaths\DirectoryCollection
     */
    public function search(string $dirName);
}
