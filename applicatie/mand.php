<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'data_functies.php';
require_once 'view_functies.php';



$mandItems = mandNaarHtmlTable();
?>



<?php
    $_SESSION['melding'] ?? '';
    unset($_SESSION['melding']);
?>

<!DOCTYPE html>
<html lang="nl">

  <head>
    <meta charset="UTF-8" />
    <title>Restaurantmenu</title>
    <script src="https://kit.fontawesome.com/e9cf9c1d51.js" crossorigin="anonymous"></script>
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

    <?= $mandItems ?>

    <footer>
        <a href="privacy.html">©privacybeleid</a>
    </footer>
    
  </body>
</html>

