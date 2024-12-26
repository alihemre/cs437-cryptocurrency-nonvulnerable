<?php
// Örnek dosya: clickable-columns-with-popularity.php

$title = "Top 100 Prices - Clickable Headers with Popularity";
include './header.php';

/**
 * cURL ile HTTP GET isteği yapıp yanıtı döndüren yardımcı fonksiyon
 */
function curl_get_contents($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Coingecko bazen User-Agent başlığı olmayan isteklere sınırlama uygulayabiliyor.
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (CryptoApp)');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'cURL error: ' . curl_error($ch);
        return false;
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpCode !== 200) {
        echo "HTTP status code: $httpCode<br>";
        echo "Response: $response<br>";
        return false;
    }

    curl_close($ch);
    return $response;
}

// 1) İlk 100 coini USD cinsinden çek
$usd_endpoint = "https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&order=market_cap_desc&per_page=100&page=1&sparkline=false";
$responseUSD = curl_get_contents($usd_endpoint);
if ($responseUSD === false) {
    echo "USD verisi çekilirken bir sorun oluştu!";
    exit;
}
$dataUSD = json_decode($responseUSD, true);

// 2) Aynı 100 coini TRY cinsinden çek
$try_endpoint = "https://api.coingecko.com/api/v3/coins/markets?vs_currency=try&order=market_cap_desc&per_page=100&page=1&sparkline=false";
$responseTRY = curl_get_contents($try_endpoint);
if ($responseTRY === false) {
    echo "TRY verisi çekilirken bir sorun oluştu!";
    exit;
}
$dataTRY = json_decode($responseTRY, true);

// 3) TRY verilerini, coin 'id' üzerinden bir dizide sakla
$tryPrices = [];
foreach ($dataTRY as $coin) {
    $coinId = $coin['id'];
    $tryPrices[$coinId] = $coin['current_price'];
}

// 4) USD verileri üzerinden dönüp tek bir $prices dizisi oluştur
$prices = [];
foreach ($dataUSD as $index => $coin) {
    $coinId     = $coin['id'];
    $coinName   = $coin['name'];
    $coinSymbol = strtoupper($coin['symbol']);
    $priceUSD   = $coin['current_price'];
    $priceTRY   = isset($tryPrices[$coinId]) ? $tryPrices[$coinId] : 0;

    $prices[] = [
        'id'              => $coinId,
        'name'            => $coinName,
        'symbol'          => $coinSymbol,
        'price_usd'       => $priceUSD,
        'price_try'       => $priceTRY,
        // Sıralamayı orijinal listeye göre yapmak için index tutuyoruz
        'original_index'  => $index
    ];
}

/*
  (Opsiyonel) Sunucu tarafı arama
  - Eğer sadece canlı arama (JS) istiyorsanız, bu bölümü kaldırabilirsiniz.
*/
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
if (!empty($searchQuery)) {
    $lowerSearch = mb_strtolower($searchQuery);
    $prices = array_filter($prices, function($coin) use ($lowerSearch) {
        $coinName   = mb_strtolower($coin['name']);
        $coinSymbol = mb_strtolower($coin['symbol']);
        return (strpos($coinName, $lowerSearch) !== false || 
                strpos($coinSymbol, $lowerSearch) !== false);
    });
}
?>

<div class="content">
  <main>
    <h2>Top 100 Cryptocurrency Prices (USD & TRY)</h2>
    
    <!-- Arama formu (Sunucu tarafı arama için opsiyonel) -->
    <form method="GET" style="margin-bottom: 20px;">
      <input 
        type="text" 
        id="searchInput"
        name="search" 
        placeholder="Search by coin name or symbol..." 
        value="<?php echo htmlspecialchars($searchQuery); ?>"
      />
      <button type="submit">Search</button>
    </form>

    <!-- Sonuç Tablosu -->
    <table>
      <thead>
        <tr>
          <!-- 1. Sütun: Popularity (tıklanabilir) -->
          <th data-sort="original_index">Popularity</th>
          <th data-sort="name">Coin</th>
          <th data-sort="symbol">Symbol</th>
          <th data-sort="price_usd">Price (USD)</th>
          <th data-sort="price_try">Price (TRY)</th>
        </tr>
      </thead>
      <tbody id="coinTableBody">
        <?php if (!empty($prices)): ?>
          <?php foreach ($prices as $coin): ?>
            <tr>
              <!-- Orijinal index'e +1 vererek 1’den başlayarak gösteriyoruz -->
              <td><?php echo ($coin['original_index'] + 1); ?></td>
              <td><?php echo htmlspecialchars($coin['name']); ?></td>
              <td><?php echo htmlspecialchars($coin['symbol']); ?></td>
              <td><?php echo number_format($coin['price_usd'], 2); ?></td>
              <td><?php echo number_format($coin['price_try'], 2); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="5">
              No coins found for "<?php echo htmlspecialchars($searchQuery); ?>"
            </td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </main>
