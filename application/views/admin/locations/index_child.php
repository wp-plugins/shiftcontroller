<?php
$class = 'alert alert-success';
?>
<div class="<?php echo $class; ?>">

<?php if( ($this->{$this->model}->allow_none) OR (count($entries) > 1) ) : ?>
	<a class="close text-danger hc-confirm" href="<?php echo ci_site_url( array($this->conf['path'], 'delete', $e->id) ); ?>" title="<?php echo lang('common_delete'); ?>">
		&times;
	</a>
<?php endif; ?>

<a href="<?php echo ci_site_url( array($this->conf['path'], 'edit', $e->id) ); ?>" title="<?php echo lang('common_edit'); ?>">
	<?php echo $e->name; ?>
</a>

<br>
<div class="pull-left">
	<?php echo ci_anchor( array($this->conf['path'], 'up', $e->id), '<i class="fa fa-arrow-left"> </i>', 'title="' . lang('common_move_up') . '"' ); ?>
</div>

<div class="pull-right">
	<?php echo ci_anchor( array($this->conf['path'], 'down', $e->id), '<i class="fa fa-arrow-right"></i>', 'title="' . lang('common_move_down') . '"' ); ?>
</div>

<div class="clearfix"></div>

</div>