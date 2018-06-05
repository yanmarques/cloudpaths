<?php

namespace Cloudpaths;

abstract class Mapper
{
    /**
     * Map a new directory - subDirectory structure.
     *
     * @param  string $directory
     * @param  array $subDirectories
     * @return this
     */
    abstract public function map(string $directory, array $subDirectories = []);

    /**
     * Map a new directory - subDirectory structure from an array.
     *
     * @param  array $paths
     * @return this
     */
    abstract public function mapArray(array $paths);

    /**
     * Search for a directory and apply the replaces. Returns a collection with
     * the directories path.
     *
     * @param  string $input
     * @param  array $replacements
     * @return Illuminate\Support\Collection
     */
    abstract public function find(string $directory, array $replacements = []);

    /**
     * Search for a directory and apply the replaces. Returns a collection with
     * the directories class.
     *
     * @param  string $input
     * @param  array $replacements
     * @return Cloudpaths\DirectoryCollection
     */
    abstract public function findDirectory(string $directory);
}
