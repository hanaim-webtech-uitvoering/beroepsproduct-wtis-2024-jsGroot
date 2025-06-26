<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'data_functies.php';

if (isset($_POST["registreren"])){
    registreren();
} elseif (isset($_POST["inloggen"])){
    inloggen();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <header>
        <div>
            <a href='index.php'><button>home</button></a>
        </div>
        <?php
        if (isset($_SESSION["gebruiker"])) {
            $gebruiker = $_SESSION['gebruiker'];
            echo "<p> gebruiker $gebruiker is ingelogd</p>";
        echo "
        <div>
            <a href='mand.php'><button>mand</button></a>
        </div>
        <div>
            <a href='profiel.php'><button>profiel</button></a>
        </div>
        <form method='post'>
            <button type='submit' name='uitloggen'>uitloggen</button>
        </form>
        ";
        } elseif (isset($_SESSION["personeel_gebruiker"])) {
            $personeel_gebruiker = $_SESSION['personeel_gebruiker'];
            echo "<p>personeel $personeel_gebruiker is ingelogd</p>
            <div>
                <a href='Bestellingoverzicht.php'><button>Bestellingoverzicht</button></a>
            </div>
            <form method='post'>
                <button type='submit' name='uitloggen'>uitloggen</button>
            </form>
            ";
        } else {
            echo"
            <div>
            <a href='registreren.php'><button>registreren/inloggen</button></a>
        </div>
        ";
        }

        if (isset($_POST['uitloggen'])) {
            session_destroy();
            header("Location: index.php");
        }
        ?>
    </header>


    <?php 
    if (isset($_SESSION["gebruiker"])){
        $gebruiker = $_SESSION['gebruiker'];
        echo "<p>$gebruiker is ingelogd</p>";
    }
    
    ?>
    <h1>Registreren</h1>
    <?php
    echo $_SESSION['melding'] ?? '';
    unset($_SESSION['melding']);
    ?>
    <form method="post" action="">
        <div>
            <label for="naam">Voornaam:</label>
            <input id="naam" name="voornaam" type="text" required>
        </div>
        <div>
            <label for="naam">Achternaam:</label>
            <input id="naam" name="achternaam" type="text" required>
        </div>
        <div>
            <label for="naam">Gebruikersaam:</label>
            <input id="naam" name="naam" type="text" required>
        </div>
        <div>
            <label for="naam">Adres:</label>
            <input id="naam" name="adres" type="text" required>
        </div>
        <div>
            <label for="wachtwoord">Wachtwoord:</label>
            <input id="wachtwoord" name="wachtwoord" type="password" required>
        </div>
        <input name="registreren" type="submit">
    </form>

    
    <h1>Inloggen</h1>
    <form method="post" action="">
    <div>
            <label for="naam">Naam:</label>
            <input id="naam" name="naam" type="text" required>
        </div>
        <div>
            <label for="wachtwoord">Wachtwoord:</label>
            <input id="wachtwoord" name="wachtwoord" type="password">
        </div>
        <input name="inloggen" type="submit">
    </form>

    <footer>
        <a href="privacy.html">©privacybeleid</a>
    </footer>
</body>
</html>