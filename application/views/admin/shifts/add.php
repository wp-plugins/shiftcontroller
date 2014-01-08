<?php echo form_open( join('/', array_merge(array($this->conf['path'], 'save', 0), $args) ), array('class' => 'form-horizontal form-condensed')); ?>

<?php if( $this->hc_modules->exists('shift_groups') ) : ?>
	<?php echo Modules::run('shift_groups/admin/shift_add_form', $object->date); ?>
<?php else : ?>
	<?php
	echo hc_bootstrap::input(
		$this->hc_form->input($fields['date']),
		$fields['date']['label'],
		$this->hc_form->error('date')
		);
	?>
<?php endif; ?>

<?php
if( count($shift_templates) )
{
	$templates_label = array();
	$templates_label[] = '<div class="dropdown">';
	$templates_label[] = '<a class="dropdown-toggle" data-toggle="dropdown" href="#">' . lang('time') . ' <b class="caret"></b></a>';
	$templates_label[] = '<ul class="dropdown-menu" role="menu">';
	foreach( $shift_templates as $sht )
	{
		$templates_label[] = '<li>';
		$templates_label[] = '<a href="#" class="hc-shift-templates" data-start="' . $sht->start . '" data-end="' . $sht->end . '">';
		$templates_label[] = $sht->name . '<br>' . hc_format_time_of_day($sht->start) . ' - ' . hc_format_time_of_day($sht->end);
		$templates_label[] = '</a>';
		$templates_label[] = '</li>';
	}
	$templates_label[] = '</ul>';
	$templates_label[] = '</div>';
	$templates_label = join( "\n", $templates_label );
}
else
{
	$templates_label = lang('time');
}

echo hc_bootstrap::input(
	$this->hc_form->input($fields['start']) . 
	' - ' .
	$this->hc_form->input($fields['end'])
	,
	$templates_label,
	$this->hc_form->error('start') OR $this->hc_form->error('end')
	);
?>

<?php
echo hc_bootstrap::input(
	$this->hc_form->input($fields['location']),
	$fields['location']['label'],
	$this->hc_form->error('location'),
	$fields['location']
	);
?>

<?php
echo hc_bootstrap::input(
	$this->hc_form->input($fields['user']),
	$fields['user']['label'],
	$this->hc_form->error('user')
	);
?>

<div class="form-actions">
<?php 
echo form_submit( 
	array(
		'name' => 'submit',
		'class' => 'btn btn-primary'),
		lang($this->conf['entity'] . '_add')
	);
?>
</div>
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