<?php
$defaults = array(
	'start'		=> $start_date,
	'end'		=> $end_date,
	'display'	=> $display,
	);
$this->hc_form->set_defaults( $defaults );
$errors = array();
?>
<?php 
echo form_open( 
	join('/', array('staff/shifts/browse')), 
	array(
		'class' => 'form-condensed'
		)
	);
?>
<?php
echo $this->hc_form->input( 
	array(
		'name'	=> 'start',
		'type'	=> 'date',
		)
	);
?>
 - 
<?php
echo $this->hc_form->input( 
	array(
		'name'	=> 'end',
		'type'	=> 'date',
		)
	);
?>

<?php
echo $this->hc_form->input( 
	array(
		'name'	=> 'display',
		'type'	=> 'hidden',
		)
	);
?>

<?php echo form_submit( array('name' => 'submit', 'class' => 'btn btn-primary'), lang('common_go')); ?>
<?php echo form_close(); ?>
