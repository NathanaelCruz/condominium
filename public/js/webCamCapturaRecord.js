
var formUpdatePhoto = $("div #frmCreateUser");

formUpdatePhoto.on("submit", function (e) {
    e.preventDefault();

    return false;
});

Webcam.set({
    width: 340,
    height: 290,
    image_format: 'jpeg',
    jpeg_quality: 90
});

Webcam.attach( '#my_cameraRecord' );

function take_snapshotRecord(photoTemp, url) {

    Webcam.snap( function(data_uri) { 

        Webcam.upload( data_uri, url + 'create/photo/temp/' + photoTemp, function(code, text) {
            
            } );  

        $(".image-tagRecord").val(data_uri);
        document.getElementById('resultsRecord').innerHTML = '<img src="'+data_uri+'"/>';
        
    } );
}
