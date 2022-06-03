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
 *
 * @since  1.0.0
 *
 * @package Affinity4\File\Test
 */
class FileTest extends TestCase
{
    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  1.0.0
     *
     * @var
     */
    private $file;

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  1.0.0
     *
     * @depends testFilesExist
     */
    public function setUp(): void
    {
        $this->file = new File();
    }

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
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
     *
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
     *
     * @since  1.0.0
     *
     * @depends testFilesExist
     */
    public function testFind()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        $filename = 'test02-01.txt';


        $this->assertEquals($pattern, $this->file->find($pattern)->getPattern());
        $this->assertInstanceOf('Affinity4\File\File', $this->file->find($pattern));

        $this->assertEquals($filename, $this->file->find($filename)->getPattern());
        $this->assertInstanceOf('Affinity4\File\File', $this->file->find($filename));
    }

    /**
     * @author  Luke Watts <luke@affinity4.ie>
     *
     * @since   2.1.3
     *
     * @depends testFind
     */
    public function testIsValidPattern()
    {
        $invalid_pattern = ['^test[\w\d-]*.txt$/', '/^test[\w\d-]*.txt$', '^test[\w\d-]*.txt$'];

        $this->assertEquals($invalid_pattern[0], $this->file->find($invalid_pattern[0])->getPattern());
        $this->assertFalse($this->file->isValidPattern($invalid_pattern[0]));

        $this->assertEquals($invalid_pattern[1], $this->file->find($invalid_pattern[1])->getPattern());
        $this->assertFalse($this->file->isValidPattern($invalid_pattern[1]));

        $this->assertEquals($invalid_pattern[2], $this->file->find($invalid_pattern[2])->getPattern());
        $this->assertFalse($this->file->isValidPattern($invalid_pattern[2]));

        $valid_pattern = '/^test[\w\d-]*.txt$/';

        $this->assertEquals($valid_pattern, $this->file->find($valid_pattern)->getPattern());
        $this->assertTrue($this->file->isValidPattern($valid_pattern));
    }

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  2.0.0
     *
     * @depends testFind
     */
    public function testIn()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        $dir = 'tests/files/01/02';

        $this->assertEquals($dir, $this->file->find($pattern)->in($dir)->getDir());
        $this->assertInstanceOf('Affinity4\File\File', $this->file->find($pattern)->in($dir));
    }

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  2.0.0
     *
     * @depends testIn
     */
    public function testGet()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        $dir = 'tests/files/01/02';

        $this->assertIsArray($this->file->find($pattern)->in($dir)->get());
        $this->assertContainsOnlyInstancesOf('SplFileInfo', $this->file->find($pattern)->in($dir)->get());

        // Test amounts returned when specified limits are given
        $this->assertCount(3, $this->file->find($pattern)->in($dir)->get());
        $this->assertCount(3, $this->file->find($pattern)->in($dir)->get(3));
        $this->assertCount(2, $this->file->find($pattern)->in($dir)->get(2));

        // Because 1 returns an object test for that instead
        $this->assertInstanceOf('SplFileInfo', $this->file->find($pattern)->in($dir)->get(1));
    }

    public function testThrowsInvalidArgumentExceptionOnLessThanMinusOne()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^An integer of -?[\d]+ cannot be passed as a limit to the `get` method. Only -1, 1 or more can be given.$/');
        $pattern = '/^test[\w\d-]*.txt$/';
        $dir = 'tests/files/01/02';

        $this->file->find($pattern)->in($dir)->get(-2);
    }

    public function testThrowsInvalidArgumentExceptionOnZero()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/^An integer of -?[\d]+ cannot be passed as a limit to the `get` method. Only -1, 1 or more can be given.$/');
        $pattern = '/^test[\w\d-]*.txt$/';
        $dir = 'tests/files/01/02';

        $this->file->find($pattern)->in($dir)->get(0);
    }

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  2.0.0
     *
     * @depends testGet
     */
    public function testUpOne()
    {
        $existing_pattern = '/^test[\w\d-]*.txt$/';
        $non_matching_pattern = '/^test[\w\d-]*.php$/';
        $dir = 'tests/files/01/02';

        $this->assertContainsOnlyInstancesOf('SplFileInfo', $this->file->find($existing_pattern)->in($dir)->upOne()->get());
        $this->assertCount(3, $this->file->find($existing_pattern)->in($dir)->upOne()->get());

        $this->assertEmpty($this->file->find($non_matching_pattern)->in($dir)->upOne()->get());
    }

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  2.0.0
     *
     * @depends testGet
     * @depends testUpOne
     */
    public function testUp()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        $dir = 'tests/files/01/02';

        $this->assertIsArray($this->file->find($pattern)->in($dir)->up()->get());
        $this->assertInstanceOf('Affinity4\File\File', $this->file->find($pattern)->in($dir)->up());
        $this->assertContainsOnlyInstancesOf('SplFileInfo', $this->file->find($pattern)->in($dir)->up()->get());
        $this->assertCount(3, $this->file->find($pattern)->in($dir)->up()->get());
    }

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  2.0.0
     *
     * @depends testGet
     */
    public function testParent()
    {
        $pattern = '/^test[\w\d-]*.txt$/';
        $dir = 'tests/files/01/02';

        $this->assertIsArray($this->file->find($pattern)->in($dir)->parent()->get());
        $this->assertInstanceOf('Affinity4\File\File', $this->file->find($pattern)->in($dir)->parent());
        $this->assertContainsOnlyInstancesOf('SplFileInfo', $this->file->find($pattern)->in($dir)->parent()->get());
        $this->assertCount(3, $this->file->find($pattern)->in($dir)->parent()->get());
    }

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  2.0.0
     *
     * @depends testGet
     */
    public function testParents()
    {
        $pattern = '/^test01-[\w\d]{2}.txt$/';
        $dir = 'tests/files/01/02';

        $this->assertIsArray($this->file->find($pattern)->in($dir)->parents()->get());
        $this->assertInstanceOf('Affinity4\File\File', $this->file->find($pattern)->in($dir)->parents());
        $this->assertContainsOnlyInstancesOf('SplFileInfo', $this->file->find($pattern)->in($dir)->parents()->get());
        $this->assertCount(3, $this->file->find($pattern)->in($dir)->parents()->get());
    }

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  2.1.1
     *
     * @depends testGet
     */
    public function testInGet()
    {
        $pattern = '/^test02-[\w\d]{2}.txt$/';
        $dir = 'tests/files/01/02';

        $this->assertIsArray($this->file->find($pattern)->in($dir)->get());
        $this->assertInstanceOf('Affinity4\File\File', $this->file->find($pattern)->in($dir));
        $this->assertContainsOnlyInstancesOf('SplFileInfo', $this->file->find($pattern)->in($dir)->get());
        $this->assertCount(3, $this->file->find($pattern)->in($dir)->get());
    }

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  2.1.4
     *
     * @depends testGet
     */
    public function testSetFileListUsingPatternWithRegex()
    {
        $dir = 'tests/files/01/02';
        $regex_pattern = '/^test02-[\w\d]{2}.txt$/';

        $this->file->find($regex_pattern)->in($dir)->get();
        $this->file->setFileListUsingPattern(
            new \DirectoryIterator($this->file->getDir()),
            $regex_pattern
        );

        $this->assertContainsOnlyInstancesOf('SplFileInfo', $this->file->getFileList());
        $this->assertCount(3, $this->file->getFileList());

        $pathnames = [
            str_replace(DIRECTORY_SEPARATOR, '/', $this->file->getFileList()[0]->getPathName()),
            str_replace(DIRECTORY_SEPARATOR, '/', $this->file->getFileList()[1]->getPathName()),
            str_replace(DIRECTORY_SEPARATOR, '/', $this->file->getFileList()[2]->getPathName())
        ];

        $expected = [
            'tests/files/01/02/test02-01.txt',
            'tests/files/01/02/test02-02.txt',
            'tests/files/01/02/test02-03.txt',
        ];

        $this->assertSame([], array_diff($expected, $pathnames));
    }

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  2.1.4
     *
     * @depends testGet
     */
    public function testSetFileListUsingPatternWithFilename()
    {
        $dir = 'tests/files/01/02';
        $regex_pattern = '/^test02-[\w\d]{2}.txt$/';
        $filename = 'test02-01.txt';

        $this->file->find($regex_pattern)->in($dir)->get();

        $this->file->setFileListUsingPattern(
            new \DirectoryIterator($this->file->getDir()),
            '/^' . preg_quote($filename) . '$/'
        );

        $this->assertCount(1, $this->file->getFileList());
        $this->assertInstanceOf('SplFileInfo', $this->file->getFileList()[0]);

        $this->assertEquals(
            'tests/files/01/02/test02-01.txt',
            str_replace(DIRECTORY_SEPARATOR, '/', $this->file->getFileList()[0]->getPathName())
        );
    }
}
