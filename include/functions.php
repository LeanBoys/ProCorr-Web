<?php //Hent elementer

//Session for forms
$_SESSION['name'] = "";
$_SESSION['email'] = "";
$_SESSION['subject'] = "";
$_SESSION['message'] = "";
$_SESSION['surname'] = "";
$_SESSION['phone'] = "";
$_SESSION['address'] = "";
$_SESSION['postcode'] = "";
$_SESSION['postcity'] = "";
$_SESSION['date'] = "";
$successMelding = "";
$failMelding = "";

// real escape string funcsjon som kan brukes for alle inputer
if(!function_exists('resc')) {
	function resc($db, $strn) {
		$strn = $db->real_escape_string($strn);
		return $strn;
	}
}

//sjekker om string bare er whitespace (eller tom, ekstra sikkerhet til "required") 
if(!function_exists('ertom')) {
	function ertom($strn) {
		return (ctype_space($strn) || $strn == "");
	}
}

//Meny elementer
$navsqlCommand = "SELECT * FROM meny ORDER BY menynr ASC"; 
$navquery = mysqli_query($db, $navsqlCommand) or die (mysqli_error()); 

$menuDisplay = '';
while ($row = mysqli_fetch_array($navquery)) { 

	$page_id = $row["menynr"];
	$page_text = $row["tekst"];
	$page_link = $row["side"];
	$tabindex = $row['rekke']+1;
	$page_tooltip = $row["tooltip"];
	$page_alt = $row["alt"];

	if ($page_id == $page ) {
  		$current = 'current'; 
		$link = '';
	} else {
    	$current = ''; 
		$link = 'href="../procorr/'.$page_link.'"';
	}
		
	$menuDisplay .= '
								<li class="nav-item '.$current.'"><a '.$link.' alt="'.$page_alt.'" title="'.$page_tooltip.'" tabindex="'.$tabindex.'">'.$page_text.'</a></li>';
}
mysqli_free_result($navquery); 



//Slide elementer
$slidesqlCommand = "SELECT * FROM bilde WHERE type='slide' ORDER BY bildenr ASC"; 
$slidequery = mysqli_query($db, $slidesqlCommand) or die (mysqli_error()); 

$slideDisplay = '';
while ($row = mysqli_fetch_array($slidequery)) { 

	$slide_ingress = $row["tittel"];
	$slide_text = $row["ingress"];
	$slide_location= $row["hvor"];
	$slide_alt= $row["alt"];
	
	$slideDisplay .= '
							<div class="slide fade">
								<div class="captions">
									<span class="caption-line-1">'.$slide_ingress.'</span>
									<span class="caption-line-2">'.$slide_text.'</span>
								</div>
								<img src="../bilder/'.$slide_location.'" alt="'.$slide_alt.'"/>
							</div>' ."\n";
}
mysqli_free_result($slidequery); 



//Innhold elementer
$contentsqlCommand = "SELECT innhold.tittel, innhold.ingress, innhold.tekst, bilde.hvor as bilderhvor, bilde.alt as bilderalt, bilde.bredde, bilde.høyde, dokument.hvor as dokumenterhvor, dokument.tekst as dokumentertekst
FROM innhold 
LEFT JOIN innholdbilde ON innhold.innholdnr = innholdbilde.innholdnr 
LEFT JOIN bilde ON bilde.bildenr = innholdbilde.bildenr
LEFT JOIN innholddok ON innhold.innholdnr = innholddok.innholdnr 
LEFT JOIN dokument ON dokument.dokumentnr = innholddok.dokumentnr
WHERE innhold.menynr = '$page' AND innhold.type = 'general' ORDER BY innhold.rekke ASC";

$contentquery = mysqli_query($db, $contentsqlCommand) or die (mysqli_error()); 

