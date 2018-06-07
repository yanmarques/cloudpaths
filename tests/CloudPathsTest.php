<?php

namespace Tests\Cloudpaths;

use Cloudpaths\Cloudpaths;
use Cloudpaths\Mapper;
use Cloudpaths\DirFactory;
use Cloudpaths\Directory;
use PHPUnit\Framework\TestCase;
use Illuminate\Config\Repository;
use Cloudpaths\Contracts\Factory;
use Illuminate\Container\Container;
use Cloudpaths\Contracts\Directory as DirectoryContract;

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
        
        $newlyDir = $cloudpaths->findDirectory('foo')->first();
        
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
        
        $newlyDir = $cloudpaths->findDirectory('foo')->first();
        
        $this->assertInstanceOf(
            Directory::class,
            $newlyDir
        );

        $index = 0;
        foreach ($newlyDir->getSubDirectories()->all() as $directory) {
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
        
        $newlyDir = $cloudpaths->findDirectory('foo')->first();
        
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
        foreach ($nestedFoo2->getSubDirectories()->all() as $directory) {
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
        
        $newlyDir = $cloudpaths->findDirectory('foo')->first();
        
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
        
        $newlyDir = $cloudpaths->findDirectory('foo')->first();
        
        $this->assertInstanceOf(
            Directory::class,
            $newlyDir
        );

        $index = 0;
        foreach ($newlyDir->getSubDirectories()->all() as $directory) {
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
        
        $newlyDir = $cloudpaths->findDirectory('foo')->first();
        
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
        foreach ($nestedFoo2->getSubDirectories()->all() as $directory) {
            $this->assertEquals(
                $directory->getName(),
                $subDirectories['foo1']['foo2'][$index]
            );
            $index++;
        }
    }

    /**
     * Should find the valid path directory.
     *
     * @return void
     */
    public function testFindDirectoryWithValidPath()
    {
        $paths = [
            'foo' => [
                'bar' => [
                    'baz'
                ],
            ]
        ];

        $cloudpaths = $this->newCloudPaths(compact('paths'));
        $found = $cloudpaths->findDirectory('foo.baz');
        
        $this->assertFalse($found->isEmpty());

        $directory = $found->first();
        $this->assertEquals($directory->getName(), 'baz');
        
        $this->assertEquals($directory->getParent()->getName(), 'bar');
        
        $this->assertEquals(
            $directory->getParent()->getParent()->getName(),
            'foo'
        );
    }

    /**
     * Should get an empty collection.
     *
     * @return void
     */
    public function testFindDirectoryWithInvalidPath()
    {
        $paths = [
            'foo' => [
                'bar' => [
                    'baz'
                ],
            ]
        ];

        $cloudpaths = $this->newCloudPaths(compact('paths'));
        $found = $cloudpaths->findDirectory('foo.any');

        $this->assertTrue($found->isEmpty());
    }

    /**
     * Should get 2 items on collection with bar name.
     * Shoudl assert each subDirectory name from found
     * directories.
     *
     * @return void
     */
    public function testFindDirectoryManyWithValidPath()
    {
        $paths = [
            'foo' => [
                'bar',
                'baz' => [
                    'bar'
                ]
            ]
        ];

        $cloudpaths = $this->newCloudPaths(compact('paths'));
        $found = $cloudpaths->findDirectory('foo.bar');

        $this->assertFalse($found->isEmpty());

        $directory = $found->shift();
        $this->assertEquals(
            $directory->getName(),
            'bar'
        );

        // Assert the top level directory.
        $this->assertEquals(
            $directory->getParent()->getName(),
            'foo'
        );

        $secondDirectory = $found->first();

        // Assert directory name.
        $this->assertEquals(
            $secondDirectory->getName(),
            'bar'
        );

        // First top level directory.
        $topLevelDirectory = $secondDirectory->getParent();

        // Assert the first top level name.
        $this->assertEquals(
            $topLevelDirectory->getName(),
            'baz'
        );

        // Assert the top level directory.
        $this->assertEquals(
            $topLevelDirectory->getParent()->getName(),
            'foo'
        );
    }

    /**
     * Should get the top level directory.
     * Should assert each subDirectory name.
     *
     * @return void
     */
    public function testFindDirectoryToplLevelDirectory()
    {
        $paths = [
            'foo' => [
                'bar' => [
                    'baz'
                ],
            ]
        ];

        $cloudpaths = $this->newCloudPaths(compact('paths'));
        $found = $cloudpaths->findDirectory('foo');
        
        $this->assertFalse($found->isEmpty());

        $directory = $found->first();

        // Assert top level directory name.
        $this->assertEquals($directory->getName(), 'foo');

        $firstSubDirectoryLevel = $directory->getSubDirectories()->first();

        // Assert first subDirectory level name.
        $this->assertEquals($firstSubDirectoryLevel->getName(), 'bar');

        $secondSubDirectoryLevel = $firstSubDirectoryLevel->getSubDirectories()->first();

        // Assert second subDirectory level name.
        $this->assertEquals($secondSubDirectoryLevel->getName(), 'baz');
    }

    /**
     * Should assert the top level directory parent is the root
     * of the configuration root.
     *
     * @return void
     */
    public function testGetParentOfTopLevelDirectory()
    {
        $config = [
            'root' => 'bar',
            'paths' => [
                'foo'
            ]
        ];

        $cloudpaths = $this->newCloudpaths($config);
        $collectionFound = $cloudpaths->findDirectory('foo');

        $this->assertFalse($collectionFound->isEmpty());

        $topLevelDirectory = $collectionFound->first();
        $this->assertEquals(
            $topLevelDirectory->getParent()->getName(),
            $config['root']
        );
    }

    /**
     * Should change the root directory from root resolver.
     *
     * @return void
     */
    public function testRootResolverReturningValidDirectory()
    {
        $config = [
            'root' => 'foo'
        ];

        $cloudpaths = $this->newCloudpaths($config);
        $cloudpaths->setRootResolver(
            function (Factory $factory, $root) {
                return $factory->create('newRoot');
            }
        );

        $this->assertEquals(
            $cloudpaths->getRoot()->getName(),
            'newRoot'
        );
    }

    /**
     * Should not change the root directory.
     *
     * @return void
     */
    public function testRootResolverReturningNull()
    {
        $config = [
            'root' => 'foo'
        ];

        $cloudpaths = $this->newCloudpaths($config);
        $cloudpaths->setRootResolver(
            function ($root) {
                // Do some stuff with root directory and return null.
            }
        );

        $this->assertEquals(
            $cloudpaths->getRoot()->getName(),
            $config['root']
        );
    }

    /**
     * Should throw \InvalidArgumentException.
     *
     * @return void
     */
    public function testRootResolverReturningInvalidObject()
    {
        $cloudpaths = $this->newCloudpaths();
        
        $this->expectException(\InvalidArgumentException::class);

        $cloudpaths->setRootResolver(
            function ($root) {
                return 'foo';
            }
        )->getRoot();
    }

    /**
     * Should get a valid path for directory.
     *
     * @return void
     */
    public function testFindWithValidPath()
    {
        $expectedPath = 'root/foo/bar/baz';

        $config = [
            'root' => 'root',
            'paths' => [
                'foo' => [
                    'bar' => [
                        'baz'
                    ]
                ]
            ]
        ];

        $cloudpaths = $this->newCloudpaths($config);

        $found = $cloudpaths->find('foo.baz');
        
        $this->assertFalse($found->isEmpty());
        $this->assertEquals($expectedPath, $found->first());
    }

    /**
     * Should get an empty collection.
     *
     * @return void
     */
    public function testFindWithInvalidPath()
    {
        $config = [
            'paths' => [
                'foo' => [
                    'bar'
                ]
            ]
        ];

        $cloudpaths = $this->newCloudpaths($config);

        $found = $cloudpaths->find('foo.any');
        
        $this->assertTrue($found->isEmpty());
    }

    /**
     * Should get the path replacing bar to baz.
     *
     * @return void
     */
    public function testFindWithValidPathWithValidReplace()
    {
        $expectedPath = 'root/foo/baz/any';
        $config = [
            'root' => 'root',
            'paths' => [
                'foo' => [
                    'bar' => [
                        'any'
                    ]
                ]
            ]
        ];

        $cloudpaths = $this->newCloudpaths($config);

        $found = $cloudpaths->find('foo.any', ['bar' => 'baz']);
        
        $this->assertFalse($found->isEmpty());
        $this->assertEquals(
            $found->first(),
            $expectedPath
        );
    }

    /**
     * Should replace the multiple replacements.
     *
     * @return void
     */
    public function testFindValidPathWithReplaces()
    {
        $expectedPath = 'root/foo/baz/baz2';
        $config = [
            'root' => 'root',
            'paths' => [
                'foo' => [
                    'bar' => [
                        'baz1'
                    ]
                ]
            ]
        ];

        $cloudpaths = $this->newCloudpaths($config);

        $found = $cloudpaths->find('foo.baz1', [
            'bar' => 'baz',
            'baz1' => 'baz2'
        ]);
        
        $this->assertFalse($found->isEmpty());
        $this->assertEquals(
            $found->first(),
            $expectedPath
        );
    }
    
    /**
     * Create a new cloudpaths instance.
     *
     * @param  array $paths
     * @return Cloudpaths\Cloudpaths
     */
    protected function newCloudpaths(array $paths = [])
    {
        $container = new Container;
        $container->bind(Factory::class, DirFactory::class);

        return new Cloudpaths(
            $container,
            new Repository($paths),
            new DirFactory
        );
    }
}
