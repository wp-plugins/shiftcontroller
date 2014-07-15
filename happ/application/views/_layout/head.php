<?php
if( defined('NTS_DEVELOPMENT') && NTS_DEVELOPMENT )
{
	$assets_dir = NTS_DEVELOPMENT . '/assets';
	$assets_web_dir = 'http://localhost/';
}
else
{
	$assets_dir = dirname(__FILE__) . '/../../../assets';
	$assets_web_dir = ci_base_url('');
}
require( $assets_dir . '/files.php' );

$inline = ( isset($GLOBALS['NTS_INLINE']) && $GLOBALS['NTS_INLINE'] ) ? TRUE : FALSE;
$style_loaded = ( isset($GLOBALS['NTS_STYLE_LOADED']) && $GLOBALS['NTS_STYLE_LOADED'] ) ? TRUE : FALSE;
?>
<?php if( ! $inline ) : ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo (isset($page_title)) ? $page_title : ''; ?></title>

<?php
if( isset($force_head) && $force_head )
{
	require( $force_head );
}
?>

<?php foreach( $css_files as $f ) : ?>
	<?php if( is_array($f) ) : ?>
		<!--[if <?php echo $f[1]; ?>]>
		<link rel="stylesheet" type="text/css" href="<?php echo $assets_web_dir . $f[0]; ?>" />
		<![endif]-->
	<?php else : ?>
		<link rel="stylesheet" type="text/css" href="<?php echo $assets_web_dir . $f; ?>" />
	<?php endif; ?>
<?php endforeach; ?>

<?php foreach( $js_files as $f ) : ?>
	<?php if( is_array($f) ) : ?>
		<!--[if <?php echo $f[1]; ?>]>
		<script language="JavaScript" type="text/javascript" src="<?php echo $assets_web_dir . $f[0]; ?>"></script>
		<![endif]-->
	<?php else : ?>
		<script language="JavaScript" type="text/javascript" src="<?php echo $assets_web_dir . $f; ?>"></script>
	<?php endif; ?>
<?php endforeach; ?>
</head>

<body>

<?php elseif( ! $style_loaded ) : ?>

<script language="JavaScript">
function hc_if_loaded( src, targetelement, targetattr )
{
	var allsuspects = document.getElementsByTagName(targetelement);
	var skip_me = false;
	for( var i = allsuspects.length; i >= 0; i-- )
	{
		if( allsuspects[i] && (allsuspects[i].getAttribute(targetattr) != null) && (allsuspects[i].getAttribute(targetattr).indexOf(src) != -1) )
		{
			skip_me = true;
			break;
		}
	}
	return skip_me;
}

function hc_get_js( src )
{
	if( ! hc_if_loaded(src, 'script', 'src') )
	{
		document.writeln('<' + 'script src="' + src + '"' + ' type="text/javascript"><' + '/script>');
	}
}

function hc_get_css( src )
{
	if( ! hc_if_loaded(src, 'link', 'href') )
	{
		var fileref = document.createElement('link');
		fileref.setAttribute( 'rel', 'stylesheet' );
		fileref.setAttribute( 'type', 'text/css' );
		fileref.setAttribute( 'href', src );
		document.getElementsByTagName('head')[0].appendChild( fileref );
	}
}

<?php foreach( $css as $f ) : ?>
hc_get_css('<?php echo $f; ?>');
<?php endforeach; ?>

<?php foreach( $js as $f ) : ?>
hc_get_js('<?php echo $f; ?>');
<?php endforeach; ?>

</script>

<?php endif; ?>