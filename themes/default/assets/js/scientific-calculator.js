/**
 * Scientific Calculator - Core Frontend Logic
 */

let currentExpression = "";
let mainDisplay = document.getElementById("calc-main-display");
let exprDisplay = document.getElementById("calc-expr");
let historyList = document.getElementById("historyList");
let memory = parseFloat(localStorage.getItem("calc_memory")) || 0;
let history = JSON.parse(localStorage.getItem("calc_history")) || [];

function updateDisplay() {
  mainDisplay.textContent = currentExpression || "0";
  if (currentExpression.length > 15) {
    mainDisplay.style.fontSize = "1.8rem";
  } else if (currentExpression.length > 10) {
    mainDisplay.style.fontSize = "2.2rem";
  } else {
    mainDisplay.style.fontSize = "3rem";
  }
}

function appendNum(num) {
  currentExpression += num;
  updateDisplay();
}

function appendOp(op) {
  if (op === "pow") {
    currentExpression += "^";
  } else {
    currentExpression += op;
  }
  updateDisplay();
}

function appendFunc(func) {
  currentExpression += func + "(";
  updateDisplay();
}

function appendIcon(icon) {
  currentExpression += icon;
  updateDisplay();
}

function deleteLast() {
  currentExpression = currentExpression.slice(0, -1);
  updateDisplay();
}

function clearAll() {
  currentExpression = "";
  exprDisplay.textContent = "";
  updateDisplay();
}

// Memory Functions
function memClear() {
  memory = 0;
  localStorage.setItem("calc_memory", 0);
  showNotice("Memory Cleared");
}

function memRecall() {
  currentExpression += memory.toString();
  updateDisplay();
}

function memAdd() {
  calculateSilent((res) => {
    memory += parseFloat(res);
    localStorage.setItem("calc_memory", memory);
    showNotice("Added to Memory");
  });
}

function memSub() {
  calculateSilent((res) => {
    memory -= parseFloat(res);
    localStorage.setItem("calc_memory", memory);
    showNotice("Subtracted from Memory");
  });
}

async function performCalculate() {
  if (!currentExpression) return;

  exprDisplay.textContent = currentExpression + " =";

  try {
    const response = await fetch(
      `${window.appConfig.baseUrl}/calculator/api/calculate`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          "X-CSRF-Token": window.appConfig.csrfToken,
        },
        body: new URLSearchParams({
          expression: currentExpression,
          csrf_token: window.appConfig.csrfToken,
        }),
      }
    );

    const data = await response.json();

    if (data.success) {
      const result = data.result;
      addToHistory(currentExpression, result);
      currentExpression = result.toString();
      updateDisplay();
    } else {
      mainDisplay.textContent = "Error";
      console.error("Calc Error:", data.result);
    }
  } catch (error) {
    mainDisplay.textContent = "Error";
    console.error("AJAX Error:", error);
  }
}

async function calculateSilent(callback) {
  if (!currentExpression) return;
  try {
    const response = await fetch(
      `${window.appConfig.baseUrl}/calculator/api/calculate`,
      {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
          "X-CSRF-Token": window.appConfig.csrfToken,
        },
        body: new URLSearchParams({ expression: currentExpression }),
      }
    );
    const data = await response.json();
    if (data.success) callback(data.result);
  } catch (e) {}
}

// History Management
function addToHistory(expr, res) {
  const item = { expr, res, time: new Date().toLocaleTimeString() };
  history.unshift(item);
  if (history.length > 50) history.pop();
  localStorage.setItem("calc_history", JSON.stringify(history));
  renderHistory();
}

function renderHistory() {
  if (history.length === 0) {
    historyList.innerHTML =
      '<div class="text-center mt-5 text-muted small">No history yet</div>';
    return;
  }

  historyList.innerHTML = history
    .map(
      (item, index) => `
        <div class="history-item" onclick="loadHistoryItem(${index})">
            <div class="history-expr">${item.expr}</div>
            <div class="history-res">${item.res}</div>
            <div class="text-end" style="font-size: 0.6rem; color: #475569;">${item.time}</div>
        </div>
    `
    )
    .join("");
}

function loadHistoryItem(index) {
  currentExpression = history[index].res.toString();
  exprDisplay.textContent = history[index].expr + " =";
  updateDisplay();
}

function clearHistory() {
  history = [];
  localStorage.removeItem("calc_history");
  renderHistory();
}

function showNotice(msg) {
  // Simple visual feedback could be added here
  console.log(msg);
}

// Keyboard Support
document.addEventListener("keydown", (e) => {
  if (e.key >= "0" && e.key <= "9") appendNum(e.key);
  if (e.key === ".") appendNum(".");
  if (["+", "-", "*", "/"].includes(e.key)) appendOp(e.key);
  if (e.key === "Enter") performCalculate();
  if (e.key === "Escape") clearAll();
  if (e.key === "Backspace") deleteLast();
});

// Initial render
renderHistory();
updateDisplay();
