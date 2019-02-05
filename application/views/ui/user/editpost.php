<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$ci =&get_instance();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>لینکدین فارسی - ویرایش نوشته</title>
	<link rel="stylesheet" type="text/css" href="{base}assets/layout/layout.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/bootstrap/css/bootstrap-grid.min.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/fontawesome/css/all.min.css">
	<link rel="shortcut icon" href="{base}assets/images/favicon.png"/>
</head>
<body class="user-panel">

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
										<h6><span class="fas fa-1x fa-pen-nib"></span>&nbsp;<span>ویرایش نوشته....</span></h6>
									</div>
									<div class="write-post-box-content">
										<p></p>
										{form_editpost_open}
											<?php
												$ci->load->model('file_model');
												if(!is_null($post['file_id']))
												{
													$temp_file_address = $ci->file_model->find_file($post['file_id']);
												?>
												<img class="img-fluid timeline_posts-image" src="{base}upload/file/<?php echo $temp_file_address; ?>" title="تصویر نوشته" alt="تصویر نوشته" /><p></p>
											<?php } ?>
											{edit_post_content}
											<p></p>
											<div class="float-left">
												<a href="{base}panel#<?php echo md5($post['id']); ?>" title="بازگشت" class="btn btn-danger">بازگشت</a> &nbsp;
												{submit_input}
											</div>
											<div class="clearfix"></div>
										{form_close}
										<p></p>
										<?php if(!empty($validation_errors)) { ?>
											<div class="alert alert-danger right-to-left text-right">{validation_errors}</div>
										<?php } ?>
										<?php if(!empty($form_success)) { ?>
											<div class="alert alert-success right-to-left text-right">{form_success}</div>
										<?php } ?>
										<div class="hr"></div>
										<p><span class="fas fa-1x fa-question-circle"></span>&nbsp;<span>راهنمایی :</span></p>
										<p>در صورت انصراف از تغییر لطفا روی کلید "بازگشت" بزنید.</p>
										<p>لطفا قبل از ثبت تغییرات حتما آنها را با دقت بررسی کنید.</p>
										<p>تصویر نوشته ها قابل تغییر نیستند و در صورت نیاز باید نوشته را حذف کنید.</p>
									</div>
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

	<script href="{base}assets/library/jquery/jquery-3.3.1.min.js"></script>
	<script href="{base}assets/library/bootstrap/js/bootstrap.min.js"></script>
	<script href="{base}assets/library/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
