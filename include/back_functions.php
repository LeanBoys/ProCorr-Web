<?php //Brukere

session_start();
$_SESSION['nybruker']="";
$_SESSION['useremail']="";
$_SESSION['logginnnavn']="";

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

$sMelding = "";
$fMelding = "";
$tilgangMelding = "";
$uppic = "";

// mappe tilgang
$bilderDir = '../bilder';
$dokumenterDir = '../dokumenter';
$procorrDir = '../procorr';
$includeDir = '../include';

if (is_dir($bilderDir) && is_writable($bilderDir)){
} else{
	$tilgangMelding .= "<p class=\"failMelding\">Du har begrenset tilgang til mappen \"Bilder\".</p>\n";
}

if (is_dir($dokumenterDir) && is_writable($dokumenterDir)){
} else{
	$tilgangMelding .= "<p class=\"failMelding\">Du har begrenset tilgang til mappen \"Dokumenter\".</p>\n";
}

if (is_dir($procorrDir) && is_writable($procorrDir)){
} else{
	$tilgangMelding .= "<p class=\"failMelding\">Du har begrenset tilgang til mappen \"ProCorr\".</p>\n";
}

if (is_dir($includeDir) && is_writable($includeDir)){
} else{
	$tilgangMelding .= "<p class=\"failMelding\">Du har begrenset tilgang til mappen \"Include\".</p>\n";
}

//Brukere innlogging
if (isset($_POST['logginn']) and $_POST['logginn'] == "Logg inn") {
		if (ertom($_POST['bruker']) or ertom($_POST['passord'])) {
			$fMelding = "Angi brukernavn og passord før du forsøker å logge inn.";
		} else {
    $navn = resc($db, $_POST['bruker']);
    // hvis det er mindre eller lik 5 forsøk
    $antfors=attempts($db, $navn);
    if ($antfors <= 5) {
		$sql = "select * from bruker where brukernavn='" . $navn . "'";
		$result = $db->query($sql);
		if ($row=$result->fetch_assoc()) {
			if ($row['passord']==hash('sha512', $_POST['passord'].$salt)) {
				$_SESSION['bruker'] = $row['brukernavn'];
				$sql = "UPDATE bruker SET first_failed_login=null, failed_login_count = 0 WHERE brukernavn = '". $navn ."';";
				$db->query($sql);
				header("Location: backend.php");
				 
			} else {   
				$_Session['bruker'] = "";
				$_SESSION['logginnnavn']=$navn;
				$fMelding = "Finner ikke Brukernavn/Passord";
        if ($antfors==5) $fMelding .= ". Dette var siste forsøk. Vent 15 min";
			}
		}
		else {
			$_SESSION['bruker'] = "";
			$_SESSION['logginnnavn']=$navn;
			$fMelding = "Finner ikke Brukernavn/Passord";
      if ($antfors==5) $fMelding .= " Dette var siste forsøk. Vent 15 min";
		} 
		} else {
    $fMelding = "For mange forsøk på dette brukernavnet, vent ". getWait($db, $navn) . " minutter";
    }
    
	}
}

 function attempts($db, $navn) {
//en form for automatisering av å resette tilgang til å prøve passord (ikke så tidskrevende når det ikke er høy aktivitet/mange attempt-tables på serveren
// endre tallet i INTERVAL 15 MINUTE for å endre hvor lang tid brukeren må vente for å prøve på nytt 
    $db->query("UPDATE bruker SET first_failed_login=null, failed_login_count=0 WHERE (first_failed_login + INTERVAL 15 MINUTE) < NOW()");
    $sql = "select * from bruker where (brukernavn = '". $navn . "');";

   $res = $db->query($sql);
   if ($row=$res->fetch_assoc()) {
    
    //setter verdi hvis det er første feilet login
    if ($row['failed_login_count'] <= 0) {
      $sql = "UPDATE bruker SET failed_login_count = 1, first_failed_login=NOW() WHERE brukernavn = '". resc($db, $_POST['bruker']) ."';";
      $db->query($sql);
      return 1;  
      } 
     //etter 5 forsøk vil ikke dette gjøres 
    else if ($row['failed_login_count'] <= 5) {
     $ant = $row['failed_login_count'];
     $ant += 1;
     $sql = "UPDATE bruker SET failed_login_count =" .  $ant . " WHERE brukernavn = '". resc($db, $_POST['bruker']) ."';";
     $db->query($sql);
     return $ant; 
     } //returnerer at det er for mange forsøk eller brukeren ikke finnes (måtte lagd ny bruker for å telle forsøk på feil brukernavn..) 
     else {return 6;};
   } 
   else {return 1;};
}

function getWait($db, $navn) {
     $sql = "select (MINUTE(first_failed_login + INTERVAL 15 MINUTE))-(MINUTE(NOW())) as MIN from bruker 
     where brukernavn = '". $navn . "';";
     
     $res = $db->query($sql);
     if ($row=$res->fetch_assoc()) {
     $min = $row['MIN'];
     //Hvis siste+15 er over en time, alstå kan minutt bli 05, og minutt i  NOW() er 58. 05-58 er -53, men brukeren må egentlig bare vente 7 min. 60+-53=7
     if ($min[0]=='-') {$min = 60+$min;}
     return $min;
     }

}

//Glemt passord
$print = "";
if (isset($_POST['mottapass']) and $_POST['mottapass'] == "Motta passord") {
	
	if (ertom($_POST['recoverpassword'])) {
		$fMelding = "Angi brukernavn før du fortsetter";
		$print = "window.onload = clickit();";
	} else {
	$brukernavn = resc($db, $_POST['recoverpassword']);
	$sql = "select * from bruker where brukernavn='" . $brukernavn . "'";
	
	$result = $db->query($sql);
		if ($row=$result->fetch_assoc()) {
			$print = "window.onload = clickit();";
			
			$email = $row["ePost"];
			
			$length = 8;
			$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    		$randompassword = substr( str_shuffle( $chars ), 0, $length );

			
			$newpass = hash('sha512', $randompassword.$salt);
			
			$db->query("update bruker set passord = '".$newpass."' where brukernavn = '".$brukernavn."'");

					
		// E-mail config
//		$EmailTo = $email;
		$EmailTo = "nevel_y@yahoo.no";
		$Subject = "Nytt passord";
		$Body = "Her er Ditt nye passord. Husk å endre passordet ditt ved neste pålogging.  \n\n" .$randompassword;
		$Header = "From: ProCorr As admin <post@localhost.no>";
	
		// send email 
		$success = mail($EmailTo, $Subject, $Body, $Header);
	
		if ($success){
		  $sMelding = "Nytt passord er sendt til e-post adressen din";
		}
		else{
		  $fMelding = "Det har oppstått en feil, Vennligst prøv igjen senere.";
		}
	
			
		} else {
			$fMelding = 'Finner ikke Brukernavnet "'.$brukernavn.'"';
			$print = "window.onload = clickit();";
		}
	}
}

//Endre Passord
if (isset($_POST['byttpass']) and $_POST['byttpass'] == "Bytt passord") {
	if (ertom($_POST['gampass']) or ertom($_POST['nyttpass1']) or ertom($_POST['nyttpass2'])) {
		$fMelding = "Angi gammelt og nytt passord før du forsøker å endre passordet";
	} else {
		$navn = resc($db, $_SESSION['bruker']);
		$res = $db->query("select passord from bruker where brukernavn = '". $navn ."'");
		
		if ($row=$res->fetch_assoc()) {
			$ektepass = $row['passord'];
			$gampass = $_POST['gampass'];
			$nyttpass1 = $_POST['nyttpass1'];
			$nyttpass2 = $_POST['nyttpass2'];
			  
			if ($nyttpass1 != $nyttpass2) {
				$fMelding = "Nye passord er ikke like";
			} else if (hash('sha512', $gampass.$salt) != $ektepass) {
				$fMelding = "Gammelt passord er feil";
			} else {
				$db->query("update bruker set passord = '" . hash('sha512', $nyttpass1.$salt) . "' where brukernavn = '" . $navn . "'");
				$sMelding = "Passord endret";
			}
      	} else {
			$fMelding = "Finner ikke gammelt passord";
		}
    }
  }

