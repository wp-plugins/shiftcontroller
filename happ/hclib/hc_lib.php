<?php
class Hc_lib {
	static function file_set_contents( $fileName, $content )
	{
		$length = strlen( $content );
		$return = 1;

		if(! $fh = fopen($fileName, 'w') ){
			echo "can't open file <B>$fileName</B> for wrinting.";
			exit;
			}
		rewind( $fh );
		$writeResult = fwrite($fh, $content, $length);
		if( $writeResult === FALSE )
			$return = 0;

		return $return;
	}

	static function parse_lang( $label )
	{
		$lang_pref = 'lang:';
		if( substr($label, 0, strlen($lang_pref)) == $lang_pref )
		{
			$label = substr($label, strlen($lang_pref));
			$label = lang( $label );
		}
		return $label;
	}

	static function parse_icon( $title, $add_fw = TRUE )
	{
		if( preg_match('/(\<i.+\>\<\/i\>\s+)(.+)/', $title, $ma) )
		{
			$link_title = $ma[2];
			$link_icon = $ma[1];
		}
		else
		{
			$link_title = strip_tags( $title );
			$link_icon = '';
		}

		if( $link_icon && $add_fw )
		{
			if( preg_match('/\<i.+class\=[\'\"](.+)[\'\"]\>\<\/i\>/', $title, $ma2) )
			{
				$class = $ma2[1];
				if( strpos($class, 'fa-fw') === FALSE )
				{
					$new_class = 'fa-fw ' . $class;
					$link_icon = str_replace( $class, $new_class, $link_icon );
				}
			}
		}
		$return = array( $link_title, $link_icon );
		return $return;
	}
}
