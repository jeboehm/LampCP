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
    $('#form_domain').bind({
        change: function() {
            $(this.form).submit();
        }
    });
});