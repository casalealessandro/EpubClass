<?php

	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>


	

	
	
	
	
	
  <body>

<div class="overload">
	<div class="loader"></div>
</div>

<div id="report_container" data-fancybox="" data-type="iframe" class="fullscreen report_container fancybox">
	<div id="report">
		
	
	</div>
</div>
<div id="content" class="container-fluid">
	<div class="row">
		<div  class="col-lg-12">
			
			
			
		</div>	
			<div class="col-lg-12 ebook-info-container">
				<div class="row">
					<div class="col-xs-12 col-md-4">
						
						
						<img src="<?php  echo $info_epub['cover_url'] ?>" />	
					</div>
					<div class="col-xs-12 col-md-8 epub_info" >
						<div class="row">
							<div class="col-md-12">
								
									<h3>Informazioni libro</h3>
								
							</div>
							<div class="col-2">
								<label for="isbn" >ISBN</label>
							</div>
							<div class="col-10">
								<input type="text" id="isbn"   name="isbn"	value="<?php  echo $info_epub['isbn']?>" class="form-control-plaintext">
								
							</div>
						</div>
						<div class="row">
							<div class="col-2">
								<label for="author">Autore</label>
							</div>
							<div class="col-10">
								<input id="author"  type="text"  name="author"	value="<?php  echo $info_epub['creator']?>"  class="form-control-plaintext">
							</div>
						</div>
						<div class="row">
							<div class="col-2">
								<label for="title">Titolo</label>
							</div>
							<div class="col-10">
								<input id="title"   type="text"  name="title"	value="<?php  echo $info_epub['title']?>"  class="form-control-plaintext">
							</div>	
						</div>
						<div class="row">
							<div class="col-2"><label for="copy">Copyright</label></div>
							<div class="col-10">
								<input id="copy"    type="text"  name="copy"	value="<?php  echo $info_epub['publisher']?>" class="form-control-plaintext">
							</div>
						</div>
						<div class="row">
							<div class="col-2">
								<label for="personalizzazioni">Seleziona Persoalizzazione</label>
							</div>
							<div class="col-10">
								<?php 
								
									if($personalizzazione){
										
										echo '<input type="button" id="remove_pers" name="remove_pers" value="Elimina personalizzazione">';
										
									}else{
									
									
									?>
										<select id="id_personalizzione" name="personalizzioni">
										
											<option value="0">Seleziona Persoalizzazione </option>
											<?php 
												for($x=0; $x< count($catella_personalizzazioni); $x++){
													
													$personalizzione = str_replace($info_epub['isbn'].'/','',$catella_personalizzazioni[$x]);
													
													echo '<option value="'.$personalizzione.'">'. $personalizzione .'</option>';
												} 
											
											?>
										
										</select>
								<?php } ?>
							</div>
						</div>
						<div class="button_wrap">
							<input type="hidden" id="epub_folder_path" name="epub_folder_path" value="<?php echo $epub_folder_path; ?>">
							<input type="hidden" id="epub_folder_name" name="epub_folder_name" value="<?php echo $epub_folder_name; ?>">
							<input type="hidden" id="cover" name="cover" value="<?php echo $info_epub['cover']; ?>">
							<input type="hidden" id="nav" name="nav" value="<?php echo $info_epub['nav_href']; ?>">
							<input id="ebook" type="button"   class="btn-primary btn" value="Crea ebook content">
							<input id="ebook_personalizzato" type="button" class="btn-primary btn" value="Scarica la personalizzione">
							
							
							<input id="validate_epub" class="btn-primary btn" value="Valida Epub" >
							<a class="btn-primary btn " href="<?php echo url() ?>index.php/epubedit?isbn=<?php  echo $info_epub['isbn']?>">Modifica ebook</a>
							
							<button id="leggi" class="btn-primary btn disabled" href="<?php echo url() ?>/reader/reader.html">Leggi</button>
						
						</div>
					
					</div>
					
					<div class="col-xs-12">
						<div class="row">
							
							
						</div>
					
					<div>
					
				</div>
			</div>
			
	</div>
		
</div>

<script type="text/javascript">
		
		var base_url = '<?php echo url();?>';
			
			
		
	</script>
	
  
  </head>

 </body>

 
</html>