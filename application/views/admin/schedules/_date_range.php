<?php
$defaults = array(
	'start'	=> $start_date,
	'end'	=> $end_date,
	);
$errors = array();
?>
<?php echo form_open( join('/', array('admin/schedules/browse')), array('class' => 'form-condensed')); ?>
<?php
	echo hc_form_input(
		array(
			'name'	=> 'start',
			'type'	=> 'date',
			),
		$defaults,
		$errors,
		FALSE
		);
?>
 - 
<?php
	echo hc_form_input(
		array(
			'name'	=> 'end',
			'type'	=> 'date',
			),
		$defaults,
		$errors,
		FALSE
		);
?>

<?php echo form_submit( array('name' => 'submit', 'class' => 'btn btn-primary'), lang('common_go')); ?>
<?php echo form_close(); ?>
