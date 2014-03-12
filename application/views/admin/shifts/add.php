<?php echo form_open( join('/', array_merge(array($this->conf['path'], 'save', 0), $args) ), array('class' => 'form-horizontal form-condensed')); ?>

<?php if( $this->hc_modules->exists('shift_groups') ) : ?>
	<?php echo Modules::run('shift_groups/admin/shift_add_form', $object->date); ?>
<?php else : ?>
	<?php
	echo Hc_html::wrap_input(
		$fields['date']['label'],
		$this->hc_form->build_input($fields['date'])
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
		$end = ($sht->end > 24*60*60) ? ($sht->end - 24*60*60) : $sht->end;

		$templates_label[] = '<li>';
		$templates_label[] = '<a href="#" class="hc-shift-templates" data-start="' . $sht->start . '" data-end="' . $end . '">';
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
		$this->hc_form->build_input($fields['location'])
		);
	?>
<?php else : ?>
	<?php
	echo $this->hc_form->input($fields['location']);
	?>
<?php endif; ?>

<?php
echo Hc_html::wrap_input(
	$fields['user']['label'],
	$this->hc_form->build_input($fields['user'])
	);
?>

<?php
echo Hc_html::wrap_input(
	'',
	form_submit( 
		array(
			'name' => 'submit',
			'class' => 'btn btn-default'
			),
		lang($this->conf['entity'] . '_add')
		)
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