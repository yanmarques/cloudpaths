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
     * Search for a directory and apply the replaces.
     * 
     * @return string|null
     */
    abstract public function find(string $directory, array $replacements = []);
}