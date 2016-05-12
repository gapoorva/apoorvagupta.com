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
			<?php page_title("Apoorva Gupta", "What I Do", $is_admin_mode);?>
			<!-- PAGE CONTENT -->
			<div class="row">
				<!-- MAIN CONTENT -->
				<div class="col-md-7 col-xs-offset-1 col-xs-6 indent">
					<?php get_content($conn, "what_i_do", $is_admin_mode); ?>
					<?php if($is_admin_mode) {$note->render();}?>
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