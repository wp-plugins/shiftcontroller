<?php
$CI =& ci_get_instance();
$app_title = $CI->config->item('nts_app_title');
?>
<?php
echo form_open(
	'wordpress/admin/users/syncrun',
	array(
		'class' => 'form-horizontal form-condensed'
		)
	);
?>

<fieldset>
	<legend>Copy User Accounts</legend>

	<table class="table table-striped table-condensed">
		<tr>
			<th>
				
			</th>
			<th>
				<?php echo $app_title; ?>
			</th>
		</tr>

		<?php foreach( $wordpress_roles as $role_value => $role_name ) : ?>
			<?php
			$this_role_count = ( isset($wordpress_count_users['avail_roles'][$role_value]) ) ? $wordpress_count_users['avail_roles'][$role_value] : 0;
			?>
			<tr class="<?php echo $this_role_count ? 'success' : ''; ?>">
				<td>
					<?php echo $role_name; ?> [<?php echo $this_role_count; ?>]
				</td>
				<td>
					<?php
					if( $role_value )
					{
						if( $role_value == 'administrator' )
						{
							$default = USER_MODEL::LEVEL_ADMIN;
						}
						else
						{
							if( $this_role_count )
								$default = USER_MODEL::LEVEL_STAFF;
							else
								$default = 0;
						}
					}
					else
						$default = 0;

					$field_name = 'role_' . $role_value;
					$options = array(
						USER_MODEL::LEVEL_ADMIN		=> lang('user_level_admin'),
						USER_MODEL::LEVEL_MANAGER	=> lang('user_level_manager'),
						USER_MODEL::LEVEL_STAFF		=> lang('user_level_staff'),
						0							=> lang('common_none'),
						);

					echo $this->hc_form->input(
						array(
							'type'		=> 'dropdown',
							'name'		=> $field_name,
							'options'	=> $options,
							'default'	=> $default,
							)
						);
					?>
				</td>
			</tr>
		<?php endforeach; ?>

		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<?php
				echo $this->hc_form->input(
					array(
						'type'	=> 'checkbox',
						'name'	=> 'append_role_name',
						)
					);
				?> Append Original Role Name To Staff Name
			</td>
		</tr>

		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<?php
				echo form_button( 
					array(
						'type' => 'submit',
						'name' => 'submit',
						'class' => 'btn btn-primary'
						),
					lang('common_go')
					);
				?>
			</td>
		</tr>
	</table>
</fieldset>

<?php echo form_close();?>