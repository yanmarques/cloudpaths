<?php

namespace Cloudpaths\Traits;

trait ParsesDotNotation
{
    /**
     * Parse a input string using dot notation.
     *
     * @param  string $input
     * @return array
     */
    public function parseInput(string $input)
    {
        // Get each firectory fragment.
        preg_match_all('/([a-zA-Z0-9\*_\/-]+)\.?/', $input, $fragments);

        // Get only matching groups.
        return (array) $fragments[1];
    }
}
