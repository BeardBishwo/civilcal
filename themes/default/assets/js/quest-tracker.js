/**
 * Quest Tracker - Global Calculation Detection logic
 * This script intercepts AJAX/Fetch requests to detect successful calculations
 * and triggers rewards for the "Tool of the Day" quest.
 */
(function() {
    if (window.questTrackerInitialized) return;
    window.questTrackerInitialized = true;

    console.log('Quest Tracker Initialized');

    // Helper to send calculation record to backend
    async function recordCalculation(toolId) {
        if (!toolId) return;
        
        try {
            const response = await fetch(`${window.appConfig.baseUrl}/api/quest/record-calculation`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': window.appConfig.csrfToken
                },
                body: new URLSearchParams({
                    tool_id: toolId,
                    csrf_token: window.appConfig.csrfToken
                })
            });
            
            const data = await response.json();
            if (data.success && data.rewarded) {
                // Trigger a global notification or event if needed
                if (window.showGlobalNotification) {
                    window.showGlobalNotification('Quest Completed!', `You earned ${data.coins} coins for using the Tool of the Day: ${toolId}`, 'success');
                }
                
                // Refresh resource HUD if it exists
                if (window.refreshResourceHUD) {
                    window.refreshResourceHUD();
                } else {
                    // Fallback: reload resource HUD via event or simple refresh
                    const event = new CustomEvent('resourceUpdate');
                    window.dispatchEvent(event);
                }
            }
        } catch (error) {
            console.error('Quest Record Error:', error);
        }
    }

    // --- Patch Fetch ---
    const originalFetch = window.fetch;
    window.fetch = async function(...args) {
        const response = await originalFetch.apply(this, args);
        const url = args[0] instanceof Request ? args[0].url : args[0];
        
        // Clone response to read it without consuming it
        const clone = response.clone();
        
        try {
            const data = await clone.json();
            if (data && data.success) {
                let toolId = null;
                
                // Case 1: Engine-based calculator (has calculator field)
                if (data.calculator) {
                    toolId = data.calculator;
                } 
                // Case 2: Specialized calculator or Scientific
                else if (url.includes('/calculator/api/')) {
                    // Extract tool ID from URL
                    // Example: /calculator/api/calculate -> scientific-calculator
                    // Example: /calculator/api/bmi -> bmi-calculator
                    const parts = url.split('/');
                    const action = parts[parts.length - 1];
                    
                    if (action === 'calculate') {
                        toolId = 'scientific-calculator';
                    } else if (action === 'convert') {
                        toolId = 'unit-converter';
                    } else {
                        toolId = action + '-calculator';
                    }
                }
                
                if (toolId) {
                    recordCalculation(toolId);
                }
            }
        } catch (e) {
            // Not a JSON response or other error, ignore
        }
        
        return response;
    };

    // --- Patch XMLHttpRequest ---
    const originalOpen = XMLHttpRequest.prototype.open;
    const originalSend = XMLHttpRequest.prototype.send;

    XMLHttpRequest.prototype.open = function(method, url) {
        this._url = url;
        return originalOpen.apply(this, arguments);
    };

    XMLHttpRequest.prototype.send = function() {
        this.addEventListener('load', function() {
            if (this.status >= 200 && this.status < 300) {
                try {
                    const data = JSON.parse(this.responseText);
                    let url = this._url;
                    
                    if (data && data.success) {
                        let toolId = null;
                        
                        if (data.calculator) {
                            toolId = data.calculator;
                        } else if (url.includes('/calculator/api/')) {
                            const parts = url.split('/');
                            const action = parts[parts.length - 1];
                            
                            if (action === 'calculate') {
                                toolId = 'scientific-calculator';
                            } else if (action === 'convert') {
                                toolId = 'unit-converter';
                            } else {
                                toolId = action + '-calculator';
                            }
                        }
                        
                        if (toolId) {
                            recordCalculation(toolId);
                        }
                    }
                } catch (e) {}
            }
        });
        return originalSend.apply(this, arguments);
    };

})();
