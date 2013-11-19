<?php
$status_classes = array(
	TIMEOFF_MODEL::STATUS_ACTIVE	=> 'success',
	TIMEOFF_MODEL::STATUS_PENDING	=> 'warning',
	TIMEOFF_MODEL::STATUS_DENIED	=> 'info',
	TIMEOFF_MODEL::STATUS_ARCHIVE	=> 'archive',
	);
$check_all_name = 'check_' . hc_random(8);
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
	'activate'	=> array(
		'<i class="icon-ok text-success"></i> ' . lang('common_approve'),
		array(
			'title'	=> lang( 'common_approve' ),
			'class'	=> 'hc-form-submit',
			'href'	=> '#active',
			)
		),
	'deny'		=> array(
		'<i class="icon-arrow-down text-warning"></i> ' . lang('common_reject'),
		array(
			'title'	=> lang( 'common_reject' ),
			'class'	=> 'hc-form-submit',
			'href'	=> '#denied',
			)
		),
	'delete'	=> array(
		'<i class="icon-remove text-error"></i> ' . lang('common_delete'),
		array(
			'href'	=> '#delete',
			'title'	=> lang( 'common_delete' ),
			'class'	=> 'hc-confirm hc-form-submit',
			)
		)
	);

$menu = array();
$menu['10'] = array(
	lang('common_with_selected'),
	'header',
	);

switch( $status )
{
	case TIMEOFF_MODEL::STATUS_ACTIVE:
		$menu['20'] = $menu_options['deny'];
		break;
	case TIMEOFF_MODEL::STATUS_PENDING:
		$menu['20'] = $menu_options['activate'];
		$menu['30'] = $menu_options['deny'];
		break;
	case TIMEOFF_MODEL::STATUS_DENIED:
		$menu['20'] = $menu_options['activate'];
		break;
	case TIMEOFF_MODEL::STATUS_ARCHIVE:
		break;
}
//$menu['39'] = 'divider';
$menu['40'] = $menu_options['delete'];
?>

<ul class="nav nav-tabs">

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
		echo ci_anchor( 
			array($this->conf['path'], 'index', $t),
			$label,
			'title="' . $this->{$this->model}->prop_text('status', FALSE, $t) . '"'
		);
?>
	</li>
<?php endforeach; ?>

<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown">
		<i class="icon-wrench"></i> <?php echo lang('common_actions'); ?><b class="caret"></b>
	</a>

	<ul class="dropdown-menu">
	<li>
	<?php
		echo ci_anchor(
			array($this->conf['path'], 'add'),
			'<i class="icon-plus-sign text-success"></i>' . ' ' . lang('common_add')
			);
	?>
	</li>

	<li class="divider"></li>

	<li>
		<a>
		<?php
		echo hc_form_input(
			array(
				'name'	=> $check_all_name,
				'type'	=> 'checkbox',
				'extra'	=> array(
					'id'	=> $check_all_name,
					),
				),
			array(),
			array(),
			FALSE
			);
		?> <?php echo lang('common_check_all'); ?>
		</a>
	</li>
	<?php echo hc_dropdown_menu($menu, 'li'); ?>
	</ul>
</li>
</ul>

<?php require( NTS_SYSTEM_APPPATH . 'views/_boilerplate/index.php' ); ?>

<?php echo form_close(); ?>

<script language="JavaScript">
jQuery('#<?php echo $check_all_name; ?>').click(function(event)
{
	var $that = $(this);
	$that.closest('form').find('[name^=id]:checkbox').each(function()
	{
		this.checked = $that.is(':checked');
	});
});
</script>