<?php
$CI =& ci_get_instance();
$remote_integration = $CI->remote_integration();
?>

<?php 
echo form_open( 
	join('/', array($this->conf['path'], 'save', $object->id)),
	array(
		'class' => 'form-horizontal form-condensed'
		)
	);
?>

<?php foreach( $fields as $f ) : ?>
	<?php
	echo hc_bootstrap::input(
		$this->hc_form->input($f),
		$f['label'],
		$this->hc_form->error($f['name'])
		);
	?>
<?php endforeach; ?>

<?php
$actions = array();
$actions[] = 
	form_submit(
		array(
			'name' => 'submit',
			'class' => 'btn btn-primary'
			),
		lang('common_save')
		)
	;

if( $this->auth->check() != $object->id )
{
	$archive_title = $object->active ? lang('user_archive') : lang('user_restore');
	$archive_btn = $object->active ? 'btn-danger' : 'btn-success';
	$actions[] = 
		ci_anchor( 
			array($this->conf['path'], 'disable', $object->id),
			$archive_title,
			'class="btn btn-inverse hc-confirm ' . $archive_btn . '"'
			)
		;
}
$actions = join( '&nbsp;', $actions );
?>
<?php 
echo hc_bootstrap::form_actions( $actions );
?>
<?php echo form_close(); ?>