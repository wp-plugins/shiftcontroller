<?php
$CI =& ci_get_instance();
$class = 'alert alert-success';
?>
<div class="<?php echo $class; ?>">
<?php echo ci_anchor( array($this->conf['path'], 'delete', $e->id), '&times;', 'class="close hc-confirm" title="' . lang('common_delete') . '"' ); ?>
<?php echo ci_anchor( array($this->conf['path'], 'edit', $e->id), '<strong>' . $e->name . '</strong>' ); ?>
<br>
<?php echo hc_format_time_of_day($e->start); ?> - <?php echo hc_format_time_of_day($e->end); ?><br>
[<?php echo $this->hc_time->formatPeriod($e->get_duration()); ?>]
</div>