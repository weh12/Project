<?php 
/**
 * 局部函数库
 */
if(!defined('EMLOG_ROOT')) {exit('error!');}
//安装检测
$setted_inc = EMLOG_ROOT."/content/templates/".Option::get('nonce_templet')."/inc/setted.".Option::get('nonce_templet').".inc";
$install_php = EMLOG_ROOT."/content/templates/".Option::get('nonce_templet').'/install.php';
if( !is_file($setted_inc) && is_file($install_php) && !is_file($install_php.'.bak') ){
    emDirect(TEMPLATE_URL.'install.php');
    exit();
}elseif( is_file($setted_inc) && is_file($install_php) && !is_file($install_php.'.bak') ){
	rename($install_php,$install_php.'.bak');
}
/*
elseif( file_get_contents($setted_inc) != $_SERVER['HTTP_HOST'] ){
	exit('兄dei，你想干啥勒？<img src="http://image.bee-ji.com/136354">');
}
*/

//表情
function owo($content){
	//$content = ob_get_clean();
	return preg_replace("#\[smilies(\d+)\]#i",'<img src="'.TEMPLATE_URL.'images/face/$1.gif" id="smilies$1"/>',$content);
}


//检测是否为手机
function em_is_mobile() {
    static $is_mobile;

    if ( isset($is_mobile) )
        return $is_mobile;

    if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
        $is_mobile = false;
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
            $is_mobile = true;
    } else {
        $is_mobile = false;
    }

    return $is_mobile;
}
//分离功能
function separate(){
	global $T_data;
	if ($T_data['preview_open'] == "true") return em_is_mobile();
}
//QQlevel，以文章数来计算
function qqlevel($num){
	if ($num<1) $nggl='<i class="QQlevel i_0"></i>';
	while($num>=64){
		$level.='<i class="QQlevel i_64"></i>';
		$num-=64;
	}
	while($num>=16){
		$level.='<i class="QQlevel i_16"></i>';
		$num-=16;
	}
	while($num>=4){
		$level.='<i class="QQlevel i_4"></i>';
		$num-=4;
	}
	while($num>=1){
		$level.='<i class="QQlevel i_1"></i>';
		$num-=1;
	}
	return $level.$nggl;
}
//时间格式获取
function get_time($ptime){
    //$ptime = strtotime($time);
    $etime = time() - $ptime;
    if ($etime < 1) {
        return '刚刚';
    }
    $interval = array(
        12 * 30 * 24 * 60 * 60 => '年前',
        30 * 24 * 60 * 60 => '个月前',
        //7 * 24 * 60 * 60 => '周前',
        24 * 60 * 60 => '天前',
        60 * 60 => '小时前',
        60 => '分钟前',
        1 => '秒前',
    );
    foreach ($interval as $secs => $str){
        $d = $etime / $secs;
        if ($d >= 1) {
            $r = round($d);
            return $r . $str;
        }
    };
}
//单位转换
function number_k($s){
	if($s > 9999){
		return number_format($s/10000,1).'万';
	}elseif($s > 999){
		return number_format($s/1000,1).'k';
	}else{
		return $s;
	}
}
//统计文章总数
function count_log_all(){
	$db = Database::getInstance();
	$data = $db->once_fetch_array("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blog WHERE type = 'blog'");
	return $data['total'];
}

