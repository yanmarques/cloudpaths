<?php

namespace Cloudpaths;

use Cloudpaths\Contracts\Directory as DirectoryContract;

class Directory implements DirectoryContract
{
    /**
     * The directory name.
     *
     * @var string
     */
    protected $name;

    /**
     * Array with mapped subDirectories of current directory.
     *
     * @var array
     */
    protected $subDirectories;

    /**
     * The parent directory.
     *
     * @var Cloudpaths\Contracts\Directory
     */
    protected $parent;

    /**
     * Class constructor.
     *
     * @param  string $name
     * @param  Cloudpaths\DirectoryCollection|null $subDirectories
     * @return void
     */
    public function __construct(
        string $name,
        DirectoryCollection $subDirectories = null,
        DirectoryContract $parent = null
    ) {
        $this->name = $name;
        $this->parent = $parent;
        $this->setSubDirectories(
            $subDirectories ?: new DirectoryCollection
        );
    }

    /**
     * Get the directory name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the sub directories.
     *
     * @return Cloudpaths\DirectoryCollection
     */
    public function getSubDirectories()
    {
        return $this->subDirectories;
    }

    /**
     * Set the parent directory of the instance.
     *
     * @param  Cloudpaths\Contracts\Directory $parent
     * @return this
     */
    public function setParent(DirectoryContract $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent directory of the instance.
     *
     * @return Cloudpaths\Contracts\Directory|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the subDirectories collection mapping throgh them and
     * relating each subDirectory with the instance.
     *
     * @param  Cloudpaths\DirectoryCollection
     * @return void
     */
    protected function setSubDirectories(DirectoryCollection $subDirectories)
    {
        $this->subDirectories = $subDirectories->map(function ($directory) {
            return $directory->setParent($this);
        });
    }
}
