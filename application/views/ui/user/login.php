<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ورود به حساب کاربری</title>
	<link rel="stylesheet" type="text/css" href="{base}assets/layout/layout.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/bootstrap/css/bootstrap-grid.min.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/fontawesome/css/fontawesome.min.css">
	<link rel="stylesheet" type="text/css" href="{base}assets/library/fontawesome/css/all.min.css">
	<link rel="shortcut icon" href="{base}assets/images/favicon.png"/>
</head>
<body class="user-out-form">
	<div class="container">
		<header>
			<div class="header">
				<div class="row">
					<div class="col-md-12">
						<nav class="navbar">
							<ul class="nav">
								<li class="nav-item"><a href="{base}find" title="همکاران خود را بیابید" class="nav-link text-light"><span>جستجوی همکاران</span>&nbsp;<span class="fas fa-1x fa-search"></span></a></li>
								<li class="nav-item"><a href="{base}register" title="عضویت رایگان" class="nav-link text-light"><span>عضویت</span>&nbsp;<span class="fas fa-1x fa-user-plus"></span></a></li>
							</ul>
						</nav>
					</div>
				</div>
			</div>
		</header>

		<section>
			<div class="content">
				<div class="row">
					<div class="col-md-4 text-center">
						<div class="logo">
							<h1 class="text-light display-4">لینکدین</h1>
							<h2 class="bg-light"><strong>فا</strong></h2>
						</div>
						<div class="form form-group">
							{form_open}
								{email_input}
								{password_input}
								<div class="text-center">
									<div class="flot-left d-inline width-50">{submit_input}</div>
									<div class="float-right d-inline width-50"><a href="{base}forget" title="رمزعبور خود را فراموش کردید؟" class="nav-link text-light"><span>بازیابی رمزعبور</span>&nbsp;<span class="fas fa-1x fa-recycle"></span></a></div>
									<div class="clearfix"></div>
								</div>
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
		</section>

		<footer>
			<div class="footer">
				<div class="row">
					<div class="col-md-12 text-right right-to-left">
						<h6 class="text-light d-inline"><span class="fas fa-1x fa-database"></span>&nbsp;<small>دایرکتوری کاربران  لینکدین فارسی : </small></h6>
						<nav class="navbar">
							<ul class="nav">
								<?php
									$counter = 1;
									foreach ($alphabet as $my_alphabet) {
										echo '<li class="nav-item"><strong><small><a href="{base}find/alphabet/' . $counter . '" title="جستجو با حرف ' . $my_alphabet . '" class="nav-link text-light">' . $my_alphabet . '</a></small></strong></li>';
										$counter++;
									}
								?>
								<li class="nav-item"><strong><small><a href="{base}find" title="جستجوی بیشتر" class="nav-link text-light"><span class="fas fa-1x fa-box-open">&nbsp; </span><span>بیشتر...</span></a></small></strong></li>
							</ul>
						</nav>
					</div>
				</div>
			</div>
		</footer>
	</div>

	<script href="{base}assets/library/jquery/jquery-3.3.1.min.js"></script>
	<script href="{base}assets/library/bootstrap/js/bootstrap.min.js"></script>
	<script href="{base}assets/library/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
