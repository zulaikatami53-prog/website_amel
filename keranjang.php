<?php
session_start();
include 'koneksi.php';

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Aksi Ubah Jumlah / Hapus Barang dari Keranjang
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($action == 'add' && $id > 0) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]++;
        } else {
            $_SESSION['cart'][$id] = 1;
        }
        header("location:keranjang.php");
        exit();
    } elseif ($action == 'decrease' && $id > 0) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]--;
            if ($_SESSION['cart'][$id] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        }
        header("location:keranjang.php");
        exit();
    } elseif ($action == 'delete' && $id > 0) {
        unset($_SESSION['cart'][$id]);
        header("location:keranjang.php");
        exit();
    } elseif ($action == 'clear') {
        $_SESSION['cart'] = array();
        header("location:keranjang.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja | Toko Aksesoris</title>
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
        .card-custom {
            border: none;
            border-radius: 18px;
            background: #ffffff;
            box-shadow: 0 10px 30px rgba(186, 230, 253, 0.35);
        }
        .btn-soft-blue {
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3);
        }
        .btn-soft-blue:hover {
            background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%);
            color: white;
            transform: translateY(-2px);
        }
        .badge-bogo {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 20px;
            letter-spacing: 0.5px;
        }
        .bogo-box {
            background-color: #fff1f2;
            border: 1px dashed #f43f5e;
            border-radius: 12px;
            padding: 10px 14px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="index.php">
            <i class="bi bi-bag-heart-fill me-2"></i> Aksesoris App
        </a>
        <a href="index.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Katalog
        </a>
    </div>
</nav>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <div class="card card-custom p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="fw-bold text-dark mb-0"><i class="bi bi-cart-check-fill text-primary me-2"></i>Keranjang Belanja</h4>
                    <?php if(!empty($_SESSION['cart'])): ?>
                        <a href="keranjang.php?action=clear" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="return confirm('Kosongkan keranjang belanja?')">
                            <i class="bi bi-trash me-1"></i> Kosongkan
                        </a>
                    <?php endif; ?>
                </div>
                <hr>

                <?php
                $total_bayar = 0;
                $total_bogo_items = 0;
                $cart_items = array();

                if (!empty($_SESSION['cart'])) {
                    $ids = implode(',', array_keys($_SESSION['cart']));
                    $query = mysqli_query($koneksi, "SELECT * FROM penjualan WHERE id IN ($ids)");
                    while ($row = mysqli_fetch_assoc($query)) {
                        $qty = $_SESSION['cart'][$row['id']];
                        $subtotal = $row['harga'] * $qty;
                        $total_bayar += $subtotal;
                        $total_bogo_items += $qty; // Setiap 1 produk gratis 1 produk bonus
                        $row['qty'] = $qty;
                        $row['subtotal'] = $subtotal;
                        $cart_items[] = $row;
                    }
                }
                ?>

                <?php if (!empty($cart_items)): ?>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr class="text-secondary small">
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark"><?php echo htmlspecialchars($item['nama_produk']); ?></div>
                                        <span class="badge badge-bogo mt-1"><i class="bi bi-gift-fill me-1"></i>BUY 1 GET 1 FREE</span>
                                    </td>
                                    <td class="text-primary font-monospace">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="keranjang.php?action=decrease&id=<?php echo $item['id']; ?>" class="btn btn-outline-secondary px-2">-</a>
                                            <span class="btn btn-light disabled px-3 fw-bold"><?php echo $item['qty']; ?></span>
                                            <a href="keranjang.php?action=add&id=<?php echo $item['id']; ?>" class="btn btn-outline-secondary px-2">+</a>
                                        </div>
                                    </td>
                                    <td class="text-end font-monospace fw-bold text-dark">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <a href="keranjang.php?action=delete&id=<?php echo $item['id']; ?>" class="btn btn-link text-danger p-0" title="Hapus">
                                            <i class="bi bi-x-circle-fill fs-5"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Promo Buy 1 Get 1 Notice Box -->
                    <div class="bogo-box mt-3 d-flex align-items-center">
                        <i class="bi bi-gift-fill text-danger fs-3 me-3"></i>
                        <div>
                            <div class="fw-bold text-danger">Promo Spesial Buy 1 Get 1 Free Aktif!</div>
                            <small class="text-secondary">Anda otomatis mendapatkan <strong>+<?php echo $total_bogo_items; ?> produk bonus gratis</strong> untuk setiap item yang dibeli!</small>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-cart-x text-muted display-3"></i>
                        <p class="text-secondary mt-3">Keranjang belanja Anda masih kosong.</p>
                        <a href="index.php" class="btn btn-soft-blue mt-2">
                            <i class="bi bi-bag-plus me-1"></i> Mulai Belanja Sekarang
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Ringkasan Belanja -->
        <div class="col-lg-4">
            <div class="card card-custom p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-receipt text-primary me-2"></i>Ringkasan Belanja</h5>
                <hr class="mt-0">
                
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Total Subtotal</span>
                    <span class="font-monospace fw-bold text-dark">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-success">
                    <span>Bonus BOGO (Item Gratis)</span>
                    <span class="fw-bold">+<?php echo $total_bogo_items; ?> Items (FREE)</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-secondary">Ongkos Kirim</span>
                    <span class="badge bg-success text-white">GRATIS ONGKIR</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fw-bold fs-6">Total Pembayaran</span>
                    <span class="fs-4 fw-bold text-primary font-monospace">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></span>
                </div>

                <?php if (!empty($cart_items)): ?>
                    <a href="checkout.php" class="btn btn-soft-blue w-100 py-3">
                        Lanjut ke Pembayaran <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary w-100 py-3" disabled>Lanjut ke Pembayaran</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
