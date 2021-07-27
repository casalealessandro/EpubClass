
<html lang="it">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" >
	 <!-- fancybox CSS -->
	<link rel="stylesheet" href="<?php echo url() ?>assets/js/librerie-js/fancybox/jquery.fancybox.min.css">
	<link rel="stylesheet" href="<?php echo url() ?>assets/js/librerie-js/bootstrap-select/css/bootstrap-select.min.css">
	
	<link rel="stylesheet" href="<?php echo url() ?>assets/css/custom.css" />
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	
	<link rel="stylesheet" href="<?php echo url() ?>assets/iframe-editor/inspector.css" />
	<link rel="stylesheet" href="<?php echo url() ?>assets/iframe-editor/style-inspector.css" />
	
	<script src="<?php echo url() ?>assets/js/jquery.min.js"></script>
	<!-- Bootstrap JS -->
	<script rel="stylesheet" src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.bundle.min.js"></script>
	 <!-- Select bootstrap JS -->
	<script src="<?php echo url() ?>assets/js/librerie-js/bootstrap-select/js/bootstrap-select.min.js"></script>
	 <!-- fancybox JS -->
	<script src="<?php echo url() ?>assets/js/librerie-js/fancybox/jquery.fancybox.min.js"></script>
	<script src="<?php echo url() ?>assets/js/librerie-js/fancybox/fullscreen.js"></script>
	<script src="<?php echo url() ?>assets/js/librerie-js/jquery-validation/jquery.validate.min.js"></script>
    

	<script src="<?php echo url() ?>assets/js/toc.js"></script>
	
	<script src="<?php echo url() ?>assets/js/function.js"></script>
	
		 <!-- fancybox JS -->
    <script src="<?php echo url() ?>assets/iframe-editor/editor-iframe.js"></script>
	

	
	

  </head>
  <body>


<div class="overload">
	<div class="loader"></div>
</div>
<div id="content" class="container-fluid">
	<div class="row">
		<div  class="col-lg-12">
		
		
			<!--<ul class="proj-list hide">	
			
			<?php 
				foreach($list_proj['folder'] as $prj_name){
					
					echo '<li><a href="'._BASE_URL_.'/index.php/epub/file/?file_name='.urlencode($current_folder).'/'.$prj_name.'.epub">'.$prj_name.'</a></li>';	
					
				}
				?>
			
			</ul>
			-->
		
		<header>
			<div class="row">
				<div class="header">
					<div class="row">
						<nav class="col-sm-4 menu_horizzontal">
							
								<ul>
									<li class="icon"><a href="<?php echo url(); ?>index.php/index"><i class="material-icons">home</i></a></li>
									<li class="icon list">Concorsi
										<ul class="sub-menu">	
											<li><a href="<?php echo url(); ?>index.php/concorsi">e-books sul server edises</a></li>
											<li><a href="<?php echo url(); ?>index.php/concorsi/lista">Importa da lista<a></li>
											
										
										</ul>
									
									</li>
									<li class="icon"><a href="<?php echo url(); ?>index.php/index">Universitario</a></li>
									
								
								</ul>
							
						</nav>
						
						<!--<div class="logo">
							
							<img src="https://www.edises.it/skin/frontend/edises/default/images/logo-edises.png" class="logo">
						
						</div>-->
						<div class="col-sm-4 menu_horizzontal right">
							
								<ul>
									<li class="icon"><a href="<?php echo url(); ?>index.php/epubedit?type=new"><i class="material-icons">add</i></a></li>
									<li class="icon search"><a href="#"><i class="material-icons">search</i></a></li>
								
								</ul>	
							
						</div>
						<div id="search-form" style="display:none">
							<input id="cerca" name="cerca" placeholder="Cerca nella lista">
						</div>
					</div>
				</div>
			</div>
		 </header>
		</div>	
		