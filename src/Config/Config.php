<?php
namespace DSchoenbauer\Config;

use DSchoenbauer\DotNotation\ArrayDotNotation;

/**
 * Loads a directory filled with JSON files allowing quick access to data
 *
 * @author David Schoenbauer
 */
class Config
{

    protected $arrayDot;
    protected $path;

    /**
     *
     * @param string $path absolute or relative directory path to a folder containing JSON files
     */
    public function __construct($path = null)
    {
        if ($path) {
            $this->load($path);
        }
    }

    /**
     * retrieves a value from the amalgamation of all the JSON files data
     * @param string $dotNotation a concatenated string of array keys
     * @param mixed $defaultValue value to be returned if the dot notation does not find data
     * @return mixed
     */
    public function get($dotNotation, $defaultValue = null)
    {
        return $this->getArrayDot()->get($dotNotation, $defaultValue);
    }

    /**
     * retrieves an array of JSON files found in a directory
     * @param string $path absolute or relative directory path to a folder containing JSON files
     * @return array retrieves an array of JSON files found in a directory
     */
    public function getFiles($path)
    {
        $scannedFiles = glob($this->filterPath($path) . "*", GLOB_MARK) ?: [];
        foreach ($scannedFiles as $file) {
            if (is_dir($file)) {
                $scannedFiles = array_merge($scannedFiles, $this->getFiles($file));
            }
        }
        return array_values(array_filter($scannedFiles, function ($file) {
            return substr(strtolower($file), -4) === 'json';
        }));
    }

    /**
     * loads data into the object from a list of JSON files. If run multiple times the data will be continually added to
     * @param array $files list of files to be loaded
     * @return $this
     */
    public function importData(array $files = [])
    {
        $data = $this->getArrayDot()->getData();
        foreach ($files as $file) {
            $data = array_replace_recursive($data, \json_decode(file_get_contents($file) ?: [], true));
        }
        $this->getArrayDot()->setData($data);
        return $this;
    }

    /**
     * loads JSON files from a directory path
     * @param string $path  absolute or relative directory path to a folder containing JSON files
     * @return $this
     */
    public function load($path)
    {
        $this->importData($this->getFiles($path));
        return $this;
    }

    /**
     * Array dot notation allows for quick and easy access to a complicated data structure
     * @return ArrayDotNotation
     */
    public function getArrayDot()
    {
        if (!$this->arrayDot) {
            $this->setArrayDot(new ArrayDotNotation([]));
        }
        return $this->arrayDot;
    }

    /**
     * Array dot notation allows for quick and easy access to a complicated data structure
     * @param ArrayDotNotation $arrayDot
     * @return $this
     */
    public function setArrayDot(ArrayDotNotation $arrayDot)
    {
        $this->arrayDot = $arrayDot;
        return $this;
    }
    
    /**
     * Cleans a string so that it is truly a path relevant to the class.
     * @param string $path  absolute or relative directory path to a folder containing JSON files
     * @return string
     */
    public function filterPath($path)
    {
        return str_replace(['/','\\'], DIRECTORY_SEPARATOR, trim($path, '\\/') . DIRECTORY_SEPARATOR);
    }
}
