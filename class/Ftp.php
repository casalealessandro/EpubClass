<?php
ini_set('memory_limit', '-1');
set_time_limit(0);


require_once "class/Dir.php";





class Ftp {
	
	
	public $ftp_server;
	public $ftp_user;
	public $ftp_pass;
	public $conn_id;
	
	
	public function __construct($ftp_server,$ftp_user,$ftp_pass){
		
		$this->ftp_server = $ftp_server;
		$this->ftp_user   = $ftp_user;
		$this->ftp_pass   = $ftp_pass;
		
		
		
	}
	
	public function connetti_ftp(){
		
		
		$this->conn_id = ftp_connect($this->ftp_server) or die("Couldn't connect to $ftp_server"); 
		if($this->conn_id){
			 
			if(ftp_login($this->conn_id, $this->ftp_user, $this->ftp_pass)){
			  
				 return $this->conn_id; 
			}
			 
			 
		}
		 
		return false;
	}
	
	
	public function lista_file($conn_id,$cartella="./"){
		ftp_pasv($conn_id, true) or die("Passive mode failed");
		$file_list = ftp_nlist($conn_id, $cartella);
		if($file_list){
			
			return $file_list;
			//ftp_close($conn_id);
		}

		// close connection
		
		return 	false;
	}
	
	
	public function download_folder($ftp_conn,$ftp_folder,$local_folder){
		ftp_pasv($ftp_conn, true) or die("Passive mode failed");
		$ftp_folder = '/'.$ftp_folder;
		//echo $ftp_folder;
		$dir_class = new Dir();
		if ($ftp_folder !== '.') {
			if (ftp_chdir($ftp_conn, $ftp_folder) === FALSE) {
				echo 'Change dir failed: ' . $ftp_folder . PHP_EOL;
				return;
			}
			if(!$dir_class->check_is_folder($local_folder)){
				
					mkdir($local_folder, 0777, true);
					
			}
			chdir($local_folder);
		}
		
		
		$list = $this->lista_file($ftp_conn,$ftp_folder);
		
		if(is_array($list)){
		
			for($i=0; $i <  count($list); $i++) {
				
				//echo ftp_chdir($ftp_conn, $list[$i]);
				
				if (@ftp_chdir($ftp_conn, $list[$i])) {
					$sep = '/';
					$folder_name = $dir_class->foldername_from_path($list[$i],$sep );
					$new_local_folder = $local_folder.'/'.$folder_name;
					//echo 'folder:' . $folder_name.'<br />';
					$this->download_folder($ftp_conn,$list[$i],$new_local_folder);
				}else{
					$sep = '/';
					$local_file_f = $local_folder.$sep.$dir_class->foldername_from_path($list[$i],$sep );
					//echo $local_file_f;
					$file_f = $dir_class->create_file($local_file_f);
					$fp = fopen($local_file_f,"w");
					//echo 'file:'. $list[$i].'<br />';*/
					if ($fp){
						ftp_fget($ftp_conn, $fp, $list[$i], FTP_BINARY, 0);
						//ftp_fget($ftp_conn, $fp, $list[$i], FTP_ASCII, 0);
					}
					
				}
			}	
			
		}
		
		
	}
	
	public function get_url_cover($manifest){
		
		$url = '';
		for($x=0; $x<= count($manifest->item); $x++){
			//echo $manifest->item[$x]['properties'].' == '.$cover.'<br />';
			if(isset($manifest->item[$x]['properties']) && (string)$manifest->item[$x]['properties'] === 'cover-image'  ){
				
				$url =  $manifest->item[$x]['href'];
				//move_uploaded_file()
			}
		}
		
		
		return $url;
		
	}
	
	public function elabora_content(){
		
		$root = $this->epub_path .'OEBPS/';
		
		//echo $root;
		
		$dir_class = new Dir();
		
		$array_dir = $dir_class->scan_folder($root);
		
		
		$array_files['post'] = $_POST;
		
		
	
		foreach($array_dir as $dir){
		
			if($dir != '.' && $dir != '..'  &&  $dir != ''){
				
				$path = $root.DIRECTORY_SEPARATOR.$dir;
				$key = str_replace('.','',$dir); 
				if($dir_class->check_is_folder($path)){
					
					$array_files[$key] = $dir_class->scan_dir($path);
					
				}else{
					$array_files[$key] = $path;
					
				}

				
			}
			
		
		}
		
		
			
		$this->costruisci_xml_file($array_files);
		
		
	}
	
