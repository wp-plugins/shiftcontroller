<?php
if( ! $entries )
{
	echo lang('common_none');
	return;
}
reset( $entries );
$grouped_entries = array();
/* group entries by time */
foreach( $entries as $e )
{
	if( ! isset($grouped_entries[$e->action_time]) )
		$grouped_entries[$e->action_time] = array();

	$grouped_entries[$e->action_time][] = $e;
}
?>

<?php foreach( $grouped_entries as $action_time => $entries ) : ?>
	<?php
	$this->hc_time->setTimestamp( $action_time );
	$this_view = '';
	$this_view .= $this->hc_time->formatWeekdayShort();
	$this_view .= ', ';
	$this_view .= $this->hc_time->formatDate();
	$this_view .= ' ';
	$this_view .= $this->hc_time->formatTime();
	reset( $entries );
	?>

	<div class="panel panel-default">

		<div class="panel-heading">
			<?php echo $this_view; ?> <?php echo $entries[0]->user->get()->title(TRUE); ?>
		</div>

		<div class="panel-body">

		<?php foreach( $entries as $e ) : ?>
			<div class="row">

			<?php
			$pname = $object->prop_name( $e->property_name );
			?>
			<?php if( $pname == 'id' ) : ?>
				<div class="col-sm-12">
					<?php echo lang('common_create'); ?>
				</div>
			<?php else : ?>
				<div class="col-sm-2">
					<?php echo $object->prop_label( $pname ); ?>
				</div>
				<div class="col-sm-10">
					<?php
					if( is_object($object->{$pname}) )
					{
						$pclass = get_class($object->{$pname});
						$pobject = new $pclass;

						$old_view = lang('common_na');
						$new_view = lang('common_na');

						if( $e->old_value )
						{
							$pobject->get_by_id( $e->old_value );
							if( $pobject->exists() )
								$old_view = $pobject->title( TRUE );
							else
								$old_view = lang('common_na');
						}
						if( $e->new_value )
						{
							$pobject->get_by_id( $e->new_value );
							if( $pobject->exists() )
								$new_view = $pobject->title( TRUE );
							else
								$new_view = lang('common_na');
						}

						if( ($e->old_value) && (! $e->new_value) )
						{
							$old_view = '<span style="text-decoration: line-through;">' . $old_view . '</span>';
							$new_view = '';
						}
						elseif( (! $e->old_value) && ($e->new_value) )
						{
							$old_view = '';
						}
					}
					else
					{
						$old_view = $object->prop_text( $pname, TRUE, $e->old_value );
						$new_view = $object->prop_text( $pname, TRUE, $e->new_value );
					}
					?>
					<?php if( $old_view ) : ?>
						<?php echo $old_view; ?>
					<?php endif; ?>

					<?php if( $new_view && $old_view ) : ?>
						<i class="fa-fw fa fa-arrow-right"></i>
					<?php endif; ?>

					<?php if( $new_view ) : ?>
						<?php echo $new_view; ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			</div>
		<?php endforeach; ?>
		</div>

	</div>
<?php endforeach; ?>