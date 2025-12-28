/**
 * Calculator Export & Share System
 * Handles PDF export and URL state sharing
 */

document.addEventListener('DOMContentLoaded', () => {
    injectActionButtons();
});

function injectActionButtons() {
    // Locate the calculator result section
    const resultSection = document.getElementById('result-section');
    if (!resultSection) return;

    // Create container for buttons
    const container = document.createElement('div');
    container.className = 'd-flex justify-content-end gap-2 mt-3 no-print';
    
    // Share Button
    const shareBtn = document.createElement('button');
    shareBtn.className = 'btn btn-outline-light btn-sm rounded-pill px-3';
    shareBtn.innerHTML = '<i class="bi bi-share me-2"></i>Share Result';
    shareBtn.onclick = shareResult;
    
    // PDF Button
    const pdfBtn = document.createElement('button');
    pdfBtn.className = 'btn btn-outline-light btn-sm rounded-pill px-3';
    pdfBtn.innerHTML = '<i class="bi bi-file-pdf me-2"></i>Save as PDF';
    pdfBtn.onclick = exportPDF;
    
    container.appendChild(shareBtn);
    container.appendChild(pdfBtn);
    
    // Append to result section or right after it
    resultSection.appendChild(container);
    
    // Check for URL params to pre-fill inputs
    checkUrlParams();
}

function shareResult() {
    // Collect all inputs
    const inputs = document.querySelectorAll('input, select');
    const params = new URLSearchParams();
    
    inputs.forEach(input => {
        if (input.id && input.value) {
            params.set(input.id, input.value);
        }
    });
    
    const url = `${window.location.origin}${window.location.pathname}?${params.toString()}`;
    
    navigator.clipboard.writeText(url).then(() => {
        alert('Link copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy: ', err);
        prompt("Copy this link:", url);
    });
}

function checkUrlParams() {
    const params = new URLSearchParams(window.location.search);
    let hasParams = false;
    
    params.forEach((value, key) => {
        const input = document.getElementById(key);
        if (input) {
            input.value = value;
            hasParams = true;
        }
    });
    
    // If params existed, trigger calculation automatically if possible
    if (hasParams) {
        const calcBtn = document.querySelector('button[onclick*="Calculate"], button[onclick*="convert"]');
        if (calcBtn) {
            setTimeout(() => calcBtn.click(), 500);
        }
    }
}

function exportPDF() {
    // Check if libraries are loaded
    if (typeof html2canvas === 'undefined' || typeof jspdf === 'undefined') {
        alert('PDF libraries loading... please wait a moment and try again.');
        loadPdfLibraries(() => exportPDF());
        return;
    }

    const element = document.querySelector('.glass-card') || document.body;
    const title = document.querySelector('h1, h2')?.textContent || 'Calculator Result';
    
    // Hide buttons for screenshot
    const buttons = document.querySelector('.no-print');
    if(buttons) buttons.style.display = 'none';

    html2canvas(element, {
        background: '#0a0b10', // Dark theme background
        scale: 2
    }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4');
        
        const imgWidth = 210;
        const pageHeight = 295;
        const imgHeight = canvas.height * imgWidth / canvas.width;
        
        doc.setFillColor(10, 11, 16); // Dark background
        doc.rect(0, 0, 210, 297, 'F');
        
        doc.addImage(imgData, 'PNG', 0, 10, imgWidth, imgHeight);
        doc.save(`${title.trim().replace(/\s+/g, '_')}_Result.pdf`);
        
        if(buttons) buttons.style.display = 'flex';
    });
}

function loadPdfLibraries(callback) {
    if (document.getElementById('html2canvas-script')) {
        return; // Already loading
    }
    
    const s1 = document.createElement('script');
    s1.id = 'html2canvas-script';
    s1.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
    
    const s2 = document.createElement('script');
    s2.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
    
    s1.onload = () => { document.head.appendChild(s2); };
    s2.onload = callback;
    
    document.head.appendChild(s1);
}
