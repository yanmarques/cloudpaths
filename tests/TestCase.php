<?php

namespace Tests\Cloudpaths;

use Cloudpaths\DirectoryCollection;
use PHPUnit\Framework\TestCase as UnitTestCase;

class TestCase extends UnitTestCase
{
    /**
     * Create a directory collection with directories.
     *
     * @param  array $directories
     * @return Cloudpaths\DirectoryCollection
     */
    protected function createDirectoryCollection(array $directories = [])
    {
        $collection = new DirectoryCollection;

        foreach ($directories as $directory) {
            $collection->push($directory);
        }

        return $collection;
    }
}
