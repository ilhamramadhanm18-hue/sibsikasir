<?php
session_start();
include "../config/koneksi.php";

if (!isset($_SESSION['nama']) || trim($_SESSION['nama']) == '') {
    header("Location: ../login.php");
    exit;
}

$username_aktif = $_SESSION['username'];
// Mengambil barang murni sesuai harga asli database
$barang = mysqli_query($conn, "SELECT * FROM barang WHERE username='$username_aktif' AND stok > 0");

$array_barang = [];
while ($b = mysqli_fetch_assoc($barang)) {
    $array_barang[] = [
        'id' => $b['id'],
        'nama_barang' => $b['nama_barang'],
        'stok' => $b['stok'],
        'harga' => (int)$b['harga'] // Menggunakan harga asli murni tanpa dikali 1000 lagi
    ];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kasir Multi-Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .card {
            border-radius: 12px;
            border: none;
        }

        .select2-container .select2-selection--single {
            height: 45px !important;
            padding: 8px !important;
        }

        .qr-placeholder {
            width: 150px;
            height: 150px;
            background: #fff;
            border: 2px dashed #ccc;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .input-kasir {
            font-size: 1.25rem;
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container mt-5 mb-5">
        <div class="row g-4">
            <div class="col-md-5">
                <div class="card shadow-lg mb-3">
                    <div class="card-header bg-warning text-white fw-bold py-3">🛍️ Pilih & Tambah Barang</div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Cari Barang</label>
                            <select id="id_barang" class="form-control">
                                <option value="">-- Cari & Pilih Barang --</option>
                                <?php foreach ($array_barang as $brg) { ?>
                                    <option value="<?= $brg['id'] ?>" data-nama="<?= htmlspecialchars($brg['nama_barang']) ?>" data-harga="<?= $brg['harga'] ?>" data-stok="<?= $brg['stok'] ?>">
                                        <?= htmlspecialchars($brg['nama_barang']) ?> | Stok: <?= $brg['stok'] ?> | Rp <?= number_format($brg['harga'], 0, ',', '.') ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-secondary">Jumlah Beli</label>
                            <input type="number" id="qty" class="form-control" min="1" value="1">
                        </div>
                        <button type="button" id="btn-tambah" class="btn btn-primary w-100 fw-bold rounded-pill">➕ Tambah ke Keranjang</button>
                    </div>
                </div>
                <a href="../dashboard.php" class="btn btn-secondary w-100 rounded-pill fw-bold">⬅️ Kembali</a>
            </div>

            <div class="col-md-7">
                <form action="simpan.php" method="POST" autocomplete="off" id="form-transaksi">
                    <div class="card shadow-lg">
                        <div class="card-header bg-success text-white fw-bold py-3">🛒 Daftar Belanjaan</div>
                        <div class="card-body p-4">

                            <div class="table-responsive mb-3" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-hover" id="tabel-keranjang">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Barang</th>
                                            <th>Jml</th>
                                            <th>Harga</th>
                                            <th>Subtotal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="row-kosong">
                                            <td colspan="5" class="text-center text-muted py-4">Keranjang masih kosong. Tambahkan barang di sebelah kiri.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold text-danger">TOTAL BELANJA</label>
                                <input type="text" id="total_tampil" class="form-control input-kasir text-danger" value="Rp 0" readonly style="background-color: #fff0f0;">
                                <input type="hidden" name="total_akhir" id="total_akhir" value="0">
                            </div>

                            <div id="blok_pembayaran" style="display: none;">
                                <hr class="mb-4">

                                <div class="mb-3">
                                    <label class="fw-bold text-primary">METODE PEMBAYARAN</label>
                                    <select name="metode_pembayaran" id="metode_pembayaran" class="form-control" onchange="toggleMetodeBayar()">
                                        <option value="Tunai">Tunai</option>
                                        <option value="QRIS">QRIS / E-Wallet</option>
                                    </select>
                                </div>

                                <div id="area_tunai">
                                    <div class="mb-3">
                                        <label class="fw-bold text-success">UANG TUNAI DITERIMA</label>
                                        <input type="text" id="uang_bayar_input" class="form-control input-kasir text-success" placeholder="0">
                                        <input type="hidden" name="uang_bayar" id="uang_bayar_asli" value="0">
                                    </div>
                                    <div class="mb-4">
                                        <label class="fw-bold text-secondary">KEMBALIAN</label>
                                        <input type="text" id="kembalian_tampil" class="form-control input-kasir text-secondary" value="Rp 0" readonly style="background-color: #f8f9fa;">
                                    </div>
                                </div>

                                <div id="area_qr" class="mb-4" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <div class="text-center">
                                        <p class="fw-bold text-dark mb-2">TOKO KASTURI</p>

                                        <div class="qr-placeholder shadow-sm d-flex align-items-center justify-content-center" style="background: white; width: 160px; height: 160px; margin: 0 auto; border: 2px dashed #ccc; border-radius: 10px; overflow: hidden;">
                                            <img src="QR1.JPG" alt="QRIS Toko" style="width: 100%; height: 100%; object-fit: contain; padding: 5px;">
                                        </div>

                                        <p class="text-muted small mt-2 mb-0">Pastikan saldo telah masuk ke rekening sebelum menyimpan transaksi.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" id="btn-simpan" class="btn btn-warning text-dark btn-lg w-100 fw-bold rounded-pill shadow-sm">💾 SIMPAN TRANSAKSI</button>
                    </div>

            </div>
        </div>
        </form>
    </div>
    </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#id_barang').select2({
                width: '100%'
            });
        });

        let keranjang = [];
        let totalGlobal = 0;

        $('#btn-tambah').click(function() {
            let sel = $('#id_barang option:selected');
            if (!sel.val()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Pilih barang terlebih dahulu!'
                });
                return;
            }

            let id = sel.val(),
                nama = sel.data('nama'),
                harga = parseInt(sel.data('harga')),
                stok = parseInt(sel.data('stok')),
                qty = parseInt($('#qty').val());
            if (isNaN(qty) || qty < 1) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Jumlah Salah',
                    text: 'Masukkan jumlah beli yang valid!'
                });
                return;
            }

            // Hitung akumulasi qty jika barang yang sama dimasukkan lagi ke keranjang
            let itemAda = keranjang.find(item => item.id === id);
            if (itemAda) {
                if ((itemAda.qty + qty) > stok) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stok Terbatas',
                        text: `Stok tidak mencukupi! Sisa stok saat ini: ${stok}`
                    });
                    return;
                }
                itemAda.qty += qty;
                itemAda.subtotal = itemAda.harga * itemAda.qty;
            } else {
                if (qty > stok) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stok Terbatas',
                        text: `Stok tidak mencukupi! Sisa stok saat ini: ${stok}`
                    });
                    return;
                }
                keranjang.push({
                    id,
                    nama,
                    harga,
                    qty,
                    subtotal: harga * qty
                });
            }

            perbaruiTabel();
            $('#qty').val(1);
        });

        function perbaruiTabel() {
            let tbody = $('#tabel-keranjang tbody').empty();
            totalGlobal = 0;

            if (keranjang.length === 0) {
                tbody.append('<tr id="row-kosong"><td colspan="5" class="text-center text-muted py-4">Keranjang masih kosong. Tambahkan barang di sebelah kiri.</td></tr>');
                $('#blok_pembayaran').fadeOut();
            } else {
                keranjang.forEach((item, i) => {
                    totalGlobal += item.subtotal;
                    tbody.append(`<tr>
                    <td><strong>${item.nama}</strong><input type="hidden" name="arr_id_barang[]" value="${item.id}"><input type="hidden" name="arr_qty[]" value="${item.qty}"></td>
                    <td><span class="badge bg-secondary">${item.qty}</span></td>
                    <td>Rp ${item.harga.toLocaleString('id-ID')}</td>
                    <td class="fw-bold">Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                    <td><button type="button" class="btn btn-danger btn-sm rounded-circle shadow-sm" onclick="hapusItemKeranjang(${i})">×</button></td>
                </tr>`);
                });
                $('#blok_pembayaran').fadeIn();
            }

            $('#total_tampil').val("Rp " + totalGlobal.toLocaleString('id-ID'));
            $('#total_akhir').val(totalGlobal);
            kalkulasiPembayaran();
        }

        function hapusItemKeranjang(index) {
            keranjang.splice(index, 1);
            perbaruiTabel();
        }

        function toggleMetodeBayar() {
            let metode = $('#metode_pembayaran').val();
            if (metode === 'QRIS') {
                $('#area_tunai').hide();
                $('#area_qr').fadeIn();
                $('#uang_bayar_input').val('');
            } else {
                $('#area_tunai').fadeIn();
                $('#area_qr').hide();
                $('#uang_bayar_input').val('');
            }
            kalkulasiPembayaran();
        }

        function kalkulasiPembayaran() {
            let metode = $('#metode_pembayaran').val();
            if (metode === 'QRIS') {
                $('#uang_bayar_asli').val(totalGlobal);
                $('#kembalian_tampil').val("Rp 0").removeClass('text-danger').addClass('text-secondary');
            } else {
                let val = $('#uang_bayar_input').val().replace(/[^0-9]/g, '');
                $('#uang_bayar_asli').val(val || 0);
                let kembali = parseInt(val || 0) - totalGlobal;

                if (parseInt(val || 0) === 0) {
                    $('#kembalian_tampil').val("Rp 0").removeClass('text-danger').addClass('text-secondary');
                } else if (kembali < 0) {
                    $('#kembalian_tampil').val("Uang Kurang").removeClass('text-secondary').addClass('text-danger');
                } else {
                    $('#kembalian_tampil').val("Rp " + kembali.toLocaleString('id-ID')).removeClass('text-danger').addClass('text-secondary');
                }
            }
        }

        $('#uang_bayar_input').on('keyup input', function() {
            let val = $(this).val().replace(/[^0-9]/g, '');
            $(this).val(val ? parseInt(val).toLocaleString('id-ID') : '');
            kalkulasiPembayaran();
        });

        // Proses submit form dengan Konfirmasi Pop-up SweetAlert2
        $('#form-transaksi').submit(function(e) {
            e.preventDefault(); // Tahan pengiriman form default

            if (keranjang.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Keranjang Kosong',
                    text: 'Silakan isi keranjang belanja terlebih dahulu!'
                });
                return false;
            }

            let metode = $('#metode_pembayaran').val();
            let uangBayar = parseInt($('#uang_bayar_asli').val());

            if (metode === 'Tunai' && uangBayar < totalGlobal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Uang Kurang',
                    text: 'Uang pembayaran tunai kurang dari total belanja!'
                });
                return false;
            }

            // Tampilkan konfirmasi transaksi akhir
            Swal.fire({
                title: 'Simpan Transaksi?',
                text: "Pastikan data pesanan dan pembayaran sudah benar!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jalankan pengiriman data murni jika dikonfirmasi
                    this.submit();
                }
            });
        });
    </script>
</body>

</html>