<?php
// themes/default/views/bounty/create.php
?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Post a New Bounty</h1>
            <div class="text-right">
                <span class="text-sm text-gray-500 block">Your Balance</span>
                <span class="font-bold text-yellow-600 text-lg">💰 <?= $data['coins'] ?> Coins</span>
            </div>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 text-sm text-yellow-800">
            <p><strong>Escrow Notice:</strong> The bounty amount will be <strong>locked</strong> immediately upon posting. It will be released to the engineer only when you accept their submission.</p>
        </div>

        <form id="create-bounty-form" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Project Title</label>
                <input type="text" name="title" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:outline-none" placeholder="e.g., Need Structural Analysis for 2-Story Residential">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Detailed Requirements</label>
                <textarea name="description" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:outline-none" placeholder="Be specific about what you need. E.g., Format, Codes to follow, Area size..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bounty Amount (Coins)</label>
                <input type="number" name="amount" min="10" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-500 focus:outline-none text-lg font-bold" placeholder="500">
                <p class="text-xs text-gray-500 mt-1">Minimum 10 coins.</p>
            </div>

            <button type="submit" id="submit-btn" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-3 rounded-lg transition shadow-md">
                🔒 Lock Coins & Post Bounty
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('create-bounty-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = document.getElementById('submit-btn');
    const originalText = btn.innerText;
    
    // Basic validation
    const amount = this.querySelector('[name=amount]').value;
    const currentCoins = <?= $data['coins'] ?>;
    
    if(amount > currentCoins) {
        alert('Insufficient coins! please earn or buy more coins.');
        return;
    }

    btn.disabled = true;
    btn.innerText = 'Processing...';

    const jsonData = {
        title: this.querySelector('[name=title]').value,
        description: this.querySelector('[name=description]').value,
        amount: amount
    };

    fetch('/api/bounty/create', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(jsonData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Bounty Posted Successfully!');
            window.location.href = '/bounty';
        } else {
            alert('Error: ' + data.message);
            btn.disabled = false;
            btn.innerText = originalText;
        }
    })
    .catch(err => {
        console.error(err);
        alert('Failed. Please try again.');
        btn.disabled = false;
        btn.innerText = originalText;
    });
});
</script>
