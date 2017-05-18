<?php $page= '2'; ?>
<?php $page_title = "ProCorr AS - Om oss";?>
<?php include("connection.php");?>
<?php include("back_functions.php");?>
<?php include("back_header.php");?>

			<!-- Main -->
			<div id="main-wrapper">
				<div class="container" id="linkify">
					<div class="top-pack">			
						<?php echo $back_omDispley; ?>
						
						<?php echo $back_contentDisplay; ?>
					
						<form method="POST">
							<div id="addroll" class="hidden">
								<div class="content-whole"> <!-- Holder innholdet i midten *(i midten på mobil-enheter) -->
									<div class="add">
										<span>
											<button class="hiddenaddbtn" type="submit" name="addinnhold" value="addinnhold" id="addinnhold" onmouseover="addin()" onmouseout="addout()">
												<img id="img" src="../bilder/back-add1.png"/>
											</button>
										</span>
									</div>
								</div>
							</div>
						</form>

						<div id="popup" class="hidden">
							<div class="popup-container">
								<?php echo $visbilder; ?> 
								<form method="POST" class="nada" action="back_omoss.php">
									<button id="closeit" type="submit" class="popup-close">X</button>
								</form> 
								<div class="return-whole">
									<p class="successMelding"><?php echo ($sMelding); ?></p> 
									<p class="failMelding"><?php echo ($fMelding); ?></p>  
									<?php echo $uppic; ?> 
								</div> 
							</div>
						</div>
						
					</div>
				</div>
			</div>

<?php include("back_footer.php");?>