<?php 
/*
* bilibili.php
* B站相簿
*/
if(!defined('EMLOG_ROOT')) {exit('error!');} 
?>
<?php
function bilibili_album($uid){
	$xx='uid='.$uid.'&page_num=0&page_size=60&biz=all';
	$url='https://api.vc.bilibili.com/link_draw/v1/doc/doc_list?'.$xx;
	$json_url = EMLOG_ROOT.'/content/uploadfile/null_file/json/'.$xx.'.json';
	if( !is_file($json_url) || ( ( date('Ymd',time()) - date('Ymd',filemtime($json_url)) ) <= 1 ) ){
		$json = myCurl($url,'110.43.34.72');
		file_put_contents($json_url, $json);
	}else{
		$json = file_get_contents($json_url);
	}
	
	$arr = json_decode($json,true);
	?>
	    <section class="style books">
		<div class="bg_xx"><div class="bg"></div></div><!-- 骚气背景 -->
			<header class="entry-header">
					<h1 class="title" itemprop="name">Bilibili相簿</h1>
					<div class="meta">UID：<?php echo $uid; ?></div>
			</header>
			<div class="single">
		    <div id="gallery-3" class="gallery">
	<?php
	foreach($arr['data']['items'] as $b){
		$bimg = $b['pictures'][0]['img_src'];
		/*
		$filename = pathinfo($bimg, PATHINFO_BASENAME);
		$dataurl = EMLOG_ROOT.'/content/templates/NULL/inc/img/'.$uid.'/';
		if(!is_dir($dataurl)) mkdir($dataurl);
		if( !is_file($dataurl.$filename) ){
			$f = myCurl($bimg,'110.43.34.72');
			file_put_contents($dataurl.$filename, $f);
		}
		$dimg = TEMPLATE_URL.'inc/img/'.$uid.'/'.$filename;
		*/
		$dimg = TEMPLATE_URL.'inc/api.php?bimg='.$bimg;
		$doc_url = BLOG_URL.'?bilibili&doc_id='.$b['doc_id'];
		$item = '<dl class="gallery-item"><dt><a href="'.$dimg.'" no-pjax><img src="'.$dimg.'@300w_1e.webp"></a></dt></dl>';
		$items = '<dl class="gallery-item"><dt><a href="'.$doc_url.'" title="'.$b['title'].'"><img src="'.$dimg.'@300w_1e.webp" style="cursor: pointer;"><div style="background: rgba(0, 0, 0, 0.5);font-size: 14px;position: absolute;bottom: 2%;right: 2%;text-align: center;color: #fff;padding: 0 5px;border-radius: 5px;">'.$b['count'].'P</div></a></dt></dl>';
		echo $b['count'] > 1 ? $items : $item;
	}
	?>
		    </div>
			</div>
        </section>
<?php
}
//https://api.vc.bilibili.com/link_draw/v1/doc/detail?doc_id=14885562
function bilibili_doc_id($doc_id){
	$xx='doc_id='.$doc_id;
	$url='https://api.vc.bilibili.com/link_draw/v1/doc/detail?'.$xx;
	$json_url = EMLOG_ROOT.'/content/uploadfile/null_file/json/'.$xx.'.json';
	if( !is_file($json_url) ){
		$json = myCurl($url,'110.43.34.72');
		file_put_contents($json_url, $json);
	}else{
		$json = file_get_contents($json_url);
	}
	$arr = json_decode($json,true);
	$title = $arr['data']['item']['title'];
	$user = '<a href="https://space.bilibili.com/'.$arr['data']['user']['uid'].'" target="_blank" no-pjax>'.$arr['data']['user']['name'].'</a>';
	?>
	    <section class="style books">
		<div class="bg_xx"><div class="bg"></div></div><!-- 骚气背景 -->
			<header class="entry-header">
					<h1 class="title" itemprop="name"><?php echo $title ? $title : '标题逃跑了~~';?></h1>
					<div class="meta">发布于 <?php echo date('Y年m月d日', $arr['data']['item']['upload_timestamp']);?> / <?php echo $arr['data']['item']['view_count'];?> 次围观 / <?php echo $user;?></div>
			</header>
			<div class="single">
		    <div id="gallery-3" class="gallery">
	<?php
	foreach($arr['data']['item']['pictures'] as $b){
		$bimg = $b['img_src'];
		$dimg = TEMPLATE_URL.'inc/api.php?bimg='.$bimg;
		echo '<dl class="gallery-item"><dt><a href="'.$dimg.'" no-pjax><img src="'.$dimg.'@300w_1e.webp"></a></dt></dl>';
	}
	?>
		    </div>
			</div>
        </section>
<?php
}
?>
<?php 
	//bilibili_album($_GET["uid"]); 
	if(!isset($_GET["doc_id"])){
		bilibili_album($T_data["b_uid"]);
	}elseif(isset($_GET["doc_id"])){
		bilibili_doc_id($_GET["doc_id"]);
	}
?>
<?php include View::getView('footer');?>