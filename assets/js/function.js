jQuery(document).ready(function($){
	
	/***UPLOAD FILE***/
	
	
	// preventing page from redirecting
    $("html").on("dragover", function(e) {
        e.preventDefault();
        e.stopPropagation();
        
    });

    $("html").on("drop", function(e) { e.preventDefault(); e.stopPropagation(); });
	
	 // Drag enter
    $('.upload-area').on('dragenter', function (e) {
        e.stopPropagation();
        e.preventDefault();
        //$("h1").text("Drop");
		 $(".upload-area").css("background-color","#fff");
    });

    // Drag over
    $('.upload-area').on('dragover', function (e) {
        e.stopPropagation();
        e.preventDefault();
       
		 $(".upload-area").css("background-color","#fff");
    });

	$('.upload-area').on('dragleave', function (e) {
		 
		 
	 e.stopPropagation();
        e.preventDefault();
       
		 $(".upload-area").removeAttr("style");
    });	 
	
    // Drop
    $('.upload-area').on('drop', function (e) {
        e.stopPropagation();
        e.preventDefault();
		
        

        var file = e.originalEvent.dataTransfer.files;
		var file_size = file[0].size;

		//fd.append('file', file_data);
		
        if(file_size < 73400320){
			
			uploadData(file);
			
		}else{
			
			var html = '<p class="alert alert-danger alert-dismissible fade show">File troppo grande caricalo via ftp</p>';
			$('.box_icon').before(html);
		} 
        
		
    });
	
	// Open file selector on div click
    $("#uploadfile").click(function(){
        $("#file_epub").click();
		
		
		var file = $("#file_epub")[0].files;
       
		
		var file_size = file.size;
		
		
        if(file_size < 73400320){
			
			uploadData(file);
			
		}else{
			
			var html = '<p class="alert alert-danger alert-dismissible fade show">File troppo grande caricalo via ftp</p>';
			$('.box_icon').before(html);
		} 
    });
	
	
	
	
	
	$(document).on('click', '.spine', function(event){
		event.preventDefault();
		
		
		var url      =  window.location.pathname; 
		var attr_url = $(this).attr('href');
		var path = url + attr_url;		
		var indexing = $('#indexing').val();
		var current;
		
		current = get_current_indexing()
		$('#epub').attr('src',path);  
		
		
	})
	
	
	/*var filename = './epub/OEBPS/Text/nav.xhtml';
	var selector = '#sidebar';
	var baseUrl = '/epub/OEBPS/Text/';
	var editable = false;
	
	toc.loadNav(filename, selector, baseUrl, editable)*/
	
	/**spine**/
	
	
	toc.next();
	/********/
	
	$(document).on('click', '.list_of_proj', function(event){
		$('.proj-list').toggle()
		
	})
	$(document).on('click', '.search', function(event){
		
		$('#search-form').toggle()
		
	})
	
	
	/*******/
	$(document).on('keyup', '#cerca', function(e) {
		/*console.log('fdfdg');
		var searchString = $('#searchText').val(),
			foundLi = $('#ebook_abilitati li span:contains("' + searchString + '")');
		foundLi.addClass('found');*/
		var foundLi;
		var filter = $(this).val();
		
		var stringSearch =  new RegExp(filter, "i")
		$(".ebooks-list li").each(function () {
			console.log($(this).text().search(stringSearch));
			
			if ($(this).text().search(stringSearch) < 0) {
				jQuery(this).hide();
			} else {
				jQuery(this).show()
				/*foundLi = $(this);
				$('#ebook_abilitati').animate({ scrollTop: foundLi.offset().top});*/
				
			}
		});
		
	
	})
	
	
	/*****ANALIZZA GLI ID****/
	var file = $(".file_name").val();
	
	$('#remove_pers').click(function(){
		
		var epub_folder_path   	=  $('#epub_folder_path').val();
		var epub_folder_name   	=  $('#epub_folder_name').val();
		$('.overload').show();
		setTimeout(function(){
		
			$.ajax({
				url: base_url + 'index.php/ajax',
				data: {
					   	'epub_folder_name' : epub_folder_name,				   
						'epub_folder_path' : epub_folder_path,
						'action':'remove_pers'
					   
					},
				dataType: 'text',
				type: 'post',
				success: function (response) {   
				  console.log(response);
				  location.reload();
				}
			});
		
		}, 3000);
		
	})	
	
	$('#ebook_personalizzato').click(function(e){
		
		e.preventDefault();

		var cover    			=  $('#cover').val();
		var author  			=  $('#author').val();
		var title    			=  $('#title').val();
		var rights   			=  $('#copy').val();
		var personalizzazione   =  $('#id_personalizzione').val();
		var isbn     			=  $('#isbn').val();
		var isbn_pers           = 'EDISESPER' + personalizzazione;
		var epub_folder_path   	=  $('#epub_folder_path').val();
		var epub_folder_name   	=  $('#epub_folder_name').val();
		var nav   	 			=  $('#nav').val();
		
		if(personalizzazione === '0' ){
			
			alert('scegli una personalizzazione');
			return;
			
		}
		
		
		$('.overload').show();
		setTimeout(function(){
		
			$.ajax({
				url: base_url + 'index.php/ajax',
				data: {
					   
					   'cover'       : cover,    
					   'isbn'   	 : isbn,   
					   'isbn_pers'   : isbn_pers,   
					   'author' 	 : author,     
					   'title'  	 : title,    
					   'rights'   	 : rights,   
					   'personalizzazione' : personalizzazione,  
					   'nav' : nav,
					   'epub_folder_path' : epub_folder_path,
					   'epub_folder_name' : epub_folder_name,
					   'action':'crea-epub'
					   
					  },
				dataType: 'text',
				type: 'post',
				success: function (response) {   
				   //console.log(response);
					$('.overload').hide();
					location.reload();
				   //$('#previewImage img').attr('src', response);
				}
			});
		
		}, 3000);
	})
	
	
	$('#ebook').click(function(){
		//console.log('ddd');
		var cover    =  $('#cover').val();
		var isbn     =  $('#isbn').val();
		var author   =  $('#author').val();
		var title    =  $('#title').val();
		var rights   =  $('#copy').val();
		var nav   	 =  $('#nav').val();
		
		var epub_folder_path   =  $('#epub_folder_path').val();
		var epub_folder_name   =  $('#epub_folder_name').val();
		
		if(title === '' || author === '' || rights ===''  ){
			
			alert('compila i campi');
			return;
			
		}
		
		$('.overload').show();
		setTimeout(function(){
		
			$.ajax({
				url: base_url + 'index.php/ajax',
				data: {
					   
					   'cover'  : cover,    
					   'isbn'   : isbn,   
					   'author' : author,     
					   'title'  : title,    
					   'rights' : rights,   
					   'epub_folder_path' : epub_folder_path,
					   'epub_folder_name' : epub_folder_name,
					   'nav' : nav,
					   action:'crea_epub_normale'
					   
					  },
				dataType: 'text',
				type: 'post',
				success: function (response) {   
				   console.log(response);
					$('.overload').hide();
				   
				   
				}
			});
		
		}, 3000);
	})
	
	$('#validate_epub').click(function(){
	
		
		var epub_folder_path   =  $('#epub_folder_path').val();
		var epub_folder_name   =  $('#epub_folder_name').val();
		
		
		$('.overload').show();
		setTimeout(function(){
		
			$.ajax({
				url: base_url + 'index.php/ajax',
				data: {
					   
					'epub_folder_path' : epub_folder_path,
					'epub_folder_name' : epub_folder_name,
					action:'valida'
					   
				},
				dataType: 'text',
				type: 'post',
				success: function (response) {   
				   //console.log(response);
					$('.overload').hide();
					$('#report').html(response);
					 open_report();
				   //$('#previewImage img').attr('src', response);
				}
			});
		
		}, 3000);
		
	})	
	
	
	//fix_image();
	
	$('#ind_epub').click(function(){
		var epub_folder_path   =  $('#epub_folder_path').val();
		var epub_folder_name   =  $('#epub_folder_name').val();
		
		var dir_ebook = epub_folder_path + epub_folder_name;
		
		
		$('.overload').show();
		setTimeout(function(){
		
			$.ajax({
				url: base_url + 'index.php/ajax',
				data: {
					   
					'epub_folder_path' : epub_folder_path,
					'epub_folder_name' : epub_folder_name,
					action:'ind_epub'
					   
				},
				dataType: 'text',
				type: 'post',
				success: function (response) {   
				   console.log(response);
					//$('.overload').hide();
					//$('#report').html(response);
				   //$('#previewImage img').attr('src', response);
				}
			});
		
		
		}, 3000);
		
	})
	
	
	/*****Apre la vinestra modale con la lista delle cartelle Ebook ****/
	
	$('#modalEbook').click(function(){
		
		var epub_folder_name = $('#server_path').val();
	
		var html = '';
		var html1 = '';
		$('.overload').show();
		setTimeout(function(){
		
			$.ajax({
				url: base_url + 'index.php/ajax/openModalEbook/',
				data: {
					   
					'epub_current_folder_name' : epub_folder_name,
					
					action:'openModalEbook'
					   
				},
				dataType: 'text',
				type: 'post',
				success: function (array_serve_folder) {   
				    var jsonData = JSON.parse(array_serve_folder);
					console.log(jsonData.current_folder)
					//html += jsonData.current_folder;
					html += '<input id="current_folder" type="text" value="'+jsonData.current_folder+'" name="current_folder">';
					
					$.each(jsonData.folder, function(index, item) {
						
							html += '<li class="open-folder" data-folder="'+item+'" >';
							html += ''+item+'';
							//html += '<span>'+jsonData.current_folder+'</span>';
							html += '</li>';
					});
						
					$('#folders-list').html(html);
					$('.overload').hide();
					
					$.fancybox.open({
						src  : '#frame_modal_ebook',
						type : 'inline',
						autoSize : true,
						width  : '70%',
						opts: {
							afterShow: function(instance, current) {
								$('.open-folder').on('click', function(e) {	
									e.preventDefault();
									instance.showLoading(current);
									var epub_click_folder_name   = $(this).attr('data-folder');
									var epub_current_folder_name = $('#current_folder').val();
									
									open_folder(epub_click_folder_name,epub_current_folder_name,instance,current)
									
									/*console.log(h);
									instance.hideLoading(current);
									instance.setContent(current, h);
									instance.update();*/
									
								})
							}
						}
						
						
					});
					//console.log(html);
				}
			});
		
		
		},3000);
		
	})
	
	
	/******/
	
	$('.add_autori').click(function(e){
		
		e.preventDefault();

		var i = $('.campi-autori-group').length;
		var input = '';
		
		    
		
		input += '<div class="campi-autori-group">';
		input += '<p><input type="text"  name="autori['+i+'][nome]"	value=""  class="form-control autori aut_nome" placeholder="Autore Nome"></p>';
		input += '<p><input type="text"  name="autori['+i+'][cognome]"	value=""  class="form-control autori aut_cognome" placeholder="Autore Cognome"></p>';
		input += '<p><textarea name="autori['+i+'][biografia]" class="form-control autori" placeholder="Biografia Autore"></textarea>';
        input += '</div>'; 


		$('#autori').append(input)

		var autori = $('input[name^="autori"]');
	
		autori.filter('input[name$="[nome]"]').each(function() {
			$(this).rules("add", {
				required: true,
				messages: {
					required: "Inserisci il nome dell'autore"
				}
			});
		});
		
		autori.filter('input[name$="[cognome]"]').each(function() {
			$(this).rules("add", {
				required: true,
				messages: {
					required : "Inserisci il cognome dell'autore",
				}
			});
		});
		
	})
	
	jQuery.validator.addMethod("autori", function(value) { 
		
	  /*var regex = /[A-Z]{6}[\d]{2}[A-Z][\d]{2}[A-Z][\d]{3}[A-Z]/;*/
		var autori = $('.campi-autori-group').length
		if(autori > 0){
			
			return true;
			
		}
		
		
	}, "Devi inserire almeno un autore");
	
	jQuery.validator.addMethod("bisac", function(value) { 
		
		
		var arrSelect = $('#bisac').val();
		
		if(arrSelect.length >=3){
			
			return true;
			
		}
		
		
	}, "Devi scegliere almeno 3 classificazioni");
	
	$('#ebook_export').validate({
		
			ignore: [],
			rules: {
				isbn_old: 'required',
				autore:{
					autori : true 
				},
				title: 'required',
				sottotitolo:'required',
				npagine: 'required',
				keyword: 'required',
				meta_description: 'required',
				prezzo: 'required',
				"bisac[]": {
					
					bisac: true
				},
				rules: 'required',
				ebook:'required'
			},
			messages: {
				isbn_old: "ISBN del precedente ebook è necessario",
				"bisac[]": {
					required: "Scegli 3 class."
				}
				
			},
			submitHandler: function(form) {
				form.submit();
			},
			showErrors: function(map, list) {
				var errorHtml = '';
				var errorMess = '';
				$('.error-msg').remove();
				
				$.each(list, function(index, arrayF) {
					console.log(arrayF)
					errorMess = arrayF.message;
					$(arrayF.element).addClass("error");
					
					//data-container="body" data-toggle="popover" data-placement="top" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus."
					errorHtml = '<p class="error-msg">'+errorMess+'</p>'
					/*$(arrayF.element).attr('data-toggle', 'popover')
					$(arrayF.element).attr('data-placement', 'top')
					$(arrayF.element).attr('data-content', 'Vivamus sagittis lacus vel augue laoreet rutrum faucibus.')
					$(arrayF.element).popover('show');*/
					
					$(arrayF.element).after(errorHtml);
				});
				
				//$('[data-toggle="popover"]').popover();
			}
			
			
	});
	//$('#ebook_export').data("validator").settings.ignore = "";
	
	$('#button_ebook_export').click(function(e){
		e.preventDefault();
		
		
		
		var form = $('#ebook_export')[0]; 
		var formData = new FormData(form);
;
		formData.append('action', 'save-onix');
		
		// Attach file
		formData.append('ebook', $('#ebook_imported').get(0).files); 
		
		
		
		$('.overload').show();
		setTimeout(function(){
		
			$.ajax({
				url: base_url + 'index.php/ajax',
				type : 'POST',
				enctype: 'multipart/form-data',
				dataType : 'text',
				processData: false,  // Important!
				contentType: false,
				cache: false,
				data : formData,
				success: function (response) {   
				   console.log(response);
					$('.overload').hide();
					//$('#report').html(response);
					 //open_report();
				   //$('#previewImage img').attr('src', response);
				},
				error:function(er){
					
					console.log(er);
					
				}
			});
		
		}, 3000);	

	})
	
	
	var autori = $('input[name^="autori"]');
	
	autori.filter('input[name$="[nome]"]').each(function() {
		$(this).rules("add", {
			required: true,
			messages: {
				required: "Nome is Mandatory"
			}
		});
	});
	
	autori.filter('input[name$="[cognome]"]').each(function() {
        $(this).rules("add", {
            required: true,
            messages: {
                Cognome: 'Cognome must be valid email address',
                required : 'Email is Mandatory',
            }
        });
    });
	
	
	$('.magentosync').click(function(e){
		
		var img = '<img src="http://192.168.0.11/edisesepub/assets/img/gif-load.gif" width="15">'
		var isbn = $(this).parents('tr').attr('id');	
		
		$(this).append(img)
		setTimeout(function(){
		
			$.ajax({
				url: 'http://localhost/edisesepub/index.php/ajax',
				data: {
					   
					isbn 				: isbn,
					action				:'sync-magento',
					
				},
				dataType: 'text',
				type: 'post',
				success: function (result) {   
					console.log(result)
					//self.find('.magentosync').text(result);
					//location.reload(true);
					
					

				},
				error:function (xhr, ajaxOptions, thrownError) {
					
					console.log(xhr.status + ':' + xhr.statusText,xhr.responseText);
				}
			});
		
		}, 3000);
		
		
	})
	
	function open_report(){
		
		$.fancybox.open({
			src  : '#report_container',
			type : 'inline',
			autoSize : false,
			'width' : '100%',
			opts : {
			  afterShow : function( instance, current ) {
				console.info('done!');
			  }
			}
		});
		
	}
	
	function get_current_indexing(){
		
		$('.sidebar li').each(function(x){
			
			
			
		})
		
	}
	
	
	function fix_image(){
		
		setTimeout(function(){	
		
			
			var img_src;
				var src;
			$('#epub').contents().find('img').each(function(){
				
				img_src = $(this).attr('src');
				console.log($(this))
				src = img_src.replace('../Images/', 'http://localhost/HtmlToImage/epub/OEBPS/Images/');
				 $(this).attr('src', src)
			});
		},400);	
	}
	
	function uploadData(file_data){
		console.log(file_data)
		
		var fd = new FormData();
		
		fd.append('file', file_data[0]);
		
		
		$('#progress_bar_content').removeClass('hide')
		$.ajax({
			xhr: function()
			  {
				var xhr = new window.XMLHttpRequest();
				//Upload progress
				xhr.upload.addEventListener("progress", function(e){
				  if (e.lengthComputable) {
					
					var ratio = Math.floor((e.loaded / e.total) * 100) + '%';
					$('#progress_bar_epub').css('width',ratio)
					$('#box_icon').text(ratio)
				  }
				}, false);
				//Download progress
				
				return xhr;
			},
		   
			url: 'index.php/ajax/upload', // point to server-side PHP script 
			dataType: 'json',  // what to expect back from the PHP script, if anything
			cache: false,
			contentType: false,
			processData: false,
			data: fd,                         
			type: 'post',
			success: function(data){
				console.log(data.msg)
				if(data.msg.type == '0'){
					 location.reload();
					
				}else{
					var html = '<p class="alert alert-danger alert-dismissible fade show">'+data.msg.text+'</p>';
					$('.box_icon').before(html);
					//console.log(html)
					
				}
			}
		});
		
	}
	
	/**** SCAN DIR ****/	
	
	function open_folder(folder_name,current_path,instance,current){
		var html1 = '';
		
		$.ajax({	
			url: base_url + 'index.php/ajax/openModalEbook/',
			data: {
				   
				'epub_folder_name' : folder_name,
				'epub_current_folder_name' : current_path,
				action:'openModalEbook'
				   
			},
			dataType: 'text',
			type: 'post',
			success: function (array_folder) {   
			   //console.log(array_folder)
				var jsonData1 = JSON.parse(array_folder);
				html1 += '<input id="current_folder" type="text" value="'+jsonData1.current_folder+'" name="current_folder">';
				
				
				$.each(jsonData1.folder, function(index, item) {
					//console.log(item)
					
						html1 += '<li class="open-folder" data-folder="'+item+'" >';
						html1 += ''+item+'';
						html1 += '</li>';
						
				});
				console.log(current)
				instance.hideLoading(current);
				instance.setContent(current, html1);
				instance.update();
				
			}

		});
		
	}
	
	
	$('#genera_indice').click(function(e){
		
		e.preventDefault();
		
		var epub_path = $('#epub_path').val()
		var epub_url = $('#epub_url').val()
		var nav_name = $('#nav_name').val();
		var class_cap = $('#class_cap').val();
		var class_cap_numero = $('#class_cap_numero').val();
		var class_cap_paragrafo = $('#class_cap_paragrafo').val();
		var _html = '<ul>';
		var menu_array_construct = [];
		var titolo,capitolo,file;
		
		$.ajax({	
			url: 'http://localhost/edisesepub/index.php/ajax',
			data: {
				   
				epub_path 			: epub_path,
				action				:'scan-epub-file',
				epub_url			: epub_url,
				class_cap   		: class_cap,
				class_cap_numero 	: class_cap_numero,
				class_cap_paragrafo : class_cap_paragrafo,
				nav_name			: nav_name
			},
			dataType: 'json',
			type: 'post',
			success: function (resul) {   
				console.log(resul)
				location.reload(true);
				
				

			},
			error:function (xhr, ajaxOptions, thrownError) {
				
				console.log(xhr.status + ':' + xhr.statusText,xhr.responseText);
			}

		});
		
		
		
		
	})
	
	$('#nav_epub ol li a').on('click', function(e){
		e.stopImmediatePropagation();
		e.stopPropagation();
		e.preventDefault();
		var href = $(this).attr('href');
		
		href = href.split('#');
		href = href[0];
		current_chapter = href;
		console.log(href);
		load_chapter(href);
		
	
	});
	

	function load_chapter(file){
		
		var html = '';
		var epub_url = $('#epub_url').val();
		var epub_path = $('#epub_path').val();
		
		
		
		var current_epub_root;
		
		
		/*$.get(url, function( html ) {
			$.ajaxSetup({ cache: false });
			html = html.split(/<body.*?>/g);
			html = html[1];
			html = html.split('</body>');
			html = html[0];
			
			console.log(html)
			//html = html.replace(/href="([^\/css]+)/g, "href=\"" + epub_url + "OEBPS/css/"); // da perfezionare
			
			console.log($("#iframe"))
			
			$("#iframe").contents().find('#doc').html(html)
			
		},'html').done(function(resp) {
			
			
			
			$('div#chapter_epub_wrap').attr('capitolo',file);
			current_chapter = file;
			
			//window.location.hash = '#' + file;
			
		}); 
		*/
		// Svuota l'iframe custom


		
		inspector.load(epub_path,epub_url,file);

		
	}
	
	
	
	
})