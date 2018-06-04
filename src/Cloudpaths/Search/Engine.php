<?php

namespace Cloudpaths\Search;

use Cloudpaths\Directory;
use Cloudpaths\Contracts\Searcher;
use Cloudpaths\DirectoryCollection;

class Engine implements Searcher
{
    /**
     * The current scope of the engine.
     *
     * @var Cloudpaths\Contracts\EngineScope
     */
    protected $scope;

    /**
     * Class constructor. The directories received will be the.
     *
     * @param  Cloudpaths\DirectoryCollection $directories
     * @return void
     */
    public function __construct(DirectoryCollection $directories)
    {
        // Set the initial current scope.
        $this->setScope($directories);
    }

    /**
     * Set collection scope to use be used on search.
     *
     * @param  Cloudpaths\DirectoryCollection
     * @return this
     */
    public function setScope(DirectoryCollection $directories)
    {
        $this->scope = $this->createScopeWithCollection($directories);

        return $this;
    }

    /**
     * Get the current scope of the search engine.
     *
     * @return Cloudpaths\Contracts\EngineScope
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Search a directory by name on the current scope.
     *
     * @param  string $dirName
     * @return Cloudpaths\DirectoryCollection
     */
    public function search(string $dirName)
    {
        // Initializes the collection.
        $collection = new DirectoryCollection;

        // Find the first directory on scope by name.
        $foundCollection = $this->firstOnScope($dirName);

        return $collection->merge($foundCollection);
    }

    /**
     * Create a new scope with the directories collection.
     *
     * @param  Cloudpaths\DirectoryCollection $directories
     * @return Cloudpaths\Search\Scope
     */
    protected function createScopeWithCollection(DirectoryCollection $directories)
    {
        return (new Scope)->setDirectoryCollection($directories);
    }

    /**
     * Get the directory on scope by its name and return a collection with
     * the results.
     *
     * @return Cloudpaths\DirectoryCollection
     */
    protected function firstOnScope(string $dirName)
    {
        // Initializes the collection that stores the results.
        $resultsCollection = new DirectoryCollection;

        // Get the collection on scope.
        $collectionScope = $this->scope->getDirectoryCollection();

        // Find the first directory that matches the directory
        // name.
        $directory = $collectionScope->first(
            function (Directory $directory) use ($dirName) {
                return $directory->getName() === $dirName;
            }
        );

        if ($directory) {

            // Push the result wheter the directory was found.
            $resultsCollection->push($directory);
        }

        return $resultsCollection;
    }
}
