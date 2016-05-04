<?php 

	class HTML_element {
		private $type = "NULLELEMENT"; //type of element
		private $attributes = array(); // map of attr
		private $children = array(); // array of inner children
		private $parent = null;

		function __construct($type_in) {
			$this->type = $type_in;
		}

		public function render() {
			$singleton_list = array("img", "meta", "link", "br", "input");
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

		public function get_attr($attr) {
			return $this->attributes[$attr];
		}

		public function text($text) {
			array_push($this->children, $text);
			return $this;
		}

		public function html($type_in) {
			array_push($this->children, new HTML_element($type_in));
			array_slice($this->children, -1)[0]->parent =& $this;
			return array_slice($this->children, -1)[0];
		}

		public function parent() {
			return $this->parent;
		}

		private function contains($string, $values) {
			foreach ($values as $value) {
				if(strpos($string, $value) === false) {
					return false;
				}
			}
			return true;
		}

		private function match($context, $selector) {
			$select = explode(":", $selector);
			return is_null($selector) || $context->contains($context->attributes[$select[0]], explode(" ", $select[1]));
		}

		public function find($element, $selector = null) {
			$found_elements = array();
			foreach($this->children as $child) {
				
				//var_dump($select);
				if(!is_string($child) && $child->type == $element && $this->match($child, $selector)) {

					//echo "doing this<br>";
					array_push($found_elements, $child);
				}
				if(!is_string($child)) {
					$in_child = $child->find($element, $selector);
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

	function validate_access($conn, $column, $valuetocheck, $check_time = FALSE) {
		// Checks if $column is in access table
		$TIMEOUT = 3600;
		$now = $check_time ? time() - $TIMEOUT : 0;

		$stmt = $conn->prepare('SELECT pw, tok, ts FROM access WHERE '. $column . "=? AND ts > ?");
		$stmt->bind_param('si', $valuetocheck, $now);

		$stmt->execute();
		$stmt->bind_result($pw, $tok, $ts);

		if($stmt->fetch()) {
			return array($pw, $tok, $ts);
		} else {
			return array();
		}

	}

	function update_access($conn, $column, $update_value, $typestr) {
		$stmt = $conn->prepare('UPDATE access SET '. $column . '=?');
		$stmt->bind_param($typestr, $update_value);

		if(!$stmt->execute()) {
			trigger_error($stmt->error, E_USER_ERROR);
		}

	}

	function admin_mode($conn) {
		$tok = $_GET['edit'];
		$result = validate_access($conn, "tok", $tok, TRUE);
		if(count($result) != 0) {
			$note = new HTML_element("div");
			$note->attr("class", "admin")->html("p")->text("Admin Mode");
			$note->render();
			return true;
		}
		return false;
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
		$head->html("link")->attr("rel", "stylesheet")->attr("type", "text/css")->attr("href", "css/bootstrap-theme.min.css");
		$head->html("link")->attr("rel", "stylesheet")->attr("type", "text/css")->attr("href", "css/main.css");

		//Scripts
		$head->html("script")->attr("type", "text/javascript")->attr("src", "js/jquery.min.js");
		$head->html("script")->attr("type", "text/javascript")->attr("src", "js/jquery-ui.min.js");
		$head->html("script")->attr("type", "text/javascript")->attr("src", "js/bootstrap.min.js");

		$head->render();
	}
	
	function page_title($title, $subsection = NULL) {

		$row = new HTML_element("div");
		$row->attr("class", "row");
		$row->html("div")->attr("class", "col-sm-10 col-xs-offset-1 col-xs-10 title indent")->html("h1")->text($title);
		if(!is_null($subsection)) {
			$row->find("h1")[0]->text("&nbsp;|&nbsp;");
			$row->find("div", "class:title")[0]->html("h2")->attr("class", "subsection")->text($subsection);
		}
		$row->render();
	}

	function footer_section() {
		$copy_right = " © 2015-2016 Apoorva Gupta ";
		$facebook_link = "https://www.facebook.com/guptaapoorva";
		$github_link = "https://www.github.com/gapoorva";
		$mail_link = "mailto:gapoorva@umich.edu";
		$logo_link = "images/favicon.ico";

		$footer = new HTML_element("div");
		$footer->attr("class", "row footer");

		$footer->html("div")->attr("class", "col-xs-offset-1 col-xs-11 col-sm-2 col-md-3")->html("p")->text($copy_right);

		$footer
			->html("div")->attr("class", "col-xs-offset-1 col-xs-6 col-sm-5 col-sm-offset-1 col-md-offset-1")
			->html("p")->html("a")->attr("href", $github_link)->text("Github")
			->parent()->text(" &nbsp; ")->html("a")->attr("href", $facebook_link)->text("Facebook")
			->parent()->text(" &nbsp; ")->html("a")->attr("href", $mail_link)->text("Email");

		$footer
			->html("div")->attr("class", "col-xs-offset-3  col-xs-1 col-sm-offset-1 col-md-offset-0")
			->html("a")->attr("href", "http://www.apoorvagupta.com")
			->html("img")->attr("class", "center-block")->attr("src", "images/favicon.ico");

		$footer->render();
	}

	function side_menu($include_picture = FALSE) {
		$blog = "heres_what_i_think.php";
		$current_work = "what_i_do.php";
		$past_work = "what_ive_done.php";
		$communicate = "communicate.php";

		$wrapper = new HTML_element("div");
		if($include_picture) {
			$wrapper->html("img")->attr("class", "img-responsive profile")->attr("src", "images/profile-image.jpg");
		} else {
			$wrapper->html("p")->attr("class" ,"lead")->html("a")->attr("href", "index.php")->text("Home");
		}
		$wrapper->html("p")->attr("class", "lead")->html("a")->attr("href", $blog)->text("Blog");
		$wrapper->html("p")->attr("class", "lead")->html("a")->attr("href", $current_work)->text("What I Do");
		$wrapper->html("p")->attr("class", "lead")->html("a")->attr("href", $past_work)->text("What I've Done");
		$wrapper->html("p")->attr("class", "lead")->html("a")->attr("href", $communicate)->text("Communicate");

		$wrapper->render();
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