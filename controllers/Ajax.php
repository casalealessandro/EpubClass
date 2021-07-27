<?php 



require_once("class/Epub.php");
require_once("class/Dir.php");
require_once("class/Ftp.php");
require_once("librerie/Edigita.php");


class ControllerAjax{
	
	public $server_path;
	public $epub_path;
	
	
	function __construct(){
		
		$this->server_path = '//192.168.0.3\Concorsi\lavori\Conversioni EPUB\concorsi\Ebook';
		
		
	}
	
	
	public function init(){
		
		if(isset($_POST['epub_folder_path']) && isset($_POST['epub_folder_name'])){
			$epub_class = new Epub($_POST['epub_folder_path'],$_POST['epub_folder_name']);
			
		}	
		$dir_class = new Dir();
		
		
		if($_POST['action'] == 'crea-epub'){
		
			
			
			$ftp  = new ftp(FTP_SERVER,FTP_USER,FTP_PASS);
			
			
			$ftp_conn = $ftp->connetti_ftp();
			if($ftp_conn){
				
				$ftp_folder = $_POST['isbn'].'/'.$_POST['personalizzazione'].'/Temp';
				$local_folder_epublishare = $_POST['epub_folder_path'].'OEBPS/epublishare';
				$local_global_folder = $_POST['epub_folder_path'].'OEBPS';
				$local_folder_JS = $_POST['epub_folder_path'].'OEBPS/Misc';
				$local_folder_CSS = $_POST['epub_folder_path'].'OEBPS/Styles';
				$local_folder_images = $_POST['epub_folder_path'].'OEBPS/Images';
				$local_folder_text = $_POST['epub_folder_path'].'OEBPS/Text/';
				$local_folder_simulatore = $_POST['epub_folder_path'].'OEBPS/Text/simulatore_epub/';
				$update_views = _PATH_. '/uploads/viewer.zip';
				$update_JS = _PATH_. '/uploads/Misc.zip';
				$update_f_accedi     = _PATH_. '/uploads/accedi.zip';
				$update_f_styles     = _PATH_. '/uploads/Styles.zip';
				$update_f_intro      = _PATH_.'/uploads/intro.xhtml';
				$update_f_epub       = _PATH_.'/uploads/Epub.zip';
				$update_f_simulatore = _PATH_.'/uploads/simulatore_epub.zip';
				
				if(!$dir_class->check_is_folder($local_folder_epublishare)){
						
						mkdir($local_folder_epublishare, 0777, true);
						//$ex_folder = $info['dirname'].'/'.$info['filename'];
				}
				
				
				$ftp->download_folder($ftp_conn,$ftp_folder,$local_folder_epublishare);
				
				
				
				if(!$dir_class->unzip($update_views,$local_folder_epublishare)){
					echo 'Errore nel estrarre la cartella '. $update_views;
					
					return false;
					
				}
				
				if(!$dir_class->unzip($update_JS,$local_global_folder)){
					echo 'Errore nel estrarre la cartella '. $update_JS;
					return false;
					
				}
				if(!$dir_class->unzip($update_f_accedi,$local_folder_images)){
					echo 'Errore nel estrarre la cartella '. $update_f_accedi;
					return false;
				}
				
				if(!$dir_class->unzip($update_f_styles,$local_global_folder)){
					echo 'Errore nel estrarre la cartella '. $update_f_styles;
					return false;
					
				}
				
				if(!file_exists($local_folder_text.'intro.xhtml')){
					
					if(!$dir_class->copy_to_folder($update_f_intro,$local_folder_text)){
						echo 'Errore nel copiare il file '. $update_f_intro;
						
					}
					
				}
				if(!$dir_class->unzip($update_f_simulatore,$local_folder_text)){
						
					
					echo 'Errore nel estrarre la cartella '. $update_f_simulatore;
						
					
				}
				
				$nav_custom = $local_folder_epublishare.'/layers/nav.xhtml';
				$epublishare_layers = $local_folder_epublishare.'/layers/';
				
				
				$epub_class->update_nav_epublishare($nav_custom,$epublishare_layers);
				
				
				
			}
			
		
			//$epub_class->elabora_content();
		
		
		}
		if($_POST['action'] == 'crea_epub_normale'){
			
			
			
			$_POST['ebook_info']['cover']  = $_POST['cover']; 
			$_POST['ebook_info']['nav']    = $_POST['nav'];    
			$_POST['ebook_info']['isbn']   = $_POST['isbn'];    
			$_POST['ebook_info']['author'] = $_POST['author'];
			$_POST['ebook_info']['title' ] = $_POST['title' ];
			$_POST['ebook_info']['rights'] = $_POST['rights'];
			//$_POST['ebook_info']['idpers'] = $_POST['personalizzazione'];
			//$_POST['ebook_info']['cover_path'] = $_POST['cover_path'];
				
				
				
				
			$epub_class->elabora_content($_POST['ebook_info']);
			
			$update_f_epub  = _PATH_.'/uploads/Epub.zip';
			$local_file_epub = $_POST['epub_folder_path'].'Epub.zip';
			if($dir_class->create_zip($local_file_epub,$_POST['epub_folder_path'])){
				$newname = $_POST['epub_folder_path']. $_POST['epub_folder_name']. '.epub';
				rename($local_file_epub, $newname);
				
				
			}

			
				/*if($dir_class->copy_to_folder($update_f_epub,$_POST['epub_folder_path'])){
					$local_file_epub = $_POST['epub_folder_path'].'Epub.zip';
				
					
				}
			*/
			
		}
		
		if($_POST['action'] == 'valida'){
			
			
			$validate_path = _PATH_.'/librerie/epubcheck/epubcheck.jar';
			$epub_folder   = $_POST['epub_folder_path'].$_POST['epub_folder_name'].'.epub';
			$file_output = _PATH_. '/assets/validazioni/report.xml';
			
			$cmd = "java -jar ".$validate_path ." -out  ". $file_output . " " . $epub_folder;
			
			if(shell_exec($cmd)){
				
				$report_xml = simplexml_load_file($file_output);
				
				
				$messages = $report_xml->repInfo->messages->message;
				
				$html = '';
				
				/*['code']
				['type']
				['msg'] 
				['file']
				**/
				if(isset($messages)|| !empty($messages)){
					
					$x=0;
					foreach($messages as $message){
						$msg = $epub_class->get_validation_msg($message);
						if($msg['type'] == 'warn'){
							$msg['type'] = 'warning';
							
						}
						$html .= '<p><i class="material-icons">'.$msg['type'].'</i>'. $msg['msg'].' - ' . $msg['file'].'</p>';
						$x++;
					}
					echo $html;
				}
			}
			
		}	
		
		if($_POST['action'] == 'ind_epub'){
			
			$epub_folder   = $_POST['epub_folder_path'];
			
			$epub_class->convert_xhtml_file($epub_folder);
			
			
		}
		
		if($_POST['action'] == 'remove_pers'){
			
			
			$local_folder_epublishare = $_POST['epub_folder_path'].'OEBPS/epublishare';		
			if($dir_class->delete_folder($local_folder_epublishare)){
				
				echo 'ok';		
				
			}
			
			
		}
		
		if($_POST['action'] == 'get-cantook-upload'){
			
			$libraryclass = new Edigita();
			$isbn  = $_POST['isbn'];
			$libraryclass->get_ebook_uploaded($isbn);
			
			if($libraryclass->get_ebook_uploaded($isbn)){
				
				echo 'in edigita';
			
			}else{
				
				echo 'No in edigita ';
			}
			
		}
		
		if($_POST['action'] == 'sync-magento'){
			
			$libraryclass = new Edigita();
			$isbn  = $_POST['isbn'];
			$ebook = $libraryclass->get_ebook_uploaded_detail($isbn);
			
			echo '<pre>';
			
			print_r($ebook);
			
			echo '</pre>';
			
			
			
			
		}	
			
		if($_POST['action'] == 'scan-epub-file'){
			
			$epub_class = new Epub();
			$dir_class = new Dir();
			$path = $_POST['epub_path'];
			$c_nav_url = $path . $_POST['nav_name'];
			$nav_file = $epub_class->get_spine_epub($c_nav_url);
			$array_serve_folder = $dir_class->scan_folder($path);
			$class_cap = $_POST['class_cap'];
			$class_cap_numero = $_POST['class_cap_numero'];
			$class_cap_paragrafo = $_POST['class_cap_paragrafo'];
			
			$upload_nav =_BASE_URL_.'uploads/nav.xhtml';
			$nav_nodes = '';
			
			$css_path = $path . 'css';
			
			
			$upload_temp_css = _PATH_ . 'uploads/custom.css';
			
			
			
			$epub_class->split_chapters($nav_file,$class_cap_numero,$_POST['epub_path'],$_POST['epub_url']);
			
			
			
			/*$dir_class->copy_to_folder($upload_temp_css,$css_path);

			
			
						
			foreach($nav_file as $key=> $file){
				
				$capHtml   = '';
				$epub_url  = $_POST['epub_url']. $file;
				$epub_path = $_POST['epub_path'] .$file;
			
		
			
				
				$html = file_get_contents($epub_url);
				
				$dom = new DOMdocument();
				libxml_use_internal_errors(true);
				$dom->loadHTML($html);
				libxml_use_internal_errors(false);
					
					
				$par = $dom->getElementsByTagName('*');
				
				$link_s = $dom->getElementsByTagName('link');
				$link_s_v = false;
				
				foreach($link_s as $link){
					
					$attr = $link->getAttribute('href');
					
					if ($attr == 'css/custom.css') {
						$link_s_v = true;
					}
					
				}
				
				if(!$link_s_v){
					
					$html_head 		= $dom->getElementsByTagName('head');
					
					
					$style_linked 	= $dom->createElement('link');	
					$style_linked->setAttribute('href','css/custom.css');
					$style_linked->setAttribute('rel', 'stylesheet');
					$style_linked->setAttribute('type', 'text/css');
					
					$html_head[0]->appendChild($style_linked);
					
				}
				$i = 0;
				$capitoloNum = 0;
				$paraNum =0;
				
				while($p = $par->item($i++))
				{
					$class_node = $p->attributes->getNamedItem('class');
					
					$capHtml .= $dom->saveHTML($p);
					
					if($class_node)
					{
						if(preg_match('~\b('.$class_cap.')\b~',$p->attributes->getNamedItem('class')->value))
						{
							
							
							$capitoloNum++;
						
							$p->setAttribute('id','capitolo_'.$capitoloNum);
							$array_nav[$key][$capitoloNum]['titolo'] 	= $p->nodeValue;
							$array_nav[$key][$capitoloNum]['id'] 		= 'capitolo_'.$capitoloNum;
							$array_nav[$key][$capitoloNum]['file'] 		= $file;
							
							$titolo_capitolo = trim($p->nodeValue);
								
							
							
						}elseif(preg_match('~\b('.$class_cap_paragrafo.')\b~',$p->attributes->getNamedItem('class')->value)){
							
							$paraNum++;
						
							
							$p->setAttribute('id','paragrafo_'.$paraNum);
							$array_nav[$key][$capitoloNum]['paragrafo'][$paraNum]['titolo'] 	= $p->nodeValue;
							$array_nav[$key][$capitoloNum]['paragrafo'][$paraNum]['id']  		= 'paragrafo_'.$paraNum;
							$array_nav[$key][$capitoloNum]['paragrafo'][$paraNum]['file']		= $file;
						}
						
						
						
					}
					
					
					
				}	
				
				$options = array(
					
					'output-xhtml' => true,
					'clean' => true, 
					'wrap-php' => false, 
					'indent-with-tabs' => true,
					'indent' => true,
					
					'show-body-only' =>false,
					'fix-backslash' => true,
					'quote-marks' => true,
					'wrap' => 1024,
					'bare' => true
				
				);
				$tidy = new tidy();
				$html_content = $dom->saveHTML();
				$html_content = $tidy->repairString($html_content,$options);
				$tidy->cleanRepair();
				
				$html_file_epub = fopen($epub_path, "w+") or die("Unable to open file!");
				
				fwrite($html_file_epub, $html_content);
				
				
				fclose($html_file_epub);
				
			}
						
			if($epub_class->create_nav($array_nav,$upload_nav,$c_nav_url)){
				
				echo 'OK';
				
			}*/
		}
		
		if($_POST['action'] == 'save-epub-file'){
			$epub_class = new Epub();
			$epub_path 		= $_POST['epub_path'];
			$epub_url 		= "http://localhost/edisesepub/epub/9788836222674/OEBPS/";
			$file      		= $_POST['file'];
			$header      	= $_POST['header'];
			$container      = json_decode($_POST['container']);
				
			$upload_temp_xhtml   = _PATH_.'uploads/temp.xhtml';
			
			$container = str_replace("<link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css\">",'',$container);
	
			$container = str_replace("<link rel=\"stylesheet\" href=\"http://192.168.0.11/edisesepub/assets/iframe-editor/inspector.css\">",'',$container);
			$container = str_replace("<link rel=\"stylesheet\" href=\"http://192.168.0.11/edisesepub/assets/iframe-editor/style-inspector.css\">",'',$container);
			$container = str_replace("<script src=\"http://192.168.0.11/edisesepub/assets/js/jquery.min.js\"></script>",'',$container);
			$container = str_replace("<script src=\"http://192.168.0.11/edisesepub/assets/js/librerie-js/bootstrap-select/js/bootstrap-select.min.js\"></script>",'',$container);
			$container = str_replace("<script rel=\"stylesheet\" src=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.bundle.min.js\"></script> ",'',$container);
			$container = str_replace("<script src=\"http://192.168.0.11/edisesepub/assets/iframe-editor/editor-iframe.js\"></script>",'',$container);
			$container = str_replace("http://localhost/edisesepub/assets/iframe-editor/",'/css/',$container);
			
			$container = str_replace($epub_url,'',$container);
			
			
			
			$temp_file = $epub_class->create_file_epub_xhtml($epub_path,$file,$container,$header,$upload_temp_xhtml);
			if($temp_file !=''){
				
				echo $temp_file;
				
			}
			
	
			
			
		}
		
		if($_POST['action'] == 'save-onix'){
			
			
			$libraryclass = new Edigita();
			
			$libraryclass->create_onix($_POST);
			$isbn = $_POST['isbn'];
			$target_dir = _PATH_.'edigita/' .$isbn. '/';
			
			$target_file = $target_dir . $_FILES["ebook"]["name"];

			$folder_name = $_FILES["ebook"]["name"];
			
			
			
			$filePath = $target_dir. $isbn.'.xml';
			
				
			if (move_uploaded_file($_FILES["ebook"]["tmp_name"], $target_file)) {
				
				$filename=$_FILES["ebook"]['name'];
				
				$newname = $target_dir .  $isbn.'.epub';
				
				
				rename($target_file, $newname);
				
				$filezip_name = $target_dir . $isbn.'.zip';
				
				if($dir_class->create_zip_folder($filezip_name,$target_dir)){
					echo 'ok';
					$fields['isbn_old'] = $_POST['isbn_old'];
					$fields['isbn'] 	= $isbn;
					$fields['status'] 	= '1';
					//echo 'ko';
					/*if($this->db_epub->insert_ebook_imported($fields)){
						echo 'ko';
						//header('location:' . $_SERVER["HTTP_REFERER"]. '&msg=completato');
					}*/ 
					
					//header('location:' . $_SERVER["HTTP_REFERER"]. '&msg=completato');
				}
				
				
				
			} 
			
		}	
		
			
	}
	
