let planten = [];

// Laad producten van de database via API
async function loadPlanten() {
  try {
    const response = await fetch('api/get-products.php');
    if (!response.ok) {
      throw new Error('Fout bij laden van producten');
    }
    planten = await response.json();
    console.log('Producten geladen van database:', planten);
    
    // Als de pagina al geladen is, toon de producten
    const productGrid = document.getElementById("producten");
    if (productGrid && typeof toonPlanten === 'function') {
      toonPlanten();
    }
  } catch (error) {
    console.error('Fout bij laden producten:', error);
  }
}

// Laad producten wanneer dit script geladen wordt
loadPlanten();
