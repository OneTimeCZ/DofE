$(document).ready(function(){

	var croppBox = null;
	var cropp;

	function readURL(input) {
        if(input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                cropp.croppie('bind', {
					url: e.target.result,
				});
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    $('input[name="img"]').change(function(){
        if(!croppBox){
	        croppBox = $("#croppbox");
			cropp = croppBox.croppie({
				viewport: {
					width: 960,
					height: 540,
				},
				boundary: {
					width: 960,
					height: 540
				}
			});
		}
		readURL(this);
	});

    var done = false;
	
	$("form#new_article").submit(function(event){
		if(!done){
			event.preventDefault();
			cropp.croppie("result", {type : "canvas"}).then(function(resp){
				$('<input>').attr({'type' : 'hidden', 'name' : 'image-article', 'value' : resp}).appendTo('form#new_article');
	    		done = true;
	    		$("form#new_article").submit();
			});
		}
	});
});