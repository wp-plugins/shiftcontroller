<div class="row">
	<div class="col-md-7">
		<ul class="list-inline">
			<li>
				<ul class="nav nav-pills">
					<?php require( dirname(__FILE__) . '/_filter.php' ); ?>
				</ul>
			</li>
			<li>
				<ul class="nav nav-tabs">
					<?php require( dirname(__FILE__) . '/_display.php' ); ?>
				</ul>
			</li>
		</ul>
	</div>

	<div class="col-md-5">
		<ul class="list-inline">
			<?php if( $display == 'calendar' ) : ?>
				<li class="pull-right">
					<?php require( dirname(__FILE__) . '/_date_navigation.php' ); ?>
				</li>
			<?php else : ?>
				<li class="pull-right">
					<?php require( dirname(__FILE__) . '/_date_range.php' ); ?>
				</li>
				<li class="pull-right">
					<?php require( dirname(__FILE__) . '/_export.php' ); ?>
				</li>
			<?php endif; ?>
		</ul>
	</div>
</div>
