<?php $page= '1'; ?>
<?php $page_title = "ProCorr AS - Forsiden";?>
<?php include("connection.php");?>
<?php include("functions.php");?>
<?php include("header.php");?>

			<!-- Banner for Slide -->
			<div id="banner-wrapper">
				<div class="banner-container">
					<div id="banner">
						<div class="slide-view">
							<div class="nav-next" onclick="plusDivs(1)"></div>
							<div class="nav-previous" onclick="plusDivs(-1)"></div>
							
							<?php echo $slideDisplay; ?>
								
						</div>
					</div>
				</div>
			</div>
			
			<!-- Main -->
			<div id="main-wrapper">
				<div class="container" id="linkify">
				
					<?php echo $homeDispley; ?>
					
					<?php echo $contentDisplay; ?>
					
				</div>
			</div>

<?php include("footer.php");?>