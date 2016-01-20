<?php

namespace tomzx\FileTracker;

class Tracker
{
	/**
	 * @var string
	 */
	private $bomFilename;

	/**
	 * @var string[]
	 */
	private $files = [];

	/**
	 * @param string $bomFilename
	 */
	public function __construct($bomFilename)
	{
		$this->bomFilename = $bomFilename;

		// TODO: Could be made lazy (only on first hasChanged call) <tom@tomrochette.com>
		if (is_readable($this->bomFilename)) {
			$this->files = (array)json_decode(file_get_contents($this->bomFilename), true);
		}
	}

	/**
	 * @param string|string[] $files
	 * @return bool
	 */
	public function hasChanged($files)
	{
		$files = (array)$files;

		// Bom file is missing some of the files we want to know about
		if ( ! empty(array_diff($files, array_keys($this->files)))) {
			return true;
		}

		// File signature is different
		foreach ($files as $file) {
			$hash = $this->files[$file];
			if ($hash !== $this->calculateFileSignature($file)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string|string[] $files
	 * @return string[]
	 */
	public function changedFiles($files)
	{
		$files = (array)$files;

		$changes = [];
		foreach ($files as $file) {
			if ( ! array_key_exists($file, $this->files)) {
				if (is_readable($file)) {
					$changes[$file] = 'added';
				} else {
					$changes[$file] = 'unknown';
				}

				continue;
			}

			if ( ! is_readable($file)) {
				$changes[$file] = 'deleted';
				continue;
			}

			$hash = $this->files[$file];
			if ($hash !== $this->calculateFileSignature($file)) {
				$changes[$file] = 'changed';
			}
		}

		return $changes;
	}

	/**
	 * @param string|string[] $files
	 */
	public function track($files)
	{
		$files = (array)$files;

		foreach ($files as $file) {
			if ( ! is_readable($file)) {
				unset($this->files[$file]);
				continue;
			}

			$this->files[$file] = $this->calculateFileSignature($file);
		}
	}

	/**
	 * @param string|string[] $files
	 */
	public function untrack($files)
	{
		$files = (array)$files;

		foreach ($files as $file) {
			unset($this->files[$file]);
		}
	}

	/**
	 * @return void
	 */
	public function save()
	{
		file_put_contents($this->bomFilename, json_encode($this->files, JSON_PRETTY_PRINT));
	}

	/**
	 * @param string $file
	 * @return string
	 */
	protected function calculateFileSignature($file)
	{
		return sha1_file($file);
	}
}
