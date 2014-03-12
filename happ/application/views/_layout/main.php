<?php if( $include_submenu ) : ?>
	<div class="row">
		<div class="col-sm-9">
			<?php if( isset($include_header) && $include_header ) : ?>
				<?php require( dirname(__FILE__) . '/header.php' ); ?>
			<?php endif; ?>
			<?php require( dirname(__FILE__) . '/content.php' ); ?>
		</div>

		<div class="col-sm-3">
			<?php require( dirname(__FILE__) . '/submenu.php' ); ?>
		</div>
	</div>

<?php else : ?>
	<?php if( isset($include_header) && $include_header ) : ?>
		<?php require( dirname(__FILE__) . '/header.php' ); ?>
	<?php endif; ?>
	<?php require( dirname(__FILE__) . '/content.php' ); ?>
<?php endif; ?>