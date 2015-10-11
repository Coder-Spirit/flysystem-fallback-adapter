# Flysystem adapter for fallback filesystems


[![Author](http://img.shields.io/badge/author-@castarco-blue.svg?style=flat-square)](https://twitter.com/castarco)
[![Build Status](https://img.shields.io/travis/Litipk/flysystem-fallback-adapter/master.svg?style=flat-square)](https://travis-ci.org/Litipk/flysystem-fallback-adapter)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/litipk/flysystem-fallback-adapter.svg?style=flat-square)](https://scrutinizer-ci.com/g/litipk/flysystem-fallback-adapter/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/litipk/flysystem-fallback-adapter.svg?style=flat-square)](https://scrutinizer-ci.com/g/litipk/flysystem-fallback-adapter)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/litipk/flysystem-fallback-adapter.svg?style=flat-square)](https://packagist.org/packages/litipk/flysystem-fallback-adapter)
[![Total Downloads](https://img.shields.io/packagist/dt/litipk/flysystem-fallback-adapter.svg?style=flat-square)](https://packagist.org/packages/litipk/flysystem-fallback-adapter)


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
