<?php
/**
 * This file is part of Affinity4\File.
 *
 * (c) 2017 Luke Watts <luke@affinity4.ie>
 *
 * This software is licensed under the MIT license. For the
 * full copyright and license information, please view the
 * LICENSE file that was distributed with this source code.
 */
namespace Affinity4\File;

/**
 * File Class
 *
 * @author Luke Watts <luke@affinity4.ie>
 *
 * @since  1.0.0
 *
 * @package Affinity4\File
 */
class File
{
    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  1.0.0
     *
     * @var \DirectoryIterator
     */
    private $iterator;

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  1.0.0
     *
     * @var
     */
    private $pattern;

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  1.0.0
     *
     * @var
     */
    private $limit = -1;

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since 2.0.0
     *
     * @var
     */
    private $dir;

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  1.0.0
     *
     * @var array
     */
    private $regex_delimiters = ['/', '#', '@', '~'];

    /**
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  1.0.0
     *
     * @var array
     */
    private $file_list = [];

    /**
     * Set the pattern to search for.
     *
     * Can be a regex pattern with the delimiters /, #, @ or ~
     *
     * Can also be a plain file name to search for only that file
     *
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  1.0.0
     *
     * @param $pattern
     *
     * @return File
     */
    public function find($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Alias of the upOne() method
     *
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  2.0.0
     *
     * @return File
     */
    public function parent()
    {
        $pattern = $this->getPattern();
        $dir = $this->getDir();

        if ($this->find($pattern)->in($dir)->has() === false) {
            $dir = dirname($dir);
            $this->file_list = $this->find($pattern)->in($dir)->get();
        } else {
            $this->file_list = $this->find($pattern)->in($dir)->get();
        }

        return $this;
    }

    /**
     * Alias of the up() method
     *
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  2.0.0
     *
     * @return File
     */
    public function parents()
    {
        $dir = $this->getDir();
        $pattern = $this->getPattern();

        if ($this->find($pattern)->in($dir)->has() === false) {
            $dir = dirname($dir);

            $this->file_list = $this->find($pattern)->in($dir)->up()->get();
        } else {
            $this->file_list = $this->find($pattern)->in($dir)->get();
        }

        return $this;
    }

    /**
     * Sets the directory to start search in.
     *
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  2.0.0
     *
     * @param $dir
     *
     * @return File
     */
    public function in($dir)
    {
        $this->dir = $dir;

        $this->make();

        return $this;
    }

    /**
     * Search the parent directory.
     *
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @return File
     */
    public function upOne()
    {
        $dir = $this->getDir();
        $pattern = $this->getPattern();

        if ($this->find($pattern)->in($dir)->has() === false) {
            $dir = dirname($dir);
            $this->file_list = $this->find($pattern)->in($dir)->get();
        } else {
            $this->file_list = $this->find($pattern)->in($dir)->get();
        }

        return $this;
    }

    /**
     * Recursively searches parent directories.
     *
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @return File
     */
    public function up()
    {
        $dir = $this->getDir();
        $pattern = $this->getPattern();

        if ($this->find($pattern)->in($dir)->has() === false) {
            $dir = dirname($dir);

            $this->file_list = $this->find($pattern)->in($dir)->up()->get();
        } else {
            $this->file_list = $this->find($pattern)->in($dir)->get();
        }

        return $this;
    }

    /**
     * Return specified amount of files
     *
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @param int $limit
     *
     * @return array|bool|mixed
     */
    public function get($limit = -1)
    {
        if ($limit === 0 || $limit < -1) {
            throw new \InvalidArgumentException(sprintf('An integer of %s cannot be passed as a limit to the `get` method. Only -1, 1 or more can be given.', $limit));
        }
        $this->limit = $limit;

        if (isset($this->getFileList()[0])) {
            if ($this->limit === -1) {
                return $this->getFileList();
            } elseif ($this->limit === 1) {
                return $this->getFileList()[0];
            } else {
                return array_slice($this->getFileList(), 0, $this->limit);
            }
        } else {
            return false;
        }
    }

    /**
     * Checks existence of file.
     *
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @return bool
     */
    public function has()
    {
        return isset($this->getFileList()[0]);
    }

    /**
     * Check is the Regex a valid pattern
     *
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  2.1.3
     *
     * @param $pattern
     *
     * @return bool
     */
    public function isValidPattern($pattern)
    {
        // Check if first character is one of the self::$regex_delimiters
        foreach ($this->regex_delimiters as $delimiter) {
            $pos = (strpos($pattern, $delimiter) === 0) ? $delimiter : false;

            // If first character is one of the $common_regex_delimiters
            if ($pos !== false) {
                // Then check if the last character is the same
                $index = strlen($pattern) - 1;

                $pos_last = (strrpos($pattern, $pos, $index) === $index) ? $pos : false;

                return ($pos_last !== false) ? true : false;
            }
        }

        return false;
    }

    /**
     * Set the file list using by matching pattern
     *
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  2.1.4
     *
     * @param \DirectoryIterator $iterator
     * @param                    $pattern
     */
    public function setFileListUsingPattern(\DirectoryIterator $iterator, $pattern)
    {
        $this->iterator = $iterator;

        // Reset the array to avoid duplicate entry issue in version 1.0.0 in recursive methods
        $this->file_list = [];

        // If first and last are the same treat expression as a regex
        foreach ($this->iterator as $item) {
            if ($item->isDot() || $item->isDir()) {
                continue;
            }

            if (preg_match($pattern, $item->getFilename()) === 1) {
                $this->file_list[] = new \SplFileInfo($item->getPathname());
            }
        }


    }

    /**
     * Make the search
     *
     * @author Luke Watts <luke@affinity4.ie>
     */
    public function make()
    {
        $this->iterator = new \DirectoryIterator($this->getDir());

        if ($this->isValidPattern($this->pattern)) {
            // If first and last are the same treat expression as a regex
            $this->setFileListUsingPattern(new \DirectoryIterator($this->getDir()), $this->pattern);
        } else {
            // Else use plain file name
            $this->setFileListUsingPattern(new \DirectoryIterator($this->getDir()), '/^' . $this->pattern . '$/');
        }
    }

    /**
     * Returns the current pattern to search for
     *
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since 1.0.0
     *
     * @return mixed
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Returns the current limit
     *
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  1.0.0
     *
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Get the current directory.
     *
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  2.0.0
     *
     * @return mixed
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Returns file list array
     *
     * @author Luke Watts <luke@affinity4.ie>
     *
     * @since  1.0.0
     *
     * @return array
     */
    public function getFileList()
    {
        return $this->file_list;
    }
}
