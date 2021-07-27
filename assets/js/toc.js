var toc = {    

    loadNav: function(filename, selector, baseUrl, editable, callback){
      $.ajax({
        'url':filename,
        'cache' : false,
        'dataType':'text',
        'success':function(data){   

          // Verifica se si sta lavorando su quello editabile      
          if(editable){

            var e = toc.navToEditable(data,baseUrl);
            $(selector).html(e);

            // Sortable solo se Editable
            $('.toc-sortable').sortable();

            // Disabilita il click sui link del toc editabile
            $(selector +' .toc-element a').click(function(e){
              e.preventDefault();
              return false;
            });

            // Eventi alla chiusura della popup edit-toc
            $('#form-edit-toc .close').click(function(e){
              e.preventDefault();
              $('#toc-toolbox button').attr('disabled', true);
              // $('.toc-sortable').sortable('disable');
            })

            // Carica handler per gli eventi del capitolo personalizzato
            toc.customChapter.load();

          } else {
            var e = toc.navToList(data,baseUrl);
            $(selector).html(e);
            // callback();
          }      


          // Evento sul EXPAND BUTTON
          $(selector+' [data-show] button[data-action=toc-expand-item]').click(function(e){
            e.preventDefault();
            toc.toggleExpandItem(this);
          });


          // Evento expandAll
          $('button[data-action=toc-expand-all]').click(function(e){
            e.preventDefault();
            $(selector+ ' li').addClass('expanded');
          });


          // Evento collapseAll
          $('button[data-action=toc-collapse-all]').click(function(e){
            e.preventDefault();
            $(selector+ ' li').removeClass('expanded');
          });

        }
      })
    },

    // prende in input il contenuto di un file nav.xhtml
    parseToc : function(html){
      html = html.split(/<nav.*?>/g);
      html = html[1];
      html = html.split('</nav>');
      html = html[0];
      
      var root = $(html).children();
      return root;
      /*
      var navObj = toc.toObj(root,'/CUSTOM URL/');
      return toc.toEditable(navObj,true); 
      */
    },

    navToObj: function(html, baseUrl){
      var root = toc.parseToc(html, baseUrl);
      return toc.toObj(root,baseUrl);
    },

    listToObj: function(selector, baseUrl){
      var root = $(selector).children();
      return toc.toObj(root,baseUrl);
    },

    toObj: function(root, baseUrl){
      // root = OL
      var nav = [];
      $.each(root, function(i,item){
        // item = LI
        var subNav = {
          'attributes' : {
            'id':'',
            'show':'on',
            'custom': 'false',
            'class':'',
          },
          'link':{},
          'nav':[]
        };
        subNav.attributes.show = (typeof(undefined)==typeof(item.attributes['data-show']))? subNav.attributes.show : item.attributes['data-show'].value;
        subNav.attributes.custom = (typeof(undefined)==typeof(item.attributes['data-custom']))? subNav.attributes.custom : item.attributes['data-custom'].value;
        subNav.attributes.class = (typeof(undefined)==typeof(item.attributes['class'])) ? '' : item.attributes['class'].value;

        var subItems = $(item).children();
        $.each(subItems, function(j,subItem){
          var tag = subItem.tagName.toUpperCase();
          if(tag == 'A'){
            var href = $(subItem).attr('href');
            var url = href.substring(href.lastIndexOf('/')+1);
            var hash = href.substring(href.lastIndexOf('#')+1);
            var label = $(subItem).text();
            subNav.attributes.id = 'nav_item_'+hash;          
            subNav.link.label = label;
            subNav.link.href = baseUrl+url;            
          }
          if(tag == 'OL'){
            subNav.nav = toc.toObj($(subItem).children(),baseUrl);
          }        
        });      
        nav.push(subNav);
      });
      return nav;
    },

    navToEditable: function(html, baseUrl){
      var obj = toc.navToObj(html, baseUrl);
      //return toc.toEditable(obj,true);
      return toc.toList(obj,true,true);
    },

    navToList: function(html, baseUrl){
      var obj = toc.navToObj(html, baseUrl);
      return toc.toList(obj,true, false);
    },

    // Crea un elenco linkato. 
    // Prende in input il risultato di parseToc
    toList : function(tocObj, isRoot, editable){

      var elementClass = isRoot ? 'toc-root-element' : 'toc-child-element';
      elementClass += ' toc-element';
      var html = '<ol class="toc-sortable">';
      $.each(tocObj, function(i,o){

        html += '<li class="'+elementClass+'" id="'+o.attributes.id+'" data-custom="'+o.attributes.custom+'" data-show="'+o.attributes.show+'">';

        // if(editable){
        //   html += '<button data-action="toc-toggle-visible-item"><i class="fa fa-eye" title="' + translate.get('nascondi') + '"></i><i class="fa fa-eye-slash" title="'+ translate.get('mostra') +'"></i></button>';
        // }

        //html += '<a href="'+o.link.href+'">'+o.link.label+'</a>';
        html += '<a contenteditable="false" href="'+o.link.href+'"';
        if(!editable){
          html += ' class="page-link spine" ';
        }
        html += '>'+o.link.label+'</a>';

      // if ( (o.nav.length>0) || (o.attributes.custom) ) {
        if ( (o.nav.length>0) ) {
          html += '<button data-action="toc-expand-item"><i class="fa fa-plus"></i><i class="fa fa-minus"></i></button>';
        }
        /*
        if(isRoot){
          html += '<button data-action="toc-expand-item"><i class="fa fa-plus"></i><i class="fa fa-minus"></i></button>';
        } 
        if(isRoot && editable){
          html += '<button data-action="toc-edit-item"><i class="fa fa-edit"></i></button><button data-action="toc-toggle-visible-item"><i class="fa fa-eye" title="Visibile"></i><i class="fa fa-eye-slash" title="Non Visibile"></i></button>';
        }
        */
        if(o.nav.length>0){
          html += toc.toList(o.nav, false, editable);
        }
        html += '</li>';
      });
      html += '</ol>';
      return html;
    },


    toNav: function(selector, filename, callback, original){
      var root = $(selector).children();
      // console.log(root);
      
      var obj = toc.toObj(root,'');
      // console.log(obj);
      toc.save(obj,filename, callback, original);
    },

	/***Naviga nell'ebook sulla base dello spine***/
	
	next:function(){
		
		
		
	},
	
	
    save: function(obj,filename,callback, original){
      $.ajax({
        url:'/themes/site_themes/epublishare/epublishare/application/libs/saveToc.php',
        method : 'POST',
        dataType : 'html',
        data : {
          'filename':filename, 
          'nav':JSON.stringify(obj),
          'nav_original': original
        },
        success : function(result){
          callback(result);
        }
      });
    },


    toggleExpandItem : function(elm){
      elm = $(elm).parent();
      $(elm).toggleClass('expanded');
    },   

    
  
    customChapter: {

        entities: {
          currentSelected: null,
          tocContainer: document.getElementById('edit-toc-container')
        },

        buttons: {
          createChapter: document.getElementById('createChapter'),
          createSubChapter: document.getElementById('createSubChapter'),
          hide: document.getElementById('hideChapter'),
          rename: document.getElementById('renameChapter'),
          delete: document.getElementById('deleteChapter')
        },

        flags: {
          isCustom: false,
          isChild: false,
          hasChild: false

        }, 

        load: function() {
            var t = toc;
            var cc = t.customChapter;
            var e = cc.entities;
            var fx = cc.functions;
            var b = cc.buttons;

            fx.resetAllEvents();

            // Click sugli elementi del toc editable
            fx.enableClickOnElements();

            // Click handler per gli eventi 
            $(b.createChapter).click(function(event) { fx.createChapter(event); });
            $(b.createSubChapter).click(function(event) { fx.createSubChapter(event); });
            $(b.hide).click(function(event) { fx.hide(event); });
            $(b.rename).click(function(event) { fx.rename(event); });
            $(b.delete).click(function(event) { fx.delete(event); });

            
            // Disabilita il tasto invio sui capitoli editable
            $('#form-edit-toc').on('keydown', '.editable', function(e) {if (e.keyCode === 13) {e.preventDefault() } }) // keydown

        },

        functions: {

            isCustom : function() {
              var t = toc;
              var cc = t.customChapter;
              var e = cc.entities;
              var fx = cc.functions;
              var b = cc.buttons;
              var f = cc.flags;

              if ( $(e.currentSelected).data('custom') ) {
                f.isCustom = true;
              } else {
                f.isCustom = false;
              }

            },

            isChild : function() {
              var t = toc;
              var cc = t.customChapter;
              var e = cc.entities;
              var fx = cc.functions;
              var b = cc.buttons;
              var f = cc.flags;

              if ( $(e.currentSelected).hasClass('toc-child-element') ) {
                f.isChild = true;
              } else {
                f.isChild = false;
              }

            },

            hasChild : function() {
              var t = toc;
              var cc = t.customChapter;
              var e = cc.entities;
              var fx = cc.functions;
              var b = cc.buttons;
              var f = cc.flags;

              if ( $(e.currentSelected).children('ol').length > 0 ) {
                f.hasChild = true;
              } else {
                f.hasChild = false;
              }

            },


            toggleButtons : function() {
              var t = toc;
              var cc = t.customChapter;
              var e = cc.entities;
              var fx = cc.functions;
              var b = cc.buttons;
              var f = cc.flags;


              $('#toc-toolbox button').attr('disabled', true);

                if ( f.isCustom && f.hasChild ) {
                  $(b.createChapter).removeAttr('disabled');
                  $(b.createSubChapter).removeAttr('disabled');
                  $(b.hide).removeAttr('disabled');
                  $(b.rename).removeAttr('disabled');

                } else if ( f.isCustom && f.isChild ) {
                  $(b.hide).removeAttr('disabled');
                  $(b.rename).removeAttr('disabled');
                  $(b.delete).removeAttr('disabled');

                } else if ( f.isChild ) {
                  $(b.hide).removeAttr('disabled');

                }  else if ( f.isCustom ) {
                  $('#toc-toolbox button').removeAttr('disabled');

                } else {
                  $(b.createChapter).removeAttr('disabled');
                  $(b.createSubChapter).removeAttr('disabled');
                  $(b.hide).removeAttr('disabled');
                }

            },

            enableClickOnElements : function() {
              var t = toc;
              var cc = t.customChapter;
              var e = cc.entities;
              var fx = cc.functions;
              var b = cc.buttons;
              var f = cc.flags;

                jQuery('li a', e.tocContainer).on('click', function(event) {
                  event = event ? event:event;    
                  var target = (event.srcElement) ? event.srcElement : event.target;
                
                  e.currentSelected = $(target).parent();
                  fx.isCustom();
                  fx.isChild();
                  fx.hasChild();

                  fx.toggleButtons();

                  jQuery('.selected', e.tocContainer).each(function(){
                    $(this).removeClass('selected');    
                  })
                  jQuery(e.currentSelected).addClass('selected');

                }); // click
            },

            resetAllEvents : function() {
              var t = toc;
              var cc = t.customChapter;
              var e = cc.entities;
              var fx = cc.functions;
              var b = cc.buttons;

              $(b.createChapter).unbind();
              $(b.createSubChapter).unbind();
              $(b.hide).unbind();
              $(b.rename).unbind();
              $(b.delete).unbind();
            },

            chapterActions : function(path, filename, action, content) {
              var t = toc;
              var cc = t.customChapter;
              var e = cc.entities;
              var fx = cc.functions;
              var b = cc.buttons;

               $.ajax({
                url:'/themes/site_themes/epublishare/epublishare/application/libs/capitoloCustom.php',
                method : 'POST',
                dataType : 'html',
                data : {
                  'path':path,
                  'filename':filename,
                  'action':action,
                  'content':content
                },
                success : function(result){
                  // callback(result);
                }
              });

            },


            createChapter: function(event) {
              var t = toc;
              var cc = t.customChapter;
              var e = cc.entities;
              var fx = cc.functions;
              var b = cc.buttons;
              event.preventDefault();

              var m = $('#toc-save-button').parents('.flow-popup');
              var path = '../../../projects/'+$(m).data('bookIsbn')+'/'+$(m).data('prgId')+'/Temp/layers/custom/chapters/';
              var url = $(e.currentSelected).find('> a').attr('href');
              var parent = url.split("/").pop();
              var chapterFile;
              var type = 'chapters';
              var custom = false;
              if ( $(e.currentSelected).data('custom') == true ) { custom = true; }


              // Routine per la ottenere il nome del capitolo/sottocapitolo
              $.ajax({
                url:'/themes/site_themes/epublishare/epublishare/application/libs/capitoloCustom.php',
                method : 'POST',
                async: false,
                // dataType : 'html',
                data : {
                  'path':path,
                  'action':'check',
                  'type': type,
                  'parent': parent,
                  'custom': custom
                },
                success : function(data){
                    chapterFile = data;
                }
              }); // ajax


              console.log('chapterFile', chapterFile);

              var html = '<li class="editable toc-root-element toc-element ui-sortable-handle " data-custom="true" data-show="on" >';
                  html += '<a class="chapterName" contenteditable="true" href="/public/editor/{segment_3}/{segment_4}/' + chapterFile + '.xhtml">Inserisci un titolo</a>';
                  html += '</li>';


              $(e.currentSelected).before($(html).fadeIn('slow'));
              $('#toc-toolbox button').attr('disabled', true);

              $('.editable').ready(function() { 

                  $(this).find('.chapterName').on('click', function() { 
                      $(this).empty().focus(); 
                  }); 


                  $('#toc-save-button').unbind();
                  // Aggiunge l'evento per il salvataggio del file relativo al capitolo
                  $('#toc-save-button').click(function(event) {

                    var url = $(e.currentSelected).prev().find('.chapterName').attr('href');
                    var docName = url.split("/").pop();
                    var filename = '../../../projects/'+$(m).data('bookIsbn')+'/'+$(m).data('prgId')+'/Temp/layers/custom/chapters/'+ docName;

                    var skel = '<?xml version="1.0" encoding="utf-8"?>'; 
                    skel += "<!-- Copyright © Edises S.A. 2013 All Rights Reserved. No part of the structure of the file may be used or reproduced without Edises's  express consent. -->";
                    skel += '<html xmlns="http://www.w3.org/1999/xhtml">';
                    skel += '<head>';
                    skel += '<meta charset="utf-8" />';
                    skel += '</head>';
                    skel += '<body>';
                    skel += '<p><em>Clicca per editare il capitolo...</em></p>';
                    skel += '</body>';
                    skel += '</html>';

                    fx.chapterActions(path, filename, 'create', skel); 

                    // Riassegna l'event listener originario al save button
                    $(this).on('click', function() { saveToc() });

                  });


              }); // ready
              
            },


            createSubChapter: function(event) {
              var t = toc;
              var cc = t.customChapter;
              var e = cc.entities;
              var fx = cc.functions;
              var b = cc.buttons;
              var f = cc.flags;
              event.preventDefault();


              var m = $('#toc-save-button').parents('.flow-popup');
              var path = '../../../projects/'+$(m).data('bookIsbn')+'/'+$(m).data('prgId')+'/Temp/layers/custom/chapters/';
              var url = $(e.currentSelected).find('> a').attr('href');
              var parent = url.split("/").pop();
              var chapterFile;
              var type = 'subchapters';
              var custom = false;
              if ( $(e.currentSelected).data('custom') == true ) { custom = true; }


              // Routine per la ottenere il nome del capitolo/sottocapitolo
              $.ajax({
                url:'/themes/site_themes/epublishare/epublishare/application/libs/capitoloCustom.php',
                method : 'POST',
                async: false,
                // dataType : 'html',
                data : {
                  'path':path,
                  'action':'check',
                  'type': type,
                  'parent': parent,
                  'custom': custom
                },
                success : function(data){
                    chapterFile = data;
                }
              }); // ajax


              var hasChild = $(e.currentSelected).find('ol.toc-sortable');
              var html = '';

              if ( f.hasChild  ) {
                html = '<li class="editable toc-child-element toc-element ui-sortable-handle" data-custom="true" data-show="on">';
                html += '<a class="chapterName" contenteditable="true" href="/public/editor/{segment_3}/{segment_4}/' + chapterFile +'.xhtml">Inserisci un titolo</a>';
                html +=  '</li>';  

                $(e.currentSelected).addClass('expanded');
                $(e.currentSelected).find('> ol.toc-sortable').prepend($(html).fadeIn('slow'));

              } else {

                html = '<ol class="toc-sortable ui-sortable">';
                html += '<li class="editable toc-child-element toc-element ui-sortable-handle" data-custom="true" data-show="on">';
                html += '<a class="chapterName" contenteditable="true" href="/public/editor/{segment_3}/{segment_4}/' + chapterFile +'.xhtml">Inserisci un titolo</a>';
                html +=  '</li>';
                html += '</ol>';

                $(e.currentSelected).addClass('expanded');
                $(e.currentSelected).append($(html).fadeIn('slow'));

              }

              $('#toc-toolbox button').attr('disabled', true);


              $('.editable').ready(function() {                 

                  $(this).find('.chapterName').on('click', function() { 
                      $(this).empty().focus(); 
                  }); 


                  $('#toc-save-button').unbind();
                  // Aggiunge l'evento per il salvataggio del file relativo al capitolo
                  $('#toc-save-button').click(function(event) {

                    var m = $('#toc-save-button').parents('.flow-popup');
                    var url = $(e.currentSelected).find('.chapterName').attr('href');
                    var docName = url.split("/").pop();
                    var filename = '../../../projects/'+$(m).data('bookIsbn')+'/'+$(m).data('prgId')+'/Temp/layers/custom/chapters/'+ docName;

                    var skel = '<?xml version="1.0" encoding="utf-8"?>'; 
                    skel += "<!-- Copyright © Edises S.A. 2013 All Rights Reserved. No part of the structure of the file may be used or reproduced without Edises's  express consent. -->";
                    skel += '<html xmlns="http://www.w3.org/1999/xhtml">';
                    skel += '<head>';
                    skel += '<meta charset="utf-8" />';
                    skel += '</head>';
                    skel += '<body>';
                    skel += '<p><em>Clicca per editare il capitolo...</em></p>';
                    skel += '</body>';
                    skel += '</html>';


                    fx.chapterActions(path, filename, 'createChild', skel);  

                    // Riassegna l'event listener originario al save button
                    $(this).on('click', function() { saveToc() });

                  });
                
              }); // ready



            }, 


            hide: function(event) {
              var t = toc;
              var cc = t.customChapter;
              var e = cc.entities;
              var fx = cc.functions;
              var b = cc.buttons;
              event.preventDefault();
              
              console.log($(e.currentSelected).data('show'));
              if($(e.currentSelected).attr('data-show')=='on'){
                console.log('visible');
                $(e.currentSelected).attr('data-show','off');
              } else {
                console.log('not visible');
                $(e.currentSelected).attr('data-show','on');
              }

            },


            rename: function(event) {
              var t = toc;
              var cc = t.customChapter;
              var e = cc.entities;
              var fx = cc.functions;
              var b = cc.buttons;
              event.preventDefault();

              chapterName = $(e.currentSelected).find('> a');
              $(e.currentSelected).toggleClass('editable');
              $(e.currentSelected).parent().sortable( 'disable' );

              var checkEditable = chapterName.attr('contenteditable');
              if (checkEditable == 'false') { 
                  chapterName.attr('contenteditable', true);
                  chapterName.focus();
              }
              else { 
                chapterName.attr('contenteditable', false);
                $(e.currentSelected).parent().sortable( 'enable' );
              }



            },



            delete: function(event) {
              var t = toc;
              var cc = t.customChapter;
              var e = cc.entities;
              var fx = cc.functions;
              var b = cc.buttons;
              event.preventDefault();

              var m = $('#toc-save-button').parents('.flow-popup');
              var url = $(e.currentSelected).find('> a').attr('href');
              var parent = $(e.currentSelected).parents('.toc-root-element');
              var type = 'chapters';

              // Aggiunge la tipologia (chapters o subchapters)
              // if ( $(e.currentSelected).hasClass('toc-root-element') ) {
              //   type = 'chapters';
              // } else if ( $(e.currentSelected).hasClass('toc-child-element') ) {
              //   type = 'subchapters';
              // }

              var docName = url.split("/").pop();
              var folderName = docName.split(".").shift();
              var path = '../../../projects/'+$(m).data('bookIsbn')+'/'+$(m).data('prgId')+'/Temp/layers/custom/'+ type +'/';
              var filename = '../../../projects/'+$(m).data('bookIsbn')+'/'+$(m).data('prgId')+'/Temp/layers/custom/'+ type +'/'+ docName ;


              $('#alertModal .errorTextWrapper').text('Sei sicuro di voler cancellare questo capitolo?');
              $('#alertModal, .overpage').show();
              $('#alertModal .confirm-button').attr('id', 'deletePopup');
              $('#alertModal .confirm-button').text('Rimuovi');



              $('#alertModal .confirm-button#deletePopup').click(function() {
                
                $('#alertModal, .overpage').hide();

                // Rimuove l'elemento
                $(e.currentSelected).fadeOut('slow', function() {
                  $(this).remove();

                  // Salva toc
                  saveToc();

                });

                // Verifica se l'elemento non ha più figli e rimuove il toc-expand button
                if ( $(parent).find('> ol').children().length == 1 ) {
                  $(parent).find('[data-action=toc-expand-item]').remove();
                }


                console.log('path', path);
                console.log('filename', filename);

                fx.chapterActions(path, filename, 'delete', '');  
                $('#toc-toolbox button').attr('disabled', true);

                // Resetta il pulsante di conferma cancellazione
                $(this).unbind('click');


              })//click


            },


            setCustomTinyMCE: function(target, filePath) {

                var projectId = $('#doc').data('id-progetto');

                // Rimuove il tinymce se già inizializzato
                tinymce.remove();

                // Init Tiny MCE
                tinymce.init({
                  selector: target.selector,
                  inline: true,
                  language: 'it',
                  closed: /^(br|hr|input|meta|img|link|param|area|source)$/,
                  menubar: false,
                  skin : 'lightgray', 
                  plugins : 'link code save table textcolor tiny_mce_wiris visualblocks ',
                  extended_valid_elements : 'math,semantics,mrow,mfrac,mn,msub,msup,munder,msqrt,annotation,audio[controls|src|data]',

                  // Content filtering
                  apply_source_formatting : false,                //added option
                  verify_html : false,                            //added option

                  forced_root_block : 'p',
                  object_resizing : 'img, table',

                  // relative_urls : false,
                  convert_urls: false,

                  // Auto Focus
                  // auto_focus: 'docCustom',

                  toolbar1 : "code | undo redo | bold italic underline strikethrough superscript subscript removeformat | formatselect formatp formath2 formath3 | fontsizeselect | visualblocks",
                  toolbar2 : "link unlink | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | cut copy paste | forecolor backcolor | table | tiny_mce_wiris_formulaEditor | addChemicalFormula",
                  toolbar3 : "addimage removeimage | addvideo removevideo | addaudio | save cancel | removeblock | moveblock",
                  formats: {
                      customformat: {'table': 'td', 'styles': {'min-width': '120px'}, attributes: {'title': 'My custom format'}}
                  },
                  save_enablewhendirty: false,
                  image_advtab :true,


                  init_instance_callback : function(editor) {
                    console.log("Editor: " + editor.id + " is now initialized.");
                  },


                  save_onsavecallback: function(editor) {

                      // var blocco = $(editorContainer).parents('.personalizzazione');
                      
                      // $(blocco).find('.Wirisformula').each(function(){
                      //   var currFormulaTag = $(this);
                      //   var src = currFormulaTag.attr('src');
                      //   var idImage = src.substring(src.indexOf("=") + 1, src.length);
                      //   idImage = idImage.substring(0, (idImage.indexOf("&") == -1) ? idImage.length : idImage.indexOf("&"));
                      //   currFormulaTag.attr('id',idImage);
                      //   if(idImage.indexOf("/") == -1) {

                      //     jQuery.ajax({
                      //       url : '/themes/site_themes/epublishare/epublishare/application/libs/copiaImmagineFormula.php',
                      //       type : 'GET',
                      //       async :false,
                      //       dataType : 'json',
                      //       data : {
                      //         'i' : idImage,
                      //         'p':projectId,
                      //         'l' : bookIsbn,
                      //         'u': inspector.entities.userId.innerHTML
                      //       }
                      //     }).always(function(response, status){
                      //       if(status == 'success'){
                      //         for(a in response){
                      //           currFormulaTag.attr(a,response[a]);
                      //           //console.log(a+'='+response[a]);
                      //         }
                      //         currFormulaTag.attr('ep_nocopy_formula_image','1');
                      //         console.log(currFormulaTag);
                      //       }
                      //     });       
                      //   }
                      // });


                      // editorContainer.innerHTML = content;
                      // var args = $(blocco).html();

                      // args = args.replace(/<br data-mce-bogus="1">/g,'<br />') ;

                      // Fix del tag img per l'inclusione della formula lato TinyMCE
                      // args = args.replace(/(<img("[^"]*"|[^\/">])*)>/g, "$1></img>");


                      console.log('editor:save');
                      var editorContainer = editor.getElement();
                      var content = editor.getContent({format: 'html'});


                      // Fix per le immagini copiate dal contenuto del libro
                      content = content.replace(/src="\.\.\/\.\.\/\.\./g,'src="http://ep2.qserver.it');

                      var postdata = {
                        'action' : 'save',
                        'content' : content,
                        'filename' : filePath
                      } 

                      jQuery.ajax({
                        url : '/themes/site_themes/epublishare/epublishare/application/libs/capitoloCustom.php',
                        type : 'POST',
                        dataType : 'html',
                        data : postdata
                      });

                      tinymce.activeEditor.windowManager.alert('Contenuto salvato.');


                      // editor.variables.currentContent[editor.current.id] = ed.getContent({format: 'raw'});

                      // Aggiorna data di modifica del progetto
                      jQuery.ajax({
                        url : '/api/progetti/'+projectId+'?m=updateEditDate',
                        type : 'POST',
                        dataType : 'json',
                        success : function(result){
                          console.log(result);
                        }
                      });

                  }, // save_onsavecallback


                  save_oncancelcallback: function (ed) { 
                      console.log('editor:cancel');
                      //ed.setContent(editor.variables.currentContent[editor.current.id],{format: 'raw'});
                      ed.setContent('',{format: 'raw'});
                      // editor.variables.currentContent[editor.current.id] = ed.getContent({format: 'raw'});
                      tinymce.activeEditor.windowManager.alert(translate.get('blocco_ripristinato'));   
                    }, // save_oncancelcallback


                  setup: function(ed) {

                    // var clicked = false;
                    ed.on('click', function(e) {
                        // var $editor = $('.mce-tinymce');
                        // $editor.draggable();

                        
                        // Attiva la libreria all'attivarsi di TinyMCE
                        // jQuery('#showLibrary').removeAttr('disabled');
                        // $('body').addClass('activeMCE');

                        
                        console.log('TinyMCE: Mostra');
                    });


                    ed.on('focus',function(){
                        console.log('editor:focus');
                        
                        $('body').addClass('activeMCE');

                        // Attiva la libreria all'attivarsi di TinyMCE
                        jQuery('#showLibrary').removeAttr('disabled');

                        
                        // $('.personalizzazione .docente').prop('contenteditable', 'false');
                        // $('.personalizzazione_inner.elemento_libreria').prop('contenteditable', 'false');

                        // Rende i container dei contenuti della libreria non editabili
                        // $('.personalizzazione .docente').prop('contenteditable', 'false');

                        // Rende il contenuto editabile
                        // $('.personalizzazione_inner.elemento_libreria .mceNonEditable').prop('contenteditable', 'true');

                        
                      // editor.variables.currentElement = ed.getContent({format: 'raw'});
                      // editor.current = ed;
                    });


                    ed.on('blur', function(e) {
                        $('body').removeClass('activeMCE');
                        console.log('TinyMCE: Nascondi'); 
                    });



                    // Evita la cancellazione dei blocchi della libreria contenuti
                     // ed.on("keydown",function(e) {
                     //  //prevent empty panels
                     //  if (e.keyCode == 8 || e.keyCode == 46) { //backspace and delete keycodes
                     //      var result = getParent('personalizzazione', ed);
                     //      if ( $(result).hasClass('personalizzazione') ) {
                     //        e.preventDefault();
                     //        return false;
                     //      }
                     //  }
                     // });


                    // var removeblock = null;
                    // var currentEditor = ed;
                    // ed.addButton('removeblock', {
                    //   title : translate.get('elimina_blocco'),
                    //   text : translate.get('elimina_blocco'),
                    //   onPostRender : function() { removeblock = this; },
                    //   onclick      : function() { alert('Clicked!');},

                    //   onclick : function() {
                    //       ed.windowManager.confirm(translate.get('vuoi_eliminare_blocco'), function(answer) {
                    //         if (answer) {
                    //           tinymce.activeEditor.windowManager.alert(translate.get('blocco_eliminato'));
                    //           var toDelete = getParent('personalizzazione', currentEditor);
                    //           toDeleteId = toDelete.attr('id');
                    //           tinyMCE.activeEditor.dom.remove(tinyMCE.activeEditor.dom.select('#'+toDeleteId));

                    //           } else {
                    //               ed.focus();
                    //           }
                    //       });
                    //   }, //onclick

                    // }); //removeblock


                   // ed.on('NodeChange', function(event) {
                   //        if(removeblock) {
                              
                   //            var result = getParent('personalizzazione', currentEditor);
                   //            $('#docCustom .personalizzazione').removeClass('selected');

                   //            // $(result).addClass('selected');
                   //            isParent = $(result).hasClass('personalizzazione');
                              
                   //            /*  
                   //            Ogni volta che viene selezionato un nodo verifica se si tratta del 
                   //            blocco dei contenuti della libreria.
                   //            In tal caso abilita il pulsante 'Elimina blocco'. */
                   //            removeblock.disabled( !(isParent == true) );
                   //        }
                   //  });



                    ed.addButton('formatp', {
                        title : translate.get('paragrafo'),
                        text : "P",
                        onclick : function() {
                          //editor.formatElement('p','Testo',ed);
                          editor.formatElement('p','',ed);
                        }
                    });


                    ed.addButton('formath2', {
                        title : translate.get('titolo'),
                        text : "H1",
                        onclick : function() {
                          //console.log(ed);
                          //editor.formatElement('h2','Titolo2_testo',ed);
                          editor.formatElement('h2','',ed);
                        }
                    });


                    ed.addButton('formath3', {
                        title : translate.get('sottotitolo'),
                        text : "H2",
                        onclick : function() {
                          //editor.formatElement('h3','Titolo3',ed);
                          editor.formatElement('h3','',ed);
                        }
                    });


                    ed.addButton('addimage', {
                        title : translate.get('aggiungi_figura'),
                        icon : "image",
                        onclick : function() {
                          // var fileUrl = '/themes/site_themes/epublishare/epublishare/application/assets/file_upload2.php';
                          var fileUrl = '/application/file_upload2';
                          var sel = ed.selection.getSel();

                          console.log('sel', sel);

                          //console.log(sel);
                          var element = sel.anchorNode.parentElement;                   

                          console.log('element', element);

                          var mediaElement = null;
                      //     if(!$(element).hasClass('fig_wrap')){
                      //       var parentElement = element;//element.parentNode;
                      //       while (parentElement.nodeType != 1) {
                      //   parentElement = parentElement.parentNode;
                      // }
                      //       while(!$(parentElement).hasClass('personalizzazione_inner')){
                      //         if($(parentElement).hasClass('fig_wrap')){
                      //           mediaElement = parentElement;
                      //           break;
                      //         }
                      //         parentElement = parentElement.parentNode;
                      //         while (parentElement.nodeType != 1) {
                      //     parentElement = parentElement.parentNode;
                      //   }
                      //       }                     
                      //     } else {
                            mediaElement = element;
                          // }

                          console.log('MEDIA ELEMENT');
                          console.log(mediaElement);

                          if(mediaElement){
                            editor.variables.currentElement = mediaElement;
                            var container =  $(mediaElement).attr('id');
                            fileUrl +='?c='+container;
                          }

                          console.log('fileUrl' , fileUrl);

                          ed.windowManager.open(
                                {
                                  title: translate.get('aggiungi_modifica_figura'),
                                  file : fileUrl,
                                  width: 440,
                                  height: 480,
                                  scrollbars : false,
                                  
                                  onsubmit: function(e) {    
                                      ed.focus();
                                  }
                                }, 

                                {
                                  custom: true
                                }

                              );
                        }
                    });

                        ed.addButton('removeimage', {
                        title : translate.get('rimuovi_figura'),
                        //image : "/themes/site_themes/epublishare/epublishare/application/assets/css/image-remove-icon.png",
                        text : translate.get('rimuovi'),
                        onclick : function() {
                          var sel = ed.selection.getSel();
                          var element = sel.anchorNode.parentElement;                   
                          var mediaElement = null;
                          if(!$(element).hasClass('fig_wrap')){
                            var parentElement = element.parentNode;
                            while (parentElement.nodeType != 1) {
                        parentElement = parentElement.parentNode;
                      }
                            while(!$(parentElement).hasClass('personalizzazione_inner')){
                              if($(parentElement).hasClass('fig_wrap')){
                                mediaElement = parentElement;
                                break;
                              }
                              parentElement = parentElement.parentNode;
                              while (parentElement.nodeType != 1) {
                          parentElement = parentElement.parentNode;
                        }
                            }                     
                          } else {
                            mediaElement = element;
                          }

                          if(mediaElement){
                            jQuery(mediaElement).remove();
                            ed.focus();
                          }
                        }
                    });

                    ed.addButton('addvideo', {
                        title : translate.get('aggiungi_video'),
                        //image : "/themes/site_themes/epublishare/epublishare/application/assets/css/youtube-icon.png",
                        icon : 'media',
                        onclick : function() {
                          // var fileUrl = '/themes/site_themes/epublishare/epublishare/application/assets/add_video.php';
                          var fileUrl = '/application/add_video/';
                          var sel = ed.selection.getSel();
                          var element = sel.anchorNode.parentElement;                   
                          var mediaElement = null;
                      //     if(!$(element).hasClass('video_youtube_wrapper')){
                      //       console.log('elemento selezionato non è container di video');
                      //       var parentElement = element;//element.parentNode;
                      //       while (parentElement.nodeType != 1) {
                      //   parentElement = parentElement.parentNode;
                      // }
                      // console.log(parentElement);
                      //       while(!$(parentElement).hasClass('personalizzazione_inner')){
                      //         if($(parentElement).hasClass('video_youtube_wrapper')){
                      //           mediaElement = parentElement;
                      //           break;
                      //         }
                      //         parentElement = parentElement.parentNode;
                      //         while (parentElement.nodeType != 1) {
                      //     parentElement = parentElement.parentNode;
                      //   }
                      //       }                     
                      //     } else {
                            mediaElement = element;
                      //     }
                          console.log(mediaElement);
                          if(mediaElement){
                            editor.variables.currentElement = mediaElement;
                            //var container =  $(mediaElement).data('idContainer');
                            var container =  $(mediaElement).attr('id');
                            fileUrl +='?c='+container;
                          }
                          
                          ed.windowManager.open(
                            {
                                title: translate.get('aggiungi_modifica_video'),
                                file : fileUrl,
                                width: 420,
                                height: 420,
                                scrollbars : false,
                                onsubmit: function(e) {    
                                    ed.focus();
                                    //ed.selection.setContent('<pre class="language-' + e.data.language + ' line-numbers"><code>' + ed.selection.getContent() + '</code></pre>');
                                }
                            },

                            {
                               custom: true
                            }

                            );
                        }
                    });

                    ed.addButton('removevideo', {
                        title : translate.get('rimuovi_video'),
                        //image : "/themes/site_themes/epublishare/epublishare/application/assets/css/youtube-remove-icon.png",
                        text : translate.get('rimuovi'),
                        onclick : function() {
                          var sel = ed.selection.getSel();
                          var element = sel.anchorNode.parentElement;
                          if(!$(element).hasClass('video_youtube_wrapper')){
                            var parentElement = element.parentNode;
                            while (parentElement.nodeType != 1) {
                        parentElement = parentElement.parentNode;
                      }
                            while(!$(parentElement).hasClass('personalizzazione_inner')){
                              if($(parentElement).hasClass('video_youtube_wrapper')){
                                mediaElement = parentElement;
                                break;
                              }
                              parentElement = parentElement.parentNode;
                              while (parentElement.nodeType != 1) {
                          parentElement = parentElement.parentNode;
                        }
                            }                     
                          } else {
                            mediaElement = element;
                          }

                          if(mediaElement){
                            jQuery(mediaElement).remove();
                            ed.focus();
                          } 
                        }
                    });

                    ed.addButton('addChemicalFormula', {
                        title : translate.get('aggiungi_formula'),
                        image : "/themes/site_themes/epublishare/epublishare/application/assets/js/Chemical_diagram_16.png",
                        //icon : 'media',
                        onclick : function() {
                          var fileUrl = '/themes/site_themes/epublishare/epublishare/application/assets/js/marvinjs/';
                          
                          var sel = ed.selection.getSel();
                          console.log(sel);
                          var element = sel.anchorNode;                   
                          var mediaElement = null;
                          if($(element).hasClass('ep_chemical_container')){
                            mediaElement = element;                   
                          } 
                          console.log(mediaElement);
                          if(mediaElement){
                            editor.variables.currentElement = mediaElement;
                            //var container =  $(mediaElement).data('idContainer');
                            var mrv =  $('img',mediaElement).attr('data-mrv');
                            fileUrl +='?mrv='+encodeURI(mrv);
                          }
                          
                          ed.windowManager.open({
                                title: translate.get('aggiungi_formula'),
                                file : fileUrl,
                                width: 620,
                                height: 600,
                                scrollbars : false,
                                onsubmit: function(e) {    
                                    ed.focus();
                                    //ed.selection.setContent('<pre class="language-' + e.data.language + ' line-numbers"><code>' + ed.selection.getContent() + '</code></pre>');
                                }
                            });
                        }
                    });


                    ed.addButton('addaudio', {
                        title : translate.get('aggiungi_nota_vocale'),
                        image : "/themes/site_themes/epublishare/epublishare/application/assets/img/microphone.png",
                        //icon : 'sound',
                        onclick : function() {
                          // var fileUrl = '/themes/site_themes/epublishare/epublishare/application/assets/add_audio.php';
                          var fileUrl = '/application/add_audio/';
                          var sel = ed.selection.getSel();
                          var element = sel.anchorNode.parentElement;                   
                          var mediaElement = null;
                          
                          
                          ed.windowManager.open({
                                title: translate.get('aggiungi_nota_vocale'),
                                file : fileUrl,
                                width: 500,
                                height: 500,
                                scrollbars : false,
                                onsubmit: function(e) {    
                                    ed.focus();
                                    //ed.selection.setContent('<pre class="language-' + e.data.language + ' line-numbers"><code>' + ed.selection.getContent() + '</code></pre>');
                                }
                            });
                        }
                    });




                  } // setup
                }); // tinymce


                function getParent(className, currentEditor) {
                  var nodes = [];
                  var element = currentEditor.selection.getNode();
                  var parentnode;

                  nodes.push(element);
                  while(element.parentNode) {
                      nodes.unshift(element.parentNode);
                      element = element.parentNode;
                  }

                  $.each(nodes, function(key, parent){
                      if ( $(parent).hasClass(className) ) {
                        parentnode = $(this);
                      }

                  }) 

                  return parentnode;

                }//getParent
  

            } // setCustomTinyMCE
        } // functions
    } // customChapter





} // end toc


