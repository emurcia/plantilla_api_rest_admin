import $ from 'jquery';
import iMask from 'imask';

jQuery(function($) {
    // Inicializando vafriables
    const admin = document.getElementById('js-admin');
    const uri = $('#' + admin.dataset.uniqid + '_uri');
    // Inicializando mascaras
    iMask( uri[0], {
        mask: function (value) {
            if(/^[a-z0-9_/{}\.-]+$/.test(value))
                return true;
            
            return false;
        },
        prepare: function (str) {
            return str.toLowerCase();
        }
    });
});