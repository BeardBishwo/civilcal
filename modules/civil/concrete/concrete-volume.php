<?php
$base = defined('APP_BASE') ? rtrim(APP_BASE, '/') : '/aec-calculator';
require_once $_SERVER['DOCUMENT_ROOT'] . $base . '/includes/functions.php';
$page_title = 'Concrete Volume Calculator';
$breadcrumb = [
    ['name' => 'Home', 'url' => app_base_url('index.php')],
    ['name' => 'Civil', 'url' => app_base_url('civil.php')],
    ['name' => 'Concrete Volume', 'url' => '#']
];
require_once $_SERVER['DOCUMENT_ROOT'] . $base . '/includes/header.php';
?>

<div class="container">
    <div class="calculator-wrapper">
        <h1>Concrete Volume Calculator</h1>
        <form id="concrete-volume-form">
            <div class="form-group">
                <label for="length">Length (m)</label>
                <input type="number" id="length" class="form-control" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="width">Width (m)</label>
                <input type="number" id="width" class="form-control" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="depth">Depth (m)</label>
                <input type="number" id="depth" class="form-control" step="0.01" required>
            </div>
            <button type="submit" class="btn-calculate">Calculate</button>
        </form>
        <div class="result-area" id="result-area">
            <h3>Result</h3>
            <p id="result"></p>
        </div>
    </div>
</div>

<script>
    document.getElementById('concrete-volume-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const length = parseFloat(document.getElementById('length').value);
        const width = parseFloat(document.getElementById('width').value);
        const depth = parseFloat(document.getElementById('depth').value);

        if (isNaN(length) || isNaN(width) || isNaN(depth)) {
            alert('Please enter valid numbers.');
            return;
        }
        
        const volume = length * width * depth;
        
        document.getElementById('result').innerHTML = `Volume: ${volume.toFixed(2)} m³`;
        document.getElementById('result-area').style.display = 'block';
    });
</script>

<?php
$base = defined('APP_BASE') ? rtrim(APP_BASE, '/') : '/aec-calculator';
require_once $_SERVER['DOCUMENT_ROOT'] . $base . '/includes/footer.php';
?>