<?php

Class 	jempe_Image_lib extends CI_Image_lib{

	var $wm_char_width = array(); // distinct width characters
	var $wm_char_radio = 0.75; // distinct width characters


	/**
	 * Watermark - Text Version
	 *
	 * @access	public
	 * @return	bool
	 */			
	function text_watermark()
	{
		if ( ! ($src_img = $this->image_create_gd()))
		{		
			return FALSE;
		}
				
		if ($this->wm_use_truetype == TRUE AND ! file_exists($this->wm_font_path))
		{
			$this->set_error('imglib_missing_font');
			return FALSE;
		}
		
		//  Fetch source image properties		
		$this->get_image_properties();				
		
		// Set RGB values for text and shadow		
		$this->wm_font_color	= str_replace('#', '', $this->wm_font_color);
		$this->wm_shadow_color	= str_replace('#', '', $this->wm_shadow_color);
		
		$R1 = hexdec(substr($this->wm_font_color, 0, 2));
		$G1 = hexdec(substr($this->wm_font_color, 2, 2));
		$B1 = hexdec(substr($this->wm_font_color, 4, 2));
	
		$R2 = hexdec(substr($this->wm_shadow_color, 0, 2));
		$G2 = hexdec(substr($this->wm_shadow_color, 2, 2));
		$B2 = hexdec(substr($this->wm_shadow_color, 4, 2));
		
		$txt_color	= imagecolorclosest($src_img, $R1, $G1, $B1);
		$drp_color	= imagecolorclosest($src_img, $R2, $G2, $B2);

		// Reverse the vertical offset
		// When the image is positioned at the bottom
		// we don't want the vertical offset to push it
		// further down.  We want the reverse, so we'll
		// invert the offset.  Note: The horizontal
		// offset flips itself automatically
	
		if ($this->wm_vrt_alignment == 'B')
			$this->wm_vrt_offset = $this->wm_vrt_offset * -1;
			
		if ($this->wm_hor_alignment == 'R')
			$this->wm_hor_offset = $this->wm_hor_offset * -1;

		// Set font width and height
		// These are calculated differently depending on
		// whether we are using the true type font or not
		if ($this->wm_use_truetype == TRUE)
		{
			if ($this->wm_font_size == '')
				$this->wm_font_size = '17';
		
			$fontwidth  = $this->wm_font_size*$this->wm_char_radio;
			$fontheight = $this->wm_font_size;
			$this->wm_vrt_offset += $this->wm_font_size;
		}
		else
		{
			$fontwidth  = imagefontwidth($this->wm_font_size);
			$fontheight = imagefontheight($this->wm_font_size);
		}

		// Set base X and Y axis values
		$x_axis = $this->wm_hor_offset + $this->wm_padding;
		$y_axis = $this->wm_vrt_offset + $this->wm_padding;

		// Set verticle alignment
		if ($this->wm_use_drop_shadow == FALSE)
			$this->wm_shadow_distance = 0;
			
		$this->wm_vrt_alignment = strtoupper(substr($this->wm_vrt_alignment, 0, 1));
		$this->wm_hor_alignment = strtoupper(substr($this->wm_hor_alignment, 0, 1));
	
		switch ($this->wm_vrt_alignment)
		{
			case	 "T" :
				break;
			case "M":	$y_axis += ($this->orig_height/2)+($fontheight/2);
				break;
			case "B":	$y_axis += ($this->orig_height - $fontheight - $this->wm_shadow_distance - ($fontheight/2));
				break;
		}
	
		$x_shad = $x_axis + $this->wm_shadow_distance;
		$y_shad = $y_axis + $this->wm_shadow_distance;

		$text_width = 0;

		if(strlen($this->wm_text)){
			$characters = str_split($this->wm_text);
	
			foreach($characters as $character){
				if(isset($this->wm_char_width[$character]))
					$char_width = ($fontwidth * $this->wm_char_width[$character]);
				else
					$char_width = $fontwidth;

				$text_width += $char_width;


			}
		}
		
		// Set horizontal alignment
		switch ($this->wm_hor_alignment)
		{
			case "L":
				break;
			case "R":
						if ($this->wm_use_drop_shadow)
							$x_shad += ($this->orig_width - $text_width);
							$x_axis += ($this->orig_width - $text_width);
				break;
			case "C":
						if ($this->wm_use_drop_shadow)
							$x_shad += floor(($this->orig_width - $text_width)/2);
							$x_axis += floor(($this->orig_width  -$text_width)/2);
				break;
		}
		
		//  Add the text to the source image
		if ($this->wm_use_truetype)
		{	
			if ($this->wm_use_drop_shadow)
				imagettftext($src_img, $this->wm_font_size, 0, $x_shad, $y_shad, $drp_color, $this->wm_font_path, $this->wm_text);
				imagettftext($src_img, $this->wm_font_size, 0, $x_axis, $y_axis, $txt_color, $this->wm_font_path, $this->wm_text);
		}
		else
		{
			if ($this->wm_use_drop_shadow)
				imagestring($src_img, $this->wm_font_size, $x_shad, $y_shad, $this->wm_text, $drp_color);
				imagestring($src_img, $this->wm_font_size, $x_axis, $y_axis, $this->wm_text, $txt_color);
		}
	
		//  Output the final image
		if ($this->dynamic_output == TRUE)
		{
			$this->image_display_gd($src_img);
		}
		else
		{
			$this->image_save_gd($src_img);
		}
		
		imagedestroy($src_img);
	
		return TRUE;
	}
	
	// --------------------------------------------------------------------


}

?>