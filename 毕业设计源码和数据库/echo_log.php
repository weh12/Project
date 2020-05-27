<?php 
/**
 * 阅读文章页面
 */
if(!defined('EMLOG_ROOT')) {exit('error!');} 
?>
	    <section class="style books">
		<div class="bg_xx"><div class="bg"></div></div><!-- 骚气背景 -->
		<header class="entry-header">
					<h1 class="title" itemprop="name"><?php echo $log_title; ?></h1>
					<div class="meta">发布于 <?php echo get_time($date);?> / <?php echo number_k($views); ?> 次围观 / <?php echo $comnum; ?> 条评论 / <?php blog_sort($logid)?> / <?php blog_author($author,1); ?> <?php editflg($logid,$author);?></div>
		</header>
			<div class="single">		
				<?php echo article_index($log_content,$logid);?>
				<?php doAction('log_related', $logData); ?>
				<?php doAction('echo_log', $logData); ?>
				<!--div class="hr-short"></div-->
				<div class="post-tags">
				<?php echo blog_tag($logid); ?>
				</div>
            </div>
			<?php include View::getView('loop/article_block');?>
        </section>

<?php if($allow_remark == 'y'): ?>
        <div class="comment-container">
<?php blog_comments_post($logid,$ckname,$ckmail,$ckurl,$verifyCode,$allow_remark); ?>
<?php blog_comments($comments,$params); ?>
		</div>
<?php endif;?>
<?php
 include View::getView('footer');
?>