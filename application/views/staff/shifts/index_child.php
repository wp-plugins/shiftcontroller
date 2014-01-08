<?php
$notes = array();
if( $this->hc_modules->exists('notes') )
{
	$notes = $sh->note->get()->all;
	if( count($notes) > 0 )
	{
		$notes_text = array();
		reset( $notes );
		foreach( $notes as $n )
		{
			$notes_text[] = $n->content;
		}
		$notes_text = join( "\n", $notes_text );
	}
}
$this->hc_time->setDateDb( $sh->date );

$date_view = '';
$date_view .= $this->hc_time->formatWeekdayShort();
$date_view .= ', ';
$date_view .= $this->hc_time->formatDate();

$time_view =  hc_format_time_of_day($sh->start) . ' - ' . hc_format_time_of_day($sh->end);
?>

<?php if( count($notes) > 0 ) : ?>
	<span class="pull-right">
		<span class="hc-tooltip" title="<?php echo $notes_text; ?>">
			<i class="icon-comment-alt"></i> <?php echo count($notes); ?>
		</span>
	</span>
<?php endif; ?>

<strong><?php echo $date_view; ?></strong>
<br>

<?php echo ci_anchor( array($this->conf['path'], 'edit', $sh->id), $time_view ); ?>

<ul class="nav nav-list nav-list-condensed">
<?php require( dirname(__FILE__) . '/_shift_dropdown.php' ); ?>
</ul>