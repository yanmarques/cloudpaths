# Cloudpaths for Laravel

[![Build Status](https://travis-ci.org/yanmarques/cloudpaths.svg?branch=master)](https://travis-ci.org/yanmarques/cloudpaths)
[![StyleCI](https://github.styleci.io/repos/135823301/shield?branch=master)](https://github.styleci.io/repos/135823301)

A mapper to create paths for cloud storage for Laravel.

## Cloudpaths Api

The cloudpaths class handles the creation of directories and subdirectories from primitive types, as string and arrays. 
Directories knows it's subdirectories and his parent directory, if any. When creating cloudpaths, an array configuration can be provided with:

* ```root```: Indicates the global root directory.
* ```paths```: Indicates the directories to map.

To create a cloudpaths instance:
```php
$config = [
    'root' => 'foo',
    'paths => [
      'bar
     ]
 ];
  
  // Must implements \Illuminate\Contracts\Config\Repository interface
  $repository = new Repository($config);
  
  $cloudpaths = new Cloudpaths($repository);
````

By default the cloudpaths uses the a directory factory and a searcher, but you are able to change them passing on constructor.

Example:
```php
// The custom factory must implement Cloudpaths\Contracts\Factory.
$cloudpaths = new Cloudpaths($repository, new CustomFactory);
```
