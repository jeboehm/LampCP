/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

var dnsrecord_holder = $('div.dnsrecords');

jQuery(document).ready(function () {
    dnsrecord_holder.find('div.dnsactions').each(function () {
        var dnsrecord_delete_link = $('<a href="#" class="btn btn-danger">-</a>');
        var dnsrecord_sort_up = $('<a href="#" class="btn btn-info">↑</a>');
        var dnsrecord_sort_down = $('<a href="#" class="btn btn-info">↓</a>');

        /**
         * Aktionsbuttons hinzufügen.
         */
        if ($(this).attr('id') != 'addbutton') {
            $(this).append(dnsrecord_delete_link);
            $(this).append(dnsrecord_sort_up);
            $(this).append(dnsrecord_sort_down);
        }

        /**
         * Eintrag löschen.
         */
        dnsrecord_delete_link.on('click', function (e) {
            var container = $(this).parent().parent().parent();
            e.preventDefault();

            $(container).remove();
        });

        /**
         * Eintrag nach oben verschieben.
         */
        dnsrecord_sort_up.on('click', function (e) {
            var container = $(this).parent().parent().parent();
            e.preventDefault();

            var thispos = $(container).find('input.dnsposition');

            if (parseInt($(thispos).val()) > 0) {
                var swappos = $(container).prev().find('input.dnsposition');
                dnsSwitchPositions(swappos, thispos, true);

                $(container).insertBefore($(container).prev());
            }
        });

        /**
         * Eintrag nach unten verschieben.
         */
        dnsrecord_sort_down.on('click', function (e) {
            var container = $(this).parent().parent().parent();
            e.preventDefault();

            // Nicht unter die Aktionsbuttons schieben.
            if (!$(container).next().hasClass('dnsformcontrol')) {
                var thispos = $(container).find('input.dnsposition');
                var swappos = $(container).next().find('input.dnsposition');
                dnsSwitchPositions(swappos, thispos, false);

                $(container).insertAfter($(container).next());
            }
        });
    });

    /**
     * Eintrag hinzufügen.
     */
    $('.dnsrecord-add').bind('click', function (e) {
        var prototype = dnsrecord_holder.attr('data-prototype');
        var newIndex = dnsrecord_holder.find(':input').length;
        var newForm = prototype.replace(/__name__/g, newIndex);
        var container = $('<div class="control-group"><div class="controls"></div></div>')
        container.find('div.controls').append(newForm);
        dnsrecord_holder.append(container);
    });
});

/**
 * Switch position fields.
 * If up is true, objB will be decreased.
 *
 * @param objA
 * @param objB
 * @param up
 */
function dnsSwitchPositions(objA, objB, up) {
    posA = parseInt($(objA).val());
    posB = parseInt($(objB).val());

    if (up) {
        $(objA).val(posA + 1);
        $(objB).val(posB - 1);
    } else {
        $(objA).val(posA - 1);
        $(objB).val(posB + 1);
    }
}
