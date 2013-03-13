/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

var mailforward_holder = $('div.mailforward');

jQuery(document).ready(function () {
	mailforward_holder.find('div.controls').each(function () {
		var mailforward_delete_link = $('<a href="#" class="btn btn-danger">-</a>');

		if($(this).attr('id') != 'addbutton') {
			$(this).append(mailforward_delete_link);
		}

		mailforward_delete_link.on('click', function (e) {
			e.preventDefault();
			$(this).parent().parent().remove();
		});
	});

	$('.mailforward-add').bind('click', function (e) {
		var prototype = mailforward_holder.attr('data-prototype');
		var newIndex = mailforward_holder.find(':input').length;
		var newForm = prototype.replace(/__name__/g, newIndex);
		mailforward_holder.append(newForm);
	});
});
