<?php $page= '3'; ?>
<?php $page_title = "ProCorr AS - Bestilling";?>
<?php include("connection.php");?>
<?php include("back_functions.php");?>
<?php include("back_header.php");?>

			<!-- Main -->
			<div id="main-wrapper">
				<div class="container" id="linkify">
					<div class="top-pack">
						
						<?php echo $back_minorDisplay; ?>
						
						<div class="maincontent-whole">
							<div id="content">
								<article class="featured">
						
									<section>
										<p class="successMelding"></p>
										<p class="failMelding"></p>
										
										<div class="form">
											<form method="POST" action="../include/bestilling.php">
												<?php
												if ($_SESSION['name']=="") { echo('
												<div class="form-content-left">
													<input accesskey="n" type="text" name="name" id="name" placeholder="Navn" autocomplete="off" required />
												</div>'); } else { echo('
												<div class="form-content-left">
													<input accesskey="n" type="text" name="name" id="name" value="'.$_SESSION['name'].'" autocomplete="off" required />
												</div>'); }
												
												if ($_SESSION['surname']=="") { echo('
												<div class="form-content-right">
													<input accesskey="en" type="text" name="surname" id="surname" placeholder="Etternavn" autocomplete="off" required />
												</div>'); } else { echo('
												<div class="form-content-right">
													<input accesskey="en" type="text" name="surname" id="surname" value="'.$_SESSION['surname'].'" autocomplete="off" required />
												</div>'); }
												
												if ($_SESSION['email']=="") { echo('
												<div class="form-whole">
													<div class="form-left">
														<input accesskey="e" type="email" name="email" id="email" placeholder="Epost" autocomplete="off" required />
													</div>'); } else { echo('
												<div class="form-whole">
													<div class="form-left">
														<input accesskey="e" type="email" name="email" id="email" value="'.$_SESSION['email'].'" autocomplete="off" required />
													</div>'); }
												
												if ($_SESSION['phone']=="") { echo('
													<div class="form-content-right">
														<input accesskey="t" type="number" name="phone" id="phone" placeholder="Telefon" min="10000000" max="999999999999"  required />
													</div>
												</div>'); } else { echo('
													<div class="form-content-right">
														<input accesskey="t" type="number" name="phone" id="phone" value="'.$_SESSION['phone'].'" min="10000000" max="999999999999"  required />
													</div>
												</div>'); }
												
												if ($_SESSION['address']=="") { echo('
												<div class="form-whole">
													<div class="form-left">
														<input accesskey="a" type="text" name="address" id="address" placeholder="Adresse" autocomplete="off" required />
													</div>'); } else { echo('
												<div class="form-whole">
													<div class="form-left">
														<input accesskey="a" type="text" name="address" id="address" value="'.$_SESSION['address'].'" autocomplete="off" required />
													</div>'); }
												
												if ($_SESSION['postcode']=="") { echo('
													<div class="form-right"> 
														<div class="form-left"> 
															<input accesskey="p" type="number" name="postcode" id="postcode" placeholder="Postnummer" min="0001" max="9999" required />
														</div>'); } else { echo('
													<div class="form-right"> 
														<div class="form-left"> 
															<input accesskey="p" type="number" name="postcode" id="postcode" value="'.$_SESSION['postcode'].'" min="0001" max="9999" required />
														</div>'); }
												
												if ($_SESSION['postcity']=="") { echo('
														<div class="form-content-right">
															<input accesskey="ps" type="text" name="postcity" id="postcity" placeholder="Poststed" autocomplete="off" required />
														</div>
													</div>
												</div>'); } else { echo('
														<div class="form-content-right">
															<input accesskey="ps" type="text" name="postcity" id="postcity" value="'.$_SESSION['postcity'].'" autocomplete="off" required />
														</div>
													</div>
												</div>'); }
												
												echo('
												<div class="form-whole">
													<div class="form-left">
														<select accesskey="vp" name="product" id="product" required>
															<option value="" disabled selected>Velg produkt</option>
														</select> 
													</div>');
												
												if ($_SESSION['date']=="") { echo('
													<div class="form-content-right">
														<input accesskey="d" type="date" name="date" id="date" value="'.date("Y-m-d").'" min="'.date("Y-m-d").'" required />
													</div>
												</div>'); } else { echo('
													<div class="form-content-right">
														<input accesskey="d" type="date" name="date" id="date" value="'.$_SESSION['date'].'" min="'.date("Y-m-d").'" required />
													</div>
												</div>'); }
												
												if ($_SESSION['message']=="") { echo('
												<div class="content-whole">
													<textarea accesskey="m" name="message" id="message" placeholder="Melding" required></textarea>
												</div>'); } else { echo('
												<div class="content-whole">
													<textarea accesskey="m" name="message" id="message" required>'.$_SESSION['message'].'</textarea>
												</div>'); }
												?>
												
												<button type="submit" value="Send" name="sendbestilling" id="sendbestilling" disabled>Send</button>
											</form>
										</div>
									</section>
								</article>
							</div>
						</div>
						
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
								<form method="POST" class="nada" action="back_bestilling.php">
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