	public function costruisci_xml_file($a_files){
		
		
		$root = $this->epub_path .'OEBPS/';
		
		$dir_class = new Dir();
		
		$filePath = 'content.opf';

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
		$identifier  = $dom->createElement('dc:identifier', 'urn:isbn:'.$a_files['post']['isbn']);
		$identifier->setAttribute('id', 'bookuid');
		
		$meta  = $dom->createElement('meta', '15');
		$meta->setAttribute('refines', '#bookuid');
		$meta->setAttribute('property', 'identifier-type');
		$meta->setAttribute('scheme', 'onix:codelist5');
		$title = $dom->createElement('dc:title', $a_files['post']['title']);
        $creator = $dom->createElement('dc:creator', $a_files['post']['author']);
		
		$publisher = $dom->createElement('dc:publisher', $a_files['post']['rights']);
        $language = $dom->createElement('dc:language', 'it');
        $rights = $dom->createElement('dc:rights', 'Copyright &#x00A9;  '.$this->year . ' '. $a_files['post']['rights']);
		$meta_name  = $dom->createElement('meta');
		$meta_name->setAttribute('name', 'sugarcube');
		$meta_name->setAttribute('content', '0.01');
		$meta_property = $dom->createElement('meta', $this->date_EN);
		$meta_name->setAttribute('property', 'dcterms:modified');
        $meta_cover = $dom->createElement('meta');
        $meta_cover->setAttribute('name', 'cover');
        $meta_cover->setAttribute('content', 'cover-image');
       
		
		
		
		
		
		
		$metadata->appendChild($identifier);
		$metadata->appendChild($meta);
		$metadata->appendChild($title);
		$metadata->appendChild($creator);
		$metadata->appendChild($publisher);
		$metadata->appendChild($language);
		$metadata->appendChild($rights);
		$metadata->appendChild($meta_name);
		$metadata->appendChild($meta_property);
		$metadata->appendChild($meta_cover);
		
		$package->appendChild($metadata);
		$manifest = $dom->createElement('manifest');
		
		
		
		$x = 0;
		
		foreach($a_files  as $key=> $files_p){
			
		
			
			
			if(is_array($files_p)){
				$id_prec = '';
				foreach($files_p as $k=> $file_p){
					
					
					
					
					
					$file_p = str_replace('\\','/',$file_p );
					$path_ebook =  str_replace($root,'',$file_p );
					//echo $path_ebook;
					$file_name  =  $dir_class->file_name_from_path($file_p);
					$mediatype  =  $this->epub_content_mediatype($file_p);
					$property   =  $this->epub_content_property($file_p);
					$item = $dom->createElement('item');
					$id = preg_replace('/.\w+$/','',$file_name);
					$m_dir =  $dir_class->folder_name($root,$file_name);
					
					/***QUESTO SERVE PER GENERARE IN SEGUITO LO SPINE DELL EBOOK ED OTTENERE UN ID CHE SIA VALIDO SOLO PER LA CARTELLA TEXT PER POI DOPO PER LEGGERE SEMPLICEMENTE IL NAV***/
					if((string)$m_dir === 'Text' ){
						
						$id = $id;
						
					}else{
						
						$id = $k.'_'.$id;
					}
						
					$item->setAttribute('id', $id );
					$item->setAttribute('href',$path_ebook );
					$item->setAttribute('media-type', $mediatype);
					
					if($property != ''){
						
						$item->setAttribute('properties', $property);
						
					}
				   
				    $id_prec = $id;
					$manifest->appendChild($item);
					$x++;
				}
				
			}
			
		}
		$package->appendChild($manifest);
		$spine           = $dom->createElement('spine' );
		$spine->setAttribute('toc', 'toc_ncx');
		
		$nav_p = $dir_class->check_is_folder($this->epub_path .'OEBPS/epublishare/layers/nav.html');
		
		if($nav_p == ''){
			
			$nav = file_get_contents($this->epub_path .'OEBPS/Text/nav.xhtml');
		}else{
			
			$nav = file_get_contents($this->epub_path .'OEBPS/epublishare/layers/nav.html');
		}
		
		
		$doc = new DOMDocument();
		libxml_use_internal_errors(true);
		$doc->loadHTML($nav);
		libxml_clear_errors();
		$nav_toc_custom = $doc->getElementsByTagName('a');
		foreach ($nav_toc_custom as $nav_field) {
			$filed =  $nav_field->getAttribute('href').'<br />';
			$filed = preg_replace('/.xhtml.*/', '', $filed);
			$itemref = $dom->createElement('itemref');
			$itemref->setAttribute('id',$filed );
			$spine->appendChild($itemref);
		}
		
		
		$package->appendChild($spine);
	
		$dom->appendChild($package);
		if($dom->save($filePath)){
		
			echo 'FATTO';
		}else{
			
			echo 'ERRORE!!  contatta webmastercasae@gmail.com';
		};
	
	}
	
	private function epub_content_property($file_path){
		
		
		$file_info =  pathinfo($file_path);
		$extension = $file_info['extension'];
		

		
		switch ($extension) {
			
			
			case "js":
				$property = 'text/javascript';
				break;	
			case "json":
				$property = 'text/json';
				break;	
			case "css":
				$property = 'text/css';
				break;
			case "xhtml":
			
				$math  = $this->find_math_script($file_path);
				if($math){
					
					$math_prop = 'mathml';
					
				}else{
					
					$math_prop = '';
					
				} 
				
				$property = 'scripted '. $math_prop;
				break;
			case "html":
				$property = 'scripted'; 
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
				$mediatype = 'text/js';
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
				$mediatype = 'application/html';
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
			default:
				$mediatype =  'application/'.$extension;
				
		}
		
		return $mediatype;
		
	}
	
	public function find_math_script($file){
		
			
	
		$contents = file_get_contents($file);
	
		$pattern = "/<math ([^>]*[^\/])>/";
		
	
		
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
	
}