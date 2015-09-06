# Flysystem adapter for fallback filesystems

## Installation

```bash
composer require litipk/flysystem-fallback-adapter
```

## Usage

```php
$main = new League\Flysystem\Adapter\AwsS3(...);
$fallback = new League\Flysystem\Adapter\Local(...);
$adapter = new Litipk\Flysystem\Fallback\FallbackAdapter($main, $fallback);
```
