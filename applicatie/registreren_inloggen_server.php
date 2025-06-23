<?php
require_once 'db_connectie.php';

function registreren() {
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$melding = '';
if (isset($_POST["registreren"])){
    $fouten = [];

    $naam = $_POST['naam'];
    $wachtwoord = $_POST['wachtwoord'];

    if(strlen($naam) < 2){
        $fouten[] = "Naam moet minstens 2 karakters hebben";
    }

    if(strlen($naam) > 12){
        $fouten[] = "Naam is te lang";
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

        $sql = "INSERT INTO [User] (username, [password], first_name, last_name, [role])
                VALUES (:naam, :passwordhash, 'John', 'Doe', 'Client')";

        $query = $db->prepare($sql);

        $succes = $query->execute([
            'naam' => $naam,
            'passwordhash' => $passwordhash
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