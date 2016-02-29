$(document).ready(function () {
	var croppBox = null, cropp, done = false;

	function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                cropp.croppie('bind', {
					url: e.target.result
				});
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    $('input[name="avatar-input"]').change(function () {
        if (!croppBox) {
	        croppBox = $("#croppbox");
			cropp = croppBox.croppie({
				viewport: {
					width: 200,
					height: 200
				},
				boundary: {
					width: 200,
					height: 200
				}
			});
		}
		readURL(this);
	});
	
	$("#upload-img-form").submit(function (event) {
		if (!done) {
			event.preventDefault();
			cropp.croppie("result", {type : "canvas"}).then(function (resp) {
				$('<input>').attr({'type' : 'hidden', 'name' : 'newAvatar', 'value' : resp}).appendTo('#upload-img-form');
                done = true;
                $("#upload-img-form").submit();
			});
		}
	});
});