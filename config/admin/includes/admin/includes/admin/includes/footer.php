    <?php if (!isset($hide_sidebar) || !$hide_sidebar): ?>
            </main>
        </div>
    </div>
    <?php endif; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Initialize DataTables
        $(document).ready(function() {
            $('.data-table').DataTable({
                "pageLength": 10,
                "order": [[0, 'desc']]
            });
        });
        
        // Confirm delete
        function confirmDelete(item) {
            return confirm(`Are you sure you want to delete this ${item}? This action cannot be undone.`);
        }
        
        // Show toast notification
        function showToast(type, message) {
            const toast = $(`
                <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `);
            $('#toastContainer').append(toast);
            const bsToast = new bootstrap.Toast(toast[0]);
            bsToast.show();
            setTimeout(() => toast.remove(), 5000);
        }
        
        // Check for URL success/error parameters
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                showToast('success', urlParams.get('success'));
            }
            if (urlParams.has('error')) {
                showToast('danger', urlParams.get('error'));
            }
        });
    </script>
    
    <!-- Toast container -->
    <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
    
</body>
</html>
