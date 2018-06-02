<?php

namespace Tests\Cloudpaths;

use Cloudpaths\Cloudpaths;
use Cloudpaths\Mapper;
use Cloudpaths\DirFactory;
use Cloudpaths\Directory;
use PHPUnit\Framework\TestCase;
use Illuminate\Config\Repository;

class CloudpathsTest extends TestCase
{
    /**
     * Should create a Mapper instance.
     * 
     * @return void
     */
    public function testCreateInstanceWith()
    {
        $cloudpaths = $this->newCloudpaths();

        $this->assertInstanceOf(
            Mapper::class,
            $cloudpaths
        );
    }

    /**
     * Should create a root directory.
     * 
     * @return void
     */
    public function testCreateWithFooRoot()
    {
        $root = 'foo'; 
        $cloudpaths = $this->newCloudpaths(compact('root'));

        $this->assertEquals(
            $cloudpaths->getRoot()->getName(),
            $root
        );
    }

    /**
     * Should map a new directory with empty subDirectories.
     * 
     * @return void
     */
    public function testMapANewDirectoryWithEmptySubDirectories()
    {
        $cloudpaths = $this->newCloudpaths();
        $cloudpaths->map('foo');
        
        $newlyDir = $cloudpaths->find('foo')->first();
        
        $this->assertInstanceOf(
            Directory::class,
            $newlyDir
        );

        $this->assertEmpty($newlyDir->getSubDirectories()->all());
    }

    /**
     * Should map a new directory with subDirectories.
     * 
     * @return void
     */
    public function testMapANewDirectoryWithPlainSubDirectories()
    {
        $subDirectories = [
            'foo',
            'bar'
        ];

        $cloudpaths = $this->newCloudpaths();
        $cloudpaths->map('foo', $subDirectories);
        
        $newlyDir = $cloudpaths->find('foo')->first();
        
        $this->assertInstanceOf(
            Directory::class,
            $newlyDir
        );

        $index = 0;
        foreach($newlyDir->getSubDirectories()->all() as $directory) {
            $this->assertEquals(
                $directory->getName(),
                $subDirectories[$index]
            );
            $index++;
        }
    }

    /**
     * Should map a new directory with subDirectories.
     * 
     * @return void
     */
    public function testMapANewDirectoryWithNestedSubDirectories()
    {
        $subDirectories = [
            'foo1' => [
                'foo2' => [
                    'bar',
                    'bar1'
                ]
            ]
        ];

        $cloudpaths = $this->newCloudpaths();
        $cloudpaths->map('foo', $subDirectories);
        
        $newlyDir = $cloudpaths->find('foo')->first();
        
        $this->assertInstanceOf(
            Directory::class,
            $newlyDir
        );
        
        $nestedFoo1 = $newlyDir->getSubdirectories()->first();
        
        $this->assertEquals(
            $nestedFoo1->getName(),
            'foo1'
        );

        $nestedFoo2 = $nestedFoo1->getSubdirectories()->first();
        
        $this->assertEquals(
            $nestedFoo2->getName(),
            'foo2'
        );

        $index = 0;
        foreach($nestedFoo2->getSubDirectories()->all() as $directory) {
            $this->assertEquals(
                $directory->getName(),
                $subDirectories['foo1']['foo2'][$index]
            );
            $index++;
        }
    }

     /**
     * Should map an array to new directory with empty subDirectories.
     * 
     * @return void
     */
    public function testMapArrayToNewDirectoryWithEmptySubDirectories()
    {
        $cloudpaths = $this->newCloudpaths();
        $cloudpaths->mapArray(['foo']);
        
        $newlyDir = $cloudpaths->find('foo')->first();
        
        $this->assertInstanceOf(
            Directory::class,
            $newlyDir
        );

        $this->assertEmpty($newlyDir->getSubDirectories()->all());
    }

    /**
     * Should map an array new directory with subDirectories.
     * 
     * @return void
     */
    public function testMapArrayToNewDirectoryWithPlainSubDirectories()
    {
        $subDirectories = [
            'foo',
            'bar'
        ];

        $cloudpaths = $this->newCloudpaths();
        $cloudpaths->mapArray([
            'foo' => $subDirectories
        ]);
        
        $newlyDir = $cloudpaths->find('foo')->first();
        
        $this->assertInstanceOf(
            Directory::class,
            $newlyDir
        );

        $index = 0;
        foreach($newlyDir->getSubDirectories()->all() as $directory) {
            $this->assertEquals(
                $directory->getName(),
                $subDirectories[$index]
            );
            $index++;
        }
    }

    /**
     * Should map an array to new directory with subDirectories.
     * 
     * @return void
     */
    public function testMapArrayToNewDirectoryWithNestedSubDirectories()
    {
        $subDirectories = [
            'foo1' => [
                'foo2' => [
                    'bar',
                    'bar1'
                ]
            ]
        ];

        $cloudpaths = $this->newCloudpaths();
        $cloudpaths->mapArray([
            'foo' => $subDirectories
        ]);
        
        $newlyDir = $cloudpaths->find('foo')->first();
        
        $this->assertInstanceOf(
            Directory::class,
            $newlyDir
        );
        
        $nestedFoo1 = $newlyDir->getSubdirectories()->first();
        
        $this->assertEquals(
            $nestedFoo1->getName(),
            'foo1'
        );

        $nestedFoo2 = $nestedFoo1->getSubdirectories()->first();
        
        $this->assertEquals(
            $nestedFoo2->getName(),
            'foo2'
        );

        $index = 0;
        foreach($nestedFoo2->getSubDirectories()->all() as $directory) {
            $this->assertEquals(
                $directory->getName(),
                $subDirectories['foo1']['foo2'][$index]
            );
            $index++;
        }
    }

    /**
     * Create a new cloudpaths instance.
     * 
     * @param  array $paths
     * @return Cloudpaths\Cloudpaths
     */
    protected function newCloudpaths(array $paths = [])
    {
        return new Cloudpaths(
            new Repository($paths),
            new DirFactory
        );
    }
}