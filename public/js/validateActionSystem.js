(function($) {
  var $formUpdateUserInfo = $("div #frmMyAccount");
  var $formUpdateUserForResident = $("div #frmMyAccountForResidentInsideModal");
  var $formModalChangePhoto = $("div #frmModalChangePhoto");
  var $formCreateUser = $("div #frmCreateUser");
  var $frmUpdateUser = $("div #frmUpdateUser");
  var $frmModalChangePhotoUser = $("div #frmModalChangePhotoUser");
  var $frmCreateEE = $("div #frmCreateEE");
  var $frmUpdateEE = $("div #frmUpdateEE");
  var $frmSearchEE = $("div #frmSearchEE");
  var $frmConfirmDelete = $("div #frmConfirmDelete");
  var $frmConfirmReport = $("div #frmConfirmReport");

  $frmConfirmDelete.on("submit", function(e) {
    e.preventDefault();

    return false;
  });

  $frmConfirmReport.on("submit", function(e) {
    e.preventDefault();

    return false;
  });


  $formUpdateUserInfo.on("submit", function(e) {
    e.preventDefault();

    return false;
  });

  $formUpdateUserForResident.on("submit", function(e) {
    e.preventDefault();

    return false;
  });

  $formModalChangePhoto.on("submit", function(e) {
    e.preventDefault();

    return false;
  });

  $formCreateUser.on("submit", function(e) {
    e.preventDefault();

    return false;
  });

  $frmUpdateUser.on("submit", function(e) {
    e.preventDefault();

    return false;
  });

  $frmModalChangePhotoUser.on("submit", function(e) {
    e.preventDefault();

    return false;
  });

  $frmCreateEE.on("submit", function(e) {
    e.preventDefault();

    return false;
  });

  $frmUpdateEE.on("submit", function(e) {
    e.preventDefault();

    return false;
  });

  $frmSearchEE.on("submit", function(e) {
    e.preventDefault();

    return false;
  });

  $formUpdateUserInfo.validate({
    errorClass: "alert alert-danger",
    errorElement: "em",
    submitHandler: function(e) {
      var dados = $("div #frmMyAccount").serialize();

      $.ajax({
        url: $formUpdateUserInfo.attr("action"),
        type: "POST",
        data: dados,
        success: function(data) {
          var main = $("#frmMyAccount");

          main.find(".alert").remove();

          var results = jQuery.parseJSON(data);

          if (
            !results.hasOwnProperty("code") ||
            !results.hasOwnProperty("message")
          ) {
            main.prepend(
              '<div class="alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>'
            );
          } else {
            main.prepend(
              '<div class="alert alert-' +
                (results.code === 200 ? "success" : "danger") +
                ' form-row" role="alert">' +
                (results.code === 200
                  ? '<img class="imgLoaderCarrying" src="' +
                    results.pathMain +
                    'Storage/site/loader/loading.svg" />'
                  : "") +
                results.message +
                "</div>"
            );
            if (results.code === 200) {
              setTimeout(function() {
                window.location.href = results.redirect;
              }, 1000);
            }
          }
        }
      });

      return false;
    }
  });

  $formUpdateUserForResident.validate({
    errorClass: "alert alert-danger",
    errorElement: "em",
    submitHandler: function(e) {
      var dados = $("div #frmMyAccountForResidentInsideModal").serialize();

      $.ajax({
        url: $formUpdateUserForResident.attr("action"),
        type: "POST",
        data: dados,
        success: function(data) {
          var main = $("#frmMyAccountForResidentInsideModal");

          main.find(".alert").remove();

          var results = jQuery.parseJSON(data);

          if (
            !results.hasOwnProperty("code") ||
            !results.hasOwnProperty("message")
          ) {
            main.prepend(
              '<div class="alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>'
            );
          } else {
            main.prepend(
              '<div class="alert alert-' +
                (results.code === 200 ? "success" : "danger") +
                ' form-row" role="alert">' +
                (results.code === 200
                  ? '<img class="imgLoaderCarrying" src="' +
                    results.pathMain +
                    'Storage/site/loader/loading.svg" />'
                  : "") +
                results.message +
                "</div>"
            );
            if (results.code === 200) {
              setTimeout(function() {
                window.location.href = results.redirect;
              }, 1000);
            }
          }
        }
      });

      return false;
    }
  });

  $formModalChangePhoto.validate({
    errorClass: "alert alert-danger",
    errorElement: "em",
    submitHandler: function(e) {
      var dados = $("div #frmModalChangePhoto").serialize();

      $.ajax({
        url: $formModalChangePhoto.attr("action"),
        type: "POST",
        data: dados,
        success: function(data) {
          var main = $("#frmModalChangePhoto");

          main.find(".alert").remove();

          var results = jQuery.parseJSON(data);

          if (
            !results.hasOwnProperty("code") ||
            !results.hasOwnProperty("message")
          ) {
            main.prepend(
              '<div class="alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>'
            );
          } else {
            main.prepend(
              '<div class="alert alert-' +
                (results.code === 200 ? "success" : "danger") +
                ' form-row" role="alert">' +
                (results.code === 200
                  ? '<img class="imgLoaderCarrying" src="' +
                    results.pathMain +
                    'Storage/site/loader/loading.svg" />'
                  : "") +
                results.message +
                "</div>"
            );
            if (results.code === 200) {
              setTimeout(function() {
                window.location.href = results.redirect;
              }, 1000);
            }
          }
        }
      });

      return false;
    }
  });

  $formCreateUser.validate({
    errorClass: "alert alert-danger",
    errorElement: "em",
    submitHandler: function(e) {
      var dados = $("div #frmCreateUser").serialize();

      $.ajax({
        url: $formCreateUser.attr("action"),
        type: "POST",
        data: dados,
        success: function(data) {
          var main = $("#frmCreateUser");

          main.find(".alert").remove();

          var results = jQuery.parseJSON(data);

          if (
            !results.hasOwnProperty("code") ||
            !results.hasOwnProperty("message")
          ) {
            main.prepend(
              '<div class="alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>'
            );
          } else {
            main.prepend(
              '<div class="alert alert-' +
                (results.code === 200 ? "success" : "danger") +
                ' form-row" role="alert">' +
                (results.code === 200
                  ? '<img class="imgLoaderCarrying" src="' +
                    results.pathMain +
                    'Storage/site/loader/loading.svg" />'
                  : "") +
                results.message +
                "</div>"
            );
            if (results.code === 200) {
              setTimeout(function() {
                window.location.href = results.redirect;
              }, 1000);
            }
          }
        }
      });

      return false;
    }
  });

  $frmUpdateUser.validate({
    errorClass: "alert alert-danger",
    errorElement: "em",
    submitHandler: function(e) {
      var dados = $("div #frmUpdateUser").serialize();

      $.ajax({
        url: $frmUpdateUser.attr("action"),
        type: "POST",
        data: dados,
        success: function(data) {
          var main = $("#frmUpdateUser");

          main.find(".alert").remove();

          var results = jQuery.parseJSON(data);

          if (
            !results.hasOwnProperty("code") ||
            !results.hasOwnProperty("message")
          ) {
            main.prepend(
              '<div class="form-row alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>'
            );
          } else {
            main.prepend(
              '<div class="form-row alert alert-' +
                (results.code === 200 ? "success" : "danger") +
                ' form-row" role="alert">' +
                (results.code === 200
                  ? '<img class="imgLoaderCarrying" src="' +
                    results.pathMain +
                    'Storage/site/loader/loading.svg" />'
                  : "") +
                results.message +
                "</div>"
            );
            if (results.code === 200) {
              setTimeout(function() {
                window.location.href = results.redirect;
              }, 1000);
            }
          }
        }
      });

      return false;
    }
  });

  $frmModalChangePhotoUser.validate({
    errorClass: "alert alert-danger",
    errorElement: "em",
    submitHandler: function(e) {
      var dados = $("div #frmModalChangePhotoUser").serialize();

      $.ajax({
        url: $frmModalChangePhotoUser.attr("action"),
        type: "POST",
        data: dados,
        success: function(data) {
          var main = $("#frmModalChangePhotoUser");

          main.find(".alert").remove();

          var results = jQuery.parseJSON(data);

          if (
            !results.hasOwnProperty("code") ||
            !results.hasOwnProperty("message")
          ) {
            main.prepend(
              '<div class="alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>'
            );
          } else {
            main.prepend(
              '<div class="alert alert-' +
                (results.code === 200 ? "success" : "danger") +
                '" role="alert">' +
                (results.code === 200
                  ? '<img class="imgLoaderCarrying" src="' +
                    results.pathMain +
                    'Storage/site/loader/loading.svg" />'
                  : "") +
                results.message +
                "</div>"
            );
            if (results.code === 200) {
              setTimeout(function() {
                window.location.href = results.redirect;
              }, 1000);
            }
          }
        }
      });

      return false;
    }
  });

  $frmCreateEE.validate({
    errorClass: "alert alert-danger",
    errorElement: "em",
    submitHandler: function(e) {
      var dados = $("div #frmCreateEE").serialize();

      $.ajax({
        url: $frmCreateEE.attr("action"),
        type: "POST",
        data: dados,
        success: function(data) {
          var main = $("#frmCreateEE");

          main.find(".alert").remove();

          var results = jQuery.parseJSON(data);

          if (
            !results.hasOwnProperty("code") ||
            !results.hasOwnProperty("message")
          ) {
            main.prepend(
              '<div class="alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>'
            );
          } else {
            main.prepend(
              '<div class="alert alert-' +
                (results.code === 200 ? "success" : "danger") +
                ' form-row" role="alert">' +
                (results.code === 200
                  ? '<img class="imgLoaderCarrying" src="' +
                    results.pathMain +
                    'Storage/site/loader/loading.svg" />'
                  : "") +
                results.message +
                "</div>"
            );
            if (results.code === 200) {
              setTimeout(function() {
                window.location.href = results.redirect;
              }, 1000);
            }
          }
        }
      });

      return false;
    }
  });

  function load_dados(search, btn)
    {
        var urlForm =
      $("div #frmCreateEE #urlInfor").val() + "search/user/forvisit";

        $.ajax({
          url: urlForm,
          type: "POST",
          data: {chanceDocument: btn, documentSearch: search},
          success: function(data) {

              var main = $("#frmCreateEE");

              main.find(".alert").remove();


              var results = jQuery.parseJSON(data);

              $("#imgEE").attr('src', results['redirect'] + 'Storage/site/default.jpg');
              $("#frmCreateEE #idUser").val('');
              $("#frmCreateEE #name").val('');
              $("#frmCreateEE #fldEmail").val('');
              $("#frmCreateEE #rg").val('');
              $("#frmCreateEE #cpf").val('');
              $("#frmCreateEE #phone").val('');
              $("#frmCreateEE #birthday").val('');

              if (
              !results.hasOwnProperty("code") ||
              !results.hasOwnProperty("message")
              ) {
              main.prepend(
                  '<div class="alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>'
              );
              } else {
                  main.prepend(
                      '<div class="alert alert-' +
                      (results.code === 200 ? "success" : "danger") +
                      ' form-row" role="alert">' +
                      results.message +
                      "</div>"
                  );

                  if (results.code === 200) {

                    $("#imgEE").attr('src', results['redirect'] + 'Storage/' + results['data']['0'].token_user + '.jpg');
                    $("#frmCreateEE #idUser").val(results['data']['0'].id_user);
                    $("#frmCreateEE #name").val(results['data']['0'].name);
                    $("#frmCreateEE #fldEmail").val(results['data']['0'].email);
                    $("#frmCreateEE #rg").val(results['data']['0'].rg);
                    $("#frmCreateEE #cpf").val(results['data']['0'].cpf);
                    $("#frmCreateEE #phone").val(results['data']['0'].phone);
                    $("#frmCreateEE #birthday").val(results['data']['0'].birthday);

                  }
              }
          }
        });
    }

    $("#documentSearch").keyup(function() {

        var $search = $(this).val();
        var $btn = $("input[type=radio][name=chanceDocument]").val();
        console.log($search);
        if ($search.length >= 1) {

            load_dados($search, $btn);

        } else {

            load_dados(null, null);

        }

    });



    function load_dados_search(search, btn)
    {
        var urlForm =
      $("div #frmSearchEE #urlInfor").val() + "search/user/exit/validate";

        $.ajax({
          url: urlForm,
          type: "POST",
          data: {chanceDocument: btn, documentSearchExit: search},
          success: function(data) {

              var main = $("#frmSearchEE");

              main.find(".alert").remove();


              var results = jQuery.parseJSON(data);

              $("#imgEE").attr('src', results['redirect'] + 'Storage/site/default.jpg');
              $("#frmSearchEE #idUser").val('');
              $("#frmSearchEE #name").val('');
              $("#frmSearchEE #fldEmail").val('');
              $("#frmSearchEE #rg").val('');
              $("#frmSearchEE #cpf").val('');
              $("#frmSearchEE #phone").val('');
              $("#frmSearchEE #birthday").val('');
              $("#frmSearchEE #idEEUpdate").val('');

              if (
              !results.hasOwnProperty("code") ||
              !results.hasOwnProperty("message")
              ) {
              main.prepend(
                  '<div class="alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>'
              );
              } else {
                  main.prepend(
                      '<div class="alert alert-' +
                      (results.code === 200 ? "success" : "danger") +
                      ' form-row" role="alert">' +
                      results.message +
                      "</div>"
                  );

                  if (results.code === 200) {

                    $("#imgEE").attr('src', results['redirect'] + 'Storage/' + results['data']['0'].token_user + '.jpg');
                    $("#frmSearchEE #idUser").val(results['data']['0'].id_user);
                    $("#frmSearchEE #name").val(results['data']['0'].name);
                    $("#frmSearchEE #fldEmail").val(results['data']['0'].email);
                    $("#frmSearchEE #rg").val(results['data']['0'].rg);
                    $("#frmSearchEE #cpf").val(results['data']['0'].cpf);
                    $("#frmSearchEE #phone").val(results['data']['0'].phone);
                    $("#frmSearchEE #birthday").val(results['data']['0'].birthday);
                    $("#frmSearchEE #idEEUpdate").val(results['data']['0'].id_EE);

                  }
              }
          }
        });
    }

    $("#documentSearchExit").keyup(function() {

        var $search = $(this).val();
        var $btn = $("input[type=radio][name=chanceDocument]").val();
        console.log($search);
        if ($search.length >= 1) {

            load_dados_search($search, $btn);

        } else {

            load_dados_search(null, null);

        }

    });
    
    $frmUpdateEE.validate({
      errorClass: "alert alert-danger",
      errorElement: "em",
      submitHandler: function(e) {
        var dados = $("div #frmUpdateEE").serialize();
  
        $.ajax({
          url: $frmUpdateEE.attr("action"),
          type: "POST",
          data: dados,
          success: function(data) {
            var main = $("#frmUpdateEE");
  
            main.find(".alert").remove();
  
            var results = jQuery.parseJSON(data);
  
            if (
              !results.hasOwnProperty("code") ||
              !results.hasOwnProperty("message")
            ) {
              main.prepend(
                '<div class="form-row alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>'
              );
            } else {
              main.prepend(
                '<div class="form-row alert alert-' +
                  (results.code === 200 ? "success" : "danger") +
                  '" role="alert">' +
                  (results.code === 200
                    ? '<img class="imgLoaderCarrying" src="' +
                      results.pathMain +
                      'Storage/site/loader/loading.svg" />'
                    : "") +
                  results.message +
                  "</div>"
              );
              if (results.code === 200) {
                setTimeout(function() {
                  window.location.href = results.redirect;
                }, 1000);
              }
            }
          }
        });
  
        return false;
      }
    });
    
    $frmSearchEE.validate({
      errorClass: "alert alert-danger",
      errorElement: "em",
      submitHandler: function(e) {
        var dados = $("div #frmSearchEE").serialize();
  
        $.ajax({
          url: $frmSearchEE.attr("action"),
          type: "POST",
          data: dados,
          success: function(data) {
            var main = $("#frmSearchEE");
  
            main.find(".alert").remove();
  
            var results = jQuery.parseJSON(data);
  
            if (
              !results.hasOwnProperty("code") ||
              !results.hasOwnProperty("message")
            ) {
              main.prepend(
                '<div class="form-row alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>'
              );
            } else {
              main.prepend(
                '<div class="form-row alert alert-' +
                  (results.code === 200 ? "success" : "danger") +
                  '" role="alert">' +
                  (results.code === 200
                    ? '<img class="imgLoaderCarrying" src="' +
                      results.pathMain +
                      'Storage/site/loader/loading.svg" />'
                    : "") +
                  results.message +
                  "</div>"
              );
              if (results.code === 200) {
                setTimeout(function() {
                  window.location.href = results.redirect;
                }, 1000);
              }
            }
          }
        });
  
        return false;
      }
    });
    

    $frmConfirmDelete.validate({
      errorClass: "alert alert-danger",
      errorElement: "em",
      submitHandler: function(e) {
        var dados = $("div #frmConfirmDelete").serialize();
  
        $.ajax({
          url: $frmConfirmDelete.attr("action"),
          type: "POST",
          data: dados,
          success: function(data) {
            var main = $("#frmConfirmDelete");
  
            main.find(".alert").remove();
  
            var results = jQuery.parseJSON(data);
  
            if (
              !results.hasOwnProperty("code") ||
              !results.hasOwnProperty("message")
            ) {
              main.prepend(
                '<div class="alert alert-danger" role="alert">Ocorreu um erro inesperado.</div>'
              );
            } else {
              main.prepend(
                '<div class="alert alert-' +
                  (results.code === 200 ? "success" : "danger") +
                  '" role="alert">' +
                  (results.code === 200
                    ? '<img class="imgLoaderCarrying" src="' +
                      results.pathMain +
                      'Storage/site/loader/loading.svg" />'
                    : "") +
                  results.message +
                  "</div>"
              );
              if (results.code === 200) {
                setTimeout(function() {
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


function exportReport (url, table = '') {

  window.open(url + '?type=' + $('input[name="typeReport"]').val() + '&table=' + table);

}