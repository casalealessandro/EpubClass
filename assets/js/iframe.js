var iFrame = document.getElementById('iframe');
var documentName = getDocumentName();
var projectName = getProjectName();
var projectId = getProjectId();
var bookId = getBookId();
var bookIsbn = getBookIsbn();
var userName = jQuery('#user-name').text();


var inspector = {

	parent : this,

	entities : {
		iFrame : iFrame,
		iFrameContainer : document.getElementById('documentContainer'),
		iFrameWindow : null,
		iFrameDocument : null,
		iFrameContent : null,
		loader : document.getElementById('documentLoader'),
		currentOver : null,	
		currentSelected : null,
		debug : true,
		modalContainer : document.getElementById('custom-modal'),
		
	},

	
	


	callLoader : function() {
		$('body').addClass('loading');
	},


	hideLoader : function() {
		setTimeout("$('body').removeClass('loading')", 500);
	},


	loaderError : function(d, textStatus, error, nullIdFlag, nullIdMailData) {

			if (nullIdFlag = '1') {

				$('#modalErrorInfo .errorTextWrapper').text( translate.get('elemento_non_selezionabile') );
				$('#modalErrorInfo #riprova').hide();

				$.getJSON(
					"/php_libs/nullMail", 
					nullIdMailData
				); // end getJSON

			}


			console.error("getJSON failed, status: " + textStatus + ", error: "+error);
			setTimeout("$('body').removeClass('loading')", 5000);

								
			setTimeout(
			  function() 
			  {
				$("#modalErrorInfo, .overpage ").show();

			  }, 5000);
	},

	repeatLastAction : function ( currentElement, params, repeatedFunction ) {
		var t = inspector;
		var e = t.entities;
		var b = t.buttons;
		var f = t.flags;
		var fx = t.functions;

		event.preventDefault();
		e.currentSelected = currentElement;
		repeatedFunction(params);
		
		console.log('[ repeatLastAction ]: OK');
		console.log('[ currentElement ]:' , currentElement);
		console.log('[ parameters ]:', params);
	},



	
	appendStyle : function(){
		var t = inspector;
		var e = t.entities;
		var head = e.iFrameDocument.getElementsByTagName('head')[0];
		newStyle = e.iFrameDocument.createElement('link');
		//newStyle.href = "../epublishare/assets/css/inspector.css?v=1";
		newStyle.href = "/themes/site_themes/epublishare/epublishare/application/assets/css/inspector.css"; 
		newStyle.rel = "stylesheet";
		newStyle.media = "all";			
		head.appendChild(newStyle);
	},

	
	getValidElement : function(element){
		var target = element;
		/* Eccezioni */
		// .figura_center_wrapper
		var fig_c_wrap = $(element).parents('.figura_center_wrapped');
		//console.log(fig_wrap);
		if(fig_c_wrap.length && fig_c_wrap.length > 0){
			target = fig_c_wrap[0];
		}

		// .figura_wrapper
		var fig_wrap = $(element).parents('.figura_wrapped');
		//console.log(fig_wrap);
		if(fig_wrap.length && fig_wrap.length > 0){
			target = fig_wrap[0];
		}

		// Tag IMG
		if(element.tagName == 'IMG' || element.tagName == 'img'){
			target = $(element).parent();
			target = target[0];
		}

		// Tag IMG in box_inner
		if( (element.tagName == 'IMG' || element.tagName == 'img') && $(element).parents('.box_inner').size() > 0 ){
			target = element;
		}

		// span.liste
		if($(element).hasClass('liste')){
			target = $(element).parents('li');
			target = target[0];
		}	

		// Tag b 
		if(element.tagName == 'b' || element.tagName == 'B'){
			target = $(element).parent();
			target = target[0];
		}

		// Tag strong 
		if(element.tagName == 'strong' || element.tagName == 'STRONG'){
			target = $(element).parent();
			target = target[0];
		}

		// Tag i 
		if(element.tagName == 'i' || element.tagName == 'I'){
			target = $(element).parent();
			target = target[0];
		}

		// Tag em 
		if(element.tagName == 'em' || element.tagName == 'EM'){
			target = $(element).parent();
			target = target[0];
		}

		// Tag b 
		if(element.tagName == 'underline' || element.tagName == 'UNDERLINE'){
			target = $(element).parent();
			target = target[0];
		}

		//console.log(target);

		return target;

	},

	getValidParent : function(element){
		var t = inspector;
		var e = t.entities;
		var f = t.flags;
		var fx = t.functions;
		var currElement = element;
		if(element.jquery){
			currElement = element[0];
			element = element[0];
		}
		console.log('cerco padre valido');	
		console.log(currElement);	
		currElement = jQuery(element).parent();
		console.log('esamino:'+currElement.id);
		if(fx.isValidNode(currElement)){
			console.log('trovato:'+currElement.id);
			console.log(currElement);
			stopPropagation();
			return currElement;
		}
		/*
		while(!fx.isValidNode(currElement) || currElement != iFrame || currElement.nodeType != 1){
			currElement = currElement.parentNode;
			console.log(currElement);
		}
		*/
		console('trovato:'+currElement.id);
		return currElement;
	},


	getElementFromPath : function(pathArray){
		var t = inspector;
		var e = t.entities;
		var progressiveElement = e.iFrameDocument.body;
		for(var i in pathArray){
			var index = parseInt(pathArray[i]);
			progressiveElement = progressiveElement.childNodes[index];
		}
		return progressiveElement;
		
	},

	getModalFieds : function(){
		var t = inspector;
		var e = t.entities;			
		var o = {};
		var a = jQuery(e.modalContainer).find('form').serializeArray();
		jQuery.each(a, function() {
			if (o[this.name]) {
				if (!o[this.name].push) {
					o[this.name] = [o[this.name]];
				}
				o[this.name].push(this.value || '');
			} else {
				o[this.name] = this.value || '';
			}
		});
		return o;
	},

	getNodePath : function(element){
		var t = inspector;
		var e = t.entities;
		var b = t.buttons;
		var fx = t.functions;
		var deep='';

		if(element.jquery){
			element=element[0];
		}

		if(element.id){
			//return element.id;
		}

		while (element !== e.iFrameDocument.body) {
			var parent=element.parentNode;
			x=0;
			while( (parent.childNodes[x] !== element) && (x < parent.childNodes.length)  ){
				x++;
			}
			deep = x + "," + deep;
			element = element.parentNode;
		}
		deep = (deep.slice(0, -1)).split(',');
		return deep;		  	
	},

	
	keyEscPress : function(event){},

	load : function(docName, listener) {
		documentName = docName;
		var t = inspector;
		var e = t.entities;
		var b = t.buttons;
		var f = t.flags;
		var r = t.repeat;
		var fx = t.functions;
		var doc = e.iFrame;
		var slug = doc.getAttribute('data-slug-documento');
		var docUrl = "/themes/site_themes/epublishare/documents/"+slug+"/OEBPS/Text/"+documentName;//+"?project=68&user=13&viewer=on";
		console.log('Carica Documento: '+documentName);
		t.flags.inspectoring = true;
		// No iFrame
		jQuery(doc).html('');
		jQuery('#ep-console').show();	
		jQuery.ajax({
			url: docUrl,
			type: "GET",
			dataType: "text",
			success: function (html) {
				html = html.split(/<body.*?>/g);
				html = html[1];
				html = html.split('</body>');
				html = html[0];

				//console.log(html);
				
				html = html.replace('')
				
				// Carica Contenuto
				jQuery(doc).html(html).addClass('inspector');

				// Scrolla all'ancora definita nell'url
				var hash = location.hash;
				var hashSplit = hash.split('?');
				if(hashSplit[1]){
					var anchor = jQuery('#'+hashSplit[1]);
					console.log(jQuery(anchor).offset().top);
					jQuery(document).scrollTop(jQuery(anchor).offset().top - 220);
				} else {
					jQuery(document).scrollTop(0);
				}

				
								
				
				
				// MouseOver
				jQuery('*',doc).mouseover(function(event){
					var target = (event.srcElement) ? event.srcElement : event.target;

					//console.log(target);
					target = fx.getValidElement(target);						

					if(jQuery(target).attr('id')){
						//console.log(target);
						//console.log(fx.isValidNode(target));
						if(fx.isValidNode(target)){
							jQuery(target).addClass('ep_highlight');
						} else {
							/*
							if(target.classList.contains('figura_centrale')){
								//target = fx.getValidParent(target);
								var parent = target.parentNode;
								while(parent.nodeType != 1){
									parent = parent.parentNode;
								}
								jQuery(parent).addClass('ep_highlight');
							}
							*/
						}
					}
				});

				// MouseOut
				jQuery('*',doc).mouseout(function(event){
					var target = (event.srcElement) ? event.srcElement : event.target;

					target = fx.getValidElement(target);
					

					if(fx.isValidNode(target)){
						jQuery(target).removeClass('ep_highlight');
					} else {
						/*
						if(target.classList.contains('figura_centrale')){
							//target = fx.getValidParent(target);
							var parent = target.parentNode;
							while(parent.nodeType != 1){
								parent = parent.parentNode;
							}
							jQuery(parent).removeClass('ep_highlight');
						}
						*/
					}
					
				});

				// Click 
				jQuery('*',doc).click(function(event){
					
					event = event ? event:event;		
					var target = (event.srcElement) ? event.srcElement : event.target;

					target = fx.getValidElement(target);
					
					if(fx.isValidNode(target)){

						/** BEGIN : SPOSTAMENTO BLOCCO **/
						if(f.movingBlock && e.movingBlock){
							event.stopPropagation();
							console.log('sposto il blocco qui');
							console.log(target);
							fx.moveBlock(e.movingBlock,target);
							fx.disableMovingState();
							
							return;
						}
						/** END : SPOSTAMENTO BLOCCO **/


						jQuery('.selected',doc).each(function(){
							$(this).removeClass('selected');							
						});
						jQuery(target).addClass('selected');
						e.currentSelected = target;
						jQuery('#showLibrary').removeAttr('disabled');

						jQuery('#ep-toolbox button').removeClass('pure-button-disabled');
						//jQuery('#ep-toolbox #collapseHiddenElement').addClass('pure-button-disabled');
						//jQuery('#ep-toolbox #unCollapseHiddenElement').addClass('pure-button-disabled');
						//console.log(e.currentSelected);
						if(jQuery(e.currentSelected).attr('data-ep-hidden-has-child')){
							//jQuery('#ep-toolbox button').addClass('pure-button-disabled');
							//jQuery('#ep-toolbox #collapseHiddenElement').removeClass('pure-button-disabled');
							jQuery('#ep-toolbox #unCollapseHiddenElement').removeClass('pure-button-disabled hide');
						}
						if(jQuery(e.currentSelected).hasClass('ep_hidden_element')){
							jQuery('#ep-toolbox #minimizeElement').addClass('pure-button-disabled');
						}
						
					} 
				});
				
				

				
				
				
			},
			error: function (xhr, status) {
				// alert("Libro non caricato");
			},
			complete: function (xhr, status) {
				//console.log(status);
			}
		});

	},

	log : function(message) {
		var t = inspector;
		var e = t.entities;
		var b = t.buttons;
		var fx = t.functions;
		if(e.debug){
			console.log(message);
		}
	},

		

	registerEvent : function(object, eventName, callback){
		object.addEventListener(eventName,callback, false);
	},	

	init : function(){

		
		//CARICA IL LIBRO
		this.functions.load(documentName, true);

		
	} 
	
};


