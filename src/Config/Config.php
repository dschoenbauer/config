<?php
namespace DSchoenbauer\Config;

use DSchoenbauer\DotNotation\ArrayDotNotation;

/**
 * Loads a directory filled with JSON files
 *
 * @author David Schoenbauer
 */
class Config
{

    protected $arrayDot;
    protected $path;

    public function __construct($path = null)
    {
        if ($path) {
            $this->load($path);
        }
    }

    public function get($dotNotation, $defaultValue = null)
    {
        return $this->getArrayDot()->get($dotNotation, $defaultValue);
    }

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

    public function importData(array $files = [])
    {
        $data = $this->getArrayDot()->getData();
        foreach ($files as $file) {
            $data = array_replace_recursive($data, \json_decode(file_get_contents($file) ?: [], true));
        }
        $this->getArrayDot()->setData($data);
        return $this;
    }

    public function load($path)
    {
        $this->importData($this->getFiles($path));
        return $this;
    }

    /**
     * @return ArrayDotNotation
     */
    public function getArrayDot()
    {
        if (!$this->arrayDot) {
            $this->setArrayDot(new ArrayDotNotation([]));
        }
        return $this->arrayDot;
    }

    public function setArrayDot(ArrayDotNotation $arrayDot)
    {
        $this->arrayDot = $arrayDot;
        return $this;
    }
    
    public function filterPath($path)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, trim($path, '/\\') . DIRECTORY_SEPARATOR);
    }
}
