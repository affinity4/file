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
     * Sets the limit to -1 so searches will return
     * a complete array of all results
     *
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     *
     * @return $this
     */
    public function all()
    {
        $this->limit = -1;
        
        return $this;
    }
    
    /**
     * Return the first item as a single SplFileInfo object.
     *
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     *
     * @return $this
     */
    public function one()
    {
        $this->limit = 1;
        
        return $this;
    }
    
    /**
     * Set the limit returned to a certain amount
     *
     * Setting to 1 will return a single object instead
     * of an array of objects.
     *
     * Setting to -1 returns an array of all results
     *
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     *
     * @param $num
     *
     * @return $this
     */
    public function amount($num)
    {
        $this->limit = $num;
        
        return $this;
    }
    
    /**
     * Search the parent directory of specified directory.
     *
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     *
     * @param $dir
     *
     * @return array|bool|mixed
     */
    public function inParentOf($dir)
    {
        $dir = dirname($dir);
        $this->iterator = new \DirectoryIterator($dir);
        
        // Check if first character is one of the self::$regex_delimiters
        foreach ($this->regex_delimiters as $delimiter) {
            $pos = (strpos($this->pattern, $delimiter) === 0) ? $delimiter : false;
            if ($pos !== false) break;
        }
        
        // If first character is one of the $common_regex_delimiters
        if ($pos !== false) {
            // Then chek if the last character is the same
            $index = strlen($this->pattern) - 1;
    
            $pos_last = (strrpos($this->pattern, $pos, $index) === $index) ? $pos : false;
    
            $first_last_match = ($pos_last !== false) ? true : false;
        }
    
        if (isset($first_last_match) && $first_last_match !== false) {
            // If first and last are the same treat expression as a regex
            foreach ($this->iterator as $item) {
                $filename = $item->getFilename();
            
                if ($item->isDot()) continue;
                if ($item->isDir()) continue;
            
                if (preg_match($this->pattern, $filename) === 1) $this->file_list[] = new \SplFileInfo($item->getPathname());
            }
        } else {
            // Else use $file as is
            foreach ($this->iterator as $item) {
                $filename = $item->getFilename();
            
                if ($item->isDot()) continue;
                if ($item->isDir()) continue;
            
                if (preg_match('/^' . preg_quote($this->pattern) . '$/', $filename) === 1) $this->file_list[] = new \SplFileInfo($item->getPathname());
            }
        }
    
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
     * Recursively search the parent directories of specified directory.
     *
     * @author Luke Watts <luke@affinity4.ie>
     * @since  1.0.0
     *
     * @param $dir
     *
     * @return array|bool|mixed
     */
    public function inParentsOf($dir)
    {
        $pattern = $this->getPattern();
        $limit = $this->getLimit();
        if ($this->find($pattern)->amount($limit)->inParentOf($dir) === false) {
            $dir = dirname($dir);
            
            return $this->find($pattern)->amount($limit)->inParentsOf($dir);
        } else {
            return $this->find($pattern)->amount($limit)->inParentOf($dir);
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
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }
    
    /**
     * Returns file list array
     *
     * @return array
     */
    public function getFileList()
    {
        return $this->file_list;
    }
}