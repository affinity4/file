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
     *
     * @depends testFilesExist
     */
    public function testFind()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        
        $this->assertEquals($pattern, $this->file->find($pattern)->getPattern());
        $this->assertInstanceOf('Affinity4\File\File', $this->file->find($pattern));
    }
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  2.0.0
     *
     * @depends testFind
     */
    public function testIn()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        $dir     = 'tests/files/01/02';
        
        $this->assertEquals($dir, $this->file->find($pattern)->in($dir)->getDir());
        $this->assertInstanceOf('Affinity4\File\File', $this->file->find($pattern)->in($dir));
    }
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  2.0.0
     *
     * @depends testIn
     */
    public function testGet()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        $dir     = 'tests/files/01/02';
        
        $this->assertInternalType('array', $this->file->find($pattern)->in($dir)->get());
        $this->assertContainsOnlyInstancesOf('SplFileInfo', $this->file->find($pattern)->in($dir)->get());
        
        // Test amounts returned when specified limits are given
        $this->assertCount(3, $this->file->find($pattern)->in($dir)->get());
        $this->assertCount(3, $this->file->find($pattern)->in($dir)->get(3));
        $this->assertCount(2, $this->file->find($pattern)->in($dir)->get(2));
        
        // Because 1 returns an object test for that instead
        $this->assertInstanceOf('SplFileInfo', $this->file->find($pattern)->in($dir)->get(1));
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp  /^An integer of -?[\d]+ cannot be passed as a limit to the `get` method. Only -1, 1 or more can be given.$/
     */
    public function testThrowsInvalidArgumentExceptionOnLessThanMinusOne()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        $dir     = 'tests/files/01/02';
        
        $this->file->find($pattern)->in($dir)->get(-2);
    }
    
    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp  /^An integer of -?[\d]+ cannot be passed as a limit to the `get` method. Only -1, 1 or more can be given.$/
     */
    public function testThrowsInvalidArgumentExceptionOnZero()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        $dir     = 'tests/files/01/02';
        
        $this->file->find($pattern)->in($dir)->get(0);
    }
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  2.0.0
     *
     * @depends testGet
     */
    public function testUpOne()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        $dir     = 'tests/files/01/02';
        
        $this->assertContainsOnlyInstancesOf('SplFileInfo', $this->file->find($pattern)->in($dir)->upOne()->get());
    }
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  2.0.0
     *
     * @depends testGet
     * @depends testUpOne
     */
    public function testUp()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        $dir     = 'tests/files/01/02';
        
        $this->assertInternalType('array', $this->file->find($pattern)->in($dir)->up()->get());
        $this->assertInstanceOf('Affinity4\File\File', $this->file->find($pattern)->in($dir)->up());
        $this->assertContainsOnlyInstancesOf('SplFileInfo', $this->file->find($pattern)->in($dir)->up()->get());
    }
}
