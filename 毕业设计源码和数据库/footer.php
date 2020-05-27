<?php 
/**
 * 页面底部信息
 */
if(!defined('EMLOG_ROOT')) {exit('error!');} 
?>
	  </div>
	  <!-- main-central -->
<?php include View::getView('side_right');?>
	</div>
	<!-- main -->
  </div>
</div>
<!-- container -->
<footer class="footer">
  <div class="width">
    <div class="footer-content">
		<?php echo preg_replace(
		array(
			'/\{BLOG_URL\}/i',
			'/\{blogname\}/i',
			'/\{icp\}/i',
			'/\{EMLOG_VERSION\}/i',
			'/\{PHP_VERSION\}/i',
			'/\{footer_info\}/i'
		),
		array(
			BLOG_URL,
			$blogname,
			$icp,
			Option::EMLOG_VERSION,
			PHP_VERSION,
			$footer_info
		),
		$T_data['footer']
		);?>
    </div>
  </div>
</footer>
<!-- footer -->
</section>

<!-- #main -->
<a class="back2top"><i class="icon Q-rocket"></i></a>
<?php if($T_data['cover_open'] == "true" && em_is_mobile()){?>
<script type="text/javascript">
    setTimeout(function(){
        document.querySelector('.preview').style.visibility = 'hidden'
    },3000)
</script>
<?php }?>
<script src="<?php echo TEMPLATE_URL; ?>assets/js/baguettebox.js"></script>
<?php 
	$json = array(
		'TEMPLATE_URL' => TEMPLATE_URL,
		'BLOG_URL' => BLOG_URL,
		'Theme_Version' => Theme_Version
	);
	echo '<script type="text/javascript">var Null_data = ' . json_encode($json, JSON_UNESCAPED_UNICODE) . ';</script>';
?>
<script src="<?php echo TEMPLATE_URL; ?>assets/js/main.js"></script>
<script src="<?php echo TEMPLATE_URL; ?>assets/js/lazyload.min.js" type="text/javascript"></script>
<?php echo $T_data['js'] ? '<script type="text/javascript">'.$T_data['js'].'</script>' : '';?>
<?php doAction('index_footer'); ?>
</body>
</html>