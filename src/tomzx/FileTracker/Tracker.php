<?php

namespace tomzx\FileTracker;

// TODO: Support various strategies
// get all changed files, only update changed files (upfront scan cost)
// VS
// know a file changed, update all files (update cost)
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
	 */
	public function update($files)
	{
		$files = (array)$files;

		foreach ($files as $file) {
			$this->files[$file] = $this->calculateFileSignature($file);
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
