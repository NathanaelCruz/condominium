$(document).ready(function () { 

    var $cpf = $("#cpf");
    var $rg = $("#rg");
    var $phone = $("#phone");
    var $avgIcome = $("#avgIncome");
    var $searchField = $("#documentSearch");
    var $documentSearchExit = $("#documentSearchExit");

    $cpf.mask('000.000.000-00', {reverse: true});
    $rg.mask("00.000.000-0", {reverse: true});
    $phone.mask("(00) #0000-0000");
    $avgIcome.mask("#.##0,00", {reverse: true});
    $searchField.mask("00.000.000-0", {reverse: true});
    $documentSearchExit.mask("00.000.000-0", {reverse: true});


    $("input[type=radio][name=chanceDocument]").change(function() {

        if (this.value == 'opcRG') {
            $searchField.mask("00.000.000-0", {reverse: true});
            $documentSearchExit.mask("00.000.000-0", {reverse: true});
        }
        else if (this.value == 'opcCFP') {
            $searchField.mask("000.000.000-00", {reverse: true});
            $documentSearchExit.mask("000.000.000-00", {reverse: true});
        }
    });

});