<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Root Path
    |--------------------------------------------------------------------------
    |
    | This value is the name of the root directory for all paths. This is used
    | when searching for a path that retrieves the directory with root.
    |
    */
    'root' => env('CLOUDPATHS_ROOT', ''),

    /*
    |--------------------------------------------------------------------------
    | Cloud Paths
    |--------------------------------------------------------------------------
    |
    | This value is the paths directories to be mapped. This is used to build and
    | find when using dynamic urls for data storage. Generally when storing data
    | on dedicated servers, the url map may contain dynamic ids or slugs names.
    |
    */
    'paths' => [
        //
    ],
];
