<?php

/**
 * UNIFIED FIREBASE ENGINE
 * Connects PHP Session to Firebase Auth for Realtime DB access.
 */
$firebaseToken = '';
if (isset($_SESSION['user_id'])) {
    // 1. Initialize the Token Service
    // Ensure App\Services\FirebaseAuthService exists and is autoloaded!
    try {
        $firebaseService = new \App\Services\FirebaseAuthService();

        // 2. Check Role for Admin Superpowers
        $isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');

        // 3. Mint the Custom Token
        $firebaseToken = $firebaseService->createCustomToken($_SESSION['user_id'], $isAdmin);
    } catch (Exception $e) {
        // Silent fail to not break the page, but log it if possible
        error_log("Firebase Token Error: " . $e->getMessage());
    }
}
?>

<script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-database-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-auth-compat.js"></script>

<script>
    (function() {
        // 2. Configuration (Replace these with your ACTUAL keys from Firebase Console)
        // TODO: Update these placeholder keys with your actual Firebase project settings
        // 2. Configuration (Dynamic from PHP)
        const firebaseConfig = <?php echo json_encode(\App\Config\Firebase::getConfig()); ?>;

        // 3. Initialize App (Singleton Pattern)
        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
            console.log("🔥 Firebase: System Initialized");
        }

        // 4. Auto-Authentication Bridge
        const phpToken = '<?php echo $firebaseToken; ?>';
        if (phpToken) {
            firebase.auth().signInWithCustomToken(phpToken)
                .then((userCredential) => {
                    console.log("🔥 Firebase: Authenticated as " + (userCredential.user.uid));

                    // Expose DB globally for other scripts (Lobby, Chat, Admin Monitor)
                    window.db = firebase.database();
                    window.firebaseUser = userCredential.user;
                })
                .catch((error) => {
                    console.error("🔥 Firebase Auth Error:", error);
                });
        } else {
            console.log("🔥 Firebase: Guest Mode (No Token)");
        }
    })();
</script>