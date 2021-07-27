<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

  
			<div class="col-lg-12">
				<div class="row">
					<form method="post" enctype="multipart/form-data"  >
					  <input type="file" name="file_epub" id="file_epub" style="display:none"/>
					  <!--<button type="button" id="upload" class="btn btn-primary">Carica qui l'ebook</button>-->
					</form>
					
					<!-- Drag and Drop container-->
					<div class="upload-area"  id="uploadfile">
						
						
						<div class="box_input">
						<svg class="box_icon"  width="50" height="43" viewBox="0 0 50 43"><path d="M48.4 26.5c-.9 0-1.7.7-1.7 1.7v11.6h-43.3v-11.6c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v13.2c0 .9.7 1.7 1.7 1.7h46.7c.9 0 1.7-.7 1.7-1.7v-13.2c0-1-.7-1.7-1.7-1.7zm-24.5 6.1c.3.3.8.5 1.2.5.4 0 .9-.2 1.2-.5l10-11.6c.7-.7.7-1.7 0-2.4s-1.7-.7-2.4 0l-7.1 8.3v-25.3c0-.9-.7-1.7-1.7-1.7s-1.7.7-1.7 1.7v25.3l-7.1-8.3c-.7-.7-1.7-.7-2.4 0s-.7 1.7 0 2.4l10 11.6z"></path></svg>
						
							<label for="file"><strong>Scegli il file epub</strong><span class="box_dragndrop"> oppure trascinalo qui</span>.</label>
						
						</div>
						
						
						
					</div>
					
					
					<div id="progress_bar_content" class="progress-bar progress-bar-striped hide">
					
						<div id="progress_bar_epub" class="progress-bar progress-bar-success progress-bar-striped">
							  <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
						</div>
					</div>
					
					<ul class="folder-list ebooks-list">
					<?php 
						
					
						
						if(isset($array_serve_folder['folder'])){
					
							foreach($array_serve_folder['folder'] as  $folder){
								
								
									echo '<li><span class="file_image"><a href="'._BASE_URL_.'index.php/epub/file?file_name='.urlencode($current_folder).'/'.urlencode($folder).' " ><img src="'._BASE_URL_.'/assets/img/folder-icon.png" /></span><span class="file_name">'. $folder.'</a></span></li>';
								
									
								
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