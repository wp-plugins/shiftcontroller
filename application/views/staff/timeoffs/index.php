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
</ul>

<?php require( NTS_SYSTEM_APPPATH . 'views/_boilerplate/index.php' ); ?>

<?php echo form_close(); ?>