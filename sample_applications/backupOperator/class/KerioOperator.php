<?php
require_once(dirname(__FILE__) . '/../../../src/KerioOperatorApi.php');

/**
 * Server Class.
 *
 * This class implements methods for server operations.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class KerioOperator extends KerioOperatorApi {

	public function getBackupState() {
		$backupState = $this->sendRequest('SystemBackup.get');
		return $backupState['status']['RUNNING'];
	}

	public function createBackup($config) {
		$method = 'SystemBackup.backupStart';
		$params = array(
			'sections' => array(
				'SYSTEM_DATABASE'	=> $config['SYSTEM_DATABASE'],
				'VOICE_MAIL'		=> $config['VOICE_MAIL'],
				'SSL_CERTIFICATES'	=> $config['SSL_CERTIFICATES'],
				'SYSTEM_LOG'		=> $config['SYSTEM_LOG'],
				'CALL_LOG'			=> $config['CALL_LOG'],
				'LICENSE'			=> $config['LICENSE'],
			)
		);
		$response = $this->sendRequest($method, $params);
		return $response;
	}
	
	public function downloadBackup($dir) {
		$method = 'SystemBackup.backupDownload';
		$backup = $this->sendRequest($method);
		
		$this->checkMemoryLimitForDownload($backup['fileDownload']['length']);
		$this->downloadFile($backup['fileDownload']['url'], $dir, $backup['fileDownload']['name']);
	}
	
	private function checkMemoryLimitForDownload($filesize) {
		$usage = memory_get_usage();
		if ($usage <= $filesize) {
			throw new KerioApiException(sprintf('PHP Fatal error: Unable to download backup, allowed memory size of %d bytes exhausted (tried to allocate %d bytes)', $usage, $filesize));
		}
	}
}
