<?php
echo form_open( 
	join('/', array($this->conf['path'], 'save', $object->id)),
	array(
		'class' => 'form-horizontal form-condensed'
		)
	);
?>
<?php
echo Hc_html::wrap_input(
	$fields['id']['label'],
	$this->hc_form->build_input( $fields['id'] )
	);
?>
<?php
echo Hc_html::wrap_input(
	$fields['date']['label'],
	$this->hc_form->build_input( $fields['date'] )
	); 
?>
<?php
echo Hc_html::wrap_input(
	$templates_label,
	array(
		$this->hc_form->build_input($fields['start']),
		' - ',
		$this->hc_form->build_input($fields['end'])
		)
	);
?>
<?php if( $location_count > 1 ) : ?>
	<?php
	echo Hc_html::wrap_input(
		$fields['location']['label'],
		$this->hc_form->build_input( $fields['location'] )
		); 
	?>
<?php else : ?>
	<?php
	echo $this->hc_form->input($fields['location']);
	?>
<?php endif; ?>

<?php
/*
echo Hc_html::wrap_input(
	$fields['user']['label'],
	$this->hc_form->build_input( $fields['user'] )
	);
*/
?>

<?php if( $object->user_id ) : ?>
	<?php
	echo Hc_html::wrap_input(
		$fields['user']['label'],
		$object->user->title(TRUE)
		);
	?>
<?php else : ?>
	<?php
	echo Hc_html::wrap_input(
		$fields['user']['label'],
		lang('shift_not_assigned')
		);
	?>
<?php endif; ?>

<?php
$btns = array();
$btns[] = form_submit(
	array(
		'name' => 'submit',
		'class' => 'btn btn-default'
		),
	lang('common_save')
	);

if( $object->status == SHIFT_MODEL::STATUS_DRAFT )
{
	$btns[] = ci_anchor( 
		array($this->conf['path'], 'save', $object->id, 'status', SHIFT_MODEL::STATUS_ACTIVE),
		'<i class="fa fa-check"></i> ' . lang('shift_publish'),
		'class="btn btn-success"'
		);
}

if( ! (($group_count > 1) && $this->hc_modules->exists('shift_groups')) )
{
	$btns[] = ci_anchor( 
		array($this->conf['path'], 'delete', $object->id),
		lang('common_delete'),
		'class="btn btn-danger btn-sm hc-confirm"'
		);
}
$btns = join( '&nbsp;', $btns );
?>
<?php 
echo Hc_html::wrap_input(
	'',
	$btns
	);
?>
<?php echo form_close(); ?>

<script language="JavaScript">
jQuery('.hc-shift-templates').on('click', function(e)
{
	jQuery(this).closest('form').find('[name=start]').val( jQuery(this).data('start') );
	jQuery(this).closest('form').find('[name=end]').val( jQuery(this).data('end') );
	jQuery(this).closest('.dropdown').find('.dropdown-toggle').dropdown('toggle');
	return false;
});
</script>