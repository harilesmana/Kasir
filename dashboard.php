<?php
$pageTitle = "Dashboard";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/auth_check.php';

// Data dummy untuk contoh
$bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$pendapatan = [12000000, 19000000, 3000000, 5000000, 2000000, 3000000, 4500000, 6000000, 8000000, 10000000, 12000000, 15000000];
$pengeluaran = [8000000, 12000000, 2500000, 4000000, 1500000, 2500000, 3000000, 4500000, 6000000, 7500000, 9000000, 11000000];
$keuntungan = [];

for ($i = 0; $i < count($pendapatan); $i++) {
    $keuntungan[] = $pendapatan[$i] - $pengeluaran[$i];
}

// Data produk terlaris
$produkTerlaris = [
    ['nama' => 'Laptop ASUS X441NA', 'terjual' => 125, 'pendapatan' => 5250000, 'keuntungan' => 1050000],
    ['nama' => 'Mouse Wireless', 'terjual' => 98, 'pendapatan' => 3920000, 'keuntungan' => 980000],
    ['nama' => 'Keyboard Mechanical', 'terjual' => 76, 'pendapatan' => 2280000, 'keuntungan' => 570000]
];

// Data kategori produk
$kategoriProduk = [
    ['nama' => 'Elektronik', 'persentase' => 45],
    ['nama' => 'Peralatan', 'persentase' => 25],
    ['nama' => 'Aksesoris', 'persentase' => 20],
    ['nama' => 'Lainnya', 'persentase' => 10]
];
?>

