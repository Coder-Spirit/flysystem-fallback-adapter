<?php

namespace Litipk\Flysystem\Replicate;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;

class FallbackAdapter implements AdapterInterface
{

    /**
     * {@inheritdoc}
     */
    public function write($path, $contents, Config $config)
    {
        // TODO: Implement write() method.
    }

    /**
     * {@inheritdoc}
     */
    public function writeStream($path, $resource, Config $config)
    {
        // TODO: Implement writeStream() method.
    }

    /**
     * {@inheritdoc}
     */
    public function update($path, $contents, Config $config)
    {
        // TODO: Implement update() method.
    }

    /**
     * {@inheritdoc}
     */
    public function updateStream($path, $resource, Config $config)
    {
        // TODO: Implement updateStream() method.
    }

    /**
     * {@inheritdoc}
     */
    public function rename($path, $newpath)
    {
        // TODO: Implement rename() method.
    }

    /**
     * {@inheritdoc}
     */
    public function copy($path, $newpath)
    {
        // TODO: Implement copy() method.
    }

    /**
     * {@inheritdoc}
     */
    public function delete($path)
    {
        // TODO: Implement delete() method.
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDir($dirname)
    {
        // TODO: Implement deleteDir() method.
    }

    /**
     * {@inheritdoc}
     */
    public function createDir($dirname, Config $config)
    {
        // TODO: Implement createDir() method.
    }

    /**
     * {@inheritdoc}
     */
    public function setVisibility($path, $visibility)
    {
        // TODO: Implement setVisibility() method.
    }

    /**
     * {@inheritdoc}
     */
    public function has($path)
    {
        // TODO: Implement has() method.
    }

    /**
     * {@inheritdoc}
     */
    public function read($path)
    {
        // TODO: Implement read() method.
    }

    /**
     * {@inheritdoc}
     */
    public function readStream($path)
    {
        // TODO: Implement readStream() method.
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
        // TODO: Implement getMetadata() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getSize($path)
    {
        // TODO: Implement getSize() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getMimetype($path)
    {
        // TODO: Implement getMimetype() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp($path)
    {
        // TODO: Implement getTimestamp() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibility($path)
    {
        // TODO: Implement getVisibility() method.
    }
}
