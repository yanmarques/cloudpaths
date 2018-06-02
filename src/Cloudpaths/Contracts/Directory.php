<?php

namespace Cloudpaths\Contracts;

interface Directory
{
    /**
     * Get the directory name.
     * 
     * @return string
     */
    public function getName();

    /**
     * Get the sub directories.
     * 
     * @return Cloudpaths\DirectoryCollection
     */
    public function getSubDirectories();
}