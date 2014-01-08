<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------

/**
 * Header Redirect
 *
 * Header redirect in two flavors
 * For very fine grained control over headers, you could use the Output
 * Library's set_header() function.
 *
 * @access	public
 * @param	string	the URL
 * @param	string	the method: location or redirect
 * @return	string
 */
if ( ! function_exists('ci_redirect'))
{
	function ci_redirect($uri = '', $method = 'location', $http_response_code = 302)
	{
		if ( ! ( (! is_array($uri)) && preg_match('#^https?://#i', $uri) ) )
		{
			$uri = ci_site_url($uri);
		}

	/* this is a hack to ensure that post controller and post system hooks are triggered */
		hc_ci_before_exit();

		switch($method)
		{
			case 'refresh'	: header("Refresh:0;url=".$uri);
				break;
			default			: header("Location: ".$uri, TRUE, $http_response_code);
				break;
		}
		return;
	}
}


/**
 * Anchor Link
 *
 * Creates an anchor based on the local URL.
 *
 * @access	public
 * @param	string	the URL
 * @param	string	the link title
 * @param	mixed	any attributes
 * @return	string
 */
if ( ! function_exists('ci_anchor'))
{
	function ci_anchor($uri = '', $title = '', $attributes = '')
	{
		$title = (string) $title;

		if ( ! is_array($uri))
		{
			$site_url = ( ! preg_match('!^\w+://! i', $uri)) ? ci_site_url($uri) : $uri;
		}
		else
		{
			$site_url = ci_site_url($uri);
		}

		if ($title == '')
		{
			$title = $site_url;
		}

		if ($attributes != '')
		{
			$attributes = _parse_attributes($attributes);
		}

		return '<a href="'.$site_url.'"'.$attributes.'>'.$title.'</a>';
	}
}

/**
 * Site URL
 *
 * Create a local URL based on your basepath. Segments can be passed via the
 * first parameter either as a string or an array.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('ci_site_url'))
{
	function ci_site_url($uri = '')
	{
		$CI =& ci_get_instance();
		return $CI->config->site_url($uri);
	}
}

// ------------------------------------------------------------------------

/**
 * Base URL
 * 
 * Create a local URL based on your basepath.
 * Segments can be passed in as a string or an array, same as site_url
 * or a URL to a file can be passed in, e.g. to an image file.
 *
 * @access	public
 * @param string
 * @return	string
 */
if ( ! function_exists('ci_base_url'))
{
	function ci_base_url($uri = '')
	{
		$CI =& ci_get_instance();
		return $CI->config->base_url($uri);
	}
}


/* End of file url_helper.php */
/* Location: ./system/helpers/url_helper.php */