function toggleDarkMode() {
    if (!document.body) {
        return;
    }

    const savedDarkMode = localStorage.getItem('darkMode');
    const isDarkMode = savedDarkMode === 'true';
    const newDarkMode = !isDarkMode;

    if (newDarkMode) {
        document.documentElement?.classList.add('dark-mode');
        document.body?.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'true');
    } else {
        document.documentElement?.classList.remove('dark-mode');
        document.body?.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'false');
    }

    const wrapper = document.querySelector('.wrapper');
    if (wrapper) {
        wrapper.classList.toggle('dark-mode', newDarkMode);
    }

    updateDarkModeButtonState();
}

window.toggleDarkMode = toggleDarkMode;
export {toggleDarkMode};

function initDarkMode() {
    const savedDarkMode = localStorage.getItem('darkMode');
    if (savedDarkMode === 'true') {
        document.documentElement?.classList.add('dark-mode');
        document.body?.classList.add('dark-mode');
        const wrapper = document.querySelector('.wrapper');
        if (wrapper) {
            wrapper.classList.add('dark-mode');
        }
    }
}

function updateDarkModeButtonState() {
    const button = document.getElementById('dark-mode-toggle-btn');
    if (!button) {
        return;
    }

    const savedDarkMode = localStorage.getItem('darkMode');
    const isDarkMode = savedDarkMode === 'true';

    if (isDarkMode) {
        button.innerHTML = '<i class="fas fa-sun"></i> <span class="d-none d-md-inline">Светлая тема</span>';
    } else {
        button.innerHTML = '<i class="fas fa-moon"></i> <span class="d-none d-md-inline">Темная тема</span>';
    }
}

window.updateDarkModeButtonState = updateDarkModeButtonState;

if (document.body) {
    initDarkMode();
} else {
    document.addEventListener('DOMContentLoaded', initDarkMode);
}
