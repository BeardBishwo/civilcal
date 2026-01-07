<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<!-- Blog Hero Section -->
<div class="relative bg-white pt-32 pb-20 lg:pt-40 lg:pb-28 overflow-hidden">
    <div class="absolute inset-0">
        <div class="absolute inset-y-0 left-0 w-1/2 bg-gray-50"></div>
    </div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h2 class="text-3xl tracking-tight font-extrabold text-gray-900 sm:text-4xl">
                Civil Engineering Blog
            </h2>
            <p class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4">
                Latest insights, tutorials, and resources for civil engineers and students.
            </p>
        </div>

        <div class="mt-12 max-w-lg mx-auto grid gap-5 lg:grid-cols-3 lg:max-w-none">
            <?php foreach ($articles as $article): ?>
                <div class="flex flex-col rounded-lg shadow-lg overflow-hidden transition hover:shadow-xl duration-300">
                    <div class="flex-shrink-0">
                        <?php if ($article['featured_image']): ?>
                            <img class="h-48 w-full object-cover" src="<?php echo htmlspecialchars($article['featured_image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                        <?php else: ?>
                            <div class="h-48 w-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center">
                                <i class="fas fa-newspaper text-white text-4xl opacity-50"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <?php if ($article['category_name']): ?>
                                    <p class="text-sm font-medium text-indigo-600">
                                        <a href="#" class="hover:underline">
                                            <?php echo htmlspecialchars($article['category_name']); ?>
                                        </a>
                                    </p>
                                <?php endif; ?>
                                <span class="text-gray-300">&bull;</span>
                                <p class="text-sm text-gray-500">
                                    <?php echo date('M d, Y', strtotime($article['published_at'])); ?>
                                </p>
                            </div>
                            <a href="<?php echo app_base_url('blog/' . $article['slug']); ?>" class="block mt-2">
                                <p class="text-xl font-semibold text-gray-900 line-clamp-2">
                                    <?php echo htmlspecialchars($article['title']); ?>
                                </p>
                                <p class="mt-3 text-base text-gray-500 line-clamp-3">
                                    <?php 
                                        if ($article['excerpt']) {
                                            echo htmlspecialchars($article['excerpt']);
                                        } else {
                                            echo strip_tags(substr($article['content'], 0, 150)) . '...';
                                        }
                                    ?>
                                </p>
                            </a>
                        </div>
                        <div class="mt-6 flex items-center">
                            <div class="flex-shrink-0">
                                <a href="#">
                                    <span class="sr-only"><?php echo htmlspecialchars($article['first_name']); ?></span>
                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name=<?php echo urlencode($article['first_name'] . ' ' . $article['last_name']); ?>&background=random" alt="">
                                </a>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($article['first_name'] . ' ' . $article['last_name']); ?>
                                </p>
                                <div class="flex space-x-1 text-sm text-gray-500">
                                    <time datetime="<?php echo $article['published_at']; ?>">
                                        <?php echo ceil(str_word_count(strip_tags($article['content'])) / 200); ?> min read
                                    </time>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-12 flex justify-center">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo $page == $i ? 'text-indigo-600 bg-indigo-50' : 'text-gray-700 hover:bg-gray-50'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
