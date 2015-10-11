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

    public function testWrite()
    {

        $this->mainAdapter->shouldReceive('write')->once()->andReturn(true);
        $this->mainAdapter->shouldReceive('has')->andReturn(true);

        $this->fallbackAdapter->shouldNotReceive('write');

        // We're testing that the return value is the same that returns mainAdapter, not that's true.
        $this->assertTrue($this->adapter->write('/path', 'Hello World', new Config()));
    }

    public function testReadFromMainFilesystem()
    {
        $this->mainAdapter->shouldReceive('has')->atLeast(1)->andReturn(true);
        $this->mainAdapter->shouldReceive('read')->atLeast(1)->andReturn(true);

        $this->fallbackAdapter->shouldNotReceive('read');

        $this->assertTrue($this->adapter->read('/path'));
    }

    public function testReadFromFallbackFilesystem()
    {
        $this->mainAdapter->shouldReceive('has')->atLeast(1)->andReturn(false);
        $this->mainAdapter->shouldNotReceive('read');

        $this->fallbackAdapter->shouldReceive('read')->atLeast(1)->andReturn(true);

        $this->assertTrue($this->adapter->read('/path'));
    }
}
