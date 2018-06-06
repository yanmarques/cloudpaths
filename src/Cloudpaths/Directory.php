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
     * Get the top level directory.
     *
     * @return Cloudpaths\Contracts\Directory
     */
    public function getTopLevelParent()
    {
        return Arr::first($this->getParentHistory());
    }

    /**
     * Build the parents history until the top level directory.
     *
     * @return Cloudpaths\DirectoryCollection
     */
    public function getParentHistory()
    {
        // Get the first nested parent of the current directory.
        $nestedParent = $this->parent;

        // Stores the parents history on a collection. Initializes with
        // the current directory.
        $history = DirectoryCollection::make($this);

        while ($nestedParent) {

            // Add the parent name to the beggining of the list while
            // the has a nested parent.
            $history[] = $nestedParent;

            // Set the next nested parent as the parent of the last
            // nested parent.
            $nestedParent = $nestedParent->getParent();
        }

        // Return the new collection with the reversed directories.
        return $history->reverse();
    }

    /**
     * Set the subDirectories collection mapping throgh them and
     * relating each subDirectory with the instance.
     *
     * @param  Cloudpaths\DirectoryCollection
     * @return void
     */
    public function setSubDirectories(DirectoryCollection $subDirectories)
    {
        $this->subDirectories = $subDirectories->map(function ($directory) {
            return $directory->setParent($this);
        });
    }
}
