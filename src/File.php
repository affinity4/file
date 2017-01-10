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
 * @since  1.0.0
 *
 * @package Affinity4\File
 */
class File
{
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     *
     * @var \DirectoryIterator
     */
    private $iterator;
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     *
     * @var
     */
    private $pattern;
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     *
     * @var
     */
    private $limit = -1;
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since 2.0.0
     *
     * @var
     */
    private $dir;
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     *
     * @var array
     */
    private $regex_delimiters = ['/', '#', '@', '~'];
    
    /**
     * @author Luke Watts <luke@affinity4.ie>
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
     * @since  1.0.0
     *
     * @param $pattern
     *
     * @return $this
     */
    public function find($pattern)
    {
        $this->pattern = $pattern;
        
        return $this;
    }
    
    /**
     * Sets the directory to start search in.
     *
     * @author Luke Watts <luke@affinity4.ie>
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
     * @return array|bool|mixed
     */
    public function upOne()
    {
        $dir     = $this->getDir();
        $pattern = $this->getPattern();
        $limit   = $this->getLimit();
    
        if ($this->find($pattern)->in($dir)->has() === false) {
            $dir = dirname($dir);
            $this->file_list = $this->find($pattern)->in($dir)->get($limit);
        } else {
            $this->file_list = $this->find($pattern)->in($dir)->get($limit);
        }
        
        return $this;
    }
    
    /**
     * @return File
     */
    public function up()
    {
        $dir = $this->getDir();
        $pattern = $this->getPattern();
        $limit   = $this->getLimit();
    
        if ($this->find($pattern)->in($dir)->has() === false) {
            $dir = dirname($dir);
            
            $this->file_list = $this->find($pattern)->in($dir)->up()->get($limit);
        } else {
            $this->file_list = $this->find($pattern)->in($dir)->get($limit);
        }
        
        return $this;
    }
    
    /**
     * Return specified amount of files
     *
     * @param int $limit
     *
     * @return array|bool|mixed
     */
    public function get($limit = -1)
    {
        if ($limit === 0 || $limit < -1) throw new \InvalidArgumentException(sprintf("An integer of %s cannot be passed as a limit to the `get` method. Only -1, 1 or more can be given.", $limit));
        $this->limit = $limit;
        
        if (isset($this->getFileList()[0])) {
            if ($this->limit === -1) {
                return $this->getFileList();
            } else if ($this->limit === 1) {
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
     * @return bool
     */
    public function has()
    {
         return isset($this->getFileList()[0]);
    }
    
    public function make()
    {
        $this->iterator = new \DirectoryIterator($this->getDir());
    
        // Check if first character is one of the self::$regex_delimiters
        foreach ($this->regex_delimiters as $delimiter) {
            $pos = (strpos($this->pattern, $delimiter) === 0) ? $delimiter : false;
            if ($pos !== false) break;
        }
    
        // If first character is one of the $common_regex_delimiters
        if ($pos !== false) {
            // Then check if the last character is the same
            $index = strlen($this->pattern) - 1;
        
            $pos_last = (strrpos($this->pattern, $pos, $index) === $index) ? $pos : false;
        
            $first_last_match = ($pos_last !== false) ? true : false;
        }
    
        if (isset($first_last_match) && $first_last_match !== false) {
            // Reset the array to avoid duplicate entry issue in version 1.0.0 in recursive methods
            $this->file_list = [];
            
            // If first and last are the same treat expression as a regex
            foreach ($this->iterator as $item) {
                $filename = $item->getFilename();
            
                if ($item->isDot()) continue;
                if ($item->isDir()) continue;
            
                if (preg_match($this->pattern, $filename) === 1) $this->file_list[] = new \SplFileInfo($item->getPathname());
            }
        } else {
            // Reset the array to avoid duplicate entry issue in version 1.0.0 in recursive methods
            $this->file_list = [];
            
            // Else use plain file name
            foreach ($this->iterator as $item) {
                $filename = $item->getFilename();
            
                if ($item->isDot()) continue;
                if ($item->isDir()) continue;
            
                if (preg_match('/^' . preg_quote($this->pattern) . '$/', $filename) === 1) $this->file_list[] = new \SplFileInfo($item->getPathname());
            }
        }
    }
    
    /**
     * Returns the current pattern to search for
     *
     * @author Luke Watts <luke@affinity4.ie>
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
     * @since  1.0.0
     *
     * @return array
     */
    public function getFileList()
    {
        return $this->file_list;
    }
}