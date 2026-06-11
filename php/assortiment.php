<?php
require_once './partials/dbconnection.php';

$searchTerm = '';
$standplaats = '';
$prijsOrder = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $searchTerm = trim($_POST['search'] ?? '');
  $standplaats = $_POST['standplaats'] ?? '';
  $prijsOrder = $_POST['prijs'] ?? '';
}

$conditions = ["voorraad > 0"];
$params = [];
$types = "";

if ($searchTerm !== '') {
  $conditions[] = "naam LIKE ?";
  $params[] = "%" . $searchTerm . "%";
  $types .= "s";
}

if ($standplaats !== '') {
  $conditions[] = "standplaats = ?";
  $params[] = $standplaats;
  $types .= "s";
}

$where = implode(" AND ", $conditions);

$order = "ORDER BY naam";
if ($prijsOrder === 'asc')
  $order = "ORDER BY verkoopprijs_eur ASC";
if ($prijsOrder === 'desc')
  $order = "ORDER BY verkoopprijs_eur DESC";

$sql = "SELECT id, naam, verkoopprijs_eur as prijs, standplaats, overview_image as afbeelding FROM planten WHERE $where $order LIMIT 50";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$standplaats_result = $conn->query("SELECT DISTINCT standplaats FROM planten WHERE voorraad > 0 ORDER BY standplaats");
?>
<!doctype html>
<html lang="nl">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="style.css" />
  <title>Nora's Flora</title>
</head>

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
    <form method="POST" class="filter-form">
      <label for="zoeken">Zoeken op plantnaam</label>
      <input type="text" id="zoeken" name="search" placeholder="Vul een plantnaam in"
        value="<?php echo htmlspecialchars($searchTerm); ?>" />

      <label for="standplaats">Standplaats</label>
      <select name="standplaats" id="standplaats">
        <option value="">Alles tonen</option>
        <?php
        if ($standplaats_result && $standplaats_result->num_rows > 0) {
          while ($row = $standplaats_result->fetch_assoc()) {
            $selected = ($_POST['standplaats'] ?? '') === $row['standplaats'] ? 'selected' : '';
            echo '<option value="' . htmlspecialchars($row['standplaats']) . '" ' . $selected . '>'
              . htmlspecialchars($row['standplaats']) . '</option>';
          }
        }
        ?>
      </select>

      <label for="prijs">Prijs</label>
      <select name="prijs" id="prijs">
        <option value="">Alles tonen</option>
        <option value="asc" <?php echo (($_POST['prijs'] ?? '') === 'asc') ? 'selected' : ''; ?>>Oplopend</option>
        <option value="desc" <?php echo (($_POST['prijs'] ?? '') === 'desc') ? 'selected' : ''; ?>>Aflopend</option>
      </select>

      <button type="submit">Filteren</button>
    </form>

    <div id="assortimentContainer">
      <div id="producten">
        <?php
        if ($result && $result->num_rows > 0) {
          while ($product = $result->fetch_assoc()) {
            $afbeelding = 'images_planten/' . $product['afbeelding'];
            $prijs = number_format($product['prijs'], 2, ',', '');
            echo '<div class="product">
              <img src="' . $afbeelding . '" alt="' . htmlspecialchars($product['naam']) . '">
              <p>' . htmlspecialchars($product['naam']) . '<br />
              <small>' . htmlspecialchars($product['standplaats']) . '</small><br />
              €' . $prijs . '</p>
              <button onclick="voegToe(' . $product['id'] . ', \'' . addslashes($product['naam']) . '\', ' . $product['prijs'] . ')">🛒</button>
            </div>';
          }
        }
        ?>
      </div>
    </div>
  </main>

  <footer id="footerContainer">
    <div id="footerLinks">
      <p>Email&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: contact@noraflora.com</p>
      <p>Telefoon&nbsp;&nbsp;: 06 12345678</p>
      <p>Adres&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Zwolle, pannenkoekendijk 420 B</p>
    </div>
    <div id="footerRechts">
      <h2>Openingstijden</h2>
      <p>Ma - Vr : 12:00 - 17:00</p>
      <p>Zaterdag : 10:00 - 17:00</p>
    </div>
  </footer>

  <script src="script.js"></script>
</body>

</html>