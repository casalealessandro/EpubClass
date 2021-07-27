<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

  
			<div class="col-lg-12">
				<div class="row">
					
					<div class="table-responsive-lg">
						<table id="ebooks_cantook" class="table table-hover ebooks_edigita ">
							<thead>
								<tr>
								  <th scope="col">#</th>
								  <th scope="col">ISBN</th>
								  <th scope="col">Titolo</th>
								  <th scope="col">Anno</th>
								  <th scope="col">Collana</th>
								  <th scope="col">Nuovo ISBN</th>
								  <th scope="col">Stato</th>
								  <th scope="col">Dettaglio</th>
								  <th scope="col">Azione in Edises</th>
								</tr>
							</thead>
					 
							<tbody>
						
					<?php 
						
					
						
						$x=1;
						
						foreach($array_list_proj as $proj){
							
							echo '<tr id="'.$proj['nuovo_isbn'].'">';
							echo '<td>'.$x.'</td>';
							echo '<td>'.$proj['isbn'].'</td>';
							echo '<td>'.$proj['titolo'].'</td>';
							echo '<td>'.$proj['anno'].'</td>';
							echo '<td>'.$proj['collana'].'</td>';
							echo '<td>'.$proj['nuovo_isbn'].'</td>';
							
							$stato = ($proj['stato'] == 1) ? $stato = 'in edigiata' : ''; 
							
							echo '<td class="stato_in_edigita"></td>';
							echo '<td><a class="btn btn-link" href="'.url().'index.php/concorsi/detail?isbn='.$proj['isbn'].'&isbnew='.$proj['nuovo_isbn'].'&stato= '.$proj['stato'].' ">Apri</a></td>';
							echo '<td>';
							echo '<a class="btn btn-link magentosync" href="#">Sincronizza con Magento</a>';
							
							echo '</td>';
							echo'</tr>';
							$x++;
						}
					?>
							</tbody>
						</table>
					</div>
					
				</div>
			</div>
			
	</div>
		
</div>


 </body>
 <script>
	jQuery(document).ready(function($){
		
		$('#ebooks_cantook tbody tr').each(function() {
			
			var isbn = $(this).attr('id');
			 $(this).find('.magentosync').addClass('disable')
			 get_stato_upload(isbn, $(this));
			
			
		  
		});
		
		function get_stato_upload(isbn, self){
			
			var img = '<img src="http://192.168.0.11/edisesepub/assets/img/gif-load.gif" width="15">'
			
			self.find('.stato_in_edigita').append(img)
			setTimeout(function(){
			
				$.ajax({
					url: 'http://localhost/edisesepub/index.php/ajax',
					data: {
						   
						isbn 				: isbn,
						action				:'get-cantook-upload',
						
					},
					dataType: 'text',
					type: 'post',
					success: function (result) {   
						console.log(result)
						self.find('.stato_in_edigita').text(result);
						$(this).find('.magentosync').removeClass('disabled')
						
						

					},
					error:function (xhr, ajaxOptions, thrownError) {
						
						console.log(xhr.status + ':' + xhr.statusText,xhr.responseText);
					}
				});
			
			}, 3000);	

			
			
		}
		
		
	});	
	
	
 </script>
</html>