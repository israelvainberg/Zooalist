<html lang="<?= $language ?>">
<head>
	<title><?= $title ?></title>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <meta name="description" content="">
    <link href="<?= base_url() ?>assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?= base_url() ?>assets/fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>assets/css/toastr.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>assets/css/style.css" rel="stylesheet">
    <!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body class="card-no-border">
    <section id="wrapper">
		<?= $content ?>
	</section>

	<?= model($model) ?>
    
    <script src="<?= base_url() ?>assets/js/axios.js"></script>
    <script src="<?= base_url() ?>assets/js/vue.js"></script>
    <script src="<?= base_url() ?>assets/js/vue-cookies.js"></script>
    <script src="<?= base_url() ?>assets/js/jquery.min.js"></script>
    <script src="<?= base_url() ?>assets/js/popper.min.js"></script>
    <script src="<?= base_url() ?>assets/js/bootstrap.min.js"></script>
    <script src="<?= base_url() ?>assets/js/validator.min.js"></script>
    <script src="<?= base_url() ?>assets/js/toastr.min.js"></script>
    <script src="<?= base_url() ?>assets/js/templates/auth.js"></script>

<!-- JS -->
<?php foreach($js as $file => $props): echo script($file, $props); endforeach; ?>

</body>
</html>