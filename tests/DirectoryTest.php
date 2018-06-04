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

        foreach ($subDirectories as $directory) {
            $collection->push($directory);
        }

        $directory = new Directory('foo', $collection);
        $this->assertEquals(
            $directory->getSubDirectories()->all(),
            $subDirectories
        );
    }

    /**
     * Should create an instance with parent directory.
     *
     * @return void
     */
    public function testCreateWithParent()
    {
        $subDirectories = [
            new Directory('foo'),
            new Directory('bar')
        ];

        $collection = new DirectoryCollection;

        foreach ($subDirectories as $directory) {
            $collection->push($directory);
        }

        $parent = new Directory('baz');
        $directory = new Directory('foo', $collection, $parent);
       
        $this->assertEquals(
            $directory->getParent(),
            $parent
        );
    }

    /**
     * Should assert that each top level subdirectory has
     * the current directory instance as parent.
     *
     * @return void
     */
    public function testParentSubDirectories()
    {
        $subDirectories = [
            new Directory('foo'),
            new Directory('bar')
        ];

        $collection = new DirectoryCollection;

        foreach ($subDirectories as $directory) {
            $collection->push($directory);
        }

        $directory = new Directory('foo', $collection);
        
        $subDirectories = $directory->getSubDirectories()->all();

        foreach ($subDirectories as $subDirectory) {
            $this->assertEquals(
                $directory,
                $subDirectory->getParent()
            );
        }
    }

    /**
     * Should change the parent for directory.
     *
     * @return void
     */
    public function testSettingANewParentForDirectory()
    {
        $parent = new Directory('root');
        $directory = new Directory('foo', null, $parent);
        $newParent = new Directory('newRoot');
        $directory->setParent($newParent);
        
        $this->assertEquals(
            $directory->getParent(),
            $newParent
        );
    }

    /**
     * Should build the full directory with the parent.
     *
     * @return void
     */
    public function testGetFullPathWithParentDirectory()
    {
        $parent = new Directory('root');
        $directory = new Directory('foo', null, $parent);
        $this->assertEquals(
            $directory->getFullPath(),
            sprintf('%s/%s', $parent->getName(), $directory->getName())
        );
    }

    /**
     * Should build only the current directory because directory
     * has no parent.
     *
     * @return void
     */
    public function testGetFullPathWithoutParentDirectory()
    {
        $directory = new Directory('foo');
        $this->assertEquals(
            $directory->getFullPath(),
            $directory->getName()
        );
    }
}
