<?php

namespace Cloudpaths;

use Closure;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use Cloudpaths\Contracts\Directory;
use Illuminate\Contracts\Support\Arrayable;

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
     * @param \Closure|null $callback
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
     * Return wheter the collection is empty.
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->items);
    }

    /**
     * Merge the collection items with the items of another collection.
     *
     * @param  Cloudpaths\DirectoryCollection $directories
     * @return this
     */
    public function merge(DirectoryCollection $directories)
    {
        $this->items = array_merge($this->items, $directories->toArray());
        return $this;
    }

    /**
     * Remove and return the first item on collection.
     *
     * @return Cloudpaths\Contracts\Directory|null
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * Iterate through each item and execute a callback with item.
     * The returned object will overwrite the original item.
     *
     * @throws  InvalidArgumentException
     *
     * @param  \Closure $callback
     * @return Cloudpaths\DirectoryCollection
     */
    public function map(Closure $callback)
    {
        // Array with all returned values that will compose
        // the new collection.
        $newItems = [];

        foreach ($this->items as $item) {

            // Execute user function and receive the result.
            $result = $this->call($callback, $item);

            if (! $result || ! $result instanceof Directory) {

                // Invalid returned value.
                throw new InvalidArgumentException(
                    'Invalid return value for collection.'
                );
            }

            // Overwrite the item with the result value.
            $newItems[] = $result;
        }

        return $this->newInstance($newItems);
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

    /**
     * Execute the callback with the directory as argument.
     *
     * @param  \Closure $callback
     * @param  Cloudpaths\Directory $directory
     * @return mixed
     */
    protected function call(Closure $callback, Directory $directory)
    {
        // Receive the function result.
        return call_user_func($callback, $directory);
    }

    /**
     * Create a new instance pushing each item to collection.
     *
     * @param  array $items
     * @return Cloudpaths\DirectoryCollection
     */
    protected function newInstance(array $items)
    {
        $collection = new static;

        foreach ($items as $item) {
            $collection->push($item);
        }

        return $collection;
    }
}
