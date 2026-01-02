<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Shop Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Item Shop</h1>
            <p class="text-gray-500">Spend your hard-earned coins on exclusive items.</p>
        </div>
        <div class="bg-yellow-100 px-6 py-3 rounded-full flex items-center shadow-sm border border-yellow-200">
            <i class="fas fa-coins text-yellow-500 text-xl mr-2"></i>
            <span class="text-xl font-bold text-yellow-700" id="user-wallet">Loading...</span>
        </div>
    </div>

    <!-- Items Grid -->
    <div id="shop-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Loading Skeleton -->
        <div class="animate-pulse bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-64"></div>
        <div class="animate-pulse bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-64"></div>
        <div class="animate-pulse bg-white p-6 rounded-xl shadow-sm border border-gray-100 h-64"></div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', loadShop);

    function loadShop() {
        const grid = document.getElementById('shop-grid');
        const wallet = document.getElementById('user-wallet');

        fetch('/api/shop/items')
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;

                wallet.textContent = new Intl.NumberFormat().format(data.user_coins);
                grid.innerHTML = '';

                if (data.items.length === 0) {
                    grid.innerHTML = '<p class="col-span-3 text-center text-gray-400">No items available in the shop right now.</p>';
                    return;
                }

                data.items.forEach(item => {
                    const card = document.createElement('div');
                    card.className = 'bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow relative';
                    
                    const btnClass = item.owned 
                        ? 'bg-gray-100 text-gray-500 cursor-not-allowed' 
                        : (data.user_coins >= item.price ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-red-50 text-red-400 cursor-not-allowed');
                    
                    const btnText = item.owned ? 'Owned' : 'Buy Now';
                    const btnAction = (!item.owned && data.user_coins >= item.price) ? `onclick="purchaseItem(${item.id})"` : '';

                    card.innerHTML = `
                        <div class="p-6 text-center border-b border-gray-50 bg-gray-50/50">
                             <img src="/assets/badges/${item.icon || 'default.png'}" onerror="this.src='https://placehold.co/100'" class="w-24 h-24 mx-auto object-contain drop-shadow-md">
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-bold text-lg text-gray-800">${item.name}</h3>
                                <span class="bg-yellow-50 text-yellow-700 px-2 py-1 rounded text-xs font-bold border border-yellow-100">
                                    <i class="fas fa-coins text-yellow-500 mr-1"></i> ${item.price}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 mb-6 h-10 overflow-hidden">${item.description}</p>
                            
                            <button ${btnAction} class="w-full py-3 rounded-lg font-bold transition-colors ${btnClass}">
                                ${btnText}
                            </button>
                        </div>
                    `;
                    grid.appendChild(card);
                });
            });
    }

    function purchaseItem(id) {
        if(!confirm('Are you sure you want to purchase this item?')) return;

        fetch('/api/shop/purchase', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ item_id: id })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Purchase Successful!');
                loadShop(); // Refresh UI
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
