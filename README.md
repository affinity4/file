# File

[![Build Status](https://travis-ci.org/affinity4/File.svg?branch=master)](https://travis-ci.org/affinity4/File)

The File class recursively searches parent directories from a specified directory for files using a regex pattern or plain filename.

## Installation
Affinity4/File is available via composer:

`composer require affinity4/file`

or

```
{
    "require": {
        "affinity4/file": "^2.0"
    }
}
```

## Usage
Assuming folder structure is:

```
root
  |-- files
  |    |-- test.php
  |    |-- 01
  |    |    |-- test01-01.php
  |    |    |-- test01-02.php
  |    |    |-- test01-03.php
  |    |    |-- 02
  |    |    |    |-- YOU-ARE-HERE
```

``` 
$file = new Affinity4\File\File;
$results = $file->find('/^test[\d]{2}-[\d]{2}.php$/')->in(__DIR__)->upOne()->get();

$results[0]->getPathname(); // root/files/01/test01-01.php
$results[1]->getPathname(); // root/files/01/test01-02.php
$results[2]->getPathname(); // root/files/01/test01-03.php
``` 

To find numerous files you can use a regex pattern with the following delimiters /, @, #, ~ in the `find()` method:

```
$file = new Affinity4\File\File;
$result = $file->find('test.php')->in(__DIR__)->up()->get();

$result->getPathname(); // root/files/test.php
```

You can also specify if you only want the one item returned using the `get()` method. This will return the first SplFileInfo object in the array, and not an array with one SplFileInfo object.
 
```
$file = new Affinity4\File\File;

$result = $file->find('/^test[\d]{2}-[\d]{2}.php$/')->in(__DIR__)->up()->get(1);

return $result->getPathname() // root/files/01/test01-01.php;
```

You can also specify exactly how many items you want returned in an array using the `get()` method.

```
$file = new Affinity4\File\File;
$results = $file->find('/^test[\d]{2}-[\d]{2}.php$/')->in(__DIR__)->up()->get(2);

$results[0]->getPathname(); // root/files/01/test01-01.php; 
$results[1]->getPathname(); // root/files/01/test01-02.php;
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