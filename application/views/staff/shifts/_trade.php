<?php
$menu = array();

if( $is_my )
{
	if( $sh->has_trade )
	{
		$menu[] = array(
			'title'	=> '<i class="fa fa-exchange text-danger"></i> ' . lang('trade_recall'),
			'href'	=> ci_site_url( array('shift_trades/staff', 'recall', $sh->id) ),
			);
	}
	else
	{
		$menu[] = array(
			'title'	=> '<i class="fa fa-exchange"></i> ' . lang('shift_list_trade'),
			'href'	=> ci_site_url( array('shift_trades/staff', 'trade', $sh->id) ),
			);
	}
}
?>

<?php if( ! $is_my ) : ?>
	<li>
		<i class="fa-fw fa fa-user"></i> <?php echo $sh->user->get()->title(); ?>
	</li>
<?php endif; ?>

<?php if( count($menu) == 1 ) : ?>
	<?php list( $title, $title_icon ) = Hc_lib::parse_icon( $menu[0]['title'] ); ?>
	<li>
		<?php echo $title_icon; ?>
		<a class="btn btn-default btn-sm" title="<?php echo $title; ?>" href="<?php echo $menu[0]['href']; ?>">
			<?php echo $title; ?>
		</a>
	</li>

<?php else : ?>
	<?php echo $title_icon; ?> 
	<a href="#" data-toggle="dropdown">
		<?php echo $title; ?> <b class="caret"></b>
	</a>
	<?php
	echo Hc_html::dropdown_menu($menu);
	?>
<?php endif; ?>
