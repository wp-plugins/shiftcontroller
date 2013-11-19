<?php
echo form_open( 
	join('/', array($this->conf['path'], 'savepassword', $object->id)),
	array(
		'class' => 'form-horizontal form-condensed'
		)
	);
?>

<?php foreach( $fields as $f ) : ?>
<?php
		if( $f['name'] == 'password' )
			$f['label'] = lang('common_new_password');

		echo hc_bootstrap::input(
			$this->hc_form->input($f),
			$f['label'],
			$this->hc_form->error($f['name'])
			);
?>
<?php endforeach; ?>

<?php 
echo hc_bootstrap::form_actions(
	form_submit( 
		array(
			'name' => 'submit',
			'class' => 'btn btn-primary'
			),
		lang('common_save'))
	);
?>

<?php echo form_close(); ?>