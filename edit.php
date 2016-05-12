<?php 
	include 'section.php';
	$note = null;
	$is_admin_mode = admin($conn, $note);
	$page = $_REQUEST['page'];
	if(!$is_admin_mode || (is_null($page) && $_SERVER['REQUEST_METHOD'] != 'POST')) {
		header("Location: index.php");
		die();
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		$handle = fopen($_REQUEST['filename'], 'w') or die("unable to update: error opening file");
		if(fwrite($handle, $_REQUEST['new_content']) === FALSE) {
			echo $_REQUEST['filename'];
			die("unable to update: did not write to file. SSH to server and resolve issue");
		}
		fclose($handle);
	}
?>
<!DOCTYPE html>
<html>
	<!-- ALL HEAD CONTENT HERE -->
	<?php head_section($is_admin_mode);?>

	<body>
		<!-- FULL SCREEN CONTAINER -->
		<div class="container-fluid">
			<!-- PAGE TITLE -->
			<?php page_title("Edit", ucwords(str_replace("_", " ", $page)), $is_admin_mode);?>
			<!-- PAGE CONTENT -->
			<div class="row">
				<!-- MAIN CONTENT -->
				<div class="col-xs-offset-1 col-xs-10 ">
					<form id="edit" method="POST" action="edit.php">
						<?php 
							if($_SERVER['REQUEST_METHOD'] == 'POST') {
								echo '<p class="lead text-success">Successfully Updated Content!</p>'; 
							}
						?>
						<p class="lead"> Content from <?php echo $page." ("; echo "<a href='". $page . ".php'>Return to page view</a>)"; ?>:</p>
						<?php 
							$filename = "";
							if($_SERVER['REQUEST_METHOD'] != 'POST') {
								$stmt = $conn->prepare("SELECT filename FROM content WHERE pagename=?");
								$stmt->bind_param("s", $page);
								$stmt->execute();
								$stmt->bind_result($filename);
								if(!$stmt->fetch()) {
									die("FILENAME ". $filename . " NOT FOUND");
								}
							} else {
								$filename = $_REQUEST['filename'];
							}
						?>
						<code><textarea class="form-control" rows="20" name="new_content"><?php include $filename;?></textarea></code>
						<?php
							echo "<input type='hidden' name='filename' value='{$filename}'/>";
							echo "<input type='hidden' name='page' value='{$page}'/>";
						?>
						<div class="form-group">
							<button type="submit" class="btn btn-primary form-control">Save Changes</button>
						</div>
						<div class="form-group">
							<a class="form-control btn btn-default" href="index.php">Cancel</a>
						</div>
					</form>
					<hr style="border-color: black">
					<p class="lead">Preview:</p>
					<hr style="border-color: black">
					
					<?php if($is_admin_mode) {$note->render();}?>
				</div>
			
			</div>
			<div class="row">
				<div class="col-md-7 col-xs-offset-1 col-xs-11 ">
					<div id="preview">
						<?php include $filename; ?>
					</div>
				</div>
			</div>
			<!-- FOOTER -->
			<?php 
			$this_page =  strlen($_SERVER['QUERY_STRING']) ? basename($_SERVER['PHP_SELF'])."?".$_SERVER['QUERY_STRING'] : basename($_SERVER['PHP_SELF']);
			footer_section($this_page); ?>
		</div>
	</body>
</html>