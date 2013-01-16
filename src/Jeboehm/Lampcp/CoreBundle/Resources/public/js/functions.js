/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

/**
 * Tries to get the controller action from url.
 * Needs a valid url scheme.
 *
 * app.php/config/subdomain/{id}/action
 * app.php/config/subdomain/action
 *
 * @return {String}
 */
function getControllerAction() {
    var pos = location.href.lastIndexOf('/');
    var action = location.href.substr(pos + 1);

    if (action != 'new' && action != 'update' && action != 'delete'
        && action != 'create' && action != 'show' && action != 'edit') {
        return 'index';
    }

    return action;
}
