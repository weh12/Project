<?php
/*
Template Name:NULL
Description:我的资讯博客<br><a href="../?setting" target="_blank">设置</a>
Version:2.5.5
ForEmlog:6.0.1
Author:匡欣
Author Url:https://www.qiuzq.cn
*/
if(!defined('EMLOG_ROOT')) {exit('error!');}
define('Theme_Version' , '2.5.5' );	
if(isset($_GET["setting"]) && isset($_GET['config']) && ROLE == ROLE_ADMIN){ 
	require_once View::getView('inc/functions');
	plugin_setting();
	die;
}
require_once View::getView('module');
require_once View::getView('functions');
ob_clean();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="x-dns-prefetch-control" content="on">
	<link rel="dns-prefetch" href="<?php echo TEMPLATE_URL;?>"/>
	<meta http-equiv="Cache-Control" content="no-transform"/>
	<meta http-equiv="Cache-Control" content="no-siteapp"/>
	<meta property="og:type" content="blog"/>
	<meta property="og:image" content="<?php echo TEMPLATE_URL.'images/tx.jpg';?>"/>
	<meta property="og:title" content="<?php echo $site_title; ?>"/>
	<meta property="og:description" content="<?php echo $site_description; ?>"/>
	<meta property="og:author" content="<?php echo $T_data['username'].','.$T_data['email'];?>"/>
	<meta name="apple-mobile-web-app-capable" content="yes"/>
	<meta name="theme-color" content="<?php echo $T_data['t_color'];?>">
	<title><?php echo $site_title; ?></title>
	<meta name="keywords" content="<?php echo $site_key; ?>" />
	<meta itemprop="name" content="<?php echo $site_title; ?>"/>
	<meta itemprop="image" content="<?php echo TEMPLATE_URL.'images/tx.jpg';?>" />
	<meta name="description"  itemprop="description" content="<?php echo $site_description; ?>" />
	<meta name="author" content="<?php echo $T_data['username'].','.$T_data['email'];?>">
	<link rel="EditURI" type="application/rsd+xml" title="RSD" href="<?php echo BLOG_URL; ?>xmlrpc.php?rsd" />
	<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="<?php echo BLOG_URL; ?>wlwmanifest.xml" />
	<link rel="alternate" type="application/rss+xml" title="RSS"  href="<?php echo BLOG_URL; ?>rss.php" />
	<link rel="shortcut icon" type="<?php echo 'image/'.pathinfo(TEMPLATE_URL.'images/tx.jpg', PATHINFO_EXTENSION);?>" href="<?php echo TEMPLATE_URL.'images/tx.jpg';?>">
	<link rel="apple-touch-icon" href="<?php echo TEMPLATE_URL.'images/tx.jpg';?>">
	<link href="<?php echo TEMPLATE_URL; ?>style.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo TEMPLATE_URL; ?>assets/css/baguetteBox.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo TEMPLATE_URL; ?>assets/icomoon/style.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo TEMPLATE_URL; ?>assets/css/prism.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo TEMPLATE_URL; ?>assets/css/hint.css" rel="stylesheet" type="text/css" />
	<?php 
	if( $T_data['sqbg_mobile_open'] == "true" && em_is_mobile() || !em_is_mobile() ){
		if($T_data['sqbg_open'] == "true") echo '<link href="'.TEMPLATE_URL.'saoqi.css" rel="stylesheet" type="text/css" />';
	}
	?>
	<!--
	<script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script type='text/javascript' src='//cdn.jsdelivr.net/npm/pjax@0.2.6/pjax.min.js?ver=0.2.6'></script>
	-->
	<script type='text/javascript' src="<?php echo TEMPLATE_URL; ?>assets/js/jquery.min.js"></script>
	<script type='text/javascript' src='<?php echo TEMPLATE_URL; ?>assets/js/pjax.min.js?ver=0.2.6'></script>
	<script type='text/javascript' src="<?php echo TEMPLATE_URL; ?>assets/js/prism.js"></script>
	<!--[if lt IE 9]>
	<script src="//cdn.bootcss.com/html5shiv/r29/html5.min.js"></script>
	<script src="//cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
	<?php echo $T_data['t_color'] ? '<style type="text/css">html{--accent-color:'.$T_data['t_color'].'}</style>' : '';?>
	<style>
	.f-head .f-img,#links .list-item .cover {
		background: none;
		padding: 0;
	}
	</style>
	<?php echo $T_data['css'] ? '<style type="text/css">'.$T_data['css'].'</style>' : '';?>
	<?php doAction('index_head'); ?>
</head>
<body>
<?php if($T_data['cover_open'] == "true" && em_is_mobile()){?>
<div class="preview pop">	
	<img src="<?php echo TEMPLATE_URL."images/tx.jpg";?>">
	<span class="txt-item" style="top:calc(15% + 150px)"><?php echo $T_data['username']; ?></span>
	<span class="txt-item" style="top:calc(15% + 190px)"><?php echo $T_data['qianming']; ?></span>
</div>
<?php }?>
<div class="bg"></div><!-- 骚气背景 -->
<!-- 手机侧栏导航 -->
<div id="mo-nav" class="<?php if($T_data['bg_vh_open']) echo 'bg_vh'; ?>">
	<div class="_banner bg"></div>
	<div class="m-avatar">
		<span class="time"><i class="icon Q-calendar"></i><?php echo floor((time()-strtotime($T_data['riqi']))/86400); ?>天</span>
		<i class="icon Q-qrcode qrcode" data-src="<?php echo ($T_data['qrcode_option'] == "1") ? (TEMPLATE_URL.'images/qrcode.jpg') : (TEMPLATE_URL.'share/qrcode.php?data='.BLOG_URL);?>"></i>
		<img src="<?php echo TEMPLATE_URL.'images/tx.jpg';?>">
		<span class="name"><?php echo $T_data['username']; ?></span>
		<p class="qq_level"><?php echo qqlevel(count_log_all());?></p>
		<p class="nowrap"><?php echo $T_data['qianming']; ?></p>
	</div>
	<ul>
		<?php blog_navi();?>
	</ul>
