<?php
$status_classes = array(
	TIMEOFF_MODEL::STATUS_ACTIVE	=> 'success',
	TIMEOFF_MODEL::STATUS_PENDING	=> 'warning',
	TIMEOFF_MODEL::STATUS_DENIED	=> 'info',
	TIMEOFF_MODEL::STATUS_ARCHIVE	=> 'archive',
	);
?>
<?php
echo form_open(
	join('/', array($this->conf['path'], 'action')),
	array(
		'class'		=> 'form-horizontal form-condensed',
		)
	);
?>
<?php
$menu_options = array(
	'pending'	=> array(
		'title'	=> '<i class="fa fa-eye text-warning"></i> ' . $this->{$this->model}->prop_text('status', FALSE, TIMEOFF_MODEL::STATUS_PENDING),
		'class'	=> 'hc-form-submit',
		'href'	=> '#pending',
		),
	'activate'	=> array(
		'title'	=> '<i class="fa fa-check-square-o text-success"></i> ' . lang('common_approve'),
		'class'	=> 'hc-form-submit',
		'href'	=> '#active',
		),
	'deny'		=> array(
		'title'	=> '<i class="fa fa-thumbs-o-down text-muted"></i> ' . lang('common_reject'),
		'class'	=> 'hc-form-submit',
		'href'	=> '#denied',
		),
	'delete'	=> array(
		'title'	=> '<i class="fa fa-times text-danger"></i> ' . lang('common_delete'),
		'class'	=> 'hc-confirm hc-form-submit',
		'href'	=> '#delete',
		)
	);

$menu = array();

switch( $status )
{
	case TIMEOFF_MODEL::STATUS_ACTIVE:
		$menu[] = $menu_options['pending'];
		$menu[] = $menu_options['deny'];
		break;
	case TIMEOFF_MODEL::STATUS_PENDING:
		$menu[] = $menu_options['activate'];
		$menu[] = $menu_options['deny'];
		break;
	case TIMEOFF_MODEL::STATUS_DENIED:
		$menu[] = $menu_options['activate'];
		break;
	case TIMEOFF_MODEL::STATUS_ARCHIVE:
		break;
}
if( $menu )
	$menu[] = '-divider-';
$menu[] = $menu_options['delete'];
?>

<ul class="nav nav-pills">
	<?php foreach( $statuses as $t => $count ) : ?>
		<?php
		$tab_class = isset($status_classes[$t]) ? 'tab-' . $status_classes[$t] : '';
		$active = ($t == $status) ? TRUE : FALSE;
		$class = array();
		if( $active )
		{
			$class[] = 'active';
			if( $tab_class )
				$class[] = $tab_class;
		}
		$class = join( ' ', $class );
		?>
		<li class="<?php echo $class; ?>">
			<?php
			$label = $this->{$this->model}->prop_text('status', FALSE, $t);
			$label .= ' [' . $count . ']';
			?>
			<?php
			echo ci_anchor( 
				array($this->conf['path'], 'index', $t),
				$label,
				'title="' . $this->{$this->model}->prop_text('status', FALSE, $t) . '"'
				);
			?>
		</li>
	<?php endforeach; ?>

	<li class="divider">&nbsp;</li>

	<li>
		<a href="<?php echo ci_site_url( array($this->conf['path'], 'add') ); ?>">
			<i class="fa fa-plus text-success"></i> <?php echo lang('common_add'); ?>
		</a>
	</li>

	<li class="divider">&nbsp;</li>

	<li>
		<a href="#" class="hc-all-checker" data-collect="id[]">
			<?php echo lang('common_check_all'); ?>
		</a>
	</li>

	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown">
			<i class="fa fa-wrench"></i> <?php echo lang('common_with_selected'); ?> <b class="caret"></b>
		</a>
		<?php
		echo Hc_html::dropdown_menu($menu);
		?>
	</li>
</ul>

<?php require( NTS_SYSTEM_APPPATH . 'views/_boilerplate/index.php' ); ?>

<?php echo form_close(); ?>