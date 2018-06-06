<?php

namespace Tests\Cloudpaths\Providers;

use Cloudpaths\Directory;
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
    public function testHandleMethodWithUrl()
    {
        $provider = new UrlEncodeProvider;
        
        $url = 'foo/bar and baz';
        $expected = collect(rawurlencode($url));

        $collection = [$url];

        $result = $provider->handle($collection, function ($collection) {
            return $collection;
        });

        $this->assertEquals(
            $result,
            $expected
        );
    }
}
