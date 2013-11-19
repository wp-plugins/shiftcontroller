<?php
$class = 'alert alert-success';
?>
<div class="<?php echo $class; ?>">
<?php if( ($this->{$this->model}->allow_none) OR (count($entries) > 1) ) : ?>
	<?php echo ci_anchor( array($this->conf['path'], 'delete', $e->id), '&times;', 'class="close hc-confirm" title="' . lang('common_delete') . '"' ); ?>
<?php endif; ?>
<?php echo ci_anchor( array($this->conf['path'], 'edit', $e->id), '<strong>' . $e->name . '</strong>' ); ?>

<br>
<div class="pull-left">
<?php echo ci_anchor( array($this->conf['path'], 'up', $e->id), '<i class="icon-arrow-left"> </i>', 'title="' . lang('common_move_up') . '"' ); ?>
</div>

<div class="pull-right">
<?php echo ci_anchor( array($this->conf['path'], 'down', $e->id), '<i class="icon-arrow-right"></i>', 'title="' . lang('common_move_down') . '"' ); ?>
</div>

<div class="clearfix"></div>

</div>