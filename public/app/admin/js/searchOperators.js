$(document).ready(function () {

    $('#searchForm').submit(function (e) {
        e.preventDefault();
    });

    $('#searchOperators').click(function () {

        var url = $('#searchForm').attr("action");
        //var url = $('#searchOperators').attr("href");
        //$('#errorSearch').css("display", "none");
        //$('#successSearch').css("display", "none");

        // console.log($('#opCif').val());
        $("#opReg").val();
        //console.log($('#idAct').val());

        if ($('#opCif').val().length > 8 || $('#opDenoop').val().length > 4 ||
            $('#idPro').val().length > 0 || $('#idAct').val().length > 0) {
            $.ajax({
                url: url,
                type: "POST",
                data: {
                    denoop: $('#opDenoop').val(),
                    cif: $('#opCif').val(),
                    opreg: $("#opReg").val(),
                    idprod: $('#idPro').val(),
                    idAct: $('#idAct').val()

                },
                success: function (response) {
                    //$('#successSearchInfo').show("slow").text("Encontrados " + response.length + " registros.");
                    $('#errorSearch').hide();
                    if (response.length > 0) {
                        //var validRegisters = [];
                        //for (var i = 0; i < response.length; i++) {
                        //    if (response[i].opEst == 'C' && response[i].opTpex != 'P'
                        //        || response[i].opEst == 'P' && response[i].opTpex != 'P') {
                        //        validRegisters.push(response[i]);
                        //    }
                        //}

                        //if (validRegisters.length > 0) {
                        if (response.length > 0) {
                            fillTable(response);
                            //console.table(validRegisters);
                            $('#successSearchInfo').removeClass('hidden').show("slow").text("Encontrados " + response.length + " registros.");
                            $('#listoperators').show('slow');
                        } else {
                            $('#listoperators').hide('slow').find('tbody').html("");
                            $('#successSearchInfo').show("slow").html('<a href="mailto:sohiscert@sohiscert.com"><i class="fa fa-envelope-o"> </i></a>' + " Consulte con Sohiscert para más información.");
                        }
                    } else {

                        $('#listoperators').hide('slow').find('tbody').html("");
                        $('#resultados').removeClass('hidden').show('slow');
                        $('#successSearchInfo').removeClass("hidden").show("slow").text("Encontrados " + response.length + " registros.");
                    }
                },
                error: function (xhr, status) {
                    alert('Error desconocido ' + status);
                }
            });
        } else {
            //$('#errorSearch').show("slow").text("Debe introducir un CIF completo o una denominación de al menos 5 caracteres o elegir un producto.");
            $('#successSearchInfo').hide();
            $('#listoperators').hide('slow');
            $('#resultados').removeClass('hidden').show('slow');
            $('#errorSearch').show("slow").removeClass().addClass('callout callout-danger').text("Debe introducir un CIF completo o una denominación de al menos 5 caracteres o elegir un producto.");
        }

    });

    function fillTable(response) {
        var content;
        var url = $('#searchForm').attr("action");
        var url2 = $('#listoperators').data('client');

        for (var i = 0; i < response.length; i++) {
            content += '<tr><td>' + "<a href='" + url + "/" + response[i].id + "'>" + response[i].opNop + "</a>" + '</td><td>' +
                "<a href='" + url + "/" + response[i].id + "'>" + response[i].reDeno.toLowerCase() + "</a>" + "</td><td>" +
                "<form action='" + url2 + "' method='POST'>" +
                "<input type='hidden' value='" + response[i].opCif + "' name='userOperatorUsername'/>" +
                "<a class='client' href =''>" + response[i].opDenoop + "</a>" +
                "</form ></td>" +
                "<td>" + response[i].opEst + "</td><td>" + response[i].opTel + '</td><td>' + response[i].opCif + '</td></tr>';
        }
        
        $('#listoperators').find('tbody').html(content);
        $('#resultados').removeClass('hidden').show('slow');

    }

    $('#listoperators').on("click", 'a.client', function (e) {
        e.preventDefault();
        $(this).closest("form").submit();
    });

});