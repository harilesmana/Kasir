<?php
$pageTitle = "Transaksi Kasir";
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth_check.php';

// Pastikan hanya kasir yang bisa akses
if ($_SESSION['role'] !== 'kasir' && $_SESSION['role'] !== 'admin') {
    header("Location: /index.php?error=unauthorized");
    exit();
}
?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-cart-plus me-2"></i>Transaksi Baru</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Daftar Barang</h6>
                            <div class="input-group" style="width: 300px;">
                                <input type="text" id="searchBarang" class="form-control" placeholder="Cari barang...">
                                <button class="btn btn-outline-secondary" type="button" id="btnSearch">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="tabelBarang">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Barang</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data barang akan diisi via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Keranjang Belanja</h6>
                    </div>
                    <div class="card-body">
                        <div id="keranjangContainer">
                            <div class="text-center text-muted py-4">
                                <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                                <p>Keranjang kosong</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <table class="table table-borderless">
                            <tr>
                                <th>Subtotal</th>
                                <td class="text-end" id="subtotal">Rp 0</td>
                            </tr>
                            <tr>
                                <th>Diskon</th>
                                <td class="text-end" id="diskon">Rp 0</td>
                            </tr>
                            <tr class="table-active">
                                <th>Total</th>
                                <td class="text-end fw-bold" id="total">Rp 0</td>
                            </tr>
                        </table>
                        
                        <div class="mb-3">
                            <label class="form-label">Pelanggan</label>
                            <input type="text" class="form-control" id="pelanggan" placeholder="Nama pelanggan">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tunai</label>
                            <input type="number" class="form-control" id="tunai" placeholder="Jumlah tunai">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kembalian</label>
                            <input type="text" class="form-control" id="kembalian" placeholder="Kembalian" readonly>
                        </div>
                        
                        <button class="btn btn-primary w-100" id="btnBayar">
                            <i class="bi bi-credit-card me-2"></i>Proses Pembayaran
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Diskon -->
<div class="modal fade" id="diskonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambahkan Diskon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Kode Diskon</label>
                    <input type="text" class="form-control" id="kodeDiskon" placeholder="Masukkan kode diskon">
                </div>
                <div class="mb-3">
                    <label class="form-label">Atau Potongan Manual</label>
                    <input type="number" class="form-control" id="potonganManual" placeholder="Jumlah potongan">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="btnApplyDiskon">Terapkan Diskon</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load data barang
    function loadBarang(search = '') {
        $.ajax({
            url: '/api/get_barang.php',
            type: 'GET',
            data: { search: search },
            success: function(response) {
                $('#tabelBarang tbody').empty();
                if (response.length > 0) {
                    response.forEach(function(barang) {
                        $('#tabelBarang tbody').append(`
                            <tr>
                                <td>${barang.kode}</td>
                                <td>${barang.nama}</td>
                                <td>Rp ${barang.harga.toLocaleString()}</td>
                                <td>${barang.stok}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary btnTambah" 
                                            data-id="${barang.id}" 
                                            data-kode="${barang.kode}" 
                                            data-nama="${barang.nama}" 
                                            data-harga="${barang.harga}" 
                                            data-stok="${barang.stok}">
                                        <i class="bi bi-plus"></i> Tambah
                                    </button>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#tabelBarang tbody').append(`
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data barang</td>
                        </tr>
                    `);
                }
            }
        });
    }

    // Inisialisasi keranjang
    let keranjang = [];
    
    // Load data barang saat halaman dimuat
    loadBarang();
    
    // Search barang
    $('#btnSearch').click(function() {
        loadBarang($('#searchBarang').val());
    });
    
    $('#searchBarang').keypress(function(e) {
        if (e.which == 13) {
            loadBarang($(this).val());
        }
    });
    
    // Tambah barang ke keranjang
    $(document).on('click', '.btnTambah', function() {
        const id = $(this).data('id');
        const kode = $(this).data('kode');
        const nama = $(this).data('nama');
        const harga = $(this).data('harga');
        const stok = $(this).data('stok');
        
        // Cek apakah barang sudah ada di keranjang
        const existingItem = keranjang.find(item => item.id == id);
        
        if (existingItem) {
            if (existingItem.qty < stok) {
                existingItem.qty++;
                existingItem.subtotal = existingItem.qty * existingItem.harga;
            } else {
                alert('Stok tidak mencukupi!');
            }
        } else {
            keranjang.push({
                id: id,
                kode: kode,
                nama: nama,
                harga: harga,
                qty: 1,
                subtotal: harga
            });
        }
        
        updateKeranjang();
    });
    
    // Update keranjang
    function updateKeranjang() {
        if (keranjang.length > 0) {
            let html = '';
            let subtotal = 0;
            
            keranjang.forEach((item, index) => {
                subtotal += item.subtotal;
                
                html += `
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <h6 class="mb-1">${item.nama}</h6>
                            <small class="text-muted">${item.kode} - Rp ${item.harga.toLocaleString()}</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <button class="btn btn-sm btn-outline-secondary btnMinus" data-index="${index}">
                                <i class="bi bi-dash"></i>
                            </button>
                            <span class="mx-2">${item.qty}</span>
                            <button class="btn btn-sm btn-outline-secondary btnPlus" data-index="${index}">
                                <i class="bi bi-plus"></i>
                            </button>
                            <span class="ms-3 fw-bold">Rp ${item.subtotal.toLocaleString()}</span>
                            <button class="btn btn-sm btn-outline-danger ms-2 btnHapus" data-index="${index}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            $('#keranjangContainer').html(html);
        } else {
            $('#keranjangContainer').html(`
                <div class="text-center text-muted py-4">
                    <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                    <p>Keranjang kosong</p>
                </div>
            `);
        }
        
        // Update total
        const diskon = parseFloat($('#diskon').text().replace('Rp ', '').replace(/\./g, '')) || 0;
        const total = subtotal - diskon;
        
        $('#subtotal').text('Rp ' + subtotal.toLocaleString());
        $('#total').text('Rp ' + total.toLocaleString());
        
        // Update kembalian
        hitungKembalian();
    }
    
    // Tambah/qty item
    $(document).on('click', '.btnPlus', function() {
        const index = $(this).data('index');
        const stok = $(this).data('stok');
        
        if (keranjang[index].qty < stok) {
            keranjang[index].qty++;
            keranjang[index].subtotal = keranjang[index].qty * keranjang[index].harga;
            updateKeranjang();
        } else {
            alert('Stok tidak mencukupi!');
        }
    });
    
    // Kurangi qty item
    $(document).on('click', '.btnMinus', function() {
        const index = $(this).data('index');
        
        if (keranjang[index].qty > 1) {
            keranjang[index].qty--;
            keranjang[index].subtotal = keranjang[index].qty * keranjang[index].harga;
            updateKeranjang();
        }
    });
    
    // Hapus item
    $(document).on('click', '.btnHapus', function() {
        const index = $(this).data('index');
        keranjang.splice(index, 1);
        updateKeranjang();
    });
    
    // Hitung kembalian
    function hitungKembalian() {
        const total = parseFloat($('#total').text().replace('Rp ', '').replace(/\./g, '')) || 0;
        const tunai = parseFloat($('#tunai').val()) || 0;
        const kembalian = tunai - total;
        
        if (kembalian >= 0) {
            $('#kembalian').val('Rp ' + kembalian.toLocaleString());
        } else {
            $('#kembalian').val('Rp 0');
        }
    }
    
    $('#tunai').on('input', hitungKembalian);
    
    // Proses pembayaran
    $('#btnBayar').click(function() {
        if (keranjang.length === 0) {
            alert('Keranjang belanja kosong!');
            return;
        }
        
        const tunai = parseFloat($('#tunai').val()) || 0;
        const total = parseFloat($('#total').text().replace('Rp ', '').replace(/\./g, '')) || 0;
        
        if (tunai < total) {
            alert('Jumlah tunai tidak mencukupi!');
            return;
        }
        
        const pelanggan = $('#pelanggan').val();
        const diskon = parseFloat($('#diskon').text().replace('Rp ', '').replace(/\./g, '')) || 0;
        
        // Simpan transaksi ke database
        $.ajax({
            url: '/api/simpan_transaksi.php',
            type: 'POST',
            data: {
                pelanggan: pelanggan,
                items: keranjang,
                subtotal: parseFloat($('#subtotal').text().replace('Rp ', '').replace(/\./g, '')),
                diskon: diskon,
                total: total,
                tunai: tunai,
                kembalian: parseFloat($('#kembalian').val().replace('Rp ', '').replace(/\./g, ''))
            },
            success: function(response) {
                if (response.success) {
                    // Cetak struk
                    window.open('/transaksi/cetak_struk.php?id=' + response.transaksi_id, '_blank');
                    
                    // Reset form
                    keranjang = [];
                    updateKeranjang();
                    $('#pelanggan').val('');
                    $('#tunai').val('');
                    $('#kembalian').val('Rp 0');
                    $('#diskon').text('Rp 0');
                    
                    alert('Transaksi berhasil disimpan!');
                } else {
                    alert('Gagal menyimpan transaksi: ' + response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat menyimpan transaksi');
            }
        });
    });
    
    // Modal diskon
    $('#btnDiskon').click(function() {
        $('#diskonModal').modal('show');
    });
    
    $('#btnApplyDiskon').click(function() {
        const kodeDiskon = $('#kodeDiskon').val();
        const potonganManual = parseFloat($('#potonganManual').val()) || 0;
        
        if (kodeDiskon) {
            // Validasi kode diskon via AJAX
            $.ajax({
                url: '/api/cek_diskon.php',
                type: 'POST',
                data: { kode: kodeDiskon },
                success: function(response) {
                    if (response.valid) {
                        const subtotal = parseFloat($('#subtotal').text().replace('Rp ', '').replace(/\./g, ''));
                        let diskon = 0;
                        
                        if (response.jenis === 'persen') {
                            diskon = subtotal * (response.nilai / 100);
                        } else {
                            diskon = response.nilai;
                        }
                        
                        $('#diskon').text('Rp ' + diskon.toLocaleString());
                        updateKeranjang();
                        $('#diskonModal').modal('hide');
                    } else {
                        alert('Kode diskon tidak valid atau sudah kadaluarsa');
                    }
                }
            });
        } else if (potonganManual > 0) {
            const subtotal = parseFloat($('#subtotal').text().replace('Rp ', '').replace(/\./g, ''));
            
            if (potonganManual > subtotal) {
                alert('Potongan tidak boleh melebihi subtotal');
            } else {
                $('#diskon').text('Rp ' + potonganManual.toLocaleString());
                updateKeranjang();
                $('#diskonModal').modal('hide');
            }
        } else {
            alert('Masukkan kode diskon atau potongan manual');
        }
    });
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>