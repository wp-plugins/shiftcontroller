<?php
$my_user_id = $this->auth->user()->id;
$is_my = ( $my_user_id == $sh->user_id ) ? TRUE : FALSE;

$notes = array();
if( $this->hc_modules->exists('notes') )
{
	$notes = $this->access_manager->filter_see( $sh->note->get()->all );
}
$this->hc_time->setDateDb( $sh->date );

$date_view = '';
$date_view .= $this->hc_time->formatWeekdayShort();
$date_view .= ', ';
$date_view .= $this->hc_time->formatDate();

$show_end_time_for_staff = $this->app_conf->get( 'show_end_time_for_staff' );
$time_view = hc_format_time_of_day($sh->start);
if( $show_end_time_for_staff )
	$time_view .= ' - ' . hc_format_time_of_day($sh->end);

$conflicts = $sh->conflicts();
$container_class = $conflicts ? 'alert-danger' : 'alert-none';
?>
<div class="alert alert-condensed <?php echo $container_class; ?>">
	<ul class="list-unstyled list-separated">
		<li>
			<i class="fa-fw fa fa-calendar"></i>

			<?php if( $is_my ) : ?>
				<a href="<?php echo ci_site_url( array($this->conf['path'], 'edit', $sh->id) ); ?>">
					<?php echo $date_view; ?>
				</a>
			<?php else : ?>
				<?php echo $date_view; ?>
			<?php endif; ?>
		</li>

		<li>
			<i class="fa-fw fa fa-clock-o"></i> <?php echo $time_view; ?>
		</li>

		<?php if( $location_count > 1 ) : ?>
			<li>
				<i class="fa-fw fa fa-home"></i> <?php echo $sh->location_name; ?>
			</li>
		<?php endif; ?>

		<?php if( $conflicts ) : ?>
			<li>
				<i class="fa-fw fa fa-exclamation-circle text-danger"></i> <?php echo lang('shift_conflicts'); ?>
			</li>
		<?php endif; ?>

		<?php if( (! $is_my) && $sh->user_id ) : ?>
			<li>
				<i class="fa-fw fa fa-exchange"></i> <?php echo $sh->user->get()->full_name(); ?>
			</li>
		<?php endif; ?>

		<?php if( count($notes) > 0 ) : ?>
			<?php foreach( $notes as $n ) : ?>
				<li style="font-style: italic;">
					<i class="fa-fw fa fa-comment-o"></i> 
					<?php echo $n->content; ?>
				</li>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php if( ! $is_my ) : ?>

			<?php 
			if( 
				( $this->hc_modules->exists('shift_trades') && $sh->has_trade ) OR 
				( ! $sh->user_id )
				): ?>
				<a class="btn btn-default btn-sm" title="<?php echo lang('shift_pick_up'); ?>" href="<?php echo ci_site_url( array('staff/shifts', 'pickup', $sh->id) ); ?>">
					<i class="fa fa-check text-success"></i> <?php echo lang('shift_pick_up'); ?>
				</a>
			<?php endif; ?>

		<?php else : ?>

			<?php if( $this->hc_modules->exists('shift_trades') ) : ?>
				<?php if( $sh->has_trade ) : ?>
					<a class="btn btn-default btn-sm" title="<?php echo lang('trade_recall'); ?>" href="<?php echo ci_site_url( array('shift_trades/staff', 'recall', $sh->id) ); ?>">
						<i class="fa fa-exchange text-danger"></i> <?php echo lang('trade_recall'); ?>
					</a>
				<?php else : ?>
					<a class="btn btn-default btn-sm" title="<?php echo lang('trade'); ?>" href="<?php echo ci_site_url( array('shift_trades/staff', 'trade', $sh->id) ); ?>">
						<i class="fa fa-exchange text-success"></i> <?php echo lang('trade'); ?>
					</a>
				<?php endif; ?>
			<?php endif; ?>

		<?php endif; ?>
	</ul>
</div>