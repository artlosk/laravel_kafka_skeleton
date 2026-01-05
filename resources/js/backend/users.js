// ========================================
// Users Management JavaScript
// ========================================

/**
 * Копирует текст из элемента в буфер обмена
 * @param {string} elementId - ID элемента с текстом для копирования
 */
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    if (!element) {
        console.warn('copyToClipboard: element not found');
        return;
    }

    try {
        element.select();
        element.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand('copy');
    } catch (e) {
        console.warn('copyToClipboard: error copying text', e);
        return;
    }

    // Show feedback
    const button = element.nextElementSibling;
    if (!button || !button.classList) {
        console.warn('copyToClipboard: button not found or invalid');
        return;
    }

    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i> Скопировано!';
    button.classList.add('btn-success');
    button.classList.remove('btn-outline-primary');

    setTimeout(() => {
        if (button && button.classList) {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-primary');
        }
    }, 2000);
}

window.copyToClipboard = copyToClipboard;

