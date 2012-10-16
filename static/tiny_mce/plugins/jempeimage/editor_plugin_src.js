/**
 * $Id: editor_plugin_src.js 677 2008-03-07 13:52:41Z spocke $
 *
 * @author Sucio Kastro
 */

(function() {
	tinymce.create('tinymce.plugins.JempeImagePlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceJempeImage', function() {
				// Internal image object like a flash placeholder
				if (ed.dom.getAttrib(ed.selection.getNode(), 'class').indexOf('mceItem') != -1)
					return;
				ed.windowManager.open({
					file : ed.getParam("jempe_image_url", ""),
					width : 950,
					height : 600,
					inline : 1,
					scrollbars : "yes"
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('jempeimage', {
				title : 'advimage.image_desc',
				cmd : 'mceJempeImage'
			});
		},

		getInfo : function() {
			return {
				longname : 'Jempe Image Editor',
				author : 'Jempe',
				authorurl : 'http://jempe.org',
				infourl : 'http://jempe.org',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('jempeimage', tinymce.plugins.JempeImagePlugin);
})();