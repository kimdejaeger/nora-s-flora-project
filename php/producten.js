// ============================================================
//  Nora's Flora — producten.js
//  Loads products from the PHP API endpoint
// ============================================================

let planten = [];

async function loadPlanten() {
  try {
    const response = await fetch('api/get-products.php');
    if (!response.ok) throw new Error('Fout bij laden van producten');
    planten = await response.json();
    console.log('Producten geladen:', planten);
  } catch (error) {
    console.error('Fout bij laden producten:', error);
  }
}

// Load on script init
loadPlanten();
