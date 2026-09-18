<?php
session_start();
include 'koneksi.php';

if (empty($_SESSION['cart'])) {
    header("location:keranjang.php");
    exit();
}

$total_bayar = 0;
$total_bogo_items = 0;
$cart_items = array();

$ids = implode(',', array_keys($_SESSION['cart']));
$query = mysqli_query($koneksi, "SELECT * FROM penjualan WHERE id IN ($ids)");
while ($row = mysqli_fetch_assoc($query)) {
    $qty = $_SESSION['cart'][$row['id']];
    $subtotal = $row['harga'] * $qty;
    $total_bayar += $subtotal;
    $total_bogo_items += $qty;
    $row['qty'] = $qty;
    $row['subtotal'] = $subtotal;
    $cart_items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran / Checkout | Toko Aksesoris</title>
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
        .form-control, .form-select {
            border-radius: 12px;
            padding: 10px 16px;
            border: 1.5px solid #e2e8f0;
            background-color: #f8fafc;
        }
        .form-control:focus, .form-select:focus {
            background-color: #ffffff;
            border-color: #38bdf8;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.15);
        }
        .payment-option {
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 18px;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 12px;
        }
        .payment-option:hover {
            border-color: #38bdf8;
            background-color: #f0f9ff;
        }
        .payment-option input[type="radio"]:checked + .payment-label {
            color: #0284c7;
            font-weight: 600;
        }
        .btn-soft-blue {
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 14px 24px;
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
            font-size: 0.7rem;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 12px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="index.php">
            <i class="bi bi-bag-heart-fill me-2"></i> Aksesoris App
        </a>
        <a href="keranjang.php" class="btn btn-outline-light btn-sm rounded-pill px-3">
            <i class="bi bi-cart me-1"></i> Kembali ke Keranjang
        </a>
    </div>
</nav>

<div class="container my-5">
    <form action="proses_bayar.php" method="POST">
        <div class="row">
            <!-- Informasi Pembeli & Metode Pembayaran -->
            <div class="col-lg-7 mb-4">
                <div class="card card-custom p-4 mb-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-person-lines-fill text-primary me-2"></i>Data Pembeli & Pengiriman</h5>
                    <hr class="mt-0 mb-4">

                    <div class="mb-3">
                        <label class="form-label text-secondary fw-medium">Nama Lengkap</label>
                        <input type="text" name="nama_pembeli" class="form-control" placeholder="Masukkan nama Anda" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-medium">Nomor HP / WhatsApp</label>
                        <input type="tel" name="no_hp" class="form-control" placeholder="Contoh: 081234567890" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-medium">Alamat Lengkap Pengiriman</label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Jl. Contoh No. 123, Kelurahan, Kota..." required></textarea>
                    </div>
                </div>

                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-credit-card-2-front-fill text-primary me-2"></i>Pilih Metode Pembayaran</h5>
                    <hr class="mt-0 mb-4">

                    <div class="payment-option">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="qris" value="QRIS Instant" checked>
                            <label class="form-check-label payment-label ms-2 d-flex justify-content-between align-items-center w-100" for="qris">
                                <span><i class="bi bi-qr-code-scan text-primary me-2"></i>QRIS (Scan All E-Wallet & M-Banking)</span>
                                <span class="badge bg-primary text-white">INSTANT</span>
                            </label>
                        </div>
                    </div>

                    <div class="payment-option">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="bank" value="Transfer Bank (BCA)">
                            <label class="form-check-label payment-label ms-2 d-flex justify-content-between align-items-center w-100" for="bank">
                                <span><i class="bi bi-bank text-primary me-2"></i>Transfer Bank BCA (No. Rek: 8820-192-881)</span>
                                <span class="badge bg-secondary text-white">VERIFIKASI OTOMATIS</span>
                            </label>
                        </div>
                    </div>

                    <div class="payment-option">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="ewallet" value="E-Wallet (GoPay/OVO/DANA)">
                            <label class="form-check-label payment-label ms-2 d-flex justify-content-between align-items-center w-100" for="ewallet">
                                <span><i class="bi bi-wallet2 text-primary me-2"></i>E-Wallet (GoPay, OVO, DANA, ShopeePay)</span>
                                <span class="badge bg-info text-dark">BEBAS BIAYA</span>
                            </label>
                        </div>
                    </div>

                    <div class="payment-option">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="metode_pembayaran" id="cod" value="COD (Bayar di Tempat)">
                            <label class="form-check-label payment-label ms-2 d-flex justify-content-between align-items-center w-100" for="cod">
                                <span><i class="bi bi-truck text-primary me-2"></i>COD (Bayar Saat Barang Sampai)</span>
                                <span class="badge bg-success text-white">BAYAR DI TEMPAT</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Pesanan & Tombol Konfirmasi -->
            <div class="col-lg-5">
                <div class="card card-custom p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-bag-check-fill text-primary me-2"></i>Rincian Pesanan</h5>
                    <hr class="mt-0">

                    <div class="mb-3" style="max-height: 280px; overflow-y: auto;">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div>
                                    <div class="fw-semibold text-dark"><?php echo htmlspecialchars($item['nama_produk']); ?></div>
                                    <small class="text-secondary"><?php echo $item['qty']; ?> x Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></small>
                                    <div><span class="badge badge-bogo">+<?php echo $item['qty']; ?> Bonus Gratis</span></div>
                                </div>
                                <span class="fw-bold font-monospace text-dark">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-secondary">Subtotal Barang</span>
                        <span class="font-monospace text-dark">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 text-danger">
                        <span>Promo Buy 1 Get 1 Free</span>
                        <span class="fw-bold">+<?php echo $total_bogo_items; ?> Items (Rp 0)</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 text-success">
                        <span>Biaya Pengiriman</span>
                        <span class="fw-bold">GRATIS</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-6">Total Akhir</span>
                        <span class="fs-4 fw-bold text-primary font-monospace">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></span>
                    </div>

                    <button type="submit" class="btn btn-soft-blue w-100 py-3">
                        <i class="bi bi-shield-check me-2"></i> Konfirmasi & Bayar Sekarang
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

</body>
</html>
