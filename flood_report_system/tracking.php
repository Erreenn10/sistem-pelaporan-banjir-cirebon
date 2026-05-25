<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Laporan - BPBD Cirebon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .tracking-box {
            margin-top: 100px;
        }
        .card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center tracking-box">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white text-center">
                        <h4><i class="fas fa-search"></i> Tracking Status Laporan</h4>
                        <p>Cek status laporan banjir Anda</p>
                    </div>
                    <div class="card-body">
                        <form action="tracking-result.php" method="GET">
                            <div class="mb-3">
                                <label class="form-label">Masukkan Kode Laporan atau Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-ticket-alt"></i></span>
                                    <input type="text" name="search" class="form-control form-control-lg" 
                                           placeholder="Contoh: FL-20241201-001 atau email@example.com" required>
                                </div>
                                <small class="text-muted">Masukkan kode laporan yang didapat saat mengirim laporan</small>
                            </div>
                            <button type="submit" class="btn btn-danger btn-lg w-100">
                                <i class="fas fa-search"></i> Cari Laporan
                            </button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="index.php" class="text-decoration-none">
                                <i class="fas fa-home"></i> Kembali ke Beranda
                            </a><?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Laporan - BPBD Cirebon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .tracking-box {
            margin-top: 100px;
        }
        .card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center tracking-box">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white text-center">
                        <h4><i class="fas fa-search"></i> Tracking Status Laporan</h4>
                        <p>Cek status laporan banjir Anda</p>
                    </div>
                    <div class="card-body">
                        <form action="tracking-result.php" method="GET">
                            <div class="mb-3">
                                <label class="form-label">Masukkan Kode Laporan atau Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-ticket-alt"></i></span>
                                    <input type="text" name="search" class="form-control form-control-lg" 
                                           placeholder="Contoh: FL-20241201-001 atau email@example.com" required>
                                </div>
                                <small class="text-muted">Masukkan kode laporan yang didapat saat mengirim laporan</small>
                            </div>
                            <button type="submit" class="btn btn-danger btn-lg w-100">
                                <i class="fas fa-search"></i> Cari Laporan
                            </button>
                        </form>
                        <div class="text-center mt-3">
                            <a href="index.php" class="text-decoration-none">
                                <i class="fas fa-home"></i> Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>