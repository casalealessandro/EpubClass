<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

  
			<div class="col-lg-12">
				<div class="row">
					
					<ul class="folder-list-no-prev ebooks-list">
					<?php 
						
						
						
						
						
						if(isset($array_serve_folder['folder'])){
					
							foreach($array_serve_folder['folder'] as  $folder){
								
								
									echo '<li><span class="file_image"><a href="'._BASE_URL_.'index.php/concorsi/?folder_name='.urlencode($current_folder).'/'.urlencode($folder).' " ><img src="'._BASE_URL_.'/assets/img/folder-icon.png" /></span><span class="file_name">'. $folder.'</a></span></li>';
								
									
								
							}
						}
						if(isset($array_serve_folder['files'])){
						
							foreach($array_serve_folder['files'] as  $file){
								
									$info = pathinfo($file);
									
									
									if(isset($info['extension']) && $info['extension'] == _EXTENSION ){
										echo '<li><a href="'._BASE_URL_.'index.php/epub/file/?file_name='.urlencode($current_folder).'/'.$file.' " ><img src="'._BASE_URL_.'/assets/img/file-icon.png" /></span><span class="file_name"><span class=""> '. $file.'</span> Apri</a></li>';
								
									}
									
									if($info['filename'] == 'mimetype'){
										
										echo  '<li><span class="file_image"><img src="'._BASE_URL_.'/assets/img/file-icon.png" /></span><span class="file_name">'. $file.'</span></li>';
									}
									
									
							}
						}
					?>
					</ul>
				</div>
			</div>
			
	</div>
		
</div>

 </body>
</html>