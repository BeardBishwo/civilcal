/**
 * Floating Scientific Calculator Widget - Enhanced Version
 */

class FloatingCalculator {
  constructor() {
    this.display = "";
    this.history = [];
    this.memory = 0;
    this.showingResult = false;
    this.init();
  }

  init() {
    this.createWidget();
    this.attachEvents();
  }

  createWidget() {
    const widget = document.createElement("div");
    widget.innerHTML = `
            <!-- Floating Button -->
            <button class="floating-calc-btn" id="floatingCalcBtn" title="Scientific Calculator">
                <i class="bi bi-calculator"></i>
            </button>

            <!-- Calculator Panel -->
            <div class="floating-calc-panel" id="floatingCalcPanel">
                <div class="calc-header">
                    <h5><i class="bi bi-calculator me-2"></i>Scientific Calculator</h5>
                    <button class="calc-close" id="calcCloseBtn">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                
                <div class="calc-display" id="calcDisplay">0</div>
                <div class="calc-memory" id="calcMemory" style="display:none; padding:5px 20px; font-size:12px; color:#666;">M: <span id="memoryValue">0</span></div>
                
                <div class="calc-buttons">
                    <!-- Row 1: Memory & Clear -->
                    <button class="calc-btn function" data-value="MC" title="Memory Clear">MC</button>
                    <button class="calc-btn function" data-value="MR" title="Memory Recall">MR</button>
                    <button class="calc-btn function" data-value="M+" title="Memory Add">M+</button>
                    <button class="calc-btn function" data-value="M-" title="Memory Subtract">M-</button>
                    
                    <!-- Row 2: Advanced Functions -->
                    <button class="calc-btn function" data-value="sin">sin</button>
                    <button class="calc-btn function" data-value="cos">cos</button>
                    <button class="calc-btn function" data-value="tan">tan</button>
                    <button class="calc-btn function" data-value="sqrt">√</button>
                    
                    <!-- Row 3: More Functions -->
                    <button class="calc-btn function" data-value="log">log</button>
                    <button class="calc-btn function" data-value="ln">ln</button>
                    <button class="calc-btn function" data-value="exp">e^x</button>
                    <button class="calc-btn function" data-value="^">x^y</button>
                    
                    <!-- Row 4: Numbers & Operators -->
                    <button class="calc-btn" data-value="7">7</button>
                    <button class="calc-btn" data-value="8">8</button>
                    <button class="calc-btn" data-value="9">9</button>
                    <button class="calc-btn operator" data-value="/">÷</button>
                    
                    <!-- Row 5 -->
                    <button class="calc-btn" data-value="4">4</button>
                    <button class="calc-btn" data-value="5">5</button>
                    <button class="calc-btn" data-value="6">6</button>
                    <button class="calc-btn operator" data-value="*">×</button>
                    
                    <!-- Row 6 -->
                    <button class="calc-btn" data-value="1">1</button>
                    <button class="calc-btn" data-value="2">2</button>
                    <button class="calc-btn" data-value="3">3</button>
                    <button class="calc-btn operator" data-value="-">−</button>
                    
                    <!-- Row 7 -->
                    <button class="calc-btn" data-value="0">0</button>
                    <button class="calc-btn" data-value=".">.</button>
                    <button class="calc-btn clear" data-value="C">C</button>
                    <button class="calc-btn operator" data-value="+">+</button>
                    
                    <!-- Row 8: Parentheses & Constants -->
                    <button class="calc-btn function" data-value="(">(</button>
                    <button class="calc-btn function" data-value=")">)</button>
                    <button class="calc-btn function" data-value="pi">π</button>
                    <button class="calc-btn function" data-value="e">e</button>
                    
                    <!-- Row 9: Equals -->
                    <button class="calc-btn equals" data-value="=" style="grid-column: span 4;">=</button>
                </div>
                
                <div class="calc-history" id="calcHistory"></div>
            </div>
        `;

    document.body.appendChild(widget);
  }

