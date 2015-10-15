<?php


use League\Flysystem\Config;
use Litipk\Flysystem\Fallback\FallbackAdapter;


class FallbackAdapterTests extends \PHPUnit_Framework_TestCase
{
    /** @var  FallbackAdapter */
    protected $adapter;

    /** @var  Mockery\MockInterface */
    protected $mainAdapter;

    /** @var  Mockery\MockInterface */
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

    public function testHas_PathExistsInMain()
    {
        $this->mainAdapter->shouldReceive('has')->andReturn(true);
        $this->fallbackAdapter->shouldNotReceive('has');

        $this->assertTrue($this->adapter->has('/path'));
    }

    public function testHas_PathExistsInFallback()
    {
        $this->mainAdapter->shouldReceive('has')->andReturn(false);
        $this->fallbackAdapter->shouldReceive('has')->andReturn(true);

        $this->assertTrue($this->adapter->has('/path'));
    }

    public function testHas_PathDoesNotExist()
    {
        $this->mainAdapter->shouldReceive('has')->andReturn(false);
        $this->fallbackAdapter->shouldReceive('has')->andReturn(false);

        $this->assertFalse($this->adapter->has('/path'));
    }

    public function testWrite_PathExistsInMain()
    {
        $this->mainAdapter->shouldReceive('has')->andReturn(true);
        $this->mainAdapter->shouldReceive('write')->once()->andReturn(true);

        $this->fallbackAdapter->shouldNotReceive('write');

        // We're testing that the return value is the same that returns mainAdapter, not that's true.
        $this->assertTrue($this->adapter->write('/path', 'Hello World', new Config()));
    }

    public function testWrite_PathExistsInFallback()
    {
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(false);
        $this->fallbackAdapter->shouldReceive('has')->once()->andReturn(true);

        $this->fallbackAdapter->shouldNotReceive('write');
        $this->fallbackAdapter->shouldReceive('readStream')->once()->andReturn(['stream'=>null]);

        $this->mainAdapter->shouldReceive('writeStream')->once()->andReturn(true);
        $this->mainAdapter->shouldReceive('write')->once()->andReturn(true);

        $this->assertTrue($this->adapter->write('/path', 'Hello World', new Config()));
    }

    public function testRead_PathExistsInMain()
    {
        $this->mainAdapter->shouldReceive('has')->atLeast(1)->andReturn(true);
        $this->mainAdapter->shouldReceive('read')->atLeast(1)->andReturn(true);

        $this->fallbackAdapter->shouldNotReceive('read');

        $this->assertTrue($this->adapter->read('/path'));
    }

    public function testRead_PathExistsInFallback()
    {
        $this->mainAdapter->shouldReceive('has')->atLeast(1)->andReturn(false);
        $this->mainAdapter->shouldNotReceive('read');

        $this->fallbackAdapter->shouldReceive('read')->atLeast(1)->andReturn(true);

        $this->assertTrue($this->adapter->read('/path'));
    }
}
