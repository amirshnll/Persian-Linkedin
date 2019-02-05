<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>لینکدین فارسی - <?php echo $person['firstname'] . ' ' . $person['lastname']; ?></title>
	<link rel="stylesheet" type="text/css" href="{base}assets/layout/layout.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/bootstrap/css/bootstrap-grid.min.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/fontawesome/css/all.min.css">
	<link rel="shortcut icon" href="{base}assets/images/favicon.png"/>
	<script type="text/javascript">
		function copyclipboard() {
			navigator.clipboard.writeText("<?php echo $base . 'user/' . $user_key; ?>");
			alert('متن کپی شد.');
		}
	</script>
</head>
<body style="background: #f7f8fa !important;">
	<div class="profile-topline">
		<div class="container">
			<div class="row">
				<div class="col-md-12 right-to-left">
					<?php if($is_login==false) { ?>
					<a href="{base}register" title="ساخت رزومه رایگان" class="text-light">
						<p class="d-inline"><sapn class="fas fa-1x fa-user-plus"></sapn>&nbsp;<span>شما هم می توانید یک رزومه ی حرفه ای داشته باشید!</span></p>
					</a>
				<?php } else { ?>
					<a href="{base}panel" title="پنل کاربری" class="text-light">
						<p class="d-inline"><span class="fas fa-1x fa-solar-panel"></span>&nbsp;<span>پنل کاربری</span></p>
					</a>
					&nbsp;&nbsp;&nbsp;
					<a href="{profile_open_key}" title="صفحه ی من" class="text-light">
						<p class="d-inline"><span class="fas fa-1x fa-scroll"></span>&nbsp;<span>صفحه ی من</span></p>
					</a>
					&nbsp;&nbsp;&nbsp;
					<a href="{base}panel/out" title="خروج از سیستم" class="text-light">
						<p class="d-inline"><span class="fas fa-1x fa-power-off"></span>&nbsp;<span>خروج از سیستم</span></p>
					</a>
				<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<div class="container">
		<header>
			<div class="profile-header">
				<div class="row right-to-left">
					<div class="col-md-8 float-left text-right">
						<h1 class="display-4 font-weight-bold"><?php echo $person['firstname'] . ' ' . $person['lastname']; ?></h1>
						<div class="social-profile-contact">
							<?php if($user_private_contact != "true") { ?>
								<p><span class="fas fa-1x fa-envelope"></span>&nbsp;<span><a class="text-dark" href="mailto:{email}" title="ارتباط با ایمیل">{email}</a></span></p>
								<p><span class="fas fa-1x fa-map-marker-alt"></span>&nbsp;<span><?php echo 'موقعیت : ' . $person['country_id'] . ' | کد پستی :' . $person['zip_code']; ?></span></p>
								<p><span class="fab fa-1x fa-linkedin"></span>&nbsp;<span><a class="text-dark" href="{linkedin}" title="ارتباط با ایمیل">{linkedin}</a></span></p>
								<p><span class="fab fa-1x fa-twitter"></span>&nbsp;<span><a class="text-dark" href="{twitter}" title="ارتباط با ایمیل">{twitter}</a></span></p>
								<p><span class="fab fa-1x fa-telegram"></span>&nbsp;<span><a class="text-dark" href="{telegram}" title="ارتباط با ایمیل">{telegram}</a></span></p>
								<p><span class="fab fa-1x fa-skype"></span>&nbsp;<span><a class="text-dark" href="{skype}" title="ارتباط با ایمیل">{skype}</a></span></p>
							<?php } ?>
						</div>
					</div>
					<div class="col-md-4 float-right text-center">
						<div class="profile-avatar">
							<img src="{base}upload/avatar/{avatar}" width="150" height="150" title="<?php echo $person['firstname'] . ' ' . $person['lastname']; ?>" alt="<?php echo $person['firstname'] . ' ' . $person['lastname']; ?>" />
						</div>
						<div class="profile-button-handler">
							<button class="btn btn-primary" onclick="copyclipboard()"><span class="fas fa-1x fa-copy"></span>&nbsp;<span>کپی کردن آدرس پروفایل</span></button>
							<button class="btn btn-primary" onclick="window.print()"><span class="fas fa-1x fa-print"></span>&nbsp;<span>پرینت رزومه</span></button>
							<?php if($is_my_page==false && $is_login==true) { ?>
								<br /><br />
								<!-- Connect Button -->
								<?php if($is_friend==false) { ?>
									<?php if($is_respond==true && $is_requester==true) { ?>
										<a href="{base}action/delete_request/{user_key}" title="حذف درخواست" class="btn btn-danger text-light">حذف درخواست</a>
									<?php } elseif($is_respond==true && $is_requester==false) { ?>
										<a href="{base}action/confirm_connect/{user_key}" title="تایید درخواست" class="btn btn-success text-light">تایید درخواست</a>
										<a href="{base}action/unconfirm_connect/{user_key}" title="رد درخواست" class="btn btn-danger text-light">رد درخواست</a>
										<br /><br />
									<?php } else { ?>
										<a href="{base}action/add_connect/{user_key}" title="درخواست ارتباط" class="btn btn-success text-light">درخواست ارتباط</a>
									<?php } ?>
								<?php } else { ?>
									<a href="{base}action/delete_connect/{user_key}" title="حذف ارتباط" class="btn btn-danger text-light">حذف ارتباط</a>
								<?php } ?>
								<!-- Report Button -->
								<a href="{base}action/report/{user_key}" title="گزارش تخلف" class="btn btn-danger text-light">گزارش تخلف</a>
								<!-- Block Button -->
								<?php if($is_block==false) { ?>
									<a href="{base}action/block/{user_key}" title="بلاک کردن" class="btn btn-danger text-light">بلاک کردن</a>
								<?php } else { ?>
									<a href="{base}action/unblock/{user_key}" title="آنبلاک کردن" class="btn btn-danger text-light">آنبلاک کردن</a>
								<?php } ?>
								<?php if($is_friend==true) { ?>
								<br /><br />
								<a href="{base}panel/message/{user_key}" title="ارسال پیام" class="btn btn-success text-light">ارسال پیام</a>
								<?php } ?>
							<?php } ?>

							<?php if(!empty($profile_success)) { ?>
								<br /><br /><div class="alert alert-success right-to-left text-right">{profile_success}</div>
							<?php } ?>
							<?php if(!empty($profile_error)) { ?>
								<br /><br /><div class="alert alert-danger right-to-left text-right">{profile_error}</div>
							<?php } ?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</header>

		<section>
			<div class="container profile-section right-to-left">
				<div class="row">
					<div class="col-md-12 text-right">
						<h3 class="font-weight-bold"><span class="fas fa-1x fa-id-card"></span>&nbsp;<span>بیوگرافی : </span></h3>
						<p class="text-justify"><?php echo $person['biography']; ?></p>
					</div>
				</div>
			</div>
		</section>

		<section>
			<div class="container profile-section right-to-left">
				<div class="row">
					<div class="col-md-12 text-right">
						<h3 class="font-weight-bold"><span class="fas fa-1x fa-toolbox"></span>&nbsp;<span>موقعیت های شغلی :</span></h3>
						<ul class="user-profile-items big-line-height">
							<?php 
								if($experience !== false)
								{
									foreach ($experience as $me) {
										if(!empty($me['start_date']))
											$me['start_date'] = " | شروع : " . $me['start_date'];
										if(!empty($me['end_date']))
											$me['end_date'] = " | پایان : " . $me['end_date'];
										echo "<li>" . $me['title'] . " <br />&nbsp;&nbsp;&nbsp;" . $me['content']. $me['start_date'] . $me['end_date'] . "</li>";
									}
								}
								else
								{
									echo "<li>آیتمی موجود نمی باشد.</li>";
								}
							?>
						</ul>
					</div>
				</div>
			</div>
		</section>

		<section>
			<div class="container profile-section right-to-left">
				<div class="row">
					<div class="col-md-12 text-right">
						<h3 class="font-weight-bold"><span class="fas fa-1x fa-university"></span>&nbsp;<span>مدارک تحصیلی :</span></h3>
						<ul class="user-profile-items big-line-height">
							<?php 
								if($education !== false)
								{
									foreach ($education as $ed) {
										if(!empty($ed['start_date']))
											$ed['start_date'] = " | شروع : " . $ed['start_date'];
										if(!empty($ed['end_date']))
											$ed['end_date'] = " | پایان : " . $ed['end_date'];
										echo "<li>" . $ed['title'] . " <br />&nbsp;&nbsp;&nbsp;" . $ed['content'] . $ed['start_date'] . $ed['end_date'] . "</li>";
									}
								}
								else
								{
									echo "<li>آیتمی موجود نمی باشد.</li>";
								}
							?>
						</ul>
					</div>
				</div>
			</div>
		</section>

		<section>
			<div class="container profile-section right-to-left">
				<div class="row">
					<div class="col-md-12 text-right">
						<h3 class="font-weight-bold"><span class="fas fa-1x fa-screwdriver"></span>&nbsp;<span>مهارت ها : </span></h3>
						<ul class="user-profile-items big-line-height">
							<?php 
								if($skills !== false)
								{
									foreach ($skills as $sk) {
										if(!empty($sk['start_date']))
											$sk['start_date'] = " | شروع : " . $sk['start_date'];
										if(!empty($sk['end_date']))
											$sk['end_date'] = " | پایان : " . $sk['end_date'];
										echo "<li>" . $sk['title'] . " <br />&nbsp;&nbsp;&nbsp;" . $sk['content'] . $sk['start_date'] . $sk['end_date'] . "</li>";
									}
								}
								else
								{
									echo "<li>آیتمی موجود نمی باشد.</li>";
								}
							?>
						</ul>
					</div>
				</div>
			</div>
		</section>

		<section>
			<div class="container profile-section right-to-left">
				<div class="row">
					<div class="col-md-12 text-right">
						<h3 class="font-weight-bold"><span class="fas fa-1x fa-project-diagram"></span>&nbsp;<span>پروژه ها : </span></h3>
						<ul class="user-profile-items big-line-height">
							<?php
								if($project !== false)
								{
									foreach ($project as $pe) {
										if(!empty($pe['start_date']))
											$pe['start_date'] = " | شروع : " . $pe['start_date'];
										if(!empty($pe['end_date']))
											$pe['end_date'] = " | پایان : " . $pe['end_date'];
										echo "<li>" . $pe['title'] . " <br />&nbsp;&nbsp;&nbsp;" . $pe['content'] . $pe['start_date'] . $pe['end_date'] . "</li>";
									}
								}
								else
								{
									echo "<li>آیتمی موجود نمی باشد.</li>";
								}
							?>
						</ul>
					</div>
				</div>
			</div>
		</section>

	</div>

	<div class="clearfix"></div>
	<footer>
		<div class="copyright-profile">
			<div class="container">
				<div class="row">
					<div class="col-md-12 right-to-left text-right text-light">
						<p>&copy; <?php echo date('Y') . " - " . (date('Y') + 1) . " : [ "; ?> تمام حقوق و مسئولیت های این صفحه مربوط به صاحب صفحه می باشد. ]</p>
					</div>
				</div>
			</div>
		</div>
	</footer>

	<script href="{base}assets/library/jquery/jquery-3.3.1.min.js"></script>
	<script href="{base}assets/library/bootstrap/js/bootstrap.min.js"></script>
	<script href="{base}assets/library/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
