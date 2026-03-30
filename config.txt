<?php
// ══════════════════════════════════════════════
//  IlmLibrary — Firebase Configuration
// ══════════════════════════════════════════════

define('FIREBASE_API_KEY',        'AIzaSyBdPXCZBkr-CLi4KigcyzlXBRUT14ony54');
define('FIREBASE_AUTH_DOMAIN',    'tornament-bc970-default-rtdb.firebaseapp.com');
define('FIREBASE_DATABASE_URL',   'https://tornament-bc970-default-rtdb.firebaseio.com');
define('FIREBASE_PROJECT_ID',     'tornament-bc970-default-rtdb');
define('FIREBASE_STORAGE_BUCKET', 'tornament-bc970-default-rtdb.appspot.com');
define('FIREBASE_MESSAGING_ID',   '000');
define('FIREBASE_APP_ID',         '1:000:web:000');

// ── Site Settings ──────────────────────────────
define('SITE_NAME',        'IlmLibrary');
define('SITE_TAGLINE',     'প্রিমিয়াম ইসলামি বুকস্টোর');
define('SITE_DESCRIPTION', 'খাঁটি ও মানসম্পন্ন ইসলামি বইয়ের সর্বোচ্চ সংগ্রহ। Quran, Hadith, Dua, Islamic Stories।');
define('SITE_LANG',        'bn');
define('DEFAULT_WHATSAPP', '01754389169');
define('DELIVERY_CHARGE',  80);
define('ITEMS_PER_PAGE',   12);

// ── Helper: return Firebase config as JSON for JS ──
function firebaseConfigJson(): string {
    return json_encode([
        'apiKey'            => FIREBASE_API_KEY,
        'authDomain'        => FIREBASE_AUTH_DOMAIN,
        'databaseURL'       => FIREBASE_DATABASE_URL,
        'projectId'         => FIREBASE_PROJECT_ID,
        'storageBucket'     => FIREBASE_STORAGE_BUCKET,
        'messagingSenderId' => FIREBASE_MESSAGING_ID,
        'appId'             => FIREBASE_APP_ID,
    ]);
}
