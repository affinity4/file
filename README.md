# File

[![Build Status](https://travis-ci.org/affinity4/file.svg?branch=master)](https://travis-ci.org/affinity4/file)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/affinity4/file/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/affinity4/file/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/affinity4/file/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/affinity4/file/?branch=master)

https://insight.sensiolabs.com/projects/25819a9c-87e4-489d-9034-78b42e6d0e86/big.png

Find files using a regex pattern or exact filename.

## Features
 - Search the current directory for a file or files by exact filename or regex pattern
 - Search the current directory and then the parent directory for a file or files using a filename or regex pattern
 - Search the current directory and then recursively up through parent directories for a file or files using a filename or regex pattern.

## Installation
Affinity4/File is available via composer:

`composer require affinity4/file`

or

```
{
    "require": {
        "affinity4/file": "^2.1"
    }
}
```

## Usage
Assuming folder structure is:

```
root
  |-- files
  |    |-- config.yml
  |    |-- config.php
  |    |-- 00
  |    |    |-- test00-01.php
  |    |    |-- test00-01.yml
  |    |    |-- test00-02.php
  |    |    |-- test00-02.yml
  |    |    |-- 01
  |    |    |    |    |-- test01-01.php
  |    |    |    |    |-- test01-01.yml
  |    |    |    |    |-- test01-02.php
  |    |    |    |    |-- test01-02.yml
  |    |    |    |    |-- test01-03.php
  |    |    |    |    |-- test01-03.yml
  |    |    |    |    |-- 02
  |    |    |    |    |    |-- YOU-ARE-HERE
  |    |    |    |    |    |-- test02-01.html
  |    |    |    |    |    |-- test02-01.css
  |    |    |    |    |    |-- test02-01.json
  |    |    |    |    |    |-- test02-02.json
```

To find numerous files you can use a regex pattern with the following delimiters /, @, #, ~ in the `find()` method. Then chain the `in()` method to start the search from that directory and use `get()` to return the results:
``` 
$file = new Affinity4\File\File;
$results = $file->find('/^test[\d]{2}-[\d]{2}.json$/')->in(__DIR__)->get();

$results[0]->getPathname(); // root/files/00/01/02/test02-01.json
$results[1]->getPathname(); // root/files/00/01/02/test02-02.json
```

You can also search the current directory and if no files are found matching the pattern search one level up by chaining the `parent()` method after `in()`:

``` 
$file = new Affinity4\File\File;
$results = $file->find('/^test00-[\d]{2}.php$/')->in(__DIR__)->parent()->get();

$results[0]->getPathname(); // root/files/00/test00-01.php
$results[1]->getPathname(); // root/files/00/test00-02.php
``` 

You can also search the current directory and if no files are found matching the pattern search all parent directories by chaining the `parents()` method after `in()`:

```
$file = new Affinity4\File\File;
$result = $file->find('config.yml')->in(__DIR__)->parents()->get();

$result->getPathname(); // root/files/config.yml
```

You can also specify if you only want the one item returned using the `get()` method. This will return the first SplFileInfo object in the array, and not an array with one SplFileInfo object.
 
```
$file = new Affinity4\File\File;

$result = $file->find('/^test[\d]{2}-[\d]{2}.php$/')->in(__DIR__)->parents()->get(1);

return $result->getPathname() // root/files/00/01/test01-01.php;
```

You can also specify exactly how many items you want returned in an array using the `get()` method. As long as the number is greater than one the result will be an array of `SPLFileInfo` objects:

```
$file = new Affinity4\File\File;
$results = $file->find('/^test[\d]{2}-[\d]{2}.php$/')->in(__DIR__)->parents()->get(2);

$results[0]->getPathname(); // root/files/00/01/test01-01.php; 
$results[1]->getPathname(); // root/files/00/01/test01-02.php;
```

## Tests

Run tests:

```
vendor/bin/phpunit
```

## Licence
(c) 2017 Luke Watts (Affinity4.ie)

This software is licensed under the MIT license. For the
full copyright and license information, please view the
LICENSE file that was distributed with this source code.
