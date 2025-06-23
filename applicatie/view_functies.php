<?php
function menuItemsNaarHtmlTable($data) {
$huidigeType = '';
$menukaart = '';

foreach($data as $rij) {
  $naam = $rij['naam'];
  $prijs = $rij['prijs'];
  $type = $rij['type'];

if($huidigeType != $type) {
  $huidigeType = $type;
  $menukaart .= "<h2>$huidigeType</h2>";
}

$menukaart .= "<table>";
$prijs_format = number_format($prijs, 2, ",");
$menukaart .= "<tr><td>$naam</td><td>&euro; $prijs_format</td></tr>";
$menukaart .= "</table>";
}

return $menukaart;
}
?>