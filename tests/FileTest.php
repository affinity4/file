<?php
/**
 * This file is part of Affinity4\File.
 *
 * (c) 2017 Luke Watts <luke@affinity4.iw>
 *
 * This software is licensed under the MIT license. For the
 * full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */
namespace Affinity4\File\Test;

use PHPUnit\Framework\TestCase;
use Affinity4\File\File;

/**
 * FileTest Class
 *
 * @author Luke Watts <luke@affinity4.ie>
 * @since  1.0.0
 *
 * @package Affinity4\File\Test
 */
class FileTest extends TestCase
{
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     *
     * @var
     */
    private $file;
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     *
     * @depends testFilesExist
     */
    public function setUp()
    {
        $this->file = new File();
    }
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     */
    public function testDirsExist()
    {
        $root = __DIR__ . 'tests';
        $this->assertDirectoryExists('tests/files', $root);
        $this->assertDirectoryExists('tests/files/01', $root);
        $this->assertDirectoryExists('tests/files/01/02', $root);
    }
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     *
     * @depends testDirsExist
     */
    public function testFilesExist()
    {
        $root = __DIR__ . 'tests';
        $this->assertFileExists('tests/files/test.txt', $root);
        $this->assertFileExists('tests/files/01/test01-01.txt', $root);
        $this->assertFileExists('tests/files/01/test01-02.txt', $root);
        $this->assertFileExists('tests/files/01/test01-03.txt', $root);
        $this->assertFileExists('tests/files/01/02/test02-01.txt', $root);
        $this->assertFileExists('tests/files/01/02/test02-02.txt', $root);
        $this->assertFileExists('tests/files/01/02/test02-03.txt', $root);
    }
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     */
    public function testfind()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        
        $this->assertEquals($pattern, $this->file->find($pattern)->getPattern());
        $this->assertInstanceOf('Affinity4\File\File', $this->file->find($pattern));
    }
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     */
    public function testAll()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        
        $this->assertEquals(-1, $this->file->find($pattern)->all()->getLimit());
        $this->assertInstanceOf('Affinity4\File\File', $this->file->find($pattern)->all());
    }
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     */
    public function testOne()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        
        $this->assertEquals(1, $this->file->find($pattern)->one()->getLimit());
        $this->assertInstanceOf('Affinity4\File\File', $this->file->find($pattern)->one());
    }
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     */
    public function testAmount()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        
        $this->assertEquals(2, $this->file->find($pattern)->amount(2)->getLimit());
        $this->assertInstanceOf('Affinity4\File\File', $this->file->find($pattern)->amount(2));
    }
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     */
    public function testInParentOf()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        $dir = 'tests/files/01/02';
    
        $this->assertInternalType('array', $this->file->find($pattern)->inParentOf($dir));
        $this->assertContainsOnlyInstancesOf('SplFileInfo', $this->file->find($pattern)->inParentOf($dir));
    }
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     */
    public function testInParentsOf()
    {
        $pattern = 'test.txt';
        $dir     = 'tests/files/01/02';
        
        $this->assertInternalType('array', $this->file->find($pattern)->inParentsOf($dir));
        $this->assertContainsOnlyInstancesOf('SplFileInfo', $this->file->find($pattern)->inParentsOf($dir));
    }
}
