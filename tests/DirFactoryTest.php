<?php

namespace Tests\Cloudpaths;

use Cloudpaths\DirFactory;
use Cloudpaths\Directory;
use PHPUnit\Framework\TestCase;

class DirFactoryTest extends TestCase
{
    /**
     * The directories factory.
     * 
     * @var Cloudpaths\DirFactory
     */
    private $factory;

    /**
     * Class constructor.
     * 
     * @return void
     */
    public function setUp()
    {
        $this->factory = new DirFactory;
    }

    /**
     * Should create an directory with empty subdirectories.
     * 
     * @return void
     */
    public function testCreateWithEmptySubdirectories()
    {
        $directory = $this->factory->create('foo');
        $this->assertEmpty($directory->getSubDirectories()->all());
    }

    /**
     * Should create an directory with foo name.
     * 
     * @return void
     */
    public function testCreateWithFooName()
    {
        $directory = $this->factory->create('foo');
        $this->assertEquals(
            $directory->getName(),
            'foo'
        );
    }

    /**
     * Should create an directory with an array of subdirectories.
     * 
     * @return void
     */
    public function testCreateWithPlainSubDirectories()
    {
        $subdirectories = [
            'foo',
            'bar'
        ];

        $directory = $this->factory->create('test', $subdirectories);
        
        $index = 0;
        foreach($directory->getSubDirectories()->all() as $directory) {
            $this->assertEquals(
                $directory->getName(),    
                $subdirectories[$index]
            );
            $index++;   
        }
    }

    /**
     * Should create an directory with an array of subdirectories.
     * 
     * @return void
     */
    public function testCreateWithNestedSubDirectories()
    {
        $subdirectories = [
            'foo' => [
                'foo1',
                'foo2'
            ]
        ];

        $directory = $this->factory->create('test', $subdirectories);
        
        // Nested subdirectory.
        $fooNested = $directory->getSubDirectories()->first(function ($directory) {
            return $directory->getName() == 'foo';
        });

        $this->assertInstanceOf(
            Directory::class,
            $fooNested
        );

        $index = 0;
        foreach($fooNested->getSubDirectories()->all() as $directory) {
            $this->assertEquals(
                $directory->getName(),
                $subdirectories['foo'][$index]
            );
            $index++;
        }
    }

    /**
     * Should create an directory with an array of subdirectories.
     * 
     * @return void
     */
    public function testCreateWithNestedDeepSubDirectories()
    {
        $subdirectories = [
            'foo' => [
                'foo1' => [
                    'foo2',
                    'foo3'
                ]
            ]
        ];

        $directory = $this->factory->create('test', $subdirectories);
        
        // Nested subdirectory.
        $fooNested = $directory->getSubDirectories()->first(function ($directory) {
            return $directory->getName() == 'foo';
        });

        $this->assertInstanceOf(
            Directory::class,
            $fooNested
        );

        $firstNested = $fooNested->getSubDirectories()->first(); 

        $this->assertEquals(
            $firstNested->getName(),
            'foo1'
        );

        $index = 0;
        foreach($firstNested->getSubDirectories()->all() as $directory) {
            $this->assertEquals(
                $directory->getName(),
                $subdirectories['foo']['foo1'][$index]
            );
            $index++;
        }
    }
}