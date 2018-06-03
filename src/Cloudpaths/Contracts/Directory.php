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

    /**
     * Get parent directory of the instance.
     *
     * @return Cloudpaths\Contracts\Directory|null
     */
    public function getParent();
}
