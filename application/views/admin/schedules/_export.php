<?php
$target_display = 'export' . $display;

$link = ci_site_url(
	array(
		$this->conf['path'], 'index',
		'display',	$target_display,
		'start',	$start_date,
		'end',		$end_date,
		'filter',	$filter,
		'id',		$id
		)
	);
?>

<a class="btn btn-default" href="<?php echo $link; ?>">
	<i class="fa fa-download"></i> <?php echo lang('common_download'); ?>
</a>