<?php
/**
 * 安装程序
 * @copyright (c) 爱享库 All Rights Reserved
 */

require_once ('../../../init.php');
error_reporting(0);

define('DEL_INSTALLER', 1);

$setted_inc = file_exists(EMLOG_ROOT."/content/templates/".Option::get('nonce_templet')."/inc/setted.".Option::get('nonce_templet').".inc");
$install_php = EMLOG_ROOT."/content/templates/".Option::get('nonce_templet').'/install.php';
if( $setted_inc || file_exists($install_php.'.bak') || ROLE != ROLE_ADMIN ){
    exit('error!');
}
//**********
$url='https://www.qiuzq.cn/content/plugins/authorization/validate.php?theme='.Option::get('nonce_templet').'&host='.$_SERVER['HTTP_HOST'];
$header[] = "Content-type: text/xml";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 1);
$content = curl_exec($ch);
//$response = curl_getinfo($ch);
if($content!='nice'){
	exit('未授权,'.$content);
}


$DB = Database::getInstance();

date_default_timezone_set('Asia/Shanghai');

header('Content-Type: text/html; charset=UTF-8');

$act = isset($_GET['action'])? $_GET['action'] : '';

if (PHP_VERSION < '5.0'){
    emMsg('您的php版本过低，请选用支持PHP5的环境配置。');
}
$template_name = Option::get('nonce_templet');

