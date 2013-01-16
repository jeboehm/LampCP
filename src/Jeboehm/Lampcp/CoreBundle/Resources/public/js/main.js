/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

$(document).ready(function () {
    // Domainselector
    $('#jeboehm_lampcp_corebundle_domainselectortype_domain').bind({
        change:function () {
            $(this.form).submit();
        }
    });

    $('.deletebutton').on({
        click:function (e) {
            e.preventDefault();

            if (confirm($(this).attr('prototype-confirm'))) {
                location.href = 'delete';
            }
        }
    });

    $('.backbutton').on({
        click:function (e) {
            e.preventDefault();

            if(getControllerAction() != 'show') {
                location.href = location.href.substr(0, location.href.lastIndexOf('/'));
            } else {
                location.href = '../';
            }
        }
    });
});
