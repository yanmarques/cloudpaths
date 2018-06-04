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
        
        foreach ($directories as $directory) {
            $collection->push($directory);
        }

        $this->assertEquals($directories, $collection->all());
    }

    /**
     * Should map through collection items and change
     * their names to baz.
     *
     * @return void
     */
    public function testMapWithValidReturnedValue()
    {
        $directories = [
            new Directory('foo'),
            new Directory('bar')
        ];

        $collection = new DirectoryCollection;
        
        foreach ($directories as $directory) {
            $collection->push($directory);
        }

        $newCollection = $collection->map(function ($directory) {
            return new Directory('baz');
        });

        foreach ($newCollection->all() as $item) {
            $this->assertEquals($item->getName(), 'baz');
        }
    }

    /**
     * Should throw an InvalidArgumentException when an invalid
     * value is returned from map function.
     *
     * @return void
     */
    public function testMapWithInValidReturnedValue()
    {
        $directories = [
            new Directory('foo')
        ];

        $collection = new DirectoryCollection;
        
        foreach ($directories as $directory) {
            $collection->push($directory);
        }

        $this->expectException(\InvalidArgumentException::class);

        $collection->map(function ($directory) {
            return null;
        });
    }

    /**
     * Should assert the collection is empty.
     *
     * @return void
     */
    public function testIsEmptyWithAEmptyCollection()
    {
        $collection = new DirectoryCollection;
        $this->assertTrue($collection->isEmpty());
    }

    /**
     * Should assert the collection not is empty.
     *
     * @return void
     */
    public function testIsEmptyWithNotEmptyCollection()
    {
        $collection = new DirectoryCollection;

        // Add items to collection, so now on the collection
        // should not be empty.
        $collection->push(new Directory('foo'));
        $this->assertFalse($collection->isEmpty());
    }

    /**
     * Should merge a collection items with another when is empty.
     *
     * @return void
     */
    public function testMergeCollectionWithEmptyCollection()
    {
        $collection = new DirectoryCollection;

        // Only this directory should be kept on collection.
        $onlyDirectory = new Directory('foo');
        $collection->push($onlyDirectory);

        // Empty collection to merge.
        $collectionToMerge = new DirectoryCollection;
        $collection->merge($collectionToMerge);
        
        $this->assertEquals(
          $collection->toArray(),
          [$onlyDirectory]
        );
    }

    /**
     * Should merge a collection items with another when has items.
     *
     * @return void
     */
    public function testMergeCollectionWithNotEmptyCollection()
    {
        $collection = new DirectoryCollection;

        // Only this directory that will be pushed on collection.
        $onlyDirectory = new Directory('foo');
        $collection->push($onlyDirectory);

        $collectionToMerge = new DirectoryCollection;

        // New directories to add on collection to merge.
        $directoriesToAdd = [
            new Directory('foo'),
            new Directory('bar'),
            new Directory('baz')
        ];

        foreach ($directoriesToAdd as $directory) {
            $collectionToMerge->push($directory);
        }

        $collection->merge($collectionToMerge);
        
        $this->assertEquals(
          $collection->toArray(),
          array_merge([$onlyDirectory], $directoriesToAdd)
        );
    }

    /**
     * Should return null from shift method.
     *
     * @return void
     */
    public function testShiftWithEmptyCollection()
    {
        $collection = new DirectoryCollection;
        $this->assertNull($collection->shift());
    }

    /**
     * Should return the first registered item.
     * Should remove item from collection.
     *
     * @return void
     */
    public function testShiftWithNotEmptyCollection()
    {
        $directories = [
            new Directory('foo'),
            new Directory('bar')
        ];

        $collection = new DirectoryCollection;

        // Register the directories.
        foreach ($directories as $directory) {
            $collection->push($directory);
        }

        $this->assertEquals($collection->shift(), $directories[0]);
        $this->assertEquals($collection->all(), [$directories[1]]);
    }
}
