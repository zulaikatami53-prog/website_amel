<?php
session_start();
include 'koneksi.php';

if (empty($_SESSION['cart']) || $_SERVER['REQUEST_METHOD'] != 'POST') {
    header("location:index.php");
    exit();
}

$nama_pembeli = mysqli_real_escape_string($koneksi, trim($_POST['nama_pembeli']));
$no_hp = mysqli_real_escape_string($koneksi, trim($_POST['no_hp']));
$alamat = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
$metode = mysqli_real_escape_string($koneksi, trim($_POST['metode_pembayaran']));

$order_id = "TRX-" . date("Ymd") . "-" . rand(1000, 9999);
$tanggal = date("d M Y, H:i");

$total_bayar = 0;
$total_bogo_items = 0;
$ordered_items = array();

// Proses pengurangan stok barang di database & hitung total
foreach ($_SESSION['cart'] as $id => $qty) {
    $id = (int)$id;
    $qty = (int)$qty;
    
    $query = mysqli_query($koneksi, "SELECT * FROM penjualan WHERE id='$id'");
    if ($query && $row = mysqli_fetch_assoc($query)) {
        $subtotal = $row['harga'] * $qty;
        $total_bayar += $subtotal;
        $total_bogo_items += $qty;
        
        $row['qty'] = $qty;
        $row['subtotal'] = $subtotal;
        $ordered_items[] = $row;
        
        // Kurangi stok barang (minimal stok 0)
        $stok_baru = max(0, $row['stok'] - $qty);
        mysqli_query($koneksi, "UPDATE penjualan SET stok='$stok_baru' WHERE id='$id'");
    }
}

// Reset Keranjang
$_SESSION['cart'] = array();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran | Toko Aksesoris</title>
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
        .invoice-card {
            border: none;
            border-radius: 20px;
            background: #ffffff;
            box-shadow: 0 15px 35px rgba(186, 230, 253, 0.45);
            max-width: 650px;
            margin: 0 auto;
        }
        .invoice-header {
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 30px;
            text-align: center;
        }
        .badge-success-soft {
            background-color: #dcfce7;
            color: #166534;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 20px;
        }
        .btn-soft-blue {
            background: linear-gradient(135deg, #0284c7 0%, #38bdf8 100%);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-soft-blue:hover {
            background: linear-gradient(135deg, #0369a1 0%, #0284c7 100%);
            color: white;
        }
        .qr-placeholder {
            width: 140px;
            height: 140px;
            background: #f1f5f9;
            border: 2px dashed #0284c7;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="index.php">
            <i class="bi bi-bag-heart-fill me-2"></i> Aksesoris App
        </a>
    </div>
</nav>

<div class="container my-5">
    <div class="invoice-card overflow-hidden">
        <div class="invoice-header">
            <i class="bi bi-check-circle-fill display-4 mb-2 d-block text-white"></i>
            <h3 class="fw-bold mb-1">Pembayaran Berhasil!</h3>
            <p class="mb-0 opacity-90">Terima kasih telah berbelanja di Toko Aksesoris</p>
        </div>

        <div class="p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <span class="text-secondary small d-block">ID Transaksi</span>
                    <strong class="font-monospace text-primary fs-6"><?php echo $order_id; ?></strong>
                </div>
                <div class="text-end">
                    <span class="text-secondary small d-block">Tanggal</span>
                    <small class="fw-medium text-dark"><?php echo $tanggal; ?></small>
                </div>
            </div>

            <!-- Detail Pembeli -->
            <div class="bg-light p-3 rounded-3 mb-4">
                <div class="row">
                    <div class="col-sm-6 mb-2 mb-sm-0">
                        <small class="text-secondary d-block">Nama Pembeli</small>
                        <strong class="text-dark"><?php echo htmlspecialchars($nama_pembeli); ?> (<?php echo htmlspecialchars($no_hp); ?>)</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-secondary d-block">Metode Pembayaran</small>
                        <span class="badge bg-primary"><?php echo htmlspecialchars($metode); ?></span>
                    </div>
                </div>
            </div>

            <!-- Tampilan QRIS jika metode QRIS -->
            <?php if (strpos($metode, 'QRIS') !== false): ?>
                <div class="text-center p-3 mb-4 rounded-3 border border-primary-subtle bg-primary-subtle bg-opacity-10">
                    <div class="qr-placeholder">
                        <i class="bi bi-qr-code-scan display-4 text-primary"></i>
                    </div>
                    <small class="text-primary fw-bold d-block">Scan QRIS untuk Pembayaran</small>
                    <small class="text-muted fs-8">Status: <span class="badge bg-success">LUNAS / BERHASIL</span></small>
                </div>
            <?php endif; ?>

            <!-- Table Produk Dibeli -->
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-box-seam text-primary me-2"></i>Rincian Barang Dibeli</h6>
            <div class="table-responsive mb-4">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th>Produk</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Harga Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ordered_items as $item): ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark"><?php echo htmlspecialchars($item['nama_produk']); ?></div>
                                    <small class="text-danger fw-medium">+<?php echo $item['qty']; ?> Item Bonus (BUY 1 GET 1 FREE)</small>
                                </td>
                                <td class="text-center fw-bold"><?php echo $item['qty']; ?></td>
                                <td class="text-end font-monospace fw-bold">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="border-top pt-3 mb-4">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-secondary">Total Item Utama</span>
                    <span class="fw-bold"><?php echo count($ordered_items); ?> Jenis Produk</span>
                </div>
                <div class="d-flex justify-content-between mb-2 text-danger">
                    <span>Total Bonus Gratis (BOGO)</span>
                    <span class="fw-bold">+<?php echo $total_bogo_items; ?> Items Free</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                    <span class="fw-bold fs-5">Total Lunas</span>
                    <span class="fs-3 fw-bold text-primary font-monospace">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></span>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-soft-blue w-100 py-3 mb-2">
                    <i class="bi bi-house-door-fill me-2"></i> Kembali ke Beranda Store
                </a>
                <button onclick="window.print()" class="btn btn-outline-secondary w-100 py-2.5">
                    <i class="bi bi-printer me-2"></i> Cetak Struk Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
