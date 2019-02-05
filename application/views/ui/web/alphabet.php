<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$ci =&get_instance();
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>لینکدین فارسی - نتیجه جستجو</title>
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
					<h1><span class="fas fa-1x fa-search"></span>&nbsp;نتیحه جستجو   دایرکتوری "{search_text}" ...</h1>
					<p></p>
					<div class="out_search">

						{form_open}
						<p>{search_input}</p>
						<p>{submit_input}</p>
						<div class="clearfix"></div>
						{form_close}
						<br />

						<?php
							if($array_result!==false)
							{
								$suggest_counter = 0;
								$ci->load->model('connections_model');
								$ci->load->model('avatar_model');
								$ci->load->model('user_option_model');

								foreach ($array_result as $ars){
									$temp_privacy = $ci->user_option_model->get_option($ars['user_id'], 'private_page');
									if($temp_privacy  == "true")
										continue;

									$temp_full_name = $ci->person_model->read_user_person($ars['user_id']);
									$temp_full_name = $temp_full_name['firstname'] . " " . $temp_full_name['lastname'];

									$temp_privacy = $ci->user_option_model->get_option($ars['user_id'], 'private_avatar');
									if($temp_privacy == "true")
										$temp_avatar = "default.png";
									else
										$temp_avatar = $ci->avatar_model->user_current_avatar($ars['user_id']);
								?>
								<a href="{base}user/<?php echo md5($ars['user_id']); ?>" title="مشاهده ی پروفایل <?php echo $temp_full_name; ?>" target="_blank">
									<div class="suggest-item">
										<div class="suggest-item-image float-right text-center" style="padding: 0 !important;">
											<img class="img-fluid" src="{base}upload/avatar/<?php echo $temp_avatar; ?>" title="<?php echo $temp_full_name; ?>" src="<?php echo $temp_full_name; ?>" />
											</div>
											<div class="suggest-item-content float-right" style="margin-top: 0px !important;">
												<p class="text-dark"><?php echo $temp_full_name; ?></p>
											</div>
										<div class="clearfix"></div>
									</div>
								</a>
								<?php $suggest_counter++;
								}

								if($suggest_counter==0)
								{
									$array_result = false;
								}
							}
						?>

					<?php 
						if($array_result===false)
						{
							echo '<p class="alert alert-dark">چیزی یافت نشد.</p>';
						}
					?>
					</div>

					<br />
					<div class="button-nvaigation text-center">
						<a class="btn btn-primary" href="{base}find" title="بازگشت به صفحه جستجو">بازگشت</a>
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