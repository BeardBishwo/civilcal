<!DOCTYPE html>
<html>
<head>
    <title>PDF Preview - <?php echo htmlspecialchars($file->title); ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
        body { margin: 0; background: #525659; display: flex; flex-direction: column; align-items: center; }
        #pdf-container { width: 100%; max-width: 1000px; margin: 20px auto; }
        canvas { display: block; margin: 0 auto 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
        #controls { position: fixed; bottom: 20px; background: rgba(0,0,0,0.8); padding: 10px 20px; border-radius: 30px; color: white; display: flex; gap: 15px; align-items: center; z-index: 100; }
        button { background: transparent; border: 1px solid white; color: white; padding: 5px 10px; border-radius: 5px; cursor: pointer; }
        button:hover { background: white; color: black; }
    </style>
</head>
<body>

    <div id="controls">
        <button id="prev">Previous</button>
        <span id="page_num"></span> / <span id="page_count"></span>
        <button id="next">Next</button>
        <button onclick="window.close()" style="margin-left: 15px; border-color: #ff6b6b; color: #ff6b6b">Close</button>
    </div>

    <div id="pdf-container"></div>

    <script>
        // PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        // Path to PDF
        var url = '<?php echo $streamUrl; ?>';

        var pdfDoc = null,
            pageNum = 1,
            pageRendering = false,
            pageNumPending = null,
            scale = 1.5,
            container = document.getElementById('pdf-container');

        // Download PDF
        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
            pdfDoc = pdfDoc_;
            document.getElementById('page_count').textContent = pdfDoc.numPages;

            // Render all pages? Or one by one? 
            // Let's render page 1 first using existing logic
            renderPage(pageNum);
        });

        function renderPage(num) {
            pageRendering = true;
            
            // Clear container for single page view or append?
            // Simple viewer: Single page view
            container.innerHTML = ''; 
            var canvas = document.createElement('canvas');
            container.appendChild(canvas);
            
            var ctx = canvas.getContext('2d');

            // Fetch page
            pdfDoc.getPage(num).then(function(page) {
                var viewport = page.getViewport({scale: scale});
                canvas.height = viewport.height;
                canvas.width = viewport.width;

                var renderContext = {
                    canvasContext: ctx,
                    viewport: viewport
                };
                var renderTask = page.render(renderContext);

                renderTask.promise.then(function() {
                    pageRendering = false;
                    document.getElementById('page_num').textContent = num;
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                });
            });
        }

        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }

        function onPrevPage() {
            if (pageNum <= 1) return;
            pageNum--;
            queueRenderPage(pageNum);
        }
        document.getElementById('prev').addEventListener('click', onPrevPage);

        function onNextPage() {
            if (pageNum >= pdfDoc.numPages) return;
            pageNum++;
            queueRenderPage(pageNum);
        }
        document.getElementById('next').addEventListener('click', onNextPage);
    </script>
</body>
</html>
