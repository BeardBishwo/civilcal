<?php
/**
 * Floating Scientific Calculator Widget (Casio-Style)
 */
?>
<div id="floating-calc-container" class="floating-calc-hidden">
    <!-- Floating Button -->
    <button id="floating-calc-btn" title="Scientific Calculator">
        <i class="bi bi-calculator"></i>
    </button>

    <!-- Calculator Panel -->
    <div id="floating-calc-panel">
        <div class="calc-header">
            <span>Scientific Calculator</span>
            <div class="calc-controls">
                <button id="calc-minimize"><i class="bi bi-dash"></i></button>
                <button id="calc-close"><i class="bi bi-x"></i></button>
            </div>
        </div>
        
        <div class="calc-display-area">
            <div id="calc-history-view" class="calc-history"></div>
            <input type="text" id="calc-display" readonly value="0">
        </div>

        <div class="calc-keypad">
            <!-- Functions -->
            <div class="keypad-row">
                <button class="btn-func" data-val="sin(">sin</button>
                <button class="btn-func" data-val="cos(">cos</button>
                <button class="btn-func" data-val="tan(">tan</button>
                <button class="btn-func" data-val="log(">log</button>
            </div>
            <div class="keypad-row">
                <button class="btn-func" data-val="ln(">ln</button>
                <button class="btn-func" data-val="exp(">exp</button>
                <button class="btn-func" data-val="sqrt(">√</button>
                <button class="btn-func" data-val="pow(">^</button>
            </div>
            <div class="keypad-row">
                <button class="btn-func" data-val="pi">π</button>
                <button class="btn-func" data-val="e">e</button>
                <button class="btn-func" data-val="(">(</button>
                <button class="btn-func" data-val=")">)</button>
            </div>

            <!-- Numbers & Basic Operators -->
            <div class="keypad-row">
                <button class="btn-num">7</button>
                <button class="btn-num">8</button>
                <button class="btn-num">9</button>
                <button class="btn-op" data-val="/">÷</button>
            </div>
            <div class="keypad-row">
                <button class="btn-num">4</button>
                <button class="btn-num">5</button>
                <button class="btn-num">6</button>
                <button class="btn-op" data-val="*">×</button>
            </div>
            <div class="keypad-row">
                <button class="btn-num">1</button>
                <button class="btn-num">2</button>
                <button class="btn-num">3</button>
                <button class="btn-op" data-val="-">-</button>
            </div>
            <div class="keypad-row">
                <button class="btn-num">0</button>
                <button class="btn-num">.</button>
                <button id="calc-clear" class="btn-clear">AC</button>
                <button class="btn-op" data-val="+">+</button>
            </div>
            <div class="keypad-row">
                <button id="calc-backspace" class="btn-back"><i class="bi bi-backspace"></i></button>
                <button id="calc-equal" class="btn-equal">=</button>
            </div>
        </div>
    </div>
</div>

<style>
#floating-calc-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

#floating-calc-btn {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    cursor: pointer;
    font-size: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s;
}

#floating-calc-btn:hover {
    transform: scale(1.1);
}

#floating-calc-panel {
    position: absolute;
    bottom: 70px;
    right: 0;
    width: 300px;
    background: #2d3436;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    display: none;
    flex-direction: column;
}

#floating-calc-container.active #floating-calc-panel {
    display: flex;
}

.calc-header {
    background: #1e272e;
    color: #dfe6e9;
    padding: 10px 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 14px;
    font-weight: bold;
}

.calc-controls button {
    background: none;
    border: none;
    color: #dfe6e9;
    cursor: pointer;
    padding: 0 5px;
}

.calc-display-area {
    background: #000;
    padding: 15px;
    text-align: right;
}

.calc-history {
    color: #aaa;
    font-size: 12px;
    height: 20px;
    overflow: hidden;
    margin-bottom: 5px;
}

#calc-display {
    width: 100%;
    background: none;
    border: none;
    color: #00d2ff;
    font-size: 28px;
    text-align: right;
    outline: none;
}

.calc-keypad {
    padding: 10px;
    gap: 5px;
    display: flex;
    flex-direction: column;
}

.keypad-row {
    display: flex;
    gap: 5px;
}

.calc-keypad button {
    flex: 1;
    height: 40px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.2s;
}

.btn-num { background: #485460; color: white; }
.btn-num:hover { background: #596275; }

.btn-func { background: #3c6382; color: #dfe6e9; font-size: 12px; }
.btn-func:hover { background: #60a3bc; }

.btn-op { background: #ffa801; color: #1e272e; }
.btn-op:hover { background: #ffc048; }

.btn-clear { background: #ef5350; color: white; }
.btn-back { background: #57606f; color: white; }
.btn-equal { background: #05c46b; color: white; flex: 2 !important; }

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('floating-calc-container');
    const btn = document.getElementById('floating-calc-btn');
    const display = document.getElementById('calc-display');
    const historyView = document.getElementById('calc-history-view');
    const clearBtn = document.getElementById('calc-clear');
    const equalBtn = document.getElementById('calc-equal');
    const backspaceBtn = document.getElementById('calc-backspace');
    const minimizeBtn = document.getElementById('calc-minimize');
    const closeBtn = document.getElementById('calc-close');

    let currentExpression = '';
    let lastResult = null;

    btn.addEventListener('click', () => {
        container.classList.toggle('active');
    });

    minimizeBtn.addEventListener('click', () => {
        container.classList.remove('active');
    });

    closeBtn.addEventListener('click', () => {
        container.classList.remove('active');
    });

    document.querySelectorAll('.btn-num, .btn-op, .btn-func').forEach(button => {
        button.addEventListener('click', () => {
            const val = button.getAttribute('data-val') || button.innerText;
            
            if (display.value === '0' || display.value === 'Error') {
                display.value = val;
            } else {
                display.value += val;
            }
            currentExpression = display.value;
        });
    });

    clearBtn.addEventListener('click', () => {
        display.value = '0';
        historyView.innerText = '';
        currentExpression = '';
    });

    backspaceBtn.addEventListener('click', () => {
        if (display.value.length > 1) {
            display.value = display.value.slice(0, -1);
        } else {
            display.value = '0';
        }
        currentExpression = display.value;
    });

    equalBtn.addEventListener('click', calculateResult);

    async function calculateResult() {
        if (!currentExpression) return;
        
        historyView.innerText = currentExpression + ' =';
        
        try {
            const response = await fetch('<?= app_base_url("/calculator/api/calculate") ?>', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': window.appConfig ? window.appConfig.csrfToken : ''
                },
                body: new URLSearchParams({ 
                    expression: currentExpression,
                    csrf_token: window.appConfig ? window.appConfig.csrfToken : ''
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                display.value = data.result;
                currentExpression = data.result.toString();
            } else {
                console.error('Calculation Error:', data.result);
                display.value = 'Error';
            }
        } catch (error) {
            console.error('AJAX Error:', error);
            display.value = 'Error';
        }
    }
});
</script>