	private function arraySort($array_nav){
		
		foreach($array_nav as $input){
			
			foreach ($input as $key=>$val) $output[$val['file']][]=$val;
			return $output;
		}
		
		
	}

	
	
	public function upload(){
		
	
		if($_FILES['file']['error'] == 0 ){
			
			if(isset($_FILES['file'])) {
				if($_FILES['file']['size'] <= 73400320) { //74 MB (size is also in bytes)
					
					if(move_uploaded_file($_FILES['file']['tmp_name'],'epub/' . $_FILES['file']['name'])){
						$html['msg']['type'] ='0';
						$html['msg']['text'] ='';
						
					}
				}else{
					$html['msg']['type'] = '1';
					$html['msg']['text'] = 'Il file è troppo grande caricalo manualmente in questo percorso:' . _PATH_ . 'epub/';
					
				}
			}
		}else{
			
			$html['msg']['type'] = '2';
			$html['msg']['text'] = 'c&apos;è un errore carica il file manualmente in questo percorso:' . _PATH_ . 'epub/ ';
		}
		echo json_encode($html);
	}
	
	
	public function openModalEbook(){
		
		$folder_name = '';
		$epub_folder_name = '';
		
		
		if(isset($_POST['epub_folder_name']) &&  $_POST['epub_folder_name'] != ''){
			
			$folder_name =  $_POST['epub_folder_name'];
			//$path = $this->epub_path.$folder_name;
		}
		
		if(isset($_POST['epub_current_folder_name']) && $_POST['epub_current_folder_name']!= '' ){
			
			$folder_name =  $_POST['epub_current_folder_name'] . DIRECTORY_SEPARATOR . $folder_name;
			//$folder_name =  str_replace($this->server_path,'',$folder_name);
			$path =  $folder_name;
			
		}
		
		
		
		$dir_class = new Dir();
		$array_serve_folder = $dir_class->scan_folder($path);
		$array_serve_folder['current_folder'] = $path;
		
		
		//$array_serve_folder = $this->_array_folder($path);
		
		echo json_encode($array_serve_folder);
		
	}
	
	
	
}

?>