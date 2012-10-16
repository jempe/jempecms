/**
 * jempe_form.js v0.9.8
 * Javascript helper for jempe_form library
 * }}}
 */

function jempe_show_date_picker(date_field, options_var)
	{
		if(date_field.hasClass("hasDatepicker") == false)
		{
			try
			{
				eval("var picker_options = "+options_var+";");
	
				if(picker_options != false)
				{
					date_field.datepicker(picker_options);
				}
				else
				{
					date_field.datepicker();
				}
			}
			catch(err)
			{
				date_field.datepicker();
			}
	
			setTimeout(function()
			{
				date_field.trigger("focus");
			}, 100);
		}
	}

(function($){
    
    // jQuery autoGrowInput plugin by James Padolsey
    // See related thread: http://stackoverflow.com/questions/931207/is-there-a-jquery-autogrow-plugin-for-text-fields
        
        $.fn.autoGrowInput = function(o) {
            
            o = $.extend({
                maxWidth: 1000,
                minWidth: 0,
                comfortZone: 70
            }, o);
            
            this.filter('input:text').each(function(){
                
                var minWidth = o.minWidth || $(this).width(),
                    val = '',
                    input = $(this),
                    testSubject = $('<tester/>').css({
                        position: 'absolute',
                        top: -9999,
                        left: -9999,
                        width: 'auto',
                        fontSize: input.css('fontSize'),
                        fontFamily: input.css('fontFamily'),
                        fontWeight: input.css('fontWeight'),
                        letterSpacing: input.css('letterSpacing'),
                        whiteSpace: 'nowrap'
                    }),
                    check = function() {
                        
                        if (val === (val = input.val())) {return;}
                        
                        // Enter new content into testSubject
                        var escaped = val.replace(/&/g, '&amp;').replace(/\s/g,'&nbsp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                        testSubject.html(escaped);
                        
                        // Calculate new width + whether to change
                        var testerWidth = testSubject.width(),
                            newWidth = (testerWidth + o.comfortZone) >= minWidth ? testerWidth + o.comfortZone : minWidth,
                            currentWidth = input.width(),
                            isValidWidthChange = (newWidth < currentWidth && newWidth >= minWidth)
                                                 || (newWidth > minWidth && newWidth < o.maxWidth);
                        
                        // Animate width
                        if (isValidWidthChange) {
                            input.width(newWidth);
                        }
                        
                    };
                    
                testSubject.insertAfter(input);
                
                $(this).bind('keyup keydown blur update', check);
                
            });
            
            return this;
        
        };
        
    })(jQuery);

	// jempe inline_editor functions start

	var jempe_edit_timeout    = 1000;
	var jempe_edit_closetimer = 0;
	var jempe_edit_item = 0;
	var jempe_previous_background = "transparent";

	function jempe_show_edit_link(site_element)
	{
		jempe_cancel_timer();
		jempe_edit_button_close();
		var jempe_element_pos = site_element.position();
		jempe_edit_item = site_element;

		var jempe_top_position = site_element.position();
		var jempe_edit_top = jempe_top_position.top - site_element.find(".jempe_edit_button").height() - 3;
		var jempe_edit_left = jempe_top_position.left;

		site_element.find(".jempe_edit_button").show().css( "top" , jempe_edit_top + "px" ).css("left" , jempe_edit_left + "px" );
		site_element.attr("id", "jempe_selected_edit_box");
	}

	function jempe_edit_button_close()
	{  
		if(jempe_edit_item){
			jempe_edit_item.removeAttr("id");
			jempe_edit_item.find(".jempe_edit_button").hide();
		}
	}
	
	function jempe_edit_timer()
	{  
		jempe_edit_closetimer = window.setTimeout(jempe_edit_button_close, jempe_edit_timeout);
	}
	
	function jempe_cancel_timer()
	{  
		window.clearTimeout(jempe_edit_closetimer);
		jempe_edit_closetimer = null;
	}
	function jempe_show_edit_field(site_element)
	{
		$("#jempe_edit_frame").html("");

		jempe_edit_button_close();
		var iframe_pos = site_element.offset();
		var jempe_iframe_width = site_element.width();
		var jempe_iframe_height = site_element.height();
		
		if(jempe_iframe_width < 200)
		{
			jempe_iframe_width = 200;
		}

		if(jempe_iframe_height < 20)
		{
			jempe_iframe_height = 20;
		}

		var buttons_top = iframe_pos.top + jempe_iframe_height +2;

		$("#jempe_edit_frame").show().addClass("jempe_loading").css("left", iframe_pos.left+"px");
		$("#jempe_edit_frame").css("top", iframe_pos.top+"px");
		$("#jempe_edit_frame").width(jempe_iframe_width);
		$("#jempe_edit_frame").height(jempe_iframe_height);

		$.ajax({
			url: jempe_inline_editor_url,
			timeout: 10000,
			type : "post",
			dataType : "html",
			data: 
			{
				"field_name" : site_element.attr("title"),
				"structure_id" : site_element.attr("rel")
			},
			success: function(html)
			{
				$("#jempe_edit_buttons").show().css("left", iframe_pos.left+"px");
				$("#jempe_edit_buttons").css("top", buttons_top+"px");

				$("#jempe_edit_frame").append(html);
				window.setTimeout(function()
				{
					$("#jempe_edit_frame").find("textarea, input, select").css({
						fontSize: site_element.css('fontSize'),
						fontFamily: site_element.css('fontFamily'),
						fontWeight: site_element.css('fontWeight'),
						letterSpacing: site_element.css('letterSpacing'),
						"height": jempe_iframe_height + "px"
					});
					if($("#jempe_edit_frame textarea[title=htmlarea]").length > 0)
					{
						eval("var htmlarea_config = " + $("#jempe_edit_frame textarea[title=htmlarea]").attr("rel"));

						htmlarea_config.theme_advanced_toolbar_location = "external";
						htmlarea_config.width = jempe_iframe_width;
						htmlarea_config.height = jempe_iframe_height;

						$("#jempe_edit_frame textarea[title=htmlarea]").tinymce(htmlarea_config);
					}
				}, 100);
			},
			error: function(request,error)
			{
				if (error == "timeout")
				{
					alert(jempe_timeout_error);
				}
				else 
				{
					alert("ERROR: " + error);
				}
			}
		});

		return false;
	}

	function jempe_edit_cancel()
	{
		$("#jempe_edit_buttons").hide();
		$("#jempe_edit_frame").html("").hide();
	}

	function jempe_get_form_values(jempe_form)
	{
		form_fields = new Object;

		jempe_form.find("input").each(function()
		{
			try{
				var field_name = $(this).attr("name");
				var field_value = $(this).val();
	
				if($(this).attr("type") == "checkbox")
				{
					if(field_name.indexOf("["))
					{
						// TODO process array
					}
					else
					{
						if($(this).is(":checked"))
						{
							eval("form_fields." +  field_name + " = field_value");
						}
						else
						{
							eval("form_fields." +  field_name + " = null");
						}
					}
				}
				else if($(this).attr("type") == "radio")
				{
					if($(this).is(":checked"))
					{
						eval("form_fields." +  field_name + " = field_value");
					}
				}
				else
				{
					eval("form_fields." +  field_name + " = field_value");
				}
			}
			catch(error)
			{

			}
		});

		jempe_form.find("select, textarea").each(function()
		{
			var field_name = $(this).attr("name");
			var field_value = $(this).val();

			eval("form_fields." +  field_name + " = field_value");
		});

		return form_fields;
	}

	function jempe_save_field()
	{
		var form_fields = jempe_get_form_values($("#form_inline_editor"));

		$.ajax({
			url: jempe_inline_save_url,
			dataType: "xml",
			timeout: 10000,
			type : "post",
			data: form_fields,
			success: function(xml){
				if($("error", xml).length > 0)
				{
					var error_message = $("error", xml).text();
					if(error_message.length > 0)
					{
						alert(error_message);
					}
					else if(parseFloat($("success", xml).text()))
					{
						window.location.reload();
					}
				}
				else
				{
					alert(jempe_error_try_again);
				}
			},
			error: function(request,error) {
				if (error == "timeout")
				{
					alert(jempe_timeout_error);
				}
				else 
				{
					alert("ERROR: " + error);
				}
			}
		});

		return false;
	}

	function convert_to_drop_down_lists(field_name, available_id, selected_id)
	{
		$("#" + available_id + ", #" + selected_id).sortable({
		connectWith: ".jempe_drag_and_drop_" + field_name,
		update: function(event, ui) {
			if(ui.item.parent().attr("id") == selected_id)
			{
				if(ui.item.find("input").size() == 0)
				{
					ui.item.append("<input type=\'hidden\' name=\'" + field_name + "[]\' value=\'" + ui.item.attr("rel") + "\' />");
				}
			}
			else
			{
				ui.item.find("input").remove();
			}

			$("#" + available_id + " li").sortElements(function(a, b){
				return $(a).text() > $(b).text() ? 1 : -1;
			});

			$("#" + selected_id + " li").sortElements(function(a, b){
				return $(a).text() > $(b).text() ? 1 : -1;
			});
		}
		}).disableSelection();
	}

	function jempe_dd_select_all(available_id, selected_id, field_name)
	{
		$("#" + available_id).find("li").each(function ()
		{
			$(this).append("<input type=\'hidden\' name=\'" + field_name + "[]\' value=\'" + $(this).attr("rel") + "\' />");
		});
		$("#" + available_id).find("li").appendTo("#" + selected_id);
	}

	function jempe_dd_remove_all(available_id, selected_id)
	{
		$("#" + selected_id).find("li input").remove();
		$("#" + selected_id).find("li").appendTo("#" + available_id);
	}

	// jempe inline_editor functions end



function isViewableVertical(item)
{
	var page_scrollTop = $(window).scrollTop();
	var page_view = page_scrollTop + $(window).height();
	
	var TopPosition = item.offset().top;
	var BottomPosition = TopPosition + item.height();

	

	if((BottomPosition >= page_view) && (TopPosition <= page_view) && (BottomPosition <= page_view) &&  (TopPosition >= page_view))
	{
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * jQuery.fn.sortElements
 * --------------
 * @param Function comparator:
 *   Exactly the same behaviour as [1,2,3].sort(comparator)
 *   
 * @param Function getSortable
 *   A function that should return the element that is
 *   to be sorted. The comparator will run on the
 *   current collection, but you may want the actual
 *   resulting sort to occur on a parent or another
 *   associated element.
 *   
 *   E.g. $('td').sortElements(comparator, function(){
 *      return this.parentNode; 
 *   })
 *   
 *   The <td>'s parent (<tr>) will be sorted instead
 *   of the <td> itself.
 */
jQuery.fn.sortElements = (function(){
 
    var sort = [].sort;
 
    return function(comparator, getSortable) {
 
        getSortable = getSortable || function(){return this;};
 
        var placements = this.map(function(){
 
            var sortElement = getSortable.call(this),
                parentNode = sortElement.parentNode,
 
                // Since the element itself will change position, we have
                // to have some way of storing its original position in
                // the DOM. The easiest way is to have a 'flag' node:
                nextSibling = parentNode.insertBefore(
                    document.createTextNode(''),
                    sortElement.nextSibling
                );
 
            return function() {
 
                if (parentNode === this) {
                    throw new Error(
                        "You can't sort elements if any one is a descendant of another."
                    );
                }
 
                // Insert before flag:
                parentNode.insertBefore(this, nextSibling);
                // Remove flag:
                parentNode.removeChild(nextSibling);
 
            };
 
        });
 
        return sort.call(this, comparator).each(function(i){
            placements[i].call(getSortable.call(this));
        });
 
    };
 
})();

