<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title></title>
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
					<a href="{base}register" title="ساخت رزومه رایگان" class="text-light">
						<p class="d-inline">شما هم می توانید یک رزومه ی حرفه ای داشته باشید!</p>
					</a>
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
							<button class="btn btn-primary" onclick="copyclipboard()">کپی کردن آدرس پروفایل</button>
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
						<h3 class="font-weight-bold">بیوگرافی : </h3>
						<p class="text-justify"><?php echo $person['biography']; ?></p>
					</div>
				</div>
			</div>
		</section>

		<section>
			<div class="container profile-section right-to-left">
				<div class="row">
					<div class="col-md-12 text-right">
						<h3 class="font-weight-bold">موقعیت های شغلی :</h3>
						<ul class="user-profile-items">
							<?php 
								if($experience !== false)
								{
									foreach ($experience as $me) {
										echo "<li>" . $me['title'] . "</li>";
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
						<h3 class="font-weight-bold">مدارک تحصیلی :</h3>
						<ul class="user-profile-items">
							<?php 
								if($education !== false)
								{
									foreach ($education as $ed) {
										echo "<li>" . $ed['title'] . "</li>";
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
						<h3 class="font-weight-bold">مهارت ها : </h3>
						<ul class="user-profile-items">
							<?php 
								if($skills !== false)
								{
									foreach ($skills as $sk) {
										echo "<li>" . $sk['title'] . "</li>";
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
						<h3 class="font-weight-bold">پروژه ها : </h3>
						<ul class="user-profile-items">
							<?php
								if($project !== false)
								{
									foreach ($project as $pe) {
										echo "<li>" . $pe['title'] . "</li>";
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

	<p>&nbsp;</p><p>&nbsp;</p>
	<script href="{base}assets/library/jquery/jquery-3.3.1.min.js"></script>
	<script href="{base}assets/library/bootstrap/js/bootstrap.min.js"></script>
	<script href="{base}assets/library/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
