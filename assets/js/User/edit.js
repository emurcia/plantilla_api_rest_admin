import iMask from 'imask';
import initializeSelect2 from '../Components/select2';
// import datepicker from '../Components/datepicker';

jQuery(document).ready(function($) {
    // Inicializando vafriables
    const admin = document.getElementById('js-admin');
    const email = $('#' + admin.dataset.uniqid + '_email');

    // Ejemplo de uso de calendario
    // const fecha = $('#' + admin.dataset.uniqid + '_date');
    // const { flatpickr: pkfecha, imask: mkfecha } = datepicker(fecha[0]);

    // Inicializando mascaras
    let emailMask = iMask( email[0], {
        mask: function (value) {
            if(/^[a-z0-9_\.-]+$/.test(value))
                return true;
            if(/^[a-z0-9_\.-]+@$/.test(value))
                return true;
            if(/^[a-z0-9_\.-]+@[a-z0-9-]+$/.test(value))
                return true;
            if(/^[a-z0-9_\.-]+@[a-z0-9-]+\.$/.test(value))
                return true;
            if(/^[a-z0-9_\.-]+@[a-z0-9-]+\.[a-z]{1,4}$/.test(value))
                return true;
            if(/^[a-z0-9_\.-]+@[a-z0-9-]+\.[a-z]{1,4}\.$/.test(value))
                return true;
            if(/^[a-z0-9_\.-]+@[a-z0-9-]+\.[a-z]{1,4}\.[a-z]{1,4}$/.test(value))
                return true;
            return false;
        },
        prepare: function (str) {
            return str.toLowerCase();
        }
    });
});