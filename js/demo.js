$(function() {
	$('#submit').click(function() {
		var user = $('#user').val();
		var pass = $('#pass').val();
		
		if (user.length > 0 && pass.length > 0) {
			var data = 'user=' + user + '&pass=' + pass;
			
			$.ajax({
				type: 'post',
				url: 'demo.php',
				data: data,
				success: function(result) {
					$('#result').html(result);
				},
				error: function(xhr, status, code) {
					$('#result').html('Server returned ' + status);
				}
			});
		}
	});
});