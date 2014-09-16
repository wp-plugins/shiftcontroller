<?php if( ! (isset($is_module) && $is_module) ) : ?>
	<?php if( $message ) : ?>
		<?php if( is_array($message) ) : ?>
			<ul class="list-unstyled">
				<?php foreach( $message as $m ) : ?>
					<li>
						<div class="alert alert-success">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<?php echo $m; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<?php echo $message;?>
			</div>
		<?php endif; ?>
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
				<?php echo $error;?>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<?php if( isset($debug_message) && $debug_message ) : ?>
		<?php if( is_array($debug_message) ) : ?>
			<ul class="list-unstyled">
				<?php foreach( $debug_message as $m ) : ?>
					<li>
						<div class="alert alert-warning">
							<button type="button" class="close" data-dismiss="alert">&times;</button>
							<?php echo $m; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<div class="alert alert-warning">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<?php echo $debug_message;?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>

<?php $this->load->view( $include ); ?>