//Logg ut
if (isset($_POST['loggut']) and $_POST['loggut'] == "Logg ut") {
   session_destroy();
   header("Location: ../default.php");
}

//Bilder og Dokumenter
//Lage thumbnails
//Finne filtype
function imageCreateFromAny($filepath) { 
    $type = exif_imagetype($filepath); 
    $allowedTypes = array( 
        1,  //  gif 
        2,  //  jpeg 
        3,  //  png 
		4,  // jpg
    ); 
    if (!in_array($type, $allowedTypes)) {  //hvis typen ikke er tillatt
        return false; 
    } 
    switch ($type) { 
        case 1 : 
            $im = imageCreateFromGif($filepath); 
        break; 
        case 2 : 
            $im = imageCreateFromJpeg($filepath); 
        break; 
        case 3 : 
            $im = imageCreateFromPng($filepath); 
        break; 
		case 4 : 
            $im = imagecreatefromjpeg($filepath); 
        break; 
    }    
    return $im;  
} 

function make_thumb($filepath, $dest) {

	// lese source image 
	$source_image = imageCreateFromAny($filepath);
  	$type = exif_imagetype($filepath);
	$width = imagesx($source_image);
	$height = imagesy($source_image);
	
	//ny bredde og høyde for størrelsen på thumbnail
	if($width>$height) {
		$nywidth = 180;
  		$nyheight = 120;
	} else {
		$nywidth = 120;
  		$nyheight = 180;
	}
	
	//lage et "virituelt" bilde
	$virtual_image = imagecreatetruecolor($nywidth, $nyheight);
	
	//putt source image i virtual image, med nye størrelser
	imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $nywidth, $nyheight, $width, $height);
	
	//lagrer thumbnailen på riktig destinasjon
  switch ($type) { 
        case 1 : 
           imagegif($virtual_image, $dest); 
        break; 
        case 2 : 
            imagejpeg($virtual_image, $dest); 
        break; 
        case 3 : 
            imagepng($virtual_image, $dest); 
        break; 
	  	case 4 :
            imagejpeg($virtual_image, $dest); 
        break;
    }  
}

//leggtil bilder
if (isset($_POST['leggtilbilde'])) {
	$innholdnr = $_POST['leggtilbilde'];
	
		$visbilder= '<form method="POST">
										<input type="hidden" name="innhold" id="innhold" value="'.$innholdnr.'">
										<button type="submit" name="fjernbilde" id="fjernbilde" value="'.$innholdnr.'"></button>
									</form>
					
									<div class="uploadform">
										<form action="#popup" method="post" enctype="multipart/form-data">
											<input class="imgchoose" type="file" name="image" id="image">
											<input class="altupload" type="text" name="alttekst" id="alttekst" placeholder="Alternativ-tekst" required>
											<button class="imgupload" type="submit" name="uploadpic" value="uploadpic" id="uploadpic">laste opp</button>
										</form>
									</div>';
                 
	$sql="SELECT bildenr, thumb, bredde, høyde FROM bilde WHERE type='general'";    
	$res = $db->query($sql);
	
	while ($row=$res->fetch_assoc()) {
        $bildeid = $row['bildenr'];
        $hvor = "../bilder/thumb/".$row['thumb'];
		$width = $row['bredde'];
		$height = $row['høyde'];
		
		if($width>$height) {
			$btnclass = 'hiddeninsertbtnwidth';
		} else {
			$btnclass = 'hiddeninsertbtnhight';
		}
        
        $visbilder .= ' 
									<form method="POST">  
										<input type="hidden" name="innhold" id="innhold" value="'.$innholdnr.'">
										<button type="submit" class="'.$btnclass.'" value="'.$bildeid.'" name="velgpic" id="velgpic">
											<img src="'. $hvor .'" class="fixed-image"/>
										</button>
									</form>';
      }
}

//Sett bilde
	if (isset($_POST['velgpic']) and isset($_POST['innhold']) ) {
		$innholdnr =  $_POST['innhold'];
		$nybilde = $_POST['velgpic'];
			
		$sql = "SELECT * FROM innholdbilde WHERE innholdnr = ".$innholdnr." ;";
		$res = $db->query($sql);
		
		if ($row=$res->fetch_assoc()) {
		  $sql="UPDATE innholdbilde SET bildenr=".$nybilde." WHERE innholdnr=".$innholdnr." ;";
		  $db->query($sql);
		  $print = "window.onload = clickit();";
		} else {
		  $sql="INSERT INTO innholdbilde VALUES(".$innholdnr.", ".$nybilde.");";
		  $db->query($sql);
		  $print = "window.onload = clickit();";
		}
	}


//fjern bilde
if (isset($_POST['fjernbilde']) and isset($_POST['innhold'])) {
   $innholdnr =  $_POST['innhold'];		
   $sql = "DELETE FROM innholdbilde WHERE innholdnr=".$innholdnr." ;";
   $db->query($sql);
   $print = "window.onload = clickit();";
}


//bilde upload
if(isset($_POST["uploadpic"])) {
	error_reporting(0);
	$picname = ($_FILES['image']['name']);
	$target_dir = "../bilder/";
	$thumb_dir = "../bilder/thumb/";
	$target_file = $target_dir . basename($_FILES['image']['name']);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	 
	$thumbdest = $thumb_dir."thumb".$picname; 
	
	if (ertom($_POST['alttekst'])) {
		$fMelding = "Legg til alternativ tekst";
	} else {
		$alttekst = resc($db, $_POST['alttekst']);


	// Check if file already exists
		if (file_exists($target_file)) {
			$fMelding = "Det finnes allerede en fil med samme navn. ";
			$uploadOk = 0;
		}

	// Check file size
		if ($_FILES["image"]["size"] > 10485760) {
			$fMelding = "Filen er for stor. Max størrelse 10MB.";
			$uploadOk = 0;
		}

	// Allow certain file formats
		if($imageFileType != "png" && $imageFileType != "jpeg"  && $imageFileType != "jpg" && $imageFileType != "gif" ) {
			$fMelding = "Bare JPEG, JPG, PNG og GIF filer er tillatt. ";
			$uploadOk = 0;
		}

	// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 1) {
			if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {

				$source_imagee = imageCreateFromAny($target_dir.$picname);
				$widthh = imagesx($source_imagee);
				$heightt = imagesy($source_imagee);

				make_thumb($target_file, $thumbdest);

				$db->query("INSERT INTO bilde VALUES ('', '', '', '$alttekst', '$picname', 'thumb$picname', '$widthh', '$heightt', 'general')");
				$sMelding = "Filen \"". basename( $_FILES["image"]["name"]). "\" er lastet opp.";


				$uppic = "<img src=\"../bilder/thumb/thumb$picname\">";

			} else {
				$fMelding = "Beklager, filen ble ikke lastet opp.";
			}

		} else {
			echo "Error: " . $addsql . "<br>" . $db->error;
		}
	}
}



