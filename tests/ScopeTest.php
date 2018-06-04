<?php

namespace Tests\Cloudpaths;

use Cloudpaths\Directory;
use Cloudpaths\Search\Scope;
use Tests\Cloudpaths\TestCase;

class ScopeTest extends TestCase
{
    /**
     * Should assert the collection set for scope is empty.
     *
     * @return void
     */
    public function testSetDirectoryCollectionWithEmptyCollection()
    {
        $scope = new Scope;
        $scope->setDirectoryCollection($this->createDirectoryCollection());
        $this->assertTrue($scope->getDirectoryCollection()->isEmpty());
    }

    /**
     * Should assert the collection set for scope is not empty.
     * Should assert the collection set is the expected.
     *
     * @return void
     */
    public function testSetDirectoryCollectionWithNotEmptyCollection()
    {
        $scope = new Scope;
    
        $expectedCollection = $this->createDirectoryCollection([
            new Directory('foo')
        ]);

        $scope->setDirectoryCollection($expectedCollection);

        $this->assertFalse($scope->getDirectoryCollection()->isEmpty());
        $this->assertEquals(
            $scope->getDirectoryCollection(),
            $expectedCollection
        );
    }
}
