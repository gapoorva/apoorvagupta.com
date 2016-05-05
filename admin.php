<?php 
	include_once 'section.php';

	if(!is_ssl()) {
		header("Location: index.php");
		die();
	} else {

		$is_post = $_SERVER['REQUEST_METHOD'] == 'POST';
		if($is_post) {
			// Attempt Authentication
			$access = validate_access($conn, "pw", hash('sha256', $_POST['password']));
			if(count($access) != 0) {
				//Authentication successful go to index with admin view
				setcookie('tok', get_token($conn, $access));
				header("Location: index.php");
				die();
			} 
		} // END IF POST REQUEST HANDLING

		// Print the page VVV
?>
<!DOCTYPE html>
<html>
	<!-- ALL HEAD CONTENT HERE -->
	<?php head_section();?>

	<body>
		<?php /*$is_admin_mode = admin_mode($conn);*/ ?>
		<!-- FULL SCREEN CONTAINER -->
		<div class="container-fluid">
			<!-- PAGE TITLE -->
			<?php page_title("Admin Mode");?>
			<!-- PAGE CONTENT -->
			<div class="row">
				<!-- MAIN CONTENT -->
				<div class="col-md-7 col-xs-offset-1 col-xs-6 indent">
					<?php 
						//home_page_content(); 
						if(!$is_post) {
							//GET REQUEST RENDERS FORM
							$form = new HTML_element("form");
							$form->attr("method", "POST")->attr("class", "col-xs-12 col-md-offset-4 col-md-4")
								->html("div")->attr("class", "form-group ")
									->html("label")->attr("for", "password")->text("Admin Mode Login")->parent()
									->html("input")->attr("type", "password")->attr("class", "form-control ")->attr("name", "password")->attr("id", "password");
							$form->html("input")->attr("type", "submit")->attr("class", "btn btn-success");

							$form->render();
						} else {
							// POST REQUEST THAT WAS INVALID RENDERS FORM WITH ERROR MESSAGE
							$form = new HTML_element("form");
							$form->attr("method", "POST")->attr("class", "col-xs-12 col-md-offset-4 col-md-4 bg-danger")
								->html("div")->attr("class", "form-group ")
									->html("label")->attr("for", "password")->text("Incorrect password. Please try again.")->parent()
									->html("input")->attr("type", "password")->attr("class", "form-control ")->attr("name", "password")->attr("id", "password");
							$form->html("input")->attr("type", "submit")->attr("class", "btn btn-success");

							$form->render();
						}

					?>
				</div>
				<!-- SIDE MENU CONTENT -->
				<div class="col-xs-offset-1 col-xs-3 col-sm-3 col-md-2 side-menu">
					<?php side_menu(); ?>
				</div>
			</div>
			<!-- FOOTER -->
			<?php footer_section(); ?>
		</div>
	</body>
</html>

<?php 
		} // END IF SSL CONNECTION
?>		
