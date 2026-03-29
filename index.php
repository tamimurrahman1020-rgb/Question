<?php
require_once __DIR__ . '/config.php';
$fbConfig = firebaseConfigJson();
// Query param routing — hash-free, সব platform এ কাজ করে
$allowedPages = ['home','shop','about','contact','wishlist','product'];
$currentPage  = in_array($_GET['page'] ?? '', $allowedPages) ? $_GET['page'] : 'home';
$productId    = isset($_GET['id']) ? htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8') : '';
?>
<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> | <?= SITE_TAGLINE ?></title>
    <meta name="description" content="<?= SITE_DESCRIPTION ?>">
    <meta name="robots" content="index, follow, max-image-preview:large">

    <!-- Open Graph -->
    <meta property="og:type"        content="website">
    <meta property="og:site_name"   content="<?= SITE_NAME ?>">
    <meta property="og:title"       content="<?= SITE_NAME ?> | <?= SITE_TAGLINE ?>">
    <meta property="og:description" content="<?= SITE_DESCRIPTION ?>">
    <meta property="og:locale"      content="bn_BD">

    <!-- Twitter Card -->
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="<?= SITE_NAME ?> | <?= SITE_TAGLINE ?>">
    <meta name="twitter:description" content="<?= SITE_DESCRIPTION ?>">

    <!-- Structured Data -->
    <script type="application/ld+json">
    {"@context":"https://schema.org","@type":"BookStore","name":"<?= SITE_NAME ?>","description":"<?= SITE_DESCRIPTION ?>","inLanguage":"bn"}
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="page-loader" id="pageLoader"></div>

<!-- Announcement Bar -->
<div id="announcementBar">
    <span id="annText"></span>
    <span id="closeAnn" onclick="document.getElementById('announcementBar').classList.remove('show')">✕</span>
</div>

<header>
    <div class="container">
        <nav class="nav-top">
            <button class="menu-toggle" onclick="toggleMobile()" aria-label="Menu">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <a href="?page=home" class="logo">Ilm<span>Library</span></a>
            <ul class="nav-links">
                <li><a href="?page=home"    class="nav-link" data-page="home">Home</a></li>
                <li><a href="?page=shop"    class="nav-link" data-page="shop">Shop</a></li>
                <li><a href="?page=about"   class="nav-link" data-page="about">About</a></li>
                <li><a href="?page=contact" class="nav-link" data-page="contact">Contact</a></li>
            </ul>
            <div class="search-bar">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" id="desktopSearch" placeholder="Search books, authors..." oninput="handleSearch()" autocomplete="off">
                <div id="desktopSugg" class="search-sugg"></div>
            </div>
            <div class="nav-actions">
                <button class="action-btn" onclick="toggleCart()" aria-label="Cart">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
                    <span class="badge" id="cartBadge">0</span>
                </button>
                <button class="action-btn" onclick="navToWishlist()" aria-label="Wishlist">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                    <span class="badge" id="wishBadge">0</span>
                </button>
            </div>
        </nav>
    </div>
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-search">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" id="mobileSearch" placeholder="Search books..." oninput="handleSearch()" autocomplete="off">
            <div id="mobileSugg" class="search-sugg"></div>
        </div>
        <a href="?page=home"    class="mobile-nav-link" data-page="home"    onclick="toggleMobile()"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>Home</a>
        <a href="?page=shop"    class="mobile-nav-link" data-page="shop"    onclick="toggleMobile()"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>Shop</a>
        <a href="?page=about"   class="mobile-nav-link" data-page="about"   onclick="toggleMobile()"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>About Us</a>
        <a href="?page=contact" class="mobile-nav-link" data-page="contact" onclick="toggleMobile()"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>Contact</a>
    </div>
</header>

<!-- ══ PAGES ══ -->
<?php include __DIR__ . '/pages/home.php';     ?>
<?php include __DIR__ . '/pages/shop.php';     ?>
<?php include __DIR__ . '/pages/about.php';    ?>
<?php include __DIR__ . '/pages/contact.php';  ?>
<?php include __DIR__ . '/pages/wishlist.php'; ?>

