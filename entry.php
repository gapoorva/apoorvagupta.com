<?php 
	include 'section.php';
	$note = null;
	$is_admin_mode = admin($conn, $note);

	//KNOWN REQUEST
	$pageid = $_REQUEST['id'];
	$known_filepath = 'entries/'.$pageid.'.php';
	$found_entry = true;

	//CREATE REQUEST (POST)
	$create_id = "post".date("mdY");
	$create_date = date("l F jS, Y | g:i a");
	$create_title = $_REQUEST['title'];
	$create_filepath = 'entries/'.$create_id.'.php';
	$create_content = $_REQUEST['content'];
	$create_lead = $_REQUEST['lead'];

	//MODE BOOLEANS
	$is_create = $_REQUEST['mode'] == 'create';
	$is_post = $_SERVER['REQUEST_METHOD'] == 'POST';
	$unauthorized = (($is_create || $is_post) && !$is_admin_mode);
	$create_new_post = $is_post && $is_create;
	$update = $is_post && !is_null($pageid);
	$retrive = !is_null($pageid);
	$add_retrive = $retrive && $is_admin_mode;

	date_default_timezone_set('America/Detroit');

	if($unauthorized) {
		header("Location: index.php");
		die();
	} else if ($create_new_post) {
		//CREATE A NEW BLOG ENTRY IN DATABASE AND CREATE FILE
		$handle = fopen($create_filepath, 'w');
		fwrite($handle, $create_content);
		fclose($handle);

		$stmt = $conn->prepare("INSERT INTO blog (`ts`, `title`, `id`, `date`, `lead`) VALUES (?,?,?,?,?);");
		$stmt->bind_param("issss", time(), $create_title, $create_id, $create_date, $create_lead);
		if(!$stmt->execute()) {
			die("write failed!");
		}
		
		//navigate to page view mode if all is well
		header("Location: entry.php?id=".$create_id);
		die();
	} else if ($update) {
		//UPDATE AN EXISTING POST
		$handle = fopen($known_filepath, 'w');
		fwrite($handle, $create_content);
		fclose($handle);

		$stmt = $conn->prepare('UPDATE blog SET ts=?, lead=?, title=? WHERE id=?');
		$stmt->bind_param("isss", time(), $create_lead, $create_title, $pageid);
		if(!$stmt->execute()) {
			die("update failed!");
		}

		//navigate to page view mode if all is well
		header("Location: entry.php?id=".$pageid);
		die();

	} else {
		//GET AN EXISTING POST
		$stmt = $conn->prepare("SELECT `date`, `title` FROM blog WHERE id=?");
		$stmt->bind_param("s", $pageid);
		$stmt->execute();
		$stmt->bind_result($entry_date, $entry_title);
		if(!$stmt->fetch()) {
			$found_entry = false;
		}
	}

	$notfound = !$found_entry && !$is_create;

?>
<!DOCTYPE html>
<html>
	<!-- ALL HEAD CONTENT HERE -->
	<?php head_section($is_admin_mode);?>
	<script type="text/javascript">
		function createLead() {
			var content = $("#content").val();
			while(content.includes("<") || content.includes(">")) {
				var start = content.indexOf("<");
				var end = content.indexOf(">");
				if(start == -1 || end == -1) {
					alert("FOUND AN ERROR IN CONTENT SYNTAX. ABORTING...");
					return false;
				}
				content = content.substring(0, start) + content.substring(end+1);
			}

			content = content.trim();

			content = content.length < 190 ? content : content.substring(0,189) + "...";

			$("#lead").val(content);

			return true;
		}
		$(document).ready( 
			function() {
				$("#new_entry").submit(function(e) {
					return createLead();
				});
			}
		);
		
	</script>

	<body>
		<!-- FULL SCREEN CONTAINER -->
		<div class="container-fluid">
			<!-- PAGE TITLE -->
			<?php page_title("Here's What I Think", NULL, $is_admin_mode);?>
			<!-- PAGE CONTENT -->
			<div class="row">
				<!-- MAIN CONTENT -->
				<div class="col-md-7 col-xs-offset-1 col-xs-6 indent">
					<?php 
						if($is_admin_mode) {
							$note->render();
						}
						if($notfound) {
							//ENTRY NOT FOUND
							echo "<p class='lead'> Sorry! Couldn't find that entry. <a href='heres_what_i_think.php'> Try again? </a>";
						} else if($is_create || $add_retrive) {
							//CREATE FORM 
					?>
						<form id="new_entry" method="POST">
							<div class="form-group">
								<label for="title"> Entry Title: </label>
								<input type="text" id="title" name="title" class="form-control" placeHolder="Question/Title here" value=<?php if($add_retrive) echo '"'.$entry_title.'"'; ?> >
							</div>
							<div class="form-group">
								<label for="content"> Entry Text (HTML): </label>
								<textarea form="new_entry" id="content" name="content" class="form-control" rows="60" placeHolder="Thoughts here. Remember to be candid and that anyone could read these!"><?php if($add_retrive) include $known_filepath; ?></textarea>
							</div>
							<input type="hidden" id="lead" name="lead">
							<!-- ADD SOMTHEING HERE FOR UPDATE VARIABLE -->
							<button type="submit" class="btn btn-primary">Submit</button>
						</form>

					<?php

						} else {
							//JUST GETTING THE CONTENT FOR THIS ID
							echo "<h2>".$entry_title."</h2>";
							echo "<h4>".$entry_date,"</h4>";
							include $known_filepath;
						}
					?>


				</div>
				<!-- SIDE MENU CONTENT -->
				<div class="col-xs-offset-1 col-xs-3 col-sm-3 col-md-2 side-menu">
					<?php side_menu(FALSE, $is_admin_mode); ?>
				</div>
			</div>
			<!-- FOOTER -->
			<?php 
			$this_page =  strlen($_SERVER['QUERY_STRING']) ? basename($_SERVER['PHP_SELF'])."?".$_SERVER['QUERY_STRING'] : basename($_SERVER['PHP_SELF']);
			footer_section($this_page); ?>
		</div>
	</body>
</html>

