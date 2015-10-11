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

    public function testWrite()
    {
        //$this->source->shouldReceive('update')->once()->andReturn(true);
        /** @var Mockery\MockInterface $mainAdapter */
        $mainAdapter = $this->mainAdapter;
        /** @var Mockery\MockInterface $fallbackAdapter */
        $fallbackAdapter = $this->fallbackAdapter;

        $mainAdapter->shouldReceive('write')->once()->andReturn(true);
        $mainAdapter->shouldReceive('has')->andReturn(true);

        $fallbackAdapter->shouldNotReceive('write');

        $this->assertTrue($this->adapter->write('/path', 'Hello World', new Config()));
    }
}
