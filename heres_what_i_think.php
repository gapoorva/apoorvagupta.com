<?php 
	include 'section.php';
	$note = null;
	$is_admin_mode = admin($conn, $note);

	$is_search = !is_null($_REQUEST['query']);
	$query_string = "";

	if($is_search) {
		$query_vector = vectorize(remove_punc(remove_tags($_REQUEST['query'])));
		$result_set = array();
		$doc_scores = array();
		if(sizeof($query_vector) == 0) {
			//If query_string contained only stop words it doesn't help us
			//just get the most recent 10 posts
			$recent = $conn->prepare('SELECT `title`, `lead`, `id`, `date` FROM blog ORDER BY ts DESC LIMIT 10');
			$recent->execute();
			$recent->bind_result($r_title, $r_lead, $r_id, $r_date);
			while($recent->fetch()) {
				//simple recent posts. Don't need to do any result grooming
				$result_set[$r_id] = array('title' => $r_title, 'lead' => $r_lead, 'date' => $r_date);
				$doc_scores[$r_id] = 0;
			}
		} else {
			//query vector contains non-trivial words, must extract all matched documents
			$matched = $conn->prepare('SELECT invindex.id, invindex.magnitude, invindex.vector_magnitude, blog.title, blog.lead, blog.date FROM invindex INNER JOIN blog ON invindex.id = blog.id WHERE invindex.word = ?');
			$matched->bind_param('s', $query_word);
			$boolean_match_set = array();
			$query_norm = 0;
			foreach ($query_vector as $query_word => $query_magnitude) {
				//update a rolling sumation of query weights squared
				$query_norm += $query_magnitude*$query_magnitude;
				//get the data for this word of the query
				$matched->execute();
				$matched->bind_result($m_id, $m_magnitude, $m_sumsquared, $m_title, $m_lead, $m_date);
				//iterate through returned results
				while($matched->fetch()) {
					if($boolean_match_set[$m_id] === NULL) { // not in set
						//denominator term of similarty for doc[k]
						$norm = sqrt($m_sumsquared);
						//rolling dot product
						$rolling_dot = $query_magnitude * $m_magnitude;

						$boolean_match_set[$m_id] = array('norm' => $norm, 'dot_product' => $rolling_dot);
						// populate result set 
						$result_set[$m_id] = array('title' => $m_title, 'lead' => $m_lead, 'date' => $m_date);

					} else { // already in set
						//update rolling dot product
						$boolean_match_set[$m_id]['dot_product'] += $m_magnitude*$query_magnitude;
					}
				}
			}
			//denominator term of similarty for query
			$query_norm = sqrt($query_norm);
			
			foreach ($boolean_match_set as $id => $doc) {
				$doc_scores[$id] = $doc['dot_product'] / ($doc['norm'] * $query_norm);
			}
		} // end non-trivial case
		arsort($doc_scores);
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
			<?php page_title("Apoorva Gupta", "Here's What I Think", $is_admin_mode);?>
			<!-- PAGE CONTENT -->
			<div class="row">
				<!-- MAIN CONTENT -->
				<div class="col-md-7 col-xs-offset-1 col-xs-6 indent">
					<?php 
						get_content($conn, "heres_what_i_think", $is_admin_mode); 
					?>
					<br>
					<div class="row">
						<form id="search" method="GET">
							<div class="form-group">
								<input type="text" class="form-control" id="query" name="query" placeholder="Search Blog Posts" <?php echo "value='".$_REQUEST['query']."'";?>>
							</div>
						</form>
					</div>
					<?php
						if($is_search) {
							$counter = 0;
							foreach($doc_scores as $id => $score) {
								if($counter == 10) break;
								echo '<a class="blog-card" href="entry.php?id='.$id.'"><p class="lead"><b><span style="color:#C23B22;">'.$result_set[$id]['title'].'</span> | '.$result_set[$id]['date'].'</b></p><p class="lead"><small>'.$result_set[$id]['lead'].'</small></p></a>';
							}
						}
					?>
					<script type="text/javascript">
						$("#search").submit( 
							function(e) {
								if($("#query").val() == "") { //don't submit empty
									e.preventDefault();
									return false;
								}
								return true;
							}
						);
					</script>
					<div class="content-item">
					<?php
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
							echo '<h2 style="margin-top:0">Recent Posts:</h2>';
							$stmt = $conn->prepare('SELECT `title`, `lead`, `id`, `date` FROM blog ORDER BY ts DESC LIMIT 7');
							$stmt->execute();
							$stmt->bind_result($title, $lead, $id, $date);

							while($stmt->fetch()) {
								echo '<a class="blog-card" href="entry.php?id='.$id.'"><p class="lead"><b>'.$title.' | '.$date.'</b></p><p class="lead"><small>'.$lead.'</small></p></a>';
							}
						}

					?>
					</div>
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