//leggtil dokumenter
if (isset($_POST['leggtildok'])) {
	$innholdnr = $_POST['leggtildok'];
      
	$visbilder= '<form method="POST">
										<input type="hidden" name="innhold" id="innhold" value="'.$innholdnr.'">
										<button type="submit" name="fjerndok" id="fjerndok" value="'.$innholdnr.'"></button>
									</form>

									<div class="uploadform">
										<form action="#popup" method="post" enctype="multipart/form-data">
											<input class="imgchoose" type="file" name="dok" id="dok">
											<input class="altupload" type="text" name="alttekst" id="alttekst" placeholder="Navn" required>
											<button class="imgupload" type="submit" name="uploaddok" value="uploaddok" id="uploaddok">laste opp</button>
										</form>
									</div>';
                 
	$sql="SELECT * FROM dokument";     
	$res = $db->query($sql);
	
	while ($row=$res->fetch_assoc()) {
        $dokid = $row['dokumentnr'];
        $hvor = "../dokumenter/".$row['hvor'];
		
		$visbilder .= ' 
									<form method="POST">  
										<input type="hidden" name="innhold" id="innhold" value="'.$innholdnr.'">
										<button type="submit" class="hiddeninsertbtnhight" name="velgdok" id="velgdok" value="'.$dokid.'">
											<a class="adddok" href="'. $hvor .'" ><img src="../bilder/dok.png" alt="'.$row['tekst'].'" class="fixed-image"/>'.$row['tekst'].'</a>
										</button>
									</form>
									';
	}
	  
}

//Sett dokument
	if (isset($_POST['velgdok']) and isset($_POST['innhold']) ) {
		$innholdnr =  $_POST['innhold'];
		$nydok = $_POST['velgdok'];
			
		$sql = "SELECT * FROM innholddok WHERE innholdnr = ".$innholdnr." ;";
		$res = $db->query($sql);
		
		if ($row=$res->fetch_assoc()) {
		  $sql="UPDATE innholddok SET dokumentnr=".$nydok." WHERE innholdnr=".$innholdnr." ;";
		  $db->query($sql);
		  $print = "window.onload = clickit();";
		} else {
		  $sql="INSERT INTO innholddok VALUES(".$innholdnr.", ".$nydok.");";
		  $db->query($sql);
		  $print = "window.onload = clickit();";
		}
	}


//fjern dokument
	if (isset($_POST['fjerndok']) and isset($_POST['innhold'])) {
	   $innholdnr =  $_POST['innhold'];		
	   $sql = "DELETE FROM innholddok WHERE innholdnr=".$innholdnr." ;";
	   $db->query($sql);
	   $print = "window.onload = clickit();";
	}


//dokument upload
if(isset($_POST["uploaddok"])) {
	error_reporting(0);
	$target_dir = "../dokumenter/";
	$target_file = $target_dir . basename($_FILES['dok']['name']);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	$dokname = ($_FILES['dok']['name']); 
	
	if (ertom($_POST['alttekst'])) {
		$fMelding = "Legg til alternativ tekst";
	} else {
		$alttekst = resc($db, $_POST['alttekst']);

	// Check if file already exists
		if (file_exists($target_file)) {
			$fMelding = "Det finnes allerede en fil med samme navn. ";
			$uploadOk = 0;
		}

	// Check file size
		if ($_FILES["image"]["size"] > 10485760) {
			$fMelding = "Filen er for stor. Max størrelse 10MB.";
			$uploadOk = 0;
		}

	// Allow certain file formats
		if($imageFileType != "txt" && $imageFileType != "pdf" && $imageFileType != "doc" && $imageFileType != "docx" ) {
			$fMelding = "Bare txt, pdf, doc og docx filer er tillatt. ";
			$uploadOk = 0;
		}
 
// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 1) {
			if (move_uploaded_file($_FILES["dok"]["tmp_name"], $target_file)) {

				$db->query("INSERT INTO dokument VALUES ('', '$dokname', '$alttekst')");
				$sMelding = "Filen \"". basename( $_FILES["dok"]["name"]). "\" er lastet opp.";

				$uppic = "<img src=\"../bilder/dok.png\" width=\"120\" height=\"180\">";

			} else {
				$fMelding = "Beklager, filen ble ikke lastet opp.";
			}

		} else {
			echo "Error: " . $addsql . "<br>" . $db->error;
		}
	}
}

//Hent elementer
//Meny/innhold emelmenter
function updateall($file) {
	ob_start();
	include($file);
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}

function update($db){
	
	$sqlfilename = "SELECT side FROM meny";    
	$res = $db->query($sqlfilename);
	
	while ($row=$res->fetch_assoc()) {
		$htmlname = $row['side'];
		$phpfile = substr($row['side'],0,-5);;
		$file = $phpfile.".php";
		$content = updateall($file);
		$newhtml = "../procorr/$htmlname";
		$fp = fopen($newhtml, "w");
		fwrite($fp, $content);
		fclose($fp);
	}
}

$navsqlCommand = "SELECT * FROM meny ORDER BY menynr ASC"; 
$navquery = mysqli_query($db, $navsqlCommand) or die (mysqli_error()); 

$redmenuDisplay = '';
$redinnholdDisplay = '';
while ($row = mysqli_fetch_array($navquery)) { 

	$page_id = $row["menynr"];
	$page_link = $row["side"];
	
	$inlink = substr($page_link,0,-5);
	$altlink = ''.$inlink.'.php';

		
	if ($page_id == 1) {
		$disabled = 'disabled';
		$tobtn =	'<button class="disredigerbtn" type="submit" name="endnavn" value="endnavn'.$row['menynr'].'" id="endnavn'.$row['menynr'].'" disabled>Endre navn / tooltip</button>
											<button class="disredigerbtn" type="submit" name="slettmeny" value="slettmeny'.$row['menynr'].'" id="slettmeny'.$row['menynr'].'" disabled>Slett element</button>';
	}
	else if($page_id == 2 or $page_id == 3 or $page_id == 4 ) {
		$disabled = '';
    	$tobtn =	'<button class="redigerbtn" type="submit" name="endnavn" value="endnavn'.$row['menynr'].'" id="endnavn'.$row['menynr'].'">Endre navn / tooltip</button>
											<button class="disredigerbtn" type="submit" name="slettmeny" value="slettmeny'.$row['menynr'].'" id="slettmeny'.$row['menynr'].'" disabled>Slett element</button>';
	}
	else {
		$disabled = '';
    	$tobtn =	'<button class="redigerbtn" type="submit" name="endnavn" value="endnavn'.$row['menynr'].'" id="endnavn'.$row['menynr'].'">Endre navn / tooltip</button>
											<button class="redigerbtn" type="submit" name="slettmeny" value="slettmeny'.$row['menynr'].'" id="slettmeny'.$row['menynr'].'">Slett element</button>';
	}
					
	
	$redmenuDisplay .= '
									<form method="POST">
										<div class="rednav-whole"> 
											<div class="rednav-left">
												<section>
													<input type="hidden" name="side" id="side" value="'.$altlink.'">
													<input type="hidden" name="oldtitle" id="oldtitle" value="'.$row['tekst'].'">
													<h1><textarea class="menytxtarea" name="tekst" placeholder="Navn" '.$disabled.'>'.$row['tekst'].'</textarea></h1>
												</section>
											</div>
											<div class="rednav-center">
												<textarea class="menytxtarea" name="tooltip" placeholder="Tooltip" '.$disabled.'>'.$row['tooltip'].'</textarea>
											</div>
											<div class="rednav-right">
												'.$tobtn.' 
											</div>
										</div>
									</form>';
						
						
	$redinnholdDisplay .= '
									<form method="POST">
										<div class="rednav-whole"> 
											<div class="rednav-left">
												<section>
													<h1><textarea class="menytxtarea" disabled>'.$row['tekst'].'</textarea></h1>
												</section>
											</div>
											<div class="rednav-right">
												<button class="redigerbtn" type="submit" name="redinnhold" value="redinnhold'.$row['rekke'].'" id="redinnhold'.$row['rekke'].'">Rediger innhold</button>
												<button class="redigerbtn" type="submit" name="tominnhold" value="tominnhold'.$row['rekke'].'" id="tominnhold'.$row['rekke'].'" onclick="return confirm(\'Er du sikkert på at du vil tømme innhold fra: '.$row['tekst'].' \')">Tøm innhold</button>
											</div>
										</div>
									</form>';						
	

}		
mysqli_free_result($navquery); 

