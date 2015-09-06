<?php

namespace Litipk\Flysystem\Replicate;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\FileNotFoundException;

class FallbackAdapter implements AdapterInterface
{
    /**
     * @var AdapterInterface
     */
    protected $mainAdapter;

    /**
     * @var AdapterInterface
     */
    protected $fallback;

    /**
     * @var bool
     */
    protected $ensureRedundancy;

    /**
     * Constructor.
     *
     * @param AdapterInterface $mainAdapter
     * @param AdapterInterface $fallback
     * @param boolean          $ensureRedundancy
     */
    public function __construct(AdapterInterface $mainAdapter, AdapterInterface $fallback, $ensureRedundancy = false)
    {
        $this->mainAdapter = $mainAdapter;
        $this->fallback = $fallback;
        $this->ensureRedundancy = $ensureRedundancy;
    }

    /**
     * Returns the main adapter.
     *
     * @return AdapterInterface
     */
    public function getMainAdapter()
    {
        return $this->mainAdapter;
    }

    /**
     * Returns the fallback adapter.
     *
     * @return AdapterInterface
     */
    public function getFallbackAdapter()
    {
        return $this->fallback;
    }

    /**
     * {@inheritdoc}
     */
    public function write($path, $contents, Config $config)
    {
        // This is done to allow "append" mode in the underlying main adapter
        if (!$this->mainAdapter->has($path) && $this->fallback->has($path)) {
            $this->portFromFallback($path, $path);
        }

        return $this->mainAdapter->write($path, $contents, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $resource, Config $config)
    {
        return $this->mainAdapter->writeStream($path, $resource, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function update($path, $contents, Config $config)
    {
        // This is done to allow "append" mode in the underlying main adapter
        if (!$this->mainAdapter->has($path) && $this->fallback->has($path)) {
            $this->portFromFallback($path, $path);
        }

        return $this->mainAdapter->update($path, $contents, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $resource, Config $config)
    {
        if ($this->mainAdapter->has($path)) {
            return $this->mainAdapter->updateStream($path, $resource, $config);
        } else {
            // TODO: Review, is this necessary?
            return $this->mainAdapter->writeStream($path, $resource, $config);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rename($path, $newpath)
    {
        if ($this->mainAdapter->has($path)) {
            return $this->mainAdapter->rename($path, $newpath);
        }

        if (false !== $this->portFromFallback($path, $newpath)) {
            return $this->fallback->delete($path);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function copy($path, $newpath)
    {
        if ($this->mainAdapter->has($path)) {
            return $this->mainAdapter->copy($path, $newpath);
        }

        return $this->portFromFallback($path, $newpath);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path)
    {
        $found = false;

        if ($this->fallback->has($path)) {
            $fallbackResult = $this->fallback->delete($path);
            $found = true;
        } else {
            $fallbackResult = true;
        }

        if ($this->mainAdapter->has($path)) {
            $mainResult = $this->mainAdapter->delete($path);
            $found = true;
        } else {
            $mainResult = true;
        }

        if (!$found) {
            throw new FileNotFoundException($path);
        }

        return ($fallbackResult && $mainResult);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDir($dirname)
    {
        $found = false;

        if ($this->fallback->has($dirname)) {
            $fallbackResult = $this->fallback->deleteDir($dirname);
            $found = true;
        } else {
            $fallbackResult = true;
        }

        if ($this->mainAdapter->has($dirname)) {
            $mainResult = $this->mainAdapter->deleteDir($dirname);
            $found = true;
        } else {
            $mainResult = true;
        }

        if (!$found) {
            throw new FileNotFoundException($dirname);
        }

        return ($fallbackResult && $mainResult);
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($dirname, Config $config)
    {
        return $this->mainAdapter->createDir($dirname, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function setVisibility($path, $visibility)
    {
        if ($this->mainAdapter->has($path)) {
            return $this->mainAdapter->setVisibility($path, $visibility);
        }

        return $this->fallback->setVisibility($path, $visibility);
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        return $this->mainAdapter->has($path) || $this->fallback->has($path);
    }

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        if ($this->mainAdapter->has($path)) {
            return $this->mainAdapter->read($path);
        }

        return $this->fallback->read($path);
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        if ($this->mainAdapter->has($path)) {
            return $this->mainAdapter->readStream($path);
        }

        return $this->fallback->readStream($path);
    }

    /**
     * {@inheritdoc}
     */
    public function listContents($directory = '', $recursive = false)
    {
        // TODO: Implement listContents() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($path)
    {
        if ($this->mainAdapter->has($path)) {
            return $this->mainAdapter->getMetadata($path);
        }

        return $this->fallback->getMetadata($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize($path)
    {
        if ($this->mainAdapter->has($path)) {
            return $this->mainAdapter->getSize($path);
        }

        return $this->fallback->getSize($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype($path)
    {
        if ($this->mainAdapter->has($path)) {
            return $this->mainAdapter->getMimetype($path);
        }

        return $this->fallback->getMimetype($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp($path)
    {
        if ($this->mainAdapter->has($path)) {
            return $this->mainAdapter->getTimestamp($path);
        }

        return $this->fallback->getTimestamp($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibility($path)
    {
        if ($this->mainAdapter->has($path)) {
            return $this->mainAdapter->getVisibility($path);
        }

        return $this->fallback->getVisibility($path);
    }

    /**
     * Copies a resource accessible through the fallback adapter to the filesystem abstracted with the main adapter.
     *
     * @param $path
     * @return array|false
     */
    private function portFromFallback($path, $newpath)
    {
        $buffer = $this->fallback->readStream($path);

        if (false === $buffer) {
            return false;
        }

        $result = $this->mainAdapter->writeStream($newpath, $buffer['stream'], new Config());

        if (is_resource($buffer['stream'])) {
            fclose($buffer['stream']);
        }

        return $result;
    }
}