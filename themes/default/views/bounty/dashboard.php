<?php
// themes/default/views/bounty/dashboard.php
?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold">My Bounties</h1>
        <a href="/bounty/create" class="text-blue-600 hover:underline">Post New</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <?php if (empty($data['bounties'])): ?>
            <div class="p-12 text-center text-gray-500">
                You haven't posted any bounties yet.
            </div>
        <?php else: ?>
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4 font-semibold text-gray-600">Bounty</th>
                        <th class="p-4 font-semibold text-gray-600">Amount</th>
                        <th class="p-4 font-semibold text-gray-600">Status</th>
                        <th class="p-4 font-semibold text-gray-600">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($data['bounties'] as $b): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="p-4">
                                <div class="font-medium text-gray-800"><?= htmlspecialchars($b['title']) ?></div>
                                <div class="text-xs text-gray-500"><?= date('M d, Y', strtotime($b['created_at'])) ?></div>
                            </td>
                            <td class="p-4 font-bold text-yellow-600"><?= $b['bounty_amount'] ?></td>
                            <td class="p-4">
                                <?php 
                                    $statusClass = [
                                        'open' => 'bg-green-100 text-green-800',
                                        'filled' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ][$b['status']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs font-bold uppercase <?= $statusClass ?>">
                                    <?= $b['status'] ?>
                                </span>
                            </td>
                            <td class="p-4">
                                <a href="/bounty/view/<?= $b['id'] ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Manage</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
