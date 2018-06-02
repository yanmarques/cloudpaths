<?php

namespace Cloudpaths;

use Cloudpaths\Contracts\Directory;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class DirectoryCollection implements Arrayable
{
    /**
     * The collection items.
     * 
     * @var array
     */
    protected $items = [];

    /**
     * Push new directory onto the end of collection.
     * 
     * @param  Cloudpaths\Directory
     * @return this
     */
    public function push(Directory $directory)
    {
        $this->items[] = $directory;
        return $this;
    }

    /**
     * Get the first directory on collection that pass the truth test.
     * 
     * @param \Closure\null $callback
     * @return Cloudpaths\Directory|null
     */
    public function first(Closure $callback = null)
    {
        return Arr::first($this->items, $callback);
    }

    /**
     * Get the last directory on collection.
     * 
     * @return Cloudpaths\Directory|null
     */
    public function last()
    {
        return Arr::last($this->items);
    }

    /**
     * Get all items as plain array.
     * 
     * @return array
     */
    public function all()
    {
        return $this->toArray();
    }

     /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->items;
    }
}