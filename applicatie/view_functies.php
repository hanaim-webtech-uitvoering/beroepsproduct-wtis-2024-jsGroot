<?php
function menuItemsNaarHtmlTable($data) {
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$huidigeType = '';
$menukaart = '';

foreach($data as $rij) { 
  $naam = $rij['naam'];
  $prijs = $rij['prijs'];
  $type = $rij['type'];
  $productName = str_replace(" ", "_", $rij['naam']);

  if (!isset($_SESSION["products"])) {
    $_SESSION["products"] = [];
}
  if (isset($_POST[$productName])) {
    if (isset($_SESSION["products"][$productName])) {
        $_SESSION["products"][$productName] += 1;
    } else {
        $_SESSION["products"][$productName] = 1;
    }
}

if($huidigeType != $type) {
  $huidigeType = $type;
  $menukaart .= "<h2>$huidigeType</h2>";
}

$prijs_format = number_format($prijs, 2, ",");
$menukaart .= "<form method='post'><table>";
$menukaart .= "<tr><td>$naam</td><td>&euro; $prijs_format</td><td><input type='submit' name='$productName' value='add to order'></td></tr>";
$menukaart .= "</table></form>";

}

return $menukaart;
}

function bestellingenNaarHtmlTable($data) {

    $bestellingen = '';

    $bestellingen .= "<table>";
    $bestellingen .= "<tr>";
    $bestellingen .= "<th>Bestelling Nummer</th>";
    $bestellingen .= "<th>Klant Gebruikersnaam</th>";
    $bestellingen .= "<th>Klant Naam</th>";
    $bestellingen .= "<th>Personeelslid</th>";
    $bestellingen .= "<th>Datum</th>";
    $bestellingen .= "<th>Status</th>";
    $bestellingen .= "<th>Acties</th>";
    $bestellingen .= "<th>Adres</th>";
    $bestellingen .= "</tr>";

    foreach($data as $rij) {
        $bestellingNummer = $rij['BestellingNummer'];

if (isset($_POST['starten_' . $bestellingNummer])) {
        $bestellingNummer = explode('_', array_keys($_POST)[0])[1];
        updateBestellingStatus($bestellingNummer, 2);
        $rij['status'] = 2;
    } elseif (isset($_POST['onderweg_' . $bestellingNummer])) {
        $bestellingNummer = explode('_', array_keys($_POST)[0])[1];
        updateBestellingStatus($bestellingNummer, 3);
        $rij['status'] = 3;
    } elseif (isset($_POST['afgerond_' . $bestellingNummer])) {
        $bestellingNummer = explode('_', array_keys($_POST)[0])[1];
        updateBestellingStatus($bestellingNummer, 4);
        $rij['status'] = 4;
    }
        
        $klantGebruikersnaam = $rij['klantGebruikersnaam'];
        $klantNaam = $rij['klantNaam'];
        $personeelslid = $rij['personeelslid'];
        $datum = $rij['datum'];
        $status = $rij['status'];
        $adres = $rij['adres'];
        $status1 = 'hallo';
        if ($status == 1) {
            $status1 = "nog niet begonnen";
        } elseif ($status == 2) {
            $status1 = "In de oven";
        } elseif ($status == 3) {
            $status1 = "Onderweg";
        }

        

        $link = "Detailoverzicht.php?bestellingNummer=$bestellingNummer";

        $bestellingen .= "<tr>";
        $bestellingen .= "<td><a href='$link'>$bestellingNummer</a></td>";
        $bestellingen .= "<td><a href='$link'>$klantGebruikersnaam</a></td>";
        $bestellingen .= "<td><a href='$link'>$klantNaam</a></td>";
        $bestellingen .= "<td><a href='$link'>$personeelslid</a></td>";
        $bestellingen .= "<td><a href='$link'>$datum</a></td>";
        $bestellingen .= "<td>$status1</td>";
        $bestellingen .= "<td>";
        $bestellingen .= "<form method='post' action='Bestellingoverzicht.php'>";
        if ($status == 1) {
            $bestellingen .= "<input type='submit' name='starten_$bestellingNummer' value='Starten'>";
        } elseif ($status == 2) {
            $bestellingen .= "<input type='submit' name='onderweg_$bestellingNummer' value='Bezorgen'>";
        } elseif ($status == 3) {
            $bestellingen .= "<input type='submit' name='afgerond_$bestellingNummer' value='Afgerond'>";
        } elseif ($status == 4) {
            $bestellingen .= "<input type='submit' name='afgerond_$bestellingNummer' value='Afgerond' disabled>";
        }
        $bestellingen .= "</form>";
        $bestellingen .= "</td>";
        $bestellingen .= "<td><a href='$link'>$adres</a></td>";
        $bestellingen .= "</tr>";
    }
    $bestellingen .= "</table>";

    return $bestellingen;
}



