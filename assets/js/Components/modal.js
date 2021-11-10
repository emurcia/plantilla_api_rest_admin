import { isBlank } from '../functions';

const appendModalHTML = () => {
    if( $('body #modal').length == 0 ) {
        $('body').append(
            '<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">'+
                '<div class="modal-dialog">'+
                    '<div class="modal-content">'+
                        '<div class="modal-header">'+
                        '</div>'+
                        '<div class="modal-body">'+
                        '</div>'+
                        '<div class="modal-footer">'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>'
        );
    }
}

/*
 * modal
 *      Funcion que facilita la creación y llamada de los modals
 *
 * Parametros:
 *      options:     Objeto JSON con los parámetros para la creación del modal
 *      autoExecute: Booleano que permite definir si se mostrará el modal al crearse, Default: FALSE
 *
 * Objecto JSON
 *      header:    String HTML | JSON Object, define el encabezado del modal.
 *          title  String HTML, define el titulo del modal.
 *          xBtn   Boolean (opcional), indica se se creará el botón de (X) para cerrar el modal. Default: TRUE.
 *      body:      String HTML, define el cuperpo del modal.
 *      footer:    String HTML | JSON Object | NULL, define el pie del modal.
 *          xBtn:  Boolean, define si se ha de crear el botón de cerrar en el footer, Default TRUE.
 *          xName: String HTML, define el nombre que se le dará al botón cerrar. Default 'Cerrar'.
 *      width:     JSON Object | NULL, define el ancho actual y/o máximo del modal.
 *          val:   Integer que contiene el valor del ancho definido para el modal en porcentaje.
 *          max:   Integer que contiene el valor del ancho máximo que puede tomar el modal en pixeles.
 *      backdrop:  Boolean, define si al hacer clic fuera del modal, este se cierra. Default TRUE.
 *      keyboard:  Boolean, define si se cierra el modal al presionar la tecla ESC. Default TRUE
 *
 * Ejemplo:
 *      var autoExecute = true;
 *      var options = {
 *          header: {
 *              title: "Título del modal",
 *              xBtn:  true
 *          },
 *          body: "<p>Body del modal.</p>",
 *          footer: {
 *              xBtn: true,
 *              xName: "Aceptar"
 *          },
 *          width: {
 *              val: 80,
 *              max: 700
 *          },
 *          backdrop: true,
 *          keyboard: true
 *      };
 *      modal( options, autoExecute );
 * Eventos:
 *     // Inicializar selects, llamadas a ajax despues de
 *     // que el modal sea mostrado, sustituye al after
 *     emodal.on('shown.bs.modal', function (event) {
 *         // codigo...
 *
 *         // Quitar evento al modal actual
 *         // para evitar duplicidad en la llamada
 *         $(this).off('shown.bs.modal');
 *     });
 */
export default ( options, autoExecute = true ) => {
    appendModalHTML();

    const modal    = $('body #modal');
    const moptions = {
        backdrop: ( isBlank( options.backdrop ) == false ? options.backdrop : true ),
        keyboard: ( isBlank( options.keyboard ) == false ? options.keyboard : true ),
        show: autoExecute

    };

    // validando el contenido del encabezado
    if( isBlank( options.header ) ) {
        console.error('Error al definir el modal, la propiedad header no ha sido proporcionada');
    } else {
        let header = modal.find('.modal-header');

        if( typeof options.header === 'string' ) {
            header.empty();
            header.append( options.header );
        } else {
            // validando el titulo del encabezado del modal
            if( isBlank( options.header.title ) ) {
                console.error('Error al definir el modal, la propiedad title del header no ha sido proporcionada');
            } else {
                let title = modal.find('.modal-title');

                // si no existe el title se agrega al header
                if( title.length === 0 ) {
                    header.append('<h4 class="modal-title" id="modalLabel">' + options.header.title + '</h4>');
                } else {
                    title.empty();
                    title.append( options.header.title );
                }

                const appendxBtn = isBlank( options.header.xBtn ) === false ? true : options.header.xBtn;
                if( header.find('button.close').length > 0 ) {
                    header.find('button.close').remove();
                }

                if( appendxBtn ) {
                    header.prepend(
                        '<button type="button" class="close" data-dismiss="modal" aria-label="Close">'+
                            '<span aria-hidden="true">&times;</span>'+
                        '</button>'
                    );
                }
            }
        }
    }

    // validando el contenido del body
    if( isBlank( options.body ) ) {
        console.error('Error al definir el modal, la propiedad body no ha sido proporcionada');
    } else {
        let body = modal.find('.modal-body');

        body.empty();
        body.append( options.body );
    }

    // validando el contenido del footer
    let footer = modal.find('.modal-footer');
    if( isBlank( options.footer ) ) {
        footer.append('<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>');
    } else {
        if( typeof options.footer === 'string' ) {
            footer.empty();
            footer.append( options.footer );
        } else {
            if( isBlank( options.footer.xBtn ) || isBlank( options.footer.xName ) ) {
                if( isBlank( options.footer.xBtn ) ) {
                    console.error('Error al definir el modal, la propiedad xBtn del footer no ha sido proporcionada');
                }

                if( isBlank( options.footer.xName ) ) {
                    console.error('Error al definir el modal, la propiedad xName del footer no ha sido proporcionada');
                }
            } else {
                footer.empty();
                footer.append('<button type="button" class="btn btn-secondary" data-dismiss="modal">' + options.footer.xName + '</button>');
            }
        }
    }

    if( isBlank( options.width ) === false ) {
        if( isBlank( options.width.val ) || isBlank( options.width.max ) ) {
            if( isBlank( options.width.val ) ) {
                console.error('Error al definir el modal, la propiedad val del width no ha sido proporcionada');
            }

            if( isBlank( options.width.max ) ) {
                console.error('Error al definir el modal, la propiedad max del width no ha sido proporcionada');
            }
        } else {
            let dialog = modal.find('.modal-dialog');
            dialog.css({ 'width': options.width.val+'%', 'max-width': options.width.max+'px' });
        }
    }

    if( autoExecute ) {
        modal.modal( moptions );
    }

    return modal;
}