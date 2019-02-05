<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>لینکدین فارسی - ویرایش اطلاعات شخصی</title>
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
					<div class="col-md-9">
						<div class="content-box">
							<h5>ویرایش اطلاعات شخصی</h5>
							<div class="real-content">
								{form_editperson_open}
									<p><strong>اطلاعات شخصی</strong></p>
									<p>نام : </p>
									<p>{firstname_input}</p>
									<p>نام خانوادگی : </p>
									<p>{lastname_input}</p>
									<p>کشور : </p>
									<p>{dropdown_1}</p>
									<p>کد پستی :</p>
									<p>{zip_code_input}</p>
									<p>تاریخ تولد : </p>
									<p>{birthday_input}</p>
									<p>&nbsp;</p>
									<div class="float-left">
										<a href="{base}panel/profile" title="بازگشت به پروفایل من"><span class="btn btn-danger text-light">بازگشت</span></a>
										{submit_input}
									</div>
									<div class="clearfix"></div>
									<p>&nbsp;</p>
								{form_close}
								<?php if(!empty($validation_errors)) { ?>
									<div class="alert alert-danger right-to-left text-right">{validation_errors}</div>
								<?php } ?>
								<?php if(!empty($form_success)) { ?>
									<div class="alert alert-success right-to-left text-right">{form_success}</div>
								<?php } ?>
								<div class="hr"></div>
								<p>راهنمایی :</p>
								<p>لطفا قبل از ثبت تغییرات حتما آنها را با دقت بررسی کنید.</p>
								<p>شما می توانید اطلاعاتی را که دوست ندارید همگان ببینند پر نکنید یا در بخش تنظیمات محدودیت های نمایشی روی صفحه ی خود اعمال کنید.</p>
							</div>
						</div>
					</div>
					<div class="col-md-3">
						<div class="content-box">
							<div class="avatar-timeline">
								<img src="" title="" alt="" />
								<h2 class="display-4"></h2>
							</div>
							<div class="connection-state-timeline">
								<div class="">

								</div>
								<div class="">

								</div>
							</div>
							<div class="social-link-timeline">
						</div>
					</div>
				</div>
			</div>
		</section>

		<footer>
			<div class="footer">
				<div class="row">
					
				</div>
			</div>
		</footer>
	</div>

	<script href="{base}assets/library/jquery/jquery-3.3.1.min.js"></script>
	<script href="{base}assets/library/bootstrap/js/bootstrap.min.js"></script>
	<script href="{base}assets/library/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
