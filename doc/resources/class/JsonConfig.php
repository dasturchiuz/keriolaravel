<?php
/**
 * Store config in JSON file.
 */
class JsonConfig {

	private $file;

	public function __construct($file)  {
		$this->setConfig($file);
	}

	/**
	 * Set config file.
	 *
	 * @param	string	Config file path
	 * @return	void
	 */
	public function setConfig($file) {
		$this->file = $file;
	}

	/**
	 * Get config.
	 *
	 * @param	void
	 * @return	array	Config
	 * @throws	Exception	Cannot open config file
	 */
	public function get() {
		$content = @file_get_contents($this->file); 
		if (FALSE === $content) {
			throw new Exception(sprintf('Cannot open config file %s', $this->file));
		}
		return json_decode($content, TRUE);
	}

	/**
	 * Set config (new content).
	 *
	 * @param	array	Config
	 * @return	boolean True on success
	 * @throws	Exception	Cannot open/write file
	 */
	public function set($data) {
		if (is_writable($this->file)) {

			if (!$handle = fopen($this->file, 'w')) {
				throw new Exception(sprintf('Cannot open file %s', $this->file));
			}

			$json = json_encode($data);

			if (fwrite($handle, $json) === FALSE) {
				throw new Exception(sprintf('Cannot write to file %s', $this->file));
			}

			fclose($handle);
		}
		else {
			throw new Exception(sprintf('File %s is not writeable.', $this->file));
		}
		return TRUE;
	}

	/**
	 * Add entry to config file (append to existing content).
	 *
	 * @param	array	Config entry
	 * @return	void
	 */
	public function addEntry($array) {
		$data = $this->get();
		array_push($data, $array);
		$this->set($data);
	}
}