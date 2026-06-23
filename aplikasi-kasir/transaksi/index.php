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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Multi-Barang - SIBSIKASIR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #1e293b;
        }

        .card {
            border-radius: 16px;
            border: none;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03);
            background: #ffffff;
        }

        .card-header {
            border-top-left-radius: 16px !important;
            border-top-right-radius: 16px !important;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.3px;
        }

        /* Desain Baru untuk Form Select2 Custom */
        .select2-container .select2-selection--single {
            height: 48px !important;
            padding: 10px 14px !important;
            border-radius: 12px !important;
            border: 1px solid #e2e8f0 !important;
            background-color: #ffffff !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px !important;
            color: #334155 !important;
            font-weight: 500;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 46px !important;
        }

        .form-control-custom {
            height: 48px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 10px 14px;
            font-weight: 500;
        }
        .form-control-custom:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        /* Gaya Khusus Area Input Nominal Kasir */
        .input-kasir {
            font-size: 1.5rem;
            font-weight: 800;
            text-align: right;
            border-radius: 14px;
            padding: 12px 18px;
            border: 2px solid #e2e8f0;
        }

        .qr-placeholder {
            width: 170px;
            height: 170px;
            background: #fff;
            border: 2px dashed #cbd5e1;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            transition: border-color 0.3s;
        }

        /* Toggle Tombol Pembayaran */
        .btn-check+label {
            border-radius: 12px;
            padding: 14px;
            font-size: 1rem;
            border: 2px solid #e2e8f0;
            color: #64748b;
            background: #fff;
            transition: all 0.2s ease;
        }
        .btn-check:checked+#label-tunai {
            border-color: #16a34a !important;
            background-color: #f0fdf4 !important;
            color: #16a34a !important;
        }
        .btn-check:checked+#label-qris {
            border-color: #2563eb !important;
            background-color: #eff6ff !important;
            color: #2563eb !important;
        }
        .btn-check+label:hover {
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }

        /* Visual Tabel Baru */
        .table modern-table th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            padding: 14px;
        }
        .table modern-table td {
            padding: 16px 14px;
            vertical-align: middle;
        }

        /* Info Badge Kasir Aktif */
        .user-badge {
            background: #ffffff;
            padding: 8px 16px;
            border-radius: 30px;
            border: 1px solid #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 0.85rem;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h3 class="fw-bold m-0 d-flex align-items-center gap-2">
                     <i class="bi bi-shop" style="color: #000000; font-size: 2.5rem;"></i> SIBSIKASIR
                </h3>
                <p class="text-muted small m-0 mt-1">Kelola penjualan barang dengan cepat, responsif, dan aman.</p>
            </div>
            <div class="user-badge shadow-sm">
                <span class="d-inline-block bg-success rounded-circle" style="width: 8px; height: 8px;"></span>
                <i class="bi bi-person-circle text-secondary"></i>
                <span><?= htmlspecialchars($_SESSION['nama']) ?> (<?= htmlspecialchars($username_aktif) ?>)</span>
            </div>
        </div>

        <div class="row g-4">
            
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white d-flex align-items-center gap-2 py-3">
                        <i class="bi bi-search"></i> Pilih & Tambah Barang Penjualan
                    </div>
                    <div class="card-body p-4">
                        <div class="row align-items-end g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-secondary">Nama / Kode Barang</label>
                                <select id="id_barang" class="form-control">
                                    <option value="">-- Cari & Pilih Barang --</option>
                                    <?php foreach ($array_barang as $brg) { ?>
                                        <option value="<?= $brg['id'] ?>" data-nama="<?= htmlspecialchars($brg['nama_barang']) ?>" data-harga="<?= $brg['harga'] ?>" data-stok="<?= $brg['stok'] ?>">
                                            <?= htmlspecialchars($brg['nama_barang']) ?> | Stok: <?= $brg['stok'] ?> | Rp <?= number_format($brg['harga'], 0, ',', '.') ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-secondary">Jumlah Beli (Qty)</label>
                                <input type="number" id="qty" class="form-control form-control-custom text-center fw-bold" min="1" value="1">
                            </div>
                            <div class="col-md-3">
                                <button type="button" id="btn-tambah" class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-2" style="height: 48px; border-radius: 12px;">
                                    <i class="bi bi-cart-plus-fill fs-5"></i> Masukkan Keranjang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <form action="simpan.php" method="POST" autocomplete="off" id="form-transaksi">
                    <div class="card shadow-lg">
                        <div class="card-header bg-primary text-white d-flex align-items-center gap-2 py-3">
                            <i class="bi bi-basket3-fill"></i> Ringkasan Daftar Belanjaan Pelanggan
                        </div>
                        <div class="card-body p-4">

                            <div class="table-responsive mb-4" style="max-height: 320px; overflow-y: auto; border: 1px solid #edf2f7; border-radius: 12px;">
                                <table class="table table-hover align-middle m-0" id="tabel-keranjang">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Detail Deskripsi Barang</th>
                                            <th width="100" class="text-center">Kuantitas</th>
                                            <th width="180" class="text-end">Harga Satuan</th>
                                            <th width="200" class="text-end">Subtotal Pembelian</th>
                                            <th width="80" class="text-center pe-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="row-kosong">
                                            <td colspan="5" class="text-center text-muted py-5">
                                                <i class="bi bi-cart-x text-secondary fs-1 d-block mb-2"></i>
                                                <span class="fw-medium">Keranjang belanja masih kosong. Tambahkan item di atas.</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mb-4">
                                <label class="fw-bold text-danger small mb-1"><i class="bi bi-tag-fill"></i> TOTAL NETTO PEMBAYARAN</label>
                                <input type="text" id="total_tampil" class="form-control input-kasir text-danger shadow-sm" value="Rp 0" readonly style="background-color: #fff5f5; border-color: #feb2b2;">
                                <input type="hidden" name="total_akhir" id="total_akhir" value="0">
                            </div>

                            <div id="blok_pembayaran" style="display: none;">
                                <hr class="my-4" style="border-style: dashed;">

                                <div class="mb-4">
                                    <label class="fw-bold text-dark small mb-2"><i class="bi bi-wallet2"></i> PILIHAN METODE PEMBAYARAN</label>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="metode_pembayaran" id="bayar_tunai" value="Tunai" checked>
                                            <label class="btn w-100 fw-bold d-flex align-items-center justify-content-center gap-2" id="label-tunai" for="bayar_tunai">
                                                <i class="bi bi-cash-stack fs-5"></i> Uang Tunai / Cash
                                            </label>
                                        </div>
                                        <div class="col-6">
                                            <input type="radio" class="btn-check" name="metode_pembayaran" id="bayar_qris" value="QRIS">
                                            <label class="btn w-100 fw-bold d-flex align-items-center justify-content-center gap-2" id="label-qris" for="bayar_qris">
                                                <i class="bi bi-qr-code-scan fs-5"></i> QRIS / E-Wallet Digital
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div id="area_tunai">
                                    <div class="mb-3">
                                        <label class="fw-bold text-success small mb-1"><i class="bi bi-box-arrow-in-right"></i> NOMINAL CASH YANG DITERIMA</label>
                                        <input type="text" id="uang_bayar_input" class="form-control input-kasir text-success shadow-sm" placeholder="0" style="background-color: #f0fdf4; border-color: #bbf7d0;">
                                        <input type="hidden" name="uang_bayar" id="uang_bayar_asli" value="0">
                                    </div>
                                    <div class="mb-4">
                                        <label class="fw-bold text-secondary small mb-1"><i class="bi bi-box-arrow-left"></i> JUMLAH UANG KEMBALIAN KASIR</label>
                                        <input type="text" id="kembalian_tampil" class="form-control input-kasir text-secondary bg-light" value="Rp 0" readonly style="border-color: #e2e8f0;">
                                    </div>
                                </div>

                                <div id="area_qr" class="mb-4" style="display: none; background: #f8fafc; padding: 24px; border-radius: 14px; border: 1px solid #e2e8f0;">
                                    <div class="text-center">
                                        <h6 class="fw-bold text-dark mb-1">TOKO KASTURI</h6>
                                        <p class="text-muted small mb-3">Pindai kode QRIS dinamis di bawah ini menggunakan aplikasi e-wallet</p>

                                        <div class="qr-placeholder shadow-sm">
                                            <img src="QR1.JPG" alt="QRIS Toko" style="width: 100%; height: 100%; object-fit: contain; padding: 6px;">
                                        </div>

                                        <div class="alert alert-warning d-inline-block mt-3 mb-0 small py-2 px-3 border-0 fw-semibold text-warning-dark" style="background: #fffbeb; color: #b45309; border-radius: 8px;">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Verifikasi mutasi masuk pada dashboard merchant Anda sebelum memproses simpan!
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row g-3 mt-2">
                                <div class="col-md-3 order-3 order-md-1">
                                    <button type="button" id="btn-kembali" class="btn btn-outline-secondary btn-lg w-100 fw-bold d-flex align-items-center justify-content-center gap-2" style="height: 52px; border-radius: 12px; font-size: 0.95rem;">
                                        <i class="bi bi-arrow-left-short fs-4"></i> Panel Dashboard
                                    </button>
                                </div>
                                <div class="col-md-4 order-2 order-md-2">
                                    <a href="riwayat.php" class="btn btn-outline-primary btn-lg w-100 fw-bold d-flex align-items-center justify-content-center gap-2" style="height: 52px; border-radius: 12px; font-size: 0.95rem;">
                                        <i class="bi bi-clock-history"></i> Buka Riwayat Nota
                                    </a>
                                </div>
                                <div class="col-md-5 order-1 order-md-3">
                                    <button type="submit" id="btn-simpan" class="btn btn-success btn-lg w-100 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-sm" style="height: 52px; border-radius: 12px; font-size: 0.95rem;">
                                        <i class="bi bi-check-circle-fill"></i> FINISH & SIMPAN TRANSAKSI
                                    </button>
                                </div>
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

            // Memicu fungsi toggle ketika tombol pembayaran diklik
            $('input[name="metode_pembayaran"]').change(function() {
                toggleMetodeBayar();
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
                    text: 'Pilih barang terlebih dahulu!',
                    confirmButtonColor: '#3b82f6'
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
                    text: 'Masukkan jumlah beli yang valid!',
                    confirmButtonColor: '#3b82f6'
                });
                return;
            }

            let itemAda = keranjang.find(item => item.id === id);
            if (itemAda) {
                if ((itemAda.qty + qty) > stok) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Stok Terbatas',
                        text: `Stok tidak mencukupi! Sisa stok saat ini: ${stok}`,
                        confirmButtonColor: '#ef4444'
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
                        text: `Stok tidak mencukupi! Sisa stok saat ini: ${stok}`,
                        confirmButtonColor: '#ef4444'
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
                tbody.append('<tr id="row-kosong"><td colspan="5" class="text-center text-muted py-5"><i class="bi bi-cart-x text-secondary fs-1 d-block mb-2"></i><span class="fw-medium">Keranjang belanja masih kosong. Tambahkan item di atas.</span></td></tr>');
                $('#blok_pembayaran').fadeOut();
            } else {
                keranjang.forEach((item, i) => {
                    totalGlobal += item.subtotal;
                    tbody.append(`<tr>
                    <td class="ps-3"><strong>${item.nama}</strong><input type="hidden" name="arr_id_barang[]" value="${item.id}"><input type="hidden" name="arr_qty[]" value="${item.qty}"></td>
                    <td class="text-center"><span class="badge bg-secondary px-2.5 py-1.5 fs-6 fw-semibold" style="border-radius:6px;">${item.qty}</span></td>
                    <td class="text-end text-secondary fw-medium">Rp ${item.harga.toLocaleString('id-ID')}</td>
                    <td class="text-end fw-bold text-dark">Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                    <td class="text-center pe-3"><button type="button" class="btn btn-sm btn-light border text-danger rounded-circle shadow-sm fw-bold d-inline-flex align-items-center justify-content-center" style="width:28px; height:28px; padding:0; font-size:16px;" onclick="hapusItemKeranjang(${i})">×</button></td>
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
            let metode = $('input[name="metode_pembayaran"]:checked').val();
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
            let metode = $('input[name="metode_pembayaran"]:checked').val();
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

        $('#form-transaksi').submit(function(e) {
            e.preventDefault();

            if (keranjang.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Keranjang Kosong',
                    text: 'Silakan isi keranjang belanja terlebih dahulu!',
                    confirmButtonColor: '#3b82f6'
                });
                return false;
            }

            let metode = $('input[name="metode_pembayaran"]:checked').val();
            let uangBayar = parseInt($('#uang_bayar_asli').val());

            if (metode === 'Tunai' && uangBayar < totalGlobal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Uang Kurang',
                    text: 'Uang pembayaran tunai kurang dari total belanja!',
                    confirmButtonColor: '#ef4444'
                });
                return false;
            }

            Swal.fire({
                title: 'Simpan Transaksi?',
                text: "Pastikan data pesanan dan pembayaran sudah benar!",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });

        $('#btn-kembali').click(function() {
            if (keranjang.length > 0) {
                Swal.fire({
                    title: 'Tinggalkan Halaman Kasir?',
                    text: "Keranjang belanja saat ini akan terhapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Ya, Tetap Kembali!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "../dashboard.php";
                    }
                });
            } else {
                window.location.href = "../dashboard.php";
            }
        });

       <?php if (isset($_GET['status'])): 
            $status = $_GET['status'];
            $metode = $_GET['metode'] ?? 'Tunai';
            
            $title = "Berhasil!";
            $text = "Transaksi telah diproses.";
            $icon = "success";

            if ($status == 'sukses' || $status == 'sukses_simpan') {
                if ($metode == 'QRIS') {
                    $title = "Transaksi QRIS Berhasil! 📱";
                    $text = "Pembayaran via QRIS/E-Wallet sukses diterima.";
                } else {
                    $title = "Transaksi Tunai Berhasil! 💵";
                    $text = "Pembayaran tunai sukses diterima.";
                }
            } elseif ($status == 'gagal' || $status == 'gagal_simpan') {
                $title = "Transaksi Gagal!";
                $text = "Terjadi kesalahan sistem saat mencoba menyimpan nota transaksi.";
                $icon = "error";
            }
        ?>
            // JIKA TRANSAKSI BERHASIL (TUNAI ATAU QRIS), GUNAKAN DETIK REAL-TIME & TIMBAL BALIK BERJALAN
            <?php if ($icon == 'success'): ?>
                let timerInterval;
                Swal.fire({
                    icon: '<?= $icon ?>',
                    title: '<?= $title ?>',
                    html: '<?= $text ?><br><br>Menutup otomatis dalam <b>3</b> detik.',
                    timer: 1500, // Durasi 3 detik
                    timerProgressBar: true, // Progress bar durasi berjalan di bagian bawah
                    showConfirmButton: true,
                    confirmButtonColor: '#16a34a',
                    confirmButtonText: 'Oke Selesai',
                    didOpen: () => {
                        Swal.showLoading();
                        const timer = Swal.getHtmlContainer().querySelector('b');
                        timerInterval = setInterval(() => {
                            // Menghitung sisa detik secara real-time (sinkron dengan sisa waktu SweetAlert2)
                            const sisaDetik = Math.ceil(Swal.getTimerLeft() / 1000);
                            if(timer) {
                                timer.textContent = sisaDetik;
                            }
                        }, 100);
                    },
                    willClose: () => {
                        clearInterval(timerInterval);
                    }
                });
            <?php else: ?>
                // JIKA TRANSAKSI GAGAL/ERROR, TETAP GUNAKAN POPUP MANUAL BIASA AGAR BISA DIBACA USER
                Swal.fire({ 
                    icon: '<?= $icon ?>', 
                    title: '<?= $title ?>', 
                    text: '<?= $text ?>', 
                    showConfirmButton: true, 
                    confirmButtonColor: '#2563eb'
                });
            <?php endif; ?>

            // Bersihkan parameter status di URL agar popup tidak terus-menerus muncul saat direfresh
            window.history.replaceState({}, document.title, window.location.pathname);
        <?php endif; ?>
    </script>
</body>

</html>