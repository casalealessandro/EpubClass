<?php 

	class Dir{
		
		
		public $local_path;
		public $upload_path;
		
		public function __construct(){
		
			$this->local_path = _PATH_;
			$this->upload_path = $this->local_path.'/epub/';
			
			
		}
		
		public function scan_folder($dir){
			
			$dirs = scandir($dir,0);
			$arr_dirs = array();
			
			
			for($x=0; $x < count($dirs); $x++){
				
				
				if($this->check_is_folder($dir.DIRECTORY_SEPARATOR .$dirs[$x])){
					if($dirs[$x] != "." && $dirs[$x] != "..") {
						$arr_dirs['folder'][$x] = $dirs[$x];
					}
						
				
				}else{
					
						
					$arr_dirs['files'][$x] = $dirs[$x];
						
					
				}
			 
				
			}
			return $arr_dirs;
		}
		
		
		public function scan_dir($dir, &$results = array()){
			
			
			if(!is_file($dir)){
				
				$files = scandir($dir);
				
				foreach($files as $key => $value){
					if($value != ''){
						$path = realpath($dir. DIRECTORY_SEPARATOR .$value);
						if(!is_dir($path)) {
							$results[] = $path;
						} else if($value != "." && $value != "..") {
							$this->scan_dir($path, $results);
							//$results[] = $path;
						}
					}
				}
						
				
						
				return $results;	
			}		
					
			
		
		
		}
		
		
		
		public static function check_is_folder($folder_path){
			
			if(is_dir($folder_path)){
				
				return true;
								
			}	
			return  false;
		}
		
		
		public function file_name_from_path($path){
			
			
			$file = basename($path);         
			
			return $file;
			
		}
		
		/*****RESTITUISCE IL NOME DELLA CARTELLA IN BASE AL PERCORSO DEL FILE*****/
		
		
		public function folder_name($root,$path_file){
			//echo $root .','.$path_file;
			$dir = dirname($path_file);
			$m_dir = str_replace($root,'',$dir);
			if($m_dir){
				
				return $m_dir;
				
			}
			return false;
		}
		
		/***SPOSTA E RINOMINA****/
		
		public function copy_to_folder($path_file,$destination=''){
			
			//echo $path_file;
			if($destination != ''){
				
				$this->upload_path = $destination;
				
			}
			
			$file_name = $this->file_name_from_path($path_file);
			$new_filepath = $this->upload_path.'/'.$file_name;
			//$new_folder = $this->upload_path.$this->foldername_from_path($new_filepath,'/');
			
		
			
			//SE IL FILE ESISTE ALLORA LO COPIO ANZI SCARICO IL FILE
			if (!file_exists($new_filepath)){
				if(!copy($path_file, $new_filepath)){
					
					return false;
					
				}
				return true;
			}
				
			
			
			
			return false;
		}
		
		
		public function unzip($file_zip,$destination=''){
			
			// assuming file.zip is in the same directory as the executing script.
			
			//echo $file_zip;
			$zip = new ZipArchive;
			$res = $zip->open($file_zip);
			if ($res === TRUE) {
					
				$zip->extractTo($destination);
			
				$zip->close();
				//unlink($file_zip);
				return true;	
			} else {
				return false;
			}		
				
			
			
			
			
			
		}
	
	
		public function create_zip($name,$rootPath){
			
			$excludes = array('.DS_Store', 'mimetype','Epub.zip','.','..');

			$rootPath = str_replace('/',DIRECTORY_SEPARATOR,$rootPath );
			//$mimetype = $rootPath.'mimetype';
			/*$zip->addFromString('container.xml', file_get_contents($meta_inf));*/
			include_once(_PATH_."/librerie/Zip.php");
			echo $rootPath.'<br />';
			$zip = new Zip();
			$zip->setZipFile($name);
			
			$zip->addFile("application/epub+zip", 'mimetype');

			/*if ($zip->open($name, ZipArchive::CREATE) != true) {
				throw new Exception("Unable open archive '$name'");
			}
			*/
			
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::SELF_FIRST);

			foreach ($files as  $file)
			{
				
				if (in_array(basename($file), $excludes)) {
					continue;
				}
				
		
				$fileName = str_replace($rootPath,'',$file);
				if (is_dir($file)){
					echo $fileName.'<br />';
					$zip->addDirectory($fileName);
				}elseif (is_file($file)) {
					$fileData =	file_get_contents($file);
					
					
					
					//$fileName = str_replace($rootPath,'',$file);
					//echo $fileName.'<br />';
					
					$zip->addFile($fileData, $fileName);
					
				}
				
				
				
			}
			$zip->finalize();
			
			
			return true;
		}
		
		public function create_zip_folder($name,$rootPath){
			
			$zip_excluded = str_replace($rootPath,'',$name);
			$excludes = array('.','..',$zip_excluded);
			$rootPath = str_replace('/',DIRECTORY_SEPARATOR,$rootPath );
			
			include_once(_PATH_."/librerie/Zip.php");
			$zip = new Zip();
			$zip->setZipFile($name);
				

				
			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::SELF_FIRST);
			
			
			
			
			
			 			
			foreach ($files as  $file)
			{
				
				if (in_array(basename($file), $excludes)) {
					continue;
				}
				
		
				$fileName = str_replace($rootPath,'',$file);
				
				if (is_dir($file)){
					$zip->addDirectory($fileName);
				}elseif (is_file($file)) {
					$fileData =	file_get_contents($file);
					$zip->addFile($fileData, $fileName);
					
				}
				
				
				
			}
			if($zip->finalize()){
				
				return true;
				
			}
			return false; 		
			
		}
		
	
	
		/***NOME DELLA CARTELLA DAL PATH ASSOLUTO**/
		
		public function foldername_from_path($path, $separetor){
			
			///VEDO PRIMA SE IL PATH E' RIFERITO AD UN FILE COSI GLI RIMUOVO L'ESTENSIONE. 
			
			$extension;
			if(is_file($path)){
				
				$file_parts = pathinfo($path);
				$extension = '.'.$file_parts['extension'];
				$path = str_replace($extension,'',$path);
			}
			
			
			
			
			$array_path = explode($separetor,(string)$path);
			
			if(is_array($array_path)){
				
				$num_array = count($array_path) -1;
				
				
				return $array_path[$num_array];
			
			}
			
			return false;
			
		}
	
		/***CREA FILE IN LOCALE***/
		
		public function create_file($file_path){
			
			//echo $file_path;
			$path = $file_path;
			$file = fopen($path,"w");
			 
			if(fwrite($file,"")){
				
				fclose($file);
				return true;
			}
			
			//fclose($file);
			return false;  
		}
	
		/***CREA UNA CARTELLA IN LOCALE***/
		
		public function create_folder($folder_path){
			
			
			$dir = $folder_path;;

			
			if(is_dir($dir) === false )
			{
				mkdir($dir);
				return true;
			}
			
			return false;
			

			
			
			
		}
	
	
		public function delete_folder($path_folder){
			
			
			$it = new RecursiveDirectoryIterator($path_folder, RecursiveDirectoryIterator::SKIP_DOTS);
			$files = new RecursiveIteratorIterator($it,RecursiveIteratorIterator::CHILD_FIRST);
			
			foreach($files as $file) {
				if ($file->isDir()){
					rmdir($file->getRealPath());
				}else{
					unlink($file->getRealPath());
				}
			}
			if(rmdir($path_folder)){
				
				return true;
				
			}
			
			return false;
		}
		
		
		
	}

	
	

?>