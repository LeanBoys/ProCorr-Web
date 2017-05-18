<?php $page= '99'; ?>
<?php $page_title = "ProCorr AS - Innlogging";?>
<?php include("connection.php");?>
<?php include("back_functions.php");?>
<?php include("functions.php");?>
<?php include("header.php");?>

			<!-- Main -->
			<div id="main-wrapper">
				<div class="container">
					<div class="top-pack">

						<div class="maincontent-whole">
							<div id="content">
								<article class="featured">

									<div id="loginform" class="visible">
										<div class="login">
											<section>
												<h2>Logg inn</h2>
												<p class="successMelding"><?php echo ($sMelding); ?></p>
												<p class="failMelding"><?php echo ($fMelding); ?></p>
												
												<div class="form">
													<form method="POST" action="innlogging.php">
														<?php
														if ($_SESSION['logginnnavn']=="") { echo('
														<input accesskey="b" type="text" name="bruker" id="user" placeholder="Brukernavn" autofocus autocomplete="off" required />');
														} else { echo('
														<input accesskey="b" type="text" name="bruker" id="user" value="'.$_SESSION['logginnnavn'].'" autofocus autocomplete="off" required />');
														}
														?>
														<input accesskey="p" type="password" name="passord" id="password" placeholder="Passord" required />
                                        				<button type="submit" value="Logg inn" name="logginn" id="login">Logg inn</button>
													</form>

													<div class="options">
														<button value="Glemt passord?" id="recoverpw_btn">Glemt passord?</button>
													</div>
												</div>

											</section>
										</div>
									</div>

									<div id="recoverpwform" class="hidden">
										<div class="recoverpw"> <!-- Glemt passord form -->
											<section>
												<h2>Glemt passord</h2>
												<p class="successMelding"><?php echo ($sMelding); ?></p>
												<p class="failMelding"><?php echo ($fMelding); ?></p>
												
												<div class="form">
													<form method="POST" action="innlogging.php">
														<input type="text" name="recoverpassword" id="recoverpassword" placeholder="Brukernavn" autocomplete="off" required />
														<button type="submit" value="Motta passord" name="mottapass" id="mottapass">Motta passord</button>
													</form>

													<div class="options">
														<button value="Tilbake" id="back_btn1" onClick="window.location.href=window.location.href">Tilbake</button>
													</div>
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


<?php include("footer.php");?>