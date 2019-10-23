$('#confirm-delete').on('show.bs.modal', function(e) {

    $(this).find('#deleteIdentify').attr('value', $(e.relatedTarget).data('href'));
    
});

$('#confirm-report').on('show.bs.modal', function(e) {

    $(this).find('#typeReport').attr('value', $(e.relatedTarget).data('href'));

    $('#frmConfirmReport #btnGroups').html('');
    $('#frmConfirmReport #btnGroupsSend').html('');

    var url = $('#urlReport').val();

    if ($(e.relatedTarget).data('href') == 'csv') {

        $('#frmConfirmReport #btnGroups').append('<button id="btnWorkerExp" class="btn btn-success" type="button" onclick="exportReport(\'' + url + '\', \'workers\');"><i class="fas fa-file-csv"></i> Funcionários</button>');
        $('#frmConfirmReport #btnGroups').append('<button id="btnEEExp" class="btn btn-success" type="button" onclick="exportReport(\'' + url + '\', \'EE\');"><i class="fas fa-file-csv"></i> Entradas e Saídas</button>');
        $('#frmConfirmReport #btnGroups').append('<button id="btnWorkerExp" class="btn btn-success" type="button" onclick="exportReport(\'' + url + '\', \'visitors\');"><i class="fas fa-file-csv"></i> Visitantes</button>');
        $('#frmConfirmReport #btnGroups').append('<button id="btnResidentExp" class="btn btn-success" type="button" onclick="exportReport(\'' + url + '\', \'residents\');"><i class="fas fa-file-csv"></i> Moradores</button>');

        $('#frmConfirmReport #btnGroupsSend').append('<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Fechar</button>');

    } else {


        $('#frmConfirmReport #btnGroupsSend').append('<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Fechar</button>');

        $('#frmConfirmReport #btnGroupsSend').append('<button type="submit" id="downloadReport" class="btn btn-outline-primary btn-ok" onclick="exportReport(\'' + url + '\', \'\');"><i class="fas fa-file-pdf"></i>Exportar</button>');

    }

});