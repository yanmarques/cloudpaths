<?php

namespace Tests\Cloudpaths;

use Cloudpaths\Directory;
use Cloudpaths\Search\Scope;
use Cloudpaths\Search\Engine;
use Tests\Cloudpaths\TestCase;
use Cloudpaths\Cloudpaths;

class EngineTest extends TestCase
{
    /**
     * Should create an search engine with directories.
     *
     * @return void
     */
    public function testCreateEngineWithDirectories()
    {
        $collection = $this->createDirectoryCollection([
            new Directory('foo')
        ]);

        // Create the search engine.
        $engine = new Engine($collection);
        
        $this->assertEquals(
            $engine->getScope()->getDirectoryCollection(),
            $collection
        );
    }

    /**
     * Should search and find the foo directory.
     *
     * @return void
     */
    public function testSearchWithValidDirectory()
    {
        // Expected directory to find.
        $expectedDirectory = new Directory('foo');

        $collection = $this->createDirectoryCollection([
            $expectedDirectory
        ]);

        // Create the search engine.
        $engine = new Engine($collection);

        $foundCollection = $engine->search($expectedDirectory->getName());

        $this->assertEquals(
            $foundCollection->first(),
            $expectedDirectory
        );
    }

    /**
     * Should change the engine collection scope.
     *
     * @return void
     */
    public function testScopeWithOtherCollection()
    {
        $collection = $this->createDirectoryCollection([
            new Directory('foo')
        ]);

        // Create the search engine.
        $engine = new Engine($collection);

        $newCollectionScope = $this->createDirectoryCollection([
            new Directory('bar')
        ]);

        // Change the engine scope.
        $engine->setScope($newCollectionScope);

        $this->assertEquals(
            $engine->getScope()->getDirectoryCollection(),
            $newCollectionScope
        );
    }

    /**
     * Should find the directory of the new scope.
     * Should not find directory of earliers scopes.
     *
     * @return void
     */
    public function testSearchWithAnotherScope()
    {
        // Directory of old scope.
        $earlyDirectory = new Directory('foo');

        $collection = $this->createDirectoryCollection([
            $earlyDirectory
        ]);

        // Create the search engine.
        $engine = new Engine($collection);

        // Expected directory to be found.
        $expectedDirectory = new Directory('bar');

        $newCollectionScope = $this->createDirectoryCollection([
            $expectedDirectory
        ]);

        // Change the engine scope.
        $engine->setScope($newCollectionScope);

        // With the new scope, the foo directory should not be found.
        $this->assertTrue(
            $engine->search($earlyDirectory->getName())
                ->isEmpty()
        );

        $this->assertEquals(
            $engine->search($expectedDirectory->getName())->first(),
            $expectedDirectory
        );
    }
}
