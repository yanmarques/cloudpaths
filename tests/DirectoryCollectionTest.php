<?php

namespace Tests\Cloudpaths;

use InvalidArgumentException;
use Cloudpaths\Directory;
use PHPUnit\Framework\TestCase;
use Cloudpaths\DirectoryCollection;

class DirectoryCollectionTest extends TestCase
{
    /**
     * Should add a new directory instance.
     *
     * @return void
     */
    public function testMakeWithValidValue()
    {
        $collection = DirectoryCollection::make(
            new Directory('foo')
        );
        
        $this->assertInstanceOf(
            Directory::class,
            $collection->first()
        );
    }

    /**
     * Should throw a InvalidArgumentException exception.
     *
     * @return void
     */
    public function testMakeWithInvalidValue()
    {
        $this->expectException(InvalidArgumentException::class);

        $collection = DirectoryCollection::make('foo');
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

        $collection = DirectoryCollection::make($directories);

        $newCollection = $collection->map(function ($directory) {
            return new Directory('baz');
        });

        $newCollection->each(function ($item) {
            $this->assertEquals($item->getName(), 'baz');
        });
    }

    /**
     * Should throw an InvalidArgumentException when an invalid
     * value is returned from map function.
     *
     * @return void
     */
    public function testMapWithInvalidReturnedValue()
    {
        $collection = DirectoryCollection::make(
            new Directory('foo')
        );

        $this->expectException(InvalidArgumentException::class);

        $collection->map(function ($directory) {
            return null;
        });
    }

    /**
     * Should
     *
     * @return void
     */
    public function testMergeMethodWithOtherCollection()
    {
        $fooDirectory = new Directory('foo');
        $barCollection = new Directory('bar');

        $collection = DirectoryCollection::make(
            $fooDirectory
        );

        $newCollection = DirectoryCollection::make(
            $barCollection
        );

        $expectedCollection = DirectoryCollection::make([
            $fooDirectory,
            $barCollection
        ]);

        $result = $collection->merge($newCollection);

        $this->assertEquals(
            $expectedCollection,
            $result
        );
    }
}
