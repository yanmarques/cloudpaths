<?php

namespace Cloudpaths\Contracts;

interface Factory
{
    /**
     * Create a directory with name and mapping an array of
     * subdirectories.
     *
     * @param  string $directory
     * @param  array $subDirectories
     * @return Cloudpaths\Directory
     */
    public function create(string $directory, array $subDirectories = []);
}
