import flatpickr from "flatpickr";
import '../../../node_modules/flatpickr/dist/flatpickr.min.css';
import { Spanish } from "flatpickr/dist/l10n/es.js"
import { isBlank } from '../functions';

export default (element, options, setMask) => {
    setMask = isBlank( setMask ) ? true : ( setMask === false ? false : true ) ;

    // creando opciones por defecto
    if( isBlank( options ) ) {
        options = {
            allowInput: true,
            dateFormat: 'd/m/Y',
            locale: Spanish
        };
    }

    // creando mascara por defecto
    let elMask = null;
    if( setMask ) {
        elMask = IMask( element, {
            mask: Date,
            pattern: 'd{/}`m{/}`Y',
            format: function (date) {
                var day = date.getDate();
                var month = date.getMonth() + 1;
                var year = date.getFullYear();

                if (day < 10) day = "0" + day;
                if (month < 10) month = "0" + month;

                return [day, month, year].join('/');
            },
            parse: function (str) {
                var yearMonthDay = str.split('/');
                var date = new Date(yearMonthDay[2], yearMonthDay[1] - 1, yearMonthDay[0])
                return date;
            }
        });

        if( isBlank(options.onValueUpdate) ) {
            options.onValueUpdate = function(selectedDates, dateStr, instance) {
                elMask.updateValue();
            }
        }
    }

    return { flatpickr: flatpickr(element, options), imask: elMask };
}