//统计评论总数
function count_com_all(){
	$db = Database::getInstance();
	$data = $db->once_fetch_array("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "comment");
	return $data['total'];
}

//统计微语总数
function count_tw_all(){
	$db = Database::getInstance();
	$data = $db->once_fetch_array("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "twitter");
	return $data['total'];
}
//获取blog表的一条内容,$content填写字段名
function blog_content($gid,$content){
    $db = Database::getInstance();
    $sql = 'SELECT ' . $content . ' FROM ' . DB_PREFIX . "blog WHERE gid='".$gid."'";
	$row = $db->once_fetch_array($sql);
    return $row[$content];
}
//获取图片
function img_url($thumburl,$gid,$content,$open=false){
	if($thumburl){
		return $thumburl;
	}elseif(img_zw($content)){
		return img_zw($content);
	}elseif(img_fj($gid) && $open){
		return img_fj($gid);
	}else{
		return TEMPLATE_URL.'images/post.jpg';
	}
}
//全局匹配正文中的图片并存入imgsrc中
function img_zw($content){
	preg_match_all("|<img[^>]+src=\"([^>\"]+)\"?[^>]*>|is", $content, $img);
	$imgsrc = !empty($img[1]) ? $img[1][0] : '';
	if($imgsrc):
		return $imgsrc;
	endif;
}
//Custom: 获取附件第一张图片
function img_fj($logid){
	$db = Database::getInstance();
	$sql = "SELECT * FROM ".DB_PREFIX."attachment WHERE blogid=".$logid." AND (`filepath` LIKE '%jpg' OR `filepath` LIKE '%gif' OR `filepath` LIKE '%png') ORDER BY `aid` ASC LIMIT 0,1";
	$imgs = $db->query($sql);
	$img_path = "";
	while($row = $db->fetch_array($imgs)){
		$img_path .= BLOG_URL.substr($row['filepath'],3,strlen($row['filepath']));
	}
	return $img_path;
}

//更新like数量
function update_like($logid){
	$logid = intval($_POST['id']);
	$DB = Database::getInstance();
	$DB->query("UPDATE " . DB_PREFIX . "blog SET nlike=nlike+1 WHERE gid=$logid");
	setcookie('dotLike_'. $logid, 'true', time() + 99999999);
}
function _like() {
	//文章点赞
	if( @$_POST['action'] == 'nlike' && isset($_POST['id'])){
		$id = intval($_POST['id']);
		header("Access-Control-Allow-Origin: *");
		update_like($id);
		echo get_like($id);
		die;
	}
	//主页   De you like me?
	if( @$_POST['action'] == 'ajax_mlike_add' ){
		header("Access-Control-Allow-Origin: *");
		global $CACHE;
		if(!isset($_COOKIE['dotLike_mlike'])){
			//$CACHE->updateCache('options');
			$mlike = Option::get('mlike');
			Option::updateOption('mlike', $mlike + 1);
			setcookie('dotLike_mlike', 'true', time() + 99999999);
			$CACHE->updateCache('options');
			//echo Option::get('mlike');
			$json = array('success' => 1,'like' => Option::get('mlike')); 
			echo json_encode($json);
			die;
		}else{
			//setcookie ("dotLike_mlike", "", time() - 99999999);
			//echo 'false';
			$json = array('success' => 0,'like' => Option::get('mlike')); 
			echo json_encode($json);
			die;
		}
	}
}
_like();
//获取like数量
function get_like($id){
	$DB = Database::getInstance();//兼容PHP7的数据库连接方式
	$sql = "SELECT nlike FROM " . DB_PREFIX . "blog WHERE gid=$id";
	$row = $DB->once_fetch_array($sql);
	return $row['nlike'];
}
//抓取远程连接内容
function myCurl($url, $ip = '114.114.114.114'){
    $ch = curl_init();     // Curl 初始化  
    $timeout = 30;     // 超时时间：30s  
    $ua='Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36';    // 伪造抓取 UA  
    curl_setopt($ch, CURLOPT_URL, $url);              // 设置 Curl 目标  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);      // Curl 请求有返回的值  
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);     // 设置抓取超时时间  
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);        // 跟踪重定向  
    curl_setopt($ch, CURLOPT_ENCODING, "");    // 设置编码  
    curl_setopt($ch, CURLOPT_REFERER, $url);   // 伪造来源网址  
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:'.$ip, 'CLIENT-IP:'.$ip));  //伪造IP  
    curl_setopt($ch, CURLOPT_USERAGENT, $ua);   // 伪造ua   
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip'); // 取消gzip压缩  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts  
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  
    $content = curl_exec($ch);   
    curl_close($ch);    // 结束 Curl  
    return $content;    // 函数返回内容  
}
//海报图生成
function createSharePng($gData,$fileName){
	//创建画布
	$width = 600;
	$height = 800;
	$im = imagecreatetruecolor($width, $height);

	//填充画布背景色
	$color = imagecolorallocate($im, 255, 255, 255);
	imagefill($im, 0, 0, $color);

	//字体文件
	$font_file_1 = EMLOG_ROOT."/content/templates/NULL/assets/hanyixizhongyuan.ttf";
	$font_file_2 = EMLOG_ROOT."/content/templates/NULL/assets/Montserrat-Regular.ttf";
	//设定字体的颜色

	$color_0 = ImageColorAllocate ($im, 0, 0, 0);
	$color_255 = ImageColorAllocate ($im, 255, 255, 255);
	$color_153 = imagecolorallocate($im, 153, 153, 153);
	$color_200 = imagecolorallocate($im, 200, 200, 200);
	
	//图片
	$source_path = myCurl($gData["pic"]);
	$head_img = imagecreatefromstring($source_path);
	$target_height = 480;
	$target_width = 600;
	$source_info = getimagesizefromstring($source_path);
	$source_width = $source_info[0];
	$source_height = $source_info[1];
	$source_ratio = $source_height / $source_width;
	$target_ratio = $target_height / $target_width;
	// 源图过高
	if ($source_ratio > $target_ratio){
		$cropped_width = $source_width;
		$cropped_height = $source_width * $target_ratio;
		$source_x = 0;
		$source_y = ($source_height - $cropped_height) / 2;
	}
	// 源图过宽
	elseif ($source_ratio < $target_ratio)
	{
		$cropped_width = $source_height / $target_ratio;
		$cropped_height = $source_height;
		$source_x = ($source_width - $cropped_width) / 2;
		$source_y = 0;
	}// 源图适中
	else{
		$cropped_width = $source_width;
		$cropped_height = $source_height;
		$source_x = 0;
		$source_y = 0;
	}
	// 裁剪
	$cropped_image = imagecreatetruecolor($cropped_width, $cropped_height);
	imagecopy($cropped_image, $head_img, 0, 0, $source_x, $source_y, $cropped_width, $cropped_height);
	imagecopyresampled($im, $cropped_image, 0, 0, 0, 0, $target_width, $target_height, $cropped_width, $cropped_height);
	imagedestroy($cropped_image);
	
	//时间
	$day = date('d', $gData["date"]);
	$day_width = imagettfbbox(70, 0, $font_file_2, $day);
	$day_width = abs($day_width[2] - $day_width[0]);
	$year = date('Y/m', $gData["date"]);
	$year_width = imagettfbbox(22, 0, $font_file_2, $year);
	$year_width = abs($year_width[2] - $year_width[0]);
	$day_left = ($year_width - $day_width) / 2;
	//  13
	imagettftext($im, 70, 0, 50 + $day_left, 390, $color_255, $font_file_2, $day);
	//————————
	imageline($im, 50, 405, 50 + $year_width, 405, $color_255);
	imageline($im, 50, 406, 50 + $year_width, 406, $color_255);
	//  2019/02
	imagettftext($im, 22, 0, 50, 440, $color_255, $font_file_2, $year);

	//标题
	$str = $gData['title'];
	if(mb_strlen($str)>16){
		$str = mb_substr($str,0,15,"UTF8")."...";
	}
	imagettftext($im, 24,0, 40, 550, $color_0 ,$font_file_1, $str);

	//
	imagettftext($im, 15,0, 40, 610, $color_153 ,$font_file_1, $gData["author"]);

	//-------------------------------------
	$style = array($color_200,$color_200,$color_200,$color_200,$color_200,$color_200,$color_255,$color_255,$color_255,$color_255,$color_255,$color_255);
	imagesetstyle($im, $style);
	imageline($im, 0, 650, 600, 650, IMG_COLOR_STYLED);

	//
	imagettftext($im, 18,0, 40, 715, $color_0 ,$font_file_1, $gData["blogname"]);
	imagettftext($im, 14,0, 40, 755, $color_153 ,$font_file_1, $gData["bloginfo"]);

	//二维码
	$qrcode_str = file_get_contents($gData["code"]);
	$qrcode_size = getimagesizefromstring($qrcode_str);
	$qrcode_img = imagecreatefromstring($qrcode_str);
	imagecopyresized($im, $qrcode_img, 460, 670, 0, 0, 110, 110, $qrcode_size[0], $qrcode_size[1]);
	//ob_clean();
	//输出图片
	imagepng ($im,$fileName);

	//释放空间
	imagedestroy($im);
	imagedestroy($head_img);
	//return true;
}
//poster
function poster_share($id, $url, $pic, $title, $date, $blogname, $bloginfo){
	//图片上传目录
	$upload_dir = 'content/uploadfile/null_file/poster/';
	//图片名称
    $filename = 'Poster-' . $id . '.png';
	//图片地址
    $file = EMLOG_ROOT . '/' . $upload_dir . $filename;
	//返回浏览连接
	$src = BLOG_URL . $upload_dir . $filename;
	//判断图片是否存在或已超过15天，创建缓存文件
	if( !is_file($file) || ( time() - filemtime($file) ) > 1296000 ){
		global $CACHE;
		$log_cache_sort = $CACHE->readCache('logsort'); 
		$sort = $log_cache_sort[$id]['name'] ? $log_cache_sort[$id]['name'] : '文章';
		global $T_data;
		$author = '作者：'.$T_data['username'].' 发布在「'.$sort.'」';
		//二维码
		$code = TEMPLATE_URL . 'share/qrcode.php?data=' . $url;
		//数据
		$gData = [
			'pic' => $pic,
			'code' => $code,
			'title' => $title,
			'date' => $date,
			'author' => $author,
			'blogname' => $blogname,
			'bloginfo' => $bloginfo
		];
		createSharePng($gData,$file);
	}
	return $src;
}
//blog-tool:获取头像
function getAvatar($email){
	$qq = str_replace("@qq.com","",$email);
	if(empty($email)){
		return TEMPLATE_URL.'images/avatar.jpg';
	}elseif( strpos($email,'@qq.com') && is_numeric($qq) && strlen($qq) > 5 && strlen($qq) < 11 ){
		return 'https://q.qlogo.cn/g?b=qq&s=100&nk=' . $qq;
	}else{
		$hash = md5(strtolower($email));
		$avatar = 'https://secure.gravatar.com/avatar/' . $hash . '?s=100';
		//$headers = @get_headers($avatar.'&d=404');
        //if (!preg_match("|200|", $headers[0])) $avatar = TEMPLATE_URL.'images/avatar.jpg';
		return $avatar;
	}
}
//comment：输出评论人等级
function echo_levels($comment_author_email){
	$DB = Database::getInstance();
	global $CACHE;
	global $T_data;
	$user_cache = $CACHE->readCache('user'); 
	$adminEmail = '"'.$user_cache[1]['mail'].'"';
	/*
	if($comment_author_email==$adminEmail){
		//echo '<em class="icon svip Q-vip"></em>';
	}
	*/
	$sql = "SELECT count(*) as author_count,mail FROM ".DB_PREFIX."comment WHERE mail != '' and mail = $comment_author_email and hide ='n'";
	$row = $DB->once_fetch_array($sql);
	$author_count = $row['author_count'];
	if($T_data['touxianleixing'] == "wb"){
		if($author_count>=0 && $author_count<3)   // && $comment_author_email!=$adminEmail
			echo '';
		else if($author_count>=3 && $author_count<10)
			echo '<em class="W_icon icon_1"></em>';
		else if($author_count>=10 && $author_count<20)
			echo '<em class="W_icon icon_2"></em>';
		else if($author_count>=20 && $author_count<30)
			echo '<em class="W_icon icon_3"></em>';
		else if($author_count>=30 &&$author_count<40)
			echo '<em class="W_icon icon_4"></em>';
		else if($author_count>=40 && $author_coun<50)
			echo '<em class="W_icon icon_5"></em>';
		else if($author_count>=50 && $author_coun<60)
			echo '<em class="W_icon icon_6"></em>';
	}elseif($T_data['touxianleixing'] == "Lv"){
		if($author_count>=0 && $author_count<3)
			echo '<em class="icon lv0 Q-LV0"></em>';
		else if($author_count>=3 && $author_count<10)
			echo '<em class="icon lv1 Q-LV1"></em>';
		else if($author_count>=10 && $author_count<20)
			echo '<em class="icon lv2 Q-LV2"></em>';
		else if($author_count>=20 && $author_count<30)
			echo '<em class="icon lv3 Q-LV3"></em>';
		else if($author_count>=30 &&$author_count<40)
			echo '<em class="icon lv4 Q-LV4"></em>';
		else if($author_count>=40 && $author_coun<50)
			echo '<em class="icon lv5 Q-LV5"></em>';
		else if($author_count>=50 && $author_coun<60)
			echo '<em class="icon lv6 Q-LV6"></em>';
	}
}
//是否为管理员，输出大会员
function if_admin($authorEmail, $adminEmail){
	if($authorEmail == $adminEmail){
		echo '<i class="icon Q-big-vip"></i>';
	}
	//主题作者
	elseif($authorEmail == "\"".strip_tags('837233287@qq.com')."\""){
		echo '<i class="icon Q-vip"></i>';
	}
}
//blog-list : 列表卡片 左、右上角 置顶、热门小图标
function top_hot($top,$sortop,$views){
	if($top=='y' || $sortop=='y'){
		echo '<div class="hot-o icon Q-hot-o"></div>';
	}
	if($views>=999){
		echo '<div class="hot hot-o icon Q-hot"></div>';
	}
}
//blog-list : 列表卡片 New、小火 小图标
function new_fire($date,$views){
	if(((date('Ymd',time())-date('Ymd',$date))<=2)){
		echo '<span><i class="icon Q-new" style="color: #f558b7;"></i></span>';
	}
	if($views>=300){
		echo '<span><i class="icon Q-fire" style="color: #f55;"></i></span>';
	}
}

