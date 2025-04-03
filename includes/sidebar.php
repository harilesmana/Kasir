<div class="sidebar">
    <div class="sidebar-header">
        <h5 class="text-center">Menu Utama</h5>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="bi bi-speedometer2"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <li class="has-submenu">
            <a href="#master" class="dropdown-toggle">
                <i class="bi bi-box-seam"></i>
                <span class="menu-title">Master Data</span>
                <i class="bi bi-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li>
                    <a href="/master/barang.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'barang.php' ? 'active' : ''; ?>">
                        <i class="bi bi-box"></i> Data Barang
                    </a>
                </li>
                <li>
                    <a href="/master/diskon.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'diskon.php' ? 'active' : ''; ?>">
                        <i class="bi bi-percent"></i> Diskon Kode
                    </a>
                </li>
                <li>
                    <a href="/master/kasir.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'kasir.php' ? 'active' : ''; ?>">
                        <i class="bi bi-person-badge"></i> Data Kasir
                    </a>
                </li>
            </ul>
        </li>
        <?php endif; ?>
        
        <li class="has-submenu">
            <a href="#transaksi" class="dropdown-toggle">
                <i class="bi bi-cash-stack"></i>
                <span class="menu-title">Transaksi</span>
                <i class="bi bi-chevron-down menu-arrow"></i>
            </a>
            <ul class="submenu">
                <li>
                    <a href="/transaksi/barang.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'barang.php' ? 'active' : ''; ?>">
                        <i class="bi bi-cart-plus"></i> Transaksi Barang
                    </a>
                </li>
                <li>
                    <a href="/transaksi/history.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : ''; ?>">
                        <i class="bi bi-clock-history"></i> History Transaksi
                    </a>
                </li>
            </ul>
        </li>
        
        <li>
            <a href="/setting.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'setting.php' ? 'active' : ''; ?>">
                <i class="bi bi-gear"></i>
                <span class="menu-title">Setting</span>
            </a>
        </li>
    </ul>
</div>