
<?php
require_once __DIR__ . '/includes/fungsi.php';

if (empty($_SESSION['keranjang'])) {
    header('Location: keranjang.php');
    exit;
}

$errors = [];
$nama = '';
$kelas = '';
$pembayaran = 'Tunai';
$pesananSelesai = false;
$ringkasan = [];
$total = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $kelas = trim($_POST['kelas'] ?? '');
    $pembayaran = $_POST['pembayaran'] ?? 'Tunai';

    if ($nama === '') {
        $errors[] = 'Nama wajib diisi.';
    }

    if ($kelas === '') {
        $errors[] = 'Kelas wajib diisi.';
    }

    if (!in_array($pembayaran, ['Tunai', 'QRIS'], true)) {
        $errors[] = 'Metode pembayaran tidak valid.';
    }

    if (!$errors) {
        foreach ($_SESSION['keranjang'] as $id => $jumlah) {
            $menu = cariMenu($id);

            if ($menu !== null && $jumlah > 0) {
                $subtotal = $menu['harga'] * $jumlah;
                $ringkasan[] = [
                    'nama' => $menu['nama'],
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal
                ];
                $total += $subtotal;
            }
        }

        if ($ringkasan) {
            $pesananSelesai = true;
            $_SESSION['keranjang'] = [];
        } else {
            $errors[] = 'Tidak ada pesanan yang valid.';
        }
    }
}

$judulHalaman = 'Checkout';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section-title">
    <span class="label">CHECKOUT</span>
    <h1>Konfirmasi Pesanan ♡</h1>
</section>

<?php if ($pesananSelesai): ?>

    <div class="checkout-card">
        <h2>🎉 Pesanan Berhasil!</h2>
        <p>Terima kasih sudah memesan di KantinKu.</p>

        <hr>

        <p><strong>Nama:</strong> <?= htmlspecialchars($nama) ?></p>
        <p><strong>Kelas:</strong> <?= htmlspecialchars($kelas) ?></p>
        <p><strong>Pembayaran:</strong> <?= htmlspecialchars($pembayaran) ?></p>

        <h3>Rincian Pesanan</h3>

        <?php foreach ($ringkasan as $item): ?>
            <p>
                <?= htmlspecialchars($item['nama']) ?>
                × <?= $item['jumlah'] ?>
                — <?= rupiah($item['subtotal']) ?>
            </p>
        <?php endforeach; ?>

        <h2>Total: <?= rupiah($total) ?></h2>

        <p>Status: Menunggu pembayaran / konfirmasi kantin.</p>

        <a href="index.php" class="btn">Kembali ke Beranda</a>
    </div>

<?php else: ?>

    <?php if ($errors): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="checkout-card">
        <form method="POST">
            <label for="nama">Nama Pemesan</label>
            <input type="text" id="nama" name="nama"
                   value="<?= htmlspecialchars($nama) ?>" required>

            <label for="kelas">Kelas</label>
            <input type="text" id="kelas" name="kelas"
                   placeholder="Contoh: 25M11"
                   value="<?= htmlspecialchars($kelas) ?>" required>

            <label for="pembayaran">Metode Pembayaran</label>
            <select id="pembayaran" name="pembayaran">
                <option value="Tunai"
                    <?= $pembayaran === 'Tunai' ? 'selected' : '' ?>>
                    Tunai di kantin
                </option>
                <option value="QRIS"
                    <?= $pembayaran === 'QRIS' ? 'selected' : '' ?>>
                    QRIS
                </option>
            </select>

            <div class="checkout-total">
                <span>Total pembayaran</span>
                <strong><?= rupiah(totalKeranjang()) ?></strong>
            </div>

            <button type="submit" class="btn full">
                Konfirmasi Pesanan
            </button>
        </form>

        <a href="keranjang.php" class="back-link">
            ← Kembali ke keranjang
        </a>
    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
