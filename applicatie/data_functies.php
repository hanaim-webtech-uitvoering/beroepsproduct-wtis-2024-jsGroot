<?php
require_once 'db_connectie.php';

function haalMenuItemsOp() {
    $db = maakVerbinding();
    $query = 'select name as naam, price as prijs, type_id as type from Product order by type_id';
    $data = $db->query($query);

    return $data->fetchAll(PDO::FETCH_ASSOC);
}



function haalBestellingenOp() {
    $db = maakVerbinding();
    $query = 'select order_id as BestellingNummer, client_username as klantGebruikersnaam, client_name as klantNaam, personnel_username as personeelslid, datetime as datum, status as status, address as adres from pizza_order';
    $data = $db->query($query);

    return $data->fetchAll(PDO::FETCH_ASSOC);
}



function haalDetailsOp($bestellingNummer) {
    $db = maakVerbinding();
    $query = 'select product_name as productNaam, quantity as aantal, order_id as id from Pizza_Order_Product where order_id = :bestellingNummer';
    $stmt = $db->prepare($query);
    $stmt->execute(['bestellingNummer' => $bestellingNummer]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



function getGegevens($gebruiker) {
    $db = maakVerbinding();
    $sql =
          "SELECT address as adres, first_name as naam FROM [User] WHERE username = :username";
        $stmt = $db->prepare($sql);
        $stmt->execute([":username" => $gebruiker]);
        $gegevens = $stmt->fetch(PDO::FETCH_ASSOC);
    return $gegevens;
}

function voegBestellingToe($gebruiker, $naam, $adres, $producten) {

    $db = maakVerbinding();
    $sql = "INSERT INTO pizza_order (client_username, client_name, personnel_username, datetime, address, status) VALUES (:gebruiker, :naam, :personeel, GETDATE(), :adres, :status)";
    $stmt = $db->prepare($sql);
    $stmt->execute(['gebruiker' => $gebruiker, 'naam' => $naam, 'personeel' => 'abrouwer', 'adres' => $adres, 'status' => '1']);

    $orderId = $db->lastInsertId();

    foreach ($producten as $product=> $aantal) {
        $sql = "INSERT INTO Pizza_Order_Product (order_id, product_name, quantity) VALUES (:order_id, :product_name, :quantity)";
        $stmt = $db->prepare($sql);
        $stmt->execute(['order_id' => $orderId, 'product_name' => str_replace("_", " ", $product), 'quantity' => $aantal]);
    }
}

function haalGebruikersBestellingenOp() {
    $db = maakVerbinding();
    $sql = 'select order_id as BestellingNummer, client_username as klantGebruikersnaam, client_name as klantNaam, personnel_username as personeelslid, datetime as datum, status as status, address as adres from pizza_order where client_username = :gebruiker';
    $stmt = $db->prepare($sql);
    $stmt->execute(['gebruiker' => $_SESSION['gebruiker']]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateBestellingStatus($bestellingNummer, $status) {
    $db = maakVerbinding();
    $sql = 'UPDATE pizza_order SET status = :status WHERE order_id = :bestellingNummer';
    $stmt = $db->prepare($sql);
    $stmt->execute(['status' => $status, 'bestellingNummer' => $bestellingNummer]);
}

function registreren() {
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$melding = '';
if (isset($_POST["registreren"])){
    $fouten = [];

    $naam = htmlspecialchars($_POST['naam']);
    $voornaam = htmlspecialchars($_POST['voornaam']);
    $achternaam = htmlspecialchars($_POST['achternaam']);
    $adres = htmlspecialchars($_POST['adres']);
    $wachtwoord = htmlspecialchars($_POST['wachtwoord']);

    if(strlen($naam) < 2){
        $fouten[] = "Naam moet minstens 2 karakters hebben";
    }

    if(strlen($naam) > 12){
        $fouten[] = "Naam is te lang";
    }

    if(strlen($voornaam) < 2){
        $fouten[] = "Voornaam moet minstens 2 karakters hebben";
    }

    if(strlen($voornaam) > 12){
        $fouten[] = "Voornaam is te lang";
    }

    if(strlen($achternaam) < 2){
        $fouten[] = "Achternaam moet minstens 2 karakters hebben";
    }

    if(strlen($achternaam) > 12){
        $fouten[] = "Achternaam is te lang";
    }

    if(strlen($adres) < 2){
        $fouten[] = "Achternaam moet minstens 2 karakters hebben";
    }

    if(strlen($adres) > 50){
        $fouten[] = "Achternaam is te lang";
    }

    if(strlen($wachtwoord) < 8){
        $fouten[] = "Wachtwoord moet minstens 8 karakters hebben";
    }

    if (count($fouten) > 0){
        $melding .= "Er zijn fouten <ul>";
        foreach($fouten as $fout){
            $melding .= "<li>$fout</li>";
        }
        $melding .= "</ul>";

        $_SESSION['melding'] = $melding;
    }
    else{
        $passwordhash = password_hash($wachtwoord, PASSWORD_DEFAULT);

        $db = maakVerbinding();

        $sql = "INSERT INTO [User] (username, [password], first_name, last_name, [address], [role])
                VALUES (:naam, :passwordhash, :voornaam, :achternaam, :adres, 'Client')";

        $query = $db->prepare($sql);

        $succes = $query->execute([
            'naam' => $naam,
            'passwordhash' => $passwordhash,
            'voornaam' => $voornaam,
            'achternaam' => $achternaam,
            'adres' => $adres
        ]);

        if ($succes){
            $melding = "Je bent geregistreerd!";
            $_SESSION['melding'] = $melding;
            $_SESSION['gebruiker'] = $naam;
            header("Location: index.php");

        }
        else{
            $melding = "Registratie is mislukt";
            $_SESSION['melding'] = $melding;

        }
        
        // if (password_verify($wachtwoord, $passwordhash)){
        //     echo "wachtwoord is juist";
        // }
        // else{
        //     echo "wachtwoord is onjuist";
        // }
    }
}
}

function inloggen() {
    if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_POST["inloggen"])){
    $naam = $_POST['naam'];
    $wachtwoord = $_POST['wachtwoord'];

    $db = maakVerbinding();

        $sql = "SELECT [password], [role] FROM [User] WHERE username = :naam";

        $query = $db->prepare($sql);

        $query->execute([
            'naam' => $naam,
        ]);

        if ($rij = $query->fetch()){
            $passwordhash = $rij['password'];
            $rol = $rij['role'];

            if (password_verify($wachtwoord, $passwordhash)){
                if ($rol === 'Client') {
                    $_SESSION["gebruiker"] = $naam;
                    echo "je bent ingelogd";
                    header("Location: index.php");
                } elseif ($rol === 'personnel') {
                    $_SESSION["personeel_gebruiker"] = $naam;
                    echo "je bent ingelogd";
                    header("Location: index.php");
                }                
            }
            else{
                echo "wachtwoord is onjuist";
            }
        }
        else{
            echo 'gebruiker niet gevonden';
        }
}
}
?>