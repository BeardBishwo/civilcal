<?php
// themes/default/views/library/upload.php
?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-md p-8">
        <h1 class="text-2xl font-bold mb-6">Upload Resource</h1>
        
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <p class="text-sm text-blue-700">
                <strong>Earn Coins!</strong> Upload high-quality resources to earn <strong>100 Coins</strong> per approved file.
                Files are reviewed by admins before publishing.
            </p>
        </div>

        <form id="upload-form" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Resource Title</label>
                <input type="text" name="title" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="e.g., 2-Storey House Plan 30x40">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="Describe contents, units, scale, etc."></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="type" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                        <option value="cad">AutoCAD / DWG</option>
                        <option value="excel">Excel Sheet</option>
                        <option value="pdf">PDF Document</option>
                        <option value="doc">Word Document</option>
                        <option value="image">Image / Sketch</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File</label>
                    <input type="file" name="file" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <p class="text-xs text-gray-500 mt-1">Max 15MB. .dwg, .xlsx, .pdf, etc.</p>
                </div>
            </div>

            <button type="submit" id="submit-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition">
                🚀 Upload Resource
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('upload-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submit-btn');
    const originalText = btn.innerText;
    
    btn.disabled = true;
    btn.innerText = 'Uploading...';

    const formData = new FormData(this);

    fetch('/api/library/upload', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Upload Successful! Your file is under review.');
            window.location.href = '/library';
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.innerText = originalText;
        }
    })
    .catch(err => {
        console.error(err);
        alert('Upload failed. Please try again.');
        btn.disabled = false;
        btn.innerText = originalText;
    });
});
</script>
