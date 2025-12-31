<?php
// themes/default/views/bounty/show.php
$bounty = $data['bounty'];
$isOwner = $data['isOwner'];
$submissions = $data['submissions'] ?? [];
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Breadcrumb -->
        <a href="/bounty" class="text-gray-500 hover:text-gray-800 mb-4 inline-block">&larr; Back to Board</a>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="p-8 border-b">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($bounty->title) ?></h1>
                        <p class="text-gray-500">Posted by <?= htmlspecialchars($bounty->requester_name) ?> on <?= date('M d, Y', strtotime($bounty->created_at)) ?></p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-yellow-600">💰 <?= $bounty->bounty_amount ?></div>
                        <span class="text-xs text-gray-400 uppercase tracking-widest">Bounty Reward</span>
                    </div>
                </div>
            </div>
            
            <div class="p-8 bg-gray-50">
                <h3 class="font-bold text-gray-700 mb-2 uppercase text-sm">Requirements</h3>
                <div class="prose max-w-none text-gray-800">
                    <?= nl2br(htmlspecialchars($bounty->description)) ?>
                </div>
            </div>

            <div class="p-8 border-t flex justify-between items-center">
                <div class="text-sm">
                    Status: <span class="font-bold uppercase"><?= $bounty->status ?></span>
                </div>
                
                <?php if (!$isOwner && $bounty->status === 'open'): ?>
                    <button onclick="document.getElementById('submit-section').scrollIntoView({behavior: 'smooth'})" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-bold shadow transition">
                        Submit Work & Earn Coins
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Submissions Section -->
        <?php if ($isOwner): ?>
            <div class="bg-white rounded-xl shadow p-8 mb-8">
                <h2 class="text-2xl font-bold mb-6">Submissions (<?= count($submissions) ?>)</h2>
                
                <?php if (empty($submissions)): ?>
                    <p class="text-gray-500 italic">No submissions yet.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($submissions as $sub): ?>
                            <div class="border rounded-lg p-4 <?= $sub['client_status'] === 'accepted' ? 'border-green-500 bg-green-50' : 'border-gray-200' ?>">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-bold">By <?= htmlspecialchars($sub['uploader_name']) ?></span>
                                    <span class="text-xs text-gray-500"><?= date('M d H:i', strtotime($sub['created_at'])) ?></span>
                                </div>
                                
                                <div class="my-4">
                                     <?php if (!empty($sub['preview_path'])): ?>
                                        <div class="relative group max-w-sm border border-red-200 rounded overflow-hidden">
                                            <img src="/<?= $sub['preview_path'] ?>" alt="Protected Preview" class="w-full h-auto blur-[1px] group-hover:blur-0 transition duration-500">
                                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                <span class="bg-red-600 text-white text-xs font-bold px-2 py-1 transform -rotate-12 opacity-80">PROTECTED PREVIEW</span>
                                            </div>
                                        </div>
                                     <?php elseif ($sub['admin_status'] === 'approved'): ?>
                                        <div class="bg-gray-100 p-4 rounded text-center text-gray-500 text-sm">
                                            No visual preview available.
                                        </div>
                                     <?php endif; ?>
                                </div>

                                <div class="flex items-center justify-between mt-4">
                                    <div class="text-sm">
                                        Admin Status: <strong class="<?= $sub['admin_status'] === 'approved' ? 'text-green-600' : 'text-yellow-600' ?>"><?= $sub['admin_status'] ?></strong>
                                    </div>
                                    
                                    <?php if ($sub['admin_status'] === 'approved'): ?>
                                        <div class="flex gap-2">
                                            <?php if ($sub['client_status'] === 'pending' && $bounty->status === 'open'): ?>
                                                <button onclick="decideSubmission(<?= $sub['id'] ?>, 'accept')" class="bg-green-600 text-white px-4 py-1 rounded text-sm hover:bg-green-700">Accept & Pay</button>
                                                <button onclick="decideSubmission(<?= $sub['id'] ?>, 'reject')" class="bg-red-600 text-white px-4 py-1 rounded text-sm hover:bg-red-700">Reject</button>
                                            <?php elseif ($sub['client_status'] === 'accepted'): ?>
                                                <span class="text-green-700 font-bold px-3 py-1 bg-green-200 rounded text-sm">✓ Accepted & Paid</span>
                                                <a href="/api/bounty/download?id=<?= $sub['id'] ?>" class="bg-gray-800 text-white px-3 py-1 rounded text-sm ml-2">Download Original</a>
                                            <?php else: ?>
                                                <span class="text-red-600 font-bold">Rejected</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm italic">Waiting for admin review...</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <script>
            function decideSubmission(id, decision) {
                if (!confirm(`Are you sure you want to ${decision} this submission? Coins will be released immediately.`)) return;
                
                let reason = null;
                if (decision === 'reject') {
                    reason = prompt('Reason for rejection:');
                    if (!reason) return;
                }

                fetch('/api/bounty/decide', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ submission_id: id, decision: decision, reason: reason })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
            </script>

        <?php elseif ($bounty->status === 'open'): ?>
            <!-- Engineer Submission Form -->
            <div id="submit-section" class="bg-white rounded-xl shadow p-8">
                <h2 class="text-2xl font-bold mb-6">Submit Your Work</h2>
                <div class="bg-blue-50 p-4 rounded text-blue-800 text-sm mb-6">
                    <p>Upload your completed file here. <strong>Important:</strong> If you upload a CAD or Excel file, you MUST upload a screenshot as well for the client preview.</p>
                </div>

                <form id="submission-form">
                    <input type="hidden" name="bounty_id" value="<?= $bounty->id ?>">
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Main File (Final Asset)</label>
                        <input type="file" name="file" required class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-400 mt-1">Supported: .dwg, .pdf, .xlsx, .zip</p>
                    </div>

                    <div id="preview-upload-field" class="mb-6 hidden">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Visual Screenshot (Required for non-images)</label>
                        <input type="file" name="preview_file" accept=".jpg,.png,.jpeg" class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50">
                        <p class="text-xs text-orange-500 mt-1">This will be watermarked and shown to the client.</p>
                    </div>

                    <button type="submit" id="sub-btn" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-lg shadow-lg transition">
                        Submit Work
                    </button>
                </form>
            </div>

            <script>
            // Show screenshot field if non-image selected
            document.querySelector('input[name="file"]').addEventListener('change', function() {
                const file = this.files[0];
                if (!file) return;
                
                const ext = file.name.split('.').pop().toLowerCase();
                const previewField = document.getElementById('preview-upload-field');
                
                if (['jpg', 'png', 'pdf'].includes(ext)) {
                    previewField.classList.add('hidden');
                    previewField.querySelector('input').required = false;
                } else {
                    previewField.classList.remove('hidden');
                    previewField.querySelector('input').required = true;
                }
            });

            document.getElementById('submission-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('sub-btn');
                btn.disabled = true;
                btn.innerText = 'Creating Secure Preview...';

                const formData = new FormData(this);

                fetch('/api/bounty/submit', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        alert('Submission Successful! Preview Generated.');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                        btn.disabled = false;
                        btn.innerText = 'Submit Work';
                    }
                });
            });
            </script>
        <?php endif; ?>
    </div>
</div>
