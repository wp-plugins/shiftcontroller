<?php
$status_classes = array(
	USER_MODEL::STATUS_ACTIVE	=> 'success',
	USER_MODEL::STATUS_ARCHIVE	=> 'archive',
	);

$fields = $this->fields;
$heading = array(
	lang('user_full_name'),
	lang('common_email'),
	lang('user_level'),
	''
	);
$this->table->set_heading( $heading );

reset( $entries );
foreach( $entries as $e ){
	$row = array();

// full name
	$row[] = ci_anchor( array($this->conf['path'], 'edit', $e->id), $e->full_name(), 'title="' . lang('common_edit') . '"' );

// email
	$row[] = ci_anchor( array($this->conf['path'], 'edit', $e->id), $e->email, 'title="' . lang('common_edit') . '"' );

// level
	$row[] = $e->prop_text('level');

// status
	$status_label = $e->prop_text('active', TRUE );

// actions
	$regular_actions = array();

	$status_actions = array();
	if( $this->auth->check() != $e->id )
	{
		if( $e->active )
			$status_actions = array(
				ci_anchor( 
					array($this->conf['path'], 'disable', $e->id),
					$e->prop_text('active', TRUE, 0),
					'title="' . $e->prop_text('active', FALSE, 0) . '"'
					),
				);
		else
		{
			$status_actions = array(
				ci_anchor( 
					array($this->conf['path'], 'disable', $e->id),
					$e->prop_text('active', TRUE, 1),
					'title="' . $e->prop_text('active', FALSE, 1) . '"'
					),
				);
		}
	}

	$actions = '';
	if( $status_actions )
	{
		$actions .= join( '', array_map(create_function('$a', 'return "<li>" . $a . "</li>";'), $status_actions) );
	}
	if( $regular_actions )
	{
		$actions .= '<li class="divider"></li>';
		$actions .= join( '', array_map(create_function('$a', 'return "<li>" . $a . "</li>";'), $regular_actions) );
	}

	if( $actions )
	{
		$action =<<<EOT
<div class="dropdown">
	$status_label <a class="dropdown-toggle" data-toggle="dropdown" href="#">
		<b class="caret"></b>
	</a>
	<ul class="dropdown-menu">
		$actions
	</ul>
</div>
EOT;
	}
	else
	{
		$action = $status_label;
	}
	$row[] = $action;
	$this->table->add_row( $row );
	}
?>

<ul class="nav nav-pills">
<?php foreach( $statuses as $t => $count ) : ?>
<?php
			$tab_class = isset($status_classes[$t]) ? 'tab-' . $status_classes[$t] : '';

			$active = ($t == $status) ? TRUE : FALSE;
			$class = array();
			if( $active )
			{
				$class[] = 'active';
				if( $tab_class )
					$class[] = $tab_class;
			}
			$class = join( ' ', $class );
?>
		<li class="<?php echo $class; ?>">
<?php
		$label = $this->{$this->model}->prop_text('active', FALSE, $t);
		$label .= ' [' . $count . ']';
		echo ci_anchor( 
			array($this->conf['path'], 'index', $t),
			$label,
			'title="' . $this->{$this->model}->prop_text('active', FALSE, $t) . '"'
		);
?>
	</li>
<?php endforeach; ?>
</ul>


<?php if( count($entries) ) : ?>

	<?php echo $this->table->generate(); ?>

	<div class="row">
	<?php echo $this->pagination->create_links(); ?>
	</div>

<?php else : ?>

	<p>
	<?php echo lang('common_none'); ?>
	</p>

<?php endif; ?>