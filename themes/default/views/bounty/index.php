<?php
// themes/default/views/bounty/index.php
?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Bounty Board</h1>
            <p class="text-gray-600">Find work or hire engineers</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="/bounty/dashboard" class="text-gray-600 hover:text-blue-600 font-medium">My Dashboard</a>
            <a href="/bounty/create" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition shadow-lg">
                📢 Post a Bounty
            </a>
        </div>
    </div>

    <!-- Bounty List -->
    <div id="bounty-list" class="grid gap-6">
        <!-- Loaded via AJAX -->
        <div class="text-center py-12 text-gray-500">Loading opportunities...</div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadBounties();
});

function loadBounties() {
    const container = document.getElementById('bounty-list');
    
    fetch('/api/bounty/browse')
        .then(res => res.json())
        .then(data => {
            if(!data.success || !data.bounties.length) {
                container.innerHTML = '<div class="text-center py-12 text-gray-500 bg-white rounded-xl shadow-sm">No active bounties right now. Be the first to post!</div>';
                return;
            }

            container.innerHTML = data.bounties.map(b => `
                <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition p-6 border border-gray-100 flex justify-between items-center group">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                             <span class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded-full font-bold">OPEN</span>
                             <span class="text-sm text-gray-400">Posted by ${b.requester_name} &bull; ${new Date(b.created_at).toLocaleDateString()}</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition">${b.title}</h3>
                        <p class="text-gray-600 line-clamp-2">${b.description || 'No description'}</p>
                    </div>
                    <div class="text-right min-w-[150px]">
                        <div class="text-2xl font-bold text-yellow-600 mb-2">💰 ${b.bounty_amount}</div>
                        <a href="/bounty/view/${b.id}" class="inline-block bg-gray-900 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-800 transition">
                            View Details
                        </a>
                    </div>
                </div>
            `).join('');
        })
        .catch(err => console.error(err));
}
</script>
