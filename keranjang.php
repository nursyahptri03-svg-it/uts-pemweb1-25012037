
<?php
require_once __DIR__ . '/includes/fungsi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $aksi = $_POST['aksi'] ?? '';

    if ($id && cariMenu($id) !== null) {
        if ($aksi === 'hapus') {
            unset($_SESSION['keranjang'][$id]);
        }

        if ($aksi === 'update') {
            $jumlah = filter_input(
                INPUT_POST,
                'jumlah',
                FILTER_VALIDATE_INT
            );

            if ($jumlah !== false && $jumlah !== null
                && $jumlah >= 1 && $jumlah <= 99) {
                $_SESSION['keranjang'][$id] = $jumlah;
            } elseif ($jumlah === 0) {
                unset($_SESSION['keranjang'][$id]);
            }
        }
    }

    header('Location: keranjang.php');
    exit;
}

$judulHalaman = 'Keranjang';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section-title">
    <span class="label">PESANAN KAMU</span>
    <h1>🛒 Keranjang Belanja</h1>
    <p>Periksa kembali pesananmu sebelum checkout.</p>
</section>

<?php if (empty($_SESSION['keranjang'])): ?>

    <div class="empty">
        <h2>Keranjang masih kosong 🛍️</h2>
        <p>Silakan pilih makanan atau minuman terlebih dahulu.</p>
        <a href="index.php#menu" class="btn">Pilih Menu</a>
    </div>

<?php else: ?>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($_SESSION['keranjang'] as $id => $jumlah): ?>
                    <?php $menu = cariMenu($id); ?>
                    <?php if ($menu !== null): ?>
                        <tr>
                            <td>
                                <?= $menu['emoji'] ?>
                                <?= htmlspecialchars($menu['nama']) ?>
                            </td>

                            <td><?= rupiah($menu['harga']) ?></td>

                            <td>
                                <form method="POST" class="quantity">
                                    <input type="hidden" name="id"
                                           value="<?= $menu['id'] ?>">
                                    <input type="hidden" name="aksi"
                                           value="update">
                                    <input type="number" name="jumlah"
                                           value="<?= $jumlah ?>"
                                           min="0" max="99" required>
                                    <button class="btn-small">Simpan</button>
                                </form>
                            </td>

                            <td><?= rupiah($menu['harga'] * $jumlah) ?></td>

                            <td>
                                <form method="POST">
                                    <input type="hidden" name="id"
                                           value="<?= $menu['id'] ?>">
                                    <input type="hidden" name="aksi"
                                           value="hapus">
                                    <button class="btn-delete">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="summary">
        <p>Total pembayaran</p>
        <h2><?= rupiah(totalKeranjang()) ?></h2>
        <a href="proses.php" class="btn">Lanjutkan Pemesanan ♡</a>
    </div>

<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
