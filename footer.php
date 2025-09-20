    </div> <!-- end content -->

    <!-- Footer -->
    <footer style="text-align:center; padding:15px; margin-top:20px; color:#aaa; font-size:0.9rem;">
      &copy; <?php echo date("Y"); ?> Megah Glory - All rights reserved.Created by Ariza
    </footer>

    <!-- Bootstrap JS Bundle (WAJIB untuk modal, dropdown, tooltip, dll) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script JS Global -->
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        console.log("Megah Glory Admin loaded ✅");
      });
    </script>

    <!-- Script JS Khusus Halaman -->
    <?php if (!empty($extra_js)) echo $extra_js; ?>

  </body>
</html>
