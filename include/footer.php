			<!-- Info -->
			<div id="info-wrapper">
				<div id="page-info" class="container">

					<div class="info-left">
						<section>
							<h2>Nyttige lenker</h2>
							<ul>
								<li><a <?php echo ($page == '2') ? 'class="current"' : 'href="../procorr/omoss.html"';?>>Om Procorr AS</a></li>
								<li><a <?php echo ($page == '4') ? 'class="current"' : 'href="../procorr/kontaktoss.html"';?>>Kontakt Procorr AS</a></li>
								<li><a <?php echo ($page == '99') ? 'class="current"' : 'href="../include/innlogging.php"';?>>Logg inn</a></li>
							</ul>
						</section>
					</div>

					<div class="info-center">
						<section>
							<h2>Samarbeids partnere</h2>
							<ul>
								<li><a href="http://www.achilles.com/">Achilles</a></li>
								<li><a >UVDB</a></li>
							</ul>
						</section>
					</div>

					<div class="info-right">
						<section>
							<h2>Sosiale media</h2>
							<ul class="contact">
								<li><a class="icon twitter"><span class="label">Twitter</span></a></li>
								<li><a class="icon facebook"><span class="label">Facebook</span></a></li>
								<li><a class="icon google-plus"><span class="label">Google+</span></a></li>
							</ul>
						</section>
					</div>

				</div>
			</div>
				
			<!-- Footer -->
			<div id="footer">
				<div class="container">
					<section>
						<?php echo $footerDispley; ?>
					</section>		
				</div>
			</div>
			
		</div>
     
		<script src="main.js"></script>
        <script src="scrollfix.js" type="text/javascript"></script>
        <?php echo ($page == '99') ? "
		<script src=\"form.js\"></script>
        <script type=\"text/javascript\">
			function clickit(){
				document.getElementById('recoverpw_btn').click();
			}
			$print
		</script>
        " : '<script src="linkify.js" type="text/javascript"></script>';?>
        
	</body>
</html>