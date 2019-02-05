<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>قوانین سایت</title>
	<link rel="stylesheet" type="text/css" href="{base}assets/layout/layout.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/bootstrap/css/bootstrap-grid.min.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/fontawesome/css/all.min.css">
	<link rel="shortcut icon" href="{base}assets/images/favicon.png"/>
</head>
<body style="background: #f7f8fa !important;">
	<div class="profile-topline">
		<div class="container">
			<div class="row">
				<div class="col-md-12 right-to-left">
					<a href="{base}register" title="ساخت رزومه رایگان" class="text-light">
						<p class="d-inline"><sapn class="fas fa-1x fa-user-plus"></sapn>&nbsp;<span>شما هم می توانید یک رزومه ی حرفه ای داشته باشید!</span></p>
					</a>
				</div>
			</div>
		</div>
	</div>

	<section class="pages">
		<div class="container">
			<div class="row right-to-left text-right">
				<div class="col-md-12 text-justify">
					<h1><span class="fas fa-1x fa-ruler"></span>&nbsp;قوانین سایت</h1>
					<p></p>
					<ul>
						<li class="nav-item">کاربر گرامی لطفا قبل از استفاده و یا ثبت نام در سرویس " لینکدین فارسی " موارد ذیل را کاملا مطالعه نمایید.</li>
						<li class="nav-item">در صورتیکه مایل به استفاده از خدمات این وب سایت می باشید حتما باید توافقنامه زیر را قبول نمایید در غیر اینصورت مجاز به استفاده از این سامانه نیستید.</li>
						<li class="nav-item">از توزیع محتوای نژادی یا قومی ، دینی و ... خودداری کنید.</li>
						<li class="nav-item">برای کمک به مبارزه و جلوگیری از هرزنامه از نام حقیقی خود استفاده نمایید.</li>
						<li class="nav-item">عكس کاربری پروفایل شخصی شما نباید دارای محتوای غیرمجاز یا توهین آمیز باشد.</li>
						<li class="nav-item">در صورت رعایت نکردن هرکدام از موارد فوق حساب کاربری شما در سیستم مسدود می شود.</li>
						<li class="nav-item">در صورت مشاهده تخلف شما به عنوان یک کاربر در این سامانه حق گزارش تخلف را خواهید داشت.</li>
						<li class="nav-item">از توزیع اطلاعات شخصی و محرمانه شخصی سایر افراد و یا استفاده از حساب شخص دیگری یا باز کردن حساب به نام فرد دیگر خودداری فرمایید.</li>
						<li class="nav-item">تمام هدف ما ارائه خدمات به کاربران عزیز ایرانی می باشد ، لذا از شما تقاضا داریم تا اطلاعات خود را بدون اغراق و با در نظر گرفتن واقعیت وارد نمایید تا این وبسایت برای شما کاربردی باشد.</li>
						<li class="nav-item">( تاریخ بروز رسانی قوانین : پاییز و زمستان 1397 خورشیدی )</li>
					</ul>
					<br />
					<div class="button-nvaigation text-center">
						<a class="btn btn-primary" href="{base}register" title="همین  حالا ثبت نام کنید.">همین  حالا ثبت نام کنید.</a>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>

			<br />
			<div class="row">
				<div class="col-md-12 text-right right-to-left alphabet">
					<h6 class="text-dark d-inline"><span class="fas fa-1x fa-database"></span>&nbsp;<small>دایرکتوری کاربران  لینکدین فارسی : </small></h6>
					<nav class="navbar">
						<ul class="nav">
							<?php
								$counter = 1;
								foreach ($alphabet as $my_alphabet) {
									echo '<li class="nav-item"><strong><small><a href="{base}find/alphabet/' . $counter . '" title="جستجو با حرف ' . $my_alphabet . '"class="nav-link text-dark">' . $my_alphabet . '</a></small></strong></li>';
									$counter++;
								}
							?>
							<li class="nav-item"><strong><small><a href="{base}find" title="جستجوی بیشتر" class="nav-link text-dark"><span class="fas fa-1x fa-box-open">&nbsp; </span><span>بیشتر...</span></a></small></strong></li>
						</ul>
					</nav>
				</div>
			</div>
			
		</div>
	</section>

	<div class="clearfix"></div>
	<footer>
		<div class="copyright-profile">
			<div class="container">
				<div class="row">
					<div class="col-md-12 right-to-left text-right text-light">
						<p>&copy; <?php echo date('Y') . " - " . (date('Y') + 1) . " : [ "; ?> تمامی حقوق این وبسایت محفوظ می باشد. ]</p>
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
