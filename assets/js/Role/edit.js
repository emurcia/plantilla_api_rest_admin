import iMask from 'imask';

jQuery(document).ready(function($) {
    // Inicializando vafriables
    const admin = document.getElementById('js-admin');
    const nombre = $('#' + admin.dataset.uniqid + '_name');

    // Inicializando mascaras
    let nombreMask = iMask( nombre[0], {
        mask: 'ROLE_RN',
        lazy: false,
        blocks: {
            RN: { mask: /[a-zA-Z_]+/ }
        },
        prepare: function (str) {
            return str.toUpperCase();
        }
    });
});