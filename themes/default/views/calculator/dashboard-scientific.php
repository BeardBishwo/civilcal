<?php
/**
 * Dashboard Scientific Calculator Partial
 * Clean, non-modal interface for immediate use on the landing page.
 */
?>

<div class="dashboard-calc-container">
    <div class="db-calc-display">
        <div id="db-calc-expr"></div>
        <div id="db-calc-display">0</div>
    </div>

    <div class="db-calc-grid">
        <!-- Shortcuts & Functions -->
        <button class="db-btn db-btn-ac" onclick="dashboardClear()">AC</button>
        <button class="db-btn db-btn-func" onclick="dashboardAppendFunc('sin')">sin</button>
        <button class="db-btn db-btn-func" onclick="dashboardAppendFunc('cos')">cos</button>
        <button class="db-btn db-btn-func" onclick="dashboardAppendFunc('sqrt')">√</button>
        <button class="db-btn db-btn-op" onclick="dashboardDeleteLast()"><i class="bi bi-backspace"></i></button>

        <!-- Numbers & Ops -->
        <button class="db-btn" onclick="dashboardAppendNum('7')">7</button>
        <button class="db-btn" onclick="dashboardAppendNum('8')">8</button>
        <button class="db-btn" onclick="dashboardAppendNum('9')">9</button>
        <button class="db-btn db-btn-func" onclick="dashboardAppendOp('(')">(</button>
        <button class="db-btn db-btn-op" onclick="dashboardAppendOp('/')">÷</button>

        <button class="db-btn" onclick="dashboardAppendNum('4')">4</button>
        <button class="db-btn" onclick="dashboardAppendNum('5')">5</button>
        <button class="db-btn" onclick="dashboardAppendNum('6')">6</button>
        <button class="db-btn db-btn-func" onclick="dashboardAppendOp(')')">)</button>
        <button class="db-btn db-btn-op" onclick="dashboardAppendOp('*')">×</button>

        <button class="db-btn" onclick="dashboardAppendNum('1')">1</button>
        <button class="db-btn" onclick="dashboardAppendNum('2')">2</button>
        <button class="db-btn" onclick="dashboardAppendNum('3')">3</button>
        <button class="db-btn db-btn-func" onclick="dashboardAppendOp('pow')">xʸ</button>
        <button class="db-btn db-btn-op" onclick="dashboardAppendOp('-')">-</button>

        <button class="db-btn" onclick="dashboardAppendNum('0')">0</button>
        <button class="db-btn" onclick="dashboardAppendNum('.')">.</button>
        <button class="db-btn db-btn-func" onclick="dashboardAppendIcon('π')">π</button>
        <button class="db-btn db-btn-op" onclick="dashboardAppendOp('+')">+</button>
        <button class="db-btn db-btn-eq" onclick="dashboardCalculate()">=</button>
    </div>
</div>

<script>
/**
 * Dashboard Calculator Logic
 * Lightweight JS to handle immediate calculations on home dashboard.
 */
let dashboardExpr = "";
const dbDisplay = document.getElementById('db-calc-display');
const dbExprDisplay = document.getElementById('db-calc-expr');

function dashboardAppendNum(num) {
    dashboardExpr += num;
    updateDashboardDisplay();
}

function dashboardAppendOp(op) {
    if (op === 'pow') dashboardExpr += '^';
    else dashboardExpr += op;
    updateDashboardDisplay();
}

function dashboardAppendFunc(func) {
    dashboardExpr += func + "(";
    updateDashboardDisplay();
}

function dashboardAppendIcon(icon) {
    if (icon === 'π') dashboardExpr += 'pi';
    else dashboardExpr += icon;
    updateDashboardDisplay();
}

function dashboardDeleteLast() {
    dashboardExpr = dashboardExpr.slice(0, -1);
    updateDashboardDisplay();
}

function dashboardClear() {
    dashboardExpr = "";
    updateDashboardDisplay();
}

function updateDashboardDisplay() {
    dbDisplay.textContent = dashboardExpr || "0";
    dbExprDisplay.textContent = ""; 
}

async function dashboardCalculate() {
    if (!dashboardExpr) return;
    
    try {
        const formData = new FormData();
        formData.append('expression', dashboardExpr);
        formData.append('csrf_token', '<?php echo csrf_token(); ?>');

        const response = await fetch('<?php echo app_base_url("/calculator/api/calculate"); ?>', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            dbExprDisplay.textContent = dashboardExpr + " =";
            dbDisplay.textContent = data.result;
            dashboardExpr = data.result.toString();
        } else {
            dbDisplay.textContent = "Error";
        }
    } catch (e) {
        dbDisplay.textContent = "Error";
    }
}
</script>
