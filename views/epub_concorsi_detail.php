<?php

	ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>


	
<body>

<div class="overload">
	<div class="loader"></div>
</div>


<div class="ebook-info-container" style="width: 99%;margin: 40px auto;">
	<div class="row">
		
		<div class="ebook-info" style="width: 80%;margin: 0 auto;">
			<form id="ebook_export" name="ebook_export"  method="post" enctype="multipart/form-data">			
				<div class="row form-group">
					
					<?php 
						

						
						$msg = '';
						$class = '';
						
						if(isset($_REQUEST['msg']) && $_REQUEST['msg'] == 'completato'){
							
							$msg 	= "e-book caricato";
							$class 	= "alert-success";
							
						}elseif(isset($_REQUEST['msg']) && $_REQUEST['msg'] == 'errore'){
							
							$msg 	= "C'è stato un errore";
							$class  = "alert alert-danger";
							
						}
					?>
					
					<div class="col-sm-12">
						
							<div class="alert <?php echo $class; ?>" role="alert">
							  <?php echo $msg; ?>
							</div>
						
					</div>
					
					<div class="col-md-12">
						<h3>Informazioni Ebook</h3>
					</div>
					<div class="col-2">
						<label for="isbn" >ISBN</label>
					</div>
					<div class="col-10">
						<input type="text" id="isbn_old"   name="isbn_old"	value="<?php  echo $sku?>" class="form-control ">
						
					</div>
				</div>
				<div class="form-group row">
					<div class="col-2">
						<label for="autore">Autore</label>
					</div>
					<div class="col-10">
						<div class="">
							
							<button class="btn btn-outline-secondary add_autori" style="display: inline-block;vertical-align: top;">Aggiungi autori</button>
						
							<input id="autore"  type="text"  name="autore"	value="<?php  echo $autore ?>"  class="form-control " aria-describedby="basic-addon1" style="display: inline-block;width: 80%;">
							
							
							
							
						</div>
						<div style="padding:10px 0">
							<textarea id="autore_desc" class="form-control" name="autore_desc" ><?php echo $autore_desc?></textarea>
						
						</div>
						
						<span id="autori">
							
							<?php
							$x=0;
							if(count($autori) > 0):						
								foreach($autori as $autore){?>
								
									<div class="campi-autori-group">
										<p><input type="text"  name="autore[<?php echo $x?>][first_name]"	value="<?php echo $autore->first_name ?>"  class="form-control autori aut_nome" placeholder="Autore Nome"></p>
										<p><input type="text"  name="autore[<?php echo $x?>][last_name]"	value="<?php echo $autore->last_name ?>"  class="form-control autori aut_cognome" placeholder="Autore Cognome"></p>
										<p><textarea name="autore[<?php echo $x?>][biography]" class="form-control autori" placeholder="Biografia Autore"><?php echo $autore->biography ?></textarea>
									</div>
						<?php		$x++;
								}
							endif;
							?>
						</span>
						
						
					</div>
				</div>
				<div class="form-group row">
					<div class="col-2">
						<label for="title">Titolo</label>
					</div>
					<div class="col-10">
						<input id="title"  type="text"  name="title"	value="<?php  echo $titolo?>"  class="form-control">
					</div>	
				</div>
				
				<div class="form-group row">
					<div class="col-2">
						<label for="sottotitolo">Sottotitolo</label>
					</div>
					<div class="col-10">
						<input id="sottotitolo"  type="text"  name="sottotitolo"	value="<?php  echo $sottotitolo?>"  class="form-control">
					</div>	
				</div>
				
				<div class="form-group row">
					<div class="col-2">
						<label for="npagine">N° pagine</label>
					</div>
					<div class="col-10">
						<input id="npagine"  type="text"  name="npagine"	value="<?php  echo $npagine?>"  class="form-control">
					</div>	
				</div>
				
				<div class="form-group row">
					<div class="col-2">
						<label for="keyword">Keyword</label>
					</div>
					<div class="col-10">
						<input id="keyword" type="text"  name="keyword"	value="<?php  echo $keyword 	?>"  class="form-control">
					</div>	
				</div>
				
				<div class="form-group row">
					<div class="col-2">
						<label for="meta_description">Descrizione breve</label>
					</div>
					<div class="col-10">
						<textarea  name="meta_description"	class="form-control"><?php  echo $meta_description 	?></textarea>
					</div>	
				</div>
				
				<div class="form-group row">
					<div class="col-2">
						<label for="description">Descrizione</label>
					</div>
					<div class="col-10">
						
						<textarea name="description" class="form-control"><?php  echo $description 	?>  </textarea>
					</div>	
				</div>
				<div class="form-group row">
					<div class="col-2">
						<label for="prezzo">Prezzo</label>
					</div>
					<div class="col-10">
						<input id="prezzo"  type="text" name="prezzo" value="<?php  echo $price	?>" class="form-control">
					</div>
				</div>
				
				<div class="form-group row">
					<div class="col-2">
						<label for="prezzo">Classificazione  <small class="text-muted">BISAC</small></label>
					</div>
					<div class="col-10">
						<select id="bisac" name="bisac[]" class="form-control" data-max-options="3" data-live-search="true" data-dropup-auto="false" data-size="15" multiple>
						
							<?php 
								foreach($bisac as $bisackey => $bisacvalue){
									
									echo '<option value="'.$bisackey.'">'.$bisacvalue.'</option>';
									
									
								}
							
							
							?>
						
						</select>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-2">
						<label for="prezzo">Collana</label>
					</div>
					<div class="col-10">
						<input id="collana"  type="text" name="collana" value="<?php echo $collana	?>" class="form-control">
					</div>
				</div>	
				
				
				<!--- 03 Prodotto completo, 04 Update, 05 Delete --->
				<div class="form-group row">
					<div class="col-2">
						<label for="prezzo">Azione</label>
					</div>
					<div class="col-10">
						<select name="azione">
						
							<option value="03">Aggiungi</option>
							<option value="04">Modifica</option>
							<option value="05">Elimina</option>
							
						</select>
					</div>
				</div>	
				
				
				<div class="form-group row">
					<div class="col-2">
						<label for="prezzo">EBOOK <sup>*</sup></label>
					</div>
					<div class="col-10">
						<!--<button id="modalEbook" class="btn btn-link">Sfoglia</button>-->
						
						
						<div class="custom-file">
							<input type="file" name="ebook" id="ebook_imported" >
							
							<p  style="font-size:12px"><i><sup>*</sup><span id="myInput"><?php echo $server_path ?></span></i> <button type="button" onclick="copyToClipboard('#myInput')">Copia testo</button></p>

						</div>
					</div>
				</div>
				
				<div class="button_wrap">
					<input id="server_path" type="hidden" name="server_path" value="<?php echo $server_path ?>">
					<input id="isbn" type="hidden" name="isbn" value="<?php echo $isbnnew ?>">
					<button id="button_ebook_export" type="submit"   class="btn-primary btn" >Crea esportato</button>
					
					
				
				</div>
			</form>
		</div>
					
					
			
	</div>
		
</div>

	<script type="text/javascript">
		
		var base_url = '<?php echo url();?>';
			
		$('#bisac').selectpicker();
		
		
		var bisac = <?php echo $bisacs ;?>
		
		
		
		function copyToClipboard(element) {
			
						
			
			var $temp = $("<input>");
			$("body").append($temp);
			$temp.val($(element).text()).select();
			document.execCommand("copy");
			$temp.remove();
		  
		  
		}
		
		$('#bisac').selectpicker('val', bisac);
		$('#bisac').selectpicker('render');
	
		
	
	</script>

  


 </body>

 
</html>