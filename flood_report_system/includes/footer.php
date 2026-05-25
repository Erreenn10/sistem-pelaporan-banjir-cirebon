<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5><i class="fas fa-water"></i> BPBD Kabupaten Cirebon</h5>
                <p>Melayani masyarakat dalam penanggulangan bencana banjir di wilayah Pantura Pangenan Cirebon.</p>
            </div>
            <div class="col-md-4">
                <h5>Kontak Darurat</h5>
                <p><i class="fas fa-phone"></i> Call Center: 112<br>
                <i class="fab fa-whatsapp"></i> WhatsApp: 0812-3456-7890</p>
            </div>
            <div class="col-md-4">
                <h5>Lokasi</h5>
                <p>Jl. Pantura Pangenan No. 123,<br>Kabupaten Cirebon, Jawa Barat</p>
            </div>
        </div>
        <hr class="bg-light">
        <div class="text-center">
            <p>&copy; 2024 Sistem Pelaporan Banjir Pantura Pangenan Cirebon. All rights reserved.</p>
        </div>
    </div>
</footer>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        if ($.fn.DataTable) {
            $('#reportsTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                }
            });
        }
    });
</script>

</body>
</html>