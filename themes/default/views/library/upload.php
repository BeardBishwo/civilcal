<?php
// themes/default/views/library/upload.php
// PREMIUM TRANSMITTER UI
?>

<!-- Load Local Library CSS -->
<link rel="stylesheet" href="<?php echo app_base_url('themes/default/assets/css/library.min.css?v=' . time()); ?>">
<!-- Load Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<div class="bg-background min-h-screen font-sans text-white relative overflow-hidden" x-data="uploadTransmitter()">
    
    <!-- Background Gradient Mesh -->
    <div class="fixed inset-0 pointer-events-none z-0">
        <div class="absolute top-[-20%] right-[-20%] w-[800px] h-[800px] bg-primary/20 rounded-full blur-[120px] animate-pulse"></div>
        <div class="absolute bottom-[-20%] left-[-20%] w-[800px] h-[800px] bg-accent/20 rounded-full blur-[120px] animate-pulse delay-700"></div>
    </div>

    <!-- Navigation Bar (Simple) -->
    <nav class="absolute top-0 left-0 w-full z-30 p-6 flex justify-between items-center bg-gradient-to-b from-black/50 to-transparent">
        <a href="<?= app_base_url('/library') ?>" class="flex items-center gap-2 text-gray-400 hover:text-white transition group">
            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center group-hover:bg-white/20">
                <i class="fas fa-arrow-left"></i>
            </div>
            <span class="font-bold text-sm tracking-widest uppercase">Return to Vault</span>
        </a>
        <div class="px-4 py-1 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-gray-400">
            SECURE UPLINK // V.2.0
        </div>
    </nav>

    <div class="container max-w-4xl mx-auto px-4 py-24 relative z-10">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-7xl font-black mb-4 tracking-tighter bg-gradient-to-r from-white via-gray-200 to-gray-500 bg-clip-text text-transparent">
                Transmitter
            </h1>
            
            <?php $reward = \App\Services\SettingsService::get('library_upload_reward', 100); ?>
            <div class="inline-flex items-center gap-3 px-6 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary font-bold tracking-wider text-sm shadow-[0_0_20px_rgba(102,126,234,0.3)] animate-pulse">
                <i class="fas fa-sparkles"></i>
                <span>EARN <?php echo $reward; ?> BB COINS PER APPROVED ASSET</span>
            </div>
        </div>

        <form @submit.prevent="submitUpload" class="glass-card p-8 md:p-12 relative overflow-hidden">
            <!-- Decorative Border Glow -->
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-primary to-transparent opacity-50"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Project Identity -->
                <div class="md:col-span-2 space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Project Identity <span class="text-primary">*</span></label>
                        <input type="text" x-model="form.title" required 
                               class="glass-input w-full py-4 text-lg bg-black/30 focus:bg-black/50" 
                               placeholder="Enter a professional title...">
                    </div>
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Core Specifications <span class="text-primary">*</span></label>
                    <textarea x-model="form.description" required rows="4"
                              class="glass-input w-full py-3 bg-black/30 focus:bg-black/50 resize-none"
                              placeholder="Describe the technical value of this submission..."></textarea>
                </div>

                <!-- Category Selector -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Classification</label>
                    <select x-model="form.type" class="glass-input w-full py-3 bg-black/30 cursor-pointer appearance-none">
                        <option value="cad">AutoCAD (.dwg/.dxf)</option>
                        <option value="solidworks">SolidWorks (.sldprt)</option>
                        <option value="excel">Excel (.xlsx/.xlsm)</option>
                        <option value="pdf">PDF Documentation</option>
                        <option value="doc">Word / Text</option>
                        <option value="image">Image / Sketch</option>
                    </select>
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Unlock Price (BB)</label>
                    <div class="relative">
                        <input type="number" x-model="form.price" min="0" class="glass-input w-full py-3 bg-black/30 pl-10" placeholder="0">
                        <i class="fas fa-coins absolute left-4 top-1/2 -translate-y-1/2 text-yellow-500"></i>
                    </div>
                </div>

                <!-- Tags -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Discovery Tags</label>
                    <input type="text" x-model="form.tags" 
                           class="glass-input w-full py-3 bg-black/30" 
                           placeholder="structural, villa, cad, architectural (Comma separated)">
                    <p class="text-[10px] text-gray-500 mt-2">Max 5 hashtags. Help users find your contribution.</p>
                </div>

                <!-- Upload Zones -->
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Main Payload <span class="text-primary">*</span></label>
                    <div @dragover.prevent="dragOver = true" @dragleave.prevent="dragOver = false" @drop.prevent="handleDrop($event, 'main')" 
                         class="relative h-40 rounded-xl border-2 border-dashed border-white/10 flex flex-col items-center justify-center transition-all bg-black/20 group hover:bg-black/30 hover:border-primary/50 cursor-pointer overflow-hidden"
                         :class="dragOver ? 'border-primary bg-primary/10' : ''">
                        
                        <input type="file" @change="handleFileSelect($event, 'main')" class="absolute inset-0 opacity-0 cursor-pointer z-20" required>
                        
                        <template x-if="!files.main">
                            <div class="text-center p-4">
                                <i class="fas fa-file-export text-3xl text-gray-500 mb-3 group-hover:text-primary transition-colors"></i>
                                <p class="text-sm font-bold text-gray-300">Deposit Primary File</p>
                                <span class="text-[10px] text-gray-500 block mt-1">Max 15MB (.dwg, .pdf, .xls)</span>
                            </div>
                        </template>
                        
                        <template x-if="files.main">
                            <div class="text-center p-4 w-full h-full bg-primary/10 flex flex-col items-center justify-center">
                                <i class="fas fa-check-circle text-4xl text-green-400 mb-2"></i>
                                <p class="text-sm font-bold text-white truncate max-w-[200px]" x-text="files.main.name"></p>
                                <span class="text-[10px] text-primary font-bold mt-1 uppercase tracking-wider">Ready to Transmit</span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="md:col-span-1">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                        Visual Preview <span x-show="['cad','solidworks'].includes(form.type)" class="text-primary">(REQUIRED)</span>
                    </label>
                    <div class="relative h-40 rounded-xl border-2 border-dashed border-white/10 flex flex-col items-center justify-center transition-all bg-black/20 group hover:bg-black/30 hover:border-accent/50 cursor-pointer overflow-hidden">
                        
                        <input type="file" @change="handleFileSelect($event, 'preview')" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer z-20" :required="['cad','solidworks'].includes(form.type)">
                        
                        <template x-if="!files.preview">
                            <div class="text-center p-4">
                                <i class="fas fa-image text-3xl text-gray-500 mb-3 group-hover:text-accent transition-colors"></i>
                                <p class="text-sm font-bold text-gray-300">Attach Snapshot</p>
                                <span class="text-[10px] text-gray-500 block mt-1">Recommended for visibility</span>
                            </div>
                        </template>
                        
                        <template x-if="files.preview">
                            <div class="absolute inset-0 w-full h-full">
                                <img :src="previewUrl" class="w-full h-full object-cover opacity-50">
                                <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/40 backdrop-blur-sm">
                                    <i class="fas fa-image text-2xl text-accent mb-2"></i>
                                    <p class="text-xs font-bold text-white">Preview Attached</p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="md:col-span-2 mt-4">
                    <button type="submit" :disabled="uploading" 
                            class="w-full py-5 rounded-xl font-black text-lg uppercase tracking-widest flex items-center justify-center gap-3 transition-all relative overflow-hidden group shadow-xl"
                            :class="uploading ? 'bg-gray-800 text-gray-500 cursor-not-allowed' : 'bg-gradient-to-r from-primary to-blue-600 hover:from-primary/90 hover:to-blue-500 text-white hover:scale-[1.01] hover:shadow-primary/40'">
                        
                        <span x-show="!uploading" class="flex items-center gap-3">
                            <i class="fas fa-satellite-dish group-hover:rotate-12 transition-transform"></i>
                            Initialize Transmission
                        </span>
                        
                        <span x-show="uploading" class="flex items-center gap-3">
                            <i class="fas fa-circle-notch fa-spin text-white"></i>
                            Transmitting...
                        </span>
                        
                        <!-- Shine Effect -->
                        <div x-show="!uploading" class="absolute top-0 -inset-full h-full w-1/2 z-5 block transform -skew-x-12 bg-gradient-to-r from-transparent to-white opacity-20 group-hover:animate-shine"></div>
                    </button>

                     <!-- Status Message -->
                     <div x-show="status.message" class="mt-4 text-center font-bold text-sm tracking-wide flex items-center justify-center gap-2 animate-fade-in-up"
                          :class="status.type === 'error' ? 'text-red-400' : 'text-green-400'">
                         <i class="fas" :class="status.type === 'error' ? 'fa-exclamation-triangle' : 'fa-check-circle'"></i>
                         <span x-text="status.message"></span>
                     </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('uploadTransmitter', () => ({
        form: {
            title: '',
            description: '',
            type: 'cad',
            price: 0,
            tags: ''
        },
        files: {
            main: null,
            preview: null
        },
        previewUrl: null,
        dragOver: false,
        uploading: false,
        status: { message: '', type: '' },

        handleFileSelect(event, type) {
            const file = event.target.files[0];
            if (file) this.processFile(file, type);
        },

        handleDrop(event, type) {
            this.dragOver = false;
            const file = event.dataTransfer.files[0];
            if (file) this.processFile(file, type);
        },

        processFile(file, type) {
            if (type === 'preview' && file.type.startsWith('image/')) {
                this.files.preview = file;
                this.previewUrl = URL.createObjectURL(file);
            } else if (type === 'main') {
                this.files.main = file;
            }
        },

        submitUpload() {
            // Validation
            if (!this.files.main) {
                this.setStatus('Protocol Failure: Primary payload missing.', 'error');
                return;
            }
            if (['cad','solidworks'].includes(this.form.type) && !this.files.preview) {
                this.setStatus('Protocol Failure: Visual preview required for technical assets.', 'error');
                return;
            }

            this.uploading = true;
            this.setStatus('Uplink active. Synchronizing with vault...', 'loading');

            const formData = new FormData();
            formData.append('csrf_token', '<?php echo csrf_token(); ?>');
            Object.keys(this.form).forEach(key => formData.append(key, this.form[key]));
            if (this.files.main) formData.append('file', this.files.main);
            if (this.files.preview) formData.append('preview', this.files.preview);

            fetch('<?php echo app_base_url("api/library/upload"); ?>', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.setStatus('Transmission Complete. Asset secured.', 'success');
                    setTimeout(() => window.location.href = '<?php echo app_base_url("/library"); ?>', 1500);
                } else {
                    this.setStatus('Transmission Error: ' + data.message, 'error');
                    this.uploading = false;
                }
            })
            .catch(err => {
                console.error(err);
                this.setStatus('Signal Loss: Connection disrupted.', 'error');
                this.uploading = false;
            });
        },

        setStatus(msg, type) {
            this.status.message = msg;
            this.status.type = type;
        }
    }));
});
</script>
