<?php

namespace Cloudpaths;

use Closure;
use Illuminate\Support\Arr;
use Cloudpaths\Search\Engine;
use InvalidArgumentException;
use Cloudpaths\Contracts\Factory;
use Illuminate\Pipeline\Pipeline;
use Cloudpaths\Contracts\Searcher;
use Illuminate\Support\Collection;
use Cloudpaths\Traits\ParsesDotNotation;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;

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
     * The directory search engine.
     *
     * @var Cloudpaths\Contracts\Searcher
     */
    protected $searchEngine;

    /**
     * The callback to resolve root directory.
     *
     * @var \Closure
     */
    protected $rootResolver;

    /**
     * The providers classes to pipe each result from
     * directory search.
     *
     * @var array
     */
    protected $providers;

    /**
     * The container implementation.
     *
     * @var Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Class constructor.
     *
     * @param  Illuminate\Contracts\Container\Container $container
     * @param  Illuminate\Contracts\Config\Repository $config
     * @param  Cloudpaths\Contracts\Factory $factory
     * @param  Cloudpaths\Contracts\Searcher $searchEngine
     * @return void
     */
    public function __construct(
        Container $container,
        Repository $config,
        Factory $factory = null,
        Searcher $searchEngine = null
    ) {
        $this->container = $container;

        $this->factory = $factory ?: new DirFactory;

        // Create a new search engine if no engine is provided.
        $this->searchEngine = $searchEngine ?: new Engine(
            new DirectoryCollection
        );

        // Get the directory collection from engine scope.
        $this->directories = $this->searchEngine->getScope()
            ->getDirectoryCollection();

        $this->config = $this->setConfig($config);

        // Set the default rootResolver.
        $this->setRootResolver(function ($root) {
            return $root;
        });

        // Register the default providers.
        $this->registerDefaultProviders();
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
            foreach ($paths as $dir => $subDirs) {
                $this->map($dir, $subDirs);
            }
        } else {

            // Handle a plain array with new directories only.
            foreach ($paths as $path) {
                $this->map($path);
            }
        }

        return $this;
    }

    /**
     * Search for a directory and apply the replaces.
     *
     * @param  string $input
     * @param  array $replacements
     * @return Illuminate\Support\Collection
     */
    public function find(string $input, array $replacements = [])
    {
        // Find the directories by the input and send to providers
        // pipeline.
        $fullDirectoryPaths = $this->sendToPipeline($this->findDirectory($input));

        return Collection::make(
            $this->applyReplaces($fullDirectoryPaths->toArray(), $replacements)
        );
    }

    /**
     * Search for a directory and apply the replaces. Returns a collection with
     * the directories class.
     *
     * @param  string $input
     * @param  array $replacements
     * @return Cloudpaths\DirectoryCollection
     */
    public function findDirectory(string $input)
    {
        // Get parsed dot notation input.
        $fragments = $this->parseInput($input);

        // Change the search scope to the main directories collection.
        $this->searchEngine->setScope($this->directories);

        // Make the first quick search on top level directories since the
        // first fragment should be the first directory.
        $foundCollection = $this->searchEngine->search(
            array_shift($fragments)
        );

        if ($foundCollection->isEmpty()) {

            // Any top level directory found. An empty collection
            // will be returned now.
            return $foundCollection;
        }

        // The top level directory to get the subDirectories.
        $topLevelDirectory = $foundCollection->first();

        if (count($fragments) < 1) {

            // Return the top level directory.
            return $this->prependRoot(
                DirectoryCollection::make($topLevelDirectory)
            );
        }

        // Get all directories found on the top level directory by the fragments
        // names. The top level subdirectories are looked up looking for directories
        // that matches the fragment name.
        return $this->prependRoot(
            $this->findManyByName(
                $fragments,
                $topLevelDirectory
            )
        );
    }

    /**
     * Get the base directory.
     *
     * @return void
     */
    public function getRoot()
    {
        return $this->callRootResolver() ?: $this->root;
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
     * Set the root resolver callback.
     *
     * @param  \Closure $rootResolver
     * @return this
     */
    public function setRootResolver(Closure $rootResolver)
    {
        $this->rootResolver = $rootResolver;

        return $this;
    }

    /**
     * Add a new provider to run on pipeline.
     *
     * @param  mixed $provider
     * @return this
     */
    public function addProvider($provider)
    {
        $this->providers = Arr::prepend($this->providers, $provider);

        return $this;
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
        // Create a directory for base directory.
        $this->root = $this->factory->create(
            $this->config->get('root') ?: ''
        );

        if ($this->config->has('paths')) {

            // Map each registered directory for further usage.
            $this->mapArray($this->config->get('paths'));
        }
    }

    /**
     * Find a directory from a collection as scope for the search engine.
     *
     * @param  string $directoryName
     * @param  Cloudpaths\DirectoryCollection $collectionScope
     * @return Cloudpaths\DirectoryCollection
     */
    protected function findOnCollectionByName(string $directoryName, DirectoryCollection $collectionScope)
    {
        // Change the search engine scope.
        $this->searchEngine->setScope($collectionScope);

        // The collection found on fragment search.
        $foundCollection = $this->searchEngine->search($directoryName);

        foreach ($collectionScope->all() as $directory) {

            // Merge the collection found with the collection to return.
            $foundCollection = $foundCollection->merge(
                $this->findOnCollectionByName($directoryName, $directory->getSubDirectories())
            );
        }

        return $foundCollection;
    }

    /**
     * Resolve the root resolver callback. The function receives the directory
     * factory and the root directory as argument.
     *
     * @throws \InvalidArgumentException
     *
     * @return Cloudpaths\Contracts\Directory|null
     */
    protected function callRootResolver()
    {
        // Execute the function and return the result.
        $result = call_user_func($this->rootResolver, $this->root, $this->factory);

        if ($result && ! $result instanceof Directory) {

            // Invalid returned object from resolver.
            throw new InvalidArgumentException('Could not resolve the root directory');
        }

        return $result;
    }

    /**
     * Find all ocorrences of the directories on the main directory subdirectories.
     *
     * @param  array $directoryNames
     * @param  Cloudpaths\Contracts\Directory $mainDirectory
     * @return Cloudpaths\DirectoryCollection
     */
    protected function findManyByName(array $directoryNames, Directory $mainDirectory)
    {
        // Initializes the collection found.
        $foundCollection = new DirectoryCollection;

        foreach ($directoryNames as $name) {

            // Merge the found collection with the collection result
            // from the search of the fragment name.
            $foundCollection = $foundCollection->merge($this->findOnCollectionByName(
                $name,
                $mainDirectory->getSubDirectories()
            ));
        }

        return $foundCollection;
    }

    /**
     * Replace the paths as an array with the replacements.
     *
     * @param  mixed  $paths
     * @param  array  $replacements
     * @return array
     */
    protected function applyReplaces($paths, array $replacements)
    {
        // Create an array for the paths.
        $paths = (array) $paths;

        foreach ($paths as $index => $path) {
            foreach ($replacements as $key => $replaces) {

                // Apply the string replace on the path.
                $paths[$index] = str_replace_first($key, $replaces, $path);
            }
        }

        return $paths;
    }

    /**
     * Send the directories collection to the providers pipeline.
     *
     * @param  Cloudpaths\DirectoryCollection $directories
     * @return Illuminate\Support\Collection
     */
    protected function sendToPipeline(DirectoryCollection $directories)
    {
        // Create the pipeline.
        $pipeline = new Pipeline($this->container);

        // Send the directories collection. After the collection has been piped through
        // all providers execute the resolver and return the collection resulted.
        return $pipeline->send($directories)
            ->through($this->providers)
            ->then($this->getPipelineResolver());
    }

    /**
     * Register the default providers.
     *
     * @return void
     */
    protected function registerDefaultProviders()
    {
        $this->providers = [
            Providers\UrlEncodeProvider::class,
            Providers\UrlBuilderProvider::class,
        ];
    }

    /**
     * Get the final pipeline resolver function.
     *
     * @return Closure
     */
    protected function getPipelineResolver()
    {
        return function ($collection) {
            return $collection instanceof Collection
                ? $collection
                : collect($collection);
        };
    }

    /**
     * Prepend the root directory name to a collection of directories.
     *
     * @param  DirectoryCollection $directories
     * @return Cloudpaths\DirectoryCollection
     */
    protected function prependRoot(DirectoryCollection $directories)
    {
        return $directories->map(function ($directory) {

            // First nested parent to encode and reverse the order.
            $parents = $directory->getParentHistory();

            // Get the top parent directory and set.
            $topLevelDirectory = $parents->shift()->setParent(
                $this->getRoot()
            );

            // Get the nested parent with all updated parents.
            return $parents->reduce(function ($carryParent, $parent) {

                // Set the parent as the parent carried by reducer.
                return $parent->setParent($carryParent);
            }, $topLevelDirectory);
        });
    }
}
