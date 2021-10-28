<?php
/**
 * HTML Class.
 *
 * HTML related methods
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class HtmlHelper {

	/**
	 * Print page header.
	 *
	 * @param	void
	 * @return	void
	 */
	public function printHeader($title) {
		print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">'
			. '<html>'
			. '<head>'
			. '<meta http-equiv="Content-type" content="text/html; charset=UTF-8">'
			. '<link rel="stylesheet" type="text/css" href="../../doc/resources/css/main.css">'
			. '<link rel="shortcut icon" href="../../doc/resources/images/favicon.ico">'
			. '<title> ' . $title . ' | Kerio APIs Client Library for PHP</title>'
			. '</head>'
			. '<body>'
			. '<div id="content">'
			. '<h1>' . $title . '</h1>';
	}

	/**
	 * Print page footer.
	 *
	 * @param	void
	 * @return	void
	 */
	public function printFooter() {
		print '</div>'
			. '<div id="footer">'
			. '<hr>'
			. '<span class="date">' .date(DATE_RSS) . ' | Version 1.4.0.234</scipt>'
			. '</div>'
			. '</body>'
			. '</html>';
	}

	/**
	 * Print page info.
	 *
	 * @param	string
	 * @return	void
	 */
	public function printInfo($info) {
		print '<div class="info">' . $info . '</div>';
	}

	/**
	 * Print message in div.success.
	 *
	 * @param	string
	 * @return	void
	 */
	public function printSuccess($message) {
		print '<div class="success">' . $message . '</div>';
	}

	/**
	 * Print message in div.error.
	 *
	 * @param	string
	 * @return	void
	 */
	public function printError($message) {
		print '<div class="error">' . $message . '</div>';
	}

	/**
	 * Print message in div.debug.pre.
	 *
	 * @param	string
	 * @return	void
	 */
	public function printDebug($message) {
		print '<div class="debug"><pre>';
		print_r($message);
		print '</pre></div>';
	}
}
