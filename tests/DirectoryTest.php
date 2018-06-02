<?php

namespace Tests\Cloudpaths;

use Cloudpaths\DirectoryCollection;
use Cloudpaths\Directory;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    /**
     * Should create an instance with foo name.
     * 
     * @return void
     */
    public function testCreateWithFooName()
    {
        $directory = new Directory('foo');
        $this->assertEquals(
            $directory->getName(),
            'foo'
        );
    }
    
    /**
     * Should create an instance with empty subdirectories.
     * 
     * @return void
     */
    public function testCreateWithEmptySubdirectories()
    {
        $directory = new Directory('foo');
        $this->assertEmpty($directory->getSubDirectories()->all());
    }

    /**
     * Should create an instance with subdirectories.
     * 
     * @return void
     */
    public function testCreateWithSubdirectories()
    {
        $subDirectories = [
            new Directory('foo'),
            new Directory('bar')
        ];

        $collection = new DirectoryCollection;

        foreach($subDirectories as $directory) {
            $collection->push($directory);
        }

        $directory = new Directory('foo', $collection);
        $this->assertEquals(
            $directory->getSubDirectories()->all(),
            $subDirectories
        );
    }
}
