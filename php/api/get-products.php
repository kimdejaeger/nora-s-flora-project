<?php
header('Content-Type: application/json');
require_once '../partials/dbconnection.php';

$query = "SELECT id, naam, verkoopprijs_eur as prijs, overview_image as afbeelding FROM planten WHERE voorraad > 0 ORDER BY naam";
$result = $conn->query($query);

$producten = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $producten[] = $row;
    }
}

echo json_encode($producten);
$conn->close();
?>
