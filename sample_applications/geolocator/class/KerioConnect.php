<?php
require_once(dirname(__FILE__) . '/../../../src/KerioConnectApi.php');

/**
 * Server Class.
 *
 * This class implements methods for server operations.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class KerioConnect extends KerioConnectApi {

	/**
	 * Get active connections list
	 *
	 * @param	void
	 * @return	array	Active connections list
	 */
	public function getConnections() {
		$method = 'Server.getConnections';
		$params = array(
			'query' => array(
				'orderBy' => array(array(
					'columnName' => 'from',
					'direction' => 'Asc'
				))
			)
		);
		$result = $this->sendRequest($method, $params);
		return $result['list'];
	}
}