/**
 * Button script for the "Icon Fonts" Wordpress plugin
 * iconFonts object loaded by "icon-fonts.php"
 */
 
(function() {
	
	// Create plugin
	tinymce.create( 'tinymce.plugins.icon_fonts', {
	
		init: function( editor, url )  {

			// Add toolbar button
			editor.addButton( 'icon_fonts', {
				cmd: 'icon_fonts_command',
				icon: 'icon-fonts',
				tooltip: 'Insert Icon',
			});
			
			// Add button command
			editor.addCommand( 'icon_fonts_command', function() {
			
				// Open dialog window 
				var win = editor.windowManager.open({
					title: 'Insert Icon',
					height: 100,
					width: 300,
					inline: 1,
					id: 'icon-fonts-dialog',
					body: [{
						type: 'listbox',
						name: 'iconFont',
						label: 'Font',
						values: iconFonts.Fonts,
						onPostRender: function() {
							this.value( iconFonts.currentFont );
						},
						onSelect: function() {
							iconFonts.currentFont = this.value();
							var listbox = win.find( '#iconName' )[ 0 ];
							if( listbox.menu ){
								listbox.menu.remove();
								listbox.menu = null;
							}							
							listbox.settings.values = listbox.settings.menu = iconFonts.Names[ iconFonts.currentFont ];
							listbox.value( iconFonts.Names[ iconFonts.currentFont ][ 0 ].value );
						}
					},{
						type: 'listbox',
						name: 'iconName',
						label: 'Icon',
						values: iconFonts.Names[ iconFonts.currentFont ],
						onPostRender: function() {
							this.value( iconFonts.currentName );
						}
					}],
					onsubmit: function(e) {
						iconFonts.currentFont = e.data.iconFont;
						iconFonts.currentName = e.data.iconName;
						// Build class attribute
						var iconClass = iconFonts.currentName;
						var fontClass = iconFonts.Classes[ iconFonts.currentFont ];
						if( '' != fontClass ) {
							iconClass = fontClass + ' ' + iconClass;
						}
						iconClass = ' class="' + iconClass + '"';
						// Build style attribute
						var iconStyle = iconFonts.Styles;
						if ( '' != iconStyle ) {
							iconStyle = ' style="' + iconStyle + '"';
						}
						// Build icon element
						var iconHTML = '<span' + iconClass + iconStyle + ' aria-hidden="true" role="presentation">&shy;</span>';
						editor.insertContent( iconHTML );
					}
				});

			});
		}
	});
	
	// Add plugin
	tinymce.PluginManager.add( 'icon_fonts', tinymce.plugins.icon_fonts );
	
})();