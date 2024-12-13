function showAlert(message, type = 'success') {
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": true,
        "positionClass": "toastr-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    toastr[type](message);
}

async function confirmMessage(message, confirmText = 'Evet', cancelText = 'Hayır') {
    const result = await Swal.fire({
        text: message,
        icon: "warning",
        showCancelButton: true,
        buttonsStyling: false,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        customClass: {
            confirmButton: "btn btn-primary",
            cancelButton: "btn btn-danger"
        }
    });

    return result.isConfirmed;
}

async function deleteItemFunc(url, selected,table) {
    if (selected.length === 0) {
        showAlert('Lütfen silmek istediğiniz kayıtları seçin', 'warning');
        return;
    }
    const confirmed = await confirmMessage("Seçili kayıtları silmek istediğinize emin misiniz?");
    if (confirmed) {
        try {
            const response = await $.post(url, {
                items: selected.map(item => item.guid),
                csrf_token: $('input[id=csrf_token]').val()
            });

            if (response.status === 'success') {
                showAlert(response.message, response.status);
                table.ajax.reload();
            } else {
                showAlert(response.message, response.status);
            }
        } catch (error) {
            showAlert('Bir hata oluştu', 'error');
        }
    } else {
        showAlert('Silme işlemi iptal edildi', 'warning');
    }
}


function showLoading(show = true) {
    if (show) {
        if (!document.getElementById('kt_app_loading')) {
            const loadingHtml = `
                <div id="kt_app_loading" class="app-loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', loadingHtml);

            if (!document.getElementById('loading-styles')) {
                const styles = `
                    <style id="loading-styles">
                        .app-loading {
                            position: fixed;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            background: rgba(255, 255, 255, 0.7);
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            z-index: 9999;
                        }
                        .app-loading .spinner-border {
                            width: 3rem;
                            height: 3rem;
                        }
                    </style>
                `;
                document.head.insertAdjacentHTML('beforeend', styles);
            }
        }
    } else {
        const loadingElement = document.getElementById('kt_app_loading');
        if (loadingElement) {
            loadingElement.remove();
        }
    }
}

function formToJson($form) {
    let formData = {};
    $form.find('input, select, textarea').each(function () {
        let $input = $(this);
        let name = $input.attr('name');
        if (name) {
            formData[name] = $input.val();
        }
    });
    return formData;
}

function resetForm($form) {
    $form.find('input:not([type=hidden]), select, textarea').val('');
    $form.find('input[type=checkbox], input[type=radio]').prop('checked', false);
}

function fillForm(el, data, type = null) {
    const $el = $(el);

    if (!type) {
        type = $el.attr('type') || $el.prop('tagName').toLowerCase();
    }

    if (type === 'checkbox' || type === 'radio') {
        $el.prop('checked', !!data);
        return;
    }

    if (type === 'select') {
        $el.val(data).trigger('change');
        return;
    }

    if (['date', 'datetime', 'time'].includes(type) && data) {
        try {
            const format = type === 'time' ? 'HH:mm' :
                type === 'date' ? 'YYYY-MM-DD' :
                    'YYYY-MM-DD HH:mm:ss';
            data = moment(data).format(format);
        } catch (e) {
            console.warn('Date formatting failed:', e);
        }
    }

    if (type === 'number') {
        data = parseFloat(data) || '';
    }

    $el.val(data);

    $el.trigger('filled');
}

function fillFormBulk($form, data) {

    // {
    //     name: 'John',
    //     email: 'john@example.com',
    //     active: true,
    //     country: 'TR'
    // }

    for (let key in data) {
        const $el = $form.find(`[name="${key}"]`);
        if ($el.length) {
            fillForm($el, data[key]);
        }
    }
}

function formatDate(date, format = 'DD.MM.YYYY') {
    return moment(date).format(format);
}

function formatCurrency(amount, currency = 'TRY') {
    return new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

function getUrlParams() {
    let params = {};
    new URLSearchParams(window.location.search).forEach((value, key) => {
        params[key] = value;
    });
    return params;
}

function slugify(text) {
    return text
        .toString()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .toLowerCase()
        .trim()
        .replace(/\s+/g, '-')
        .replace(/[^\w-]+/g, '')
        .replace(/--+/g, '-');
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function formatPhoneNumber(number) {
    let cleaned = ('' + number).replace(/\D/g, '');
    let match = cleaned.match(/^(\d{3})(\d{3})(\d{4})$/);
    if (match) {
        return '(' + match[1] + ') ' + match[2] + '-' + match[3];
    }
    return number;
}

function truncateText(text, length = 100, suffix = '...') {
    if (text.length <= length) return text;
    return text.substring(0, length).trim() + suffix;
}

function safeJSONParse(str, fallback = {}) {
    try {
        return JSON.parse(str);
    } catch (e) {
        console.error('JSON parse error:', e);
        return fallback;
    }
}

function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function copyToClipboard(text) {
    return navigator.clipboard.writeText(text).then(() => {
        showAlert('Panoya kopyalandı', 'success');
    }).catch(err => {
        console.error('Kopyalama hatası:', err);
        showAlert('Kopyalama başarısız', 'error');
    });
}

const storage = {
    set: (key, value) => {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch (e) {
            console.error('LocalStorage set error:', e);
        }
    },
    get: (key, defaultValue = null) => {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (e) {
            console.error('LocalStorage get error:', e);
            return defaultValue;
        }
    },
    remove: (key) => {
        try {
            localStorage.removeItem(key);
        } catch (e) {
            console.error('LocalStorage remove error:', e);
        }
    }
};