$contentDisplay = '';
while ($row = mysqli_fetch_array($contentquery)) { 
	
	$fulltekst = $row['tekst'];
	$vistekst = str_replace("\n","</p>\n<p>",$fulltekst);
	
	$width = $row['bredde'];
	if($width>900) {
		$style = 'width: 100%; height: 100%;';
	} else {
		$style = '';
	}
		
	$wherepic = '<img class="bilder" src="../bilder/'.$row['bilderhvor'].'" alt="'.$row['bilderalt'].'" width="'.$row['bredde'].'" height="'.$row['høyde'].'" style="'.$style.'"/>';
	$wheredok = '<a class="dokumenter" href="../dokumenter/'.$row['dokumenterhvor'].'" target="_blank" download>'.$row['dokumentertekst'].'</a>';
	$bildelengde = strlen($wherepic);
	$doklengde = strlen($wheredok);
	
	if ($bildelengde<74) {
		$wherepic = "";
	}
	
	if ($doklengde<74) {
		$wheredok = "";
	} 
	
	$contentDisplay .= '		
						<div class="maincontent-whole">
							<div id="content">
								<article class="featured">
									<header>
										<h2>'.$row['tittel'].'</h2>
										<span class="byline">'.$row['ingress'].'</span>
									</header>
									
										<p>'.$vistekst.'</p>

									<div class="content-whole">
										<section>
											'.$wherepic.'
											'.$wheredok.'
										</section>
									</div>

								</article>
							</div>
						</div>' ."\n";
}
mysqli_free_result($contentquery); 



//Special elementer
$specialsqlCommand = "SELECT * FROM innhold WHERE menynr = '$page' AND type='main'";
$specialquery = mysqli_query($db, $specialsqlCommand) or die (mysqli_error());

$imagesqlCommand = "SELECT * FROM bilde WHERE type='main' ORDER BY bildenr ASC";
$imagequery = mysqli_query($db, $imagesqlCommand) or die (mysqli_error());
$imageDisplay = [];
while ($row = mysqli_fetch_array($imagequery)) {
		$imageDisplay[] = '<section> 
											<a class="image image-fit"><img src="../bilder/'.$row['hvor'].'" alt="'.$row['alt'].'"/></a>
											<h3>'.$row['tittel'].'</h3>
											<p>'.$row['ingress'].'</p>
										</section>';
}

$sjefsqlCommand = "SELECT * FROM kontakt LEFT JOIN bilde ON bilde.bildenr = kontakt.bildenr WHERE bilde.type = 'portrait' ORDER BY kontaktnr ASC";
$sjefquery = mysqli_query($db, $sjefsqlCommand) or die (mysqli_error());
$sjefDisplay = [];
while ($row = mysqli_fetch_array($sjefquery)) {
	$sjefDisplay[] = '<section>
												<h1 class="img-circle"><img src="../bilder/'.$row['hvor'].'" alt="'.$row['alt'].'" /></h1>
												<h2>'.$row['tittel'].'</h2>
												<h3>'.$row['ingress'].'</h3>
												<a href="mailto:'.$row['ePost'].'"><h4>'.$row['ePost'].'</h4></a>
												<a href="tel:'.$row['tlf'].'"><h4>'.$row['tlf'].'</h4></a>
											</section>';
}

$homeDispley = '';
$omDispley = '';
$minorDisplay = '';
while ($row = $specialquery->fetch_assoc()) {
	$fulltekst = $row['tekst'];
	$vistekst = str_replace("\n","</p>\n<p>",$fulltekst);
	
	$homeDispley = '		
						<div class="maincontent-whole">
							<div id="content">
								<article class="featured">
									<header>
										<h2>'.$row['tittel'].'</h2>
										<span class="byline">'.$row['ingress'].'</span>
									</header>
									
										<p>'.$vistekst.'</p>
										
									<div class="content-center">
										<div class="content-left">
											'.$imageDisplay[0].'
										</div>

										<div class="content-right">
											'.$imageDisplay[1].'
										</div>
									</div>
									
									<div class="content-center">
										<div class="content-left">
											'.$imageDisplay[2].'
										</div>

										<div class="content-right">
											'.$imageDisplay[3].'
										</div>
									</div>

								</article>
							</div>
						</div>' ."\n";
	
	$omDispley = '
						<div class="maincontent-whole">
							<div id="content">
								<article class="featured">
									<header>
										<h2>'.$row['tittel'].'</h2>
										<span class="byline">'.$row['ingress'].'</span>
									</header>

									<div class="roundit">
										<div class="circle-left">
											'.$sjefDisplay[0].'
										</div>

										<div class="circle-center">
											'.$sjefDisplay[1].'
										</div>

										<div class="circle-right">
											'.$sjefDisplay[2].'
										</div>
									</div>

										<p>'.$vistekst.'</p>

								</article>
							</div>
						</div>';
	
	$minorDisplay = '<div class="maincontent-whole">
							<div id="content">
								<article class="featured">
									<header>
										<h2>'.$row['tittel'].'</h2>
										<span class="byline">'.$row['ingress'].'</span>
									</header>

										<p>'.$vistekst.'</p>

								</article>
							</div>
						</div>';
}
mysqli_free_result($specialquery);
mysqli_free_result($imagequery);
mysqli_free_result($sjefquery);



