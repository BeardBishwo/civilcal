<?php
// Theme Preview View
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Theme Preview - <?php echo htmlspecialchars($theme['display_name']); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: <?php echo htmlspecialchars($themeConfig['config']['colors']['primary']); ?>;
            --secondary: <?php echo htmlspecialchars($themeConfig['config']['colors']['secondary']); ?>;
            --accent: <?php echo htmlspecialchars($themeConfig['config']['colors']['accent']); ?>;
            --background: <?php echo htmlspecialchars($themeConfig['config']['colors']['background']); ?>;
            --text: <?php echo htmlspecialchars($themeConfig['config']['colors']['text']); ?>;
            --text-secondary: <?php echo htmlspecialchars($themeConfig['config']['colors']['text_secondary']); ?>;
            --font-family: <?php echo htmlspecialchars($themeConfig['config']['typography']['font_family']); ?>;
            --heading-size: <?php echo htmlspecialchars($themeConfig['config']['typography']['heading_size']); ?>;
            --body-size: <?php echo htmlspecialchars($themeConfig['config']['typography']['body_size']); ?>;
            --line-height: <?php echo htmlspecialchars($themeConfig['config']['typography']['line_height']); ?>;
        }

        body {
            background: var(--background);
            color: var(--text);
            font-family: var(--font-family);
            font-size: var(--body-size);
            line-height: var(--line-height);
        }

        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        header h1 {
            font-size: var(--heading-size);
            margin-bottom: 10px;
        }

        .hero {
            padding: 60px 20px;
            text-align: center;
            background: linear-gradient(135deg, var(--primary), var(--secondary), var(--accent));
        }

        .hero h2 {
            font-size: calc(var(--heading-size) * 0.8);
            margin-bottom: 10px;
        }

        .hero p {
            color: var(--text-secondary);
            margin-bottom: 20px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 40px 20px;
        }

        .card {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            padding: 20px;
            backdrop-filter: blur(10px);
            transition: all 0.3s;
        }

        .card:hover {
            background: rgba(255,255,255,0.15);
            transform: translateY(-5px);
        }

        .card h3 {
            color: var(--accent);
            margin-bottom: 10px;
        }

        .card p {
            color: var(--text-secondary);
            font-size: 0.9em;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background: var(--primary);
            color: var(--text);
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.3s;
        }

        .button:hover {
            background: var(--secondary);
            transform: scale(1.05);
        }

        footer {
            background: rgba(0,0,0,0.3);
            color: var(--text-secondary);
            padding: 20px;
            text-align: center;
            margin-top: 40px;
        }

        <?php if (isset($customizations['custom_css'])): ?>
            <?php echo $customizations['custom_css']; ?>
        <?php endif; ?>
    </style>
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($theme['display_name']); ?></h1>
        <p><?php echo htmlspecialchars($theme['description']); ?></p>
    </header>

    <section class="hero">
        <h2>Welcome to <?php echo htmlspecialchars($theme['display_name']); ?></h2>
        <p>This is a live preview of your theme customizations</p>
        <button class="button">Get Started</button>
    </section>

    <section class="cards">
        <div class="card">
            <h3>Feature One</h3>
            <p>Experience the primary color scheme with smooth transitions and modern design.</p>
        </div>
        <div class="card">
            <h3>Feature Two</h3>
            <p>Custom typography settings applied throughout the interface.</p>
        </div>
        <div class="card">
            <h3>Feature Three</h3>
            <p>Responsive layout that adapts to all screen sizes perfectly.</p>
        </div>
    </section>

    <footer>
        <p>&copy; 2025 <?php echo htmlspecialchars($theme['display_name']); ?> - Preview Mode</p>
    </footer>
</body>
</html>
