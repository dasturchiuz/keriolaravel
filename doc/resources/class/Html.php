<?php
/**
 * HTML Class.
 *
 * HTML related methods
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class Html {

	private $resourcesPath = '';
	private $backpage = '';
	private $title = 'Kerio APIs Client Library for PHP';

	/**
	 * Set page title.
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Set path to your CSS resources.
	 */
	public function setResources($path) {
		$this->resourcesPath = $path; 
	}

	/**
	 * Set page for back action in header page title.
	 * 
	 * @param	string	Page url
	 * @param	string	Page title
	 * @return	void
	 */
	public function setBackpage($url, $title) {
		$this->backpage['url'] = $url;
		$this->backpage['title'] = $title;
	}

	/**
	 * Print page header.
	 *
	 * @param	string	Header title
	 * @param	string	Header CSS class
	 * @return	void
	 */
	public function printHeader($title, $class = null) {
		$pageTitle = ($title && $this->title)
			? sprintf('%s | %s', $title, $this->title)
			: $this->title;
		
		print '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">'
			. '<html>'
			. '<head>'
			. '<meta http-equiv="Content-type" content="text/html; charset=UTF-8">'
			. '<link rel="stylesheet" type="text/css" href="' . $this->resourcesPath . 'css/main.css">'
			. '<link rel="shortcut icon" href="' . $this->resourcesPath . 'images/favicon.ico">'
			. '<title> ' . $pageTitle . '</title>'
			. '</head>'
			. '<body>'
			. '<div id="header" class="shadow-bottom ' . $class . '">'
			. $this->title
			. '</div>'
			. '<div id="content">';
		
		if ($this->backpage) {
			printf('<span class="right-navigation"><a href="%s">%s</a></span>', $this->backpage['url'], $this->backpage['title']);
		}

		if ($title) print '<h1>' . $title . '</h1>';
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
			. 'Copyright © 2012-2012 Kerio Technologies s.r.o. | Version 1.4.0.234 | <a target="_blank" href="http://www.kerio.com/developers">www.kerio.com/developers</a>'
			. '</div>'
			. '</body>'
			. '</html>';
	}

	/**
	 * Print page info in p.info.
	 *
	 * @param	string
	 * @return	void
	 */
	public function printInfo($message) {
		printf('<p class="info">%s</p>', $message);
	}

	/**
	 * Print message in p.success.
	 *
	 * @param	string
	 * @return	void
	 */
	public function printSuccess($message) {
		printf('<p class="success">%s</p>', $message);
	}

	/**
	 * Print message in p.warning.
	 *
	 * @param	string
	 * @return	void
	 */
	public function printWarning($message) {
		printf('<p class="warning">%s</p>', $message);
	}

	/**
	 * Print message in p.note.
	 *
	 * @param	string
	 * @return	void
	 */
	public function printNote($message) {
		printf('<p class="note">%s</p>', $message);
	}

	/**
	 * Print message in p.debug.
	 *
	 * @param	string
	 * @return	void
	 */
	public function printPrettyprint($message) {
		printf('<p class="prettyprint">%s</p>', $message);
	}

	/**
	 * Print message in p.
	 *
	 * @param	string
	 * @return	void
	 */
	public function p($message) {
		printf('<p>%s</p>', $message);
	}
}