</div>
<!-- end -->
<a href="javascript:(0);" class="header-off off-overlay"></a>

<section id="main">
<header class="header">
<div class="bg_xx"><div class="bg"></div></div><!-- 骚气背景 -->
  <section id="mobilebar" class="<?php if($T_data['mobile_color_open']) echo 't_color'; ?>">
	<div class="inner">
		<div class="col back"><a href="javascript:void(0);" class="header-btn"><span class="icon Q-menu"></span></a></div>
		<div class="col title"><a href="<?php echo BLOG_URL; ?>"><?php echo $blogname; ?></a></div>
		<div class="col switch"><a href="javascript:;" class="js-toggle-search"><span class="icon Q-search"></span></a></div>
	</div>
  </section>
<form class="js-search search-form" method="get" action="<?php echo BLOG_URL; ?>" role="search">
	<div class="search-form-inner">
		<div>
			<i class="icon Q-search"></i>
			<input class="text-input" type="search" name="keyword" placeholder="<?php echo $T_data['search_text']; ?>" autocomplete="off">
		</div>
	</div>
</form>

  <div class="width">
    <div class="menu">
	  <h1 class="logo"><a href="<?php echo BLOG_URL; ?>"><?php echo $blogname; ?></a></h1>
      <nav class="nav">
	    <ul>
	      <?php blog_navi();?>
	    </ul>
	  </nav>
	</div>
  </div>
	<?php if($T_data['bg_open']){ ?>
  <section class="banner bg" style="background-image: url(<?php echo $T_data['bg_url'] ? $T_data['bg_url'] : TEMPLATE_URL.'images/post.jpg';?>);" data-time="8000">
				<!--div id="banner-data" style="display:none">
					<img id="banner-0" src="<?php echo TEMPLATE_URL.'images/post.jpg';?>">
					<img id="banner-1" src="https://www.ianiu.cn/content/plugins/i_img/uploads/152007620650505.png">
					<img id="banner-2" src="https://www.ianiu.cn/content/uploadfile/tpl_options-master/bg.png">
				</div-->		
  </section>
	<?php } ?>

</header>
<!-- header -->

<div class="container">
  <div class="width">
    <div class="main">
<?php include View::getView('side_left');?>
	  <div class="main-central main-mod">
<?php 
if(isset($_GET["setting"])){include View::getView('setting');exit();}
if(isset($_GET["message"])){include View::getView('message');exit();}
?>
<?php if(blog_tool_ishome() || isset($_GET['post_type'])){?>
        <div id="post-type" class="style">
		<div class="bg_xx"><div class="bg"></div></div><!-- 骚气背景 -->
			<ul>
				<li class="<?php if ( !isset($_GET['post_type']) || (isset($_GET['post_type']) && ($_GET['post_type']=='any')) ) echo 'current'; ?>"><a class="post-type-link" ajaxhref="<?php echo BLOG_URL . '?' . http_build_query(array_merge($_GET, array('post_type' => 'any'))); ?>">全部</a></li>
				<li class="<?php if ( isset($_GET['post_type']) && ($_GET['post_type']=='new') ) echo 'current'; ?>"><a class="post-type-link" ajaxhref="<?php echo BLOG_URL . '?' . http_build_query(array_merge($_GET, array('post_type' => 'new'))); ?>">最新</a></li>
				<li class="<?php if ( isset($_GET['post_type']) && ($_GET['post_type']=='hot') ) echo 'current'; ?>"><a class="post-type-link" ajaxhref="<?php echo BLOG_URL . '?' . http_build_query(array_merge($_GET, array('post_type' => 'hot'))); ?>">最热</a></li>
				<li class="<?php if ( isset($_GET['message']) ) echo 'current'; ?>"><a class="post-type-link" ajaxhref="<?php echo BLOG_URL . '?message'; ?>">消息</a></li>
				<?php 
				if($T_data['l_sort_id']){
					$l_sort_id = explode(',',$T_data['l_sort_id']); 
					for($i=0;$i<count($l_sort_id);$i++){
						echo list_sort_navi($l_sort_id[$i]);
					} 
				}
				?> 
			</ul>
		</div>
<?php }?>
<?php 
if(isset($_GET["missevan"]) || isset($_GET["bilibili"]) || $_GET["plugin"] == 'micro_album'){
?>
        <div id="post-type" class="style">
		<div class="bg_xx"><div class="bg"></div></div><!-- 骚气背景 -->
			<ul>
				<li class="<?php echo $_GET["plugin"] == 'micro_album' ? 'current' : ''; ?>"><a class="post-type-link" href="<?php echo BLOG_URL . '?plugin=micro_album'; ?>">相册</a></li>
				<li class="<?php echo isset($_GET["bilibili"]) ? 'current' : ''; ?>"><a class="post-type-link" href="<?php echo BLOG_URL . '?bilibili'; ?>">相簿</a></li>
				<li class="<?php echo isset($_GET["missevan"]) ? 'current' : ''; ?>"><a class="post-type-link" href="<?php echo BLOG_URL . '?missevan'; ?>">图包</a></li>
			</ul>
		</div>
<?php
}
if(isset($_GET["missevan"])){include View::getView('missevan');exit();}
if(isset($_GET["bilibili"])){include View::getView('bilibili');exit();}
?>