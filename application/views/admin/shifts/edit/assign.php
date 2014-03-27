<?php
$free_staff = $object->find_staff();
?>

<?php if( ! $free_staff ) : ?>
	<li>
		<a href="#" title="<?php echo lang('shift_no_staff'); ?>">
			<i class="fa fa-exclamation-circle text-danger"></i> <?php echo lang('shift_no_staff'); ?>
		</a>
	</li>
<?php else : ?>

	<?php foreach( $free_staff as $st ) : ?>
		<?php
		$href = ci_site_url( 
			array(
				'admin/shifts/save',
				$object->id,
				'user', $st->id,
				)
			);
		?>

		<?php if( $st->warning ) : ?>
			<?php $warning_label = $st->warning->title(); ?>
			<li>
				<a href="<?php echo $href; ?>" title="<?php echo $warning_label; ?>" class="hc-confirm">
					<i class="fa fa-fw fa-user text-danger"></i> <?php echo $st->full_name(); ?>
					<br>
					<?php echo $warning_label; ?>
				</a>
			</li>
		<?php else : ?>
			<li>
				<a href="<?php echo $href; ?>" title="<?php echo lang('shift_assign_staff'); ?>">
					<i class="fa fa-fw fa-user text-success"></i> <?php echo $st->full_name(); ?>
				</a>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>

<?php endif; ?>

<li class="divider"></li>