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
	public function printHeader() {
		print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">'
			. '<html>'
			. '<head>'
			. '<meta http-equiv="Content-type" content="text/html; charset=UTF-8">'
			. '<link rel="stylesheet" type="text/css" href="resources/style.css">'
			. '<link rel="shortcut icon" href="../../doc/resources/images/favicon.ico">'
			. '<title>BLF - Busy Lamp Field</title>'
			. '</head>'
			. '<body>'
			. '<div id="content">'
			. '<h1>Busy Lamp Field</h1>';
	}

	/**
	 * Print page footer.
	 *
	 * @param	void
	 * @return	void
	 */
	public function printFooter() {
		print '</div>';
		print '<div id="footer">'
			. '<hr>'
			. '<span class="date">' .date(DATE_RSS) . ' | Version 1.4.0.234</scipt>'
			. '</div>'
			. '</body>'
			. '</html>';
	}

	public function printLegend() {
		print '<div id="legend">'
			. '<h2>Legend</h2>'
			. '<table>'
			. '<tr>'
			. '<td class="offline">off-line, not registered</td>'
			. '<td class="online">on-line, registered</td>'
			. '<td class="dialing">ringing to/from number</td>'
			. '<td class="connected">connected (speeking)</td>'
			. '</tr></table>'
			. '</div>';
	}

	public function printExtension($status, $username, $number, $from = '', $duration = '') {
		printf('<div id="extension" class="%s"><table>'
			. '<tr>'
			. '<td class="username" colspan="2">%s</td>'
			. '</tr>'
			. '<tr>'
			. '<td class="number">%s</td>'
			. '<td class="call">'
			. '%s<br>%s</td>'
			. '</tr>'
			. '</table></div>', $status, $username, $number, $from, $duration);
	}

	/**
	 * Print message in div.error.
	 *
	 * @param	string
	 * @return	void
	 */
	public function printError($message) {
		printf('<div class="error">%s</div>', $message);
	}
}