function detailsNaarHtmlTable($data) {
    $details = '';
    
    $details .= "<h2>Details van de bestellingNr ".$_GET['bestellingNummer']."</h2>";
    $details .= "<table>";
    $details .= "<tr>";
    $details .= "<th>Product Naam</th>";
    $details .= "<th>Aantal</th>";
    $details .= "</tr>";



    foreach($data as $rij) {
        $productNaam = $rij['productNaam'];
        $aantal = $rij['aantal'];

        $details .= "<tr>";
        $details .= "<td>$productNaam</td>";
        $details .= "<td>$aantal</td>";
        $details .= "</tr>";
    }
    
    
    $details .= "</table>";

    return $details;
}

function mandNaarHtmlTable() {
    $mand = '';

    if (isset($_POST['bestellen']) && !empty($_POST['naam']) && !empty($_POST['adres']) && isset($_SESSION['products']) && !empty($_SESSION['products'])) {
        $gebruikersnaam = $_SESSION['gebruiker'];
        voegBestellingToe($gebruikersnaam, $_POST['naam'], $_POST['adres'], $_SESSION['products']);
        unset($_SESSION['products']);
    }


    if (!isset($_SESSION['products']) || empty($_SESSION['products'])) {
        $mand .= "<tr><td colspan='2'>Je mand is leeg</td></tr>";
        $mand .= "</table>";
        return $mand;
    }

    $mand .= "<table>";
    $mand .= "<tr>";
    $mand .= "<th>Product Naam</th>";
    $mand .= "<th>Aantal</th>";
    $mand .= "</tr>";

    foreach($_SESSION['products'] as $productNaam => $aantal) {
        $aantal1 = isset($_POST[$productNaam]) ? $_POST[$productNaam] : $aantal;
        if (isset($_POST['verwijder']) && isset($_POST[$productNaam])) {
            unset($_SESSION['products'][$productNaam]);
            continue;
        }
        if (isset($_POST[$productNaam])) {
            $_SESSION['products'][$productNaam] = (int)$aantal1;
        }

        $mand .= "<tr>";
        $mand .= "<td>$productNaam</td>";
        $mand .= "<td>";
        $mand .= "<form method='post'>";
        $mand .= "<input type='number' name='$productNaam' value='$aantal1' min='1'>";
        $mand .= "</form>";
        $mand .= "<form method='post'>";
        $mand .= "<input type='hidden' name='$productNaam' value=''>";
        $mand .= "<input type='submit' name='verwijder' value='verwijder'>";
        $mand .= "</form>";
        $mand .= "</td>";
        $mand .= "</tr>";
    }
    
    $mand .= "</table>";

    if (isset($_SESSION["gebruiker"]) && !empty($_SESSION["gebruiker"])) {
        
        $gegevens = getGegevens($_SESSION["gebruiker"]);

        $_POST["naam"] = $gegevens["naam"];
        $_POST["adres"] = $gegevens["adres"];
    }

    

    $mand .= "<form method='post'>";
    $mand .= "<h2>Bestelling Plaatsen</h2>";
    $mand .= "<label for='naam'>Naam:</label>";
    $mand .= "<input type='text' id='naam' name='naam' value='{$_POST['naam']}' required>";
    $mand .= "<label for='adres'>Adres:</label>";
    $mand .= "<input type='text' id='adres' name='adres' value='{$_POST['adres']}' required>";
    $mand .= "<input type='submit' name='bestellen' value='Bestellen'>";
    $mand .= "</form>";

    return $mand;
}

function profielHtml($gebruiker) {
    $data = haalGebruikersBestellingenOp($gebruiker);

    $bestellingen = '';

    $bestellingen .= "<table>";
    $bestellingen .= "<tr>";
    $bestellingen .= "<th>Bestelling Nummer</th>";
    $bestellingen .= "<th>Klant Gebruikersnaam</th>";
    $bestellingen .= "<th>Klant Naam</th>";
    $bestellingen .= "<th>Datum</th>";
    $bestellingen .= "<th>Status</th>";
    $bestellingen .= "<th>Adres</th>";
    $bestellingen .= "</tr>";

    foreach($data as $rij) {
        $bestellingNummer = $rij['BestellingNummer'];
        $klantGebruikersnaam = $rij['klantGebruikersnaam'];
        $klantNaam = $rij['klantNaam'];
        $personeelslid = $rij['personeelslid'];
        $datum = $rij['datum'];
        $status = $rij['status'];
        $adres = $rij['adres'];

        if ($status == 1) {
            $status = "nog niet begonnen";
        } elseif ($status == 2) {
            $status = "In de oven";
        } elseif ($status == 3) {
            $status = "Onderweg";
        } elseif ($status == 4) {
            $status = "Bezorgd";
        }

        $bestellingen .= "<tr>";
        $bestellingen .= "<td>$bestellingNummer</td>";
        $bestellingen .= "<td>$klantGebruikersnaam</td>";
        $bestellingen .= "<td>$klantNaam</td>";
        $bestellingen .= "<td>$datum</td>";
        $bestellingen .= "<td>$status</td>";
        $bestellingen .= "<td>$adres</td>";
        $bestellingen .= "</tr>";
    }
    $bestellingen .= "</table>";

    

    return $bestellingen;
}

?>