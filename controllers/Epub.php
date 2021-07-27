<?php 

require_once("class/Epub.php");
require_once("class/Dir.php");
require_once("class/Ftp.php");


class ControllerEpub{
	
	public $server_path;
	public $epub_path;
	public $ftp_class;
	
	
	function __construct(){
		
		$this->server_path = 'epub/';
		$this->epub_path = _EPUB_PATH;
		
		
		$ftp_server = '';
		$ftp_user   = '';
		$ftp_pass   = '';
		$this->ftp_class = new Ftp($ftp_server,$ftp_user,$ftp_pass);
		
		
		
		
		
	}
	
	
	public function init(){
		
		
	}
	
	
	
	
	public function _array_folder($path_folder){
		
		//echo $path_folder;
		
		$dir_class = new Dir();
		$_server_folders = $dir_class->scan_folder($path_folder);
		return $_server_folders;
		
	}
	
	public function file(){
		
		if(isset($_REQUEST['file_name'])){
			$path_ebook = $this->server_path.$_REQUEST['file_name'];
		}		
		
		
		$dir  = new Dir();
		$file_name = $dir->file_name_from_path($path_ebook);
		$internal_epub_path = $this->epub_path.$file_name;
		
		$epub_path_info = pathinfo($internal_epub_path);
		
		
		$epub_folder_path = $this->epub_path.$epub_path_info['filename'].'/';
	
		$epub_folder_name = $epub_path_info['filename'];
		
		if(!$dir->check_is_folder($epub_folder_path)){
			$dir->unzip($internal_epub_path,$epub_folder_path);	
			$file_path = $internal_epub_path.'.epub';
		
			if(is_file($internal_epub_path)){
				
				unlink ($internal_epub_path);
			}
		}
		
		
		
		
		$epub = new Epub($epub_folder_path,$epub_path_info['filename']);
		$info_epub = $epub->epub_info();
		$info_epub['isbn'];
		
		
		include_once(_PATH_.'/views/header.php');
		include_once(_PATH_.'/views/ebook.php');
		
		
		
	}
	
	
	
	
	
	private function js_function(){
		
		$script ='
		
			<script>
				$(document).ready(function(){
					
					alert("Vuoi continuare a lavorare su questa versione o vuoi scaricarne una nuova?");
					
					
				})
			
			</script>
		
		';
		return $script;
		
	}
}

?>
