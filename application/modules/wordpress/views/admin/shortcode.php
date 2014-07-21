<p>
With the following shoftcode you can insert your "Everyone's Schedule" into a post or a page. 
Please also make sure that you set the <a href="<?php echo ci_site_url('conf/admin'); ?>">Who can view everyone's schedule</a> setting to <strong>Everyone</strong>.
</p>

<p>
<code>[<?php echo $shortcode; ?>]</code>
</p>

<p>
You can also supply additional parameters to control the display:
</p>

<ul>
	<li>
		<strong>range</strong>: <em>week</em> [default], <em>month</em>
	</li>
	<li>
		<strong>start</strong>: <em>yyyymmdd</em>, for example <em>20140803</em>
	</li>
</ul>

<p>
For example:
</p>
<p>
<code>[<?php echo $shortcode; ?> range="month" start="20140801"]</code>
</p>
