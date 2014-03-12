<?php
$status_classes = array(
	TIMEOFF_MODEL::STATUS_ACTIVE	=> 'success',
	TIMEOFF_MODEL::STATUS_PENDING	=> 'warning',
	TIMEOFF_MODEL::STATUS_DENIED	=> 'info',
	TIMEOFF_MODEL::STATUS_ARCHIVE	=> 'archive',
	);

$notes = array();
if( $this->hc_modules->exists('notes') )
	$notes = $e->note->get()->all;

$status = $e->get_status();

$class = array();
$class[] = 'alert';
$class[] = 'alert-regular';
if( isset($status_classes[$status]) )
{
	$class[] = 'alert-' . $status_classes[$status];
}
$class = join( ' ', $class );

$conflicts = array();
if( in_array($status, array(TIMEOFF_MODEL::STATUS_ACTIVE, TIMEOFF_MODEL::STATUS_PENDING)) )
{
	$conflicts = $e->conflicts();
	if( count($conflicts) > 0 )
	{
		$class .= ' alert-danger';
	}
}

$this->hc_time->setDateDb( $e->date );
if( $e->date != $e->date_end )
{
	$date_view = $this->hc_time->formatDateRange( $e->date, $e->date_end );
}
else
{
	$date_view = $this->hc_time->formatDate();
}
$time_view = $this->hc_time->formatPeriodOfDay($e->start, $e->end)
?>

<div class="pull-right" style="margin: 0.5em 0.5em;">
	<?php
	echo $this->hc_form->input( 
		array(
			'name'	=> 'id[]',
			'type'	=> 'checkbox',
			'value'	=> $e->id,
			),
		FALSE
		);
	?>
</div>

<div class="<?php echo $class; ?>">

<?php if( 0 ) : ?>
	<?php echo ci_anchor( array($this->conf['path'], 'delete', $e->id), '&times;', 'class="close text-danger hc-confirm" title="' . lang('common_delete') . '"' ); ?>
<?php endif; ?>

<ul class="list-unstyled list-separated">
	<li class="dropdown">
		<i class="fa-fw fa fa-calendar"></i> 
		<a class="" href="#" data-toggle="dropdown">
			<?php echo $date_view; ?> <b class="caret"></b>
		</a>

		<?php
		$to = $e;
		$_skip_title = TRUE;
		require( dirname(__FILE__) . '/../schedules/_timeoff_dropdown.php' );
		?>
	</li>

	<li>
		<i class="fa-fw fa fa-clock-o"></i> <?php echo $time_view; ?>
	</li>

	<li>
		<i class="fa-fw fa fa-user"></i> <?php echo $e->user->get()->full_name(); ?>
	</li>

	<li>
		<?php if( count($conflicts) > 0 ) : ?>
			<i class="fa-fw fa fa-exclamation-circle text-danger"></i> <?php echo lang('shift_conflicts'); ?>
		<?php else : ?>
			<i class="fa-fw fa fa-check text-success"></i> <?php echo lang('shift_no_conflicts'); ?>
		<?php endif; ?>
	</li>

	<?php if( count($notes) > 0 ) : ?>
		<?php
		$notes_text = array();
		reset( $notes );
		foreach( $notes as $n )
		{
			$notes_text[] = $n->content;
		}
		$notes_text = join( ";<br>", $notes_text );
		?>
		<li style="font-style: italic;">
			<i class="fa-fw fa fa-comment-o"></i> 
			<?php echo $notes_text; ?>
		</li>
	<?php endif; ?>
</ul>

</div>