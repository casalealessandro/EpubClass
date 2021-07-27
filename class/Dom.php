<?php 



class Dom{
	
	public $dom;
	public $html;
	public $doc;
	function __construct(){
		
		$this->dom = new domDocument; 
		
	}
	
	private function get_html_from_file($file){
		
		
		
			 
		// load the html into the object
		$html =  file_get_contents($file); 
		 
		$str = str_replace("\r", " ", $html);
		$str = str_replace("\n", " ", $html);

		$this->doc = $str;
		
		
		if($html){
			
			return true;
			
		}
		return false;
	}
	
	
	
	
	public function get_array_element($file){
		
		
		
		$this->get_html_from_file($file);
		
		//echo $html ;
		$this->find('id');
		/*foreach($arr as $item) { // DOMElement Object
			$id =  $item->getAttribute("id");
			
			$links[] =  $id;
			
		}
		return $links;*/
	}
	
	
	public function find($entity){
		
		
	
		$pattern = "/([\w-:\*]*)(?:\#([\w-]+)|\.([\w-]+))?(?:\[@?(!?[\w-:]+)(?:([!*^$]?=)[\"']?(.*?)[\"']?)?\])?([\/, ]+)/is";
		//preg_match_all($pattern, trim($selector_string).' ', $matches, PREG_SET_ORDER);
		preg_match_all($pattern, $this->doc, $matches, PREG_SET_ORDER);
		
		echo "<pre>";
			
		print_r($matches);
		
		echo "</pre>";
		
		
	
	}
	
}


?>