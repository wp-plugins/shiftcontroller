<?php echo form_open( join('/', array_merge(array($this->conf['path'], 'save', 0), $args) ), array('class' => 'form-horizontal form-condensed')); ?>

<?php
echo $this->hc_form->input(
	array(
		'name'	=> 'repeat',
		'type'	=> 'hidden'
		)
	);
?>

<?php
echo hc_bootstrap::input(
	hc_bootstrap::nav_tabs(
		array(
			'single'	=> lang('time_single_day'),
			'multiple'	=> lang('time_multiple_days')
			),
		$this->hc_form->get_default('repeat'),
		'repeat'
		),
	'&nbsp;'
	);
?>

<?php
echo hc_bootstrap::tab_content(
	array(
		'single'	=> 
			hc_bootstrap::input(
				$this->hc_form->input( $fields['date'] ),
				$fields['date']['label'],
				$this->hc_form->error('date')
				) . 
			hc_bootstrap::input(
				$this->hc_form->input( $fields['start'] ) .
				' - ' .
				$this->hc_form->input( $fields['end'] ),
				lang('time'),
				$this->hc_form->error('start') OR $this->hc_form->error('end')
				),
		'multiple'	=> 
			hc_bootstrap::input(
				$this->hc_form->input(
					array(
						'name'	=> 'date_start',
						'label'	=> lang('time_date_from'),
						'type'	=> 'date',
						)
					) . 
				' - ' . 
				$this->hc_form->input(
					array(
						'name'	=> 'date_end',
						'label'	=> lang('time_date_to'),
						'type'	=> 'date',
						)
					),
				lang('time_dates'),
				$this->hc_form->error('date_start') OR $this->hc_form->error('date_end')
				),
		),
	$this->hc_form->get_default('repeat')
	);
?>

<?php if( $this->hc_modules->exists('notes') ) : ?>
	<?php
	echo hc_bootstrap::input(
		$this->hc_form->input(
			array(
				'name'	=> 'notes',
				'type'	=> 'textarea',
				'rows'	=> 3
				)
			),
		lang('common_notes')
		);
	?>
<?php endif; ?>

<?php
echo hc_bootstrap::form_actions(
	form_submit( 
		array(
			'name' => 'submit',
			'class' => 'btn btn-primary'
			),
		lang( $this->conf['entity'] . '_add' )
		)
	);
?>

<?php echo form_close(); ?>