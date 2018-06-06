<?php

namespace Tests\Cloudpaths\Providers;

use Cloudpaths\Directory;
use PHPUnit\Framework\TestCase;
use Cloudpaths\DirectoryCollection;
use Cloudpaths\Providers\UrlBuilderProvider;

class UrlBuilderProviderTest extends TestCase
{
    /**
     * Should build the directory path.
     *
     * @return void
     */
    public function testHandleMethodWithDirectory()
    {
        $topLevelDirectory = new Directory('foo');
        $directory = new Directory('bar', null, $topLevelDirectory);

        $provider = new UrlBuilderProvider;
        
        $collection = DirectoryCollection::make([
            $directory
        ]);

        $expectedResult = collect('foo/bar');

        $result = $provider->handle($collection, function ($collection) {
            return $collection;
        });

        $this->assertEquals(
            $result,
            $expectedResult
        );
    }
}
