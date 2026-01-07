<?php include_once __DIR__ . '/../../layouts/header.php'; ?>

<!-- Single Article View -->
<div class="relative py-16 bg-white overflow-hidden">
    <div class="hidden lg:block lg:absolute lg:inset-y-0 lg:h-full lg:w-full">
        <div class="relative h-full text-lg max-w-prose mx-auto" aria-hidden="true">
            <svg class="absolute top-12 left-full transform translate-x-32" width="404" height="384" fill="none" viewBox="0 0 404 384">
                <defs>
                    <pattern id="74b3fd99-0a6f-4271-bef2-e80eeafdf357" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor" />
                    </pattern>
                </defs>
                <rect width="404" height="384" fill="url(#74b3fd99-0a6f-4271-bef2-e80eeafdf357)" />
            </svg>
            <svg class="absolute top-1/2 right-full transform -translate-y-1/2 -translate-x-32" width="404" height="384" fill="none" viewBox="0 0 404 384">
                <defs>
                    <pattern id="f210dbf6-a58d-4871-961e-36d5016a0f49" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                        <rect x="0" y="0" width="4" height="4" class="text-gray-200" fill="currentColor" />
                    </pattern>
                </defs>
                <rect width="404" height="384" fill="url(#f210dbf6-a58d-4871-961e-36d5016a0f49)" />
            </svg>
        </div>
    </div>

    <div class="relative px-4 sm:px-6 lg:px-8">
        <div class="text-lg max-w-prose mx-auto">
            <div class="flex items-center space-x-2 text-sm text-indigo-600 font-semibold tracking-wide uppercase">
                <?php if ($article['category_name']): ?>
                    <a href="#" class="hover:underline"><?php echo htmlspecialchars($article['category_name']); ?></a>
                <?php endif; ?>
                <span class="text-gray-300">&bull;</span>
                <span><?php echo date('M d, Y', strtotime($article['published_at'])); ?></span>
            </div>
            
            <h1 class="mt-2 block text-3xl text-center leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                <?php echo htmlspecialchars($article['title']); ?>
            </h1>

            <?php if ($article['featured_image']): ?>
                <figure class="mt-8">
                    <img class="w-full rounded-lg shadow-lg object-cover h-96" 
                         src="<?php echo htmlspecialchars($article['featured_image']); ?>" 
                         alt="<?php echo htmlspecialchars($article['title']); ?>" 
                         width="1310" height="873">
                </figure>
            <?php endif; ?>

            <div class="mt-6 prose prose-indigo prose-lg text-gray-500 mx-auto">
                <?php echo $article['content']; ?>
            </div>

            <!-- Tags -->
            <?php if ($article['tags']): ?>
                <div class="mt-8 flex flex-wrap gap-2">
                    <?php foreach (explode(',', $article['tags']) as $tag): ?>
                        <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                            #<?php echo trim($tag); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Share -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Share this article</h3>
                <div class="mt-4 flex space-x-4">
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(current_url()); ?>&text=<?php echo urlencode($article['title']); ?>" target="_blank" class="text-gray-400 hover:text-blue-400 transition">
                        <i class="fab fa-twitter fa-lg"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(current_url()); ?>" target="_blank" class="text-gray-400 hover:text-blue-600 transition">
                        <i class="fab fa-facebook fa-lg"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(current_url()); ?>&title=<?php echo urlencode($article['title']); ?>" target="_blank" class="text-gray-400 hover:text-blue-700 transition">
                        <i class="fab fa-linkedin fa-lg"></i>
                    </a>
                </div>
            </div>

            <!-- Related Articles -->
            <?php if (!empty($related)): ?>
                <div class="mt-16 pt-16 border-t border-gray-200">
                    <h3 class="text-2xl font-bold text-gray-900 mb-8">Related Articles</h3>
                    <div class="grid gap-8 lg:grid-cols-2">
                        <?php foreach ($related as $relatedPost): ?>
                            <a href="<?php echo app_base_url('blog/' . $relatedPost['slug']); ?>" class="flex flex-col group">
                                <div class="flex-shrink-0">
                                    <?php if ($relatedPost['featured_image']): ?>
                                        <img class="h-48 w-full object-cover rounded-lg shadow-sm group-hover:shadow-md transition" 
                                             src="<?php echo htmlspecialchars($relatedPost['featured_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($relatedPost['title']); ?>">
                                    <?php else: ?>
                                        <div class="h-48 w-full bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-newspaper text-gray-300 text-3xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="mt-4">
                                    <h4 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition">
                                        <?php echo htmlspecialchars($relatedPost['title']); ?>
                                    </h4>
                                    <p class="mt-2 text-sm text-gray-500">
                                        <?php echo date('M d, Y', strtotime($relatedPost['published_at'])); ?>
                                    </p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../layouts/footer.php'; ?>
