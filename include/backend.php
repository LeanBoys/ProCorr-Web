<?php $page= '100'; ?>
<?php $page_title = "Backend - Rediger meny";?>
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
										<h2>Rediger meny</h2>
										<?php echo $tilgangMelding; ?> 
									</header>
										<?php echo $redmenuDisplay; ?> 
									<div class="lastrednav-whole">
										<form method="POST">
											<button class="redigerbtnadd" type="submit" name="leggtil" value="leggtil" id="leggtil">&plus;</button>
										</form>
									</div>
									
								</article>
							</div>
						</div>
						
					</div>
				</div>
			</div>

<?php include("back_footer.php");?>