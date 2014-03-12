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
echo Hc_html::wrap_input(
	'&nbsp;',
	hc_bootstrap::nav_tabs(
		array(
			'single'	=> lang('time_single_day'),
			'multiple'	=> lang('time_multiple_days')
			),
		$this->hc_form->get_default('repeat'),
		'repeat'
		)
	);
?>

<?php
echo hc_bootstrap::tab_content(
	array(
		'single'	=> 
			Hc_html::wrap_input(
				$fields['date']['label'],
				$this->hc_form->build_input( $fields['date'] )
				) . 
			Hc_html::wrap_input(
				lang('time'),
				array(
					$this->hc_form->build_input( $fields['start'] ),
					' - ',
					$this->hc_form->build_input( $fields['end'] )
					)
				),
		'multiple'	=> 
			Hc_html::wrap_input(
				lang('time_dates'),
				array(
					$this->hc_form->build_input(
						array(
							'name'	=> 'date_start',
							'label'	=> lang('time_date_from'),
							'type'	=> 'date',
							)
						),
					' - ',
					$this->hc_form->build_input(
						array(
							'name'	=> 'date_end',
							'label'	=> lang('time_date_to'),
							'type'	=> 'date',
							)
						),
					)
				),
		),
	$this->hc_form->get_default('repeat')
	);
?>

<?php if( $this->hc_modules->exists('notes') ) : ?>
	<?php
	echo Hc_html::wrap_input(
		lang('common_notes'),
		$this->hc_form->build_input(
			array(
				'name'	=> 'notes',
				'type'	=> 'textarea',
				'rows'	=> 3
				)
			)
		);
	?>
<?php endif; ?>

<?php
echo Hc_html::wrap_input(
	'',
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