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
	
	
	
}
