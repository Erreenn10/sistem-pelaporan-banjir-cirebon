<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include '../config/database.php';

// Get statistics
$total = $pdo->query("SELECT COUNT(*) FROM reports")->fetchColumn();
$diterima = $pdo->query("SELECT COUNT(*) FROM reports WHERE status='Diterima'")->fetchColumn();
$ditindaklanjuti = $pdo->query("SELECT COUNT(*) FROM reports WHERE status='Ditindaklanjuti'")->fetchColumn();
$dikerjakan = $pdo->query("SELECT COUNT(*) FROM reports WHERE status='Dikerjakan'")->fetchColumn();
$selesai = $pdo->query("SELECT COUNT(*) FROM reports WHERE status='Selesai'")->fetchColumn();

// Get reports
$reports = $pdo->query("SELECT * FROM reports ORDER BY created_at DESC")->fetchAll();

// Chart data
$statusData = [$diterima, $ditindaklanjuti, $dikerjakan, $selesai];
$dailyData = $pdo->query("SELECT DATE(created_at) as date, COUNT(*) as count FROM reports GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 7")->fetchAll();

// Prepare data for JavaScript
$dates = [];
$counts = [];
foreach(array_reverse($dailyData) as $row) {
    $dates[] = $row['date'];
    $counts[] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - BPBD Cirebon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f4f6f9;
        }
        .sidebar {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            min-height: 100vh;
        }
        .sidebar .nav-link {
            color: white;
            padding: 12px 20px;
            margin: 5px 0;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.2);
            transform: translateX(5px);
        }
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.2);
        }
        .stat-card {
            border-radius: 15px;
            padding: 20px;
            color: white;
            transition: transform 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card i {
            font-size: 3rem;
            opacity: 0.7;
        }
        .content-wrapper {
            padding: 20px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card-header {
            background: white;
            border-bottom: 2px solid #f0f0f0;
            padding: 15px 20px;
            border-radius: 15px 15px 0 0;
        }
        .status-update {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 p-0 sidebar">
                <div class="text-center text-white py-4">
                    <i class="fas fa-water fa-3x"></i>
                    <h5 class="mt-2">BPBD Cirebon</h5>
                    <small>Admin Panel</small>
                </div>
                <nav class="nav flex-column px-3">
                    <a class="nav-link active" href="index.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a class="nav-link" href="#reports">
                        <i class="fas fa-list"></i> Semua Laporan
                    </a>
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </nav>
                <div class="text-center text-white mt-5">
                    <small>© 2024 BPBD Cirebon</small>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-10 p-0">
                <nav class="navbar navbar-light bg-white shadow-sm px-4">
                    <span class="navbar-brand fw-bold">
                        <i class="fas fa-tachometer-alt text-primary"></i> Dashboard
                    </span>
                    <div>
                        <span class="me-3">
                            <i class="fas fa-user-circle text-primary"></i> 
                            <?= htmlspecialchars($_SESSION['admin_username']) ?>
                        </span>
                        <a href="logout.php" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </nav>
                
                <div class="content-wrapper">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card bg-primary">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50">Total Laporan</h6>
                                        <h2 class="fw-bold mb-0"><?= $total ?></h2>
                                    </div>
                                    <i class="fas fa-file-alt"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card bg-warning">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50">Diterima</h6>
                                        <h2 class="fw-bold mb-0"><?= $diterima ?></h2>
                                    </div>
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card bg-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50">Ditindaklanjuti</h6>
                                        <h2 class="fw-bold mb-0"><?= $ditindaklanjuti ?></h2>
                                    </div>
                                    <i class="fas fa-tasks"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card bg-success">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50">Selesai</h6>
                                        <h2 class="fw-bold mb-0"><?= $selesai ?></h2>
                                    </div>
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charts -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-chart-bar text-primary"></i> 
                                        Grafik Status Laporan
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="statusChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-chart-line text-success"></i> 
                                        Grafik Laporan per Tanggal
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="dailyChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Reports Table -->
                    <div class="card" id="reports">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-table text-info"></i> 
                                Daftar Laporan Masyarakat
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="reportsTable" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Kode</th>
                                            <th>Nama Pelapor</th>
                                            <th>Email</th>
                                            <th>Alamat</th>
                                            <th>Status</th>
                                            <th>Tanggal Laporan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(count($reports) > 0): ?>
                                            <?php foreach($reports as $report): ?>
                                            <tr>
                                                <td class="fw-bold"><?= htmlspecialchars($report['report_code']) ?></td>
                                                <td><?= htmlspecialchars($report['reporter_name']) ?></td>
                                                <td><?= htmlspecialchars($report['email']) ?></td>
                                                <td><?= htmlspecialchars(substr($report['address'], 0, 30)) ?>...</td>
                                                <td>
                                                    <select class="form-select form-select-sm status-update" 
                                                            data-id="<?= $report['id'] ?>" 
                                                            style="width: 150px;">
                                                        <option value="Diterima" <?= $report['status'] == 'Diterima' ? 'selected' : '' ?>>
                                                            📋 Diterima
                                                        </option>
                                                        <option value="Ditindaklanjuti" <?= $report['status'] == 'Ditindaklanjuti' ? 'selected' : '' ?>>
                                                            🔄 Ditindaklanjuti
                                                        </option>
                                                        <option value="Dikerjakan" <?= $report['status'] == 'Dikerjakan' ? 'selected' : '' ?>>
                                                            ⚙️ Dikerjakan
                                                        </option>
                                                        <option value="Selesai" <?= $report['status'] == 'Selesai' ? 'selected' : '' ?>>
                                                            ✅ Selesai
                                                        </option>
                                                    </select>
                                                </td>
                                                <td><?= date('d/m/Y H:i', strtotime($report['created_at'])) ?></td>
                                                <td>
                                                    <a href="detail.php?id=<?= $report['id'] ?>" class="btn btn-sm btn-info" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="delete.php?id=<?= $report['id'] ?>" 
                                                       class="btn btn-sm btn-danger" 
                                                       title="Hapus"
                                                       onclick="return confirm('Yakin ingin menghapus laporan ini?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">Belum ada laporan</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Initialize DataTable
        if ($.fn.DataTable) {
            $('#reportsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                },
                pageLength: 10,
                responsive: true
            });
        }
        
        // Create Status Chart
        const ctx1 = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['Diterima', 'Ditindaklanjuti', 'Dikerjakan', 'Selesai'],
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: <?= json_encode($statusData) ?>,
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(13, 202, 240, 0.8)',
                        'rgba(13, 110, 253, 0.8)',
                        'rgba(25, 135, 84, 0.8)'
                    ],
                    borderColor: [
                        '#ffc107',
                        '#0dcaf0',
                        '#0d6efd',
                        '#198754'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        
        // Create Daily Chart
        const ctx2 = document.getElementById('dailyChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: <?= json_encode($dates) ?>,
                datasets: [{
                    label: 'Jumlah Laporan',
                    data: <?= json_encode($counts) ?>,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#dc3545',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        
        // Update status via AJAX
        $('.status-update').on('change', function() {
            const id = $(this).data('id');
            const status = $(this).val();
            const selectElement = $(this);
            
            // Show loading state
            selectElement.prop('disabled', true);
            
            $.ajax({
                url: 'update-status.php',
                method: 'POST',
                data: {
                    id: id,
                    status: status
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Show success notification
                        showNotification('Status berhasil diperbarui!', 'success');
                        // Reload page after 1 second to refresh stats
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showNotification('Gagal memperbarui status!', 'error');
                        selectElement.prop('disabled', false);
                    }
                },
                error: function() {
                    showNotification('Terjadi kesalahan!', 'error');
                    selectElement.prop('disabled', false);
                }
            });
        });
        
        // Function to show notification
        function showNotification(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const notification = $(`
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed top-0 end-0 m-3" 
                     role="alert" style="z-index: 9999; min-width: 300px;">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            $('body').append(notification);
            setTimeout(function() {
                notification.fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 3000);
        }
    });
    </script>
</body>
</html>