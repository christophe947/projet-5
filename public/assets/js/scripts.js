window.onload = function() {
    if (window.jQuery) {  
        // jQuery is loaded  
        alert("Yeah!");
    } else {
        // jQuery is not loaded
        alert("Doesn't Work");
    }
}
//die();

//$(document).ready(function (){
//    $(".testAjax").trigger("click")(function(event) {
//        event.preventDefault();
//        page = ($(this).attr("href"));
//        jQuery.ajax({
//            url: page,
//            dataType: "html",
//            success: function (data/*, textStatus, rawRequest*/) {
//                jQuery("#ajaxDivMedia").html(data);
//            },
//            error: function (/*rawRequest, textStatus, errorThrow*/) {
//                jQuery("#ajaxDivMedia").html("Erreur de chargement...");
//            }
//        });
//    });
//});

$("#mus").trigger("click", function() {
    alert("Handler for `click` called.");
});

//});
/*$(".formAjax").on("submit", function(e){
    e.preventDefault();
    let data = {};
    $(this).serializeArray().forEach((object)=>{
       data[object.name] = object.value;
    });
    console.log(data);
    
    //TODO: ajax call here with data
    //If ajax call fails because server can't decode
    //Think of doing : data = JSON.stringify(data);
    console.log(JSON.stringify(data));
    
 })*/

variable = {
    pictureButton: document.querySelector("#pic"),
    videoButton: document.querySelector("#vid"),
    musicButton: document.querySelector("#mus"),
    albumButton: document.querySelector("#alb"),
    //idFinal: "#" + ($(this).attr("id")),
    //idFinal2: ($(this).attr("id"))
};

/*if ($(".divBtnUpload").length > 0 ){
    $(document).ready(function (){
        $("#pic").trigger( "click" );
       
    });
 };*/
 $( ".divBtnUpload" ).on( "click", function() {
    alert( "Handler for `click` called." );
  } );

  $( "#other" ).on( "click", function() {
    $( "#target" ).trigger( "click" );
  } );
 
// $(function() {
//    if ($(".divBtnUpload").length > 0 ){
//        alert("Hello World! Welcome to upwork.");
//};
//  }); 

 
 //$('#content-container').html(linkText);

("#pic").trigger("click")(function(){
    alert("Hello World! Welcome to upwork.");
    variable.pictureButton.style.color = "rgba(84, 84, 84, 1)";
    variable.pictureButton.style.boxShadow = "rgba(0, 0, 0, 0.5) 1px 1px 10px";
    variable.videoButton.style.color = "rgba(84, 84, 84, 0.75)";
    variable.videoButton.style.boxShadow = "none";
    variable.musicButton.style.color = "rgba(84, 84, 84, 0.75)";
    variable.musicButton.style.boxShadow = "none";
});

$("#vid").trigger("click")(function(){
    alert('tttttt');
});

$( "#vid" ).on( "click", function() {
   alert('fefefe');
  } );

$("#vid").trigger("click")(function(){
    alert("Hello World! Welcome to upwork.");
    variable.pictureButton.style.color = "rgba(84, 84, 84, 0.75)";
    variable.pictureButton.style.boxShadow = "none";
    variable.videoButton.style.color = "rgba(84, 84, 84, 1)";
    variable.videoButton.style.boxShadow = "rgba(0, 0, 0, 0.5) 1px 1px 10px";
    variable.musicButton.style.color = "rgba(84, 84, 84, 0.75)";
    variable.musicButton.style.boxShadow = "none";
});

$("#mus").click(function(){
    variable.pictureButton.style.color = "rgba(84, 84, 84, 0.75)";
    variable.pictureButton.style.boxShadow = "none";
    variable.videoButton.style.color = "rgba(84, 84, 84, 0.75)";
    variable.videoButton.style.boxShadow = "none";
    variable.musicButton.style.color = "rgba(84, 84, 84, 1)";
    variable.musicButton.style.boxShadow = "rgba(0, 0, 0, 0.5) 1px 1px 10px";
});







$(".loopAlbum").click(function(event) {
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



























  /*function getXhr(){
    var xhr = null; 
    if(window.XMLHttpRequest) // Firefox et autres
        xhr = new XMLHttpRequest(); 
    else if(window.ActiveXObject){ // Internet Explorer 
        try {
            xhr = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    else { // XMLHttpRequest non supporté par le navigateur 
        alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest..."); 
        xhr = false; 
    } 
    return xhr
}*/

/**
* Méthode qui sera appelée sur le clic du bouton
*/
/*function go(){
    var xhr = getXhr()
    // On définit ce qu'on va faire quand on aura la réponse
    xhr.onreadystatechange = function(){
        // On ne fait quelque chose que si on a tout reçu et que le serveur est OK
        if(xhr.readyState == 4 && xhr.status == 200){
            $("#ajaxDivMedia")
            alert(xhr.responseText);
        }
    }
    xhr.open("GET", "https://127.0.0.1:8000/profil/53/media/videos", true);
    xhr.send(null);
}*/