<?php
/**
 * This file is part of the kerio-api-php.
 *
 * Copyright (c) Kerio Technologies s.r.o.
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code
 * or visit Developer Zone. (http://www.kerio.com/developers)
 *
 * Do not modify this source code.
 * Any changes may be overwritten by a new version.
 */

/**
 * Logging Class.
 *
 * This class implements basic method for logging into a file.
 *
 * Example:
 * <code>
 * <?php
 * require_once(dirname(__FILE__) . '/src/Logger.php');
 *
 * $log = new Logger();
 * $log->setFile('log.txt');
 * $log->write('My log message');
 *
 * ?>
 * </code>
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @license		http://www.kerio.com/developers/license-agreement
 * @version		1.4.0.234
 */
class Logger {

	/**
	 * Default file
	 * @var	string
	 */
	private $file = '/tmp/logfile.log';

	/**
	 * File handler
	 * @var filehandler
	 */
	private $fileHandler = '';

	/**
	 * Class constructor.
	 *
	 * @return	void
	 */
	public function Logger() {
		$this->setFile($this->file);
	}

	/**
	 * Class destructor.
	 *
	 * @return	void
	 */
	public function __destruct() {
		$this->closeFile();
	}

	/**
	 * Set a file.
	 *
	 * @param	string
	 * @return	void
	 */
	public function setFile($file) {
		if (is_string($file)) {
			$this->file = $file;
		}
	}

	/**
	 * Write a message to a file.
	 *
	 * Write current time, script name and message to a log file.
	 *
	 * Message is written with the following format:
	 *     Y-m-d hh:mm:ss (script name) message
	 *
	 * @param	string	Message
	 * @return	void
	 */
	public function write($message){
		if (!$this->fileHandler) {
			$this->openFile();
		}

		$selfName = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
		$date = date('Y-m-d H:i:s');

		$message = sprintf('%s (%s) %s%s', $date, $selfName, $message, "\n");

		/* Encode to UTF8 */
		if (mb_detect_encoding($message, "UTF-8") <> "UTF-8") {
			$message = utf8_encode($message);
		}

		@fwrite($this->fileHandler, $message);
	}

	/**
	 * Open a file for writing.
	 *
	 * Open log file for writing only.
	 * Place the file pointer at the end of the file.
	 * If the file does not exist, attempt to create it.
	 *
	 * @return	void
	 * @throws	Exception
	 */
	private function openFile() {
		$this->fileHandler = @fopen($this->file, 'a');
		if (FALSE === $this->fileHandler) {
			throw new Exception(sprintf('Unable to open, file %s is not writeable.', $this->file));
		}
	}

	/**
	 * Close a file.
	 *
	 * @return	void
	 */
	private function closeFile() {
		@fclose($this->fileHandler);
	}
}
