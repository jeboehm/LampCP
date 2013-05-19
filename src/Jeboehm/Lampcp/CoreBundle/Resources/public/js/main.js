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
        change: function () {
            $(this.form).submit();
        }
    });

    $('.btn').on({
        click: function (e) {
            if ($(this).attr('prototype-confirm')) {
                if (!confirm($(this).attr('prototype-confirm'))) {
                    return false;
                }
            }

            if ($(this).attr('prototype-target')) {
                e.preventDefault();
                location.href = $(this).attr('prototype-target');
            }
        }
    });
});
