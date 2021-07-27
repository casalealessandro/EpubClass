<?php 

require_once("class/Epub.php");
require_once("class/Dir.php");

class ControllerIndex{
	
	public $server_path;
	public $dir_class;
	
	
	function __construct(){
		
		$this->server_path = 'epub';
		
		
	}
	
	
	public function init(){
		
		///* MODALITA ON LINE *//
		
		
		
		$current_folder = '';
		$breadcrumb = array();
		if(isset($_REQUEST['folder_name'])){
			$current_folder = $_REQUEST['folder_name'] ;
			$path =  $this->server_path.'\\'. $_REQUEST['folder_name'];	
		}else{
			$path =  $this->server_path;
		}
		
		$list_proj = $this->_array_folder(_EPUB_PATH);
		
		$breadcrumb  =  explode('/',$current_folder);
           
       
		
		$array_serve_folder = $this->_array_folder($path);
		
	
		include_once(_PATH_.'/views/header.php');
		include_once(_PATH_.'/views/index.php');
		
	}
	
	
	
	
	
	
	public function _array_folder($path_folder){
		
		//echo $path_folder;
		
		$dir_class = new Dir();
		$_server_folders = $dir_class->scan_folder($path_folder);
		return $_server_folders;
		
	}
	
	
}

?>
