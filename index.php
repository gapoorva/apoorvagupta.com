<?php 
	include 'section.php';
	$note = null;
	$is_admin_mode = admin($conn, $note);
?>
<!DOCTYPE html>
<html>
	<!-- ALL HEAD CONTENT HERE -->
	<?php head_section();?>

	<body>
		<!-- FULL SCREEN CONTAINER -->
		<div class="container-fluid">
			<!-- PAGE TITLE -->
			<?php page_title("Apoorva Gupta");?>
			<!-- PAGE CONTENT -->
			<div class="row">
				<!-- MAIN CONTENT -->
				<div class="col-md-7 col-xs-offset-1 col-xs-6 indent">
					<?php home_page_content(); ?>
					<?php if($is_admin_mode) {$note->render();}?>
				</div>
				<!-- SIDE MENU CONTENT -->
				<div class="col-xs-offset-1 col-xs-3 col-sm-3 col-md-2 side-menu">
					<?php side_menu(TRUE); ?>
				</div>
			</div>
			<!-- FOOTER -->
			<?php footer_section(); ?>
		</div>
	</body>
</html>

