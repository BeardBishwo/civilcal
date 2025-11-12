<?php
require_once 'includes/functions.php';
init_secure_session();

$page_title = "User Profile";

// Middleware to check if user is logged in
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'includes/header.php';

// Reconstruct user array from session for compatibility
$user = [
    'username' => $_SESSION['username'] ?? '',
    'email' => $_SESSION['email'] ?? '',
    'full_name' => $_SESSION['full_name'] ?? '',
];
$profile_pic = app_base_url('assets/images/profile.png'); // Default profile picture

?>
<style>
    .profile-card {
        background: var(--glass);
        backdrop-filter: blur(20px);
        border-radius: 20px;
        padding: 2rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: var(--shadow);
        text-align: center;
    }
    .profile-img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--accent);
        margin-bottom: 1.5rem;
    }
    .profile-info p {
        font-size: 1.2rem;
        margin: 0.5rem 0;
    }
</style>

<div class="container">
    <div class="profile-card">
        <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="profile-img">
        <h1>User Profile</h1>
        <div class="profile-info">
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>