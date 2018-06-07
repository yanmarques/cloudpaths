<?php

namespace Cloudpaths\Contracts;

use Cloudpaths\DirectoryCollection;

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
     * Set the subDirectories collection mapping through them and
     * relating each subDirectory with the instance.
     *
     * @param  Cloudpaths\DirectoryCollection
     * @return this
     */
    public function setSubDirectories(DirectoryCollection $subDirectories);

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
     * Get the top level directory.
     *
     * @return Cloudpaths\Contracts\Directory
     */
    public function getTopLevelParent();

    /**
     * Build the parents history until the top level directory.
     *
     * @return Cloudpaths\DirectoryCollection
     */
    public function getParentHistory();
}
