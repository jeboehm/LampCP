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
    dnsrecord_holder.find('div.controls').each(function () {
        var dnsrecord_delete_link = $('<a href="#" class="btn btn-danger">-</a>');

        if($(this).attr('id') != 'addbutton') {
            $(this).append(dnsrecord_delete_link);
        }

        dnsrecord_delete_link.on('click', function (e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });
    });

    $('.dnsrecord-add').bind('click', function (e) {
        var prototype = dnsrecord_holder.attr('data-prototype');
        var newIndex = dnsrecord_holder.find(':input').length;
        var newForm = prototype.replace(/__name__/g, newIndex);
        var container = $('<div class="control-group"><div class="controls"></div></div>')
        container.find('div.controls').append(newForm);
        dnsrecord_holder.append(container);
    });
});
