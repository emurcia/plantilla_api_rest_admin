import '../../styles/Components/showDialogMsg.scss';

/*
 * showDialogMsg
 *      Funcion que facilita mostrar los Message Dialog
 *
 * Parametros:
 *      title:                 titulo de dialog
 *      msg:                   mensaje dentro del dialog
 *      dialogClass:           [dialog-warning | dialog-error | dialog-info | dialog-success]
 *      closeBtnName:          Permite definir un nombre para el botón cancelar.
 *      arrayBtns:             Array que contiene los botones junto con la lógica que ha de funcionar dentro de este.
 *      createDefaultBtnClose: True o False, bandera permite definir si se creará el boton cerrar por defecto.
 *      width:                 Permite cambiar el ancho por defecto de la ventana.
 *      modal:                 Permitir o no un comportamiento similar a un modal (Deshabilitar otros elementos de la pantalla). Default: true
 *      closeOnEscape:         True o False, indica si se puede cerrar el dialog presionando la tecla ESC. Default: false
 *      createXBtn:            True o FAlse, indica si se se agregará o no el boton (X) para cerrar el dialog por defecto. Default: true
 *
 * Ejemplo:
 *      var title        = 'Limite excedido';
 *      var dialogClass  = 'dialog-warning';
 *      var msg          = 'Cuerpo del Mensaje';
 *      var width        = 500;
 *      var modal        = true;
 *      var closeBtnName = '';
 *      var createDefaultBtnClose = null;
 *
 *      var arrayBtns = [
 *                          {
 *                              text: 'Aceptar', click: function() {
 *                                  window.location = "http://midominio.com";
 *                              }
 *                          },
 *                          {
 *                              text: 'Cerrar', click: function( event, ui) {
 *                                  jQuery( this ).dialog( "close" );
 *                              }
 *                          }
 *                      ];
 *      showDialogMsg(title, msg, dialogClass, closeBtnName, arrayBtns, createDefaultBtnClose, width, modal, closeOnEscape, createXBtn);
 */
export default (title, msg, dialogClass, closeBtnName, arrayBtns, createDefaultBtnClose, width, modal, closeOnEscape, createXBtn) => {
    if (jQuery('body #dialog-message').length > 0) {
        jQuery('body #dialog-message').remove();
    }

    if (jQuery('body #dialog-message').length === 0) {
        jQuery("body").append('<div id="dialog-message"></div>');
    }

    var element = jQuery('body #dialog-message');

    if (typeof dialogClass === "undefined" || dialogClass === null || dialogClass === '') {
        dialogClass = 'dialog-info'
    }

    if (typeof arrayBtns === "undefined" || arrayBtns === null || arrayBtns === '') {
        arrayBtns = [];
    }

    if (typeof closeBtnName === "undefined" || closeBtnName === null || closeBtnName === '') {
        closeBtnName = 'Cerrar';
    }

    if (typeof createDefaultBtnClose === "undefined" || createDefaultBtnClose === null || createDefaultBtnClose === '') {
        createDefaultBtnClose = true;
    }

    if (typeof width === "undefined" || width === null || width === '') {
        width = 300;
    }

    if (typeof modal === "undefined" || modal === null || modal === '') {
        modal = true;
    }

    if (typeof closeOnEscape === "undefined" || closeOnEscape === null || closeOnEscape === '') {
        closeOnEscape = false;
    }

    if (typeof createXBtn === "undefined" || createXBtn === null || createXBtn === '') {
        createXBtn = true;
    }

    if (createDefaultBtnClose === true || arrayBtns.length === 0) {
        arrayBtns.push({
            text: closeBtnName, click: function () {
                jQuery(this).dialog("close");
            }
        });
    }

    if (typeof title === "undefined" || title === null || title === '') {
        switch (dialogClass.replace('dialog-', '')) {
            case 'error':
                title = 'Ha ocurrido un Error!';
                break;
            case 'warning':
                title = 'Advertencia!';
                break;
            case 'success':
                title = 'Realizado correctamente!';
                break;
            default:
                title = 'Información';
                break;
        }
    }

    if (typeof msg === "undefined" || msg === null || msg === '') {
        switch (dialogClass.replace('dialog-', '')) {
            case 'error':
                msg = 'Se ha producido un error inesperado! Por favor, verifique los datos ingresados e intente nuevamente.';
                break;
            case 'warning':
                msg = 'Atenci&oacute;n, verifique la información proporcionada y que los datos esten completos. Esto podría generar un error.';
                break;
            case 'success':
                msg = 'La acci&oacute;n se ha realizado correctamente.';
                break;
            default:
                msg = 'No se ha definido información a mostrar al usuario.';
        }
    }

    var msgWi = '';

    switch (dialogClass.replace('dialog-', '')) {
        case 'error':
            msgWi = '<i class="fa fa-fw fa-times-circle"></i>&nbsp;&nbsp;' + msg;
            break;
        case 'warning':
            msgWi = '<i class="fa fa-fw fa-warning"></i>&nbsp;&nbsp;' + msg;
            break;
        case 'success':
            msgWi = '<i class="fa fa-fw fa-check-circle"></i>&nbsp;&nbsp;' + msg;
            break;
        default:
            msgWi = '<i class="fa fa-fw fa-info-circle"></i>&nbsp;&nbsp;' + msg;
    }

    element.append(msgWi);

    element.dialog({
        dialogClass: dialogClass + ( createXBtn ? '' : ' no-x-close'  ),
        modal: modal,
        title: title,
        width: width,
        buttons: arrayBtns,
        closeOnEscape: closeOnEscape
    });
}