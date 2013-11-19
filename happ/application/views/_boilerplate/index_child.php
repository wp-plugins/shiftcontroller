<?php
$class = 'alert alert-regular alert-success';
?>
<div class="<?php echo $class; ?>">
<?php echo ci_anchor( array($this->conf['path'], 'delete', $e->id), '<i class="icon-remove text-error"></i>', 'class="close hc-confirm" title="' . lang('common_delete') . '"' ); ?>
<?php echo ci_anchor( array($this->conf['path'], 'edit', $e->id), '<strong>' . $e->title() . '</strong>' ); ?>
</div>