function EmThemanet_MSG($msg, $url = 'javascript:history.back(-1);', $isAutoGo = false) {
	if ($msg == '404') {
		header("HTTP/1.1 404 Not Found");
		$msg = '抱歉，你所请求的页面不存在！';
	}
	echo "
<!doctype html>
<html lang=\"en\">
<head>
<meta charset=\"UTF-8\">
<title>Emlog ".ucfirst($template_name)."</title>
<style>
body{color: #999;font-family: 'Microsoft Yahei';text-align: center;margin: 0;padding: 0;}
h1{font-size: 60px;margin: 0 0 20px;padding: 10px 0;background-color: #19B5FE;color: #fff;font-weight: normal;padding-top: 200px;}
h3{font-size: 25px;margin: 0 0 40px;font-weight: normal;}
a{font-size: 14px;color: #fff;background-color: #00AAEF;display: inline-block;padding: 10px 20px;border-radius: 2px;text-decoration: none;}
a:hover{color: #fff;background-color: #049FDE;}
@media (max-width: 600px) {
	h1{padding-top: 100px;font-size: 120px;}
}
</style>
</head>
<body>
<h1>恭喜，配置成功！</h1>
<h3>{$msg}</h3>
<a href=\"".BLOG_URL."\">返回首页</a>
<a href=\"".BLOG_URL."admin\">进入后台</a>";
	exit;
}
if(!$act && ROLE == ROLE_ADMIN){
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Emlog <?php echo ucfirst($template_name); ?></title>
<style>
body{color: #999;font-family: 'Microsoft Yahei';text-align: center;margin: 0;padding: 0;}
h1{font-size: 140px;margin: 0 0 20px;padding: 10px 0;background-color: #19B5FE;color: #fff;font-weight: normal;padding-top: 200px;}
h3{font-size: 25px;margin: 0 0 40px;font-weight: normal;}
.btn{display:inline-block;padding:6px 15px;margin-bottom:0;font-size:14px;font-weight:normal;line-height:1.42857143;text-align:center;white-space:nowrap;vertical-align:middle;-ms-touch-action:manipulation;touch-action:manipulation;cursor:pointer;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none;background-image:none;border:1px solid transparent;border-radius:2px;}
.btn:focus,.btn:active:focus,.btn.active:focus,.btn.focus,.btn:active.focus,.btn.active.focus{outline:thin dotted;outline:5px auto -webkit-focus-ring-color;outline-offset:-2px;}
.btn:hover,.btn:focus,.btn.focus{color:#333;text-decoration:none;}
.btn:active,.btn.active{background-image:none;outline:0;-webkit-box-shadow:inset 0 3px 5px rgba(0,0,0,.125);box-shadow:inset 0 3px 5px rgba(0,0,0,.125);}
.btn.disabled,.btn[disabled],fieldset[disabled] .btn{cursor:not-allowed;filter:alpha(opacity=65);-webkit-box-shadow:none;box-shadow:none;opacity:.65;}
a.btn.disabled,fieldset[disabled] a.btn{pointer-events:none;}
.btn-primary{color:#fff;background-color:#00AAEE;border-color:#00AAEE;}
.btn-primary:hover,.btn-primary:focus,.btn-primary:active{color:#fff;background-color:#00A1EC;border-color:#00A1EC;}
@media (max-width: 600px) {
	h1{padding-top: 100px;font-size: 120px;}
}
</style>
</head>
<body>
<form name="form1" method="post" action="install.php?action=install">
	<h1><?php echo ucfirst($template_name); ?></h1>
	<h3>检测到您第一次使用<?php echo ucfirst($template_name); ?>主题，请先点击开始配置按钮，配置相关内容！</h3>
	<input type="submit" class="btn btn-primary" value="开始配置">
</form>
</body>
</html>
<?php
}
if(($act == 'install' || $act == 'reinstall') && ROLE == ROLE_ADMIN){
    if( !file_exists(EMLOG_ROOT."/content/templates/{$template_name}/inc/setted.{$template_name}.inc")){
        //$user_cache = $CACHE->readCache('user');
        $incFile = EMLOG_ROOT."/content/templates/{$template_name}/inc/setted.{$template_name}.inc";
        $myfile = fopen($incFile, "w") or die("Unable to open file!");
        fwrite($myfile,$_SERVER['HTTP_HOST']); 
        fclose($myfile);
    }
	//创建缓存目录
	if(!is_readable(EMLOG_ROOT.'/content/uploadfile/null_file/')) mkdir(EMLOG_ROOT.'/content/uploadfile/null_file/');
	//海报图目录
	if(!is_readable(EMLOG_ROOT.'/content/uploadfile/null_file/poster/')) mkdir(EMLOG_ROOT.'/content/uploadfile/null_file/poster/');
	//json
	if(!is_readable(EMLOG_ROOT.'/content/uploadfile/null_file/json/')) mkdir(EMLOG_ROOT.'/content/uploadfile/null_file/json/');
	//头像缓存
	if(!is_readable(EMLOG_ROOT.'/content/uploadfile/null_file/avatar/')) mkdir(EMLOG_ROOT.'/content/uploadfile/null_file/avatar/');
	//nlike
	if($DB->num_rows($DB->query("show columns from ".DB_PREFIX."blog like 'nlike'")) == 0){
		$sql = "ALTER TABLE ".DB_PREFIX."blog ADD nlike int unsigned NOT NULL DEFAULT '0'";
		$DB->query($sql);
	}
	//***
	global $CACHE;
	$CACHE->updateCache('options');
	//mlike
	$mlike = Option::get('mlike');
	if(is_null($mlike)){
		$mlike = 0;
		$DB->query("INSERT INTO ".DB_PREFIX."options(option_name, option_value) VALUES('mlike', '$mlike')");
		$CACHE->updateCache('options');
	}
	//tpl_sidenum
	$tpl_sidenum = Option::get('tpl_sidenum');
	if($tpl_sidenum < 2){
		Option::updateOption('tpl_sidenum', 2);
		$CACHE->updateCache('options');
	}
	//page
	function insert_page($title,$content,$alias,$allow_remark,$template){
		$DB = Database::getInstance();
		$is_sql = $DB->query("SELECT 1 FROM ".DB_PREFIX."blog WHERE template='".$template."'");
		if (!$DB->num_rows($is_sql) && !empty($template)) {
			$DB->query("INSERT INTO ".DB_PREFIX."blog(title,date,content,alias,sortid,type,allow_remark,template) VALUES('".$title."','".time()."','".$content."','".$alias."','-1','page','".$allow_remark."','".$template."')");
		}
	}
	insert_page('留言板','<p>这是一个留言板</p>','msg','y','page');
	insert_page('友链','','links','y','page_links');
	insert_page('归档','','archivers','n','page_archivers');
	insert_page('图册','','photo','y','page_photo');
	
    $result .= "您的".ucfirst($template_name)."主题相关配置已配置完毕，现在可以开始您的创作了，就这么简单!";
    /*if (DEL_INSTALLER === 1 && !@unlink('./install.php') || DEL_INSTALLER === 0) {
        $result .= '<p style="color:red;margin:10px 20px;">警告：请手动删除主题目录下安装文件：install.php</p> ';
    }
	*/
    EmThemanet_MSG($result, 'none');
}
?>