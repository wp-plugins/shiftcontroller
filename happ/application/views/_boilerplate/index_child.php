<?php
$class = 'alert alert-success';
?>
<div class="<?php echo $class; ?>">
<?php echo ci_anchor( array($this->conf['path'], 'delete', $e->id), '<i class="fa fa-times text-danger"></i>', 'class="close hc-confirm" title="' . lang('common_delete') . '"' ); ?>
<?php echo ci_anchor( array($this->conf['path'], 'edit', $e->id), '<strong>' . $e->title() . '</strong>' ); ?>
</div>