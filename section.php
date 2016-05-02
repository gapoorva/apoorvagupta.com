<?php 

	function head_section() {
		echo '<head>
				<meta charset="utf-8">
				<meta name="author" content="Apoorva Gupta">
				<meta name="description" content="The personal website of Apoorva Gupta, Software Developer.">
				<meta keywords="apoorva, gupta, software, developer, michigan, computer, science, programer, projects, project">
				<meta http-equiv="content-type" content="text/html;charset=UTF-8">
				<meta property="og:url" content="http://www.apoorvagupta.com">
				<meta property="og:title" content="Apoorva Gupta, Software Developer">
				<meta property="og:description" content="The personal website of Apoorva Gupta, Software Developer.">
				<meta property="og:site_name" content="Apoorva Gupta">
				<!-- TODO:
				<meta property="og:image" content="???">
				 -->

				<title>Apoorva Gupta</title>

				<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon"/>

				<link rel="stylesheet" type="text/css" href="css/jquery-ui.css"/>
				<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"/>
				<link rel="stylesheet" type="text/css" href="css/bootstrap-theme.min.css"/>
				<link rel="stylesheet" type="text/css" href="css/main.css"/>

				<script type="text/javascript" src="js/jquery.min.js"></script>
				<script type="text/javascript" src="js/jquery-ui.min.js"></script>
				<script type="text/javascript" src="js/bootstrap.min.js"></script>
			</head>';
	}
	
	function page_title($title, $subsection = NULL) {
		$out= '<div class="row">
				<div class="col-sm-10 col-xs-offset-1 col-xs-10 title indent">
					<h1>' . $title;

		if(!is_null($subsection)) {
			$out .= '&nbsp;|&nbsp;</h1><h2 class="subsection">' . $subsection . '</h2>';
		} else {
			$out .= "</h1>";
		}
				
		$out .=		'</div>
				</div>';	
		echo $out;
	}

	function footer_section() {
		echo '<div class="row footer">
				<div class="col-xs-offset-1 col-xs-11 col-sm-2 col-md-3">
					<p> © 2015-2016 Apoorva Gupta </p>
				</div>
				<div class="col-xs-offset-1 col-xs-6 col-sm-5 col-sm-offset-1 col-md-offset-1">
					<p><a href="https://github.com/gapoorva">Github</a> &nbsp; <a href="https://www.facebook.com/guptaapoorva">Facebook</a> &nbsp; <a href="mailto:gapoorva@umich.edu">Email</a></p>
				</div>
				<div class="col-xs-offset-3  col-xs-1 col-sm-offset-1 col-md-offset-0"><a href="http://www.apoorvagupta.com"><img class="center-block" src="images/favicon.ico"></a></div>
			</div>';
	}

	function side_menu($include_picture = FALSE) {
		$out = '';
		if($include_picture) {
			$out .= '<img class="img-responsive profile" src="images/profile-image.jpg">';
		}	
		$out .=		'<p class="lead"><a href="#">Personal Blog</a></p>
					<p class="lead"><a href="#">What I Do</a></p>
					<p class="lead"><a href="#">What I\'ve Done</a></p>
					<p class="lead"><a href="#">Communicate</a></p>
				</div>';
		echo $out;
	}

	function home_page_content() {
		echo '<p class="lead"> A young software developer based in the United States.</p>
					<br>
					<p class="lead">Pursing a Bachelor’s Degree in Computer Science at the <a href="https://www.umich.edu" target="_blank">University of Michigan</a> and working at <a href="https://www.qualtrics.com" target="_blank">Qualtrics Labs.</a></p>
					<br>
					<p class="lead">Likes to code, travel, hike, cook, and eat.</p>
					<br>
					<p class="lead">Works on lots of cool things that you should check out.</p>
					<br>
					<p class="lead">Likes meeting new people and making new experiences. Most importantly, likes doing things that help others.</p>
					<br>
					<p class="lead">Looking to connect with like minded people, employers, and the curious visitor.</p>';
	}
?>