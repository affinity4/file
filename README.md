# File
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
$iterator = new Affinity4/File('.');
$iterator->setFile('/^test[\d\w-]*.php$/');
$iterator->setLimit(2);


## Licence
