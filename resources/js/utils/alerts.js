/**
 * Professional Alert System
 * Toast notifications + Confirmation dialogs
 */

class AlertSystem {
  constructor() {
    this.toastContainer = null;
    this.toasts = [];
    this.initToastContainer();
  }

  initToastContainer() {
    if (!this.toastContainer) {
      this.toastContainer = document.createElement('div');
      this.toastContainer.className = 'toast-container';
      document.body.appendChild(this.toastContainer);
    }
  }

  // Toast Notifications
  toast(options) {
    const {
      type = 'info',
      title = '',
      message = '',
      duration = 4000,
      showProgress = true,
    } = options;

    const icons = {
      success: 'bi-check-circle-fill',
      error: 'bi-x-circle-fill',
      warning: 'bi-exclamation-triangle-fill',
      info: 'bi-info-circle-fill',
    };

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
      <div class="toast-icon">
        <i class="bi ${icons[type]}"></i>
      </div>
      <div class="toast-content">
        ${title ? `<div class="toast-title">${title}</div>` : ''}
        ${message ? `<div class="toast-message">${message}</div>` : ''}
      </div>
      <button class="toast-close" aria-label="Close">
        <i class="bi bi-x"></i>
      </button>
      ${showProgress ? '<div class="toast-progress"></div>' : ''}
    `;

    this.toastContainer.appendChild(toast);
    this.toasts.push(toast);

    const closeBtn = toast.querySelector('.toast-close');
    closeBtn.addEventListener('click', () => {
      this.removeToast(toast);
    });

    if (duration > 0) {
      setTimeout(() => {
        this.removeToast(toast);
      }, duration);
    }

    return toast;
  }

  removeToast(toast) {
    if (!toast || !toast.parentElement) return;
    toast.classList.add('exit');
    setTimeout(() => {
      if (toast.parentElement) {
        this.toastContainer.removeChild(toast);
        const index = this.toasts.indexOf(toast);
        if (index > -1) {
          this.toasts.splice(index, 1);
        }
      }
    }, 300);
  }

  success(title, message, duration) {
    return this.toast({ type: 'success', title, message, duration });
  }

  error(title, message, duration) {
    return this.toast({ type: 'error', title, message, duration });
  }

  warning(title, message, duration) {
    return this.toast({ type: 'warning', title, message, duration });
  }

  info(title, message, duration) {
    return this.toast({ type: 'info', title, message, duration });
  }

  // Confirmation Dialogs
  confirm(options) {
    const {
      title = 'Confirmation',
      message = 'Êtes-vous sûr ?',
      confirmText = 'OK',
      cancelText = 'Annuler',
      type = 'danger'
    } = options;

    return new Promise((resolve) => {
      const overlay = document.createElement('div');
      overlay.className = 'confirm-overlay';

      const icons = {
        danger: 'bi-exclamation-triangle-fill',
        warning: 'bi-exclamation-circle-fill',
        info: 'bi-info-circle-fill'
      };

      const dialog = document.createElement('div');
      dialog.className = `confirm-dialog type-${type}`;
      dialog.innerHTML = `
        <div class="confirm-icon">
          <i class="bi ${icons[type]}"></i>
        </div>
        <div class="confirm-content">
          <h3 class="confirm-title">${title}</h3>
          <p class="confirm-message">${message}</p>
        </div>
        <div class="confirm-actions">
          <button class="confirm-btn confirm-btn-cancel" data-action="cancel">
            ${cancelText}
          </button>
          <button class="confirm-btn confirm-btn-confirm" data-action="confirm">
            ${confirmText}
          </button>
        </div>
      `;

      overlay.appendChild(dialog);
      document.body.appendChild(overlay);

      setTimeout(() => overlay.classList.add('show'), 10);

      const close = (result) => {
        overlay.classList.remove('show');
        overlay.classList.add('hide');
        setTimeout(() => {
          if (overlay.parentElement) {
            document.body.removeChild(overlay);
          }
          resolve(result);
        }, 200);
        document.removeEventListener('keydown', handleEscape);
      };

      dialog.querySelector('[data-action="confirm"]').addEventListener('click', () => close(true));
      dialog.querySelector('[data-action="cancel"]').addEventListener('click', () => close(false));
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) close(false);
      });

      const handleEscape = (e) => {
        if (e.key === 'Escape') close(false);
      };
      document.addEventListener('keydown', handleEscape);
    });
  }

  // Convenience confirm methods
  confirmDelete(itemName = 'cet élément') {
    return this.confirm({
      title: 'Supprimer ?',
      message: `Voulez-vous vraiment supprimer ${itemName} ? Cette action est irréversible.`,
      confirmText: 'Supprimer',
      cancelText: 'Annuler',
      type: 'danger'
    });
  }

  confirmAction(title, message) {
    return this.confirm({
      title,
      message,
      confirmText: 'Continuer',
      cancelText: 'Annuler',
      type: 'warning'
    });
  }

  // Alert Dialogs (like confirm but with only OK button)
  alert(options) {
    const {
      title = 'Information',
      message = '',
      confirmText = 'OK',
      type = 'info' // success, error, warning, info
    } = options;

    return new Promise((resolve) => {
      const overlay = document.createElement('div');
      overlay.className = 'confirm-overlay';

      const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
      };

      const dialog = document.createElement('div');
      dialog.className = `confirm-dialog type-${type}`;
      dialog.innerHTML = `
        <div class="confirm-icon">
          <i class="bi ${icons[type]}"></i>
        </div>
        <div class="confirm-content">
          <h3 class="confirm-title">${title}</h3>
          <p class="confirm-message">${message}</p>
        </div>
        <div class="confirm-actions">
          <button class="confirm-btn confirm-btn-confirm confirm-btn-single" data-action="confirm">
            ${confirmText}
          </button>
        </div>
      `;

      overlay.appendChild(dialog);
      document.body.appendChild(overlay);

      setTimeout(() => overlay.classList.add('show'), 10);

      const close = () => {
        overlay.classList.remove('show');
        overlay.classList.add('hide');
        setTimeout(() => {
          if (overlay.parentElement) {
            document.body.removeChild(overlay);
          }
          resolve(true);
        }, 200);
        document.removeEventListener('keydown', handleEscape);
      };

      dialog.querySelector('[data-action="confirm"]').addEventListener('click', close);
      overlay.addEventListener('click', (e) => {
        if (e.target === overlay) close();
      });

      const handleEscape = (e) => {
        if (e.key === 'Escape' || e.key === 'Enter') close();
      };
      document.addEventListener('keydown', handleEscape);
    });
  }

  // Convenience alert methods
  alertSuccess(message) {
    return this.alert({
      title: message,
      message: '',
      confirmText: 'OK',
      type: 'success'
    });
  }

  alertError(message) {
    return this.alert({
      title: message,
      message: '',
      confirmText: 'OK',
      type: 'error'
    });
  }

  alertWarning(message) {
    return this.alert({
      title: message,
      message: '',
      confirmText: 'OK',
      type: 'warning'
    });
  }

  alertInfo(message) {
    return this.alert({
      title: message,
      message: '',
      confirmText: 'OK',
      type: 'info'
    });
  }
}

// Create singleton
const alerts = new AlertSystem();

// Export for use in Vue components
export default alerts;

// Also make available globally (optional)
if (typeof window !== 'undefined') {
  window.alerts = alerts;
}
