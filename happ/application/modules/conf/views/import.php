<div class="page-header">
<h2><?php echo lang('menu_conf_import');?></h2>
</div>

<div class="row">

<div class="col-md-6">
<?php echo form_open_multipart( 'admin/conf/import/do', array('class' => 'well')); ?>

<div class="control-group">
<label class="radio inline">
<?php echo form_radio('mode', 'overwrite', 'append'); ?><?php echo lang('conf_import_mode_overwrite'); ?>
</label>
<label class="radio inline">
<?php echo form_radio('mode', 'append', 'append'); ?><?php echo lang('conf_import_mode_append'); ?>
</label>
</div>

<?php
$f = array(
	'name'		=> 'userfile',
	'title'		=> 'userfile',
	);
?>
<div class="control-group">
<label>.csv only</label>
<div class="controls">  
<?php echo form_upload( $f ); ?>
</div>
</div>

<div class="controls">
<?php echo form_button( array('type' => 'submit', 'name' => 'submit', 'class' => 'btn btn-primary'), lang('common_upload')); ?>
</div>

<?php echo form_close(); ?>
</div>

<div class="col-md-4">
<p>
<?php echo lang('conf_import_help'); ?>: 
</p>
<ul>
<?php array_map( create_function('$e', 'echo "<li><strong>$e</strong></li>";'), $fields ); ?>
</ul>
<p>
<?php echo lang('location_products_file_help'); ?>

</div>


</div>