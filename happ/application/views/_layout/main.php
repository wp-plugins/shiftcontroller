<?php
$CI =& ci_get_instance();
$ri = $CI->remote_integration();
?>

<?php if( $include_submenu ) : ?>
<?php if( $ri ) : ?>
	<div class="row-fluid">
<?php else : ?>
	<div class="row">
<?php endif; ?>
		<div class="span9">
			<?php if( isset($include_header) && $include_header ) : ?>
				<?php require( dirname(__FILE__) . '/header.php' ); ?>
			<?php endif; ?>
			<?php require( dirname(__FILE__) . '/content.php' ); ?>
		</div>

		<div class="span3">
			<?php require( dirname(__FILE__) . '/submenu.php' ); ?>
		</div>
	</div>

<?php else : ?>
	<?php if( isset($include_header) && $include_header ) : ?>
		<?php require( dirname(__FILE__) . '/header.php' ); ?>
	<?php endif; ?>
	<?php require( dirname(__FILE__) . '/content.php' ); ?>
<?php endif; ?>