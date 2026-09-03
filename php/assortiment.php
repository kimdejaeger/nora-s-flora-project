<!doctype html>
<html lang="nl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Bekijk het volledige bloemenassortiment van Nora's Flora. Filter op soort, standplaats en prijs." />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500;1,600&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="style.css" />
  <title>Assortiment – Nora's Flora</title>
</head>

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
  $params[] = $searchTerm . "%";
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

<body>

  <!-- ── HEADER ── -->
  <header>
    <a href="index.html" id="divLogo" aria-label="Nora's Flora – naar de homepage"></a>
    <nav id="divNavigatie" aria-label="Hoofdnavigatie">
      <a href="index.html">Home</a>
      <a href="assortiment.php" aria-current="page">Assortiment</a>
      <a href="contact.html">Contact</a>
      <a href="shoppingcart.html" class="nav-cart">Winkelmandje</a>
      <span id="cartCount" aria-live="polite"></span>
    </nav>
  </header>

  <main>

    <!-- ── PAGE HERO ── -->
    <div class="assortiment-header">
      <h1>Ons assortiment</h1>
      <p>Verse planten en bloemen, dagelijks aangevuld</p>
    </div>

    <div id="assortimentContainer">

      <!-- ── FILTER BAR ── -->
      <form method="POST" class="filter-form" role="search" aria-label="Producten filteren">

        <div class="filter-group">
          <label for="zoeken">Zoeken</label>
          <input
            type="text"
            id="zoeken"
            name="search"
            placeholder="Zoek op plantnaam..."
            value="<?php echo htmlspecialchars($searchTerm); ?>"
            autocomplete="off"
          />
        </div>

        <div class="filter-group">
          <label for="standplaats">Standplaats</label>
          <select name="standplaats" id="standplaats">
            <option value="">Alle standplaatsen</option>
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
        </div>

        <div class="filter-group">
          <label for="prijs">Prijs</label>
          <select name="prijs" id="prijs">
            <option value="">Standaard volgorde</option>
            <option value="asc"  <?php echo (($_POST['prijs'] ?? '') === 'asc')  ? 'selected' : ''; ?>>Laagste eerst</option>
            <option value="desc" <?php echo (($_POST['prijs'] ?? '') === 'desc') ? 'selected' : ''; ?>>Hoogste eerst</option>
          </select>
        </div>

        <button type="submit">Filteren</button>
      </form>

      <!-- ── PRODUCT GRID ── -->
      <div id="producten" role="list" aria-label="Producten">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($product = $result->fetch_assoc()) {
                $afbeelding  = 'images_planten/' . htmlspecialchars($product['afbeelding']);
                $naam        = htmlspecialchars($product['naam']);
                $standplaats = htmlspecialchars($product['standplaats']);
                $prijs       = number_format($product['prijs'], 2, ',', '');
                $id          = (int) $product['id'];
                $prijsRaw    = (float) $product['prijs'];
                $naamJs      = addslashes($product['naam']);

                echo '<div class="product" role="listitem">
                  <div class="product-image-wrap">
                    <img src="' . $afbeelding . '" alt="' . $naam . '" loading="lazy" />
                    <button
                      class="product-add-btn"
                      onclick="voegToe(' . $id . ', \'' . $naamJs . '\', ' . $prijsRaw . ')"
                      aria-label="' . $naam . ' toevoegen aan winkelmandje"
                      title="Toevoegen aan winkelmandje"
                    >+</button>
                  </div>
                  <div class="product-info">
                    <p class="product-name">' . $naam . '</p>
                    <p class="product-subtitle">' . $standplaats . '</p>
                    <p class="product-price">€' . $prijs . '</p>
                  </div>
                </div>';
            }
        } else {
            echo '<p style="grid-column:1/-1;text-align:center;color:var(--ink-muted);padding:64px 0;font-size:15px;">
                    Geen planten gevonden voor deze selectie.
                  </p>';
        }
        ?>
      </div>

    </div>
  </main>

  <!-- ── FOOTER ── -->
  <footer>
    <div id="footerContainer">
      <div class="footer-brand">
        <p>Verse bloemen met liefde samengesteld in Zwolle. Elke dag opnieuw.</p>
        <div class="footer-social">
          <a href="#" aria-label="Instagram">📸</a>
          <a href="#" aria-label="Facebook">📘</a>
          <a href="#" aria-label="Pinterest">📌</a>
        </div>
      </div>
      <div id="footerLinks">
        <span class="footer-col-title">Contact</span>
        <div class="footer-contact-grid">
          <span class="footer-label">Email</span>
          <span class="footer-value">contact@noraflora.com</span>
          <span class="footer-label">Tel</span>
          <span class="footer-value">06 12345678</span>
          <span class="footer-label">Adres</span>
          <span class="footer-value">Pannenkoekendijk 420 B, Zwolle</span>
        </div>
      </div>
      <div id="footerRechts">
        <span class="footer-col-title">Openingstijden</span>
        <p>Maandag – Vrijdag &nbsp;: 12:00 – 17:00</p>
        <p>Zaterdag &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 10:00 – 17:00</p>
        <p>Zondag &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: Gesloten</p>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© 2025 Nora's Flora — Alle rechten voorbehouden</span>
      <nav class="footer-legal-links" aria-label="Juridische links">
        <a href="#">Privacybeleid</a>
        <a href="#">Algemene voorwaarden</a>
        <a href="#">Retourbeleid</a>
      </nav>
    </div>
  </footer>

  <script src="script.js"></script>
</body>
</html>
