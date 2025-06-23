<?php
require_once 'db_connectie.php';

function haalMenuItemsOp() {
    $db = maakVerbinding();
    $query = 'select name as naam, price as prijs, type_id as type from Product order by type_id';
    $data = $db->query($query);

    return $data->fetchAll(PDO::FETCH_ASSOC);
}
?>