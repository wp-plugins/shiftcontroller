<?php if( ! (isset($is_module) && $is_module) ) : ?>
	<?php if( $message ) : ?>
	<div class="alert alert-success">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<?php	if( is_array($message) ) : ?>
	<ul>
	<?php 		foreach( $message as $m ) : ?>
	<li><?php 		echo $m; ?></li>
	<?php 		endforeach; ?>
	</ul>
	<?php 	else : ?>
	<?php 		echo $message;?>
	<?php 	endif; ?>
	</div>
	<?php endif; ?>

	<?php if( $error ) : ?>
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php if( is_array($error) ) : ?>
				<ul>
				<?php foreach( $error as $e ) : ?>
					<li>
						<?php echo $e; ?>
					</li>
				<?php endforeach; ?>
				</ul>
			<?php else : ?>
	<?php		echo $error;?>
	<?php	endif; ?>
	</div>
	<?php endif; ?>
<?php endif; ?>

<?php $this->load->view( $include ); ?>