function getDocumentName(){
	//url = iFrame.src;
	url = jQuery(iFrame).data('src');
	// Rimuove le ancore, se esistono
	url = url.substring(0, (url.indexOf("#") == -1) ? url.length : url.indexOf("#"));
	// Rimuove le query string, se esistono
	url = url.substring(0, (url.indexOf("?") == -1) ? url.length : url.indexOf("?"));
	// Rimuove il percorso antecedente al nome del file
	url = url.substring(url.lastIndexOf("/") + 1, url.length);
	// Rimuove l'estensione dal nome del file
	url = url.substring(0, (url.indexOf(".") == -1) ? url.length : url.indexOf("."));
	return url+'.xhtml';
}

function getProjectName(){
	var pn = iFrame.getAttribute('data-nome-progetto');
	return pn.toLowerCase();
}

function getProjectId(){
	var pn = iFrame.getAttribute('data-id-progetto');
	return pn;
}

function getBookId(){
	var pn = iFrame.getAttribute('data-id-libro');
	return pn;
}

function getBookIsbn(){
	var pn = iFrame.getAttribute('data-isbn-libro');
	return pn;
}

function getBookSlug(){
	var pn = iFrame.getAttribute('data-slug-documento');
	return pn;
}

// click per la chiusura della popup della libreria
function closeLibraryPopup(showLibraryButton){
	$('#form-libreria-contenuti a.close', parent.document).click(function(e) {
      e.preventDefault();
      $(showLibraryButton).trigger('click');
      $(this).unbind();
	})
}


				      



// Chiude alert errore in inspector
$('.alert-close-button, .abort-button, #riprova').on('click', function() {

  event.preventDefault();
  $('.overpage, #modalErrorInfo').hide();
  

})
	        		