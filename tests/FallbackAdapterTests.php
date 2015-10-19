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
        // Preconditions
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(true);

        $this->mainAdapter->shouldReceive('write')->once()->andReturn(true);
        $this->fallbackAdapter->shouldNotReceive('write');

        // We're testing that the returned value is the same that returns mainAdapter, not that's true.
        $this->assertTrue($this->adapter->write('/path', 'Hello World', new Config()));
    }

    public function testWrite_PathExistsInFallback()
    {
        // Preconditions
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(false);
        $this->fallbackAdapter->shouldReceive('has')->once()->andReturn(true);

        // The data is retrieved through the fallback adapter to be written through the main adapter,
        // this is done in order to ensure consistency in case the 'append mode' is used.
        $this->fallbackAdapter->shouldNotReceive('write');
        $this->fallbackAdapter->shouldReceive('readStream')->once()->andReturn(['stream'=>null]);

        $this->mainAdapter->shouldReceive('writeStream')->once()->andReturn(true);

        // The new data is written AFTER copying the old data, this is inefficient but ensures consistency.
        $this->mainAdapter->shouldReceive('write')->once()->andReturn(true);

        // We're testing that the return valu
        $this->assertTrue($this->adapter->write('/path', 'Hello World', new Config()));
    }

    public function testWrite_PathDoesNotExist()
    {
        // Preconditions
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(false);
        $this->fallbackAdapter->shouldReceive('has')->once()->andReturn(false);

        // We ensure we don't try to copy anything from the fallback to the main adapter
        $this->fallbackAdapter->shouldNotReceive('readStream');
        $this->mainAdapter->shouldNotReceive('writeStream');

        // Only the main adapter receives a write operation
        $this->fallbackAdapter->shouldNotReceive('write');
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

    public function testCopy_PathExistsInMain()
    {
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(true);
        $this->mainAdapter->shouldReceive('copy')->once()->andReturn(true);
        $this->fallbackAdapter->shouldNotReceive('copy');

        $this->assertTrue($this->adapter->copy('/src', '/dest'));
    }

    public function testCopy_PathExistsInFallback()
    {
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(false);
        $this->fallbackAdapter->shouldReceive('has')->once()->andReturn(true);

        $this->fallbackAdapter->shouldReceive('readStream')->once()->andReturn(['stream'=>null]);
        $this->mainAdapter->shouldReceive('writeStream')->once()->andReturn(true);

        $this->assertTrue($this->adapter->copy('/src', '/dest'));
    }

    public function testCopy_PathDoesNotExist()
    {
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(false);
        $this->fallbackAdapter->shouldReceive('has')->once()->andReturn(false);

        $this->assertFalse($this->adapter->copy('/src', '/dest'));
    }

    public function testListContents()
    {
        $this->mainAdapter->shouldReceive('listContents')->once()->andReturn([
            [
                "type" => "file",
                "path" => "A",
                "timestamp" => 1444950202,
                "size" => 0,
            ],
            [
                "type" => "file",
                "path" => "B",
                "timestamp" => 1444950204,
                "size" => 0,
            ],
            [
                "type" => "dir",
                "path" => "C",
                "timestamp" => 1444950309,
            ]
        ]);
        $this->fallbackAdapter->shouldReceive('listContents')->once()->andReturn([
            [
                "type" => "file",
                "path" => "B",
                "timestamp" => 1444950204,
                "size" => 0,
            ],
            [
                "type" => "dir",
                "path" => "C",
                "timestamp" => 1444950309,
            ],
            [
                "type" => "file",
                "path" => "D",
                "timestamp" => 1444950307,
                "size" => 0,
            ]
        ]);

        $this->assertEquals(
            [
                [
                    "type" => "file",
                    "path" => "A",
                    "timestamp" => 1444950202,
                    "size" => 0,
                ],
                [
                    "type" => "file",
                    "path" => "B",
                    "timestamp" => 1444950204,
                    "size" => 0,
                ],
                [
                    "type" => "dir",
                    "path" => "C",
                    "timestamp" => 1444950309,
                ],
                [
                    "type" => "file",
                    "path" => "D",
                    "timestamp" => 1444950307,
                    "size" => 0,
                ]
            ],
            $this->adapter->listContents());
    }
}
