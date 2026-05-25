<?php
session_start();
include 'config/database.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$stmt = $pdo->prepare("SELECT * FROM reports WHERE report_code = ? OR email = ? ORDER BY created_at DESC");
$stmt->execute([$search, $search]);
$reports = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Tracking - BPBD Cirebon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 50px 0;
        }
        .result-card {
            border-radius: 20px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .status-header {
            padding: 15px 20px;
            color: white;
        }
        .status-diterima { background: linear-gradient(135deg, #f39c12, #e67e22); }
        .status-ditindaklanjuti { background: linear-gradient(135deg, #3498db, #2980b9); }
        .status-dikerjakan { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
        .status-selesai { background: linear-gradient(135deg, #27ae60, #229954); }
        .progress-bar-custom {
            height: 10px;
            border-radius: 5px;
            background: #e0e0e0;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            transition: width 0.5s ease;
        }
        .photo-tracking {
            max-width: 100%;
            max-height: 200px;
            border-radius: 10px;
            cursor: pointer;
        }
        #map {
            height: 300px;
            border-radius: 10px;
            margin-top: 10px;
        }
        .modal-image {
            max-width: 100%;
            max-height: 80vh;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="text-center mb-4">
                    <a href="index.php" class="btn btn-light">
                        <i class="fas fa-home"></i> Kembali ke Beranda
                    </a>
                </div>
                
                <?php if(count($reports) > 0): ?>
                    <?php foreach($reports as $report): ?>
                        <?php
                        $statusClass = '';
                        $statusText = '';
                        $progress = 0;
                        $steps = [
                            'Diterima' => ['progress' => 25, 'icon' => 'fa-inbox', 'desc' => 'Laporan telah diterima oleh BPBD'],
                            'Ditindaklanjuti' => ['progress' => 50, 'icon' => 'fa-tasks', 'desc' => 'Tim sedang meninjau lokasi'],
                            'Dikerjakan' => ['progress' => 75, 'icon' => 'fa-hard-hat', 'desc' => 'Penanganan banjir sedang dilakukan'],
                            'Selesai' => ['progress' => 100, 'icon' => 'fa-check-circle', 'desc' => 'Penanganan banjir telah selesai']
                        ];
                        $currentStep = $steps[$report['status']];
                        $progress = $currentStep['progress'];
                        ?>
                        
                        <div class="card result-card">
                            <div class="status-header status-<?= strtolower(str_replace(' ', '', $report['status'])) ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h4 class="mb-0">
                                            <i class="fas <?= $currentStep['icon'] ?>"></i> 
                                            <?= $report['status'] ?>
                                        </h4>
                                        <p class="mb-0 mt-2">
                                            <i class="fas fa-ticket-alt"></i> Kode Laporan: <?= $report['report_code'] ?>
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <small>Tanggal Laporan</small>
                                        <p class="mb-0 fw-bold"><?= date('d/m/Y H:i', strtotime($report['created_at'])) ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body p-4">
                                <!-- Progress Bar -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Progress Penanganan</span>
                                        <span class="fw-bold"><?= $progress ?>%</span>
                                    </div>
                                    <div class="progress-bar-custom">
                                        <div class="progress-fill bg-<?= 
                                            $report['status'] == 'Diterima' ? 'warning' : 
                                            ($report['status'] == 'Ditindaklanjuti' ? 'info' : 
                                            ($report['status'] == 'Dikerjakan' ? 'primary' : 'success')) ?>" 
                                            style="width: <?= $progress ?>%">
                                        </div>
                                    </div>
                                    <p class="text-muted small mt-2">
                                        <i class="fas fa-info-circle"></i> <?= $currentStep['desc'] ?>
                                    </p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-user"></i> Informasi Pelapor</h6>
                                        <table class="table table-sm table-borderless">
                                            <tr><td width="120">Nama</td><td>: <?= htmlspecialchars($report['reporter_name']) ?></td></tr>
                                            <tr><td>Email</td><td>: <?= htmlspecialchars($report['email']) ?></td></tr>
                                            <tr><td>Telepon</td><td>: <?= htmlspecialchars($report['phone']) ?></td></tr>
                                            <tr><td>Alamat</td><td>: <?= nl2br(htmlspecialchars($report['address'])) ?></td></tr>
                                        </table>
                                        
                                        <h6 class="mt-3"><i class="fas fa-align-left"></i> Keterangan</h6>
                                        <p><?= nl2br(htmlspecialchars($report['description'])) ?></p>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <?php if($report['photo'] && file_exists('assets/uploads/' . $report['photo'])): ?>
                                            <h6><i class="fas fa-image"></i> Foto Banjir</h6>
                                            <img src="assets/uploads/<?= $report['photo'] ?>" 
                                                 class="photo-tracking img-fluid" 
                                                 alt="Foto Banjir"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#photoModal"
                                                 onclick="showPhoto('assets/uploads/<?= $report['photo'] ?>')">
                                            <small class="text-muted d-block mt-1">Klik untuk memperbesar</small>
                                        <?php endif; ?>
                                        
                                        <?php if($report['latitude'] && $report['longitude']): ?>
                                            <h6 class="mt-3"><i class="fas fa-map-marker-alt"></i> Lokasi Banjir</h6>
                                            <div id="map-<?= $report['id'] ?>" class="map-tracking" 
                                                 data-lat="<?= $report['latitude'] ?>" 
                                                 data-lng="<?= $report['longitude'] ?>"
                                                 data-address="<?= htmlspecialchars($report['address']) ?>"></div>
                                            <a href="https://www.google.com/maps?q=<?= $report['latitude'] ?>,<?= $report['longitude'] ?>" 
                                               target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                                <i class="fas fa-external-link-alt"></i> Buka di Google Maps
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-search fa-4x text-muted mb-3"></i>
                            <h4>Tidak Ditemukan</h4>
                            <p>Tidak ada laporan dengan kode/email: <strong><?= htmlspecialchars($search) ?></strong></p>
                            <a href="tracking.php" class="btn btn-primary">Coba Lagi</a>
                            <a href="report-form.php" class="btn btn-danger">Buat Laporan Baru</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal untuk foto besar -->
    <div class="modal fade" id="photoModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Foto Banjir</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalPhoto" src="" alt="Foto Banjir" class="modal-image">
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
    function showPhoto(src) {
        document.getElementById('modalPhoto').src = src;
    }
    
    // Inisialisasi semua peta
    document.querySelectorAll('.map-tracking').forEach(function(element) {
        var lat = parseFloat(element.dataset.lat);
        var lng = parseFloat(element.dataset.lng);
        var address = element.dataset.address;
        var id = 'map-' + Math.random().toString(36).substr(2, 9);
        element.id = id;
        
        var map = L.map(id).setView([lat, lng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);
        
        L.marker([lat, lng]).addTo(map).bindPopup(address).openPopup();
    });
    </script>
</body>
</html>