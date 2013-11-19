<?php
$notes = array();
if( $this->hc_modules->exists('notes') )
	$notes = $sh->note->get()->all;
$this->hc_time->setDateDb( $sh->date );

$date_view = '';
$date_view .= $this->hc_time->formatWeekdayShort();
$date_view .= ', ';
$date_view .= $this->hc_time->formatDate();
?>

<?php if( count($notes) > 0 ) : ?>
	<span class="pull-right">
		<i class="icon-comment-alt"></i> <?php echo count($notes); ?>
	</span>
<?php endif; ?>

<strong><?php echo $date_view; ?></strong>
<br>
<?php echo hc_format_time_of_day($sh->start); ?> - <?php echo hc_format_time_of_day($sh->end); ?>

<ul class="nav nav-list nav-list-condensed">
<?php require( dirname(__FILE__) . '/_shift_dropdown.php' ); ?>
</ul>