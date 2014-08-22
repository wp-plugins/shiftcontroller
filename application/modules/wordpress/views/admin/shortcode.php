<p>
With the following shoftcode you can insert your <strong>Everyone's Schedule</strong> view into a post or a page. 
</p>

<p>
<code>[<?php echo $shortcode; ?>]</code>
</p>

<p>
By default, this view will display all active upcoming shifts for all the locations. 
If need to, you can adjust it by supplying additional parameters to control the display:
</p>

<ul class="list-separated">
	<li>
		<strong>start</strong>: <em>yyyymmdd</em>, for example <em>20140901</em>. If not supplied, it will start from the current date.
	</li>
	<li>
		<strong>end</strong>: <em>yyyymmdd</em>, for example <em>20140930</em>. If not supplied, it will show all shifts until the last one.
	</li>
	<li>
		<strong>range</strong>
		<ul style="margin-left: 2em;">
			<li>
				<em>week</em>: it will display shifts starting from Sunday (or Monday) of the current week regardless of the current week day.
			</li>
			<li>
				<em>month</em>: it will display shifts starting from the 1st of the current month regardless of the current date.
			</li>
			<li>
				<em>5 days</em>, <em>2 weeks</em>, etc: it will display shifts starting from the current date (of the one set by <em>start</em>) for the period given.
			</li>
		</ul>
		Please note that if <strong>range</strong> is given, it will overwrite the <strong>end</strong> setting.
	</li>
	<li>
		<strong>location</strong>: <em>location id</em>, for example <em>2</em>. You can find out the id of a location in <a href="<?php echo ci_site_url('admin/locations'); ?>">Configuration &gt; Locations</a>. If not supplied, it will display shifts of all locations.
	</li>
	<li>
		<strong>staff</strong>: <em>staff id</em>, for example <em>3</em>. You can find out the id of an employee in <a href="<?php echo ci_site_url('admin/users'); ?>">Users</a>. If not supplied, it will display shifts of all employees.
	</li>
</ul>

<p>
Examples
</p>

<p>
September in location #2:
</p>

<p>
<code>[<?php echo $shortcode; ?> start="20140901" end="20140930" location="2"]</code>
</p>

<p>
Current week:
</p>

<p>
<code>[<?php echo $shortcode; ?> range="week"]</code>
</p>

<p>
Next three days:
</p>

<p>
<code>[<?php echo $shortcode; ?> range="3 days"]</code>
</p>