<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <div class="logo" style="font-family:'Playfair Display',serif">Ilm<span>Library</span></div>
                <p>ইসলামি জ্ঞানের আলো সবার কাছে পৌঁছে দেওয়াই আমাদের লক্ষ্য। বিশ্বস্ততার সাথে সেবা প্রদান করে আসছি ২০১৫ সাল থেকে।</p>
                <div class="social-links">
                    <a href="#" class="social-link" title="Facebook"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg></a>
                    <a href="#" class="social-link" title="WhatsApp"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
                    <a href="#" class="social-link" title="YouTube"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.6C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.95A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="#0f172a"/></svg></a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="?page=home">Home</a></li>
                    <li><a href="?page=shop">Shop</a></li>
                    <li><a href="?page=about">About Us</a></li>
                    <li><a href="?page=contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Categories</h4>
                <ul>
                    <li><a href="?page=shop">Quran</a></li>
                    <li><a href="?page=shop">Hadith</a></li>
                    <li><a href="?page=shop">Dua</a></li>
                    <li><a href="?page=shop">Stories</a></li>
                    <li><a href="?page=shop">Fiqh</a></li>
                    <li><a href="?page=shop">Seerah</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All Rights Reserved. Made with
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#ef4444" stroke="#ef4444" stroke-width="1"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                for the Ummah.
            </p>
            <div class="footer-badges">
                <span class="footer-badge"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>Secure Payment</span>
                <span class="footer-badge"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>Fast Delivery</span>
                <span class="footer-badge"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>Verified Books</span>
            </div>
        </div>
    </div>
</footer>

<!-- Cart Sidebar -->
<div class="cart-overlay" id="cartSidebar">
    <div class="cart-header">
        <h3><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>Shopping Cart</h3>
        <button class="close-cart" onclick="toggleCart()"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
    </div>
    <div class="cart-body" id="cartListUI"></div>
    <div class="cart-footer">
        <div class="coupon-row">
            <input class="coupon-input" id="couponInput" placeholder="COUPON CODE" maxlength="15">
            <button class="btn-coupon" onclick="applyCoupon()">Apply</button>
        </div>
        <div id="couponMsg"></div>
        <div class="subtotal-row"><span>Subtotal</span><span id="cartSubtotal">Tk 0</span></div>
        <div class="discount-row" id="discountRow" style="display:none"><span>Discount</span><span id="discountAmt">-Tk 0</span></div>
        <div class="delivery-row"><span>🚚 Delivery Charge</span><span id="deliveryCharge">Tk <?= DELIVERY_CHARGE ?></span></div>
        <div class="total-row"><span>Total</span><span id="cartTotal">Tk 0</span></div>
        <button style="background:linear-gradient(135deg,#25D366,#128C7E);color:#fff;padding:13px;border-radius:12px;width:100%;font-weight:700;font-size:.9rem;display:flex;align-items:center;justify-content:center;gap:8px" onclick="checkoutWA()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:20px;height:20px"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            WhatsApp এ অর্ডার করুন
        </button>
    </div>
</div>

<!-- Product Modal -->
<div class="modal" id="pModal">
    <div class="modal-content" id="modalBody"></div>
</div>

<!-- Float Buttons -->
<a class="float-wa" id="floatWA" href="https://wa.me/88<?= DEFAULT_WHATSAPP ?>?text=Assalamu Alaikum" target="_blank" rel="noopener" aria-label="WhatsApp">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
</a>
<button class="back-top" id="backTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 15 12 9 6 15"/></svg>
</button>

<div class="toast" id="toast">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    <span id="toastTxt">Done!</span>
</div>

<!-- Initial page activation (no flash) -->
<script>
(function(){
    var p = '<?= $currentPage ?>';
    var target = (p === 'product') ? 'shop' : (p || 'home');
    var valid = ['home','shop','about','contact','wishlist'];
    if (valid.indexOf(target) === -1) target = 'home';
    var el = document.getElementById('page-' + target);
    if (el) el.classList.add('active');
    document.querySelectorAll('.nav-link,.mobile-nav-link').forEach(function(l){
        var pg = l.getAttribute('data-page') || '';
        if (pg === target) l.classList.add('active');
    });
})();
</script>

<!-- Firebase SDKs -->
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>

<!-- Firebase Init + Constants (PHP inject করছে) -->
<script>
const firebaseConfig  = <?= $fbConfig ?>;
const DEFAULT_WHATSAPP = '<?= DEFAULT_WHATSAPP ?>';
const DELIVERY_CHARGE  = <?= DELIVERY_CHARGE ?>;
const ITEMS_PER_PAGE   = <?= ITEMS_PER_PAGE ?>;
// PHP থেকে initial page/product — JS navigation শুরু করবে এখান থেকে
const INITIAL_PAGE     = '<?= $currentPage ?>';
const INITIAL_PRODUCT  = '<?= $productId ?>';

firebase.initializeApp(firebaseConfig);
const db = firebase.database();
</script>

<!-- App JS -->
<script src="js/app.js"></script>

</body>
</html>
