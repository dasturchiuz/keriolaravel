<?php
/**
 * Class with local functions for Kerio Workspace
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @version		1.4.0.234
 */
class SamepageClient {

	/**
	 * API handler
	 * @var	SamepageApi
	 */
	private $api;
	
	/**
	 * Root page constant
	 */
	const ROOT = -1;

	/**
	 * Construct.
	 * 
	 * @param	SamepageApi	API object
	 * @return	object	Workspace
	 */
	public function __construct($api) {
		$this->api = $api;
		return $this;
	}

	/**
	 * Create new component.
	 *  
	 * @param	integer	Item ID
	 * @param	string	Item type
	 * @param	string	Item name
	 * @return	integer	Item ID
	 */
	private function create($pid, $type, $name) {
		$params = array(
			'pid' => $pid,
			'type' => $type,
			'name' => $name
		);
		$result = $this->api->sendRequest('Items.create', $params);
		return $result['item']['id'];
	}

	/**
	 * Create new page.
	 * 
	 * @param	integer	Space ID
	 * @param	string	Page name
	 * @return	integer	Page ID
	 */
	public function createPage($spaceId, $name) {
		return $this->create($spaceId, 'Page', $name);
	}

	/**
	 * Create new file library.
	 * 
	 * @param	integer	Page ID
	 * @param	string	Title
	 * @return	integer	Item ID
	 */
	public function createFileLibrary($pageId, $title) {
		return $this->create($pageId, 'FileLib', $title);
	}

	public function createFolderInFileLibrary($parentId, $title) {
		return $this->create($parentId, 'FileFolder', $title);
	}
	
	/**
	 * Create new text component.
	 * 
	 * @param	integer	Page ID
	 * @param	string	Title
	 * @param	string	Content, can be HTML
	 * @return	integer	Item ID
	 */
	public function createTextNoteComponent($pageId, $title, $content) {
		$id = $this->create($pageId, 'TextNote', $title);
		$params = array(
			'id' => $id,
			'value' => array(
				'text' => $content
			)
		);
		$this->	api->sendRequest('Items.update', $params);
	}

	/**
	 * Upload a file to component.
	 * 
	 * @param	integer	Item ID, can be type of Image, FileLib
	 * @param	string	Path to file
	 * @return	integer	Item ID
	 */
	public function upload($itemId, $file) {
		$result = $this->api->uploadFile($file, $itemId);
		return $result['item']['id'];
	}
	
	/**
	 * Upload all files in given directory to file library
	 * 
	 * @param integer $itemId Item ID of File Library
	 * @param string  $root   Directory to upload
	 */
	public function uploadFilesFromFolder($itemId, $root) {
		$iter = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS),
			RecursiveIteratorIterator::SELF_FIRST,
			RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
		);
		
		foreach ($iter as $path => $dir) {
			if ($dir->isDir()) {
				echo "[D] " . $dir . "\n";
				$dirId = $this->createFolderInFileLibrary($itemId, basename($dir));
				$this->uploadFilesFromFolder($dirId, $dir);
			} else {
				try {
					$this->upload($itemId, $path);
					echo "[F] " . $dir . "\n";
				} catch (KerioApiException $error) {
					echo "[E] " . $error->getMessage() . "\n";
				}
			}
		}
	}
}

