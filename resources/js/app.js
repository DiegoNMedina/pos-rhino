import './bootstrap';

if (document.querySelector('[data-pos-page]')) {
    import('./pos/index');
}

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
