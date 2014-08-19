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

<ul>
	<li>
		<strong>start</strong>: <em>yyyymmdd</em>, for example <em>20140901</em>
	</li>
	<li>
		<strong>end</strong>: <em>yyyymmdd</em>, for example <em>20140930</em>
	</li>
	<li>
		<strong>location</strong>: <em>location id</em>, for example <em>2</em>. You can find out the id of a location in <a href="<?php echo ci_site_url('admin/locations'); ?>">Configuration &gt; Locations</a>
	</li>
</ul>

<p>
For example:
</p>
<p>
<code>[<?php echo $shortcode; ?> start="20140901" end="20140930" location="2"]</code>
</p>
