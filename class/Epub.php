<?php
ini_set('memory_limit', '-1');
set_time_limit(0);


require_once "class/Dir.php";


class Epub {
	
	
	public $epub_path;
	public $sep;
	public $epub_url;
	public $year;
	public $date_EN;
	
	public function __construct($path_ebook='',$folder_ebook=''){
		date_default_timezone_set('Europe/Rome'); 
		$this->sep = DIRECTORY_SEPARATOR;
		
		$this->epub_path = $path_ebook;
		$this->epub_url = _EPUB_URL.$folder_ebook;
		$this->year = date("Y");
		$this->date_EN = date('Y-m-d\TH:i:s\Z');
		
	}
	
	
	
	
	public function epub_info(){
		
		$info_array = Array();
		$url = $this->epub_path .'/META-INF/container.xml';
		
		$xml_mime = simplexml_load_file($url);
		$container_url = $this->epub_path. $xml_mime->rootfiles->rootfile['full-path'];
		
		
		if ($container_url ==='') {
           return false;
        }
        
		
		//get_file_contentopf($xml_mime)
		
		$info_array = $this->get_file_contentopf($container_url);
		
		return 	$info_array;
	}
	
	
	public function get_file_contentopf($c_url){
		//echo $c_url;
		$xml_content = simplexml_load_file($c_url);
		
		$metadata = $xml_content->metadata;
		$manifest = $xml_content->manifest;
				
		$cover_href = $this->get_url_by_property($manifest,'cover-image' );
		$nav_href = $this->get_url_by_property($manifest, 'nav' );
		$url_cover = $this->epub_url.'/OEBPS/'.$cover_href;
		$url_nav = $this->epub_url.'/OEBPS/'.$nav_href;
		
		
		$a_Info['cover'] = $cover_href;
		$a_Info['cover_url'] = $url_cover;
		$a_Info['nav_href'] = $nav_href;
		$a_Info['nav_url'] = $url_nav;
		$a_Info['title']= $xml_content->metadata->children('dc', true)->title->__toString();
		$s_isbn = $xml_content->metadata->children('dc', true)->identifier->__toString();
		$a_Info['isbn']= str_replace('urn:isbn:','',$s_isbn);
		$a_Info['creator']= $xml_content->metadata->children('dc', true)->creator->__toString();
		$a_Info['publisher']= $xml_content->metadata->children('dc', true)->publisher->__toString();
		
			
			
		return $a_Info;
		
		
		
			
			
	}
	
	public function get_url_by_property($manifest,$property){
		$url = '';
		
		
		
		
		for($x=0; $x<= count($manifest->item); $x++){
			//echo $manifest->item[$x]['properties'].' == '.$cover.'<br />';
			if(isset($manifest->item[$x]['properties']) && (string)$manifest->item[$x]['properties'] === $property  ){
				
				$url =  $manifest->item[$x]['href'];
				//move_uploaded_file()
			}
		
		}
		
		return $url;
		
	}
	
	public function elabora_content($ebook_info){
		
		$root = $this->epub_path ;
		
		
		$dir_class = new Dir();
		
		$array_dir = $dir_class->scan_folder($root);
		
		
	
		foreach($array_dir as $key => $dir){
			
			if(is_array($dir)){
				
				foreach($dir as $k => $d){
					$path = $root.DIRECTORY_SEPARATOR.$d;
					if($dir_class->check_is_folder($path)){
						
						$array_files['content'][] = $dir_class->scan_dir($path);
					}	
					
				
				}	
				
			}else{
				
				$array_files['content'][] = $root.$dir;
				
				
			}	
				
				
		
		}
		
		
		
		
		$array_files['ebook_info'] = $ebook_info;
		
		
		$this->costruisci_xml_file($array_files);
		
		
	}
	
