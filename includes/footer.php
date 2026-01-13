    </div> <!-- End container -->
    
    <!-- ======================================== -->
    <!-- FOOTER -->
    <!-- ======================================== -->
    <footer class="footer mt-5 py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">
                &copy; <?php echo date('Y'); ?> Sistem Inventaris Gudang. 
                Dibuat untuk memenuhi Level 3 - SKKNI Junior Web Developer.
            </span>
        </div>
    </footer>
    
    <!-- ======================================== -->
    <!-- JAVASCRIPT LIBRARIES -->
    <!-- ======================================== -->
    
    <!-- jQuery - Required untuk DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap 5 JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS - Untuk tabel interaktif dengan search, sort, pagination -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 JS - Untuk alert konfirmasi yang cantik -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JavaScript -->
    <script src="assets/js/script.js"></script>
    
    <!-- ======================================== -->
    <!-- INLINE SCRIPTS (optional, per halaman) -->
    <!-- ======================================== -->
    <?php if (isset($inline_script)): ?>
        <script>
            <?php echo $inline_script; ?>
        </script>
    <?php endif; ?>
</body>
</html>
