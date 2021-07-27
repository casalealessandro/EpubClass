<?php
ini_set('memory_limit', '-1');
set_time_limit(0);


require_once "Epub.php";





class EpubeEditor {
	
	public $epubClass;
	public $epub_path;
	public $sep;
	public $epub_url;
	public $year;
	public $date_EN;
	
	public function __construct($path_ebook,$folder_ebook){
		
		$this->sep = DIRECTORY_SEPARATOR;
		//echo $path_ebook;
		//echo $path_ebook;
		$this->epub_path = $path_ebook;
		$this->epub_url = _EPUB_URL.$folder_ebook;
		$this->year = date("Y");
		$this->date_EN = date('Y-m-d\TH:i:s\Z');
		$this->epubClass = new Epub($path_ebook,$folder_ebook);
		
		
		
	}
	
	
	
	
	public function get_first_charapter(){
				
		
		
		$nav = file_get_contents($this->epub_path .'OEBPS/nav.xhtml');
		
		$doc = new DOMDocument();
		libxml_use_internal_errors(true);
		$doc->loadHTML($nav);
		libxml_clear_errors();
		$first_elemnt = $doc->getElementsByTagName('a')->item(0);
		
		
		
		
		
			
		return $first_elemnt->getAttribute('href');
		
		
	}
	
	
	
	
	//***TUTTO EPUBLISHERE***//
	
	
	
	/***********NAV CUSTOM*************/
	
	public function update_nav_epublishare($nav_path,$layers){
		
		$nav_content = file_get_contents($nav_path);
		$layers_nav = $layers . 'nav.txt';
		
		preg_match('/<nav epub:type="toc" id="toc">(.*?)<\/nav>/s', $nav_content, $match);
		$nav_content = str_replace('epub:type="toc"', '', $match[0]);
	
		if(file_put_contents($layers_nav,$nav_content)>0){
			
			unlink($nav_path);
			
			
		}
			
		
		
		
				
	}
	
	
	
	//***FINE TUTTO EPUBLISHERE***//
	
	
	
	
	
	
	
	
}