	public function costruisci_xml_file($a_files){
		
		
				
				
		$root = $this->epub_path .'OEBPS/';
		
		$dir_class = new Dir();
		
		$filePath = $root.'content.opf';

		$dom     = new DOMDocument('1.0', 'utf-8'); 
		$dom->formatOutput = true;
		$dom->preserveWhiteSpace = false;
		
		$package = $dom->createElement('package');
		$package->setAttribute('xmlns', 'http://www.idpf.org/2007/opf');
		$package->setAttribute('unique-identifier', 'bookuid');
		$package->setAttribute('version', '3.0');


		$metadata           = $dom->createElement('metadata' );
		$metadata->setAttribute('xmlns:dc','http://purl.org/dc/elements/1.1/');
		$metadata->setAttribute('xmlns:opf','http://www.idpf.org/2007/opf');
		$identifier  = $dom->createElement('dc:identifier', 'urn:isbn:'.$a_files['ebook_info']['isbn']);
		$identifier->setAttribute('id', 'bookuid');
		
		$meta  = $dom->createElement('meta', '15');
		$meta->setAttribute('refines', '#bookuid');
		$meta->setAttribute('property', 'identifier-type');
		$meta->setAttribute('scheme', 'onix:codelist5');
		$title = $dom->createElement('dc:title', $a_files['ebook_info']['title']);
        $creator = $dom->createElement('dc:creator', $a_files['ebook_info']['author']);
		
		$publisher = $dom->createElement('dc:publisher', $a_files['ebook_info']['rights']);
        $language = $dom->createElement('dc:language', 'it');
        $rights = $dom->createElement('dc:rights', 'Copyright &#x00A9;  '.$this->year . ' '. $a_files['ebook_info']['rights']);
		$meta_name  = $dom->createElement('meta');
		$meta_name->setAttribute('name', 'sugarcube');
		$meta_name->setAttribute('content', '0.01');
		//$meta_property = $dom->createElement('meta', $this->date_EN);
		$meta_modifier  = $dom->createElement('meta', $this->date_EN);
		$meta_modifier->setAttribute('property', 'dcterms:modified');
        $meta_cover = $dom->createElement('meta');
        $meta_cover->setAttribute('name', 'cover');
        $meta_cover->setAttribute('content', 'cover-image');
       
		$nav = $root.$a_files['ebook_info']['nav'];
		
		
		//property="dcterms:modified"
		
		
		$metadata->appendChild($identifier);
		$metadata->appendChild($meta);
		$metadata->appendChild($title);
		$metadata->appendChild($creator);
		$metadata->appendChild($publisher);
		$metadata->appendChild($language);
		$metadata->appendChild($rights);
		$metadata->appendChild($meta_name);
		$metadata->appendChild($meta_cover);
		$metadata->appendChild($meta_modifier);
		
		$package->appendChild($metadata);
		$manifest = $dom->createElement('manifest');
		
		/*COVER*/
		
		$cover = $a_files['ebook_info']['cover'];
		$cover_path = $root.$cover;
		$mediatype_cover   =  $this->epub_content_mediatype($cover_path);
		
		$item = $dom->createElement('item');
		$item->setAttribute('id', 'cover_img' );
		$item->setAttribute('href',$cover );
		$item->setAttribute('media-type', $mediatype_cover);
		$item->setAttribute('properties', 'cover-image');
		$manifest->appendChild($item);
		
		/*INDICE*/
		
		$nav = $a_files['ebook_info']['nav'];
		$nav_path = $root.$nav;
		$mediatype_nav   =  $this->epub_content_mediatype($nav_path);
		
		$item = $dom->createElement('item');
		$item->setAttribute('id', 'nav_toc' );
		$item->setAttribute('href',$nav );
		$item->setAttribute('media-type', $mediatype_nav);
		$item->setAttribute('properties', 'nav');
		$manifest->appendChild($item);
	
		
		$x = 0;
		foreach($a_files['content']  as $key=> $files_p){
			
		
			if(is_array($files_p)){
				$id_prec = '';
				
				foreach($files_p as $k=> $file_p){
				
					
					
					$file_p = str_replace('\\','/',$file_p );
					$path_ebook =  str_replace($root,'',$file_p);
					
					$file_name  =  $dir_class->file_name_from_path($file_p);
					
					$mediatype  =  $this->epub_content_mediatype($file_p);
					$property   =  $this->epub_content_property($file_p);
					$id = preg_replace('/^\d+/','',$file_name);// tutti caratteri numerici all'inzio del nome del file
					$id = preg_replace('/.\w+$/','',$id);
					$id = preg_replace('/\W+/','',$id);
					$m_dir =  $dir_class->folder_name($root,$file_p);
					
					$file_info =  pathinfo($file_p);
				
				
					/*if($file_info['extension'] === 'xhtml'){
						
						/***QUESTO SERVE PER GENERARE IN SEGUITO LO SPINE DELL EBOOK ED OTTENERE UN ID CHE SIA VALIDO SOLO PER LA CARTELLA TEXT O EPUBLISHER CAP PERSONALIZZATI PER POI DOPO PER LEGGERE SEMPLICEMENTE IL NAV
						$id = 'f'.$id;
						
						modifica del 16/06/2021 
					}else
						*/
					
					
					if($m_dir === 'epublishare/layers/custom/chapters'){
						
						$id = 'custom_'.$id; 
						
					}else{
						//$file_name
						$id = 'f'.$id.$x;
						
					}
					if($file_info['extension'] == 'xhtml'){
						
						$file_spine['spine_folder'][] = $m_dir; 
					}
					
					if($file_info['filename'] != 'toc' && $file_info['filename'] != 'content' && $path_ebook != $cover  && $path_ebook != $a_files['ebook_info']['nav'] && $file_info['extension'] != 'xhtml'){
						//echo  $file_info['extension']. '<br />';
						//echo  $path_ebook. '<br />';
						
						$item = $dom->createElement('item');


						if(!empty($mediatype)|| !$mediatype == null || $mediatype !=''){
							
							$item->setAttribute('id', $id );
							$item->setAttribute('href',$path_ebook );
							$item->setAttribute('media-type', $mediatype);
							
							if($property != ''){
								
								$item->setAttribute('properties', $property);
								
							}
						   
							
							$manifest->appendChild($item);
							$x++;
						}
					}	
					
					
				}
				
				
				
				
			
			
			}
				
			
		}
		
		$nav_href = $this->get_spine_epub($nav_path);
			
		$text_folder = array_unique($file_spine['spine_folder']); 		
		foreach ($nav_href as $nav_field) {
			
			//$filed =  $nav_field->getAttribute('href');
			$file_name  =  $dir_class->file_name_from_path($root.$nav_field);
			
			$file_nav = $root.$text_folder[0].'/'.$nav_field;
	
			$id = preg_replace('/.xhtml.*/', '', $nav_field);
			$id = preg_replace('/\W+/','',$id);
			$id = 'f'.$id;
			$mediatype_nav  =  $this->epub_content_mediatype($file_nav);
			$property_nav   =  $this->epub_content_property($file_nav);
			
			$folder_name = $dir_class->folder_name($root,$file_nav);
			
			$spine = $folder_name.'/'.$nav_field;
			
			if(!empty($mediatype_nav)|| !$mediatype_nav == null || $mediatype_nav !=''){
				$item = $dom->createElement('item');	
				$item->setAttribute('id', $id );
				$item->setAttribute('href',$spine);
				$item->setAttribute('media-type', $mediatype_nav);
				
				if($property_nav != ''){
					
					$item->setAttribute('properties', $property_nav);
					
				}
			   
				
				$manifest->appendChild($item);
				
			}
			
			
			
		
			$itemref_array[] = $id;
		}
		
		
		//echo $id_toc;
		$package->appendChild($manifest);
		$spine  = $dom->createElement('spine' );
		
		//$spine->setAttribute('toc', $id_toc);
		
		
		
		$itemref_array = array_unique($itemref_array);//Lo faccio perchè può capitare che un href sia duplicato nel nav; quindi elimino i duplicati array_unique.
		
		
		
		foreach ($itemref_array as $item) {
			
			$item = $item;
			$itemref = $dom->createElement('itemref');
			$itemref->setAttribute('idref',$item );
			$spine->appendChild($itemref);
		}	
		
		$package->appendChild($spine);
	
		$dom->appendChild($package);
		if($dom->save($filePath)){
		
			echo 'FATTO';
		}else{
			
			echo 'ERRORE!!  contatta webmastercasale@gmail.com';
		};
	
	}
	

	
	private function epub_content_property($file_path){
		
		
		$file_info =  pathinfo($file_path);
		$extension = $file_info['extension'];
		$script_prop = '';
		$math_prop = '';
		
		
		switch ($extension) {
			
			
			
			case "xhtml":
			
				$math  = $this->find_math_script($file_path);
				$script = $this->find_generic_script($file_path);
				if($script){
					$script_prop = 'scripted';
										
				}
				
				if($math){
					
					$math_prop = ' mathml';
				
				}
				
				
				$property = $script_prop.$math_prop;
				break;
				
			
			
			case "svg":
				
				$property = 'svg'; 
				break;	
				
			default:
				$property =  '';
				
		}
		
		return $property;
		
	}
	
