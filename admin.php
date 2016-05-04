<?php include 'section.php';?>
<!DOCTYPE html>

<html>
	<!-- ALL HEAD CONTENT HERE -->
	<?php head_section();?>
	<body>
		<!-- FULL SCREEN CONTAINER -->
		<div class="container-fluid">
			<!-- PAGE TITLE -->
			<?php page_title("Admin Token Retrieval");?>
			<!-- PAGE CONTENT -->
			<div class="row">
				<!-- MAIN CONTENT -->
				<?php
					if($_SERVER['REQUEST_METHOD'] == "GET") {

						$form = new HTML_element("form");
						$form->attr("method", "POST")->attr("class", "col-xs-offset-1 col-xs-10 col-sm-offset-4 col-sm-4 col-md-offset-5 col-md-2")
							->html("div")->attr("class", "form-group ")
								->html("label")->attr("for", "password")->text("Please enter Admin password to receive edit token.")->parent()
								->html("input")->attr("type", "password")->attr("class", "form-control ")->attr("name", "password")->attr("id", "password");
						$form->html("input")->attr("type", "submit")->attr("class", "btn btn-success");

						$form->render();
					} else {

						$access = validate_access($conn, "pw", hash('sha256', $_POST['password']));

						if(count($access) != 0) {

							$newtok = hash('sha256', $access[0]+$access[1]+strval($access[2]));

							update_access($conn, "tok", $newtok, "s");
							update_access($conn, "ts", time(), "i");

							$result = new HTML_element("div");
							$result->attr("class", "lead col-xs-offset-1 col-xs-10")
								->html("p")->text("Edit token:")->parent()
								->html("p")->text($newtok)->parent()
								->html("p")->html("a")->attr("href", "index.php?edit=".$newtok)->text("Continue to admin view");
							$result->render();
							
						} else {

							$form = new HTML_element("form");
							$form->attr("method", "POST")->attr("class", "col-xs-offset-1 col-xs-10 col-sm-offset-4 col-sm-4 col-md-offset-5 col-md-2 bg-danger")
								->html("div")->attr("class", "form-group ")
									->html("label")->attr("for", "password")->text("Incorrect password. Please try again.")->parent()
									->html("input")->attr("type", "password")->attr("class", "form-control ")->attr("name", "password")->attr("id", "password");
							$form->html("input")->attr("type", "submit")->attr("class", "btn btn-success");

							$form->render();
						}

						// $pw = ;
						// $stmt = $conn->prepare("SELECT tok FROM access WHERE pw=?");
						// $stmt->bind_param("s", $pw);
						// $stmt->execute();
						// $stmt->bind_result($tok);
						// if($stmt->fetch()) {
						// 	$result = new HTML_element("div");
						// 	$result->attr("class", "lead col-xs-offset-1 col-xs-10")
						// 		->html("p")->text("Edit token:")->parent()
						// 		->html("p")->text($tok);
						// 	$result->render();

						// 	$stmt->close();

						// 	$newtok = hash('sha256', $pw+$tok);
							
						// 	$update = "UPDATE access set tok='".$newtok."' WHERE tok='".$tok."'";

						// 	echo $newtok. "<br>". $update . "<br>";

						// 	if($settokstmt = $conn->query($update)) {
						// 		echo "success";
						// 	} else {
						// 		echo "FATAL ERROR";
						// 	}
						// }

					}
				?>
				
			</div>
			<!-- FOOTER -->
			<?php footer_section(); ?>
		</div>
	</body>
</html>
