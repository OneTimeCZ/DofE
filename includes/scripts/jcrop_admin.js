$(function () {
    $('input#file').change(function () {
        readURL(this);
        
        $('div.image-cropper-container').toggle();
        $('#admin-cropper').width('100%').height('auto');
        
        setTimeout(function () {
            /*jQuery(function ($) {
                $('img#admin-cropper').Jcrop({
                    bgColor:     'black',
                    bgOpacity:   0.4,
                    aspectRatio: 16 / 9
                });
            });*/
            
            $('#admin-cropper').Jcrop({
            
            }, function () {
                var bounds = this.getBounds(),
                    boundx = bounds[0],
                    boundy = bounds[1];
                
                jcrop_api = this;
            });
        }, 3000);
    });
    
    
    
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                $('#admin-cropper').attr('src', e.target.result);
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }
});