</div>

<!-- Canlı arama ve tıklanabilir sütun başlıkları ile sıralama için JS kodları -->
<script>
  // 1) PHP'den gelen coin listesini (filtrelenmiş de olabilir) JS'e aktar.
  const allCoins = <?php echo json_encode(array_values($prices), JSON_UNESCAPED_UNICODE); ?>;
  
  // "orijinal" listeyi saklamak isterseniz (ilave gereksinimler için)
  const originalCoins = [...allCoins];

  // HTML elemanlarını seçelim
  const searchInput   = document.getElementById('searchInput');
  const coinTableBody = document.getElementById('coinTableBody');
  
  // Sıralama için "en son tıklanan sütun" ve "sıralama yönü" değişkenlerini tanımlayalım.
  let currentSortColumn = null;   // Örn: 'original_index', 'name', 'symbol', 'price_usd', 'price_try'
  let currentSortDir    = 'asc';  // 'asc' veya 'desc'

  // 2) Tabloyu yeniden çizme fonksiyonu
  function renderTable(coins) {
    coinTableBody.innerHTML = '';

    if (coins.length === 0) {
      const noRow = document.createElement('tr');
      noRow.innerHTML = '<td colspan="5">No coins found</td>';
      coinTableBody.appendChild(noRow);
      return;
    }

    coins.forEach(coin => {
      const row = document.createElement('tr');
      row.innerHTML = `
        <td>${coin.original_index + 1}</td>
        <td>${coin.name}</td>
        <td>${coin.symbol}</td>
        <td>${parseFloat(coin.price_usd).toFixed(2)}</td>
        <td>${parseFloat(coin.price_try).toFixed(2)}</td>
      `;
      coinTableBody.appendChild(row);
    });
  }

  // 3) Filtre + Sıralama işlemlerini tek bir fonksiyonda topluyoruz
  function filterAndSortCoins() {
    // a) Arama sorgusu
    const query = searchInput.value.toLowerCase();
    // b) Filtreleme
    let filtered = originalCoins.filter(coin => {
      const name   = coin.name.toLowerCase();
      const symbol = coin.symbol.toLowerCase();
      return name.includes(query) || symbol.includes(query);
    });

    // c) Sıralama (currentSortColumn varsa)
    if (currentSortColumn) {
      filtered.sort((a, b) => {
        const valA = a[currentSortColumn];
        const valB = b[currentSortColumn];

        // Numerik mi, string mi diye kontrol edelim
        if (typeof valA === 'number' && typeof valB === 'number') {
          // Numerik karşılaştırma
          return currentSortDir === 'asc' ? (valA - valB) : (valB - valA);
        } else {
          // String karşılaştırma (örneğin, name veya symbol)
          const strA = String(valA).toLowerCase();
          const strB = String(valB).toLowerCase();
          if (strA < strB) return currentSortDir === 'asc' ? -1 : 1;
          if (strA > strB) return currentSortDir === 'asc' ? 1 : -1;
          return 0;
        }
      });
    }

    // d) Tablonun güncellenmesi
    renderTable(filtered);
  }

  // 4) "Arama" kutusu üzerinde "keyup" (veya "input") olayını dinliyoruz.
  searchInput.addEventListener('keyup', function() {
    filterAndSortCoins();
  });

  // 5) Sütun başlıklarına tıklanma olaylarını ekleyelim.
  //    Tüm <th> etiketlerini seçip data-sort attribüsüne bakalım.
  const thElements = document.querySelectorAll('thead th[data-sort]');
  thElements.forEach(th => {
    th.addEventListener('click', () => {
      const sortField = th.getAttribute('data-sort');

      // Eğer aynı sütuna tekrar tıklanıyorsa, yönü değiştir (toggle)
      if (currentSortColumn === sortField) {
        currentSortDir = (currentSortDir === 'asc') ? 'desc' : 'asc';
      } else {
        // Farklı bir sütuna geçiş yapılıyorsa, varsayılan olarak asc
        currentSortColumn = sortField;
        currentSortDir    = 'asc';
      }

      filterAndSortCoins();
    });
  });

  // 6) Sayfa ilk yüklendiğinde tabloyu gösterelim (hiçbir sütunda sıralama olmadan)
  //    Yani popülerlik sütunu, orijinal_index'e göre "asc" olarak zaten sıralı geliyor.
  renderTable(originalCoins);
</script>

<?php include './footer.php'; ?>
