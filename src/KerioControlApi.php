<?php
namespace Dasturchiuz\Keriolaravel;
use Dasturchiuz\Keriolaravel\Classes\KerioApi;

/**
 * Administration API for Kerio Control.
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
 * require_once(dirname(__FILE__) . '/src/KerioControlApi.php');
 *
 * $api = new KerioControlApi('Sample application', 'Company Ltd.', '1.0');
 *
 * try {
 *     $api->login('firewall.company.tld', 'admin', 'SecretPassword');
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
class KerioControlApi extends KerioApi {

	/**
	 * Defines product-specific JSON-RPC settings
	 * @var array
	 */
	protected $jsonRpc = array(
		'version'	=> '2.0',
		'port'		=> 4081,
		'api'		=> '/admin/api/jsonrpc/'
	);

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
	 * Get constants defined by product.
	 *
	 * @param	void
	 * @return	array	Array of constants
	 */
	public function getConstants() {
		$response = $this->sendRequest('Server.getNamedConstantList');
		return $response['constants'];
	}
}
