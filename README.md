# File Tracker

[![License](https://poser.pugx.org/tomzx/file-tracker/license.svg)](https://packagist.org/packages/tomzx/file-tracker)
[![Latest Stable Version](https://poser.pugx.org/tomzx/file-tracker/v/stable.svg)](https://packagist.org/packages/tomzx/file-tracker)
[![Latest Unstable Version](https://poser.pugx.org/tomzx/file-tracker/v/unstable.svg)](https://packagist.org/packages/tomzx/file-tracker)
[![Build Status](https://img.shields.io/travis/tomzx/file-tracker.svg)](https://travis-ci.org/tomzx/file-tracker)
[![Code Quality](https://img.shields.io/scrutinizer/g/tomzx/file-tracker.svg)](https://scrutinizer-ci.com/g/tomzx/file-tracker/code-structure)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/tomzx/file-tracker.svg)](https://scrutinizer-ci.com/g/tomzx/file-tracker)
[![Total Downloads](https://img.shields.io/packagist/dt/tomzx/file-tracker.svg)](https://packagist.org/packages/tomzx/file-tracker)

Track files for changes.

## Getting started

```php
<?php

require_once 'vendor/autoload.php';

$bomFilename = '.bom.json';
$tracker = new \tomzx\FileTracker\Tracker($bomFilename);

$files = 'README.md';
if ($tracker->hasChanged($files)) {
	echo 'README changed!' . PHP_EOL;
}

// Update the hash of the given files
$tracker->track($files);

$files = ['composer.json', 'composer.lock'];
if ($tracker->hasChanged($files)) {
	echo 'Run composer!' . PHP_EOL;
}

// Update the hash of the given files
$tracker->track($files);

// Store the latest hash in .bom.json
$tracker->save();

$files = ['.gitignore'];
$changes = $tracker->changedFiles($files);
if ($changes) {
	echo 'Gitignore was changed' . PHP_EOL;
}

// Only update the hash of .gitignore if it changed
$tracker->track(array_keys($changes));
```

## License

The code is licensed under the [MIT license](http://choosealicense.com/licenses/mit/). See [LICENSE](LICENSE).
