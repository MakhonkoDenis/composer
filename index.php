<?php
	if ( ! class_exists('Tools') ) {
		require_once __DIR__ . "/tools.php";

		$tools = new Tools();
		$config = $tools->get_config();
		//var_dump($config);
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<meta name="description" content="Theme Composer">
		<meta name="author" content="Behaart">
		<link rel="shortcut icon" href="assets/image/fav-icon.png" type="image/png">
		<title>Theme Composer</title>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
		<link rel="stylesheet" href="assets/css/min/style.min.css">
	</head>

	<body>
		<div class="container">
			<header class="row header">
				<div class="col-xs-8">
					<h1>Wordpress Theme Generator</h1>
				</div>
			</header>
			<section class="row content">
				<article class="col-xs-8">
					<form class="form-horizontal" id="cherry-composer">
						<div class="form-group">
							<label for="theme_name" class="col-sm-4 control-label">Theme name:</label>
							<div class="col-sm-8">
								<input type="text" id="theme_name" class="form-control input-lg" name ="theme_name" value="">
							</div>
						</div>
						<div class="form-group">
							<label for="branch_name" class="col-sm-4 control-label">Select theme type:</label>
							<div class="col-sm-8">
								<select id="branch_name" name ="branch_name" class="form-control input-lg">
									<?php
										echo $tools->render_oprions( $config['theme_branches'] );
									 ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-9">
								<div id="message" class="alert alert-danger" role="alert">ERROR</div>
							</div>
							<div class="col-sm-2 button">
								<button id="download-theme" class="btn btn-primary btn-lg" type="submit" formmethod="post" formaction="composer.php">Download Theme</button>
							</div>
						</div>

					</form>
				</article>
			</section>
		</div>
		<script src="assets/js/min/jquery-2.2.2.min.js"></script>
		<script src="assets/js/min/script.min.js"></script>
	</body>
</html>