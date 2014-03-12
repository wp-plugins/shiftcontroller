<?php
$fields = array(
	array(
		'name' => 'first_name',
		'label' => lang('user_first_name'),
		),
	array(
		'name' => 'last_name',
		'label' => lang('user_last_name'),
		),
	array(
		'name' => 'email',
		'label' => lang('common_email'),
		),
	array(
		'name' => 'password',
		'label' => lang('common_password'),
		'type' => 'password'
		),
	array(
		'name' => 'confirm_password',
		'label' => lang('common_password_confirm'),
		'type' => 'password'
		),
	);
reset( $fields );
?>

<div class="page-header">
<h2><?php echo $page_title; ?></h2>
</div>

<?php echo form_open('setup/run', array('class' => 'form-horizontal form-condensed')); ?>
<fieldset>
<legend><?php echo lang('setup_admin'); ?></legend>
<?php foreach( $fields as $f ) : ?>
	<?php
	echo Hc_html::wrap_input(
		$f['label'],
		$this->hc_form->build_input($f)
		);
	?>
<?php endforeach; ?>
</fieldset>

<?php
echo Hc_html::wrap_input(
	'',
	form_button( 
		array(
			'type' => 'submit',
			'name' => 'submit',
			'class' => 'btn btn-default'
			),
		lang('setup_setup')
		)
	);
?>

<?php echo form_close();?>