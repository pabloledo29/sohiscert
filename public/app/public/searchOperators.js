$(document).ready(function() {

	$('#searchForm').submit(function(e){
    	e.preventDefault();
  	});

	$('#searchOperators').click(function(){
      	
      	var url = $('#searchForm').attr("action");
      	//var url = $('#searchOperators').attr("href");
      	$('#errorSearch').css("display","none");
      	$('#successSearch').css("display","none");

      	// console.log($('#opCif').val());
      	$( "#opReg" ).val();
      	
      	if( $('#opCif').val().length > 8 || $('#opDenoop').val().length > 4 || $('#idPro').val().length > 0){
	      	$.ajax({
	        	url: url,
	        	type: "POST",
	        	data: {
	        		denoop: $('#opDenoop').val(),
	        		cif: $('#opCif').val(),
	        		opreg: $( "#opReg" ).val(),
	        		idprod: $('#idPro').val(),	        		
	        	},
		        success: function(response) { 
		        	//$('#successSearchInfo').show("slow").text("Encontrados " + response.length + " registros.");
		        	$('#errorSearch').hide();

			        if(response.length > 0){
			    		//console.table(response);
			        	var validRegisters = new Array();
			        	for(var i = 0; i < response.length; i++){
			        		if(response[i].opEst == 'C' && response[i].opTpex != 'P'){
			        			validRegisters.push(response[i]);
			        		}
			        	}

			        	if(validRegisters.length > 0){
			        		fillTable(response);
				        	//console.table(validRegisters);
				        	$('#successSearchInfo').show("slow").text("Encontrados " + validRegisters.length + " registros.");
				        	$('#listoperators').show('slow');
				        }else{
				        	$('#listoperators').hide('slow');
			    			$('#listoperators tbody').html("");
			    			//$('#successSearchInfo').show("slow").text("Consulte con Sohiscert para más información.");
			    			//$('#successSearchInfo').show("slow").html('<i class="fa fa-envelope"><a href=""</i>'+" Consulte con Sohiscert para más información.");
			    			$('#successSearchInfo').show("slow").html('<a href="mailto:sohiscert@sohiscert.com"><i class="fa fa-envelope-o"> </i></a>'+" Consulte con Sohiscert para más información.");

				        }
			    	}else{
			    		$('#successSearchInfo').show("slow").text("Encontrados " + response.length + " registros.");
			    		$('#listoperators').hide('slow');
			    		$('#listoperators tbody').html("");
			    	}
		        }, 
		        error: function (xhr, status) {  
		          alert('Error desconocido ' + status); 
		        }  
	    	});
		} else {
			$('#errorSearch').show("slow").text("Debe introducir un CIF completo o una denominación de al menos 5 caracteres o elegir un producto.");
			$('#successSearchInfo').hide();
			$('#listoperators').hide('slow');
			//console.log('Debe rellenar al menos uno los campos');
		}

    });

function fillTable(response){
	var content;
	var url = $('#searchForm').attr("action");

	for(var i = 0; i< response.length; i++){
		
		// console.log(response[i].codigo);

		content+= '<tr><td>' + "<a href='" + url + "/" + response[i].id + "'>"+ response[i].opNop + "</a>"+ '</td><td>' + 
		"<a href='" + url + "/" + response[i].id + "'>" + response[i].reDeno.toLowerCase()+"</a>" + '</td><td>' + 
		"<a href='" + url + "/" + response[i].id + "'>" + response[i].opDenoop.toLowerCase()+"</a>" + '</td><td>' + 
		response[i].opCif + '</td></tr>';
	}

	$('#listoperators tbody').html(content);

}

});