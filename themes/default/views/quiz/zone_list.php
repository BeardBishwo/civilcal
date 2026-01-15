<?php

/**
 * Quiz Zone - Syllabus Learning Path
 * Displays hierarchical course structure with progress tracking
 */
?>

<div class="quiz-zone-container">
    <!-- Header Section -->
    <div class="zone-header">
        <div class="header-content">
            <div class="breadcrumb">
                <a href="<?= app_base_url('/quiz') ?>"><i class="fas fa-home"></i> Dashboard</a>
                <span>/</span>
                <span>Quiz Zone</span>
            </div>
            <h1 class="zone-title">
                <i class="fas fa-bullseye"></i>
                <?= htmlspecialchars($course['title'] ?? 'Learning Path') ?>
            </h1>
            <?php if ($eduLevel): ?>
                <p class="zone-subtitle"><?= htmlspecialchars($eduLevel['title']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Progress Overview -->
        <div class="progress-overview">
            <div class="progress-stat">
                <div class="stat-value"><?= $completedUnits ?>/<?= $totalUnits ?></div>
                <div class="stat-label">Units Completed</div>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: <?= $overallProgress ?>%"></div>
            </div>
            <div class="progress-percentage"><?= $overallProgress ?>%</div>
        </div>
    </div>

    <!-- Syllabus Tree -->
    <div class="syllabus-tree">
        <?php if (empty($papers)): ?>
            <div class="empty-state">
                <i class="fas fa-book-open"></i>
                <h3>No Content Available</h3>
                <p>The syllabus for this course is being prepared. Check back soon!</p>
                <a href="<?= app_base_url('/quiz/setup') ?>" class="btn-primary">Change Course</a>
            </div>
        <?php else: ?>
            <?php foreach ($papers as $paper): ?>
                <div class="paper-section" x-data="{ open: true }">
                    <!-- Paper Header -->
                    <div class="paper-header" @click="open = !open">
                        <div class="paper-info">
                            <i class="fas fa-book" style="color: #3b82f6;"></i>
                            <h2><?= htmlspecialchars($paper['title']) ?></h2>
                            <span class="unit-count"><?= count($paper['categories'] ?? []) ?> Categories</span>
                        </div>
                        <i class="fas fa-chevron-down toggle-icon" :class="{ 'rotated': !open }"></i>
                    </div>

                    <!-- Categories -->
                    <div class="paper-content" x-show="open" x-transition>
                        <?php foreach ($paper['categories'] ?? [] as $category): ?>
                            <div class="category-section" x-data="{ catOpen: false }">
                                <!-- Category Header -->
                                <div class="category-header" @click="catOpen = !catOpen">
                                    <div class="category-info">
                                        <i class="fas fa-folder" style="color: #a855f7;"></i>
                                        <h3><?= htmlspecialchars($category['title']) ?></h3>
                                        <span class="unit-count"><?= count($category['units'] ?? []) ?> Units</span>
                                    </div>
                                    <i class="fas fa-chevron-down toggle-icon" :class="{ 'rotated': !catOpen }"></i>
                                </div>

                                <!-- Units -->
                                <div class="category-content" x-show="catOpen" x-transition>
                                    <div class="units-grid">
                                        <?php foreach ($category['units'] ?? [] as $unit): ?>
                                            <div class="unit-card <?= $unit['is_completed'] ? 'completed' : '' ?>">
                                                <!-- Unit Icon -->
                                                <div class="unit-icon">
                                                    <?php if ($unit['is_completed']): ?>
                                                        <i class="fas fa-check-circle" style="color: #10b981;"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-circle" style="color: #6b7280;"></i>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Unit Info -->
                                                <div class="unit-info">
                                                    <h4><?= htmlspecialchars($unit['title']) ?></h4>
                                                    <?php if ($unit['description']): ?>
                                                        <p class="unit-description"><?= htmlspecialchars($unit['description']) ?></p>
                                                    <?php endif; ?>

                                                    <!-- Progress Bar -->
                                                    <?php if ($unit['completion_percentage'] > 0): ?>
                                                        <div class="unit-progress">
                                                            <div class="unit-progress-bar" style="width: <?= $unit['completion_percentage'] ?>%"></div>
                                                        </div>
                                                        <span class="progress-text"><?= $unit['completion_percentage'] ?>% Complete</span>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Unit Actions -->
                                                <div class="unit-actions">
                                                    <?php if ($unit['quiz_count'] > 0): ?>
                                                        <a href="<?= app_base_url('/quiz/start/' . $unit['id']) ?>" class="btn-start-quiz">
                                                            <i class="fas fa-play"></i>
                                                            Start Quiz
                                                        </a>
                                                        <span class="quiz-count"><?= $unit['quiz_count'] ?> quiz<?= $unit['quiz_count'] > 1 ? 'zes' : '' ?></span>
                                                    <?php else: ?>
                                                        <span class="no-quiz">No quizzes yet</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    .quiz-zone-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 2rem;
    }

    /* Header */
    .zone-header {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: #94a3b8;
        margin-bottom: 1rem;
    }

    .breadcrumb a {
        color: #60a5fa;
        text-decoration: none;
        transition: color 0.3s;
    }

    .breadcrumb a:hover {
        color: #93c5fd;
    }

    .zone-title {
        font-size: 2rem;
        font-weight: 700;
        color: white;
        margin: 0 0 0.5rem 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .zone-subtitle {
        color: #94a3b8;
        font-size: 1.1rem;
        margin: 0;
    }

    /* Progress Overview */
    .progress-overview {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .progress-stat {
        text-align: center;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #60a5fa;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #94a3b8;
        margin-top: 0.25rem;
    }

    .progress-bar-container {
        flex: 1;
        height: 12px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 6px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 100%);
        border-radius: 6px;
        transition: width 0.5s ease;
    }

    .progress-percentage {
        font-size: 1.25rem;
        font-weight: 700;
        color: white;
        min-width: 60px;
        text-align: right;
    }

    /* Syllabus Tree */
    .syllabus-tree {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    /* Paper Section */
    .paper-section {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .paper-header {
        padding: 1.5rem;
        background: rgba(59, 130, 246, 0.1);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.3s;
    }

    .paper-header:hover {
        background: rgba(59, 130, 246, 0.15);
    }

    .paper-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .paper-info h2 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: white;
    }

    .unit-count {
        background: rgba(255, 255, 255, 0.1);
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-size: 0.85rem;
        color: #94a3b8;
    }

    .toggle-icon {
        color: #94a3b8;
        transition: transform 0.3s;
    }

    .toggle-icon.rotated {
        transform: rotate(-90deg);
    }

    /* Category Section */
    .paper-content {
        padding: 1rem;
    }

    .category-section {
        margin-bottom: 1rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 8px;
        overflow: hidden;
    }

    .category-header {
        padding: 1rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.3s;
    }

    .category-header:hover {
        background: rgba(168, 85, 247, 0.1);
    }

    .category-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .category-info h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: white;
    }

    /* Units Grid */
    .category-content {
        padding: 1rem;
    }

    .units-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1rem;
    }

    /* Unit Card */
    .unit-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 1.25rem;
        display: flex;
        gap: 1rem;
        transition: all 0.3s;
    }

    .unit-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        border-color: rgba(96, 165, 250, 0.3);
    }

    .unit-card.completed {
        border-color: rgba(16, 185, 129, 0.3);
        background: rgba(16, 185, 129, 0.05);
    }

    .unit-icon {
        font-size: 1.5rem;
    }

    .unit-info {
        flex: 1;
    }

    .unit-info h4 {
        margin: 0 0 0.5rem 0;
        font-size: 1rem;
        font-weight: 600;
        color: white;
    }

    .unit-description {
        font-size: 0.85rem;
        color: #94a3b8;
        margin: 0 0 0.75rem 0;
        line-height: 1.4;
    }

    .unit-progress {
        height: 4px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 2px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .unit-progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
        border-radius: 2px;
    }

    .progress-text {
        font-size: 0.75rem;
        color: #10b981;
        font-weight: 600;
    }

    .unit-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.5rem;
    }

    .btn-start-quiz {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s;
    }

    .btn-start-quiz:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .quiz-count {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .no-quiz {
        font-size: 0.85rem;
        color: #6b7280;
        font-style: italic;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 4rem;
        color: #475569;
        margin-bottom: 1rem;
    }

    .empty-state h3 {
        color: white;
        margin: 0 0 0.5rem 0;
    }

    .empty-state p {
        margin: 0 0 1.5rem 0;
    }

    .btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    @media (max-width: 768px) {
        .quiz-zone-container {
            padding: 1rem;
        }

        .zone-header {
            padding: 1.5rem;
        }

        .zone-title {
            font-size: 1.5rem;
        }

        .progress-overview {
            flex-direction: column;
            align-items: stretch;
        }

        .units-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>