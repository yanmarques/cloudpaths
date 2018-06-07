<?php

namespace Cloudpaths\Providers;

use Closure;
use Cloudpaths\Contracts\Factory;
use Cloudpaths\Contracts\Directory;
use Cloudpaths\DirectoryCollection;

class UrlEncodeProvider
{
    /**
     * Directory factory implementation.
     *
     * @var Cloudpaths\Contracts\Factory
     */
    protected $factory;

    /**
     * Create a new class.
     *
     * @param  Cloudpaths\Contracts\Factory $factory
     * @return void
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Handle the collection on the pipeline.
     *
     * @param  Cloudpaths\DirectoryCollection $collection
     * @param  \Closure $next
     * @return mixed
     */
    public function handle(DirectoryCollection $directories, Closure $next)
    {
        $directories->transform(function ($oldDirectory) {

            // Create a new directory with url encode name.
            $newDirectory = $this->factory->create(
                $this->encodeDirectoryName($oldDirectory)
            )->setSubDirectories($oldDirectory->getSubDirectories());

            if ($oldDirectory->getParent()) {

                // Sync the old parent directory to the newly directory.
                $newDirectory->setParent($oldDirectory->getParent());
            }
            
            return $this->encodeParentRecursive($newDirectory);
        });
        
        return $next($directories);
    }

    /**
     * Encode the directory name.
     *
     * @param  Cloudpaths\Contracts\Directory $directory
     * @return string
     */
    public function encodeDirectoryName(Directory $directory)
    {
        return rawurlencode($directory->getName());
    }

    /**
     * Encode all the parents name of the directory.
     *
     * @param  Cloudpaths\Contracts\Directory $directory
     * @return Cloudpaths\Contracts\Directory
     */
    public function encodeParentRecursive(Directory $directory)
    {
        // First nested parent to encode and reverse the order.
        $parents = $directory->getParentHistory();

        // Remove the first item on history, that is the current directory.
        $parents->pop();

        // Directory has no parent.
        if ($parents->isEmpty()) {
            return $directory;
        }

        $parents->transform(function ($directory) {

            // Encode the parent directory name.
            return $this->factory->create($this->encodeDirectoryName($directory));
        });
        
        $parent = $parents->reduce(function ($carryParent, $parent) {
            if ($carryParent) {

                // Set the parent as the parent carried by reducer.
                $parent->setParent($carryParent);
            }

            return $parent;
        });
        
        return $directory->setParent($parent);
    }
}
