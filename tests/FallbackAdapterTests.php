<?php


use League\Flysystem\Config;
use Litipk\Flysystem\Fallback\FallbackAdapter;


class FallbackAdapterTests extends \PHPUnit_Framework_TestCase
{
    /** @var  FallbackAdapter */
    protected $adapter;
    protected $mainAdapter;
    protected $fallbackAdapter;

    public function setup()
    {
        $this->mainAdapter = Mockery::mock('League\\Flysystem\\AdapterInterface');
        $this->fallbackAdapter = Mockery::mock('League\\Flysystem\\AdapterInterface');

        $this->adapter = new FallbackAdapter($this->mainAdapter, $this->fallbackAdapter);
    }

    public function testGetMainAdapter()
    {
        $this->assertEquals($this->mainAdapter, $this->adapter->getMainAdapter());
    }

    public function testGetFallbackAdapter()
    {
        $this->assertEquals($this->fallbackAdapter, $this->adapter->getFallbackAdapter());
    }
}
