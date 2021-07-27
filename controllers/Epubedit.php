<?php 

require_once("class/Epub.php");

require_once("class/Dir.php");
require_once("class/Ftp.php");


class ControllerEpubedit{
	
	public $server_path;
	public $epub_path;
	public $ftp_class;
	public $dir_class;
	
	
	function __construct(){
		
		$this->server_path = '9788836222674';
		$this->epub_path = _EPUB_PATH;
		$this->dir_class = new Dir();
		
		$ftp_server = 'ep2.qserver.it';
		$ftp_user   = 'edisesprj';
		$ftp_pass   = 'MjBlZGlzZXNwcmoxNQ1_';
		
		
	}
	
	
	public function init(){
		
		
		if(isset($_REQUEST['isbn'])){
			$isbn = $_REQUEST['isbn'];
			$epub_path = $this->epub_path.'/'.$isbn.'/';
			$epub_url = _EPUB_URL_2.'/'.$isbn.'/';
			
			
		}		
		
		
		
				
				
		
		$epub = new Epub($epub_path,$isbn);
		$info_epub = $epub->epub_info();
		
		
		$nav_url = $info_epub['nav_url'];
		$nav_name = basename($nav_url);
		
		$nav = $epub->get_toc_epub($nav_url);
		
		
		
		
		include_once(_PATH_.'/views/header.php');
		include_once(_PATH_.'/views/ebookeditor.php');
		
		
	}
	
	public function iframe(){
	
		include_once(_PATH_.'/views/editor/editor-iframe.xhtml');
		
	}	
	
}

?>