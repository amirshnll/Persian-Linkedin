<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>404 - چیزی یافت نشد</title>
<link rel="shortcut icon" href="<?php echo config_item('base_url'); ?>/assets/images/favicon.png"/>
<style type="text/css">
body {
	
}
#container {
	margin: auto;
	width: 80%;
}
</style>
</head>
<body>
	<div id="container">
		<img src="<?php echo config_item('base_url'); ?>/assets/images/error.png" width="100%" title="چیزی یافت نشد." alt="چیزی یافت نشد." />
	</div>
</body>
</html>
<?php die(); ?>