// ========================================
// Fix для date input - открытие календаря при клике в любом месте поля
// ========================================

/**
 * Инициализирует обработку date input полей
 * Делает весь input кликабельным для открытия календаря
 */
export function initDateInputs() {
    document.addEventListener('DOMContentLoaded', function () {
        const dateInputs = document.querySelectorAll('input[type="date"]');

        dateInputs.forEach(function (input) {
            input.addEventListener('click', function (e) {
                if (!e.target.matches('::-webkit-calendar-picker-indicator')) {
                    if (input.showPicker && typeof input.showPicker === 'function') {
                        try {
                            input.showPicker();
                        } catch (err) {
                            input.focus();
                            const indicator = input.shadowRoot?.querySelector('::-webkit-calendar-picker-indicator');
                            if (indicator) {
                                indicator.click();
                            }
                        }
                    } else {
                        input.focus();
                    }
                }
            });

            input.style.cursor = 'pointer';

            input.style.pointerEvents = 'auto';
        });
    });
}

