<?php
$brand_title = $this->config->item('nts_app_title');
$brand_url = $this->config->item('nts_app_url');
?>
<?php if( 0 ) : ?>
	<div class="row" style="margin-top: 1em;">
	<small>Powered by <a href="<?php echo $brand_url; ?>"><?php echo $brand_title; ?></a></small>
	</div>
<?php endif; ?>

<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('.hc-multiselect').multiselect();
});
</script>