import initializeSelect2 from '../Components/select2';
import iMask from 'imask';

jQuery(function($) {
    
    const admin = document.getElementById('js-admin');
    const codigo = $('#' + admin.dataset.uniqid + '_codigo');

    // Inicializando mascaras
    iMask( codigo[0], {
        mask: 'PRN',
        lazy: false,
        blocks: {
            RN: { mask: /[a-zA-Z_]+/ }
        },
        prepare: function (str) {
            return str.toUpperCase();
        }
    });
});