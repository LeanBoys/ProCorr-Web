<?php $page= '101'; ?>
<?php $page_title = "Backend - Rediger innhold";?>
<?php include("connection.php");?>
<?php include("back_functions.php");?>
<?php include("back_header.php");?>
			
			<!-- Main -->
			<div id="main-wrapper">
				<div class="container">
					<div class="top-pack">
				
						<div class="maincontent-whole">
							<div id="content">
								<article class="featured">
									<header>
										<h2>Rediger innhold</h2>
									</header>
									<?php echo $redinnholdDisplay; ?> 
									
									<div class="lastredinnhold-whole">
									</div>
								</article>
							</div>
						</div>
						
					</div>
				</div>
			</div>

<?php include("back_footer.php");?>