//Footer elementer
$contactsqlCommand = "SELECT * FROM kontakt WHERE kontaktnr=1";
$contactquery = mysqli_query($db, $contactsqlCommand) or die (mysqli_error());

$footerDispley = '';
while ($row = $contactquery->fetch_assoc()) {
	
	$footerDispley = '<h1>Besøksadresse: </h1>
						<a href="https://www.google.no/maps/place/'.$row['adresse'].','.$row['postnr'].' '.$row['poststed'].'" target="_blank">
						<h2>'.$row['adresse'].', '.$row['postnr'].' '.$row['poststed'].'</h2>
						</a>

						<h1>Telefon: </h1>
						<a href="tel:0047'.$row['tlf'].'"><h2>'.$row['tlf'].'</h2></a>

						<h1>E-Post: </h1>
						<a href="mailto:'.$row['ePost'].'"><h2>'.$row['ePost'].'</h2></a>' ."\n";
}
mysqli_free_result($contactquery);


//Melding
if (isset($_POST['sendmelding']) and $_POST['sendmelding'] == "Send") {

	//E-mail config
	//$EmailTo = "admin@procorr.no";
	$EmailTo = "nevel_y@yahoo.no";
	$Name = $_POST['name'];
	$Email = $_POST['email'];
	$Subject = $_POST['subject'];
	$Message = $_POST['message'];
	$Header = "From: '$Email'";
		
  	// prepare email body text
	$Body = "";
	$Body .= "Navn:  ";
	$Body .= $Name;
	$Body .= "\n";
	$Body .= "E-post:  ";
	$Body .= $Email;
	$Body .= "\n";
	$Body .= "Emne:  ";
	$Body .= $Subject;
	$Body .= "\n";
	$Body .= "Melding:  ";
	$Body .= $Message;
	$Body .= "\n";

	//Send email
	//sjekker om string bare er whitespace (ekstra sikkerhet til "required")
	if (ertom($Name) or ertom($Email) or ertom($Subject) or ertom($Message)) {
		$_SESSION['name'] = $Name;
		$_SESSION['email'] = $Email;
		$_SESSION['subject'] = $Subject;
		$_SESSION['message'] = $Message;
		$failMelding = "Et eller flere felt er tom."; 
	} else {
		$success = mail($EmailTo, $Subject, $Body, $Header);
		if ($success) {
			$successMelding = "Takk for din henvendelse, vi vil ta kontakt med deg så snart som mulig.";
			$_SESSION['name'] = "";
			$_SESSION['email'] = "";
			$_SESSION['subject'] = "";
			$_SESSION['message'] = "";
		} else {
			$_SESSION['name'] = $Name;
			$_SESSION['email'] = $Email;
			$_SESSION['subject'] = $Subject;
			$_SESSION['message'] = $Message;
			$failMelding = "Det har oppstått en feil, vennligst prøv igjen senere.";
		}
	}
}



//Produkt elementer
$productsqlCommand = "SELECT * FROM tjeneste";
$productquery = mysqli_query($db, $productsqlCommand) or die (mysqli_error());

$productDispley = '';
while ($row = $productquery->fetch_assoc()) {
	
	$product_id = $row["tjenestenr"];
	$product_text = $row["tjeneste"];
	
	$productDispley .= '				
															<option value="'.$product_id.'">'.$product_text.'</option>';
}
mysqli_free_result($productquery);



