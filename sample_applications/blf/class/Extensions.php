<?php
require_once(dirname(__FILE__) . '/Blf.php');
require_once(dirname(__FILE__) . '/Extension.php');

/**
 * Extensions Class.
 *
 * Extensions class
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class Extensions extends Blf {

	private $extensions = array();

	public function get() {
		$params = array(
			'query' => array(
				'orderBy' => array(array(
					'columnName' => 'telNum',
					'direction' => 'Asc'
				))
			)
		);

		$result = $this->api->sendRequest('Extensions.get', $params);
		
		$extensions = $result['sipExtensionList'];
		$tmp = array();
		
		foreach ($extensions as $ext) {
			$line = new Extension($ext);
			if ($line->multipleGroup == $line->getLineType()) continue; // Skip groups
			
			array_push($tmp, $line);
		}
		
		$this->extensions = $tmp;
		return $this->extensions;
	}	
}
