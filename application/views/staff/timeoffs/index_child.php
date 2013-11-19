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
		$class .= ' alert-error';
	}
}

$this->hc_time->setDateDb( $e->date );

$date_view = $this->hc_time->formatDate();
if( $e->date != $e->date_end )
{
	$this->hc_time->setDateDb( $e->date_end );
	$date_view .= ' - ' . $this->hc_time->formatDate();
}
else
{
	$date_view .= ' [' . $this->hc_time->formatPeriodOfDay($e->start, $e->end) . ']';
}
?>
<div class="<?php echo $class; ?>">

<?php echo ci_anchor( array($this->conf['path'], 'delete', $e->id), '&times;', 'class="close hc-confirm" title="' . lang('common_delete') . '"' ); ?>
<?php echo ci_anchor( array($this->conf['path'], 'edit', $e->id), '<strong>' . $date_view . '</strong>' ); ?>

<br>
<?php if( count($conflicts) > 0 ) : ?>
	<i class="icon-exclamation-sign text-error"></i> <?php echo lang('shift_conflicts'); ?>
<?php else : ?>
	<i class="icon-ok text-success"></i> <?php echo lang('shift_no_conflicts'); ?>
<?php endif; ?>

<?php if( count($notes) > 0 ) : ?>
<?php
		$notes_text = array();
		reset( $notes );
		foreach( $notes as $n )
		{
			$notes_text[] = $n->content;
		}
		$notes_text = join( "\n", $notes_text );
?>
	<div class="pull-right">
	<i class="icon-comment-alt" title="<?php echo $notes_text; ?>"></i> <?php echo count($notes); ?>
	</div>
<?php endif; ?>

</div>