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
     * Set the parent directory of the instance.
     *
     * @param  Cloudpaths\Contracts\Directory $parent
     * @return this
     */
    public function setParent(Directory $parent);

    /**
     * Get parent directory of the instance.
     *
     * @return Cloudpaths\Contracts\Directory|null
     */
    public function getParent();

    /**
     * Build the full path until the root directory.
     *
     * @return string
     */
    public function getFullPath();
}