//Bestilling
if (isset($_POST['sendbestilling']) and $_POST['sendbestilling'] == "Send") {
											
	$name = resc($db, $_POST['name']);
	$surname = resc($db, $_POST['surname']);
	$email = resc($db, $_POST['email']);
	$phone = resc($db, $_POST['phone']);
	$address = resc($db, $_POST['address']);
	$postcode = resc($db, $_POST['postcode']);
	$postcity = resc($db, $_POST['postcity']);
	$product = resc($db, $_POST['product']);
	$date = resc($db, $_POST['date']);
	$message = resc($db, $_POST['message']);
	
	list($y, $m, $d) = explode('-', $date);
	$postcheck = $db->query("SELECT * FROM postkatalog WHERE postnr=".$postcode);  
	$row_cnt = $postcheck->num_rows;
	
	//Lagre bestilling
	//sjekker om string bare er whitespace (ekstra sikkerhet til "required")
	if (ertom($name) or ertom($surname) or ertom($email) or ertom($phone) or ertom($address) or ertom($postcode) or ertom($postcity) or ertom($product) or ertom($date) or ertom($message)) {
		$_SESSION['name'] = $name;
		$_SESSION['surname'] = $surname;
		$_SESSION['email'] = $email;
		$_SESSION['phone'] = $phone;
		$_SESSION['address'] = $address;
		$_SESSION['postcode'] = $postcode;
		$_SESSION['postcity'] = $postcity;
		$_SESSION['date'] = $date;
		$_SESSION['message'] = $message;
		$failMelding = "Et eller flere felt er tom.";
		
	} else if (!checkdate($m, $d, $y)) {
		$_SESSION['name'] = $name;
		$_SESSION['surname'] = $surname;
		$_SESSION['email'] = $email;
		$_SESSION['phone'] = $phone;
		$_SESSION['address'] = $address;
		$_SESSION['postcode'] = $postcode;
		$_SESSION['postcity'] = $postcity;
		$_SESSION['date'] = $date;
		$_SESSION['message'] = $message;
		$failMelding = "Feil dato format. Vennligst bruk riktig format, eks. 2017-05-22 (år-måned-dag)";
		
	} else if ($date < date("Y-m-d")) {
		$_SESSION['name'] = $name;
		$_SESSION['surname'] = $surname;
		$_SESSION['email'] = $email;
		$_SESSION['phone'] = $phone;
		$_SESSION['address'] = $address;
		$_SESSION['postcode'] = $postcode;
		$_SESSION['postcity'] = $postcity;
		$_SESSION['date'] = $date;
		$_SESSION['message'] = $message;
		$failMelding = "Feil dato. Datoen kan ikke være fortid.";
		
	} else if ($row_cnt == 0) {
		$_SESSION['name'] = $name;
		$_SESSION['surname'] = $surname;
		$_SESSION['email'] = $email;
		$_SESSION['phone'] = $phone;
		$_SESSION['address'] = $address;
		$_SESSION['postcode'] = $postcode;
		$_SESSION['postcity'] = $postcity;
		$_SESSION['date'] = $date;
		$_SESSION['message'] = $message;
		$failMelding = "Feil Postnummer. Vennligst bruk riktig postnummer.";
		
	} else {
			
		$maxvalue = $db->query("SELECT MAX(kundenr) AS max FROM kunde");      
		if (!$maxvalue) die($db->error);
		while($row = mysqli_fetch_array($maxvalue, MYSQLI_ASSOC)) {
			$kundenr = $row['max'] + 1;
        } 
		
		$addkundesql = "INSERT INTO kunde VALUES ('$kundenr', '$name', '$surname', '$address', $postcode, '$phone', '$email', '')";
		$addbestillingsql = "INSERT INTO bestilling VALUES ('', '$kundenr', '$date', '', '$product', 'netbestilling', '', '$message' )";
			
		if ($db->query($addkundesql) === TRUE and $db->query($addbestillingsql) === TRUE) {
			$_SESSION['name'] = "";
			$_SESSION['surname'] = "";
			$_SESSION['email'] = "";
			$_SESSION['phone'] = "";
			$_SESSION['address'] = "";
			$_SESSION['postcode'] = "";
			$_SESSION['postcity'] = "";
			$_SESSION['date'] = "";
			$_SESSION['message'] = "";
			$successMelding = "Takk for din henvendelse, vi vil ta kontakt med deg så snart som mulig.";
		} else {
			$_SESSION['name'] = $name;
			$_SESSION['surname'] = $surname;
			$_SESSION['email'] = $email;
			$_SESSION['phone'] = $phone;
			$_SESSION['address'] = $address;
			$_SESSION['postcode'] = $postcode;
			$_SESSION['postcity'] = $postcity;
			$_SESSION['date'] = $date;
			$_SESSION['message'] = $message;
			$failMelding = "Det har oppstått en feil, vennligst prøv igjen senere.";
			echo "Error: " . $addkundesql . "<br>" . $db->error;
			echo "Error: " . $addbestillingsql . "<br>" . $db->error;
		}
	}
}
?>