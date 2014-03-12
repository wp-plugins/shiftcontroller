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
	?>
	<?php
	echo Hc_html::wrap_input(
		$f['label'],
		$this->hc_form->build_input($f)
		);
	?>
<?php endforeach; ?>

<?php
echo Hc_html::wrap_input(
	'',
	form_submit( 
		array(
			'name' => 'submit',
			'class' => 'btn btn-default'
			),
		lang('common_save')
		)
	);
?>

<?php echo form_close(); ?>