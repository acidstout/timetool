/**
 * Click handler which triggers token generation.
 * 
 * @returns false
 */
$(function() {
	$('#submit').click(function() {
		var user = $('#user').val();
		var pass = $('#pass').val();
		
		if (user.length > 0 && pass.length > 0) {
			var data = 'user=' + user + '&pass=' + pass;
			
			$.ajax({
				type: 'post',
				url: 'index.php',
				data: data,
				success: function(result) {
					$('#result').html(result);
				},
				error: function(xhr, status, code) {
					$('#result').html('Server returned ' + status);
				}
			});
		}
		
		return false;
	});
});


/**
 * Copy provided text to the clipboard.
 * 
 * @param text
 * @returns false
 */
function copyTextToClipboard(text) {
	var textArea = document.createElement('textarea');
	textArea.className = 'copyhelper';
	textArea.value = text;
	document.body.appendChild(textArea);
	textArea.select();

	try {
		var successful = document.execCommand('copy');
		if (successful) {
			alert('In die Zwischenablage kopiert.');
		} else {
			window.prompt('Automatisches Kopieren fehlgeschlagen. Diesen Text manuell kopieren: ', text);
		}
	} catch (err) {
		window.prompt('Automatisches Kopieren fehlgeschlagen. Diesen Text manuell kopieren: ', text);
	}
	
	document.body.removeChild(textArea);
	
	return false;
}