//ajax悬浮文章
function ajax_content($blogname,$bloginfo){
	if ( $_POST['action'] == 'ajax_content_post' && 'POST' == $_SERVER['REQUEST_METHOD'] ){
		$id = intval($_POST['id']);
		global $CACHE;
		$Log_Model = new Log_Model();
		$logData = $Log_Model->getOneLogForHome($id);
        if ($logData === false) {
            show_404_page();
        }
        extract($logData);
		include View::getView('loop/single-ajax');
		die();
	}
}
ajax_content($blogname,$bloginfo);

//微语api 机器人发微语
function twitter_api($T_token){
	if($_GET['action'] == 'twitter' && $_POST){
		$t = isset($_POST['t']) ? addslashes(trim($_POST['t'])) : '';
		$img = '';
		$token = isset($_POST['token']) ? addslashes(trim($_POST['token'])) : '';
		//验证token
		if($token != $T_token){
          	ob_clean();
			echo 'token error';
			die;
		}
		
		//上传目录
		if($_POST['img'] || isset($_FILES['file']) || $_POST['mp3_url'] || isset($_FILES['mp3'])){
			$uppath = 'content/uploadfile/' . gmdate('Ym') . '/';
			$upload_dir = EMLOG_ROOT . '/' . $uppath;
			//创建目录
			if(!is_readable($upload_dir)) mkdir($upload_dir);
		}
		
		$typeArr = array("jpeg","jpg", "png", "gif");
		//网络图片上传
		if($_POST['img'] && (!isset($_FILES['file']) || $_FILES['file']['error'] == 4)){
			//图片文件名
			$type = pathinfo($_POST['img'], PATHINFO_EXTENSION);//获取图片后缀
			$type = in_array($type, $typeArr) ? $type : 'png';
			$filename = 'duola-' . time() . rand(10, 99) . "." . $type;
			//写入图片
			file_put_contents($upload_dir . $filename, myCurl($_POST['img']));
			$img = $uppath . $filename; 
		}
		//直接图片上传
		if(isset($_FILES['file']) && $_FILES['file']['error'] != 4 && !$_POST['img']){
			//图片名称
			$type = substr($_FILES['file']['type'],6);//获取图片后缀
			$type = in_array($type, $typeArr) ? $type : 'png';
			$filename = 'duola-' . time() . rand(10, 99) . "." . $type;//图片名称 
			//上传图片
			move_uploaded_file($_FILES["file"]["tmp_name"],$upload_dir . $filename);
			$img = $uppath . $filename;   
		}
		
		//mp3
		$mp3 = '';
		if($_POST['mp3_url'] && (!isset($_FILES['mp3']) || $_FILES['mp3']['error'] == 4)){
			$mp3_type = pathinfo($_POST['mp3_url'], PATHINFO_EXTENSION);
			$mp3_filename = 'duola_mp3-' . time() . rand(10, 99) . "." . $mp3_type;
			file_put_contents($upload_dir . $mp3_filename, myCurl($_POST['mp3_url']));
			$mp3 = $uppath . $mp3_filename;
		}
		//文件上传
		if(isset($_FILES['mp3']) && $_FILES['mp3']['error'] != 4 && !$_POST['mp3_url']){
			$mp3_type = substr($_FILES['mp3']['type'],6);
			$mp3_filename = 'duola_mp3-' . time() . rand(10, 99) . "." . $mp3_type;
			move_uploaded_file($_FILES["mp3"]["tmp_name"],$upload_dir . $mp3_filename);
			$mp3 = $uppath . $mp3_filename;
		}
		
		//无内容，却带图
		if ($img && !$t) {
			$t = '分享图片';
		}
		//无内容
		if(!$t){
          	ob_clean();
			echo 'msg error';
			die;
		}
		$t = preg_replace("/\[CQ:face,id=182\]/i",'[smilies1]',$t);
		//插入记录
		$Twitter_Model = new Twitter_Model();
		$tdata = array(
				'content' => $Twitter_Model->formatTwitter($t),
				'author' => 1,
				'date' => time(),
				'img' => $img,
				'mp3' => $mp3
		);
		if($Twitter_Model->addTwitter($tdata)){
          	ob_clean();
			echo 'ok,' . BLOG_URL . 't';
			die;
		}else{
          	ob_clean();
			echo 'error';
			die;
		}
	}
}
//获取微语列表
function twitter_list(){
	if($_GET['wxt'] == 'list'){	// && $_SERVER['HTTP_BLOG_URL'] == BLOG_URL
		global $CACHE;
		$user_cache = $CACHE->readCache('user');
		$Twitter_Model = new Twitter_Model();
		$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
		$tws = $Twitter_Model->getTwitters($page);
		$twnum = $Twitter_Model->getTwitterNum();
		$pages = @ceil($twnum / Option::get('index_twnum'));

		foreach($tws as $val){
			$author = $user_cache[$val['author']]['name'];
			$avatar = empty($user_cache[$val['author']]['avatar']) ? 
                TEMPLATE_URL . "images/tx.jpg" : 
                BLOG_URL . $user_cache[$val['author']]['avatar'];
			$img = empty($val['img']) ? "" : BLOG_URL . $val['img'];
			$mp3 = empty($val['mp3']) ? "" : BLOG_URL . $val['mp3'];
			
			$t = preg_replace("#\[smilies(\d+)\]#i",'',$val["t"]);
			$data[] = array(
				'id' => $val["id"],
				't' => $t,
				'date' => $val['date'],
				'author' => $author,
				'avatar' => $avatar,
				'img' => $img,
				'mp3' => $mp3
			);
		}
		$ret = array(
				'twitters' => $data,
				'page' => $page,
				'pages' => $pages
				);
		ob_clean();
		header('Content-Type:application/json; charset=utf-8');
		exit(json_encode($ret));
	}
}
//文章列表
function wechat_getLogs(){
	if($_GET['log'] == 'list'){
		
		global $CACHE;
		$Log_Model = new Log_Model();

        $options_cache = Option::getAll();
        extract($options_cache);

        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
       
        //$pageurl = '';
        $sqlSegment ='ORDER BY top DESC ,date DESC';
        $sta_cache = $CACHE->readCache('sta');
        $lognum = $sta_cache['lognum'];
        //$pageurl .= Url::logPage();
		/*
        if(isset($_GET['random'])){
            $logarr = $Log_Model->getRandLog(1);
            $rnd = $logarr[0]['gid'];
            header("Location:" . Url::log($rnd));
            exit();
        }
		*/
        $total_pages = ceil($lognum / $index_lognum);
        if ($page > $total_pages) {
            $page = $total_pages;
        }
        $logs = $Log_Model->getLogsForHome($sqlSegment, $page, $index_lognum);
        //$page_url = pagination($lognum, $index_lognum, $page, $pageurl);
		
		$user_cache = $CACHE->readCache('user');
		
		if(!empty($logs)){
			foreach($logs as $value){
				
				$author = $user_cache[$value['author']]['name'];
				$avatar = empty($user_cache[$value['author']]['avatar']) ? 
					TEMPLATE_URL . "images/tx.jpg" : 
					BLOG_URL . $user_cache[$value['author']]['avatar'];
					
				$logdes = blog_tool_purecontent($value['content'],70);
				
				$logsData[] = array(
							'logid' => $value['logid'],
							'log_title' => $value['log_title'],
							'date' => get_time($value['date']),
							'content' => $logdes,
							'images' => wechat_pic($value['content'],$value['thumbs']),
							//'thumbs' => $thumbs,
							'views' => number_k($value['views']),
							'comnum' => $value['comnum'],
							'author' => $author,
							'avatar' => $avatar
							);
			}
			ob_clean();
			header('Content-Type:application/json; charset=utf-8');
			$ret = array(
					'lists' => $logsData,
					'page' => $page,
					'pages' => $total_pages
					);
			exit(json_encode($ret));
		}
	}
}
//特色图
function wechat_pic($content,$thumbs){
	$imgsrc = preg_match_all("|<img[^>]+src=\"([^>\"]+)\"?[^>]*>|is", $content, $imgs);
	$imgNum = count($imgs[1]);
	$imgNum_max = $imgNum;

	if($thumbs != ''){
		$images = $thumbs;
	}elseif($imgNum >= 1){
		$images = !empty($imgs[1]) ? $imgs[1][0] : '';
		if(!preg_match('/(http|https):\/\//', $images)){
			$images = BLOG_URL . $images;
		}
		
	}
	return $images;
}
//单篇文章
function wechat_getOneLog(){
	if($_GET['log'] == 'post' && isset($_GET['id'])){
		$id = intval($_GET['id']);
		global $CACHE;
		
		$user_cache = $CACHE->readCache('user');
		$log_cache_sort = $CACHE->readCache('logsort');
		
		$Log_Model = new Log_Model();
		$logData = $Log_Model->getOneLogForHome($id);
        
        extract($logData);
		//作者
		$author = $user_cache[$author]['name'];
		//图片链接
		$log_content = preg_replace('#\<a (.*?)><img (.*?)\><\/a>#i','<img class="wechat_img" $2>',$log_content);
		$log_content = preg_replace('#\[wz gid=(\d+)\]#i','',$log_content);
		//表情
		$log_content = owo($log_content);
		
		$logData = array(
                'log_title' => $log_title,
                //'timestamp' => $timestamp,
                'date' => get_time($date),
                'logid' => $logid,
                'sortid' => $sortid,
				'sortname' => $log_cache_sort[$sortid]['name'],
                'thumbs'=> $thumbs,
                //'copy' => $copy,
                //'copyurl'=> $copyurl,
                //'type' => $type,
                'author' => $author,
                'log_content' => $log_content,
                'views' => $views,
                'comnum' => $comnum,
                //'top' => $top,
                //'sortop' => $sortop,
                //'attnum' => $attnum,
                //'allow_remark' => $allow_remark,
                //'password' => $password,
                //'template' => $template,
                );
		ob_clean();
		header('Content-Type:application/json; charset=utf-8');
		$ret = array(
				'post' => $logData
				);
		exit(json_encode($ret));
	}
}
if($T_data['twitter_token']!='' || $T_data['twitter_token']!=null){
	twitter_api($T_data['twitter_token']);
	twitter_list();
	wechat_getLogs();
	wechat_getOneLog();
}