<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'registreren_inloggen_server.php';

if (isset($_POST["registreren"])){
    registreren();
} elseif (isset($_POST["inloggen"])){
    inloggen();
}

var_dump($_SESSION);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 
    if (isset($_SESSION["gebruiker"])){
        $gebruiker = $_SESSION['gebruiker'];
        echo "<p>$gebruiker is ingelogd</p>";
    }
    
    ?>
    <h1>Registreren</h1>
    <?php
    $_SESSION['melding'] ?? '';
    unset($_SESSION['melding']);
    ?>
    <form method="post" action="">
        <div>
            <label for="naam">Naam:</label>
            <input id="naam" name="naam" type="text" required>
        </div>
        <div>
            <label for="wachtwoord">Wachtwoord:</label>
            <input id="wachtwoord" name="wachtwoord" type="password">
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
</body>
</html>