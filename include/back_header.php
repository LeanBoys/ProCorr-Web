<?php
if ($_SESSION ['bruker']) {} 
else {Header("Location: ../default.php");}
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title><?php echo $page_title; ?></title>
        
        <!-- Meta -->
		<meta http-equiv="content-type" content="text/html; charset=utf-8" /> <!-- Angir karakter encoding for dokumentet -->
        <meta name="viewport" content="width=device-width, initial-scale=1" /> <!-- Angir bredden på siden likt skjermbredden av enheten og zoomnivå når siden først lastet av nettleseren -->
        
        <meta name="description" content="ProCorr AS utfører alt innen korrosjonsbehandling på skip, kraftverk, bruer, industrier m.m" /> <!-- Webside beskrivelse -->
        <meta name="keywords" content="korrosjonsbehandling, sandblåsing, sandsweeping, stål, betong, høytrykkrengjøring, metallisering, sprøytemling, epoxy, uretan " /> <!-- Webside søkeord -->

		<link rel="shortcut icon" href="../bilder/back_favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" href="reset.css" /> <!-- CSS Reset tvinger alle nettlesere til å tilbakestille alle sine stiler til null, dermed unngå vi cross-browser forskjeller -->
		<link rel="stylesheet" href="back_style.css" /> <!-- CSS koden ligger i egen fil -->

	</head>

	<body onload="loadScroll()" onunload="saveScroll()">
		<div id="page-wrapper">
		
			<!-- Header -->
			<div id="header-wrapper">
				<div class="container">
					<header id="page-header">

						<!-- Logo -->
						<a <?php echo ($page == '100') ? '' : 'href="backend.php"';?> tabindex="1" ><img id="logo" src="../bilder/logo.png" alt="procorr as logo"/></a>
						<h1 id="logo-name"><a <?php echo ($page == '100') ? '' : 'href="backend.php"';?>><span>ProCorr AS</span></a></h1>

						<!-- Hoved navigasjon -->
						<nav id="nav">
							<ul class="nav-list">
								<li <?php echo ($page == '100') ? 'class="nav-item current"' : 'class="nav-item"';?>><a <?php echo ($page == '100') ? '' : 'href="backend.php"';?> alt="Tilbake til Rediger meny" tabindex="2">Rediger meny</a></li>
								<li <?php echo ($page == '101') ? 'class="nav-item current"' : 'class="nav-item"';?>><a <?php echo ($page == '101') ? '' : 'href="back_redinnhold.php"';?> alt="Gå til Rediger innhold" tabindex="3">Rediger innhold</a></li>
								<li <?php echo ($page == '102') ? 'class="nav-item current"' : 'class="nav-item"';?>><a <?php echo ($page == '102') ? '' : 'href="back_byttpw.php"';?> alt="Gå til Bytt passord" tabindex="4">Bytt passord</a></li>
								
								<li class="nav-item">
									<form method="POST" >
										<button class="loggut-btn" type="submit" value="Logg ut" name="loggut" id="loggut" tabindex="5">Logg ut</button>
									</form>
								</li>
							</ul>
						</nav>

					</header>
				</div>
			</div>