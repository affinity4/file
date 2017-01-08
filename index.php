<?php
/**
 * This file is part of File.
 *
 * (c) 2016 Luke Watts <luke@luke-watts.com>
 *
 * This software is licensed under the MIT license. For the
 * full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */
require_once __DIR__ . '/vendor/autoload.php';

$file = new \Affinity4\File\File();

echo "<pre>", var_dump($file->find('test.txt')->inParentsOf('tests/files/01/02')), "</pre>";