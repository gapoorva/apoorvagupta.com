<?php 


	//////////////////////////////////////////////////////////
	///////////////////// HTML BUILDER ///////////////////////
	//////////////////////////////////////////////////////////


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

		public function add($HTML_OBJECT_IN) {
			array_push($this->children, $HTML_OBJECT_IN);
			return $this;
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


	//////////////////////////////////////////////////////////
	///////////////////// DATEBASE API ///////////////////////
	//////////////////////////////////////////////////////////



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

	function authenticate($conn, &$note) {
		$tok = $_COOKIE['tok'];
		$access = validate_access($conn, "tok", $tok, TRUE);
		if(count($access) != 0) {

			//update with new token
			setcookie('tok', get_token($conn, $access));

			//admin noted to visually verify is_admin
			$note = new HTML_element("div");
			$note->attr("class", "admin")->html("p")->text("Admin Mode");
			$note->html("a")->text("Exit")->attr("href", "logout.php");
			/*$note->html("script")->text("console.log('tok:', '".$tok."');");
			$note->html("script")->text("console.log('newtok:', '".hash('sha256', $access[0]+$access[1]+strval($access[2]))."');");
			$note->html("script")->text("console.log('access[0]:', '".$access[0]."');");
			$note->html("script")->text("console.log('access[1]:', '".$access[1]."');");
			$note->html("script")->text("console.log('access[2]:', '".$access[2]."');");*/
			//$note->render();
			return true;
		}
		return false;
	}

	function admin($conn, &$note) {
		if(isset($_COOKIE['tok'])) {
		/*if($_SERVER['REQUEST_METHOD'] == 'GET' && $_GET['admin'] == 'edit') {*/
			return authenticate($conn, $note);
		}
		return false;
	}


	//////////////////////////////////////////////////////////
	////////////////////// UTILIIES //////////////////////////
	//////////////////////////////////////////////////////////

	function get_token($conn, $access) {
		//Generate new token
		$newtok = hash('sha256', $access[0]+$access[1]+strval($access[2]));
		//update access table
		update_access($conn, "tok", $newtok, "s");
		update_access($conn, "ts", time(), "i");

		return $newtok;

	}

	function is_ssl() {
	    if ( isset($_SERVER['HTTPS']) ) {
	        if ( 'on' == strtolower($_SERVER['HTTPS']) )
	            return true;
	        if ( '1' == $_SERVER['HTTPS'] )
	            return true;
	    } elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
	        return true;
	    }
	    return false;
	}

	function remove_tags($str) {
		$start = 0;
		$end = 0;
		while(($start = strpos($str, '<')) !== FALSE && ($end = strpos($str, '>')) !== FALSE) {
			$str = substr($str, 0, $start) . substr($str, $end+1);	
		}
		return $str;
	}

	function remove_punc($str) {
		$found = 0;
		$punc = array('\'', '"', '.', ',', '?', '!', '/', ':', ';', '\\', '(', ')', '{', '}', '[', ']', '*', '&', '^', '%', '$', '#', '@', '-' );
		foreach($punc as $symbol) {
			while(($found = strpos($str, $symbol)) !== FALSE) {
				$str = substr($str, 0, $found) . substr($str, $found+1);
			}
		}
		return $str;
	}

	function vectorize($str) {
		//Remove stop words and create frequency mapping of words
		$map = array();
		$stop_words = array("", "a", "about", "above", "above", "across", "after", "afterwards", "again", "against", "all", "almost", "alone", "along", "already", "also","although","always","am","among", "amongst", "amoungst", "amount",  "an", "and", "another", "any","anyhow","anyone","anything","anyway", "anywhere", "are", "around", "as",  "at", "back","be","became", "because","become","becomes", "becoming", "been", "before", "beforehand", "behind", "being", "below", "beside", "besides", "between", "beyond", "bill", "both", "bottom","but", "by", "call", "can", "cannot", "cant", "co", "con", "could", "couldnt", "cry", "de", "describe", "detail", "do", "done", "down", "due", "during", "each", "eg", "eight", "either", "eleven","else", "elsewhere", "empty", "enough", "etc", "even", "ever", "every", "everyone", "everything", "everywhere", "except", "few", "fifteen", "fify", "fill", "find", "fire", "first", "five", "for", "former", "formerly", "forty", "found", "four", "from", "front", "full", "further", "get", "give", "go", "had", "has", "hasnt", "have", "he", "hence", "her", "here", "hereafter", "hereby", "herein", "hereupon", "hers", "herself", "him", "himself", "his", "how", "however", "hundred", "ie", "if", "in", "inc", "indeed", "interest", "into", "is", "it", "its", "itself", "keep", "last", "latter", "latterly", "least", "less", "ltd", "made", "many", "may", "me", "meanwhile", "might", "mill", "mine", "more", "moreover", "most", "mostly", "move", "much", "must", "my", "myself", "name", "namely", "neither", "never", "nevertheless", "next", "nine", "no", "nobody", "none", "noone", "nor", "not", "nothing", "now", "nowhere", "of", "off", "often", "on", "once", "one", "only", "onto", "or", "other", "others", "otherwise", "our", "ours", "ourselves", "out", "over", "own","part", "per", "perhaps", "please", "put", "rather", "re", "same", "see", "seem", "seemed", "seeming", "seems", "serious", "several", "she", "should", "show", "side", "since", "sincere", "six", "sixty", "so", "some", "somehow", "someone", "something", "sometime", "sometimes", "somewhere", "still", "such", "system", "take", "ten", "than", "that", "the", "their", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "therefore", "therein", "thereupon", "these", "they", "thickv", "thin", "third", "this", "those", "though", "three", "through", "throughout", "thru", "thus", "to", "together", "too", "top", "toward", "towards", "twelve", "twenty", "two", "un", "under", "until", "up", "upon", "us", "very", "via", "was", "we", "well", "were", "what", "whatever", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "whereupon", "wherever", "whether", "which", "while", "whither", "who", "whoever", "whole", "whom", "whose", "why", "will", "with", "within", "without", "would", "yet", "you", "your", "yours", "yourself", "yourselves", "the");

		$word = strtolower(strtok($str, " \n\t"));
		while($word != FALSE) {
			if(!in_array($word, $stop_words)) {
				if(array_key_exists($word, $map)) {
					$map[$word]++;
				} else {
					$map[$word] = 1;
				}
			}
			$word = strtolower(strtok(" \n\t"));
		}

		return $map;

	}

	//////////////////////////////////////////////////////////
	/////////////////// CONTENT CREATION /////////////////////
	//////////////////////////////////////////////////////////

	function get_head_section() {

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

		return $head;
	}

	function head_section($is_admin = false) {
		get_head_section()->render();
	}

	function get_page_title($title, $subsection = NULL) {

		$row = new HTML_element("div");
		$row->attr("class", "row");
		$row->html("div")->attr("class", "col-sm-10 col-xs-offset-1 col-xs-10 title indent")->html("h1")->text($title);
		if(!is_null($subsection)) {
			$row->find("h1")[0]->text("&nbsp;|&nbsp;");
			$row->find("div", "class:title")[0]->html("h2")->attr("class", "subsection")->text($subsection);
		}
		return $row;
		$row->render();
	}

	function page_title($title, $subsection = NULL, $is_admin = false) {
		get_page_title($title, $subsection)->render();
	}

	function get_footer_section($this_page) {
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
			->html("a")->attr("href", "https://gator4221.hostgator.com/~gapoorva/new/admin.php?src=".urlencode($this_page))
			->html("img")->attr("class", "center-block")->attr("src", "images/favicon.ico");
		
		return $footer;
	}

	function footer_section($this_page) {
		get_footer_section($this_page)->render();
	}

	function get_side_menu($include_picture = FALSE) {
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

		return $wrapper;
	}

	function side_menu($include_picture = FALSE, $is_admin = FALSE) {
		get_side_menu($include_picture)->render();
	}

	function get_content($conn, $content, $is_admin = FALSE) {
		$stmt = $conn->prepare("SELECT filename FROM content WHERE pagename=?");
		$stmt->bind_param("s", $content);
		$stmt->execute();
		$stmt->bind_result($filename);

		if($stmt->fetch()) {
			if($is_admin) {
				echo '<p class="lead"><b><a href="edit.php?page='.urlencode($content).'"> Edit this content </a></b></p>';
			}
			include $filename;
		} else {
			die("filename for content '".$content."' was not found");
		}
	}
?>