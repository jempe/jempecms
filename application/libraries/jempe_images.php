<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Jempe
 *
 * An open source application CMS derived from Codeigniter php framework
 *
 * @package             Jempe
 * @author              Sucio Kastro
 * @license             http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @link                http://jempe.org
 * @since               Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Jempe Images Library
 *
 * Images resizing functions
 *
 * @package             Jempe
 * @subpackage  Libraries
 * @category    Images
 * @author              Sucio Kastro
 * @link                http://code.google.com/p/jempe/wiki/JempeImageClass
 */

class Jempe_images {
	var $image_library = "gd2";

        /**
        * Resize image proportionally
        *
        * Resize an image to fit in a rectangle (width x height)
        *
        * @access       public
	* @param	string	path to the image file
	* @param	int	width of rectangle
	* @param	int	height of rectangle
        * @return       mixed
        */
	function process_proportional($source_image, $width, $height)
	{
		$CI =& get_instance();
		$CI->load->library('jempe_cms');
		$CI->load->library('image_lib');
		$CI->image_lib->clear();
	
		$config['image_library'] = $CI->jempe_cms->image_library;
		$config['source_image'] = $source_image ;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = $width;
		$config['height'] = $height;
	
		$CI->image_lib->initialize($config); 
	
		if($CI->image_lib->resize())
		{
			return TRUE;
		}
		else
		{
			return $CI->image_lib->display_errors();
		}
	}

        // --------------------------------------------------------------------

        /**
        * Crop image to fit in a square
        *
        * Resize and crop image to fit in a square
        *
        * @access       public
	* @param	string	path to the image file
	* @param	int	width and height of square
        * @return       mixed
        */
	function process_square($source_image, $size)
	{
		return $this->process_cropped($source_image, $size, $size);
	}

        // --------------------------------------------------------------------

        /**
        * Crop image to fit in a rectangle
        *
        * Resize and crop image to fit in a rectangle (width x height)
        *
        * @access       public
	* @param	string	path to the image file
	* @param	int	width of rectangle
	* @param	int	height of rectangle
        * @return       bool
        */
	function process_cropped($source_image, $width, $height)
	{
		$CI =& get_instance();
		$CI->load->library('jempe_cms');
		$CI->load->library('image_lib');
		$CI->image_lib->clear();
	
		$image_info = getimagesize($source_image);
	
		if(($image_info[0] / $image_info[1]) > ($width / $height))
		{
			$config["master_dim"] = "height";
		}
		else
		{
			$config["master_dim"] = "width";
		}
	
		$config['image_library'] = $CI->jempe_cms->image_library;
		$config['source_image'] = $source_image;
		$config['maintain_ratio'] = TRUE;
		$config['width'] = $width;
		$config['height'] = $height;
	
		$CI->image_lib->initialize($config); 
	
		$CI->image_lib->resize();
		$CI->image_lib->clear();
	
		$image_info = getimagesize($source_image);
	
		$config['x_axis'] = floor(($image_info[0] - $width ) / 2);
		$config['y_axis'] = floor(($image_info[1] - $height ) / 2);
	
		$config['image_library'] = $CI->jempe_cms->image_library;
		$config['source_image'] = $source_image ;
		$config['maintain_ratio'] = FALSE;
		$config['width'] = $width;
		$config['height'] = $height;
	
		$CI->image_lib->initialize($config); 
	
	
		if($CI->image_lib->crop())
		{
			return TRUE;
		}
		else
		{
			return $CI->image_lib->display_errors();
		}
	}

        // --------------------------------------------------------------------

        /**
        * Use a grayscale image to mask image
        *
        * @access       public
	* @param	string	path to the image file
	* @param	string	path to the mask file
	* @param	int	width of rectangle
	* @param	int	height of rectangle
        * @return       void
        */
	function process_mask($source_image, $mask, $width, $height)
	{
		$CI =& get_instance();
		$CI->load->library("imagemask");
	
		$CI->imagemask->mask($width, $height, $source_image, $mask, $source_image);
	}

        // --------------------------------------------------------------------

