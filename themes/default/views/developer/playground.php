
<style>
    .playground-container {
        max-width: 1400px;
        margin: 2rem auto;
        padding: 0 2rem;
    }
    
    .playground-layout {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        height: calc(100vh - 200px);
    }
    
    .playground-panel {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    
    body.dark-theme .playground-panel {
        background: #1e293b;
        border-color: #334155;
    }
    
    .panel-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
    }
    
    body.dark-theme .panel-header {
        background: #0f172a;
        border-color: #334155;
    }
    
    .panel-content {
        flex: 1;
        padding: 1.5rem;
        overflow-y: auto;
    }
    
    .endpoint-selector {
        margin-bottom: 2rem;
    }
    
    .endpoint-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.875rem;
        background: white;
    }
    
    body.dark-theme .endpoint-select {
        background: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    
    body.dark-theme .form-label {
        color: #d1d5db;
    }
    
    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    body.dark-theme .form-input {
        background: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }
    
    .json-editor {
        width: 100%;
        height: 200px;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.875rem;
        padding: 1rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        resize: vertical;
        background: #f9fafb;
    }
    
    body.dark-theme .json-editor {
        background: #0f172a;
        border-color: #4b5563;
        color: #e2e8f0;
    }
    
    .send-button {
        width: 100%;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
    }
    
    .send-button:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }
    
    .send-button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
    
    .response-area {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .response-tabs {
        display: flex;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 1rem;
    }
    
    body.dark-theme .response-tabs {
        border-color: #374151;
    }
    
    .response-tab {
        padding: 0.75rem 1rem;
        background: none;
        border: none;
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        border-bottom: 2px solid transparent;
    }
    
    .response-tab.active {
        color: #3b82f6;
        border-bottom-color: #3b82f6;
    }
    
    body.dark-theme .response-tab {
        color: #9ca3af;
    }
    
    body.dark-theme .response-tab.active {
        color: #60a5fa;
        border-bottom-color: #60a5fa;
    }
    
    .response-content {
        flex: 1;
        overflow: hidden;
    }
    
    .response-body {
        height: 100%;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 0.875rem;
        padding: 1rem;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        overflow-y: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    body.dark-theme .response-body {
        background: #0f172a;
        border-color: #374151;
        color: #e2e8f0;
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .status-success {
        background: #dcfdf7;
        color: #065f46;
    }
    
    .status-error {
        background: #fef2f2;
        color: #991b1b;
    }
    
    body.dark-theme .status-success {
        background: #064e3b;
        color: #6ee7b7;
    }
    
    body.dark-theme .status-error {
        background: #7f1d1d;
        color: #fca5a5;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #f3f4f6;
        border-radius: 50%;
        border-top-color: #3b82f6;
        animation: spin 1s ease-in-out infinite;
        margin-right: 0.5rem;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .method-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-right: 0.5rem;
    }
    
    .method-post {
        background: #dcfdf7;
        color: #065f46;
    }
    
    .method-get {
        background: #dbeafe;
        color: #1e40af;
    }
    
    body.dark-theme .method-post {
        background: #064e3b;
        color: #6ee7b7;
    }
    
    body.dark-theme .method-get {
        background: #1e3a8a;
        color: #93c5fd;
    }
    
    @media (max-width: 1024px) {
        .playground-layout {
            grid-template-columns: 1fr;
            height: auto;
        }
        
        .playground-panel {
            height: 600px;
        }
    }
</style>

<div class="playground-container">
    <div style="text-align: center; margin-bottom: 3rem;">
        <h1 style="font-size: 2.5rem; font-weight: 700; color: #1f2937; margin: 0 0 1rem 0;">
            API Playground
        </h1>
        <p style="font-size: 1.125rem; color: #6b7280; margin: 0;">
            Test our engineering calculation APIs interactively
        </p>
    </div>
    
    <div class="playground-layout">
        <!-- Request Panel -->
        <div class="playground-panel">
            <div class="panel-header">
                <h3 style="margin: 0; color: #1f2937; font-size: 1.25rem; font-weight: 600;">
                    <i class="fas fa-paper-plane me-2"></i> Request
                </h3>
            </div>
            <div class="panel-content">
                <!-- Endpoint Selection -->
                <div class="endpoint-selector">
                    <label class="form-label">Select Endpoint</label>
                    <select class="endpoint-select" id="endpointSelect">
                        <option value="">Choose an endpoint to test...</option>
                        <?php foreach ($endpoints as $endpoint): ?>
                        <option value="<?php echo htmlspecialchars(json_encode($endpoint)); ?>">
                            [<?php echo $endpoint['method']; ?>] <?php echo $endpoint['endpoint']; ?> - <?php echo $endpoint['name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- API Key -->
                <div class="form-group">
                    <label class="form-label">API Key</label>
                    <input type="password" class="form-input" id="apiKey" placeholder="Enter your API key">
                    <small style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem; display: block;">
                        Get your API key from the <a href="/developers/getting-started" style="color: #3b82f6;">developer dashboard</a>
                    </small>
                </div>
                
                <!-- Request URL -->
                <div class="form-group">
                    <label class="form-label">Request URL</label>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <span class="method-badge method-post" id="methodBadge">POST</span>
                        <input type="text" class="form-input" id="requestUrl" placeholder="Select an endpoint above" readonly style="flex: 1;">
                    </div>
                </div>
                
                <!-- Request Body -->
                <div class="form-group">
                    <label class="form-label">Request Body (JSON)</label>
                    <textarea class="json-editor" id="requestBody" placeholder="Enter JSON request body...">
{
  "length": 10,
  "width": 5,
  "height": 0.2,
  "unit": "m"
}</textarea>
                </div>
                
                <!-- Send Button -->
                <button class="send-button" id="sendRequest">
                    <i class="fas fa-paper-plane me-2"></i> Send Request
                </button>
            </div>
        </div>
        
        <!-- Response Panel -->
        <div class="playground-panel">
            <div class="panel-header">
                <h3 style="margin: 0; color: #1f2937; font-size: 1.25rem; font-weight: 600;">
                    <i class="fas fa-code me-2"></i> Response
                </h3>
            </div>
            <div class="panel-content response-area">
                <!-- Response Tabs -->
                <div class="response-tabs">
                    <button class="response-tab active" data-tab="body">Response Body</button>
                    <button class="response-tab" data-tab="headers">Headers</button>
                    <button class="response-tab" data-tab="curl">cURL Command</button>
                </div>
                
                <!-- Response Content -->
                <div class="response-content">
                    <div class="response-body" id="responseBody">
                        <div style="text-align: center; color: #9ca3af; padding: 2rem;">
                            <i class="fas fa-play-circle" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                            <p>Select an endpoint and click "Send Request" to see the response</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Example Requests -->
    <div style="margin-top: 3rem; background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
        <h3 style="margin: 0 0 1.5rem 0; color: #1f2937; font-size: 1.5rem; font-weight: 600;">
            Example Requests
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 1.5rem;">
                <h4 style="margin: 0 0 1rem 0; color: #1f2937; font-weight: 600;">
                    Concrete Volume Calculation
                </h4>
                <div style="font-family: 'Monaco', 'Menlo', monospace; font-size: 0.875rem; background: #f9fafb; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
{
  "length": 10,
  "width": 5,
  "height": 0.2,
  "unit": "m"
}
                </div>
                <button class="btn-example" onclick="loadExample('concrete-volume')" style="background: #3b82f6; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; font-size: 0.875rem; cursor: pointer;">
                    Load Example
                </button>
            </div>
            
            <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 1.5rem;">
                <h4 style="margin: 0 0 1rem 0; color: #1f2937; font-weight: 600;">
                    Electrical Load Calculation
                </h4>
                <div style="font-family: 'Monaco', 'Menlo', monospace; font-size: 0.875rem; background: #f9fafb; padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
{
  "voltage": 240,
  "current": 20,
  "power_factor": 0.85,
  "phases": 3
}
                </div>
                <button class="btn-example" onclick="loadExample('electrical-load')" style="background: #3b82f6; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; font-size: 0.875rem; cursor: pointer;">
                    Load Example
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const endpointSelect = document.getElementById('endpointSelect');
    const requestUrl = document.getElementById('requestUrl');
    const methodBadge = document.getElementById('methodBadge');
    const requestBody = document.getElementById('requestBody');
    const sendButton = document.getElementById('sendRequest');
    const responseBody = document.getElementById('responseBody');
    const responseTabs = document.querySelectorAll('.response-tab');
    
    let currentResponse = null;
    let currentHeaders = null;
    let currentCurl = null;
    
    // Endpoint selection
    endpointSelect.addEventListener('change', function() {
        if (this.value) {
            const endpoint = JSON.parse(this.value);
            requestUrl.value = `https://api.engicalc.com${endpoint.endpoint}`;
            methodBadge.textContent = endpoint.method;
            methodBadge.className = `method-badge method-${endpoint.method.toLowerCase()}`;
            
            // Update request body based on endpoint
            if (endpoint.name.includes('Concrete')) {
                requestBody.value = JSON.stringify({
                    length: 10,
                    width: 5,
                    height: 0.2,
                    unit: "m"
                }, null, 2);
            } else if (endpoint.name.includes('Electrical')) {
                requestBody.value = JSON.stringify({
                    voltage: 240,
                    current: 20,
                    power_factor: 0.85,
                    phases: 3
                }, null, 2);
            }
        } else {
            requestUrl.value = '';
            methodBadge.textContent = 'POST';
            requestBody.value = '';
        }
    });
    
    // Send request
    sendButton.addEventListener('click', async function() {
        const apiKey = document.getElementById('apiKey').value;
        const url = requestUrl.value;
        const body = requestBody.value;
        
        if (!url) {
            showNotification('Please select an endpoint', 'error');
            return;
        }
        
        if (!apiKey) {
            showNotification('Please enter your API key', 'error');
            return;
        }
        
        // Show loading
        sendButton.disabled = true;
        sendButton.innerHTML = '<span class="loading-spinner"></span> Sending...';
        
        try {
            // Parse request body
            let requestData = null;
            if (body.trim()) {
                try {
                    requestData = JSON.parse(body);
                } catch (e) {
                    throw new Error('Invalid JSON in request body');
                }
            }
            
            // Make request (this is a demo, so we'll simulate the response)
            await new Promise(resolve => setTimeout(resolve, 1000)); // Simulate network delay
            
            // Simulate response based on endpoint
            const mockResponse = generateMockResponse(url, requestData);
            currentResponse = mockResponse.body;
            currentHeaders = mockResponse.headers;
            currentCurl = generateCurlCommand(url, apiKey, requestData);
            
            // Show response
            showResponse('body');
            
        } catch (error) {
            currentResponse = {
                error: true,
                message: error.message,
                timestamp: new Date().toISOString()
            };
            showResponse('body');
        } finally {
            // Reset button
            sendButton.disabled = false;
            sendButton.innerHTML = '<i class="fas fa-paper-plane me-2"></i> Send Request';
        }
    });
    
    // Response tabs
    responseTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            responseTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            showResponse(this.dataset.tab);
        });
    });
    
    function showResponse(type) {
        let content = '';
        
        switch (type) {
            case 'body':
                if (currentResponse) {
                    const statusClass = currentResponse.error ? 'status-error' : 'status-success';
                    const statusText = currentResponse.error ? 'ERROR' : 'SUCCESS';
                    content = `<div class="status-badge ${statusClass}">${statusText}</div>\n`;
                    content += JSON.stringify(currentResponse, null, 2);
                } else {
                    content = 'No response yet';
                }
                break;
                
            case 'headers':
                if (currentHeaders) {
                    content = Object.entries(currentHeaders)
                        .map(([key, value]) => `${key}: ${value}`)
                        .join('\n');
                } else {
                    content = 'No headers yet';
                }
                break;
                
            case 'curl':
                content = currentCurl || 'No cURL command yet';
                break;
        }
        
        responseBody.textContent = content;
    }
    
    function generateMockResponse(url, requestData) {
        if (url.includes('concrete/volume')) {
            return {
                body: {
                    success: true,
                    result: {
                        volume: requestData ? (requestData.length * requestData.width * requestData.height) : 10.0,
                        unit: requestData?.unit === 'ft' ? 'ft³' : 'm³',
                        calculation_id: 'calc_' + Math.random().toString(36).substr(2, 9),
                        timestamp: new Date().toISOString()
                    }
                },
                headers: {
                    'Content-Type': 'application/json',
                    'X-RateLimit-Remaining': '99',
                    'X-Response-Time': '45ms'
                }
            };
        } else if (url.includes('electrical/load')) {
            return {
                body: {
                    success: true,
                    result: {
                        power: requestData ? (requestData.voltage * requestData.current * requestData.power_factor * Math.sqrt(requestData.phases || 1)) : 8160,
                        unit: 'W',
                        current: requestData?.current || 20,
                        voltage: requestData?.voltage || 240,
                        calculation_id: 'calc_' + Math.random().toString(36).substr(2, 9),
                        timestamp: new Date().toISOString()
                    }
                },
                headers: {
                    'Content-Type': 'application/json',
                    'X-RateLimit-Remaining': '98',
                    'X-Response-Time': '52ms'
                }
            };
        }
        
        return {
            body: {
                error: true,
                message: 'Endpoint not found',
                code: 404
            },
            headers: {
                'Content-Type': 'application/json'
            }
        };
    }
    
    function generateCurlCommand(url, apiKey, requestData) {
        let curl = `curl -X POST "${url}" \\\\\n`;
        curl += `  -H "Authorization: Bearer ${apiKey}" \\\\\n`;
        curl += `  -H "Content-Type: application/json"`;
        
        if (requestData) {
            curl += ` \\\\\n  -d '${JSON.stringify(requestData)}'`;
        }
        
        return curl;
    }
    
    // Global function for example buttons
    window.loadExample = function(type) {
        if (type === 'concrete-volume') {
            // Find and select the concrete volume endpoint
            const options = endpointSelect.options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].text.includes('Concrete Volume')) {
                    endpointSelect.selectedIndex = i;
                    endpointSelect.dispatchEvent(new Event('change'));
                    break;
                }
            }
        } else if (type === 'electrical-load') {
            // Find and select the electrical load endpoint
            const options = endpointSelect.options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].text.includes('Electrical Load')) {
                    endpointSelect.selectedIndex = i;
                    endpointSelect.dispatchEvent(new Event('change'));
                    break;
                }
            }
        }
    };
});
</script>

