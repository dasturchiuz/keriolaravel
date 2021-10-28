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
			. '<link rel="stylesheet" type="text/css" href="resources/style.css">'
			. '<link rel="shortcut icon" href="../../doc/resources/images/favicon.ico">'
			. '<title>Backup | Kerio APIs Client Library for PHP</title>'
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
	 * Print checkbox.
	 * 
	 * @param	string	Name
	 * @param	boolean	Checked
	 * @param	boolean	Disabled
	 */
	public function printCheckbox($name, $checked = FALSE, $disabled = FALSE) {
		$checked	= ($checked) ? 'checked' : '';
		$disabled	= ($disabled) ? '' : 'disabled';
		printf('<input type="checkbox" name="%s" %s %s>', $name, $checked, $disabled);
	}

	/* New backup form */
	public function printBackupForm($submitAllowed = TRUE) {
		print '<div class="legends">'; 
		print '<form method="POST" action="">';
		print '<fieldset class="form">';

		print '	<legend>Backup configuration</legend>';
		
		$this->printCheckbox('SYSTEM_DATABASE', TRUE, $submitAllowed);
		print '<label>System configuration</label><br>';
		
		$this->printCheckbox('VOICE_MAIL', TRUE, $submitAllowed);
		print '<label>Voicemail data</label><br>';

		$this->printCheckbox('SSL_CERTIFICATES', TRUE, $submitAllowed);
		print '<label>SSL certificates</label><br>';

		$this->printCheckbox('SYSTEM_LOG', TRUE, $submitAllowed);
		print '<label>System logs</label><br>';

		$this->printCheckbox('CALL_LOG', TRUE, $submitAllowed);
		print '<label>Call logs</label><br>';

		$this->printCheckbox('LICENSE', TRUE, $submitAllowed);
		print '<label>License</label><br><br>';
		
		if($submitAllowed) {
			print '<script type="text/javascript">
				function toggle(el) {
					myEl = document.getElementById(el);
					myEl.style.display = (myEl.style.display == "none") ? "inline" : "none";
				}
				</script>';
			print '<input type="submit" value="Start backup" onclick="toggle(\'progress\');"><span id="progress" style="display: none;">Please wait...</span>';
		}
		else {
			print '<a href="javascript:window.location.reload()">[reload]</a>';
		}
		
		print '</fieldset>';
		print '</form>';
		print '</div>';
	}
	
	/* Show last 10 backups */
	public function printBackupFileList($directory, $mask = '*.*', $limit = 10) {
		$files = glob($directory . '/' . $mask);
		array_multisort(
		    array_map('filemtime', $files),
		    SORT_NUMERIC,
		    SORT_DESC,
		    $files
		);
		
		/* Check requested limit to display */
		$limit = (count($files) > $limit) ? $limit : count($files);
		
		/* Display data */
		$i = 0;
		print '<div class="legends">';
		print '<fieldset class="backups">';
		printf('<legend>Last %d backups</legend>', $limit);
		while($i < $limit) {
			list($dir, $file) = explode('/', $files[$i]);
			printf('<li><a href="%s/%s">%s</a></li>', $dir, $file, $file);
			$i++;
		}
		print '</fieldset>';
		print '</div>';
	}
}