  attachEvents() {
    const btn = document.getElementById("floatingCalcBtn");
    const panel = document.getElementById("floatingCalcPanel");
    const closeBtn = document.getElementById("calcCloseBtn");
    const buttons = document.querySelectorAll(".calc-btn");

    // Toggle panel
    btn.addEventListener("click", () => {
      panel.classList.toggle("active");
    });

    // Close panel
    closeBtn.addEventListener("click", () => {
      panel.classList.remove("active");
    });

    // Button clicks
    buttons.forEach((button) => {
      button.addEventListener("click", () => {
        const value = button.getAttribute("data-value");
        this.handleInput(value);
      });
    });

    // Keyboard support
    document.addEventListener("keydown", (e) => {
      if (!panel.classList.contains("active")) return;

      if (e.key >= "0" && e.key <= "9") this.handleInput(e.key);
      if (["+", "-", "*", "/"].includes(e.key)) this.handleInput(e.key);
      if (e.key === "Enter") this.handleInput("=");
      if (e.key === "Escape") this.handleInput("C");
      if (e.key === ".") this.handleInput(".");
      if (e.key === "(" || e.key === ")") this.handleInput(e.key);
    });
  }

  handleInput(value) {
    const display = document.getElementById("calcDisplay");

    // Memory operations
    if (value === "MC") {
      this.memory = 0;
      this.updateMemoryDisplay();
      return;
    }
    if (value === "MR") {
      this.display = this.memory.toString();
      display.textContent = this.display;
      return;
    }
    if (value === "M+") {
      const current = this.evaluateExpression(this.display);
      if (!isNaN(current)) {
        this.memory += current;
        this.updateMemoryDisplay();
      }
      return;
    }
    if (value === "M-") {
      const current = this.evaluateExpression(this.display);
      if (!isNaN(current)) {
        this.memory -= current;
        this.updateMemoryDisplay();
      }
      return;
    }

    // Clear
    if (value === "C") {
      this.display = "";
      display.textContent = "0";
      this.showingResult = false;
      return;
    }

    // If showing result, start fresh on number input
    if (this.showingResult && !isNaN(value)) {
      this.display = "";
      this.showingResult = false;
    }

    // Equals
    if (value === "=") {
      try {
        const result = this.evaluateExpression(this.display);
        this.addToHistory(this.display + " = " + result);
        this.display = result.toString();
        display.textContent = result;
        this.showingResult = true;
      } catch (e) {
        display.textContent = "Error";
        setTimeout(() => {
          this.display = "";
          display.textContent = "0";
        }, 1500);
      }
      return;
    }

    // Add to display
    this.display += value;
    display.textContent = this.display;
  }

  evaluateExpression(expr) {
    // Replace functions
    expr = expr.replace(/sin/g, "Math.sin");
    expr = expr.replace(/cos/g, "Math.cos");
    expr = expr.replace(/tan/g, "Math.tan");
    expr = expr.replace(/sqrt/g, "Math.sqrt");
    expr = expr.replace(/log/g, "Math.log10");
    expr = expr.replace(/ln/g, "Math.log");
    expr = expr.replace(/exp/g, "Math.exp");
    expr = expr.replace(/pi/g, "Math.PI");
    expr = expr.replace(/\^/g, "**");

    // Handle 'e' constant
    expr = expr.replace(/(?<!\w)e(?!\w)/g, "Math.E");

    return eval(expr);
  }

  updateMemoryDisplay() {
    const memDiv = document.getElementById("calcMemory");
    const memValue = document.getElementById("memoryValue");

    if (this.memory !== 0) {
      memDiv.style.display = "block";
      memValue.textContent = this.memory.toFixed(4);
    } else {
      memDiv.style.display = "none";
    }
  }

  addToHistory(entry) {
    this.history.unshift(entry);
    if (this.history.length > 10) this.history.pop();

    const historyDiv = document.getElementById("calcHistory");
    historyDiv.innerHTML = this.history
      .map((item) => `<div class="calc-history-item">${item}</div>`)
      .join("");
  }
}

// Initialize on page load
document.addEventListener("DOMContentLoaded", () => {
  new FloatingCalculator();
});
