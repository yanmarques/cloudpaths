<?php

namespace Cloudpaths;

use Illuminate\Contracts\Config\Repository;
use Cloudpaths\Traits\ParsesDotNotation;
use Cloudpaths\DirectoryCollection;
use Cloudpaths\Contracts\Factory;
use Illuminate\Support\Arr;

class Cloudpaths extends Mapper
{
    use ParsesDotNotation;

    /**
     * Collection with mapped directories.
     * 
     * @var Cloudpaths\DirectoryCollection
     */
    protected $directories;

    /**
     * The base directory used to build any directory.
     * 
     * @var Cloudpaths\Directory|null
     */
    protected $root;

    /**
     * The config repository.
     * 
     * @var Illuminate\Config\Repository
     */
    protected $config;

    /**
     * The directory factory.
     * 
     * @var Cloudpaths\Contracts\Factory
     */
    protected $factory;

    /**
     * Class constructor.
     * 
     * @param  Illuminate\Contracts\Config\Repository $config
     * @param  Cloudpaths\Contracts\Factory $factory
     * @param  Cloudpaths\DirectoryCollection $directories
     * @return void
     */
    public function __construct(
        Repository $config, 
        Factory $factory,
        DirectoryCollection $directories = null
    ) {
        $this->factory = $factory;
        $this->directories = $directories ?: new DirectoryCollection;
        $this->config = $this->setConfig($config);
    }

    /**
     * Map a new directory - subDirectory structure.
     * 
     * @param  string $directory
     * @param  array $subDirectories
     * @return this
     */
    public function map(string $directory, array $subDirectories = []) 
    {
        // Create a directory class for the path.
        $directory = $this->factory->create(
            $directory, 
            $subDirectories
        );

        // Append the newly directory to the directories list.
        $this->directories->push($directory);

        return $this;
    }

    /**
     * Map a new directory - subDirectory structure from an array.
     * 
     * @param  array $paths
     * @return this
     */
    public function mapArray(array $paths) 
    {
        if (Arr::isAssoc($paths)) {

            // Map an associative array inserting new directory struture.
            foreach($paths as $dir => $subDirs) {
                $this->map($dir, $subDirs);
            }
        } else {

            // Handle a plain array with new directories only.
            foreach($paths as $path) {
                $this->map($path);
            }
        }

        return $this;
    }

    /**
     * Search for a directory and apply the replaces.
     * 
     * @return string|null
     */
    public function find(string $input, array $replacements = []) 
    {
        // Collection to be returned.
        $collection = new DirectoryCollection;

        // Get parsed dot notation input. 
        $fragments = $this->parseInput($input);
        
        // Make the first quick search on top level directories since the
        // first fragment should be the first directory.
        $directory = $this->performQuickSearch($fragments[0]);

        if (! $directory) {

            // Any top level directory found. An empty collection 
            // will be returned now.
            return $collection;
        }

        if (count($fragments) <= 1) {

            // Push a found directory from a search on top level 
            // directories.
            $collection->push($directory);
        } else {
            
            // TO-DO Create search through subdirectories.
            // see https://github.com/yanmarques/cloudpaths/issues/5
            //
            // Keep searching through the directory subDirectories until 
            // we find the next fragmente. It's important that any achieved 
            // subDirectory be stored somewhere for further url building. 
        }

        return $collection;
    }

    /**
     * Get the base directory.
     * 
     * @return void
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Set a new repository configuration. 
     * 
     * @param  Illuminate\Config\Repository $config
     * @return void
     */
    public function setConfig(Repository $config)
    {
        $this->config = $config;
        $this->readConfig();
    }

    /**
     * Read configuration repository to discover new
     * directories and set up a base directory. An array
     * with custom configuration is allowed.
     * 
     * @param  array $config
     * @return void
     */
    protected function readConfig()
    {
        if ($this->config->has('root')) {

            // Create a directory for base directory.
            $this->root = $this->factory->create(
                $this->config->get('root')
            );
        }

        if ($this->config->has('paths')) {

            // Map each registered directory for further usage.
            $this->map($this->config->get('paths'));
        }
    }

    /**
     * Search a directory on top level directories.
     * 
     * @param  string $dirName
     * @return Cloudpaths\Directory|null
     */
    protected function performQuickSearch(string $dirName)
    {
        return $this->directories->first(
            function (Directory $directory) use ($dirName) {
                return $directory->getName() == $dirName;
            }
        );
    }
}