//Hent innhold
$contentsqlCommand = "SELECT innhold.innholdnr as innholdnr, innhold.tittel, innhold.ingress, innhold.tekst, bilde.hvor as bildehvor, bilde.alt as bildealt, bilde.bredde, bilde.høyde, dokument.hvor as dokumenthvor, dokument.tekst as dokumenttekst
FROM innhold 
LEFT JOIN innholdbilde ON innhold.innholdnr = innholdbilde.innholdnr 
LEFT JOIN bilde ON bilde.bildenr = innholdbilde.bildenr
LEFT JOIN innholddok ON innhold.innholdnr = innholddok.innholdnr 
LEFT JOIN dokument ON dokument.dokumentnr = innholddok.dokumentnr
WHERE innhold.menynr = '$page' AND innhold.type = 'general' ORDER BY innhold.rekke ASC";

$contentquery = mysqli_query($db, $contentsqlCommand) or die (mysqli_error()); 

$back_contentDisplay = '';
while ($row = mysqli_fetch_array($contentquery)) { 
	$id = $row['innholdnr'];
	
	$width = $row['bredde'];
	if($width>900) {
		$style = 'width: 100%; height: 100%;';
	} else {
		$style = '';
	}
		
	$wherepic = '<img class="bilder" src="../bilder/'.$row['bildehvor'].'" alt="'.$row['bildealt'].'" width="'.$row['bredde'].'" height="'.$row['høyde'].'" style="'.$style.'"/>';
	$wheredok = '<a class="dokumenter" href="../dokumenter/'.$row['dokumenthvor'].'" target="_blank" download>'.$row['dokumenttekst'].'</a>';
	$bildelengde = strlen($wherepic);
	$doklengde = strlen($wheredok);
	
	if ($bildelengde<74) {
		$wherepic = "";
	}
	
	if ($doklengde<74) {
		$wheredok = "";
	} 
	
	$back_contentDisplay .= '
							<div class="maincontent-whole">
								<div id="content">
									<article class="featured">
										<form method="POST">
											<header>
												<h1><textarea placeholder="Tittel" class="txtareah1" name="tittel">'.$row['tittel'].'</textarea></h1>
												<h2><textarea placeholder="Ingress" class="txtareah2" name="ingress">'.$row['ingress'].'</textarea></h2>
											</header>
											
												<p><textarea placeholder="Tekst" class="txtareap" name="tekst">'.$row['tekst'].'</textarea></p>
												
											<div class="content-whole">
												<section>
													'.$wherepic.'
													'.$wheredok.'
												</section>
											</div>
											
											<div class="btn-left">
												<section>
													<button class="redbtn" type="submit" name="leggtilbilde" value="'.$row['innholdnr'].'" id="leggtilbilde" formaction="#popup">Legg til bilde</button>
													<button class="redbtn" type="submit" name="leggtildok" value="'.$row['innholdnr'].'" id="leggtildok" formaction="#popup">Legg til dokument</button>
												</section>
											</div>
											
											<div class="btn-right">
												<section>
													<button class="redbtn" type="submit" name="innholdlagre" value="innholdlagre'.$row['innholdnr'].'" id="innholdlagre'.$row['innholdnr'].'">Lagre</button>
													<button class="redbtn" type="submit" name="innholdslett" value="innholdslett'.$row['innholdnr'].'" id="innholdslett'.$row['innholdnr'].'">Slett</button>
												</section>
											</div>
										 </form>
									</article>
								</div>
							</div>' ."\n";
}
mysqli_free_result($contentquery); 

