/**
 * Global Notification & Modal System
 * Provides consistent UI for notifications and confirmations across the entire application
 */

// Global Notification System
window.showNotification = function (message, type = "success") {
  const notification = document.createElement("div");
  notification.className = `global-notification ${type}`;
  notification.innerHTML = `
        <i class="fas fa-${
          type === "success"
            ? "check-circle"
            : type === "error"
            ? "exclamation-circle"
            : "info-circle"
        }"></i>
        <span>${message}</span>
    `;
  document.body.appendChild(notification);

  setTimeout(() => notification.classList.add("show"), 10);
  setTimeout(() => {
    notification.classList.remove("show");
    setTimeout(() => notification.remove(), 300);
  }, 3000);
};

// Global Confirm Modal
window.showConfirmModal = function (title, message, onConfirm, options = {}) {
  const {
    confirmText = "Confirm",
    cancelText = "Cancel",
    confirmClass = "btn-confirm",
    icon = "fa-exclamation-triangle",
  } = options;

  const modal = document.createElement("div");
  modal.className = "global-modal-overlay";
  modal.innerHTML = `
        <div class="global-modal">
            <div class="global-modal-header">
                <i class="fas ${icon} global-modal-icon"></i>
                <h3>${title}</h3>
            </div>
            <div class="global-modal-body">
                <p>${message}</p>
            </div>
            <div class="global-modal-footer">
                <button class="btn-cancel">
                    <i class="fas fa-times"></i> ${cancelText}
                </button>
                <button class="${confirmClass}">
                    <i class="fas fa-check"></i> ${confirmText}
                </button>
            </div>
        </div>
    `;
  document.body.appendChild(modal);

  const confirmBtn = modal.querySelector(`.${confirmClass}`);
  const cancelBtn = modal.querySelector(".btn-cancel");

  confirmBtn.onclick = () => {
    modal.remove();
    if (onConfirm) onConfirm();
  };

  cancelBtn.onclick = () => modal.remove();

  modal.onclick = (e) => {
    if (e.target === modal) modal.remove();
  };

  // ESC key to close
  const escHandler = (e) => {
    if (e.key === "Escape") {
      modal.remove();
      document.removeEventListener("keydown", escHandler);
    }
  };
  document.addEventListener("keydown", escHandler);
};

// Global Alert Modal (Info only, no confirmation)
window.showAlert = function (title, message, type = "info") {
  const iconMap = {
    success: "fa-check-circle",
    error: "fa-exclamation-circle",
    warning: "fa-exclamation-triangle",
    info: "fa-info-circle",
  };

  const modal = document.createElement("div");
  modal.className = "global-modal-overlay";
  modal.innerHTML = `
        <div class="global-modal">
            <div class="global-modal-header ${type}">
                <i class="fas ${iconMap[type]} global-modal-icon"></i>
                <h3>${title}</h3>
            </div>
            <div class="global-modal-body">
                <p>${message}</p>
            </div>
            <div class="global-modal-footer">
                <button class="btn-primary">
                    <i class="fas fa-check"></i> OK
                </button>
            </div>
        </div>
    `;
  document.body.appendChild(modal);

  const okBtn = modal.querySelector(".btn-primary");
  okBtn.onclick = () => modal.remove();
  modal.onclick = (e) => {
    if (e.target === modal) modal.remove();
  };
};

// Global Prompt Modal
window.showPrompt = function (title, message, onConfirm, options = {}) {
  const {
    placeholder = "",
    defaultValue = "",
    inputType = "text",
    confirmText = "Submit",
    cancelText = "Cancel",
  } = options;

  const modal = document.createElement("div");
  modal.className = "global-modal-overlay";
  modal.innerHTML = `
        <div class="global-modal">
            <div class="global-modal-header">
                <i class="fas fa-keyboard global-modal-icon"></i>
                <h3>${title}</h3>
            </div>
            <div class="global-modal-body">
                <p>${message}</p>
                <input type="${inputType}" class="global-modal-input" placeholder="${placeholder}" value="${defaultValue}">
            </div>
            <div class="global-modal-footer">
                <button class="btn-cancel">
                    <i class="fas fa-times"></i> ${cancelText}
                </button>
                <button class="btn-confirm">
                    <i class="fas fa-check"></i> ${confirmText}
                </button>
            </div>
        </div>
    `;
  document.body.appendChild(modal);

  const input = modal.querySelector(".global-modal-input");
  const confirmBtn = modal.querySelector(".btn-confirm");
  const cancelBtn = modal.querySelector(".btn-cancel");

  input.focus();

  confirmBtn.onclick = () => {
    const value = input.value.trim();
    modal.remove();
    if (onConfirm) onConfirm(value);
  };

  cancelBtn.onclick = () => modal.remove();

  input.addEventListener("keypress", (e) => {
    if (e.key === "Enter") {
      confirmBtn.click();
    }
  });

  modal.onclick = (e) => {
    if (e.target === modal) modal.remove();
  };
};

// Loading Overlay
window.showLoading = function (message = "Loading...") {
  const existing = document.querySelector(".global-loading-overlay");
  if (existing) return;

  const loading = document.createElement("div");
  loading.className = "global-loading-overlay";
  loading.innerHTML = `
        <div class="global-loading-spinner">
            <div class="spinner"></div>
            <p>${message}</p>
        </div>
    `;
  document.body.appendChild(loading);
  document.body.style.overflow = "hidden";
};

window.hideLoading = function () {
  const loading = document.querySelector(".global-loading-overlay");
  if (loading) {
    loading.remove();
    document.body.style.overflow = "";
  }
};
