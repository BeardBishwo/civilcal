<?php
// themes/default/views/library/viewer/pdf.php
// PREMIUM DISTRACTION-FREE PDF VIEWER
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Viewing: <?php echo htmlspecialchars($file->title); ?> | Blueprint Vault</title>
    
    <!-- Load Local Library CSS -->
    <link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/library.min.css?v=' . time()); ?>">
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-900 overflow-hidden h-screen flex flex-col" x-data="pdfViewer()">

    <!-- Top Toolbar -->
    <div class="glass border-b border-white/10 px-4 py-3 flex justify-between items-center z-50 relative">
        <div class="flex items-center gap-4">
            <button @click="windowClose()" class="w-8 h-8 rounded-full hover:bg-white/10 flex items-center justify-center text-gray-400 hover:text-white transition">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div>
                <h1 class="font-bold text-white text-sm md:text-base truncate max-w-[200px] md:max-w-md leading-tight">
                    <?php echo htmlspecialchars($file->title); ?>
                </h1>
                <span class="text-[10px] text-gray-500 uppercase tracking-wider">SECURE PREVIEW MODE</span>
            </div>
        </div>

        <div class="flex items-center gap-2 md:gap-4">
            <!-- Zoom Controls -->
            <div class="hidden md:flex items-center gap-1 bg-black/40 rounded-lg p-1 border border-white/5">
                <button @click="zoomOut()" class="w-8 h-8 rounded hover:bg-white/10 text-gray-400 hover:text-white transition"><i class="fas fa-search-minus"></i></button>
                <span class="text-xs font-mono w-12 text-center text-gray-300" x-text="Math.round(scale * 100) + '%'"></span>
                <button @click="zoomIn()" class="w-8 h-8 rounded hover:bg-white/10 text-gray-400 hover:text-white transition"><i class="fas fa-search-plus"></i></button>
            </div>

            <!-- Page Nav -->
            <div class="flex items-center gap-2 bg-black/40 rounded-lg p-1 border border-white/5">
                <button @click="prevPage()" class="w-8 h-8 rounded hover:bg-white/10 text-gray-400 hover:text-white transition disabled:opacity-30" :disabled="pageNum <= 1">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <span class="text-xs font-mono text-gray-300">
                    <span x-text="pageNum"></span> / <span x-text="pageCount || '--'"></span>
                </span>
                <button @click="nextPage()" class="w-8 h-8 rounded hover:bg-white/10 text-gray-400 hover:text-white transition disabled:opacity-30" :disabled="pageNum >= pageCount">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <a href="<?php echo app_base_url('/api/library/download?id=' . $file->id); ?>" class="bg-primary hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold flex items-center gap-2 transition shadow-lg shadow-blue-900/20">
                <i class="fas fa-download"></i> <span class="hidden md:inline">Download</span>
            </a>
        </div>
    </div>

    <!-- Main PDF Area -->
    <div class="flex-grow bg-black/50 overflow-auto relative flex items-start justify-center p-4 md:p-8" id="scroll-container">
        
        <div class="relative shadow-2xl">
             <canvas id="the-canvas" class="block bg-white"></canvas>
             
             <!-- Loading Overlay -->
             <div x-show="rendering" class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center">
                 <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
             </div>
        </div>

    </div>

    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        document.addEventListener('alpine:init', () => {
            Alpine.data('pdfViewer', () => ({
                url: '<?php echo $streamUrl; ?>',
                pdfDoc: null,
                pageNum: 1,
                pageCount: 0,
                pageRendering: false,
                pageNumPending: null,
                scale: 1.2,
                rendering: true,
                canvas: null,
                ctx: null,

                init() {
                    this.canvas = document.getElementById('the-canvas');
                    this.ctx = this.canvas.getContext('2d');
                    
                    // Initial load
                    pdfjsLib.getDocument(this.url).promise.then((pdfDoc_) => {
                        this.pdfDoc = pdfDoc_;
                        this.pageCount = this.pdfDoc.numPages;
                        this.renderPage(this.pageNum);
                    }).catch(err => {
                        console.error('PDF Load Error:', err);
                        alert('Error loading document: ' + err.message);
                    });
                },

                renderPage(num) {
                    this.pageRendering = true;
                    this.rendering = true;
                    
                    // Verify if cancelled? No easy way in vanilla promise without abortcontroller
                    this.pdfDoc.getPage(num).then((page) => {
                        var viewport = page.getViewport({scale: this.scale});
                        this.canvas.height = viewport.height;
                        this.canvas.width = viewport.width;

                        var renderContext = {
                            canvasContext: this.ctx,
                            viewport: viewport
                        };
                        var renderTask = page.render(renderContext);

                        renderTask.promise.then(() => {
                            this.pageRendering = false;
                            this.rendering = false;
                            if (this.pageNumPending !== null) {
                                this.renderPage(this.pageNumPending);
                                this.pageNumPending = null;
                            }
                        });
                    });

                    // Update page counters
                    this.pageNum = num;
                },

                queueRenderPage(num) {
                    if (this.pageRendering) {
                        this.pageNumPending = num;
                    } else {
                        this.renderPage(num);
                    }
                },

                prevPage() {
                    if (this.pageNum <= 1) return;
                    this.pageNum--;
                    this.queueRenderPage(this.pageNum);
                },

                nextPage() {
                    if (this.pageNum >= this.pageCount) return;
                    this.pageNum++;
                    this.queueRenderPage(this.pageNum);
                },

                zoomIn() {
                    this.scale += 0.2;
                    this.renderPage(this.pageNum);
                },

                zoomOut() {
                    if (this.scale <= 0.4) return;
                    this.scale -= 0.2;
                    this.renderPage(this.pageNum);
                },

                windowClose() {
                    window.close();
                }
            }));
        });
    </script>
</body>
</html>
