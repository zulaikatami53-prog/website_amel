<?php
session_start();
include 'koneksi.php';

// Inisialisasi Keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Ambil produk acak atau produk pertama untuk disematkan di Live Stream
$pinned_product = null;
$query_pinned = mysqli_query($koneksi, "SELECT * FROM penjualan ORDER BY id ASC LIMIT 1");
if ($query_pinned && mysqli_num_rows($query_pinned) > 0) {
    $pinned_product = mysqli_fetch_assoc($query_pinned);
}

$total_cart_count = array_sum($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopee Live | Aksesoris Official Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body {
            background-color: #0f172a;
            color: #ffffff;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }
        .live-container {
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
            background: #1e293b;
            position: relative;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
        }
        .live-header {
            position: absolute;
            top: 15px;
            left: 15px;
            right: 15px;
            z-index: 20;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .seller-badge {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(10px);
            padding: 6px 14px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }
        .badge-live-tag {
            background: #ef4444;
            color: white;
            font-weight: 700;
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 12px;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        .video-viewport {
            width: 100%;
            height: 100vh;
            position: relative;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .video-mockup-bg {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.9);
        }
        .live-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.2) 50%, rgba(15, 23, 42, 0.6) 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 70px 15px 20px;
        }
        .chat-stream-box {
            max-height: 220px;
            overflow-y: auto;
            margin-bottom: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            scroll-behavior: smooth;
        }
        .chat-bubble {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 14px;
            padding: 8px 14px;
            font-size: 0.85rem;
            max-width: 85%;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .chat-username {
            color: #38bdf8;
            font-weight: 600;
            margin-right: 6px;
        }
        .pinned-product-card {
            background: rgba(255, 255, 255, 0.95);
            color: #0f172a;
            border-radius: 16px;
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            margin-bottom: 12px;
        }
        .pinned-img {
            width: 54px;
            height: 54px;
            object-fit: cover;
            border-radius: 10px;
        }
        .badge-bogo {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 8px;
        }
        .btn-live-buy {
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 8px 14px;
            font-weight: 600;
            font-size: 0.85rem;
            white-space: nowrap;
        }
        .bottom-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .chat-input {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 20px;
            padding: 10px 16px;
            font-size: 0.85rem;
        }
        .chat-input::placeholder { color: rgba(255, 255, 255, 0.6); }
        .chat-input:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: #38bdf8;
            color: white;
            box-shadow: none;
        }
        .btn-icon-live {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: transform 0.2s;
        }
        .btn-icon-live:active { transform: scale(0.9); }
        .btn-love { background: #ef4444; border: none; }
        .floating-heart {
            position: absolute;
            bottom: 80px;
            right: 25px;
            color: #ef4444;
            font-size: 1.8rem;
            animation: floatUp 1.2s ease-out forwards;
            pointer-events: none;
        }
        @keyframes floatUp {
            0% { transform: translateY(0) scale(1); opacity: 1; }
            100% { transform: translateY(-120px) scale(1.4); opacity: 0; }
        }
    </style>
</head>
<body>

<div class="live-container">
    <!-- Header Live Stream -->
    <div class="live-header">
        <div class="seller-badge">
            <div class="badge-live-tag"><i class="bi bi-broadcast me-1"></i>LIVE</div>
            <div>
                <div class="fw-bold fs-7 text-white mb-0">Aksesoris Official</div>
                <small class="text-info fs-8"><i class="bi bi-eye-fill me-1"></i><span id="viewer-count">1.428</span> penonton</small>
            </div>
            <button class="btn btn-primary btn-sm rounded-pill py-0 px-2 fs-8 fw-semibold ms-2">+ Ikuti</button>
        </div>

        <a href="index.php" class="btn-icon-live text-decoration-none" title="Tutup Live">
            <i class="bi bi-x-lg"></i>
        </a>
    </div>

    <!-- Video Player Simulation -->
    <div class="video-viewport">
        <?php if ($pinned_product && file_exists('uploads/' . $pinned_product['gambar'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($pinned_product['gambar']); ?>" class="video-mockup-bg" alt="Shopee Live Streaming">
        <?php else: ?>
            <div class="text-center p-4">
                <i class="bi bi-camera-reels-fill display-1 text-info opacity-75 mb-3 d-block"></i>
                <h5>Shopee Live Aksesoris HD</h5>
                <small class="text-muted">Host sedang memperagakan promo Buy 1 Get 1 Free</small>
            </div>
        <?php endif; ?>

        <!-- Live Overlay Content -->
        <div class="live-overlay">
            <div></div>

            <div>
                <!-- Live Chat Stream -->
                <div class="chat-stream-box" id="chat-stream">
                    <div class="chat-bubble"><span class="chat-username">Budi_Store:</span> Kak casingnya ready warna bening?</div>
                    <div class="chat-bubble"><span class="chat-username">Siti_Ayu:</span> Wah dapet Buy 1 Get 1 free beneran! 😍</div>
                    <div class="chat-bubble"><span class="chat-username">Host_Live:</span> Halo Kak Siti! Iya hari ini semua promo BOGO ya Kak!</div>
                    <div class="chat-bubble"><span class="chat-username">Rian_Gamer:</span> Langsung checkout tempered glass 9D 🚀</div>
                </div>

                <!-- Pinned Product Card (Produk Sematan) -->
                <?php if ($pinned_product): ?>
                <div class="pinned-product-card">
                    <img src="uploads/<?php echo htmlspecialchars($pinned_product['gambar']); ?>" class="pinned-img" alt="Product">
                    <div class="flex-grow-1 overflow-hidden">
                        <span class="badge badge-bogo mb-1"><i class="bi bi-gift-fill me-1"></i>BUY 1 GET 1</span>
                        <div class="fw-bold fs-7 text-truncate"><?php echo htmlspecialchars($pinned_product['nama_produk']); ?></div>
                        <div class="text-primary fw-bold font-monospace fs-7">Rp <?php echo number_format($pinned_product['harga'], 0, ',', '.'); ?></div>
                    </div>
                    <a href="index.php?add_cart_id=<?php echo $pinned_product['id']; ?>" class="btn btn-live-buy text-decoration-none">
                        <i class="bi bi-bag-plus me-1"></i> Beli
                    </a>
                </div>
                <?php endif; ?>

                <!-- Bottom Chat Input & Action Buttons -->
                <form id="live-chat-form" onsubmit="sendChat(event)">
                    <div class="bottom-actions">
                        <input type="text" id="chat-input-text" class="form-control chat-input" placeholder="Tanya sesuatu di Live..." required autocomplete="off">
                        <a href="keranjang.php" class="btn-icon-live position-relative text-decoration-none" title="Keranjang Belanja">
                            <i class="bi bi-bag-fill"></i>
                            <?php if ($total_cart_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger fs-8"><?php echo $total_cart_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <button type="button" class="btn-icon-live btn-love" onclick="sendLove()" title="Beri Suka">
                            <i class="bi bi-heart-fill"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div id="heart-container"></div>
    </div>
</div>

<script>
    // Fitur simulasi Chat Realtime di Live Stream
    function sendChat(e) {
        e.preventDefault();
        var input = document.getElementById('chat-input-text');
        var text = input.value.trim();
        if (text !== '') {
            var chatBox = document.getElementById('chat-stream');
            var newBubble = document.createElement('div');
            newBubble.className = 'chat-bubble';
            newBubble.innerHTML = '<span class="chat-username">Saya:</span> ' + escapeHtml(text);
            chatBox.appendChild(newBubble);
            chatBox.scrollTop = chatBox.scrollHeight;
            input.value = '';
        }
    }

    function escapeHtml(string) {
        return String(string).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // Animasi Heart Love melayang saat diklik
    function sendLove() {
        var container = document.getElementById('heart-container');
        var heart = document.createElement('i');
        heart.className = 'bi bi-heart-fill floating-heart';
        heart.style.right = (15 + Math.random() * 40) + 'px';
        container.appendChild(heart);
        setTimeout(function() { heart.remove(); }, 1200);

        // Tambah viewer count secara acak
        var viewer = document.getElementById('viewer-count');
        var count = parseInt(viewer.innerText.replace('.', '')) + Math.floor(Math.random() * 3) + 1;
        viewer.innerText = count.toLocaleString('id-ID');
    }

    // Obrolan penonton otomatis berdatangan secara alami
    var randomChats = [
        "Kak packingnya pake bubble wrap ga?",
        "Spill casing buat type C dong kak!",
        "Mantap order ke-2 kali dapet gratisan!",
        "Voucher gratis ongkirnya aktif ya kakk",
        "Kualitas mantap tempered glass presisi banget!"
    ];
    var randomUsers = ["Dian_Pratama", "Maya_Indah", "Reno_99", "Tina_Aksesoris", "Gading_Cell"];

    setInterval(function() {
        var chatBox = document.getElementById('chat-stream');
        var randChat = randomChats[Math.floor(Math.random() * randomChats.length)];
        var randUser = randomUsers[Math.floor(Math.random() * randomUsers.length)];
        var newBubble = document.createElement('div');
        newBubble.className = 'chat-bubble';
        newBubble.innerHTML = '<span class="chat-username">' + randUser + ':</span> ' + randChat;
        chatBox.appendChild(newBubble);
        chatBox.scrollTop = chatBox.scrollHeight;
    }, 4500);
</script>

</body>
</html>
