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
     * Class constructor.
     *
     * @param  string $name
     * @param  Cloudpaths\DirectoryCollection|null $subDirectories
     * @return void
     */
    public function __construct(string $name, DirectoryCollection $subDirectories = null)
    {
        $this->name = $name;
        $this->subDirectories = $subDirectories ?: new DirectoryCollection;
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
}
