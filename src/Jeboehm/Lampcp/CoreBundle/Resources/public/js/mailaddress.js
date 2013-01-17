/**
 * LampCP
 * https://github.com/jeboehm/LampCP
 *
 * Licensed under the GPL Version 2 license
 * http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

var mailforward_holder = $('ul.mailforward');

jQuery(document).ready(function () {
	mailforward_holder.find('div').each(function () {
		var mailforward_delete_link = $('<a href="#" class="btn btn-danger">-</a>');
		$(this).append(mailforward_delete_link);
		$(this).addClass('input-append');

		mailforward_delete_link.on('click', function (e) {
			e.preventDefault();
			$(this).parent().parent().remove();
		});
	});

	$('.mailforward-add').bind('click', function (e) {
		var prototype = mailforward_holder.attr('data-prototype');
		var newIndex = mailforward_holder.find(':input').length;
		var newForm = prototype.replace(/__name__/g, newIndex);
		var newFormLi = $('<li></li>').append(newForm);
		mailforward_holder.append(newFormLi);
	});

	$('#jeboehm_lampcp_corebundle_mailaddresstype_mailaccount_enabled').on('click', function (e) {
		toggleMailAccountSettings();
	});

	toggleMailAccountSettings();
});

/**
 * Toggles MailAccountSettings
 */
function toggleMailAccountSettings() {
	var enabled = false;
	var i = 0;

	if($('#jeboehm_lampcp_corebundle_mailaddresstype_mailaccount_enabled').attr('checked')) {
		enabled = true;
	}

	$('#jeboehm_lampcp_corebundle_mailaddresstype_mailaccount').find('div').each(function () {
		if(i > 0) {
			if(enabled) {
				$(this).show();
			} else {
				$(this).hide();
			}
		}

		i++;
	});
}
