<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$ci =&get_instance();
function word_limiter($str, $limit = 100, $end_char = '&#8230;')
{
	if (trim($str) === '')
	{
		return $str;
	}

	preg_match('/^\s*+(?:\S++\s*+){1,'.(int) $limit.'}/', $str, $matches);

	if (strlen($str) === strlen($matches[0]))
	{
		$end_char = '';
	}
	return rtrim($matches[0]).$end_char;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>لینکدین فارسی - پنل کاربری</title>
	<link rel="stylesheet" type="text/css" href="{base}assets/layout/layout.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/bootstrap/css/bootstrap-grid.min.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/fontawesome/css/all.min.css">
	<link rel="shortcut icon" href="{base}assets/images/favicon.png"/>
	<script href="{base}assets/library/jquery/jquery-3.3.1.min.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script type="text/javascript">
		function readmore(id) {
			var fulltextid = "fulltext_" + id;
			var expecttextid = "content_" + id;
			var readmore_button = "readmore_button_" + id;
			var clearfix_readmore_button = "clearfix_readmore_button_" + id;
			var text = document.getElementById(fulltextid).innerHTML;
			var readmore_button = document.getElementById(readmore_button).style.display = "none";
			var readmore_button = document.getElementById(clearfix_readmore_button).style.display = "none";
			document.getElementById(expecttextid).innerHTML = text;
		}
	</script>
	<script>
		$(document).ready(function(){
			$.ajaxSetup({cache:false});
		        $(".refresh_key").click(function(){
		            var refresh_key = $(this).attr("href");
		            $("#refresh_key").html("لطفا منتظر بمانید...");
		            $("#body").load(refresh_key);
		        return false;
		        });
		});
	</script>
</head>
<body class="user-panel" id="body">

	<header>
		<div class="header">
			<div class="container">
				<div class="row right-to-left text-right">
					<div class="col-md-3">
						<a href="{base}panel" title="پنل کاربری">
							<div class="logo">
								<span class="fab fa-lg fa-linkedin"></span>
								<h1 class="d-inline text-dark">لینکدین فارسی | پنل کاربری</h1>
							</div>
						</a>
					</div>
					<div class="col-md-6">
						<div class="search">
							{form_search_open}
								{search_input}
							{form_close}
						</div>
					</div>
					<div class="col-md-3 left-to-right">
						<nav class="navbar">
							<ul class="nav">
								<a href="{base}panel/out" title="خروج"><li><span class="fas fa-lg fa-power-off text-danger"></span></li></a>
								<a href="{base}panel/setting" title="تنظیمات"><li><span class="fas fa-lg fa-cog"></span></li></a>
								<a href="{base}panel/profile" title="پروفایل من"><li><span class="fas fa-lg fa-user"></span></li></a>
								<a href="{base}panel/notification" title="اعلانات"><li><span class="fas fa-lg fa-bell"></span></li></a>
								<a href="{base}panel/message" title="پیام ها"><li><span class="fas fa-lg fa-envelope"></span></li></a>
								<a href="{base}panel/profile/connections" title="ارتباطات"><li><span class="fas fa-lg fa-handshake"></span></li></a>
							</ul>
						</nav>
					</div>
				</div>
			</div>
		</div>
	</header>

	<div class="container">
		<section>
			<div class="content">
				<div class="row right-to-left text-right">
					<div class="col-md-3">
						<div class="content-box">
							<h5><span class="fas fa-1x fa-users"></span>&nbsp; <span>شاید این افراد را بشناسید</span></h5>
							<div class="real-content">
								<?php if($user_suggest_3!==false) { ?>
									<?php 
										$suggest_counter = 0;
										$ci->load->model('connections_model');
										$ci->load->model('avatar_model');
										foreach ($user_suggest_3 as $us3){
											if($us3['id'] == $my_user_id)
												continue;
											if(!$ci->connections_model->is_connection($my_user_id, $us3['id']))
											{ 
												$temp_full_name = $ci->person_model->read_user_person($us3['id']);
												$temp_full_name = $temp_full_name['firstname'] . " " . $temp_full_name['lastname'];
												$temp_avatar = $ci->avatar_model->user_current_avatar($us3['id']);
												?>

												<a href="{base}user/<?php echo md5($us3['id']); ?>" title="مشاهده ی پروفایل <?php echo $temp_full_name; ?>" target="_blank">
													<div class="suggest-item">
														<div class="suggest-item-image float-right text-center">
															<img class="img-fluid" src="{base}upload/avatar/<?php echo $temp_avatar; ?>" title="<?php echo $temp_full_name; ?>" src="<?php echo $temp_full_name; ?>" />
														</div>
														<div class="suggest-item-content float-right">
															<p class="text-dark"><?php echo $temp_full_name; ?></p>
														</div>
														<div class="clearfix"></div>
													</div>
												</a>

												<?php $suggest_counter++;
											}
										}

										if($suggest_counter==0)
										{
											echo '<p class="alert alert-dark">در حال حاظر پیشنهادی موجود نیست.</p>';
										}
									?>
								<?php } else { ?>
									<p class="alert alert-dark">پیشنهادی موجود نیست.</p>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="content-box">
							<div class="real-content">
								<div class="write-post-box">
									<div class="write-post-box-title">
										<h6><span class="fas fa-1x fa-pen-nib"></span>&nbsp;<span> نوشته ای را به اشتراک بگذارید. ...</span></h6>
									</div>
									<div class="write-post-box-content">
										{form_newpost_open}
											{write_post_content}
											<p></p>
											<div class="float-left">
												<label for="file-upload" class="custom-file-upload btn btn-primary"> انتخاب فایل</label>
												{post_submit_input}
											</div>
											<div class="clearfix"></div>
											{file_post_content}
										{form_close}
										<?php if(!empty($validation_errors)) { ?>
											<div class="alert alert-danger right-to-left text-right">{validation_errors}</div>
										<?php } ?>
										<?php if(!empty($form_success)) { ?>
											<div class="alert alert-success right-to-left text-right">{form_success}</div>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>

						<?php if(!empty($post_delete)) { ?>
						<div class="alert alert-success">{post_delete}</div>
						<?php } ?>


						<div class="timeline-posts">
							<?php 
							$ci->load->model('connections_model');
							$ci->load->model('avatar_model');
							$ci->load->model('post_view_model');
							$ci->load->model('like_model');
							$ci->load->model('file_model');
							$ci->load->library('jdf');
							if(!is_null($timeline_posts) && $timeline_posts!==false) {
								$post_counter = 0;
								foreach ($timeline_posts as $posts) {
									if(!$ci->connections_model->is_connection($my_user_id, $posts['user_id']) && $my_user_id !== $posts['user_id'])
										continue;
									$post_counter++;
									$temp_full_name = $ci->person_model->read_user_person($posts['user_id']);
									$temp_full_name = $temp_full_name['firstname'] . " " . $temp_full_name['lastname'];
									$temp_avatar = $ci->avatar_model->user_current_avatar($posts['user_id']);
									$lastpostid = md5($posts['id']);
								?>
									<span id="<?php echo md5($posts['id']); ?>"></span>
									<div class="content-box">
										<div class="real-content">
											<div class="timeline-posts-user">
												<div class="timeline-posts-user-avatar float-right text-center">
													<img src="{base}upload/avatar/<?php echo $temp_avatar; ?>" title="<?php echo $temp_full_name; ?>" alt="<?php echo $temp_full_name; ?>" />
												</div>
												<div class="timeline-posts-user-fullname float-right">
													<a class="text-dark" href="{base}user/<?php echo md5($posts['user_id']); ?>" title="مشاهده ی پروفایل <?php echo $temp_full_name; ?>" target="_blank">
														<p><?php echo $temp_full_name; ?></p>
													</a>
												</div>
												<div class="clearfix"></div>
											</div>
											<div class="timeline_posts-content">
												<?php
													if(!is_null($posts['file_id']))
													{
														$temp_file_address = $ci->file_model->find_file($posts['file_id']);
													?>
													<img class="img-fluid timeline_posts-image" src="{base}upload/file/<?php echo $temp_file_address; ?>" title="تصویر نوشته" alt="تصویر نوشته" />
												<?php } 
												$temp_content = word_limiter($posts['content'], 50);
												if($temp_content !== $posts['content']) { echo '<div id="content_' . md5($posts['id']) . '">' . $temp_content . '</div>'; ?>
												<span id="fulltext_<?php echo md5($posts['id']); ?>" class="d-none"><?php echo $posts['content']; ?></span>
												<div class="float-left">
													<button id="readmore_button_<?php echo md5($posts['id']); ?>" onclick="readmore('<?php echo md5($posts['id']); ?>')" class="btn btn-info continue-button">خواندن ادامه</button>
												</div>
												<div id="clearfix_readmore_button_<?php echo md5($posts['id']); ?>" class="clearfix"></div>
												<?php } else { echo '<div id="content_' . md5($posts['id']) . '">' . $posts['content'] . '</div>'; } ?>
											</div>
											<div class="timeline_posts-footer nav">
												<?php
													if($my_user_id!=$posts['user_id'])
														$ci->post_view_model->insert($my_user_id, $posts['id'], time());
													$temp_post_view = $ci->post_view_model->post_view_count($posts['id']);
													$temp_post_like = $ci->like_model->post_like_count($posts['id']);
													$temp_is_like = $ci->like_model->is_like($posts['id'], $my_user_id);
													if($temp_post_view===false)
														$temp_post_view=0;
													if($temp_post_like===false)
														$temp_post_like=0;
												?>
												<ul class="navbar">
													<li class="nav-item text-gray"><span class="fas fa-1x fa-eye"></span>&nbsp;بازدید : <?php echo $temp_post_view; ?></li>
													<li class="nav-item text-gray"><span class="fas fa-1x fa-calendar"></span>&nbsp; آخرین ویرایش : <?php echo $ci->jdf->jdate('j F y', $posts['updated_time']); ?></li>
													<?php if($temp_is_like===false) { ?> 
													<li class="nav-item text-gray"><a rel="user-panel" id="refresh_key" class="text-gray like-anchor refresh_key" href="{base}panel/post/like/<?php echo md5($posts['id']); ?>" title="لایک"><span class="fas fa-1x fa-heart"></span>&nbsp;<?php echo $temp_post_like; ?> لایک</a></li>
													<?php } else { ?>
													<li class="nav-item text-gray"><a rel="user-panel" id="refresh_key" class="like-anchor-active refresh_key" href="{base}panel/post/dislike/<?php echo md5($posts['id']); ?>" title="حذف لایک"><span class="fas fa-1x fa-heart"></span>&nbsp;<?php echo $temp_post_like; ?> لایک</a></li>
													<?php } ?>
													<?php
														if($my_user_id == $posts['user_id']){ ?>
															<li class="nav-item text-primary"><a class="text-primary" href="{base}panel/post/edit/<?php echo md5($posts['id']); ?>" title="ویرایش نوشته"><span class="fas fa-1x fa-pen"></span>&nbsp; ویرایش</a></li>
															<li class="nav-item text-danger"><a rel="user-panel" id="refresh_key" class="refresh_key text-danger" href="{base}panel/post/delete/<?php echo md5($posts['id']); ?>" title="حذف نوشته"><span class="fas fa-1x fa-trash"></span>&nbsp; حذف</a></li>
														<?php }	?>
												</ul>
											</div>
										</div>
									</div>
								<?php }
								if($post_counter==0) { $nopost = true; ?>
								<div class="content-box">
									<div class="real-content">
										<img class="img-fluid" src="{base}assets/images/nopost.png" title="نوشته ای یافت نشد" alt="نوشته ای یافت نشد" />
									</div>
								</div>
							<?php } ?>
							<?php } else { $nopost = true; ?>
								<div class="content-box">
									<div class="real-content">
										<img class="img-fluid" src="{base}assets/images/nopost.png" title="نوشته ای یافت نشد" alt="نوشته ای یافت نشد" />
									</div>
								</div>
							<?php } ?>

							<?php if(!isset($nopost) || $nopost!=true) { ?>
								<div class="loadmore text-center">
									<div class="hr"></div>
									<a rel="user-panel" class="refresh_key" id="refresh_key" href="{base}panel/{timeline_posts_count}#<?php echo $lastpostid; ?>" title="نوشته ی بیشتر"><div class="loadmore_post"><span>+</span></div></a>
								</div>
							<?php } ?>

							<div class="content-box" id="user_suggest_5">
								<h5><span class="fas fa-1x fa-user-plus"></span>&nbsp; <span>ارتباطات خود را افزایش دهید.</span></h5>
								<div class="real-content">
								<?php if($user_suggest_5!==false) { ?>
									<?php 
										$suggest_counter = 0;
										$ci->load->model('connections_model');
										$ci->load->model('avatar_model');

										foreach ($user_suggest_5 as $us5){
											if($us5['id'] == $my_user_id)
												continue;
											if(!$ci->connections_model->is_connection($my_user_id, $us5['id']) && !$ci->connections_model->is_respond_connection($my_user_id, $us5['id']))
											{ 
												$temp_full_name = $ci->person_model->read_user_person($us5['id']);
												$temp_full_name = $temp_full_name['firstname'] . " " . $temp_full_name['lastname'];
												$temp_avatar = $ci->avatar_model->user_current_avatar($us5['id']);
												?>

												<a href="{base}user/<?php echo md5($us5['id']); ?>" title="مشاهده ی پروفایل <?php echo $temp_full_name; ?>" target="_blank">
													<div class="suggest-item">
														<div class="suggest-item-image float-right text-center">
															<img class="img-fluid" src="{base}upload/avatar/<?php echo $temp_avatar; ?>" title="<?php echo $temp_full_name; ?>" src="<?php echo $temp_full_name; ?>" />
														</div>
														<div class="suggest-item-content float-right">
															<p class="text-dark"><?php echo $temp_full_name; ?></p>
														</div>
														<div class="suggest-item-add float-left">
															<a href="{base}action/add_connect/<?php echo md5($us5['id']); ?>" title="درخواست ارتباط" class="btn btn-success text-light">درخواست ارتباط</a>
														</div>
														<div class="clearfix"></div>
													</div>
												</a>

												<?php $suggest_counter++;
											}
										}

										if($suggest_counter==0)
										{
											echo '<p class="alert alert-dark">در حال حاظر پیشنهادی موجود نیست.</p>';
										}
									?>
								<?php } else { ?>
									<p class="alert alert-dark">پیشنهادی موجود نیست.</p>
								<?php } ?>
								<?php if(!empty($profile_success)) { ?>
								<div class="alert alert-success">{profile_success}</div>
								<?php } ?>
								</div>
							</div>
						</div>

					</div>
					<div class="col-md-3">
						<div class="content-box">
							<div class="avatar-timeline text-center">
								<img src="{base}upload/avatar/{user_current_avatar}" title="تصویر کاربری" alt="تصویر کاربری" width="120" height="120" />
							</div>
							<div class="full-name-timeline text-center">
								<h4 class="display-4">{user_full_name}</h4>
								<p id="register_date" class="text-gray">تاریخ عضویت : {register_date}</p>
								<a class="btn btn-warning text-light" href="{profile_open_key}" title="بازکردن صفحه ی من" target="_blank">بازکردن صفحه ی من</a>
							</div>
							<div class="connection-state-timeline text-center">
								<div class="real-content">
									<div class="float-right text-center width-50">
										<p><strong>{user_view_profile}</strong></p>
										<p class="text-gray">بازدیدها</p>
									</div>
									<div class="float-left text-center width-50">
										<p><strong>{user_connection_count}</strong></p>
										<p class="text-gray">ارتباطات</p>
									</div>
									<div class="clearfix"></div>
								</div>
							</div>
							<?php if(!empty($linkedin) && !empty($twitter) && !empty($telegram) && !empty($skype)) { ?>
								<div class="social-link-timeline left-to-right text-left">
									<div class="real-content">
										<?php if(!empty($linkedin)) { ?>
											<a href="{linkedin}" title="لینکدین" target="_blank">
												<p>
													<span class="fab fa-lg fa-linkedin text-gray"></span>
													<span class="text-gray">{linkedin_limit}</span>
												</p>
											</a>
										<?php } ?>
										<?php if(!empty($twitter)) { ?>
										<a href="{twitter}" title="توییتر" target="_blank">
											<p>
												<span class="fab fa-lg fa-twitter text-gray"></span>
												<span class="text-gray">{twitter_limit}</span>
											</p>
										</a>
										<?php } ?>
										<?php if(!empty($telegram)) { ?>
										<a href="{telegram}" title="تلگرام" target="_blank">
											<p>
												<span class="fab fa-lg fa-telegram text-gray"></span>
												<span class="text-gray">{telegram_limit}</span>
											</p>
										</a>
										<?php } ?>
										<?php if(!empty($skype)) { ?>
										<a href="{skype}" title="اسکایپ" target="_blank">
											<p>
												<span class="fab fa-lg fa-skype text-gray"></span>
												<span class="text-gray">{skype_limit}</span>
											</p>
										</a>
										<?php } ?>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</section>

		<footer>
			<div class="footer">
				<div class="row">
					<p>&copy; <?php echo date('Y'); ?> Persian Linkedin. All Right Reserved (<a class="text-dark" href="{base}panel/rules" title="قوانین سایت">Rules</a>).</p>
				</div>
			</div>
		</footer>
	</div>

	<script href="{base}assets/library/bootstrap/js/bootstrap.min.js"></script>
	<script href="{base}assets/library/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
