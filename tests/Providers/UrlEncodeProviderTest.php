<?php

namespace Tests\Cloudpaths\Providers;

use Cloudpaths\Directory;
use Cloudpaths\DirFactory;
use PHPUnit\Framework\TestCase;
use Cloudpaths\DirectoryCollection;
use Cloudpaths\Providers\UrlEncodeProvider;

class UrlEncodeProviderTest extends TestCase
{
    /**
     * Should build the directory path.
     *
     * @return void
     */
    public function testHandleMethodWithDirectoryWithoutParent()
    {
        $provider = new UrlEncodeProvider(new DirFactory);
        
        $directory = new Directory('bar and foo');
        $collection = DirectoryCollection::make($directory);

        $expected = DirectoryCollection::make(
            new Directory(rawurlencode($directory->getName()))
        );

        $result = $provider->handle($collection, function ($collection) {
            return $collection;
        });

        $this->assertEquals(
            $result,
            $expected
        );
    }

    /**
     * Should encode all parents name of directory.
     *
     * @return void
     */
    public function testHandleMethodWithDirectoryWithParent()
    {
        $provider = new UrlEncodeProvider(new DirFactory);
        
        $parentDirectory = new Directory('foo and bar');
        $secondLevel = new Directory('bar and foo', null, $parentDirectory);
        $directory = new Directory('baz and bar2', null, $secondLevel);

        $collection = DirectoryCollection::make($directory);

        $expected = DirectoryCollection::make(
            new Directory(
                rawurlencode($directory->getName()),
                null,
                new Directory(
                    $provider->encodeDirectoryName($secondLevel),
                    null,
                    new Directory($provider->encodeDirectoryName($parentDirectory))
                )
            )
        );

        $result = $provider->handle($collection, function ($collection) {
            return $collection;
        });
        
        $this->assertEquals(
            $result,
            $expected
        );
    }
}
