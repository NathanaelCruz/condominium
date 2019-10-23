(function ($) { 

    var $formLogin = $("#boxLogin #frmLogin");
    var $formReset = $("#boxReset #frmLogin");
    var $formLogout = $("div #frmModalLogout");
    var $frmForgot = $("div #frmForgot");

    $formLogin.on("submit", function (e) {
        e.preventDefault();

        return false;
    });

    $formReset.on("submit", function (e) {
        e.preventDefault();

        return false;
    });

    $formLogout.on("submit", function (e) {
        e.preventDefault();

        return false;
    });

    $frmForgot.on("submit", function (e) {
        e.preventDefault();

        return false;
    });

    $formLogin.validate({
        errorClass: "alert alert-danger",
        errorElement: "em",
        submitHandler: function (e) {

            var dados = $('#boxLogin #frmLogin').serialize();
            
            $.ajax({
                url: $formLogin.attr("action"),
                type: "POST",
                data: dados,
                success: function (data) {

                    var main = $("#boxLogin");

                    main.find(".alert").remove();

                    var results = jQuery.parseJSON(data);

                    if (!results.hasOwnProperty("code") || !results.hasOwnProperty("message")) { 
                        main.prepend(
                            '<div class="alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>');
                    } else {
                        main.prepend(
                            '<div class="alert alert-' + ((results.code === 200) ? 'success' : 'danger') + '" role="alert">' + ((results.code === 200) ? '<img class="imgLoaderCarrying" src="' + results.pathMain + 'Storage/site/loader/loading.svg" />' : '') + results.message + '</div>');
                            if (results.code === 200) {
                                setTimeout(function () {
                                    window.location.href = results.redirect ;
                                }, 500);
                            }

                    }
                }
            });

            return false;
        }
    });
    
    $formLogout.validate({
        errorClass: "alert alert-danger",
        errorElement: "em",
        submitHandler: function (e) {

            var dados = $('div #frmModalLogout').serialize();
            
            $.ajax({
                url: $formLogout.attr("action"),
                type: "POST",
                data: dados,
                success: function (data) {

                    var main = $("div #frmModalLogout");

                    main.find(".alert").remove();

                    var results = jQuery.parseJSON(data);

                    if (!results.hasOwnProperty("code") || !results.hasOwnProperty("message")) { 
                        main.prepend(
                            '<div class="alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>');
                    } else {
                        main.prepend(
                            '<div class="alert alert-' + ((results.code === 200) ? 'success' : 'danger') + '" role="alert">' + ((results.code === 200) ? '<img class="imgLoaderCarrying" src="' + results.pathMain + 'Storage/site/loader/loading.svg" />' : '') + results.message + '</div>');
                            if (results.code === 200) {
                                setTimeout(function () {
                                    window.location.href = results.redirect;
                                }, 1000);
                            }

                    }
                }
            });

            return false;
        }
    });
    
    $frmForgot.validate({
        errorClass: "alert alert-danger",
        errorElement: "em",
        submitHandler: function (e) {

            var dados = $('div #frmForgot').serialize();
            
            $.ajax({
                url: $frmForgot.attr("action"),
                type: "POST",
                beforeSend: function(){
    
                    $("div#alertCarrying").fadeIn(200);
                    $("div#alertCarrying").html('Enviando ...');
    
                },
                data: dados,
                success: function (data) {

                    $("div#alertCarrying").fadeOut(100);
                    var main = $("div #frmForgot");

                    main.find(".alert").remove();

                    var results = jQuery.parseJSON(data);

                    if (!results.hasOwnProperty("code") || !results.hasOwnProperty("message")) { 
                        main.prepend(
                            '<div class="alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>');
                    } else {
                        main.prepend(
                            '<div class="alert alert-' + ((results.code === 200) ? 'success' : 'danger') + '" role="alert">' + ((results.code === 200) ? '<img class="imgLoaderCarrying" src="' + results.pathMain + 'Storage/site/loader/loading.svg" />' : '') + results.message + '</div>');
                            if (results.code === 200) {
                                setTimeout(function () {
                                    window.location.href = results.redirect;
                                }, 1000);
                            }

                    }
                }
            });

            return false;
        }
    });


    $formReset.validate({
        errorClass: "alert alert-danger",
        errorElement: "em",
        submitHandler: function (e) {

            var dados = $('div #frmLogin').serialize();
            
            $.ajax({
                url: $formReset.attr("action"),
                type: "POST",
                data: dados,
                success: function (data) {

                    var main = $("div #frmLogin");

                    main.find(".alert").remove();

                    var results = jQuery.parseJSON(data);

                    if (!results.hasOwnProperty("code") || !results.hasOwnProperty("message")) { 
                        main.prepend(
                            '<div class="alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>');
                    } else {
                        main.prepend(
                            '<div class="alert alert-' + ((results.code === 200) ? 'success' : 'danger') + '" role="alert">' + ((results.code === 200) ? '<img class="imgLoaderCarrying" src="' + results.pathMain + 'Storage/site/loader/loading.svg" />' : '') + results.message + '</div>');
                            if (results.code === 200) {
                                setTimeout(function () {
                                    window.location.href = results.redirect;
                                }, 1000);
                            }

                    }
                }
            });

            return false;
        }
    });

})(jQuery); 