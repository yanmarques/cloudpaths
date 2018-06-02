<?php

namespace Tests\Cloudpaths;

use Cloudpaths\Traits\ParsesDotNotation;
use PHPUnit\Framework\TestCase;

class CloudpathsTest extends TestCase
{
    use ParsesDotNotation;

    /**
     * Should return the original input as an array.
     * 
     * @return void
     */
    public function testParseInputWithNonDotNotationInput()
    {
        $expected = ['foo'];
        $received = $this->parseInput($expected[0]);

        $this->assertEquals(
            $expected,
            $received
        );
    }

    /**
     * Should return the input splited by the dots.
     * 
     * @return void
     */
    public function testParseInputWithDotNotationInput()
    {
        $expected = ['foo', 'bar'];
        $received = $this->parseInput(implode('.', $expected));
        
        $this->assertEquals(
            $expected,
            $received
        );
    }

    /**
     * Should return the input with allowed special characteres 
     * splited by the dots.
     * 
     * @return void
     */
    public function testParseInputWithSpecialChar()
    {
        $expected = ['foo/bar', 'bar-foo', 'baz_bar'];
        $received = $this->parseInput(implode('.', $expected));
        
        $this->assertEquals(
            $expected,
            $received
        );
    }
}