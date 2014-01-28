<table class="table table-striped table-condensed">
<tr>
	<th><?php echo lang('time_date'); ?> & <?php echo lang('time'); ?></th>
	<?php if( ! $user_id ) : ?>
		<th><?php echo lang('user'); ?></th>
	<?php endif; ?>
	<th>IP</th>
</tr>

<?php foreach( $entries as $e ) : ?>
	<tr>
		<td>
			<?php
			$this->hc_time->setTimestamp( $e->action_time );
			$this_view = '';
			$this_view .= $this->hc_time->formatWeekdayShort();
			$this_view .= ', ';
			$this_view .= $this->hc_time->formatDate();
			$this_view .= ' ';
			$this_view .= $this->hc_time->formatTime();
			?>
			<?php echo $this_view; ?>
		</td>

		<?php if( ! $user_id ) : ?>
			<td>
				<?php echo $e->user_first_name; ?> <?php echo $e->user_last_name; ?> [<?php echo $e->user_email; ?>]
			</td>
		<?php endif; ?>

		<td>
			<?php echo $e->remote_ip; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>