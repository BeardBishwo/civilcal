<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Media Management - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="admin-layout">
    <?php include __DIR__ . '/../partials/topbar.php'; ?>

    <div class="admin-container">
        <?php include __DIR__ . '/../partials/sidebar.php'; ?>

        <main class="admin-main">
            <div class="admin-content">
                <div class="content-header">
                    <h1>Media Library</h1>
                    <p>Upload, manage, and organize your website's media files.</p>
                </div>

                <div class="media-gallery">
                    <h3>Media Files</h3>
                    <div class="media-grid">
                        <div class="media-item">
                            <h4>Sample Image</h4>
                            <p>Manage your media files here.</p>
                        </div>
        </main>
    </div>
</body>

</html>