	private function epub_content_mediatype($file_path){
		
		
		$file_info =  pathinfo($file_path);
		$extension = $file_info['extension'];
		

		
		switch ($extension) {
			
			
			case "png":
				$mediatype = 'image/png';
				break;
			case "svg":
				$mediatype = 'image/svg+xml';
				break;
			case "jpeg":
				$mediatype = 'image/jpeg';
				break;
			case "jpg":
				$mediatype = 'image/jpeg';
				break;
			case "gif":
				$mediatype = 'image/gif';
				break;
			case "js":
				$mediatype = 'text/javascript';
				break;	
			case "json":
				$mediatype = 'text/json';
				break;	
			case "css":
				$mediatype = 'text/css';
				break;
			case "xhtml":
				$mediatype = 'application/xhtml+xml';
				break;
			case "html":
				$mediatype = 'text/html';
				break;	
			case "otf":
				$mediatype = 'application/vnd.ms-opentype';
				break;
			case "woff":
				$mediatype = 'application/font-woff';
				break;
			case "ttf":
				$mediatype = 'application/font-ttf';
				break;
			case "ncx":
				$mediatype = 'application/x-dtbncx+xml';
				break;	
			case "txt":
				$mediatype = 'text/plain';
				break;		
			default:
				$mediatype =  null;
				
		}
		
		return $mediatype;
		
	}
	
