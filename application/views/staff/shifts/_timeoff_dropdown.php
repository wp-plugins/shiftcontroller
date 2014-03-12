<?php
/* CLASS */
$class = array();
$class[] = 'dropdown-toggle';
$class[] = 'alert';
$class[] = 'alert-archive';
if( $to->status == TIMEOFF_MODEL::STATUS_ACTIVE )
	$class[] = 'alert-archive-success';
$class[] = 'alert-condensed';
if( count($conflicts) )
{
	$class[] = 'alert-danger2';
}
$class = join( ' ', $class );

/* TITLES */
$title = array();
$title[] = '<i class="fa fa-coffee"></i> ' . $this->hc_time->formatPeriodOfDay($to->start, $to->end);
$title = join( ' ', $title );

/* MENU */
$menu = array();

/* MENU - EDIT */
$icon = $conflicts ? '<i class="fa fa-exclamation-circle text-danger" title="' . lang('shift_conflicts') . '"></i>' : '<i class="fa fa-edit"></i>';
$menu[] = array(
	'href'	=> ci_site_url( array('staff/timeoffs/edit', $to->id) ),
	'title'	=> $icon . ' ' . lang('common_edit'),
	'class'	=> 'hc-parent-loader',
	);

/* MENU - DELETE */
$menu[] = '-divider-';
$menu[] = array(
	'href'	=> ci_site_url( array('staff/timeoffs/delete', $to->id) ),
	'title'	=> '<i class="fa fa-times text-danger"></i> ' . lang('common_delete'),
	'class'	=> 'hc-confirm',
	);
?>

<?php if( ! isset($_skip_title) ) : ?>
<a class="<?php echo $class; ?>" href="#" data-toggle="dropdown">
	<?php echo $title; ?>
</a>
<?php endif; ?>

<?php
echo Hc_html::dropdown_menu($menu);
?>