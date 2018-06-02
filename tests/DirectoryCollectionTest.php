<?php

namespace Tests\Cloudpaths;

use Cloudpaths\DirectoryCollection;
use Cloudpaths\Directory;
use PHPUnit\Framework\TestCase;

class DirectoryCollectionTest extends TestCase
{
    /**
     * Should add a new directory instance.
     * 
     * @return void
     */
    public function testPushAddDirectoryToCollection()
    {
        $collection = new DirectoryCollection;
        $collection->push(new Directory('foo'));
        $this->assertInstanceOf(
            Directory::class,
            $collection->first()
        );
    }

    /**
     * Should receive null.
     * 
     * @return void
     */
    public function testFirstOnEmptyCollection()
    {
        $collection = new DirectoryCollection;
        $this->assertNull($collection->first());
    }

    /**
     * Should find first directory with foo name.
     * 
     * @return void
     */
    public function testFirstDirectoryWithFooName()
    {
        $fooDirectory = new Directory('foo');
        $collection = new DirectoryCollection;
        $collection->push($fooDirectory);
        $this->assertInstanceOf(
            Directory::class,
            $collection->first(function (Directory $directory) {
                return $directory->getName() == 'foo';
            })
        );
    }

    /**
     * Should find first directory with foo name, but only
     * bar is registered, receive null.
     * 
     * @return void
     */
    public function testFirstDirectoryWithBarName()
    {
        $barDirectory = new Directory('bar');
        $collection = new DirectoryCollection;
        $collection->push($barDirectory);
        $this->assertNull(
            $collection->first(function (Directory $directory) {
                return $directory->getName() == 'foo';
            })
        );
    }

    /**
     * Should receive the same directory pushed.
     * 
     * @return void
     */
    public function testLastWithOnlyOneDirectory()
    {
        $fooDirectory = new Directory('foo');
        $collection = new DirectoryCollection;
        $collection->push($fooDirectory);
        $this->assertEquals(
            $fooDirectory,
            $collection->last()
        );
    }

    /**
     * Should receive the last pushed directory.
     * 
     * @return void
     */
    public function testLastWithMoreDirectories()
    {
        $fooDirectory = new Directory('foo');
        $collection = new DirectoryCollection;
        
        $collection->push(new Directory('bar'))
            ->push($fooDirectory);
        
        $this->assertEquals(
            $fooDirectory,
            $collection->last()
        );
    }

    /**
     * Should receive null due to empty collection.
     * 
     * @return void
     */
    public function testLastWithEmptyCollection()
    {
        $collection = new DirectoryCollection;
           
        $this->assertNull($collection->last());
    }

    /**
     * Should return empty array.
     * 
     * @return void
     */
    public function testAllWithEmptyCollection()
    {
        $collection = new DirectoryCollection;
        $this->assertEmpty($collection->all());
    }

    /**
     * Should return an array with all directories.
     * 
     * @return void
     */
    public function testAllWithDirectoriesList()
    {
        $directories = [
            new Directory('foo'),
            new Directory('bar')
        ];

        $collection = new DirectoryCollection;
        
        foreach($directories as $directory) {
            $collection->push($directory);
        }

        $this->assertEquals($directories, $collection->all());
    }
}