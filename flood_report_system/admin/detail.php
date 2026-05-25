<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM reports WHERE id = ?");
$stmt->execute([$id]);
$report = $stmt->fetch();

if (!$report) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Laporan - Admin BPBD Cirebon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body {
            background: #f4f6f9;
        }
        .detail-card {
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .detail-header {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: white;
            padding: 20px;
        }
        .info-row {
            padding: 12px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-label {
            font-weight: 600;
            color: #666;
        }
        .photo-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .photo-container img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        #map {
            height: 350px;
            border-radius: 10px;
            margin-top: 10px;
        }
        .status-badge {
            padding: 8px 15px;
            border-radius: 25px;
            font-weight: 600;
        }
        .btn-back {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
        }
        .btn-back:hover {
            background: #5a6268;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="detail-card">
            <div class="detail-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">
                            <i class="fas fa-file-alt"></i> Detail Laporan Banjir
                        </h4>
                        <p class="mb-0 mt-2">Kode Laporan: <strong><?= htmlspecialchars($report['report_code']) ?></strong></p>
                    </div>
                    <span class="status-badge bg-<?= 
                        $report['status'] == 'Diterima' ? 'warning' : 
                        ($report['status'] == 'Ditindaklanjuti' ? 'info' : 
                        ($report['status'] == 'Dikerjakan' ? 'primary' : 'success')) ?> text-dark">
                        <?= $report['status'] ?>
                    </span>
                </div>
            </div>
            
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-7">
                        <h5 class="mb-3"><i class="fas fa-user-circle"></i> Informasi Pelapor</h5>
                        <div class="info-row">
                            <div class="row">
                                <div class="col-md-4 info-label">Nama Lengkap</div>
                                <div class="col-md-8">: <?= htmlspecialchars($report['reporter_name']) ?></div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="row">
                                <div class="col-md-4 info-label">Email</div>
                                <div class="col-md-8">: <?= htmlspecialchars($report['email']) ?></div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="row">
                                <div class="col-md-4 info-label">Nomor HP</div>
                                <div class="col-md-8">: <?= htmlspecialchars($report['phone']) ?></div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="row">
                                <div class="col-md-4 info-label">Alamat</div>
                                <div class="col-md-8">: <?= nl2br(htmlspecialchars($report['address'])) ?></div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="row">
                                <div class="col-md-4 info-label">Tanggal Laporan</div>
                                <div class="col-md-8">: <?= date('d F Y H:i:s', strtotime($report['created_at'])) ?></div>
                            </div>
                        </div>
                        
                        <h5 class="mt-4 mb-3"><i class="fas fa-align-left"></i> Keterangan Kejadian</h5>
                        <div class="info-row">
                            <p><?= nl2br(htmlspecialchars($report['description'])) ?></p>
                        </div>
                        
                        <?php if($report['latitude'] && $report['longitude']): ?>
                            <h5 class="mt-4 mb-3"><i class="fas fa-map-marker-alt"></i> Koordinat GPS</h5>
                            <div class="info-row">
                                <div class="row">
                                    <div class="col-md-4 info-label">Latitude</div>
                                    <div class="col-md-8">: <?= $report['latitude'] ?></div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-4 info-label">Longitude</div>
                                    <div class="col-md-8">: <?= $report['longitude'] ?></div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <a href="https://www.google.com/maps?q=<?= $report['latitude'] ?>,<?= $report['longitude'] ?>" 
                                           target="_blank" class="btn btn-sm btn-success mt-2">
                                            <i class="fas fa-external-link-alt"></i> Buka di Google Maps
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <h5 class="mt-4 mb-3"><i class="fas fa-map"></i> Peta Lokasi</h5>
                            <div id="map"></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-5">
                        <h5 class="mb-3"><i class="fas fa-image"></i> Foto Banjir</h5>
                        <div class="photo-container">
                            <?php if($report['photo'] && file_exists('../assets/uploads/' . $report['photo'])): ?>
                                <img src="../assets/uploads/<?= $report['photo'] ?>" alt="Foto Banjir" class="img-fluid">
                                <div class="mt-3">
                                    <a href="../assets/uploads/<?= $report['photo'] ?>" download class="btn btn-sm btn-primary">
                                        <i class="fas fa-download"></i> Download Foto
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-camera fa-4x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada foto yang diupload</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-end">
                    <a href="index.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <?php if($report['latitude'] && $report['longitude']): ?>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([<?= $report['latitude'] ?>, <?= $report['longitude'] ?>], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        var marker = L.marker([<?= $report['latitude'] ?>, <?= $report['longitude'] ?>]).addTo(map);
        marker.bindPopup("<b>Lokasi Banjir</b><br><?= htmlspecialchars($report['address']) ?>").openPopup();
        
        // Tambahkan circle untuk area banjir
        L.circle([<?= $report['latitude'] ?>, <?= $report['longitude'] ?>], {
            color: '#dc3545',
            fillColor: '#f03',
            fillOpacity: 0.2,
            radius: 100
        }).addTo(map);
    </script>
    <?php endif; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>