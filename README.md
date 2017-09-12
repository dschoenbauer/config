# Config

A common library to load a directory full of JSON files into a massive array that
can be accessed.

[![Build Status](https://travis-ci.org/dschoenbauer/config.svg?branch=develop)](https://travis-ci.org/dschoenbauer/config) [![Coverage Status](https://coveralls.io/repos/github/dschoenbauer/config/badge.svg?branch=develop)](https://coveralls.io/github/dschoenbauer/config?branch=develop) [![License](https://img.shields.io/packagist/l/dschoenbauer/config.svg)](https://github.com/dschoenbauer/config) [![Downloads](https://img.shields.io/packagist/dt/dschoenbauer/config.svg)](https://packagist.org/packages/dschoenbauer/config) [![Version](https://img.shields.io/packagist/v/dschoenbauer/config.svg)](https://github.com/dschoenbauer/config/releases)


### Methods summary
|Method | Description|
| ----- | ----- |
|public **__construct( string $path = null )**|
|public **mixed get( string $dotNotation, mixed $defaultValue = null )**|retrieves a value from the amalgamation of all the JSON files data|
|public **array getFiles( string $path )**|retrieves an array of JSON files found in a directory|
|public	**importData( array $files = [] )**|loads data into the object from a list of JSON files. If run multiple times the data will be continually added to|
|public	**load( string $path )**|loads JSON files from a directory path|
|public **DSchoenbauer\DotNotation\ArrayDotNotation	#getArrayDot( )**|Array dot notation allows for quick and easy access to a complicated data structure|
|public	**setArrayDot( DSchoenbauer\DotNotation\ArrayDotNotation $arrayDot )**|Array dot notation allows for quick and easy access to a complicated data structure|
|public string	**filterPath( string $path )**|Cleans a string so that it is truly a path relevant to the class.|

