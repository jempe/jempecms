	function insert_tinymce_images(image_ids, thumb_type)
	{
		var content = "<p>";
		var image_content = "";
		
		for(var i = 0; i < image_ids.length; i++)
		{
			eval("image_content = image_" + image_ids[i] + "_" + thumb_type );
			
			content += '<img src="' + image_content.url + '?jempe_thumb_id=' + image_content.thumb_image + '_" width="' + image_content.thumb_width + '" height="' + image_content.thumb_height + '" />';
		}
		
		content += "</p>";
		
		insert_tinymce_content(content);
	}

	function insert_tinymce_content(content)
	{
		window.opener.jempe_tinymce.tinymce().execCommand("mceInsertContent", false, content);
		window.close();
	}
	
	function insert_image(image_id, image_src)
	{
		var selected_image_id = image_id;
		window.opener.selected_jempe_image_manager.find("input").val( selected_image_id);
		window.opener.selected_jempe_image_manager.find(".remove_image_button").attr("style" , "display:block;" );
		
		var d = new Date();
		window.opener.selected_jempe_image_manager.find(".jempe_image_manager_photo").attr("src", image_src + "?time=" + d.getTime());
		window.close();
	}
	
	function show_thumb_popup()
	{
		$.fancybox({
			"href" : "#select_thumb_popup"
		});
	}

	function insert_jempe_link(link_url)
	{
		window.opener.$("#href", window.opener.$("iframe:last").contents()).val(link_url);
		window.close();
	}