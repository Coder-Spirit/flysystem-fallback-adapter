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

    public function testWrite()
    {
        // Preconditions
        $this->mainAdapter->shouldReceive('write')->once()->andReturn(true);

        // We're testing that the returned value is the same that returns mainAdapter, not that's true.
        $this->assertTrue($this->adapter->write('/path', 'Hello World', new Config()));
    }

    public function testWriteStream()
    {
        // Preconditions
        $this->mainAdapter->shouldReceive('writeStream')->once()->andReturn(true);

        // We're testing that the returned value is the same that returns mainAdapter, not that's true.
        $this->assertTrue($this->adapter->writeStream('/path', 'Hello World', new Config()));
    }

    public function testUpdate_PathExistsInMain()
    {
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(true);
        $this->mainAdapter->shouldReceive('update')->once()->andReturn(true);

        $this->assertTrue($this->adapter->update('/path', 'Hello World', new Config()));
    }

    public function testUpdate_PathExistsInFallback()
    {
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(false);
        $this->fallbackAdapter->shouldReceive('has')->once()->andReturn(true);

        $this->fallbackAdapter->shouldReceive('readStream')->once()->andReturn(['stream'=>null]);
        $this->mainAdapter->shouldReceive('writeStream')->once()->andReturn(true);

        $this->mainAdapter->shouldReceive('update')->once()->andReturn(true);

        $this->assertTrue($this->adapter->update('/path', 'Hello World', new Config()));
    }

    public function testUpdateStream_PathExistsInFallback()
    {
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(true);
        $this->mainAdapter->shouldReceive('updateStream')->once()->andReturn(true);

        $this->assertTrue($this->adapter->updateStream('/path', 'Hello World', new Config()));
    }

    public function testUpdateStream_PathDoesNotExists()
    {
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(false);
        $this->mainAdapter->shouldReceive('writeStream')->once()->andReturn(true);

        $this->assertTrue($this->adapter->updateStream('/path', 'Hello World', new Config()));
    }

    public function testDelete_Main()
    {
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(true);
        $this->fallbackAdapter->shouldReceive('has')->once()->andReturn(false);

        $this->mainAdapter->shouldReceive('delete');
        $this->fallbackAdapter->shouldNotReceive('delete');

        $this->adapter->delete('/path');
    }

    public function testDelete_Fallback()
    {
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(false);
        $this->fallbackAdapter->shouldReceive('has')->once()->andReturn(true);

        $this->fallbackAdapter->shouldReceive('delete');
        $this->mainAdapter->shouldNotReceive('delete');

        $this->adapter->delete('/path');
    }

    public function testDelete_MainFallback()
    {
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(true);
        $this->fallbackAdapter->shouldReceive('has')->once()->andReturn(true);

        $this->mainAdapter->shouldReceive('delete');
        $this->fallbackAdapter->shouldReceive('delete');

        $this->adapter->delete('/path');
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

    public function testRename_PathExistsInMain()
    {
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(true);
        $this->mainAdapter->shouldReceive('rename')->once()->andReturn(true);

        $this->assertTrue($this->adapter->rename('/src', '/dest'));
    }

    public function testRename_PathExistsInFallback()
    {
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(false);

        $this->fallbackAdapter->shouldReceive('readStream')->once()->andReturn(['stream'=>null]);
        $this->mainAdapter->shouldReceive('writeStream')->once()->andReturn(true);

        $this->fallbackAdapter->shouldReceive('delete')->once()->andReturn(true);

        $this->assertTrue($this->adapter->rename('/src', '/dest'));
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

    public function testGetVisibility_PathExistsInMain()
    {
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(true);
        $this->mainAdapter->shouldReceive('getVisibility')->once()->andReturn(true);
        $this->fallbackAdapter->shouldNotReceive('getVisibility');

        $this->assertTrue($this->adapter->getVisibility('/path'));
    }

    public function testGetVisibility_PathExistsInFallback()
    {
        $this->mainAdapter->shouldReceive('has')->once()->andReturn(false);
        $this->mainAdapter->shouldNotReceive('getVisibility');
        $this->fallbackAdapter->shouldReceive('getVisibility')->once()->andReturn(true);

        $this->assertTrue($this->adapter->getVisibility('/path'));
    }

    public function testGetVisibility_PathDoesNotExist()
    {
        // TODO : Assert Exception?
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
