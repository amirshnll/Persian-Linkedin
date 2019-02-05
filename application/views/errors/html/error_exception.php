<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>exception - چیزی یافت نشد</title>
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
<?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>

	<p>Backtrace:</p>
	<?php foreach ($exception->getTrace() as $error): ?>

		<?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>

			<p style="margin-left:10px">
			File: <?php echo $error['file']; ?><br />
			Line: <?php echo $error['line']; ?><br />
			Function: <?php echo $error['function']; ?>
			</p>
		<?php endif ?>

	<?php endforeach ?>

<?php endif ?>
<?php die(); ?>