<?php

namespace Cloudpaths;

use InvalidArgumentException;
use Illuminate\Support\Collection;
use Cloudpaths\Contracts\Directory as DirectoryContract;

class DirectoryCollection extends Collection
{
    /**
     * Class constructor.
     *
     * @return void
     */
    public function __construct($items = [])
    {
        static::setItemsProxy($items, $this);
    }

    /**
     * Create a new collection instance if the value isn't one already.
     *
     * @param  mixed  $items
     * @return static
     */
    public static function make($items = [])
    {
        $instance = new static;

        // Proxy the items to the instance.
        static::setItemsProxy($items, $instance);

        return $instance;
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        throw_unless($value instanceof DirectoryContract, new InvalidArgumentException(
            'Value pushed must be a '.
            DirectoryContract::class.' instance.'
        ));

        parent::offsetSet($key, $value);

        return $this;
    }

    /**
     * Deep clone the collection object.
     *
     * @return Cloudpaths\DirectoryCollection
     */
    public function replicate()
    {
        return static::make($this->transform(function ($directory) {
            return clone $directory;
        })->toArray());
    }

    /**
     * Proxy the items setting of the collection instance.
     *
     * @param  mixed $items
     * @param  Cloudpaths\DirectoryCollection $instance
     * @return void
     */
    protected static function setItemsProxy($items = [], DirectoryCollection $instance)
    {
        if (! is_array($items)) {

            // Create an array of the item.
            $items = [$items];
        }

        foreach ($items as $item) {

            // Set each item as new item passing the offset proxy to force the
            // items to implement the directory interface.
            $instance->offsetSet(null, $item);
        }
    }
}
