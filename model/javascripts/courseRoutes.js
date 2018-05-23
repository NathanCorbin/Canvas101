// derived from: https://stackoverflow.com/a/8802111
var g = document.getElementById('courses');
for (var i = 0, len = g.children.length; i < len; i++)
{
    (function(index){
        g.children[i].onclick = function(){
            var url = window.location.href;
	        url = url.replace(/\d+$/, "");
	        url += index;
            window.location.href = url;
        }    
    })(i);
}

$("#refreshApi").on("click", function(){
    $.ajax({
        type: "POST",
        data: {"refresh": true},
        dataType: "text",
        success: function(data) {
            location.href = window.location.href;
        }, 
    });
});