<?php

namespace Cloudpaths;

use Cloudpaths\Contracts\Factory;

class DirFactory implements Factory
{
    /**
     * Create a directory with name and mapping an array of
     * subdirectories.
     *
     * @param  string $directory
     * @param  array $subDirectories
     * @return Cloudpaths\Directory
     */
    public function create(string $directory, array $subDirectories = [])
    {
        return new Directory($directory, $this->createSubDirectories(
           $subDirectories
        ));
    }

    /**
     * Map all subdirectories and create a subdirectories collection.
     *
     * @param  array $subDirectories
     * @return CloudPaths\DirectoryCollection
     */
    protected function createSubDirectories(array $subDirectories)
    {
        $collection = new DirectoryCollection;

        foreach ($subDirectories as $directory => $subDirectories) {
            if (is_string($directory)) {

                // Subdirectories have pathKeys directories.
                //
                // Ex: [
                //    'pathKey' => ['pathKeySubdirectories']
                // ]
                // Then we need to get the array keys, that represents
                // the subdirectory name and recursive into the subdirectory
                // value.
                $subDirectories = $this->createSubDirectories(
                    (array) $subDirectories
                );
            } else {

                // Non-associative array, then the directory name is
                // the subDirectory.
                $directory = $subDirectories;

                // Set null for subDirectories as any subDirectories
                // structure was found.
                $subDirectories = null;
            }

            // Push the newly directory to subDirectories collection.
            $collection->push(
                new Directory($directory, $subDirectories)
            );
        }

        return $collection;
    }
}