        /**
        * Add image as background
        *
	* Resize image to fit a rectangle and add a color background
	*
        * @access       public
	* @param	string	path to the image file
	* @param	string	path to the background image (only png)
	* @param	int	width of rectangle
	* @param	int	height of rectangle
        * @return       void
        */
	function process_background($source_image, $background_image, $width, $height)
	{
		$path_parts = explode('/', $source_image);
		$ext_parts = explode('.', end($path_parts));
		$ext = end($ext_parts);
		$temp_file_name = str_replace('.'.$ext, '_temp.png', end($path_parts));

		$temp_image = str_replace(end($path_parts), $temp_file_name, $source_image);

		$this->process_proportional($source_image, $width, $height);

		$CI =& get_instance();
		$CI->load->library('jempe_cms');
		$CI->load->library('image_lib');
		$CI->image_lib->clear();

		$image_info = getimagesize($source_image);
	
		$config['x_axis'] = floor(($image_info[0] - $width) / 2);
		$config['y_axis'] = floor(($image_info[1] - $height) / 2);
	
		$config['image_library'] = $CI->jempe_cms->image_library;
		$config['source_image'] = $source_image;
		$config['maintain_ratio'] = FALSE;
		$config['width'] = $width;
		$config['height'] = $height;
	
		$CI->image_lib->initialize($config); 
	
		$CI->image_lib->crop();
	
		$CI->image_lib->clear();

		copy($background_image, $temp_image);

		$config['image_library'] = $CI->jempe_cms->image_library;
		$config['source_image'] = $temp_image ;
		$config['wm_overlay_path'] = $source_image;
		$config['wm_opacity'] = 100;
		$config['wm_type'] = 'overlay';
		$config['maintain_ratio'] = TRUE;
		$config['create_thumb'] = FALSE;
		$config['wm_vrt_alignment'] = 'top';
		$config['wm_hor_alignment'] = 'left';
		$config['wm_x_transp'] = false;
		$config['wm_y_transp'] = false;
	
		$CI->image_lib->initialize($config); 
	
		$CI->image_lib->watermark();

		$result = $CI->image_lib->watermark();

		copy($temp_image, $source_image);
		unlink($temp_image);
	
		if($result)
		{
			return TRUE;
		}
		else
		{
			return $CI->image_lib->display_errors();
		}	
	}


        // --------------------------------------------------------------------

        /**
        * Add background color to image
        *
	* Resize image to fit a rectangle and add a color background
	*
        * @access       public
	* @param	string	path to the image file
	* @param	hex	color code
	* @param	int	width of rectangle
	* @param	int	height of rectangle
        * @return       void
        */
	function process_backgroundcolor($source_image, $background_color, $width, $height)
	{
		$path_parts = explode('/', $source_image);
		$ext_parts = explode('.', end($path_parts));
		$ext = end($ext_parts);

		$this->process_proportional($source_image, $width, $height);


		// TODO convert hex to rgb decimal values

		shell_exec('convert "'.$source_image.'" -resize '.$width.'x'.$height.' -background white '.$source_image);
		
		return TRUE;	
	}

        // --------------------------------------------------------------------

        /**
        * Add watermark to image
	*
        * @access       public
	* @param	string	path to the image file
	* @param	string	path to the watermark file
	* @param	int	width of rectangle
	* @param	int	height of rectangle
        * @return       void
        */
	function process_watermark($source_image, $watermark,  $width, $height, $opacity)
	{
		$CI =& get_instance();
		$CI->load->library('jempe_cms');
		$CI->load->library('image_lib');
		$CI->image_lib->clear();
	
		$image_info = getimagesize($source_image);
	
		$temp_marker = time();
	
		if(($image_info[0] / $image_info[1]) > ($width / $height))
		{
			$config["master_dim"] = "width";
		}
		else
		{
			$config["master_dim"] = "heigth";
		}
	
		$config['image_library'] = $CI->jempe_cms->image_library;
		$config['source_image'] = $source_image ;
		$config['maintain_ratio'] = TRUE;
		$config['create_thumb'] = TRUE;
		$config['thumb_marker'] = $temp_marker;
		$config['width'] = $width; 
		$config['height'] = $height;
	
		$CI->image_lib->initialize($config); 
	
		$CI->image_lib->resize();
	
		$thumb_path = $CI->image_lib->full_dst_path;
	
		$CI->image_lib->clear();
	
		$config['image_library'] = $CI->jempe_cms->image_library;
		$config['source_image'] = $source_image ;
		$config['maintain_ratio'] = FALSE;
		$config['create_thumb'] = FALSE;
		$config['width'] = $width;
		$config['height'] = $height;
	
		$CI->image_lib->initialize($config); 
	
		$CI->image_lib->resize();
	
		$CI->image_lib->clear();
	
		$config['image_library'] = $CI->jempe_cms->image_library;
		$config['source_image'] = $source_image ;
		$config['wm_overlay_path'] = $watermark;
		$config['wm_opacity'] = $opacity;
		$config['wm_type'] = 'overlay';
		$config['maintain_ratio'] = TRUE;
		$config['create_thumb'] = FALSE;
		$config['wm_vrt_alignment'] = 'top';
		$config['wm_hor_alignment'] = 'left';
		$config['wm_x_transp'] = 2;
		$config['wm_y_transp'] = 2;
	
		$CI->image_lib->initialize($config); 

		if($CI->image_lib->watermark())
		{
			unlink($thumb_path);
			return TRUE;
		}
		else
		{
			unlink($thumb_path);
			return $CI->image_lib->display_errors();
		}
	}
}

// END Jempe_images class

/* End of file jempe_images.php */
/* Location: ./application/libraries/jempe_images.php */