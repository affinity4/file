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
        "affinity4/file": "^1.0"
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
  |    |    |-- 02
  |    |    |    |-- test02-01.php
  |    |    |    |-- YOU-ARE-HERE
```

``` 
$file = new Affinity4\File\File;
$result = $file->find('test.php')->one()->inParentsOf(__DIR__);

return $result->getPathname(); // root/files/test.php
``` 

To find numerous files you can use a regex pattern with the following delimiters /, @, #, ~ in the `find()` method:

```
$file = new Affinity4\File\File;
$results = $file->find('/^test[\d]{2}-[\d]{2}.php$/')->inParentsOf(__DIR__);

var_dump($results); 
```

Would Return:

```
[
  0 => object(SplFileInfo)#5 (2) {
    ["pathName":"SplFileInfo":private] => string(28) "root/files/01/test01-01.php",
    ["fileName":"SplFileInfo":private] => string(13) "test01-01.php"
  ],
  1 => object(SplFileInfo)#6 (2) [
    ["pathName":"SplFileInfo":private] => string(28) "root/files/01/test01-02.php",
    ["fileName":"SplFileInfo":private] => string(13) "test01-02.php"
  ],
  2 => object(SplFileInfo)#7 (2) [
    ["pathName":"SplFileInfo":private] => string(28) "root/files/01/test01-03.php",
    ["fileName":"SplFileInfo":private] => string(13) "test01-03.php"
  ]
]
```

You can also specify the if you only want the one item returned using the `one()` method. This will return the first SplFileInfo object in the array, and not an array with one SplFileInfo object.
 
```
$file = new Affinity4\File\File;

$result = $file->find('/^test[\d]{2}-[\d]{2}.php$/')->one()->inParentsOf(__DIR__);

return $result->getPathname() // root/files/01/test01-01.php;
```

You can also specify exactly how many items you want returned in an array using the `amount()` method.

```
$file = new Affinity4\File\File;
$results = $file->find('/^test[\d]{2}-[\d]{2}.php$/')->amount(2)->inParentsOf(__DIR__);

var_dump($results); 
```

Would Return:

```
[
  0 => object(SplFileInfo)#5 (2) {
    ["pathName":"SplFileInfo":private] => string(28) "root/files/01/test01-01.php",
    ["fileName":"SplFileInfo":private] => string(13) "test01-01.php"
  ],
  1 => object(SplFileInfo)#6 (2) [
    ["pathName":"SplFileInfo":private] => string(28) "root/files/01/test01-02.php",
    ["fileName":"SplFileInfo":private] => string(13) "test01-02.php"
  ]
]
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