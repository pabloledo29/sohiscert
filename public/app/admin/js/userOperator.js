$(document).ready(function () {


    $('#refresh').click(function (e) {

        e.preventDefault();

        //console.log('actualizar');

        var id = $('#refreshForm').find('input[name="id"]').val();
        var url = $('#refreshForm').attr('action');

        //console.log(id + ' ' + url + ' ');


        if (id !== null) {

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    id: id,
                },
                success: function (response) {

                    if (response.type == "success") {
                        var client = JSON.parse(response.client);
                        $("#deno").html(client.cl_deno);
                        $('#address').html(client.cl_dom + '<br>' + client.cl_pob + ', '
                            + client.cl_prov + '<br>' + client.cl_cdp);
                        $('#country').html(client.cl_pais);
                        $('#tel').html(client.cl_tel);
                        $('#fax').html(client.cl_fax);
                        $('#email').html(client.cl_email);

                        if (client.updated_date !== undefined) {
                            var fecha = client.created_date.slice(0, client.created_date.indexOf('T'));
                            fecha = fecha.split('-');
                            fecha = fecha[2] + '/' + fecha[1] + '/' + fecha[0];
                            $('#updtime').html(fecha);
                        } else {
                            var fecha = client.created_date.slice(0, client.created_date.indexOf('T'));
                            fecha = fecha.split('-');
                            fecha = fecha[2] + '/' + fecha[1] + '/' + fecha[0];
                            $('#updtime').html(fecha);
                        }

                        fillTable(JSON.parse(response.operators));
                        $("#updMsg p").html('Datos actualizados correctamente.');
                        $("#updMsg").show().removeClass().addClass('callout callout-success');

                    } else if (response.type == 'error') {
                        $("#updMsg p").html('No se pueden actualizar los datos en este momento.');
                        $("#updMsg").show().removeClass().addClass('callout callout-danger');
                    }
                },
                error: function (xhr, status) {

                    alert('Error desconocido ' + status);
                }

            });
        }

    });

    function fillTable(operators) {
        var content = '';
        var url = "/admin/expediente/show";
        var aurl = $('a.showOp');
        if (aurl.length > 0) {
            url = aurl.attr('href');
            url = url.slice(0, url.lastIndexOf('/'));
        }

        for (var i = 0; i < operators.length; i++) {

            content += '<tr><td>' + "<a href='" + url + "/" + operators[i].id + "'>" + operators[i].op_nop + "</a>" + '</td><td>' +
                operators[i].op_registro.re_deno + '</td></tr>';
        }

        $('#listoperators tbody').html(content);

    }

});