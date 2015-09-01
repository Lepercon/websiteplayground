(function($) {

	$.fn.editable = function(settings) {
		// if nothing is selected, return nothing; can't chain anyway
		if (!this.length) {
			options && options.debug && window.console && console.warn( "No editable content" );
			return;
		}
		var editors = [];
		$(this).each(function() {
			// check if a editor for this form was already created
			var editor = $.data(this, 'editable');
			if ( editor ) {
				editors.push(editor);
			}
			else {
				// create new editor
				editor = new $.editable(settings, this);
				// add to element data
				$.data(this, 'editable', editor);
				editors.push(editor);
			}
		});
		if(editors.length == 1) return editors[0];
		return editors;
	};
	
	$.fn.offsetRight = function() {
		return this.offsetParent().width() - this.outerWidth() - this.position().left;
	};
	
	$.fn.duplicate = function(count, cloneEvents) {
		var tmp = [];
		for ( var i = 0; i < count; i++ ) {
			$.merge( tmp, this.clone( cloneEvents ).get() );
		}
		return this.pushStack( tmp );
	};
	
	// constructor for editable
	$.editable = function( options, toEdit ) {
		this.settings = $.extend( true, {}, $.editable.defaults, options );
		this.currentlyEditing = toEdit;
		this.init();
	};
	
	$.editable.uniqueID = function(prefix) {
		prefix = prefix || 'uid';
		if(!$.editable.uniqueID.index[prefix]) {
			$.editable.uniqueID.index[prefix] = 1;
			if($('#' + prefix).size() == 0) return prefix;
		}
		var id = '';
		while(1) {
			id = prefix + '-' + ($.editable.uniqueID.index[prefix]++);
			if($('#' + id).size() == 0) break;
		}
		return id;
	};
	$.editable.uniqueID.index = [];
	
	// custom selectors
	$.extend($.expr[':'], {
		notparents : function(a,i,m){
			return ($(a).parents(m[3]).length < 1);
		}
	});
	
	$.extend($.editable, {
		// default settings
		defaults: {
			editableUrl : window.location.protocol + '//' + (window.location.protocol + '//' + window.location.host + window.location.pathname).substring((window.location.protocol + '//' + window.location.host + window.location.pathname).indexOf('://') + 3, (window.location.protocol + '//' + window.location.host + window.location.pathname).indexOf('/butler.jcr')) + '/butler.jcr/editable_c', // url to the editable server script
			pageName : '', // name of the page, used to build other urls
			saveFilePath : '' // specify a override for the file to write to
		},

		setDefaults: function(settings) {
			$.extend( $.editable.defaults, settings );
		},
		
		prototype: {
				// Things inside prototype are returned when calling the editable method on an element. These are actions specific to the element they are bound to, eg saving the edit.
				init: function() {
					var obj = this;
					this._editing = false; // set the editing property
					
					this._selectedImage = null; // set the selected image property
					this.setupImageTools(); // setup image tools
					this.createEditBar.call(this); // EDIT BAR
					this.setupUndo.call(this); // UNDO SETUP
					
					// css changes
					$(this.currentlyEditing).addClass('editable-not-editing');
					if($(this.currentlyEditing).height() < 100) $(this.currentlyEditing).css('min-height', '100px');
					
					// SELECTION
					$(this.currentlyEditing).mouseup(function() {
						$.editable.updateSelection.call(obj);
					});
					
					if(typeof editableShowImageInsert != 'undefined') {
						obj.editFunctions.showImageInsert.call(obj);
						editableShowImageInsert = undefined;
					}
					if(typeof editableShowDocInsert != 'undefined') {
						obj.editFunctions.showDocInsert.call(obj);
						editableShowDocInsert = undefined;
					}
				},
				
				destructor : function() {
					$(this.editbar).remove();
				},
				
				setupUndo : function() {
					var obj = this;
					this.undo = []; // create undo array
					// bind to content's change event
					$(this.currentlyEditing).change(function() {
						// change is called on every change (ie every character) so a timeout is required to only record changes when the user stops editing.
						if(obj.undoTimer) clearTimeout(obj.undoTimer);
						obj.undoTimer = setTimeout(function() { obj.undo.unshift(obj.currentlyEditing.innerHTML); }, 1500);
					});
					// change event doesn't actually exist.  Work around this by using other events
					var before;
					$(this.currentlyEditing).focus(function() {
						before = $(this).html();
					}).bind('keypress paste', function() { 
						if (before != $(this).html()) {
							before = $(this).html();
							$(this).change();
						}
					});
				},
				
				setupImageTools : function() {
					var obj = this;
					// add click handler
					$(this.currentlyEditing).on('click', 'img', function(){
						if(!obj._editing) return false; // return if not editing
						if(!obj._selectedImage) obj.createImageBar.call(obj); // if no image was previously selected add image editing bar
						// check if image has changed from previous selection
						if(this != obj._selectedImage){
							// bind a click event to the document to perform deselection actions
							$(document).one('click', function(){
								obj._selectedImage = null;
								obj.removeImageBar.call(obj);
							});
							obj._selectedImage = this; // change the selected image
						}
					});
				},
				
				createEditBar : function() {
					var obj = this;
					// get an id for the edit bar
					this.editID = $.editable.uniqueID('editable-edit-bar');
					// create the edit bar
					$(obj.currentlyEditing).before(
						$('<div />', { 'id': this.editID, 'class': 'editable-edit-bar editable-icons-black' })
					).addClass('editable-not-editing');
					// get the DOM element
					this.editBar = document.getElementById(this.editID);
					// create the edit button
					this.createEditableButton.call(this);
					
					///// positioning and scroll behaviour
					var editBarPositioning = this.getEditBarPositioning.call(this);
					// position at top right corner of content
					$(this.editBar).css({ 'right' : editBarPositioning.right, 'top' : editBarPositioning.top});
					this.editBarPositionFixed = false;
					$(window).scroll(function(e) {
						// only when scrolling
						if(obj._editing) {
							var scrollTop = $(window).scrollTop();
							if(scrollTop > editBarPositioning.offsetTop){
								if(!obj.editBarPositionFixed) {
									// first time scroll has moved below bar
									obj.scroll.fixedPos.call(obj, editBarPositioning);
								}
							}
							else if(obj.editBarPositionFixed){
								// first time scroll has moved back above content
								obj.scroll.absolutePos.call(obj, editBarPositioning);
							}
						}
					});
					
					// generate an array of edit bar items
					this.editBarItems = 'style|bold|italic|underline|list|link|img|doc|functions|undo'.split('|');
					// add save and cancel to the end if they don't exist
					if($.inArray(this.editBarItems, 'save') === -1) this.editBarItems.push('save');
					if($.inArray(this.editBarItems, 'cancel') === -1) this.editBarItems.push('cancel');
				},
				
				scroll: {
					fixedPos : function(editBarPositioning) {
						var obj = this;
						$(this.editBar).css({'position' : 'fixed', 'top' : 0, 'right' : editBarPositioning.offsetRight});
						this.editBarPositionFixed = true;
					},
					absolutePos : function(editBarPositioning) {
						$(this.editBar).css({'position' : 'absolute', 'top' : editBarPositioning.top, 'right' : editBarPositioning.right}).unbind('mouseenter mouseleave');
						this.editBarPositionFixed = false;
					}
				},
				
				getEditBarPositioning: function() {
					var tmpOffset = $(this.currentlyEditing).offset();
					var editBarPositioning = {
						'top' : $(this.currentlyEditing).position().top - $(this.editBar).outerHeight(),
						'right' : $(this.currentlyEditing).offsetRight(),
						'offsetTop' : tmpOffset.top - $(this.editBar).outerHeight(),
						'offsetRight' : $(window).width() - (tmpOffset.left + $(this.currentlyEditing).outerWidth())
					};
					var obj = this;
					$.each(editBarPositioning, function(i,v) {
						if(v < 0) {
							// there is not enough space for the bar!  make the space required by adding margins.
							$(obj.currentlyEditing).css('margin-'+i, -v);
							editBarPositioning[i] = 0;
						}
					});
					return editBarPositioning;
				},
				
				genEditBarButton: function(cls, title, click, icon, html, addClass) {
					addClass = addClass ? ' '+addClass : '';
					var but = $('<button />', { 'class' : 'editable-button editable-'+cls+addClass, 'click' : click, 'title' : title });
					if(html) but.html(html);
					if(icon) but.append($('<div />', { 'class' : 'editable-button-icon' }));
					return but;
				},
				
				genEditBarItem: function(itemName) {
					var obj = this;
					switch(itemName) {
						case 'style':
							var opt = function(type) {
								if(type == 'p') var text = 'Paragraph';
								else var text = 'Heading '+type.substring(1,2);
								return $('<option />', {'value': type}).text(text);
							};
							return $('<select />', { 'change' : function() { obj.execCommand.call(obj,'formatBlock', this.value); } })
								.append(opt('p')).append(opt('h1')).append(opt('h2')).append(opt('h3'));
						case 'bold':
							return this.genEditBarButton('bold', 'Bold', function() { obj.editFunctions.stdFunc.call(obj, 'bold'); }, false, 'B');
						case 'italic':
							return this.genEditBarButton('italic', 'Italic', function() { obj.editFunctions.stdFunc.call(obj, 'italic'); }, false, 'I');
						case 'underline':
							return this.genEditBarButton('underline', 'Underline', function() { obj.editFunctions.stdFunc.call(obj, 'underline'); }, false, 'U');
						case 'list':
							return this.genEditBarButton('list', 'Convert to list', function() { obj.editFunctions.stdFunc.call(obj, 'insertUnorderedList'); }, true);
						case 'link':
							return this.genEditBarButton('link', 'Insert link', function() { obj.editFunctions.createLink.call(this, obj); }, true);
						case 'img':
							return this.genEditBarButton('img', 'Insert image', function() { obj.editFunctions.createImg.call(obj); }, true);
						case 'doc':
							return this.genEditBarButton('doc', 'Insert document', function() { obj.editFunctions.createDoc.call(obj); }, true);
						case 'functions':
							return this.genEditBarButton('functions', 'Insert function call', function() { obj.editFunctions.createFunc.call(obj); }, false, '{}');
						case 'undo':
							return this.genEditBarButton('undo', 'Undo', function() { obj.editFunctions.undo.call(obj); }, true);
						case 'save':
							return this.genEditBarButton('save', 'Save', function() { obj.save.call(obj); }, false, '&#x2714;');
						case 'cancel':
							return this.genEditBarButton('cancel', 'Cancel', function() { obj.cancel.call(obj); }, false, '&#x2718;');
						case 'edit':
							return this.genEditBarButton('edit', 'Edit this page', function() { obj.edit.call(obj); });
						default:
							break;
					}
				},
				
				createEditableButton : function() {
					var obj = this;
					$(this.editBar).html(obj.genEditBarItem('edit'));
				},
				
				createEditingBar: function() {
					var obj = this;
					var eb = $();
					$(obj.editBarItems).each(function(i, val) {
						eb = eb.add(obj.genEditBarItem.call(obj, val));
					});
					$(this.editBar).html(eb);
				},
				
				createImageBar: function() {
					var obj = this;
					var eb = $();
					var clickFunc = function(action) {
						var img = $(obj._selectedImage);
						img.attr('style', '');
						switch(action) {
							case 'float-left':
								img.css({'float':'left', 'clear':'left', 'margin':'10px 10px 10px 0'});
								break;
							case 'float-right':
								img.css({'float':'right', 'clear':'right', 'margin':'10px 0 10px 10px'});
								break;
							case 'central':
								img.css({'margin': '10px auto', 'display': 'block'});
								break;
							case 'inline':
								img.css({'float':'none', 'clear':'none', 'margin':'5px'});
								break;
						}
						obj.updateUndo.call(obj);
					};
					var items = ['float-left', 'float-right', 'central', 'inline'];
					var titles = ['Align left, wrap to right', 'Align right, wrap to left', 'Align central, no wrapping', 'Inline with text'];
					$.each(items, function(i,val){
						eb = eb.add(obj.genEditBarButton('image-'+val, titles[i], function(){ clickFunc(val); }, true, null, 'editable-image-item'));
					});
					eb = eb.add($('<div />', { 'class' : 'editable-image-item editable-vertical-seperator' }));
					$(this.editBar).prepend(eb);
				},
				
				removeImageBar: function() {
					$(this.editBar).find('.editable-image-item').remove();
				},
				
				updateUndo : function() {
					$(this.currentlyEditing).change();
				},
				
				execCommand : function(command, params) {
					this.handleReselection();
					document.execCommand(command, false, params);
					this.selection = $.editable.fetchSelection();
					this.updateUndo.call(this);
				},
				
				handleReselection: function() {
					if(this.selection) $.editable.restoreSelection(this.selection);
					else this.selection = $.editable.createSelection(this.currentlyEditing);
					return document;
				},
				
				edit: function() {
					this.prevContent = this.currentlyEditing.innerHTML;
					this.createEditingBar.call(this);
					$(this.currentlyEditing).removeClass('editable-not-editing').addClass('editable-editing editable-black');
					this.undo[0] = this.currentlyEditing.innerHTML;
					this.currentlyEditing.contentEditable = true;
					try {
						document.execCommand("styleWithCSS", 0, false);
					} catch (e) {
						try {
							document.execCommand("useCSS", 0, true);
						} catch (e) {
							try {
								document.execCommand('styleWithCSS', false, false);
							} catch (e) {}
						}
					}
					this._editing = true;
				},
				
				save: function() {
					var obj = this;
					$.post(this.settings.editableUrl + '/save_page/' + this.settings.pageName, {
						'content' : this.currentlyEditing.innerHTML,
						'save_page' : true,
						'file_path' : this.settings.saveFilePath
					}, function(data) {
						if(typeof data != 'object') {
							console.log(data);
							return;
						}
						if(data.success) {
							if(data.replaceWith) {
								obj.currentlyEditing.innerHTML = data.replaceWith;
							}
							obj.stopEditing.call(obj, true);
						}
						$(obj.editBar).find('.editable-save').html('&#x2714;');
					}, 'json');
					$(this.editBar).find('.editable-save').html('<img src="'+window.location.protocol + '//' + (window.location.protocol + '//' + window.location.host + window.location.pathname).substring((window.location.protocol + '//' + window.location.host + window.location.pathname).indexOf('://') + 3, (window.location.protocol + '//' + window.location.host + window.location.pathname).indexOf('/butler.jcr')) + '/butler.jcr/application/views/common/editable/icons/loader.gif" />');
				},
				
				cancel: function() {
					this.currentlyEditing.innerHTML = this.prevContent;
					this.stopEditing.call(this);
				},
				
				stopEditing: function(saved) {
					this.createEditableButton.call(this);
					this.undo = [];
					$(this.currentlyEditing).addClass('editable-not-editing').removeClass('editable-editing');
					this.currentlyEditing.contentEditable = false;
					$.editable.deselect(); // sometimes cursor hangs around
					this._editing = false;
					// reposition edit bar
					var editBarPositioning = this.getEditBarPositioning.call(this);
					// position at top right corner of content
					this.scroll.absolutePos.call(this, editBarPositioning);
				},
				
				editing: function() {
					return this._editing;
				},
				
				// EDIT FUNCTIONS
				
				editFunctions: {
					stdFunc: function(func) {
						this.execCommand.call(this, func);
					},
					
					undo: function() {
						if(this.undo[1]) {
							this.currentlyEditing.innerHTML = this.undo[1];
							this.undo.shift();
						}
					},
					
					createImg: function(showSecondTab) {
						var obj = this;
						var url;
						$('<div />', {
							'class' : 'editable-image-create'
						}).dialog({
							title: 'Insert Image',
							resizable: false,
							modal: true,
							width: 450,
							open: function(event, ui) {
								var dialog = $(this);
								$('.editable-image-create').append('<h3>Enter URL</h3><div class="editable-image-create-inserturl"></div><h3>Upload Image</h3><div class="editable-image-create-content"></div>').accordion({
									collapsible : true,
									heightStyle: "content",
									create: function(event, ui) {
										$('.editable-image-create-inserturl').append(
											$('<input />', { 'type' : 'text', 'value' : 'http://' }).focus()
											.add(
												$('<input />', { 'type' : 'button', 'value' : 'Insert' })
												.click(function() {
													var input = this;
													url = $(this).siblings('input').val();
													var img = new Image();
													img.onerror = function() {
														$(input).siblings('p').addClass('red').text('Invalid Image');
													};
													img.onload = function() {
														dialog.dialog("close");
													};
													if($(this).siblings('p').size() == 0) $(this).parent().prepend($('<p>Checking...</p>'));
													else $(this).siblings('p').text('Checking...').removeClass('red');
													img.src = url;
												})
											)
										);
										
										var new_spinner = $('#spinner').clone().attr('id', 'dialog-spinner').show();
										$('.editable-image-create-content').html(new_spinner);
										
										$.ajax({
											dataType: "html",
											url: obj.settings.editableUrl + '/load_page_images/' + obj.settings.pageName,
											success : function(data) {
												$('.editable-image-create-content').html(data);
												$('.editable-image-create-addexisting li').click(function(){
													dialog.dialog("close");
													obj.execCommand.call(obj,'insertImage', $(this).find('img').attr('src').replace('_thumb', ''));
												});
												// image delete click
												$('.editable-image-create-remove').click(function(e){
													e.stopPropagation();
													var thumbSrc = $(this).siblings('img').attr('src');
													var src = thumbSrc.replace('_thumb', '');
													var img = $(this).siblings('img').clone();
													var wrapper = $(this).parents('.editable-image-create-content');
													wrapper.css('height',wrapper.height()).html(
														$('<div />', { 'class' : 'editable-image-create-delete-confirm' })
														.append(img)
														.append($('<br /><p>Are you sure you want to delete this image?</p>'))
														.append($('<button />', { 'click':function() {
															var button = this;
															$(document).one('editable-delete-finished', function() {
																$(obj.currentlyEditing).find('img[src="'+src+'"]').remove();
																$(button).parents('.editable-image-create-content').html(secondTab).find('img[src="'+thumbSrc+'"]').closest('li').remove();
																//secondTab = $(button).parents('.editable-image-create-content').html();
															});
															$.post(obj.settings.editableUrl + '/delete_image/' + obj.settings.pageName, {'image':src.substring(src.lastIndexOf('/')+1)}, function(){
																$(document).trigger('editable-delete-finished');
															});
														}}).text('Delete'))
														.append($('<button />', { 'click':function() {
															$(this).parents('.editable-image-create-content').html(secondTab);
															bind();
														}}).text('Cancel'))
													);
												});
												// if show second tab, switch to second tab
												if(showSecondTab) $('.editable-image-create').accordion({active: 1});
											}
										});
									}
								});
							},
							close: function(event, ui) {
								$(this).dialog('destroy').remove();
							}
						});
					},
					
					// end createImg
					
					showImageInsert: function() {
						this.edit.call(this);
						this.editFunctions.createImg.call(this, true);
					},
					
					createDoc: function() {
						var obj = this;
						$('<div />', {
							'class' : 'editable-doc-add'
						}).dialog({
							title: 'Insert Document',
							resizable: false,
							modal: true,
							width: 450,
							open: function(event, ui) {
								var new_spinner = $('#spinner').clone().attr('id', 'dialog-spinner').show();
								$('.editable-doc-add').html(new_spinner);
								var dialog = $(this);
								$.ajax({
									dataType: "html",
									url: obj.settings.editableUrl + '/load_page_docs/' + obj.settings.pageName,
									success : function(data) {
										$('.editable-doc-add').html(data);
										// Doc select click
										$('.editable-doc-create li').click(function(){
											dialog.dialog("close");
											url = $(this).find('.editable-doc-create-url').text();
											file = $(this).find('.editable-doc-create-full-filename').text();
											
											obj.handleReselection.call(obj);
											$.editable.insertNodeAtSelection($('<a />', { 'class' : 'no-jsify', 'href' : url }).text(file)[0]);
											obj.updateUndo.call(obj);
										});
										// Doc delete click
										$('.editable-doc-create-remove').click(function(e){
											e.stopPropagation();
											var url = $(this).siblings('.editable-doc-create-details').children('.editable-doc-create-url').text();
											var docName = $(this).siblings('.editable-doc-create-details').children('.editable-doc-create-full-filename').text();
											var doc = $(this).parent().children().not('editable-doc-create-remove').clone(false);
											var wrapper = $(this).closest('.editable-doc-add');
											var contents = wrapper.children().detach();
											wrapper.html(
												$('<div />', { 'class' : 'editable-doc-create-delete-confirm' })
												.append(doc)
												.append($('<br /><p>Are you sure you want to delete this document?</p>'))
												.append($('<button />', { 'click':function() {
													var button = this;
													$.post(obj.settings.editableUrl + '/delete_doc/' + obj.settings.pageName, {'doc':docName}, function(){
														$(obj.currentlyEditing).find('a[href="'+url+'"]').remove();
														$(button).parents('.editable-doc-add').html(contents).find('div.editable-doc-create-url:contains('+url+')').closest('li').remove();
													});
												}}).text('Delete'))
												.append($('<button />', { 'click':function() {
													$(this).parents('.editable-doc-add').html(contents);
												}}).text('Cancel'))
											);
										});
									}
								});
							},
							close: function(event, ui) {
								$(this).dialog('destroy').remove();
							}
						})
					},
					// end createDoc
					
					showDocInsert: function() {
						this.edit.call(this);
						this.editFunctions.createDoc.call(this);
					},
					
					createLink: function(obj) {
						$('<input />', {
							'type' : 'text',
							'id' : 'editable-link-entry',
							'val' : 'http://',
							'placeholder' : 'URL',
							'style' : 'width: 100%; padding: 0px;'
						}).dialog({
							resizable: false,
							modal: true,
							title: "Enter URL",
							width: 400,
							buttons: {
								"Insert": function () {
									var url = $('#editable-link-entry').val();
									obj.execCommand.call(obj,'createLink', url);
									obj.updateUndo.call(obj);
									$(this).dialog("close");
								}
							},
							open: function(event, ui) {
								// Handle insert on enter key press
								$('#editable-link-entry').keypress(function(e) {
									if (e.keyCode == $.ui.keyCode.ENTER) {
										$(this).parent().find("button:eq(1)").trigger("click");
									}
								})
							},
							close: function(event, ui) {
								$(this).dialog('destroy').remove();
							}
						});
						$('#editable-link-entry').focus();
					},
					
					createFunc: function() {
						var obj = this;
						var result;
						
						$('<ul />', {
							'class' : 'editable-function-insert-params'
						}).dialog({
							title: 'Insert Contact Link',
							resizable: false,
							modal: true,
							width: 400,
							buttons: {
								"Insert" : function() {
									var params = [];
									$('.editable-function-insert-params li').each(function() {
										var input = $(this).find('input, select');
										var param = result['get_exec_contact'].params[input.attr('name')];
										var val = (param.type == 'bool' ? (input.attr('checked') ? 1 : 0) : input.val());
										params.push('\''+val+'\'');
									});
									
									obj.handleReselection.call(obj);
									$.editable.insertNodeAtSelection('{get_exec_contact: '+params.join(', ')+'}');
									obj.updateUndo.call(obj);
									$(this).dialog("close");
								}
							},
							open : function(event, ui) {
								var new_spinner = $('#spinner').clone().attr('id', 'dialog-spinner').show();
								$('.editable-function-insert-params').before(new_spinner);
								$.getJSON(obj.settings.editableUrl + '/get_page_functions/' + obj.settings.pageName, function(data) {
									result = data;
									var get_ip = function(param, type, options) {
										if(type == 'bool') return $('<input />', { 'id' : 'editable-contact-link-' + param, 'type' : 'checkbox', 'name' : param, 'value' : '1' });
										if(type == 'select') return $('<select />', { 'id' : 'editable-contact-link-' + param, 'name' : param }).append(options);
									};
									var paramsList = $();
									$.each(result['get_exec_contact'].params, function(param) {
										if(typeof this.options == 'undefined') this.options = null;
										paramsList = paramsList.add(
											$('<li />').append($('<label />', {'for' : 'editable-contact-link-' + param}).html(this.desc)).append(get_ip(param, this.type, this.options))
										);
									});
									$('#dialog-spinner').remove();
									$('.editable-function-insert-params').html(paramsList);
								});
							},
							close: function(event, ui) {
								$(this).dialog('destroy').remove();
							}
						});
					} // end createFunc
				} // end edit functions
		}, // end prototype
		
		// create a collapsed selection at the beginning of the element
		createSelection : function(element) {
			var range = rangy.createRange();
			range.setStart(element, 0);
			range.setEnd(element, 0);
			// perform the selection
			var sel = rangy.getSelection();
			sel.removeAllRanges();
			sel.addRange(range);
			sel.collapseToStart();
			
			return $.editable.fetchSelection();
		},
		
		fetchSelection : function() {
			return rangy.getSelection().getRangeAt(0).cloneRange();
		},
		
		restoreSelection : function(selection) {
			var sel = rangy.getSelection();
			sel.removeAllRanges();
			sel.addRange(selection);
		},
		
		updateSelection : function() {
			this.selection = $.editable.fetchSelection();
		},
		
		insertNodeAtSelection : function(node) {
			if(typeof node == 'string') node = document.createTextNode(node);
			rangy.getSelection().getRangeAt(0).insertNode(node);
		},
		
		deselect : function() {
			rangy.getSelection().removeAllRanges();
		}
	});
})(jQuery);