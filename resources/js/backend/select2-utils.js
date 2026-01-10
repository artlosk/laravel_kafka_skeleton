function isSelect2Loaded() {
    return typeof window.$ !== 'undefined' && 
           typeof window.$.fn !== 'undefined' && 
           typeof window.$.fn.select2 === 'function';
}

export function waitForSelect2(callback, maxAttempts = 50, interval = 100) {
    if (isSelect2Loaded()) {
        callback();
        return;
    }

    let attempts = 0;
    const checkInterval = setInterval(() => {
        attempts++;
        
        if (isSelect2Loaded()) {
            clearInterval(checkInterval);
            callback();
        } else if (attempts >= maxAttempts) {
            clearInterval(checkInterval);
            console.error('Select2 не загрузился в течение ожидаемого времени');
        }
    }, interval);
}

export function initSelect2(options = {}) {
    const defaultOptions = {
        placeholder: 'Выберите...',
        allowClear: true,
        ...options
    };

    waitForSelect2(() => {
        window.$('.select2').each(function() {
            if (!window.$(this).hasClass('select2-hidden-accessible')) {
                window.$(this).select2(defaultOptions);
            }
        });
    });
}

export function destroySelect2(selectors) {
    if (!isSelect2Loaded()) {
        return;
    }

    selectors.forEach(selector => {
        const $el = window.$(selector);
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
    });
}
