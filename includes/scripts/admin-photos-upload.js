$(document).ready(function () {
    if(window.File && window.FileList && window.FileReader) {
        var filesInput = document.getElementById("croppbox-input");
        
        filesInput.addEventListener("change", function(event){
            
            var files = event.target.files;
            var output = document.getElementById("image-holder");
            
            $("img.preview").remove();
            
            for(var i = 0; i< files.length; i++) {
                var file = files[i];
                
                if(!file.type.match('image')) continue;
                
                var imageReader = new FileReader();
                
                imageReader.addEventListener("load",function(event) {
                    
                    var imageFile = event.target;
                    
                    var div = document.createElement("div");
                    div.className = "previewBox";
                    
                    div.innerHTML = "<img class='img img-responsive preview img-thumbnail' src='" + imageFile.result + "'" +
                            "title='Náhled'/>";
                    
                    output.insertBefore(div,null); $('<input>').attr({'type' : 'hidden', 'name' : 'image[]', 'multiple' : 'multiple', 'value' : imageFile.result}).appendTo('form#edit_gallery');           
                });
                
                imageReader.readAsDataURL(file);
            }                                        
        });
    } else {
        alert("Your browser does not support File API");
    }
});