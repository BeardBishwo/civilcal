<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Civil City Exam' ?></title>
    
    <!-- Base URL for JavaScript -->
    <script>
        window.APP_BASE_URL = <?php echo json_encode(app_base_url('/')); ?>;
    </script>
    
    <!-- Essential CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/assets/css/app.css">

    <!-- MathJax for Formulas -->
    <script>
    window.MathJax = {
      tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']],
        displayMath: [['$$', '$$'], ['\\[', '\\]']],
        processEscapes: true
      },
      options: {
        ignoreHtmlClass: 'tex2jax_ignore',
        processHtmlClass: 'tex2jax_process'
      }
    };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js" id="MathJax-script" async></script>

    <style>
        body { background-color: #f3f4f6; overflow-y: hidden; }
        .exam-header { background: white; border-bottom: 2px solid #e5e7eb; height: 70px; display: flex; align-items: center; justify-content: space-between; padding: 0 2rem; position: fixed; top: 0; width: 100%; z-index: 100; }
        .exam-body { margin-top: 70px; height: calc(100vh - 70px); display: flex; }
        .question-area { flex: 1; padding: 2rem; overflow-y: auto; display: flex; justify-content: center; }
        .question-card { background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); width: 100%; max-width: 900px; padding: 3rem; min-height: 500px; }
        .palette-sidebar { width: 300px; background: white; border-left: 1px solid #e5e7eb; overflow-y: auto; padding: 1.5rem; }
        .q-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 0.5rem; }
        .q-btn { aspect-ratio: 1; border: 1px solid #d1d5db; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer; transition: all 0.2s; }
        .q-btn:hover { background: #f9fafb; }
        .q-btn.active { border-color: #6366f1; background: #e0e7ff; color: #4338ca; }
        .q-btn.answered { background: #10b981; color: white; border: none; }
        .q-btn.review { border-color: #f59e0b; color: #f59e0b; border-width: 2px; }
        .timer-badge { font-family: monospace; font-size: 1.5rem; font-weight: bold; color: #374151; background: #f3f4f6; padding: 0.25rem 1rem; border-radius: 8px; }
        .option-label { cursor: pointer; transition: all 0.2s; border: 2px solid transparent; }
        .option-label:hover { background-color: #f9fafb; }
        .option-radio:checked + div { border-color: #6366f1; background-color: #eef2ff; }
        
        /* MathJax sizing */
        mjx-container { font-size: 110% !important; }
    </style>
</head>
<body oncontextmenu="return false;">
    <?= $content ?? '' ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/quiz/exam-engine.js?v=<?= time() ?>"></script>
</body>
</html>