//Rediger meny/innhold side
// Legg til ny meny
if (isset($_POST['leggtil'])) {
	
	$maxvalueplus = $db->query("SELECT MAX(menynr) AS max FROM meny");      
        if (!$maxvalueplus) die($db->error);
        while($row = mysqli_fetch_array($maxvalueplus, MYSQLI_ASSOC)) {
			$hpo = $row['max'] + 1;
        } 
		
    $addsql = "INSERT INTO meny VALUES ('$hpo', 'Side $hpo', 'side$hpo.html', '$hpo', '', 'Gå til Side $hpo')";

	$back_content = "<?php \$page = '$hpo'; ?>
<?php \$page_title = \"ProCorr AS - Side $hpo\";?>
<?php include(\"connection.php\");?>
<?php include(\"back_functions.php\");?>
<?php include(\"back_header.php\");?>
					
				<!-- Main -->
				<div id=\"main-wrapper\">
					<div class=\"container\" id=\"linkify\">
						<div class=\"top-pack\">
						
							<?php echo \$back_contentDisplay; ?>

							<form method=\"POST\">
								<div id=\"addroll\" class=\"hidden\">
									<div class=\"content-whole\"> <!-- Holder innholdet i midten *(i midten på mobil-enheter) -->
										<div class=\"add\">
											<span>
												<button class=\"hiddenaddbtn\" type=\"submit\" name=\"addinnhold\" value=\"addinnhold\" id=\"addinnhold\" onmouseover=\"addin()\" onmouseout=\"addout()\">
													<img id=\"img\" src=\"../bilder/back-add1.png\"/>
												</button>
											</span>
										</div>
									</div>
								</div>
							</form>
							
							<div id=\"popup\" class=\"hidden\">
								<div class=\"popup-container\">
									<?php echo \$visbilder; ?>
									<form method=\"POST\" class=\"nada\" action=\"back_side$hpo.php\">
										<button id=\"closeit\" type=\"submit\" class=\"popup-close\">X</button>
									</form>
									<div class=\"return-whole\">
										<p class=\"successMelding\"><?php echo (\$sMelding); ?></p> 
										<p class=\"failMelding\"><?php echo (\$fMelding); ?></p> 
										<?php echo \$uppic; ?>
									</div> 
								</div>
							</div>
                      
						</div>
					</div>
				</div>

<?php include(\"back_footer.php\");?>";

	$content = "<?php \$page = '$hpo';?>
<?php \$page_title = \"ProCorr AS - Side $hpo\";?>
<?php include(\"connection.php\");?>
<?php include(\"functions.php\");?>
<?php include(\"header.php\");?>

			<!-- Main -->
			<div id=\"main-wrapper\">
				<div class=\"container\" id=\"linkify\">
					<div class=\"top-pack\">
					
						<?php echo \$contentDisplay; ?>
						
					</div>
				</div>
			</div>

<?php include(\"footer.php\");?>";

	if ($db->query($addsql) === TRUE) {
		
		$back_filename = "back_side".$hpo.".php";
		$newbackfile = fopen($back_filename,'w');
		fwrite($newbackfile, $back_content);
		fclose($newbackfile);
		
		$filename = "side".$hpo.".php";
		$newfile = fopen($filename,'w');
		fwrite($newfile, $content);
		fclose($newfile);

		$file = $filename;
		function read($file) {
			ob_start();
			include($file);
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
		$content = read($file);
	
		$newhtml = "../procorr/side".$hpo.".html";
		$fp = fopen($newhtml, "w");
		fwrite($fp, $content);
		fclose($fp);
		
		update($db);

		header("Location: backend.php");
	
	} else {
    	echo "Error: " . $addsql . "<br>" . $db->error;
	}
}


// Endre meny navn
if (isset($_POST['endnavn'])) {
	$meny_nr = substr($_POST['endnavn'],7);
	$sidenavn = $_POST['side'];
	
	if (ertom($_POST['tekst'])) {
		echo '<script language="javascript">';
		echo 'alert("Vennligst skriv inn sidenavn.")';
		echo '</script>';
	} else {
		if (!preg_match('/^[0-9a-zA-Z\s]+$/', ($_POST['tekst']))) {
		echo '<script language="javascript">';
		echo 'alert("Sidenavnet kan bare bestå av bokstaver og tall.")';
		echo '</script>';
		} else {
			$tekst = resc($db, $_POST['tekst']);
			$tooltip = resc($db, $_POST['tooltip']);

			$nymenynavn = resc($db,$_POST['tekst']);
			$sql= "SELECT tekst from meny WHERE tekst='". $nymenynavn . "' AND menynr!='".$meny_nr."'";
			$res = $db->query($sql);
			if ($row=$res->fetch_assoc()) {
				echo '<script language="javascript">';
				echo 'alert("Det finnes allerede en side med dette navnet.")';
				echo '</script>';
			} else {
				$altname = preg_replace('/\s+/', '', strtolower("".$tekst.".html"));
				$newname = preg_replace('/\s+/', '', strtolower("".$tekst.".php"));
				$newalt = $_POST['tekst'];

				$oldtitle = "\$page_title = \"ProCorr AS - ".$_POST['oldtitle']."";
				$newtitle = "\$page_title = \"ProCorr AS - ".$tekst."";

				$oldobname = substr($sidenavn,0,-4);
				$newobname = substr($newname,0,-4);
				$oldform = "class=\"nada\" action=\"back_".$oldobname.".php";
				$newform = "class=\"nada\" action=\"back_".$newobname.".php";

				$oldhtml = ("../procorr/$oldobname.html");
	
	
				$updatemenysql = "UPDATE meny SET tekst='".$tekst."', side='".$altname."', tooltip='".$tooltip."', alt='Gå til ".$newalt."' WHERE menynr=".$meny_nr;
	
				if ($db->query($updatemenysql) === TRUE) {
					rename('back_'.$sidenavn.'','back_'.$newname.'');
					rename($sidenavn,$newname);	
					unlink($oldhtml);

					$path_to_file = 'back_'.$newname.'';
					$file_contents = file_get_contents($path_to_file);
					$file_contents = str_replace($oldtitle,$newtitle,$file_contents);
					$file_contents = str_replace($oldform,$newform,$file_contents);
					file_put_contents($path_to_file,$file_contents);

					$path_to_file = $newname;
					$file_contents = file_get_contents($path_to_file);
					$file_contents = str_replace($oldtitle,$newtitle,$file_contents);
					file_put_contents($path_to_file,$file_contents);

					$file = $newname;
					function read($file) {
						ob_start();
						include($file);
						$content = ob_get_contents();
						ob_end_clean();
						return $content;
					}
					$content = read($file);

					$newhtml = "../procorr/$altname";
					$fp = fopen($newhtml, "w");
					fwrite($fp, $content);
					fclose($fp);

				} else {
					echo "Error: " . $updatemenysql . "<br>" . $db->error;
				}
				update($db);
				header("Location: backend.php");
			}
		}
	}
}

// Slett meny
if (isset($_POST['slettmeny'])) {
	$page_id = substr($_POST['slettmeny'],9);
	$menuname = preg_replace('/\s+/', '', strtolower("".$_POST['tekst'].".php"));
	$htmlname = substr($menuname,0,-4);
	$htmlpath = '../procorr/'.$htmlname.'.html';
	
    $deletesql = "DELETE FROM meny WHERE menynr=".$page_id;
 	
	if ($db->query($deletesql) === TRUE) {
		unlink('back_'.$menuname.'');
		unlink($menuname);
		unlink($htmlpath);
		
		update($db);
		header("Location: backend.php");

	} else {
    	echo '<script language="javascript">';
      	echo 'alert("Innholdet i dette elementet må tømmes før det kan slettes. \nRediger innhold --> Tøm innhold")';
      	echo '</script>';
	}
}

// Rediger innhold side
if (isset($_POST['redinnhold'])) {
	$page_nr = substr($_POST['redinnhold'],10);
	
	$changesql = "SELECT * FROM meny WHERE rekke=".$page_nr;
	$result = $db->query($changesql);

	if ($result->num_rows > 0) {
    	while($row = $result->fetch_assoc()) {
			$altname = substr($row["side"],0,-5);
			$page_name = 'back_'.$altname.'.php';
			header("Location: $page_name");
    	}
	} else {
    echo "Error: " . $changesql . "<br>" . $db->error;
	}
}

// Tøm innhold
if (isset($_POST['tominnhold'])) {
	$tom_nr = substr($_POST['tominnhold'],10);
	
	$sqlmenyid = mysqli_query($db, "SELECT innholdnr FROM innhold WHERE type = 'general' AND menynr=".$tom_nr);
	
	while ($row = $sqlmenyid->fetch_assoc()) {
		$innholdnr = $row['innholdnr'];
		$tominnholbildesql = "DELETE FROM innholdbilde WHERE innholdnr=".$innholdnr;
		$db->query($tominnholbildesql);
    }
	
	$tominnholdsql = "DELETE FROM innhold WHERE type = 'general' AND menynr=".$tom_nr;
	
	if ($db->query($tominnholdsql) === TRUE) {
		update($db);
		header("Location: back_redinnhold.php");
	} else {
    	echo "Error: " . $tominnholdsql . "<br>" . $db->error;
	}

}

// Lagre innhold
if (isset($_POST['innholdlagre'])) {
	$innhold_nr = substr($_POST['innholdlagre'],12);
	
	if (ertom($_POST['tittel'])){
			echo '<script language="javascript">';
      		echo 'alert("Vennligst skriv inn tittel.")';
      		echo '</script>';
	} else {
		$tittel = resc($db, $_POST['tittel']);
		$ingress = resc($db, $_POST['ingress']);
		$tekst = resc($db, $_POST['tekst']);

		$updateinnholdsql = "UPDATE innhold SET tittel='".$tittel."', ingress='".$ingress."', tekst='".$tekst."' WHERE innholdnr=".$innhold_nr;

		if ($db->query($updateinnholdsql) === TRUE) {
			update($db);
			header('Location: '.$_SERVER['PHP_SELF']);
		} else {
			echo "Error: " . $updateinnholdsql . "<br>" . $db->error;
		}
	}
}

// Slett innhold
if (isset($_POST['innholdslett'])) {
	$innhold_nr = substr($_POST['innholdslett'],12);
	
	$deleteinnholdsql = "DELETE FROM innhold WHERE innholdnr=".$innhold_nr;
	
	if ($db->query($deleteinnholdsql) === TRUE) {
		update($db);
		header('Location: '.$_SERVER['PHP_SELF']);
	} else {
    	echo "Error: " . $deleteinnholdsql . "<br>" . $db->error;
	}
}

// legg til innhold
if (isset($_POST['addinnhold'])) {
	$menyid = $page;
	
	$rekkemaxvalue = $db->query("SELECT MAX(rekke) AS maxinnhold FROM innhold WHERE menynr=".$menyid);      
    	if (!$rekkemaxvalue) die($db->error);
        	while($row = mysqli_fetch_array($rekkemaxvalue, MYSQLI_ASSOC)) {
				$rmv = $row['maxinnhold'] + 1;
			}

	$addinnholdsql = "INSERT INTO innhold VALUES ( '', '', '', '', $rmv, 'general', $menyid)";

	if ($db->query($addinnholdsql) === TRUE) {
		header('Location: '.$_SERVER['PHP_SELF']);
	} else {
	echo "Error: " . $addinnholdsql . "<br>" . $db->error;
	}
}

//Footer elementer
//Hente footer
$contactsqlCommand = "SELECT * FROM kontakt WHERE kontaktnr=1";
$contactquery = mysqli_query($db, $contactsqlCommand) or die (mysqli_error());

$backfooterDispley = '';
while ($row = $contactquery->fetch_assoc()) {

	$backfooterDispley = '	
			<div id="footer">
				<div class="container">
					<form method="POST">
						<section>
							<div class="footer-center">
								<h1>Besøksadresse:</h1>
								<h2><textarea placeholder="Adresse" class="footertxtarea" name="adresse">'.$row['adresse'].'</textarea></h2> 
								<h2><textarea placeholder="Postnr" class="footertxtarea" name="postnr">'.$row['postnr'].'</textarea></h2>
								<h2><textarea placeholder="Sted" class="footertxtarea" name="poststed">'.$row['poststed'].'</textarea></h2>
							</div>
							<div class="footer-center">
								<h1>Telefon:</h1>
								<h2><textarea placeholder="Tlf" class="footertxtarea" name="tlf">'.$row['tlf'].'</textarea></h2>
								<h1>E-Post:</h1>
								<h2><textarea placeholder="E-Post" class="footertxtarea" name="ePost">'.$row['ePost'].'</textarea></h2>
							</div>
							<div class="footer-center">
								<button class="footredbtn" type="submit" value="Lagre" name="footerlagre" id="footerlagre">Lagre</button>
								<button class="footredbtn" type="submit" value="Slett" name="footerslett" id="footerslett">Slett</button>
							</div>
						</section>
					</form>
				</div>
			</div>';
								
}
mysqli_free_result($contactquery); 

//Lagre footer
if (isset($_POST['footerlagre']) and $_POST['footerlagre'] == "Lagre") {
	$adresse = resc($db, $_POST['adresse']);
	$postnr = resc($db, $_POST['postnr']);
	$poststed = resc($db, $_POST['poststed']);
	$tlf = resc($db, $_POST['tlf']);
	$ePost = resc($db, $_POST['ePost']);
	
	$updatekontaktsql = "UPDATE kontakt SET ePost='".$ePost."', tlf='".$tlf."', adresse='".$adresse."', postnr='".$postnr."', poststed='".$poststed."' WHERE kontaktnr=1;";
	
	if ($db->query($updatekontaktsql) === TRUE) {
		update($db);
		header('Location: '.$_SERVER['PHP_SELF']);
	} else {
    	echo "Error: " . $updatekontaktsql . "<br>" . $db->error;
	}
}

//Slett footer
if (isset($_POST['footerslett']) and $_POST['footerslett'] == "Slett") {

	$deletekontaktsql = "UPDATE kontakt SET ePost='', tlf='', adresse='', postnr='', poststed='' WHERE kontaktnr=1;";
	
	if ($db->query($deletekontaktsql) === TRUE) {
		update($db);
		header('Location: '.$_SERVER['PHP_SELF']);
	} else {
    	echo "Error: " . $deletekontaktsql . "<br>" . $db->error;
	}
}

//Slide elementer
//Hent Slide
$slidesqlCommand = "SELECT * FROM bilde WHERE bildenr IN(1,2,3) ORDER BY bildenr ASC"; 
$slidequery = mysqli_query($db, $slidesqlCommand) or die (mysqli_error()); 

$getSlide = [];
$back_slideDisplay = '';
while ($row = mysqli_fetch_array($slidequery)) {
	
	$getSlide[] = '<section>
									<form method="POST">
										<button class="hiddenimgbtn" type="submit" name="slidebilde" value="'.$row['bildenr'].'" id="slidebilde" formaction="#popup">
											<img src="../bilder/'.$row['hvor'].'" class="fixed-image"/>
										</button>
																				
										<h1><textarea placeholder="Tittel" class="slidetxth1" name="tittel">'.$row['tittel'].'</textarea></h1>
										<h2><textarea placeholder="Ingress" class="slidetxth2" name="ingress">'.$row['ingress'].'</textarea></h2>
										<p><textarea placeholder="Alternativ-tekst" class="slidetxtalt" name="alt">'.$row['alt'].'</textarea></p>
										<button class="slidebtn" type="submit" name="slidelagre" value="slidelagre'.$row['bildenr'].'" id="slidelagre'.$row['bildenr'].'">Lagre</button>
										<button class="slidebtn" type="submit" name="slideslett" value="slideslett'.$row['bildenr'].'" id="slideslett'.$row['bildenr'].'">Slett</button>
									</form>
								</section>';

}

	$back_slideDisplay .= '<div id="banner-wrapper">
					<div class="banner-container">
						<div id="banner">

							<div class="slide-left">
								'.$getSlide[0].'
							</div>

							<div class="slide-center">
								'.$getSlide[1].'
							</div>

							<div class="slide-right">
								'.$getSlide[2].'
							</div>

						</div>
					</div>
				</div>';
mysqli_free_result($slidequery); 

//Vis bilder
if (isset($_POST['slidebilde'])) {
	$slideid = $_POST['slidebilde'];
	
		$visbilder= '<form method="POST">
										<input type="hidden" name="slide" id="slide" value="'.$slideid.'">
										<button type="submit" name="fjernslide" id="fjernbilde" value="'.$slideid.'"></button>
									</form>
					
									<div class="uploadform">
										<form action="#popup" method="post" enctype="multipart/form-data">
											<input class="imgchoose" type="file" name="image" id="image">
											<input class="altupload" type="text" name="alttekst" id="alttekst" placeholder="Alternativ-tekst" required>
											<button class="imgupload" type="submit" name="uploadpic" value="uploadpic" id="uploadpic">laste opp</button>
										</form>
									</div>';
                 
	$sql="SELECT bildenr, thumb, bredde, høyde FROM bilde WHERE type='general'";    
	$res = $db->query($sql);
	
	while ($row=$res->fetch_assoc()) {
        $bildeid = $row['bildenr'];
        $hvor = "../bilder/thumb/".$row['thumb'];
		$width = $row['bredde'];
		$height = $row['høyde'];
		
		if($width>$height) {
			$btnclass = 'hiddeninsertbtnwidth';
		} else {
			$btnclass = 'hiddeninsertbtnhight';
		}
        
        $visbilder .= ' 
									<form method="POST">  
										<input type="hidden" name="slide" id="slide" value="'.$slideid.'">
										<button type="submit" class="'.$btnclass.'" value="'.$bildeid.'" name="velgslide" id="velgslide">
											<img src="'. $hvor .'" class="fixed-image"/>
										</button>
									</form>';
      }
}


//Sett slide bilde
	if (isset($_POST['velgslide']) and isset($_POST['slide']) ) {
		$slideid =  $_POST['slide'];
		$nybilde = $_POST['velgslide'];
					
		$sql = "SELECT * FROM bilde WHERE bildenr = ".$nybilde." ;";
		$res = $db->query($sql);
		
		while ($row=$res->fetch_assoc()) {
			
		  $sql = "UPDATE bilde SET alt='".$row['alt']."', hvor='".$row['hvor']."', thumb='".$row['thumb']."', bredde=1200, høyde=440, type='slide' WHERE bildenr=".$slideid;
		  $db->query($sql);
			
		  $print = "window.onload = clickit();";
		}
	}


//fjern slide bilde
if (isset($_POST['fjernslide']) and isset($_POST['slide'])) {
   $slideid =  $_POST['slide'];	
	
   $sql = "UPDATE bilde SET alt='', hvor='slideholder.jpg', thumb='', bredde=1200, høyde=440, type='notslide' WHERE bildenr=".$slideid;
   $db->query($sql);
   $print = "window.onload = clickit();";
}


// Lagre slide
if (isset($_POST['slidelagre'])) {
	$slide_nr = substr($_POST['slidelagre'],10);
	
	if (ertom($_POST['tittel'])){
			echo '<script language="javascript">';
      		echo 'alert("Vennligst skriv inn tittel.")';
      		echo '</script>';
	} else {
		$tittel = resc($db, $_POST['tittel']);
		$ingress = resc($db, $_POST['ingress']);
		$alt = resc($db, $_POST['alt']);

		$updateinnholdsql = "UPDATE bilde SET tittel='".$tittel."', ingress='".$ingress."', alt='".$alt."' WHERE bildenr=".$slide_nr;

		if ($db->query($updateinnholdsql) === TRUE) {
			update($db);
			header('Location: '.$_SERVER['PHP_SELF']);
		} else {
			echo "Error: " . $updateinnholdsql . "<br>" . $db->error;
		}
	}
}

// Slett slide
if (isset($_POST['slideslett'])) {
	$slide_nr = substr($_POST['slideslett'],10);
	
	$deleteinnholdsql = "UPDATE bilde SET tittel='', ingress='', alt='', hvor='slideholder.jpg', thumb='', bredde=1200, høyde=440, type='notslide' WHERE bildenr=".$slide_nr;
	
	if ($db->query($deleteinnholdsql) === TRUE) {
		update($db);
		header('Location: '.$_SERVER['PHP_SELF']);
	} else {
    	echo "Error: " . $deleteinnholdsql . "<br>" . $db->error;
	}
}

//Special elementer
//Home innhold
$homesqlCommand = "SELECT * FROM innhold WHERE menynr = '$page' AND type='main'";
$homequery = mysqli_query($db, $homesqlCommand) or die (mysqli_error()); 

$imagesqlCommand = "SELECT * FROM bilde WHERE bildenr IN(5,6,7,8) ORDER BY bildenr ASC";
$imagequery = mysqli_query($db, $imagesqlCommand) or die (mysqli_error());
$homeImageDisplay = [];
while ($row = mysqli_fetch_array($imagequery)) {
	$homeImageDisplay[] = '<section>
												<button class="hiddenimgbtn" type="submit" name="homebilde" value="'.$row['bildenr'].'" id="homebilde" formaction="#popup">
													<img src="../bilder/'.$row['hvor'].'" class="fixed-image"/>
												</button>

												<h1><textarea placeholder="Tittel" class="hometxth1" name="imgtittel">'.$row['tittel'].'</textarea></h1>
												<h2><textarea placeholder="Ingress" class="hometxth2" name="imgingress">'.$row['ingress'].'</textarea></h2>
												<p><textarea placeholder="Alternativ-tekst" class="hometxtalt" name="alt">'.$row['alt'].'</textarea></p>
												<button class="homeimgbtn" type="submit" name="imglagre" value="imglagre'.$row['bildenr'].'" id="imglagre'.$row['bildenr'].'">Lagre</button>
											</section>';	
}

$backsjefsqlCommand = "SELECT kontakt.*, bilde.* FROM kontakt LEFT JOIN bilde ON bilde.bildenr = kontakt.bildenr WHERE bilde.type = 'portrait' ORDER BY kontaktnr ASC";
$backsjefquery = mysqli_query($db, $backsjefsqlCommand) or die (mysqli_error());
$backsjefDisplay = [];
while ($row = mysqli_fetch_array($backsjefquery)) {
	$backsjefDisplay[] = '<section>
													<button class="hiddenimgbtn" type="submit" name="kontaktbilde" value="'.$row['bildenr'].'" id="kontaktbilde" formaction="#popup">
														<h1 class="img-circle"><img src="../bilder/'.$row['hvor'].'"/></h1>
													</button>
													<h1><textarea placeholder="Navn" class="hometxth1" name="navn" style="resize: none;">'.$row['tittel'].'</textarea></h1>
													<h2><textarea placeholder="Stilling" class="hometxth2" name="stilling" style="resize: none;">'.$row['ingress'].'</textarea></h2>
													<input type="hidden" name="kontakt" id="kontakt" value="'.$row['kontaktnr'].'">
													<p><textarea placeholder="E-post" class="hometxtalt" name="epost" style="resize: none;">'.$row['ePost'].'</textarea></p>
													<p><textarea placeholder="Telefon" class="hometxtalt" name="tlf" style="resize: none;">'.$row['tlf'].'</textarea></p>
													<button class="homeimgbtn" type="submit" name="kontaktlagre" value="kontaktlagre'.$row['bildenr'].'" id="kontaktlagre'.$row['bildenr'].'">Lagre</button>
												</section>';
}

$back_homeDispley = '';
$back_omDispley = '';
$back_minorDisplay = '';
while ($row = mysqli_fetch_array($homequery)) {
	
	$back_homeDispley = '
						<div class="maincontent-whole">
							<div id="content">
								<article class="featured">
									<form method="POST">
										<header>
											<h1><textarea placeholder="Tittel" class="txtareah1" name="tittel">'.$row['tittel'].'</textarea></h1>
											<h2><textarea placeholder="Ingress" class="txtareah2" name="ingress">'.$row['ingress'].'</textarea></h2>
										</header>

											<p><textarea placeholder="Tekst" class="txtareap" name="tekst">'.$row['tekst'].'</textarea></p>
											
										<div class="content-whole">
											<section>
												<button class="homebtn" type="submit" name="homelagre" value="homelagre'.$row['innholdnr'].'" id="homelagre'.$row['innholdnr'].'">Lagre</button>
												<button class="homebtn" type="submit" name="homeslett" value="homeslett'.$row['innholdnr'].'" id="homeslett'.$row['innholdnr'].'">Slett</button>
											</section>
										</div>
									</form>
									<div class="content-center">
										<form method="POST">
											<div class="content-left">
												'.$homeImageDisplay[0].'
											</div>
										</form>
										<form method="POST">
											<div class="content-right">
												'.$homeImageDisplay[1].'
											</div>
										</form>
									</div>
									<div class="content-center">
										<form method="POST">
											<div class="content-left">
												'.$homeImageDisplay[2].'
											</div>
										</form>
										<form method="POST">
											<div class="content-right">
												'.$homeImageDisplay[3].'
											</div>
										</form>
									</div>
								</article>
							</div>
						</div>';
	
	
	$back_omDispley = '<div class="maincontent-whole">
							<div id="content">
								<article class="featured">
									
									<form method="POST">
										<header>
											<h1><textarea placeholder="Tittel" class="txtareah1" name="tittel">'.$row['tittel'].'</textarea></h1>
											<h2><textarea placeholder="Ingress" class="txtareah2" name="ingress">'.$row['ingress'].'</textarea></h2>
										</header>

											<p><textarea placeholder="Tekst" class="txtareap" name="tekst">'.$row['tekst'].'</textarea></p>
											
										<div class="content-whole">
											<section>
												<button class="homebtn" type="submit" name="homelagre" value="homelagre'.$row['innholdnr'].'" id="homelagre'.$row['innholdnr'].'">Lagre</button>
												<button class="homebtn" type="submit" name="homeslett" value="homeslett'.$row['innholdnr'].'" id="homeslett'.$row['innholdnr'].'">Slett</button>
											</section>
										</div>
									</form>
									
									<div class="roundit">
										<div class="slide-left">
											<form method="POST">
												'.$backsjefDisplay[0].'
											</form>
										</div>

										<div class="slide-center">
											<form method="POST">
												'.$backsjefDisplay[1].'
											</form>
										</div>
										<div class="slide-right">
											<form method="POST">
												'.$backsjefDisplay[2].'
											</form>
										</div>
									</div>
									
								</article>
							</div>
						</div>';
	
	
	$back_minorDisplay ='<div class="maincontent-whole">
							<div id="content">
								<article class="featured">
									<form method="POST">
										<header>
											<h1><textarea placeholder="Tittel" class="txtareah1" name="tittel">'.$row['tittel'].'</textarea></h1>
											<h2><textarea placeholder="Ingress" class="txtareah2" name="ingress">'.$row['ingress'].'</textarea></h2>
										</header>

											<p><textarea placeholder="Tekst" class="txtareap" name="tekst">'.$row['tekst'].'</textarea></p>
											
										<div class="content-whole">
											<section>
												<button class="homebtn" type="submit" name="homelagre" value="homelagre'.$row['innholdnr'].'" id="homelagre'.$row['innholdnr'].'">Lagre</button>
												<button class="homebtn" type="submit" name="homeslett" value="homeslett'.$row['innholdnr'].'" id="homeslett'.$row['innholdnr'].'">Slett</button>
											</section>
										</div>
									</form>
								</article>
							</div>
						</div>';
	
}
mysqli_free_result($homequery); 
mysqli_free_result($imagequery); 
mysqli_free_result($backsjefquery); 


// Lagre home
if (isset($_POST['homelagre'])) {
	$innhold_nr = substr($_POST['homelagre'],9);
	
	if (ertom($_POST['tittel'])){
			echo '<script language="javascript">';
      		echo 'alert("Vennligst skriv inn tittel.")';
      		echo '</script>';
	} else {
		$tittel = resc($db, $_POST['tittel']);
		$ingress = resc($db, $_POST['ingress']);
		$tekst = resc($db, $_POST['tekst']);
		
		$updateinnholdsql = "UPDATE innhold SET tittel='".$tittel."', ingress='".$ingress."', tekst='".$tekst."' WHERE innholdnr=".$innhold_nr;

		if ($db->query($updateinnholdsql) === TRUE) {
			update($db);
			header('Location: '.$_SERVER['PHP_SELF']);
		} else {
			echo "Error: " . $updateinnholdsql . "<br>" . $db->error;
		}
	}
}

// Slett home 
if (isset($_POST['homeslett'])) { 
	$innhold_nr = substr($_POST['homeslett'],9);
	
	$deleteinnholdsql = "UPDATE innhold SET tittel='', ingress='', tekst='' WHERE innholdnr=".$innhold_nr; 
	
	if ($db->query($deleteinnholdsql) === TRUE) { 
		update($db);
		header('Location: '.$_SERVER['PHP_SELF']);
	} else {
    	echo "Error: " . $deleteinnholdsql . "<br>" . $db->error;
	}
}

if (isset($_POST['homebilde'])) {
	$imageid = $_POST['homebilde'];
	
		$visbilder= '
						<div class="uploadform">
							<form action="#popup" method="post" enctype="multipart/form-data">
								<input class="imgchoose" type="file" name="image" id="image">
								<input class="altupload" type="text" name="alttekst" id="alttekst" placeholder="Alternativ-tekst" required>
								<button class="imgupload" type="submit" name="uploadpic" value="uploadpic" id="uploadpic">laste opp</button>
							</form>
						</div>';
                 
	$sql="SELECT bildenr, thumb, bredde, høyde FROM bilde WHERE type='general'";    
	$res = $db->query($sql);
	
	while ($row=$res->fetch_assoc()) {
        $bildeid = $row['bildenr'];
        $hvor = "../bilder/thumb/".$row['thumb'];
		$width = $row['bredde'];
		$height = $row['høyde'];
		
		if($width>$height) {
			$btnclass = 'hiddeninsertbtnwidth';
		} else {
			$btnclass = 'hiddeninsertbtnhight';
		}
        
        $visbilder .= ' 
									<form method="POST">  
										<input type="hidden" name="imagehome" id="imagehome" value="'.$imageid.'">
										<button type="submit" class="'.$btnclass.'" value="'.$bildeid.'" name="velgimagehome" id="velgimagehome">
											<img src="'. $hvor .'" class="fixed-image"/>
										</button>
									</form>';
      }
}

//Sett homeimg
	if (isset($_POST['velgimagehome']) and isset($_POST['imagehome']) ) {
		$imgid =  $_POST['imagehome'];
		$nybilde = $_POST['velgimagehome'];
					
		$sql = "SELECT * FROM bilde WHERE bildenr = ".$nybilde." ;";
		$res = $db->query($sql);
		
		while ($row=$res->fetch_assoc()) {
			
		  $sql = "UPDATE bilde SET alt='".$row['alt']."', hvor='".$row['hvor']."', thumb='".$row['thumb']."', bredde=384, høyde=182, type='main' WHERE bildenr=".$imgid;
		  $db->query($sql);
			
		  $print = "window.onload = clickit();";
		}
	}

// Lagre homeimg
if (isset($_POST['imglagre'])) {
	$bilde_nr = substr($_POST['imglagre'],8);
	
	if (ertom($_POST['imgtittel'])){
			echo '<script language="javascript">';
      		echo 'alert("Vennligst skriv inn tittel.")';
      		echo '</script>';
	} else {
		$imgtittel = resc($db, $_POST['imgtittel']);
		$imgingress = resc($db, $_POST['imgingress']);
		$alt = resc($db, $_POST['alt']);
		
		$lagreimgsql = "UPDATE bilde SET tittel='".$imgtittel."', ingress='".$imgingress."', alt='".$alt."' WHERE bildenr=".$bilde_nr;

		if ($db->query($lagreimgsql) === TRUE) {
			update($db);
			header('Location: '.$_SERVER['PHP_SELF']);
		} else {
			echo "Error: " . $lagreimgsql . "<br>" . $db->error;
		}
	}
}


if (isset($_POST['kontaktbilde'])) {
	$imageid = $_POST['kontaktbilde'];
	
		$visbilder= '
						<div class="uploadform">
							<form action="#popup" method="post" enctype="multipart/form-data">
								<input class="imgchoose" type="file" name="image" id="image">
								<input class="altupload" type="text" name="alttekst" id="alttekst" placeholder="Alternativ-tekst" required>
								<button class="imgupload" type="submit" name="uploadpic" value="uploadpic" id="uploadpic">laste opp</button>
							</form>
						</div>';
                 
	$sql="SELECT bildenr, thumb, bredde, høyde FROM bilde WHERE type='general'";    
	$res = $db->query($sql);
	
	while ($row=$res->fetch_assoc()) {
        $bildeid = $row['bildenr'];
        $hvor = "../bilder/thumb/".$row['thumb'];
		$width = $row['bredde'];
		$height = $row['høyde'];
		
		if($width>$height) {
			$btnclass = 'hiddeninsertbtnwidth';
		} else {
			$btnclass = 'hiddeninsertbtnhight';
		}
        
        $visbilder .= ' 
									<form method="POST">  
										<input type="hidden" name="kontaktimg" id="kontaktimg" value="'.$imageid.'">
										<button type="submit" class="'.$btnclass.'" value="'.$bildeid.'" name="velgkontaktimg" id="velgkontaktimg">
											<img src="'. $hvor .'" class="fixed-image"/>
										</button>
									</form>';
      }
}

//Sett kontaktimg
	if (isset($_POST['velgkontaktimg']) and isset($_POST['kontaktimg']) ) {
		$imgid =  $_POST['kontaktimg'];
		$nybilde = $_POST['velgkontaktimg'];
					
		$sql = "SELECT * FROM bilde WHERE bildenr = ".$nybilde." ;";
		$res = $db->query($sql);
		
		while ($row=$res->fetch_assoc()) {
			
		  $sql = "UPDATE bilde SET alt='".$row['alt']."', hvor='".$row['hvor']."', thumb='".$row['thumb']."', bredde=331, høyde=383, type='portrait' WHERE bildenr=".$imgid;
		  $db->query($sql);
			
		  $print = "window.onload = clickit();";
		}
	}


// Lagre kontakt
if (isset($_POST['kontaktlagre'])) {
	$bilde_nr = substr($_POST['kontaktlagre'],12);
	$kontakt_nr = $_POST['kontakt'];
	
	if (ertom($_POST['navn'])){
			echo '<script language="javascript">';
      		echo 'alert("Vennligst skriv inn navn.")';
      		echo '</script>';
	} else {
		$navn = resc($db, $_POST['navn']);
		$stilling = resc($db, $_POST['stilling']);
		$epost = resc($db, $_POST['epost']);
		$tlf = resc($db, $_POST['tlf']);
		
		$lagreimgsql = "UPDATE bilde SET tittel='".$navn."', ingress='".$stilling."' WHERE bildenr=".$bilde_nr;
		$lagrekontaktsql = "UPDATE kontakt SET ePost='".$epost."', tlf='".$tlf."' WHERE kontaktnr=".$kontakt_nr;
		
		if ($db->query($lagreimgsql) === TRUE and $db->query($lagrekontaktsql) === TRUE) {
			update($db);
			header('Location: '.$_SERVER['PHP_SELF']);
		} else {
			echo "Error: " . $lagreimgsql . "<br>" . $db->error;
			echo "Error: " . $lagrekontaktsql . "<br>" . $db->error;
		}
	}
}
?>
