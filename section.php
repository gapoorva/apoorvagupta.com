<?php 

	class HTML_element {
		private $type = "NULLELEMENT"; //type of element
		private $attributes = array("class" => ""); // map of attr
		private $children = array(); // array of inner children

		function __construct($type_in) {
			$this->type = $type_in;
		}

		public function render() {
			$singleton_list = array("img", "meta", "link", "br");
			echo "<" . $this->type . " ";
			foreach($this->attributes as $attr => $val) {
				if($val != "") {
					echo $attr . "=\"" . $val . "\" ";
				}
			}

			if(in_array($this->type, $singleton_list)) {
				echo "/>\n";
			} else {
				echo ">";
				foreach($this->children as $child) {
					if(is_string($child)) { 
						echo $child;
					} else {
						$child->render();
					}
				}
				echo "</" . $this->type . ">"; 
			}
		}

		public function attr($attr, $val) {
			$this->attributes[$attr] = $val;
			return $this;
		}

		public function text($text) {
			array_push($this->children, $text);
			return $this;
		}

		public function html($type_in) {
			array_push($this->children, new HTML_element($type_in));
			return array_slice($this->children, -1)[0];
		}

		private function contains($string, $values) {
			foreach ($values as $value) {
				if(strpos($string, $value) === false) {
					return false;
				}
			}
			return true;
		}

		public function find($element, $classes = array()) {
			$found_elements = array();
			foreach($this->children as $child) {
				if(!is_string($child) &&
					$child->type == $element &&
					$this->contains($child->attributes["class"], $classes)) {

					array_push($found_elements, $child);
					$in_child = $child->find($element, $classes);
					foreach ($in_child as $elt) {
						array_push($found_elements, $elt);
					}
				}
			}
			return $found_elements;
		}
	}


	$servername = "localhost";
	$username = "gapoorva_1";
	$password = "mysql";
	$dbname = "gapoorva_content";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);

	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 


	function head_section() {

		$head = new HTML_element("head");
		//Charset
		$head->html("meta")->attr("charset", "utf-8");
		//Description
		$head->html("meta")->attr("name", "description")->attr("content", "the personal website of Apoorva Gupta, Software Developer.");
		//Author
		$head->html("meta")->attr("name", "author")->attr("content", "Apoorva Gupta");
		//Keywords
		$head->html("meta")->attr("keywords", "apoorva, gupta, software, developer, michigan, computer, science, programer, projects, project");
		//HTTP-Equiv
		$head->html("meta")->attr("http-equiv", "content-type")->attr("content", "text/html;charset=UTF-8");
		//fb og:url
		$head->html("meta")->attr("property","og:url")->attr("content", "http://www.apoorvagupta.com");
		//fb og:title
		$head->html("meta")->attr("property", "og:title")->attr("content", "Apoorva Gupta, Software Developer");
		//fb og:description
		$head->html("meta")->attr("property", "og:description")->attr("content", "The personal website of Apoorva Gupta, Software Developer.");
		//fb og:site_name
		$head->html("meta")->attr("property", "og:site_name")->attr("content", "Apoorva Gupta");
		//fb og:image
		$head->html("meta")->attr("property", "og:image")->attr("content", "images/profile-image.jpg");

		//Title
		$head->html("title")->text("Apoorva Gupta");

		//Favicon
		$head->html("link")->attr("rel", "shortcut icon")->attr("href", "images/favicon.ico")->attr("type", "image/x-icon");

		//Stylesheets
		$head->html("link")->attr("rel", "stylesheet")->attr("type", "text/css")->attr("href", "css/jquery-ui.css");
		$head->html("link")->attr("rel", "stylesheet")->attr("type", "text/css")->attr("href", "css/bootstrap.min.css");
		$head->html("link")->attr("rel", "stylesheet")->attr("type", "text/css")->attr("href", "css/bootstrap-theme.css");
		$head->html("link")->attr("rel", "stylesheet")->attr("type", "text/css")->attr("href", "css/main.css");

		//Scripts
		$head->html("script")->attr("type", "text/javascript")->attr("src", "js/jquery.min.js");
		$head->html("script")->attr("type", "text/javascript")->attr("src", "js/jquery-ui.min.js");
		$head->html("script")->attr("type", "text/javascript")->attr("src", "js/bootstrap.min.js");

		$head->render();
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