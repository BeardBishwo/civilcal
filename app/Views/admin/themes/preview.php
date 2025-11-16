<!DOCTYPE html>
<html>
<head>
    <title>Theme Preview</title>
    <style>
        <?php if (isset($previewCSS)): ?>
        <?php echo $previewCSS; ?>
        <?php endif; ?>
        
        body {
            font-family: var(--font-family, "Segoe UI", Tahoma, Geneva, Verdana, sans-serif);
            background-color: var(--color-background, #f7f9fc);
            color: var(--color-text, #1e293b);
            line-height: var(--line-height, 1.6);
            margin: 0;
            padding: 20px;
        }
        
        .preview-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 14px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            padding: 30px;
        }
        
        h1, h2, h3 {
            color: var(--color-primary, #4361ee);
        }
        
        .button {
            background-color: var(--color-primary, #4361ee);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .button.secondary {
            background-color: var(--color-secondary, #6c757d);
        }
        
        .card {
            background-color: white;
            border: 1px solid #E5E9F1;
            border-radius: 14px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        }
        
        .color-preview {
            display: flex;
            gap: 10px;
            margin: 10px 0;
        }
        
        .color-box {
            width: 30px;
            height: 30px;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <h1>Theme Preview</h1>
        <p>This is a live preview of your theme customizations.</p>
        
        <div class="color-preview">
            <div>
                <strong>Primary Color:</strong>
                <div class="color-box" style="background-color: var(--color-primary, #4361ee);"></div>
            </div>
            <div>
                <strong>Secondary Color:</strong>
                <div class="color-box" style="background-color: var(--color-secondary, #6c757d);"></div>
            </div>
            <div>
                <strong>Accent Color:</strong>
                <div class="color-box" style="background-color: var(--color-accent, #f093fb);"></div>
            </div>
            <div>
                <strong>Background:</strong>
                <div class="color-box" style="background-color: var(--color-background, #f7f9fc);"></div>
            </div>
        </div>
        
        <div class="card">
            <h2>Sample Content</h2>
            <p>This is a sample paragraph to demonstrate the theme typography and colors. The text should be using your customized styles.</p>
            <button class="button">Primary Button</button>
            <button class="button secondary">Secondary Button</button>
        </div>
        
        <div class="card">
            <h3>Form Elements</h3>
            <form>
                <div style="margin-bottom: 15px;">
                    <label for="sample-input">Sample Input:</label>
                    <input type="text" id="sample-input" style="width: 100%; padding: 8px; border: 1px solid #E5E9F1; border-radius: 4px;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label for="sample-select">Sample Select:</label>
                    <select id="sample-select" style="width: 100%; padding: 8px; border: 1px solid #E5E9F1; border-radius: 4px;">
                        <option>Option 1</option>
                        <option>Option 2</option>
                        <option>Option 3</option>
                    </select>
                </div>
            </form>
        </div>
    </div>
</body>
</html>