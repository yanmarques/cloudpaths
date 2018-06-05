# Cloudpaths for Laravel

[![Build Status](https://travis-ci.org/yanmarques/cloudpaths.svg?branch=master)](https://travis-ci.org/yanmarques/cloudpaths)
[![StyleCI](https://github.styleci.io/repos/135823301/shield?branch=master)](https://github.styleci.io/repos/135823301)

A mapper to create dynamic urls for storing data on cloud. 

## Table of Contents

* [Installing](#installing)
* [Running tests](#running-tests)
* [Getting Started](#getting-started)
* [Api](#api)
    - [Mapping a new directory](#mapping-a-new-directory)
    - [Mapping an array](#mapping-an-array-of-directories)
    - [Find a directory](#find-a-directory-path-by-dot-notation-input)
    - [Find a directory replacing dynamic directories](#find-a-directory-replacing-a-dynamic-directory-name)    

# Installing

To install you can add as a dependency for your project with composer.

```shell
composer require yanmarques/cloudpaths
```

# Running tests

Tests are good for any project, skipping tests may kill kittens.

```shell
./vendor/bin/phpunit -c phpunit.xml
```

# Getting Started
## Register the Service Provider

The package registers itself using the service provider. For this you must add the service provider to the ```providers``` list on your ```config/app.php```.

```php
'providers' => [
    ...
    Cloudpaths\CloudpathsServiceProvider::class
]
```

## Facades (Optional)

Laravel allows us to use Facade classes as aliases for registered services on the application container. To use the Cloudpaths Facade class you must add the Facade path to the ```aliases``` of the ```config/app.php```.

```php
'aliases' => [
    ...
    Cloudpaths\Facades\Cloudpaths::class
]
```

## Configuration

Once the service provider has been registered the Cloudpaths application, it will try to read the configuration from file. To configure it, you must publish the configuration file to the ```config``` path. Open the console and run on your project:

```shell
php artisan vendor:publish Cloudpaths\CloudpathsServiceProvider::class
```

## Cloudpaths

The Cloudpaths class is a mapper class, which maps each directory as directory classes that implements the ```Cloudpaths\Contracts\Directory.php``` interface. When a new directory is been mapped, a new directory class is created, with their subdirectories, also composed by directory classes. 

The ```Cloudpaths\DirectoryCollection.php``` collects a bunch of directories that implements the directory interface. It extends the wonderfull ```Illuminate\Support\Collection.php``` from Laravel, inheriting it's methods. Although the collection
proxies the method that stores a new item to accepts only directory items.

* To create a new cloudpaths instance:
```php
// Must implements Illuminate\Contracts\Config\Repostory interface.
$repository = new Repository;
  
$cloudpaths = new Cloudpaths($repository);
````

To build a new directory and it's subdirectories, the Cloudpaths uses a Factory class to handle this operation. The factory implements the ```Cloudpaths\Contracts\Factory.php``` interface. The default factory is the ```Cloudpaths\DirFactory.php``` Factory implementation, but you can implement your and pass as second argument to Cloudpaths.

Example:
```php
// The custom factory that implements Cloudpaths\Contracts\Factory::class.
$cloudpaths = new Cloudpaths($repository, new CustomFactory);
```

## Searcher

The searcher is a search engine implemention to find for a directory by name on a given collection scope. The search engine uses scopes to change the search view. The scope will be a directory collection where only the current scope is the search target, when the scope is changed, the search is also changed.

You can implement your own searcher that implements the ```Cloudpaths\Contracts\Searcher.php``` interface and provide as the third argument for Cloudpaths. The default searcher is the ```Cloudpaths\Search\Engine.php```.

Example:
```php
$cloudpaths = new Cloudpaths($repository, $factory, new CustomSearcher);
```

## Api

### Mapping a new directory:

```php
// A top level directory called foo is created with a subdirectory called bar.
$cloudpaths->map('foo', ['bar']);
```

### Mapping an array of directories:

```php
// A top level directory called foo is created with a subdirectory called bar.
// Another top level directory called baz is created witout subdirectories.
// The function recurses on the array, so don't worry about deep level arrays.
$cloudpaths->mapArray([
    'foo' => [
        'bar'
    ],
    'baz'
]);
```

### Find a directory path by dot notation input. 

You do not have to instruct the full path to the directory, just the top level directory where the directory is located and the target directory, we will find it :).

```php
// The repository is configured with the root name as 'root'. The directories struture is the following.   
// root
//   |--foo
//       |--bar
//           |--baz

// A Illuminate\Support\Collection is returned with all mathed paths. We want the first one.
$found = $cloudpaths->find('foo.baz')->first();

'root/foo/bar/baz'
```

### Find a directory replacing a dynamic directory name

To replace, you provide an array with a key/value, the key represent the key to find and the value is the value to replace.

```php
// The repository is configured with the root name as 'root'. The directories struture is the following.   
// root
//   |--foo
//       |--:id
//           |--baz
// A Illuminate\Support\Collection is returned with all mathed paths. We want the first one.
// We want to change the id to a dynamic id value.
$found = $cloudpaths->find('foo.baz', [':id' => 1])->first();

'root/foo/1/baz'
```

# License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.
