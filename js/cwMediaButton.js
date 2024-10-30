
var cwShortcode = '';
var patt = /cwContentTypeOptions\[(\w+)\]/i;

jQuery(document).ready(function() {
	jQuery('#cwSubmitSelectShortcode').on('click', function() {
		
		var contentType = jQuery('#cwContentTypeSelector option:selected').val();

		cwShortcode = '[cw ' + contentType;

		jQuery('.cwContentTypeOptions').each(function() {
			var currentElement = jQuery(this);
			var value = currentElement.val();
			if (value > '') {
				var name = currentElement.attr('name');
				var optionName = patt.exec(name)[1];
				cwShortcode+= ' ' + optionName + '=' + value;
			}
		});

		cwShortcode+= ']';

		window.send_to_editor(cwShortcode);
		tb_remove();
	})
});


var cwContentTypeSelectorElement = document.getElementById("cwContentTypeSelector");
var cwShortCodeParametersContainerElement = document.getElementById("cwShortCodeParametersContainer");


function cwDisplayContentTypeOptions() {
	
	var cwContentTypeSelectorElement = document.getElementById("cwContentTypeSelector");	
	var cwSelectedContentTypeValue = cwContentTypeSelectorElement.options[cwContentTypeSelectorElement.selectedIndex].value;

	jQuery.post(wpAdminUrl+'admin-ajax.php', {'action':'cwAjaxActionContentTypeOptions', 'cwSelectedContentType': cwSelectedContentTypeValue}, function(response) {
		cwShortCodeParametersContainer.innerHTML = response;
	});
}
