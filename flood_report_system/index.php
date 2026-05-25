<?php 
session_start();
include 'includes/header.php';
include 'config/database.php';

// Ambil 3 laporan terbaru
$stmt = $pdo->query("SELECT * FROM reports ORDER BY created_at DESC LIMIT 3");
$latestReports = $stmt->fetchAll();
?>

<div class="hero-section">
    <div class="container">
        <div class="row min-vh-100 align-items-center">
            <div class="col-lg-6 text-white">
                <h1 class="display-4 fw-bold">Sistem Pelaporan Banjir</h1>
                <h2 class="h3 mb-3">Pantura Pangenan Cirebon</h2>
                <p class="lead mb-4">Laporkan kejadian banjir di wilayah Anda kepada BPBD untuk segera ditindaklanjuti.</p>
                <div class="d-flex gap-3">
                    <a href="report-form.php" class="btn btn-danger btn-lg">
                        <i class="fas fa-exclamation-triangle"></i> Laporkan Sekarang
                    </a>
                    <a href="tracking.php" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-search"></i> Tracking Laporan
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card bg-white bg-opacity-10 text-white">
                    <div class="card-body">
                        <h3 class="card-title"><i class="fas fa-bell"></i> Update Terkini</h3>
                        <?php if(count($latestReports) > 0): ?>
                            <?php foreach($latestReports as $report): ?>
                                <div class="mb-3 pb-2 border-bottom border-light">
                                    <small class="text-warning"><?= date('d/m/Y H:i', strtotime($report['created_at'])) ?></small>
                                    <p class="mb-0"><?= htmlspecialchars(substr($report['description'], 0, 100)) ?>...</p>
                                    <small>Status: 
                                        <span class="badge bg-<?= 
                                            $report['status'] == 'Diterima' ? 'warning' : 
                                            ($report['status'] == 'Ditindaklanjuti' ? 'info' : 
                                            ($report['status'] == 'Dikerjakan' ? 'primary' : 'success')) ?>">
                                            <?= $report['status'] ?>
                                        </span>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Belum ada laporan terbaru</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>