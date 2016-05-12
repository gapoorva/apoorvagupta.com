<?php 
	include 'section.php';
	$note = null;
	$is_admin_mode = admin($conn, $note);
?>
<!DOCTYPE html>
<html>
	<!-- ALL HEAD CONTENT HERE -->
	<?php head_section($is_admin_mode);?>

	<body>
		<!-- FULL SCREEN CONTAINER -->
		<div class="container-fluid">
			<!-- PAGE TITLE -->
			<?php page_title("Apoorva Gupta", "Here's What I Think", $is_admin_mode);?>
			<!-- PAGE CONTENT -->
			<div class="row">
				<!-- MAIN CONTENT -->
				<div class="col-md-7 col-xs-offset-1 col-xs-6 indent">
					<?php 
						get_content($conn, "heres_what_i_think", $is_admin_mode); 
						echo '<hr style="border-color:black">';
						if($is_admin_mode) {
							$note->render();

							echo "<p class='lead'><a href='entry.php?mode=create' class='btn btn-primary'> Create New Journal Entry</a></p>";
							/*
							create table blog (title varchar(255), id varchar(255), date varchar(40), lead varchar(200));
							*/

							$stmt = $conn->prepare("SELECT `title`, `id`, `date` FROM blog ORDER BY ts DESC");
							$stmt->execute();
							$stmt->bind_result($title, $id, $date);

							while($stmt->fetch()) {
								echo '<a href="entry.php?id='.$id.'">'.$title.'</a> ['.$date.']<br>';
							}
						} else {
							$stmt = $conn->prepare('SELECT `title`, `lead`, `id`, `date` FROM blog ORDER BY ts DESC');
							$stmt->execute();
							$stmt->bind_result($title, $lead, $id, $date);

							while($stmt->fetch()) {
								echo '<a class="blog-card" href="entry.php?id='.$id.'"><p class="lead"><b>'.$title.' | '.$date.'</b></p><p class="lead"><small>'.$lead.'</small></p></a>';
							}
						}

					?>

				</div>
				<!-- SIDE MENU CONTENT -->
				<div class="col-xs-offset-1 col-xs-3 col-sm-3 col-md-2 side-menu">
					<?php side_menu(false, $is_admin_mode); ?>
				</div>
			</div>
			<!-- FOOTER -->
			<?php 
			$this_page =  strlen($_SERVER['QUERY_STRING']) ? basename($_SERVER['PHP_SELF'])."?".$_SERVER['QUERY_STRING'] : basename($_SERVER['PHP_SELF']);
			footer_section($this_page); ?>
		</div>
	</body>
</html>