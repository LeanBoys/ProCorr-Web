<!DOCTYPE HTML>
<html>
	<head>
		<title><?php echo $page_title; ?></title>
        
        <!-- Meta -->
		<meta http-equiv="content-type" content="text/html; charset=utf-8" /> <!-- Angir karakter encoding for dokumentet -->
        <meta name="viewport" content="width=device-width, initial-scale=1" /> <!-- Angir bredden på siden likt skjermbredden av enheten og zoomnivå når siden først lastet av nettleseren -->
        
        <meta name="description" content="ProCorr AS utfører alt innen korrosjonsbehandling på skip, kraftverk, bruer, industrier m.m" /> <!-- Webside beskrivelse -->
        <meta name="keywords" content="korrosjonsbehandling, sandblåsing, sandsweeping, stål, betong, høytrykkrengjøring, metallisering, sprøytemling, epoxy, uretan " /> <!-- Webside søkeord -->

		<link rel="shortcut icon" href="../bilder/favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" href="reset.css" /> <!-- CSS Reset tvinger alle nettlesere til å tilbakestille alle sine stiler til null, dermed unngå vi cross-browser forskjeller -->
		<link rel="stylesheet" href="style.css" /> <!-- CSS koden ligger i egen fil -->

	</head>

	<body onload="loadScroll()" onunload="saveScroll()">
		<div id="page-wrapper">
		
			<!-- Header -->
			<div id="header-wrapper">
				<div class="container">
					<header id="page-header">

						<!-- Logo -->
						<a <?php echo ($page == '1') ? '' : 'href="../procorr/default.html"';?> tabindex="1"><img id="logo" src="../bilder/logo.png" alt="procorr as logo"/></a>
						<h1 id="logo-name"><a <?php echo ($page == '1') ? '' : 'href="../procorr/default.html"';?>><span>ProCorr AS</span></a></h1>

						<!-- Hoved navigasjon -->
						<nav id="nav">
							<ul class="nav-list">
								<?php echo $menuDisplay; ?> 
								
							</ul>
						</nav>

					</header>
				</div>
			</div>
			