<div class="row">
    <!-- Statistik Cepat -->
    <div class="col-md-3 mb-4">
        <div class="card dashboard-card bg-primary text-white">
            <div class="card-body text-center">
                <div class="card-icon">
                    <i class="bi bi-box-seam" style="font-size: 2.5rem;"></i>
                </div>
                <h5 class="card-title">Data Barang</h5>
                <p class="card-text"><?= rand(50, 200) ?> items</p>
                <a href="master/barang.php" class="btn btn-light">Kelola</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card dashboard-card bg-success text-white">
            <div class="card-body text-center">
                <div class="card-icon">
                    <i class="bi bi-percent" style="font-size: 2.5rem;"></i>
                </div>
                <h5 class="card-title">Diskon Kode</h5>
                <p class="card-text"><?= rand(5, 20) ?> kode aktif</p>
                <a href="master/diskon.php" class="btn btn-light">Kelola</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card dashboard-card bg-info text-white">
            <div class="card-body text-center">
                <div class="card-icon">
                    <i class="bi bi-cart" style="font-size: 2.5rem;"></i>
                </div>
                <h5 class="card-title">Transaksi Hari Ini</h5>
                <p class="card-text"><?= rand(5, 50) ?> transaksi</p>
                <a href="transaksi/history.php" class="btn btn-light">Lihat</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card dashboard-card bg-warning text-dark">
            <div class="card-body text-center">
                <div class="card-icon">
                    <i class="bi bi-cash-stack" style="font-size: 2.5rem;"></i>
                </div>
                <h5 class="card-title">Keuntungan</h5>
                <p class="card-text">Rp <?= number_format(end($keuntungan), 0, ',', '.') ?></p>
                <button class="btn btn-dark" id="btnRefreshKeuntungan">
                    <i class="bi bi-arrow-clockwise"></i> Update
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Diagram Keuntungan -->
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="bi bi-graph-up me-2"></i>Analisis Keuntungan</h5>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-secondary active" data-period="month">Bulanan</button>
                    <button class="btn btn-sm btn-outline-secondary" data-period="quarter">Triwulan</button>
                    <button class="btn btn-sm btn-outline-secondary" data-period="year">Tahunan</button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="profitChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Aktivitas Terakhir -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5><i class="bi bi-clock-history me-2"></i>Aktivitas Terakhir</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach (array_slice($produkTerlaris, 0, 3) as $index => $aktivitas): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span>Transaksi <?= $aktivitas['nama'] ?></span>
                            <small class="text-muted"><?= rand(1, 60) ?> menit lalu</small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="transaksi/history.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Produk Terlaris -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-trophy me-2"></i>Produk Terlaris</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Terjual</th>
                                <th>Pendapatan</th>
                                <th>Keuntungan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($produkTerlaris as $produk): ?>
                            <tr>
                                <td><?= $produk['nama'] ?></td>
                                <td><?= $produk['terjual'] ?></td>
                                <td>Rp <?= number_format($produk['pendapatan'], 0, ',', '.') ?></td>
                                <td class="text-success">Rp <?= number_format($produk['keuntungan'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Kategori Produk -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5><i class="bi bi-tags me-2"></i>Kategori Produk</h5>
            </div>
            <div class="card-body">
                <canvas id="categoryChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Data untuk chart
    const bulan = <?php echo json_encode($bulan); ?>;
    const pendapatan = <?php echo json_encode($pendapatan); ?>;
    const pengeluaran = <?php echo json_encode($pengeluaran); ?>;
    const keuntungan = <?php echo json_encode($keuntungan); ?>;
    const kategoriProduk = <?php echo json_encode($kategoriProduk); ?>;
    
    // Format angka ke Rupiah
    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // Diagram Keuntungan
    const profitCtx = document.getElementById('profitChart').getContext('2d');
    const profitChart = new Chart(profitCtx, {
        type: 'bar',
        data: {
            labels: bulan,
            datasets: [
                {
                    label: 'Pendapatan',
                    data: pendapatan,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Pengeluaran',
                    data: pengeluaran,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Keuntungan',
                    data: keuntungan,
                    type: 'line',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 3,
                    fill: false,
                    tension: 0.1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += formatRupiah(context.raw);
                            return label;
                        }
                    }
                },
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return formatRupiah(value);
                        }
                    }
                }
            }
        }
    });
    
    // Diagram Kategori Produk
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: kategoriProduk.map(item => item.nama),
            datasets: [{
                data: kategoriProduk.map(item => item.persentase),
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            return `${label}: ${value}%`;
                        }
                    }
                }
            }
        }
    });
    
    // Filter periode
    $('[data-period]').click(function() {
        $('[data-period]').removeClass('active');
        $(this).addClass('active');
        
        const period = $(this).data('period');
        let newLabels = [];
        let newPendapatan = [];
        let newPengeluaran = [];
        let newKeuntungan = [];
        
        if (period === 'month') {
            newLabels = <?php echo json_encode($bulan); ?>;
            newPendapatan = <?php echo json_encode($pendapatan); ?>;
            newPengeluaran = <?php echo json_encode($pengeluaran); ?>;
            newKeuntungan = <?php echo json_encode($keuntungan); ?>;
        } else if (period === 'quarter') {
            newLabels = ['Q1', 'Q2', 'Q3', 'Q4'];
            
            // Hitung per kuartal
            for (let i = 0; i < 4; i++) {
                const start = i * 3;
                const end = start + 3;
                
                newPendapatan[i] = pendapatan.slice(start, end).reduce((a, b) => a + b, 0);
                newPengeluaran[i] = pengeluaran.slice(start, end).reduce((a, b) => a + b, 0);
                newKeuntungan[i] = keuntungan.slice(start, end).reduce((a, b) => a + b, 0);
            }
        } else if (period === 'year') {
            newLabels = ['2023'];
            newPendapatan = [pendapatan.reduce((a, b) => a + b, 0)];
            newPengeluaran = [pengeluaran.reduce((a, b) => a + b, 0)];
            newKeuntungan = [keuntungan.reduce((a, b) => a + b, 0)];
        }
        
        // Update chart
        profitChart.data.labels = newLabels;
        profitChart.data.datasets[0].data = newPendapatan;
        profitChart.data.datasets[1].data = newPengeluaran;
        profitChart.data.datasets[2].data = newKeuntungan;
        profitChart.update();
    });
    
    // Refresh data keuntungan
    $('#btnRefreshKeuntungan').click(function() {
        // Simulasi update data
        const newKeuntungan = keuntungan.map(val => val * (0.9 + Math.random() * 0.2));
        const lastProfit = newKeuntungan[newKeuntungan.length - 1];
        
        // Update card
        $(this).prev().text('Rp ' + lastProfit.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."));
        
        // Update chart
        profitChart.data.datasets[2].data = newKeuntungan;
        profitChart.update();
        
        // Toast notifikasi
        showToast('Data keuntungan telah diperbarui!');
    });
    
    // Fungsi show toast
    function showToast(message) {
        const toast = `
            <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <strong class="me-auto">System</strong>
                        <small>Baru saja</small>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(toast);
        setTimeout(() => $('.toast').remove(), 3000);
    }
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>