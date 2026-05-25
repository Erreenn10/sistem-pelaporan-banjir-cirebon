<?php 
session_start();
include 'includes/header.php';
include 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $report_code = 'FL-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    $reporter_name = $_POST['reporter_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $description = $_POST['description'];
    $latitude = $_POST['latitude'] ?: null;
    $longitude = $_POST['longitude'] ?: null;
    
    // Upload foto dengan validasi yang lebih baik
    $photo = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $_FILES['photo']['name']);
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $file_size = $_FILES['photo']['size'];
        
        // Validasi ekstensi
        if (!in_array($file_ext, $allowed)) {
            $_SESSION['error'] = "Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.";
            header("Location: report-form.php");
            exit();
        }
        
        // Validasi ukuran
        if ($file_size > $max_size) {
            $_SESSION['error'] = "Ukuran file terlalu besar. Maksimal 5MB.";
            header("Location: report-form.php");
            exit();
        }
        
        // Buat folder uploads jika belum ada
        $upload_dir = 'assets/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Upload file
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $filename)) {
            $photo = $filename;
        } else {
            $_SESSION['error'] = "Gagal mengupload foto. Silakan coba lagi.";
            header("Location: report-form.php");
            exit();
        }
    }
    
    $sql = "INSERT INTO reports (report_code, reporter_name, email, phone, address, photo, description, latitude, longitude) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$report_code, $reporter_name, $email, $phone, $address, $photo, $description, $latitude, $longitude])) {
        $_SESSION['success'] = "Laporan berhasil dikirim! Kode laporan Anda: " . $report_code . ". Simpan kode ini untuk tracking.";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal mengirim laporan. Silakan coba lagi.";
        header("Location: report-form.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Laporan Banjir - BPBD Cirebon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS untuk OpenStreetMap -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .form-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .card {
            border-radius: 20px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        .card-header {
            background: linear-gradient(135deg, #dc3545, #c82333);
            padding: 20px 30px;
        }
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
        }
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        #map {
            height: 400px;
            border-radius: 10px;
            z-index: 1;
        }
        .location-info {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 10px;
            margin-top: 10px;
        }
        .photo-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .btn-submit {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            padding: 12px;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            transform: translateY(-2px);
        }
        .btn-get-location {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .btn-get-location:hover {
            background: #218838;
        }
        .coordinate-input {
            font-family: monospace;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container form-container">
        <div class="card">
            <div class="card-header text-white">
                <h4 class="mb-0">
                    <i class="fas fa-edit"></i> Form Laporan Banjir
                </h4>
                <p class="mb-0 mt-2">Laporkan kejadian banjir di wilayah Pantura Pangenan Cirebon</p>
            </div>
            <div class="card-body p-4">
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" id="reportForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="reporter_name" class="form-control" required placeholder="Masukkan nama lengkap">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" name="email" class="form-control" required placeholder="contoh@email.com">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Nomor HP</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" name="phone" class="form-control" required placeholder="08123456789">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Alamat/Jalan</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" name="address" class="form-control" required placeholder="Jl. Pantura Pangenan...">
                            </div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Upload Foto Banjir</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-camera"></i></span>
                                <input type="file" name="photo" class="form-control" accept="image/*" id="photoInput">
                            </div>
                            <div id="photoPreview" class="mt-2"></div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Format: JPG, PNG, GIF (Max 5MB)
                            </small>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label required-field">Keterangan Kejadian</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                                <textarea name="description" rows="4" class="form-control" required placeholder="Contoh: Banjir terjadi sejak jam 20.00 WIB dengan ketinggian air 50cm, akses jalan terputus..."></textarea>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Lokasi Banjir (GPS)</label>
                            <button type="button" class="btn btn-get-location w-100 mb-2" id="getLocationBtn">
                                <i class="fas fa-location-dot"></i> Gunakan Lokasi Saya Sekarang
                            </button>
                            <div id="map"></div>
                            <div class="location-info mt-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label small">Latitude</label>
                                        <input type="text" name="latitude" id="latitude" class="form-control form-control-sm coordinate-input" readonly placeholder="Klik pada peta">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Longitude</label>
                                        <input type="text" name="longitude" id="longitude" class="form-control form-control-sm coordinate-input" readonly placeholder="Klik pada peta">
                                    </div>
                                </div>
                                <div id="addressInfo" class="mt-2 small text-muted"></div>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-mouse-pointer"></i> Klik pada peta untuk menentukan lokasi, atau klik tombol "Gunakan Lokasi Saya"
                            </small>
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-submit text-white w-100">
                                <i class="fas fa-paper-plane"></i> Kirim Laporan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    
    <script>
    // Inisialisasi Peta
    var map = L.map('map').setView([-6.7123, 108.5432], 13);
    
    // Tile Layer OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);
    
    // Marker variable
    var marker = null;
    
    // Geocoder control untuk mencari alamat
    var geocoder = L.Control.geocoder({
        defaultMarkGeocode: false,
        placeholder: 'Cari lokasi...',
        errorMessage: 'Lokasi tidak ditemukan'
    }).on('markgeocode', function(e) {
        var center = e.geocode.center;
        map.setView(center, 15);
        if (marker) map.removeLayer(marker);
        marker = L.marker(center).addTo(map);
        document.getElementById('latitude').value = center.lat;
        document.getElementById('longitude').value = center.lng;
        getAddressFromCoords(center.lat, center.lng);
    }).addTo(map);
    
    // Fungsi untuk mendapatkan alamat dari koordinat (Reverse Geocoding)
    async function getAddressFromCoords(lat, lng) {
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
            const data = await response.json();
            if (data.display_name) {
                document.getElementById('addressInfo').innerHTML = `
                    <i class="fas fa-location-dot text-success"></i> 
                    <strong>Lokasi terpilih:</strong> ${data.display_name.substring(0, 200)}
                `;
            }
        } catch (error) {
            console.log('Gagal mendapatkan alamat');
        }
    }
    
    // Event klik pada peta
    map.on('click', function(e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;
        
        if (marker) map.removeLayer(marker);
        marker = L.marker(e.latlng).addTo(map);
        
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
        
        getAddressFromCoords(lat, lng);
    });
    
    // Fungsi mendapatkan lokasi otomatis dari browser
    document.getElementById('getLocationBtn').addEventListener('click', function() {
        if (navigator.geolocation) {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mendapatkan lokasi...';
            this.disabled = true;
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    
                    map.setView([lat, lng], 15);
                    
                    if (marker) map.removeLayer(marker);
                    marker = L.marker([lat, lng]).addTo(map);
                    
                    document.getElementById('latitude').value = lat.toFixed(8);
                    document.getElementById('longitude').value = lng.toFixed(8);
                    
                    getAddressFromCoords(lat, lng);
                    
                    document.getElementById('getLocationBtn').innerHTML = '<i class="fas fa-location-dot"></i> Gunakan Lokasi Saya Sekarang';
                    document.getElementById('getLocationBtn').disabled = false;
                },
                function(error) {
                    let errorMessage = '';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Izin lokasi ditolak. Silakan izinkan akses lokasi di browser Anda.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Informasi lokasi tidak tersedia.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Waktu permintaan lokasi habis.';
                            break;
                    }
                    alert(errorMessage);
                    document.getElementById('getLocationBtn').innerHTML = '<i class="fas fa-location-dot"></i> Gunakan Lokasi Saya Sekarang';
                    document.getElementById('getLocationBtn').disabled = false;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        } else {
            alert('Browser Anda tidak mendukung geolocation.');
        }
    });
    
    // Preview foto sebelum upload
    document.getElementById('photoInput').addEventListener('change', function(e) {
        const previewDiv = document.getElementById('photoPreview');
        previewDiv.innerHTML = '';
        
        if (this.files && this.files[0]) {
            const file = this.files[0];
            
            // Validasi ukuran file
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar! Maksimal 5MB.');
                this.value = '';
                return;
            }
            
            // Validasi tipe file
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Format file tidak didukung! Gunakan JPG, PNG, atau GIF.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'photo-preview';
                img.style.maxWidth = '200px';
                img.style.maxHeight = '200px';
                img.style.borderRadius = '10px';
                img.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
                previewDiv.appendChild(img);
                
                // Tambahkan tombol hapus
                const removeBtn = document.createElement('button');
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-danger mt-2 ms-2';
                removeBtn.innerHTML = '<i class="fas fa-trash"></i> Hapus';
                removeBtn.onclick = function() {
                    document.getElementById('photoInput').value = '';
                    previewDiv.innerHTML = '';
                };
                previewDiv.appendChild(removeBtn);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Validasi form sebelum submit
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        const latitude = document.getElementById('latitude').value;
        const longitude = document.getElementById('longitude').value;
        
        // Optional: Jika GPS tidak diisi, tetap bisa submit
        // Tapi kita beri peringatan
        if (!latitude || !longitude) {
            if (!confirm('Anda belum memilih lokasi di peta. Lanjutkan tanpa lokasi GPS?')) {
                e.preventDefault();
            }
        }
    });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>