<?php

namespace Cloudpaths;

use Illuminate\Support\Arr;
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
     * Build the full path until the root directory.
     *
     * @return string
     */
    public function getFullPath()
    {
        // The nested parent for each parent directory.
        $nestedParent = $this->parent;

        // Stores the full path. Initializes with the current
        // directory name.
        $fullPath = [$this->name];

        while ($nestedParent) {

            // Add the parent name to the beggining of the list.
            $fullPath = Arr::prepend($fullPath, $nestedParent->getName());

            // Set the next nested parent as the parent of the last
            // nested parent.
            $nestedParent = $nestedParent->getParent();
        }

        return implode('/', $fullPath);
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
