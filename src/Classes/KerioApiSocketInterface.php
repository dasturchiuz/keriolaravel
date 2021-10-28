<?php
namespace Dasturchiuz\Keriolaravel\Classes;


/**
 * Kerio API Socket Interface.
 *
 * This interface describes basic methods used in HTTP communication.
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @license		http://www.kerio.com/developers/license/sdk-agreement
 * @version		1.4.0.234
 */
interface KerioApiSocketInterface {

	/**
	 * Send data to socket.
	 *
	 * @param	string
	 */
	public function send($data);
}
