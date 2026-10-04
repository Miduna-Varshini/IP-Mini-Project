<?php
require 'config.php';
$title = 'VOGUEX - Timeless Style. Modern You.';
$page  = 'home';
$best  = $conn->query("SELECT * FROM products WHERE is_bestseller=1 ORDER BY id LIMIT 4");
$tiles = $conn->query("SELECT image FROM products ORDER BY id LIMIT 8")->fetch_all(MYSQLI_ASSOC);
include 'includes/header.php';
?>
<section class="hero">
  <div class="container hero-inner">
    <div>
      <small>NEW SEASON COLLECTION</small>
      <h1>TIMELESS STYLE.<br>MODERN YOU.</h1>
      <p>Premium quality, perfect fit and effortless style for every moment of your life.</p>
      <div class="btn-row">
        <a href="shop.php" class="btn">SHOP NOW &rarr;</a>
        <a href="shop.php?new=1" class="btn btn-light">EXPLORE NEW ARRIVALS</a>
      </div>
    </div>
    <div class="hero-imgs">
      <?php foreach ([0,1,2] as $i): ?>
        <img src="images/<?= e($tiles[$i]['image'] ?? 'placeholder.jpg') ?>" alt="" onerror="this.onerror=null;this.src='images/placeholder.jpg'">
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="strip">
  <div class="container strip-inner">
    <div><b>FREE SHIPPING</b><span>On orders over $50</span></div>
    <div><b>EASY RETURNS</b><span>30 days return policy</span></div>
    <div><b>EXCLUSIVE OFFERS</b><span>Enjoy up to 30% off</span></div>
    <div><b>24/7 SUPPORT</b><span>We are here for you</span></div>
  </div>
</section>

<div class="container">
  <div class="tiles">
    <div class="tile"><div><h3>MEN COLLECTION</h3><p>Sharp looks for every occasion.</p></div><a href="shop.php?cat=1">SHOP MEN &rarr;</a><img src="images/<?= e($tiles[0]['image'] ?? '') ?>" alt="" onerror="this.style.display='none'"></div>
    <div class="tile"><div><h3>NEW ARRIVALS</h3><p>Discover the latest trends.</p></div><a href="shop.php?new=1">EXPLORE NOW &rarr;</a><img src="images/<?= e($tiles[2]['image'] ?? '') ?>" alt="" onerror="this.style.display='none'"></div>
    <div class="tile"><div><h3>ACCESSORIES</h3><p>Complete your look effortlessly.</p></div><a href="shop.php?cat=3">SHOP ACCESSORIES &rarr;</a><img src="images/<?= e($tiles[4]['image'] ?? '') ?>" alt="" onerror="this.style.display='none'"></div>
    <div class="tile sale-tile"><div><h3>SALE</h3><p>Up to 50% off on selected items.</p></div><a href="shop.php?sale=1">SHOP SALE &rarr;</a></div>
  </div>

  <h2 class="section-title">BEST SELLERS</h2>
  <div class="grid">
    <?php while ($p = $best->fetch_assoc()) echo product_card($p); ?>
  </div>
</div>

<div class="container features">
  <div><b>PREMIUM FABRICS</b>Comfort meets durability</div>
  <div><b>PERFECT FIT</b>Tailored for you</div>
  <div><b>SUSTAINABLE FASHION</b>Better for you &amp; the planet</div>
  <div><b>TRUSTED BY 500K+</b>Happy customers worldwide</div>
</div>
<?php include 'includes/footer.php'; ?>
