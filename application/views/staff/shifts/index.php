<?php require( dirname(__FILE__) . '/_tabs.php' ); ?>

<?php if( ! count($shifts) ) : ?>
	<p>
	<?php echo lang('common_none'); ?>
<?php endif; ?>

<?php
$per_row = 4;
$row_open = FALSE;
$ii = 0;
?>

<?php foreach( $shifts as $sh ) : ?>
	<?php
	$ii++;
	?>

	<?php if( 1 == ($ii % $per_row) ) : ?>
		<div class="row">
		<?php $row_open = TRUE; ?>
	<?php endif; ?>

	<div class="col-sm-3">
		<?php require( dirname(__FILE__) . '/index_child.php' ); ?>
	</div>

	<?php if( ! ($ii % $per_row) ) : ?>
		</div>
		<?php $row_open = FALSE; ?>
	<?php endif; ?>

<?php endforeach; ?>

<?php if( $row_open ) : ?>
	</div>
<?php endif; ?>