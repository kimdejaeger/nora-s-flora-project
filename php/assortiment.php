<!doctype html>
<html lang="nl">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css" />
  <title>Nora's Flora</title>

</head>

<?php
require_once './partials/dbconnection.php';

$query = "SELECT id, naam, verkoopprijs_eur as prijs, overview_image as afbeelding FROM planten WHERE voorraad > 0 ORDER BY naam";
$result = $conn->query($query);
?>

<body>

  <header>
    <div id="divLogo">
      <img id="imgLogo" src="images/Nora'sFloraLogo.png" alt="Nora'sFloraLogo" />
    </div>
    <div id="divNavigatie">
      <a href="index.html">Home</a>
      <a href="assortiment.php">Assortiment</a>
      <a href="contact.html">Contact</a>
      <a href="shoppingcart.html">Winkelmandje</a>
    </div>
  </header>
  <main>
    <div id="assortimentContainer">

      <div id="producten">
        <?php
        if ($result && $result->num_rows > 0) {
          while ($product = $result->fetch_assoc()) {
            $afbeelding = 'images_planten/' . $product['afbeelding'];
            $prijs = number_format($product['prijs'], 2, ',', '');
            echo '<div class="product">
            <img src="' . $afbeelding . '" alt="' . $product['naam'] . '">
            <p>' . $product['naam'] . '<br />€' . $prijs . '</p>
            <button onclick="voegToe(' . $product['id'] . ', \'' . addslashes($product['naam']) . '\', ' . $product['prijs'] . ')">🛒</button></div>';
          }
        }
        ?>
      </div>
  </main>
  <footer id="footerContainer">
    <div id="footerLinks">
      <p>
        Email&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: contact@noraflora.com
      </p>
      <p>Telefoon&nbsp;&nbsp;: 06 12345678</p>
      <p>
        Adres&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Zwolle, pannenkoekendijk
        420 B
      </p>
    </div>
    <div id="footerRechts">
      <h2>Openingstijden</h2>
      <p>Ma - Vr : 12:00 - 17:00</p>
      <p>Zaterdag : 10:00 - 17:00</p>
    </div>
  </footer>
</body>
<script src="script.js"></script>

</html>