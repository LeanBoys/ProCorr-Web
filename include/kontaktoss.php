<?php $page= '4'; ?>
<?php $page_title = "ProCorr AS - Kontakt oss";?>
<?php include("connection.php");?>
<?php include("functions.php");?>
<?php include("header.php");?>

			<!-- Main -->
			<div id="main-wrapper">
				<div class="container">
					<div class="top-pack">
					
						<?php echo $minorDisplay; ?>
						
						<div class="maincontent-whole">
							<div id="content">
								<article class="featured">
									
									<section>
										<header>
											<span class="byline">Her finner du oss</span>
										</header>
										
										<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2018.4793024279538!2d10.40541431608358!3d59.608382381757295!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x464137f4bfb87315%3A0x28f2c10b1f8bc0ad!2sStorgaten+99%2C+3060+Svelvik!5e0!3m2!1sen!2sno!4v1493329388931" width="1200" height="400" frameborder="0" style="border:0"></iframe>

										<p id="melding"></p>
									</section>

									<section>
										<header>
											<span class="byline">Kontakt oss</span>
											<p class="successMelding"><?php echo ($successMelding); ?></p>
											<p class="failMelding"><?php echo ($failMelding); ?></p>
										</header>

										<div class="form">
											<form method="post" action="../include/kontaktoss.php#melding">
											
												<?php
												if ($_SESSION['name']=="") { echo('
												<input accesskey="n" type="text" name="name" id="name" placeholder="Navn" autocomplete="off" required />');
												} else { echo('
												<input accesskey="n" type="text" name="name" id="name" value="'.$_SESSION['name'].'" autocomplete="off" required />');
												}

												if ($_SESSION['email']=="") { echo('
												<input accesskey="e" type="email" name="email" id="email" placeholder="Epost" autocomplete="off" required />');
												} else { echo('
												<input accesskey="e" type="email" name="email" id="email" value="'.$_SESSION['email'].'" autocomplete="off" required />');
												}

												if ($_SESSION['subject']=="") { echo('
												<input accesskey="t" type="text" name="subject" id="subject" placeholder="Emne" autocomplete="off" required />');
												} else { echo('
												<input accesskey="t" type="text" name="subject" id="subject" value="'.$_SESSION['subject'].'" autocomplete="off" required />');
												}

												if ($_SESSION['message']=="") { echo('
												<textarea accesskey="m" name="message" id="message" placeholder="Melding" required></textarea>');
												} else { echo('
												<textarea accesskey="m" name="message" id="message" required>'.$_SESSION['message'].'</textarea>');
												}
												?>
												
												<button type="submit" value="Send" name="sendmelding" id="sendmelding">Send</button>
												
											</form>
										</div>
									</section>

								</article>
							</div>
						</div>
						
						<?php echo $contentDisplay; ?>
						
					</div>
				</div>
			</div>


<?php include("footer.php");?>