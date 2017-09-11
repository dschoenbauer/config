<?php
namespace DSchoenbauer\Config;

use DSchoenbauer\Config\Config;
use DSchoenbauer\DotNotation\ArrayDotNotation;
use PHPUnit\Framework\TestCase;

/**
 * Description of ConfigTest
 *
 * @author David Schoenbauer
 */
class ConfigTest extends TestCase
{
    /* @var $object Config */

    private $object;

    const PATH = "ConfigFiles";

    protected function setUp()
    {
        $this->object = new Config();
    }

    public function testArrayDotNotationLazyLoad()
    {
        $this->assertInstanceOf(ArrayDotNotation::class, $this->object->getArrayDot());
    }

    public function testArrayDotNotation()
    {
        $adn = $this->getMockBuilder(ArrayDotNotation::class)->getMock();
        $this->assertSame($adn, $this->object->setArrayDot($adn)->getArrayDot());
    }

    /**
     * @dataProvider filterPathDataProvider
     * @param type $result
     * @param type $path
     */
    public function testFitlerPath($path, $result)
    {
        chdir(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . self::PATH);
        $this->assertEquals($result, $this->object->filterPath($path));
    }

    public function filterPathDataProvider()
    {
        return [
            'trailing-win' => ['test\\', 'test' . DIRECTORY_SEPARATOR],
            'trailing-lin' => ['test/', 'test' . DIRECTORY_SEPARATOR],
            'trailing-none' => ['test', 'test' . DIRECTORY_SEPARATOR],
            'mid-win' => ["test\one", 'test' . DIRECTORY_SEPARATOR . 'one' . DIRECTORY_SEPARATOR],
            'mid-lin' => ['test/one', 'test' . DIRECTORY_SEPARATOR . 'one' . DIRECTORY_SEPARATOR],
        ];
    }

    public function testConstructorCallsLoad()
    {
        $path = 'test';
        $config = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $config->expects($this->once())->method('load')->with($path);
        $config->__construct($path); //load will be called here ...once
        $config->__construct(); //with no path load will not be called
        $config->__construct(null); //with null path load will not be called
    }

    public function testGet()
    {
        $arrayDot = $this->getMockBuilder(ArrayDotNotation::class)->getMock();
        $arrayDot->expects($this->any())->method('get')->with('test.test', null)->willReturn('value');
        $this->assertEquals('value', $this->object->setArrayDot($arrayDot)->get('test.test', null));
    }

    public function testLoad()
    {
        $path = 'test';
        $config = $this->getMockBuilder(Config::class)
                ->setMethods(['getFiles', 'importData'])->getMock();
        $config->expects($this->once())->method('getFiles')->with($path)->willReturn([]);
        $config->expects($this->once())->method('importData')->with([]);
        $this->assertSame($config, $config->load($path));
    }

    /**
     * @dataProvider getFilesDataProvider
     */
    public function testGetFiles($path, $result)
    {
        chdir(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . self::PATH);
        $this->assertEquals($result, $this->object->getFiles(DIRECTORY_SEPARATOR . $path));
    }

    public function getFilesDataProvider()
    {
        return [
            'simple 1 file' => ['TestA', ['TestA' . DIRECTORY_SEPARATOR . 'local.json']],
            'simple 2 files' => ['TestB', [
                    'TestB' . DIRECTORY_SEPARATOR . 'local.json',
                    'TestB' . DIRECTORY_SEPARATOR . 'remote.json'
                ]
            ],
            'sub directories' => ['TestC', [
                    'TestC' . DIRECTORY_SEPARATOR . 'a' . DIRECTORY_SEPARATOR . 'local.json',
                    'TestC' . DIRECTORY_SEPARATOR . 'b' . DIRECTORY_SEPARATOR . 'local.json'
                ]
            ],
        ];
    }

    /**
     * @dataProvider importDataDataProvider
     */
    public function testImportData($files, $result)
    {
        chdir(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . self::PATH);
        $this->assertEquals($result, $this->object->importData($files)->getArrayDot()->getData());
    }

    public function importDataDataProvider()
    {
        return [
            'single file' => [['TestA' . DIRECTORY_SEPARATOR . 'local.json'], ['local' => true]],
            'multiple file' => [['TestB' . DIRECTORY_SEPARATOR . 'local.json', 'TestB' . DIRECTORY_SEPARATOR . 'remote.json',], ['local' => true, 'remote' => true]],
            'competing files' => [['TestC' . DIRECTORY_SEPARATOR . 'a' . DIRECTORY_SEPARATOR . 'local.json', 'TestC' . DIRECTORY_SEPARATOR . 'b' . DIRECTORY_SEPARATOR . 'local.json',], ['local' => false]], //First file is true, second is false and wins with the value
            'complicated data schema' => [['TestD' . DIRECTORY_SEPARATOR . 'local.json', 'TestD' . DIRECTORY_SEPARATOR . 'remote.json',],
                [
                    'local' => true,
                    'database' => [
                        'host' => 'devbox',
                        'err-mode' => 1,
                        'charset' => 'utf8'
                    ],
                    'user' => ['name' => ['first' => 'John', 'last' => 'Doe'], 'active' => false],
                    'remote' => true,
                ]
            ],
        ];
    }
}
