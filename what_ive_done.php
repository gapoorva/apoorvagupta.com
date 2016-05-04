<?php include 'section.php';?>
<!DOCTYPE html>
<html>
	<!-- ALL HEAD CONTENT HERE -->
	<?php head_section();?>

	<body>
		<?php $is_admin_mode = admin_mode();?>
		<!-- FULL SCREEN CONTAINER -->
		<div class="container-fluid">
			<!-- PAGE TITLE -->
			<?php page_title("Apoorva Gupta", "What I've Done");?>
			<!-- PAGE CONTENT -->
			<div class="row">
				<!-- MAIN CONTENT -->
				<div class="col-md-7 col-xs-offset-1 col-xs-6 indent">
					<?php home_page_content(); ?>
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
