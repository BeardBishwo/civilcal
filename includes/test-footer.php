<?php
// Test footer with minimal code to avoid conflicts
?>
    </div><!-- Close main content -->

    <!-- Debug: JavaScript path -->
    <script>console.log('Debug: Loading back-to-top.js from:', <?php echo json_encode(app_base_url('assets/js/back-to-top.js')); ?>);</script>

    <!-- Back to Top JavaScript -->
    <script src="<?php echo htmlspecialchars(app_base_url('assets/js/new-back-to-top.js')); ?>" type="text/javascript"></script>

    <!-- Initialization check -->
    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, checking for button...');
        var btn = document.getElementById('back-to-top-btn');
        if (!btn) {
            console.log('Button not found, creating dynamically...');
            btn = document.createElement('div');
            btn.id = 'back-to-top-btn';
            btn.className = 'back-to-top-btn';
            btn.setAttribute('role', 'button');
            btn.setAttribute('aria-label', 'Back to top');
            btn.setAttribute('tabindex', '0');
            
            btn.innerHTML = `
                <i class="fas fa-chevron-up" aria-hidden="true"></i>
                <span class="back-to-top-text">Back to Top</span>
            `;
            
            document.body.appendChild(btn);
            console.log('Button created and added to DOM');
        }
    });
    </script>

    </body>
</html>