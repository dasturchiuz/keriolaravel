<?php
namespace Dasturchiuz\Keriolaravel;
use Dasturchiuz\Keriolaravel\Classes\KerioApi;

/**
 * Samepage.io.
 * STATUS: In progress, might change in the future
 *
 * This class implements product-specific methods and properties and currently is under development.
 * Class is not intended for stable use yet.
 * Functionality might not be fully verified, documented, or even supported.
 *
 * Please note that changes can be made without further notice.
 *
 * Example:
 * <code>
 * <?php
 * require_once(dirname(__FILE__) . '/src/KerioWorkspaceApi.php');
 *
 * $api = new SamepageApi('Sample Application', 'Company Ltd.', '1.0');
 *
 * try {
 *     $api->login('samepage.io', 'user@company.tld', 'SecretPassword');
 *     $api->sendRequest('...');
 *     $api->logout();
 * } catch (KerioApiException $error) {
 *     print $error->getMessage();
 * }
 * ?>
 * </code>
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @license		http://www.kerio.com/developers/license/sdk-agreement
 * @version		1.4.0.234
 */
class SamepageApi extends KerioApi {

	/**
	 * Defines default product-specific JSON-RPC settings
	 * @var array
	 */
	protected $jsonRpc = array(
		'version'	=> '2.0',
		'port'		=> 443,
		'api'		=> '/server/data'
	);
	
	/**
	 * File info, for upload
	 * @var	array
	 */
	private $file = array();

	/**
	 * Tenant info
	 * @var	string
	 */
	private $tenant = '';
	
	private $endpoint = '';

	/**
	 * Class constructor.
	 *
	 * @param	string	Application name
	 * @param	string	Application vendor
	 * @param	string	Application version
	 * @return	void
	 * @throws	KerioApiException
	 */
	public function __construct($name, $vendor, $version) {
		parent::__construct($name, $vendor, $version);
	}

	/**
	 * @see class/KerioApi::login()
	 */
	public function login($hostname, $username, $password) {
		$this->application = 'CLIENT';
		$response = parent::login($hostname, $username, $password);
		if ($response['tenant']) {
			$this->setTenant($response['tenant']);
		}
		return $response;
	}
	
	/**
	 * @see class/KerioApi::logout()
	 */
	public function logout() {
		$response = parent::logout();
		$this->jsonRpc['api'] = '/server/data';
	}

	/**
	 * Get tenant.
	 * 
	 * @param	void
	 * @return	string
	 */
	public function getTenant() {
		return $this->tenant;
	}

        /**
         * Set tenant.
         * 
         * @param   string
         * @return  void
         */
        public function setTenant($tenantId) {
            $this->tenant = $tenantId;
            $this->jsonRpc['api'] = sprintf('/%s%s', $this->tenant, '/server/data');
        }
        
	/**
	 * Get headers for PUT request.
	 *
	 * @param	string	Request body
	 * @return	string	Request body
	 */
	protected function getHttpPutRequest($data) {
		$this->headers['POST']			= sprintf('%s&filename=%s&parentId=%d&lenght=%d HTTP/1.1', $this->endpoint, rawurlencode($this->file['filename']), $this->file['parentId'], $this->file['lenght']);
		$this->headers['Accept:']		= '*/*';
		$this->headers['Content-Type:']	= sprintf('application/k-upload');
	
		return $data;
	}
	
	/**
	 * Put a file to server.
	 *
	 * @param	string	Absolute path to file
	 * @param	integer	Reference ID where uploaded file belongs to
	 * @return	array	Result
	 * @throws	KerioApiException
	 */
	public function uploadFile($filename, $id = null) {
		$data = @file_get_contents($filename);

		$this->endpoint = sprintf('%s?method=Files.create', $this->jsonRpc['api']);

		$this->file['filename'] = basename($filename);
		$this->file['parentId'] = $id;
		$this->file['lenght']	= strlen($data);
	
		if ($data) {
			$this->debug(sprintf('Uploading file %s to item %d', $filename, $id));
			$json_response = $this->send('PUT', $data);
		}
		else {
			throw new KerioApiException(sprintf('Unable to open file %s', $filename));
		}
	
		$response = json_decode($json_response, TRUE);
		return $response['result'];
	}
}
