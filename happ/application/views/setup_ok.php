<div class="page-header">
<h2><?php echo $page_title; ?> OK</h2>
</div>

<?php
$brand_title = $this->config->item('nts_app_title');
?>
<p>
Thank you for trying <strong><?php echo $brand_title; ?></strong>! Please now proceed to the <a href="<?php echo ci_site_url(); ?>">start page</a>.

<?php if( $this->input->server('SERVER_NAME') != 'localhost') : ?>
<br><br><br><br>

<p>
<script type="text/javascript">
  var _paq = _paq || [];
  _paq.push(["trackPageView"]);
  _paq.push(["enableLinkTracking"]);

  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://www.fiammante.com/piwik/";
    _paq.push(["setTrackerUrl", u+"piwik.php"]);
    _paq.push(["setSiteId", "2"]);
	_paq.push(['trackGoal', 2]);
    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
    g.defer=true; g.async=true; g.src=u+"piwik.js"; s.parentNode.insertBefore(g,s);
  })();
</script>
<?php endif; ?>