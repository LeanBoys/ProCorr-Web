<?php $page= '102'; ?>
<?php $page_title = "Backend - Bytt passord";?>
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
										<h2>Bytt passord</h2>
									</header>
									<div id="loginform" class="visible">
										<div class="login">
											<section>
												<p class="successMelding"><?php echo ($sMelding); ?></p>
												<p class="failMelding"><?php echo ($fMelding); ?></p>
																								
												<div class="form">
													<form class="form" method="POST" action="back_byttpw.php">
														<input accesskey="p" type="password" name="gampass" id="newpassword" placeholder="Gammelt passord" autofocus required />
														<h1></h1><br>
														<input accesskey="np1" type="password" name="nyttpass1" id="password" placeholder="Nytt passord" required />
														<input accesskey="np2" type="password" name="nyttpass2" id="password" placeholder="Gjenta nytt passord" required />
														<button type="submit" value="Bytt passord" name="byttpass" id="byttpass">Bytt passord</button>
													</form>
												</div>
											</section>
										</div>
									</div>
								</article>
							</div>
						</div>
						
					</div>
				</div>
			</div>

<?php include("back_footer.php");?>