	public function find_math_script($file){
		
			
	
		$contents = file_get_contents($file) ;
	
		$pattern = "/<math ([^>]*[^\/])>/";
		
	
		
		// search, and store all matching occurences in $matches
		if(preg_match($pattern, $contents,$match)){
			
			return true;
		   	
		   
		}
		
	}
	
	public function find_generic_script($file){
		
			
	
		$contents = file_get_contents($file) ;
	
		$pattern = "/<script ([^>]*[^\/])>/";
		
	
		
		// search, and store all matching occurences in $matches
		if(preg_match($pattern, $contents,$match)){
			
			return true;
		   	
		   
		}
		
	}
	
	public function copy_img_local($cover_path){
		
		
		$dir_class = New Dir();
		
		$_url =  $dir_class->copy_to_folder($cover_path);
		return $_url; 
		
			

		
		
	}
	
	
	
	
	
	
	
	public function reference_proj($post,$file_path) {
		
		$file_path = $file_path.'readex.xtml';
		
		
		// Aggiungo il file readme.xml
		$doc = new DomDocument('1.0', 'UTF-8');
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;

		$project = $doc->createElement("project");
		$project = $doc->appendChild($project);


		$project_id = $doc->createElement('id', $post['ebook_info']['idpers']);
		$project_name = $doc->createElement('name', $post['ebook_info']['title']);
		$project_id = $project->appendChild($project_id);
		$project_name = $project->appendChild($project_name);
		 


		$author = $doc->createElement('author');
		$author = $project->appendChild($post['ebook_info']['author']);
		


		$book = $doc->createElement('book');
		$book = $project->appendChild($book);
		$book_name = $doc->createElement('name',$post['ebook_info']['title']);
		$book_isbn = $doc->createElement('isbn',$post['ebook_info']['isbn']);
		$book->appendChild($book_name);
		$book->appendChild($book_isbn);


		$xml_string = $doc->saveXML();
		$doc->save($file_path);
		
		
	}
	
	/*****************VALIDAZIONE*********************/
	
	public function get_validation_msg($message){
		
		if($message != ''){
			
			$messageInfo = explode(',',$message);
			$messageInfo['code']  = $messageInfo[0]; 
			$messageInfo['type']  = strtolower(trim($messageInfo[1])); 
			$messageInfo['msg']   = $messageInfo[2]; 
			$messageInfo['file']  = $messageInfo[3]; 
			
			return $messageInfo;
		}
		return false;
	}
	
	
	
	public function get_spine_epub($nav){
		
		$nav_c = file_get_contents($nav);
		
		//$nav = file_get_contents($_SESSION['USER_CURRENT_ISBN_PATH']."OEBPS/Text/nav.xhtml");


		$dom = new DOMdocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($nav_c);
		libxml_use_internal_errors(false);
		//$array = array ();
		//$finder = new DomXPath($doc);
					
		$a = $dom->getElementsByTagName('a');
		
		$i = 0;
		while($alink = $a->item($i++))
		{
			$href = $alink->attributes->getNamedItem('href');
			
			if($href)
			{
				
				$hrefarray[] = strtok($href->value,'#');
					
			
			}
		}	
		
		
		
		return array_unique($hrefarray);
		
	}
	
	

	
}
