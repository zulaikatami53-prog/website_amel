<?php
session_start();
include 'koneksi.php';

// Inisialisasi Keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$total_cart_count = array_sum($_SESSION['cart']);

$added_msg = '';
if (isset($_GET['add_cart_id'])) {
    $cart_id = (int)$_GET['add_cart_id'];
    if ($cart_id > 0) {
        if (isset($_SESSION['cart'][$cart_id])) {
            $_SESSION['cart'][$cart_id]++;
        } else {
            $_SESSION['cart'][$cart_id] = 1;
        }
        header("location:index.php?msg=added");
        exit();
    }
}

if (isset($_GET['msg']) && $_GET['msg'] == 'added') {
    $added_msg = "Produk berhasil ditambahkan ke keranjang belanja!";
}

$kategori_filter = isset($_GET['kategori']) ? mysqli_real_escape_string($koneksi, $_GET['kategori']) : '';
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, trim($_GET['search'])) : '';

$where_clause = "WHERE 1=1";
if (!empty($kategori_filter)) {
    $where_clause .= " AND kategori='$kategori_filter'";
}
if (!empty($search_query)) {
    $where_clause .= " AND (nama_produk LIKE '%$search_query%' OR deskripsi LIKE '%$search_query%')";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0284c7">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Store Aksesoris Shopee Style | Katalog Lengkap</title>
    
    <!-- PWA Manifest Link -->
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="uploads/case_hp.png">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * { font-family: 'Poppins', sans-serif; }
        body { background-color: #f0f9ff; min-height: 100vh; }
        .navbar-custom {
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            box-shadow: 0 4px 20px rgba(56, 189, 248, 0.25);
        }
        .live-banner-btn {
            background: linear-gradient(135deg, #ef4444 0%, #f43f5e 100%);
            color: white;
            font-weight: 700;
            border-radius: 30px;
            padding: 8px 18px;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
            animation: pulseLive 1.8s infinite;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }
        .live-banner-btn:hover { color: white; transform: scale(1.03); }
        @keyframes pulseLive {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.6); }
            70% { box-shadow: 0 0 0 12px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }
        .hero-banner {
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 50%, #7dd3fc 100%);
            color: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(56, 189, 248, 0.3);
            margin-bottom: 30px;
        }
        .card-product {
            border: none;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(186, 230, 253, 0.4);
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }
        .card-product:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 30px rgba(56, 189, 248, 0.3);
        }
        .product-img-box {
            width: 100%;
            height: 200px;
            background-color: #f8fafc;
            position: relative;
            overflow: hidden;
        }
        .product-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        .card-product:hover .product-img-box img {
            transform: scale(1.08);
        }
        .badge-discount {
            position: absolute;
            top: 10px;
            left: 10px;
            background: #ef4444;
            color: white;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 4px 8px;
            border-radius: 8px;
            z-index: 2;
        }
        .badge-bogo {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
            color: white;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 12px;
        }
        .badge-star {
            background-color: #fef3c7;
            color: #d97706;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 3px 8px;
            border-radius: 6px;
        }
        .btn-soft-blue {
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 10px 18px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);
            width: 100%;
        }
        .btn-soft-blue:hover {
            background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%);
            color: white;
        }
        .cart-badge {
            background-color: #ef4444;
            color: white;
            border-radius: 50%;
            padding: 2px 7px;
            font-size: 0.75rem;
            font-weight: 700;
            vertical-align: top;
            margin-left: -5px;
        }
        .category-pill {
            background-color: #ffffff;
            color: #0284c7;
            border: 1.5px solid #bae6fd;
            border-radius: 20px;
            padding: 8px 18px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-block;
        }
        .category-pill:hover, .category-pill.active {
            background-color: #0284c7;
            color: white;
            border-color: #0284c7;
        }
        .pwa-install-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            font-weight: 600;
            border-radius: 30px;
            padding: 8px 18px;
            border: none;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
            transition: all 0.2s;
        }
        .pwa-install-btn:hover { transform: translateY(-2px); color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3 sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="index.php">
            <i class="bi bi-bag-heart-fill me-2"></i> Store Aksesoris
        </a>

        <!-- Form Pencarian Shopee Style -->
        <form action="index.php" method="GET" class="d-none d-md-flex mx-auto col-md-4">
            <div class="input-group">
                <input type="text" name="search" class="form-control rounded-pill-start border-0 ps-3" placeholder="Cari aksesoris..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button class="btn btn-light text-primary rounded-pill-end px-3" type="submit"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <div class="d-flex align-items-center gap-2">
            <!-- Tombol Install PWA Mobile App -->
            <button id="pwa-install-btn" class="pwa-install-btn d-none" onclick="installPWA()">
                <i class="bi bi-phone-vibe me-1"></i> Install App
            </button>

            <!-- Button Live Stream -->
            <a href="live.php" class="live-banner-btn">
                <i class="bi bi-broadcast me-1.5 fs-6"></i> FITUR LIVE
            </a>

            <!-- Tombol Keranjang -->
            <a href="keranjang.php" class="btn btn-light text-primary fw-bold rounded-pill px-3 shadow-sm position-relative">
                <i class="bi bi-cart-fill me-1"></i> Keranjang
                <?php if ($total_cart_count > 0): ?>
                    <span class="cart-badge"><?php echo $total_cart_count; ?></span>
                <?php endif; ?>
            </a>

            <?php if (isset($_SESSION['admin'])): ?>
                <a href="logout.php" class="btn btn-outline-light btn-sm rounded-pill px-3 ms-1">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-light btn-sm rounded-pill px-3 ms-1">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Admin Login
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container my-4">
    <?php if(!empty($added_msg)): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo $added_msg; ?>
            <a href="keranjang.php" class="fw-bold text-success text-decoration-underline ms-2">Lihat Keranjang & Checkout &raquo;</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Banner Shopee Style & App Install Notice -->
    <div class="hero-banner d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <span class="badge bg-danger mb-2 fs-7"><i class="bi bi-lightning-charge-fill me-1"></i>PUBLISHED & MOBILE APP READY</span>
            <h2 class="fw-bold mb-1">Buy 1 Get 1 Free Aksesoris Premium!</h2>
            <p class="mb-0 opacity-90">Dapat diakses online dari seluruh dunia & dapat dipasang sebagai Aplikasi Mobile di Android/iOS/PC</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="live.php" class="btn btn-light text-danger fw-bold rounded-pill px-4 py-2.5 shadow">
                <i class="bi bi-play-circle-fill me-2 fs-5"></i> Tonton Aksesoris Live
            </a>
            <button class="btn btn-success fw-bold rounded-pill px-4 py-2.5 shadow text-white" onclick="installPWA()">
                <i class="bi bi-download me-2"></i> Pasang Aplikasi HP
            </button>
        </div>
    </div>

    <!-- Filter Kategori Produk -->
    <div class="mb-4 overflow-auto text-nowrap pb-2">
        <a href="index.php" class="category-pill me-2 <?php if(empty($kategori_filter)) echo 'active'; ?>">Semua Produk</a>
        <a href="index.php?kategori=Casing HP" class="category-pill me-2 <?php if($kategori_filter == 'Casing HP') echo 'active'; ?>"><i class="bi bi-phone me-1"></i> Casing HP</a>
        <a href="index.php?kategori=Tempered Glass" class="category-pill me-2 <?php if($kategori_filter == 'Tempered Glass') echo 'active'; ?>"><i class="bi bi-shield-check me-1"></i> Tempered Glass</a>
        <a href="index.php?kategori=Kabel %26 Charger" class="category-pill me-2 <?php if($kategori_filter == 'Kabel & Charger') echo 'active'; ?>"><i class="bi bi-plug me-1"></i> Kabel & Charger</a>
        <a href="index.php?kategori=Audio %26 Earphone" class="category-pill me-2 <?php if($kategori_filter == 'Audio & Earphone') echo 'active'; ?>"><i class="bi bi-headphones me-1"></i> Audio</a>
    </div>

    <!-- Nav Tabs: Katalog Shopee & Manajemen Admin -->
    <ul class="nav nav-pills mb-4 gap-2" id="storeTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 fw-semibold" id="katalog-tab" data-bs-toggle="tab" data-bs-target="#katalog" type="button" role="tab">
                <i class="bi bi-grid-fill me-2"></i>Katalog Shopee Style
            </button>
        </li>
        <?php if (isset($_SESSION['admin'])): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4 fw-semibold" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin" type="button" role="tab">
                <i class="bi bi-sliders me-2"></i>Manajemen Stok (Admin)
            </button>
        </li>
        <?php endif; ?>
    </ul>

    <div class="tab-content" id="storeTabsContent">
        <!-- KATALOG PRODUK SHOPEE STYLE -->
        <div class="tab-pane fade show active" id="katalog" role="tabpanel">
            <div class="row g-4">
                <?php
                $res = mysqli_query($koneksi, "SELECT * FROM penjualan $where_clause ORDER BY id DESC");
                if ($res && mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)){
                        $diskon = isset($row['diskon']) ? (int)$row['diskon'] : 0;
                        $harga_diskon = $diskon > 0 ? $row['harga'] - ($row['harga'] * $diskon / 100) : $row['harga'];
                ?>
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="card card-product">
                        <?php if ($diskon > 0): ?>
                            <span class="badge-discount">-<?php echo $diskon; ?>% OFF</span>
                        <?php endif; ?>

                        <div class="product-img-box">
                            <?php if (!empty($row['gambar']) && file_exists('uploads/' . $row['gambar'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($row['gambar']); ?>" alt="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                            <?php else: ?>
                                <div class="d-flex align-items-center justify-content-center h-100 bg-light text-primary">
                                    <i class="bi bi-bag-heart display-3 opacity-50"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="p-3 d-flex flex-column flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge badge-star"><i class="bi bi-star-fill me-1"></i>5.0 | Star Seller</span>
                                <span class="badge badge-bogo">BUY 1 GET 1</span>
                            </div>

                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="<?php echo htmlspecialchars($row['nama_produk']); ?>">
                                <?php echo htmlspecialchars($row['nama_produk']); ?>
                            </h6>
                            
                            <small class="text-secondary mb-2 d-block text-truncate">
                                <i class="bi bi-tag-fill text-info me-1"></i><?php echo htmlspecialchars($row['kategori']); ?> 
                                <?php if(!empty($row['variasi'])): ?> &bull; <?php echo htmlspecialchars($row['variasi']); ?><?php endif; ?>
                            </small>

                            <div class="mt-auto pt-2 border-top">
                                <div class="d-flex align-items-baseline gap-2 mb-2">
                                    <span class="text-primary fw-bold font-monospace fs-5">Rp <?php echo number_format($harga_diskon, 0, ',', '.'); ?></span>
                                    <?php if ($diskon > 0): ?>
                                        <small class="text-muted text-decoration-line-through font-monospace fs-8">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></small>
                                    <?php endif; ?>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <small class="text-muted fs-8"><i class="bi bi-box me-1"></i>Stok: <?php echo $row['stok']; ?></small>
                                    <small class="text-success fs-8 fw-semibold">Bebas Ongkir</small>
                                </div>

                                <?php if ($row['stok'] > 0): ?>
                                    <a href="index.php?add_cart_id=<?php echo $row['id']; ?>" class="btn btn-soft-blue">
                                        <i class="bi bi-cart-plus-fill me-1"></i> + Keranjang
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-secondary w-100 rounded-3" disabled>Stok Habis</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                    }
                } else {
                ?>
                <div class="col-12 text-center py-5">
                    <i class="bi bi-search text-muted display-4"></i>
                    <p class="text-secondary mt-2">Produk yang dicari tidak ditemukan.</p>
                </div>
                <?php } ?>
            </div>
        </div>

        <!-- MANAJEMEN STOK ADMIN -->
        <?php if (isset($_SESSION['admin'])): ?>
        <div class="tab-pane fade" id="admin" role="tabpanel">
            <div class="card p-4 border-0 rounded-4 shadow-sm bg-white">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-box-seam text-primary me-2"></i>Daftar Stok Produk Lengkap</h5>
                    <a href="tambah.php" class="btn btn-soft-blue w-auto">
                        <i class="bi bi-plus-circle me-1.5"></i> Tambah Produk Shopee Style
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Foto</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Diskon</th>
                                <th>Stok</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $res_admin = mysqli_query($koneksi, "SELECT * FROM penjualan ORDER BY id DESC");
                            while($row_admin = mysqli_fetch_assoc($res_admin)){
                            ?>
                            <tr>
                                <td>
                                    <?php if (!empty($row_admin['gambar']) && file_exists('uploads/' . $row_admin['gambar'])): ?>
                                        <img src="uploads/<?php echo htmlspecialchars($row_admin['gambar']); ?>" width="44" height="44" class="rounded-3 object-fit-cover" alt="Thumb">
                                    <?php else: ?>
                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="width:44px; height:44px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row_admin['nama_produk']); ?></strong>
                                    <small class="d-block text-muted"><?php echo htmlspecialchars($row_admin['variasi']); ?></small>
                                </td>
                                <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($row_admin['kategori']); ?></span></td>
                                <td class="fw-semibold text-primary font-monospace">Rp <?php echo number_format((float)$row_admin['harga'], 0, ',', '.'); ?></td>
                                <td><?php echo (int)$row_admin['diskon']; ?>%</td>
                                <td><span class="badge bg-secondary"><?php echo $row_admin['stok']; ?> unit</span></td>
                                <td class="text-center">
                                    <a href="edit.php?id=<?php echo $row_admin['id']; ?>" class="btn btn-warning btn-sm me-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="hapus.php?id=<?php echo $row_admin['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus barang ini?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // PWA Service Worker Registration & Installation Script
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('sw.js')
            .then(reg => console.log('PWA Service Worker terdaftar!'))
            .catch(err => console.log('Service Worker gagal:', err));
    }

    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) installBtn.classList.remove('d-none');
    });

    function installPWA() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('Pengguna memasang aplikasi PWA');
                }
                deferredPrompt = null;
            });
        } else {
            alert('Untuk memasang aplikasi di HP:\n• Android (Chrome): Ketuk titik 3 di kanan atas -> pilih "Tambahkan ke Layar Utama / Install App"\n• iPhone (Safari): Ketuk ikon Bagikan (Share) -> pilih "Tambah ke Layar Utama"');
        }
    }
</script>
</body>
</html>
