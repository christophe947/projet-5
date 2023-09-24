
$(function() {
    $(".testAjax").on("click", function(event) {
        event.preventDefault();
        page = $(this).attr("href");
        jQuery.ajax({
            url: page,
            dataType: "html",
            success: function (data) {
                jQuery("#ajaxDivMedia").html(data);
                //jQuery("#blockPage").style.height = "110% !important";
            },
            error: function () {
                jQuery("#ajaxDivMedia").html("Erreur de chargement...");
            }
        });
    });
});


if ($(".divBtnUpload").length > 0 ){
    $(function (){
        $("#pic").trigger( "click" );
       
    });
 };


variable = {
    pictureButton: document.querySelector("#pic"),
    videoButton: document.querySelector("#vid"),
    musicButton: document.querySelector("#mus"),
    albumButton: document.querySelector("#alb"),
    idFinal: "#" + ($(this).attr("id")),
    idFinal2: ($(this).attr("id"))
};


$("#pic").on("click", function(){
    variable.pictureButton.style.color = "rgba(84, 84, 84, 1)";
    variable.pictureButton.style.boxShadow = "rgba(0, 0, 0, 0.5) 1px 1px 10px";
    variable.videoButton.style.color = "rgba(84, 84, 84, 0.75)";
    variable.videoButton.style.boxShadow = "none";
    variable.musicButton.style.color = "rgba(84, 84, 84, 0.75)";
    variable.musicButton.style.boxShadow = "none";
});

$("#vid").on("click", function(){
    variable.pictureButton.style.color = "rgba(84, 84, 84, 0.75)";
    variable.pictureButton.style.boxShadow = "none";
    variable.videoButton.style.color = "rgba(84, 84, 84, 1)";
    variable.videoButton.style.boxShadow = "rgba(0, 0, 0, 0.5) 1px 1px 10px";
    variable.musicButton.style.color = "rgba(84, 84, 84, 0.75)";
    variable.musicButton.style.boxShadow = "none";
});

$("#mus").on("click", function(){
    variable.pictureButton.style.color = "rgba(84, 84, 84, 0.75)";
    variable.pictureButton.style.boxShadow = "none";
    variable.videoButton.style.color = "rgba(84, 84, 84, 0.75)";
    variable.videoButton.style.boxShadow = "none";
    variable.musicButton.style.color = "rgba(84, 84, 84, 1)";
    variable.musicButton.style.boxShadow = "rgba(0, 0, 0, 0.5) 1px 1px 10px";
});
    

$(".loopAlbum").on("click", function(event) {
    event.preventDefault();
    var id = ($(this).attr("id"));
    previous = document.querySelector(".previousAlbum");
    if(previous !== null) {
       previous.style.boxShadow = "none";
            previous.style.color = "rgba(84, 84, 84, 0.75)";
            $(".previousAlbum").removeClass( "previousAlbum" ).addClass("loopAlbum");
        }
        $("#\\"+id).removeClass( "loopAlbum" ).addClass("loopSelected");
    
        if ($(".loopSelected").length > 0 ) {
            selected = document.querySelector(".loopSelected"); 
            selected.style.boxShadow = "rgba(0, 0, 0, 0.5) 1px 1px 10px";
            selected.style.color = "rgba(84, 84, 84, 1)";
        if (selected.style.boxShadow = true) {
            $(".loopSelected").removeClass( "loopSelected" ).addClass("previousAlbum"); 
        }
    };
});
