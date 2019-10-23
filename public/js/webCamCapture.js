
var formUpdatePhoto = $("div #frmModalChangePhoto");

formUpdatePhoto.on("submit", function (e) {
    e.preventDefault();

    return false;
});

Webcam.set({
    width: 365,
    height: 290,
    image_format: 'jpeg',
    jpeg_quality: 90
});

Webcam.attach( '#my_camera' );

function take_snapshot(url) {

    Webcam.snap( function(data_uri) { 

        var dataPost = $("#frmModalChangePhoto #token_userChangePhoto");

        Webcam.upload( data_uri, url + 'update/MyAccount/new/photo/' + dataPost.val(), function(code, text) {
            
            } );  

        $(".image-tag").val(data_uri);
        document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
        
    } );
}
