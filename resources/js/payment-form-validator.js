$(document).ready(function() {

	$("#b-form-checkout").validate({
		ignoreTitle: true,
		ignore: ":hidden",
		rules : {
			bcash_cpf : {
				required : true
			},
			'card-number' : {
				required : true
			},
			'card-installment' : {
				required : true
			},
			validade_mes_cartao : {
				required : true
			},
			validade_ano_cartao : {
				required : true
			},
			'card-owner-name' : {
				required : true
			},
			'card-security-code' : {
				required : true
			}
		},
		messages : {
			bcash_cpf : {
				required : "Campo cpf obrigatório."
			},
			'card-number' : {
				required : "Número do cartão obrigatório."
			},
			'card-installment' : {
				required : "Quantidade de parcelas obrigatório."
			},
			validade_mes_cartao : {
				required : "Mês de vencimento do cartão obrigatório."
			},
			validade_ano_cartao : {
				required : "Ano de vencimento do cartão obrigatório."
			},
			'card-owner-name' : {
				required : "Nome do titular do cartão obrigatório."
			},
			'card-security-code' : {
				required : "Código de segurança obrigatório."
			}
		},
		errorPlacement: function(error, element) {
			if (element.attr("name") == "card-installment" ) {
		        error.insertAfter(".parcelamentosCartao");
	     } else if (element.attr("name") == "validade_mes_cartao" || element.attr("validade_ano_cartao") ) {
	     		error.insertAfter("#validadeCartao");
	     } else {
			error.insertAfter(element);
	      }
		},
		 success: function(label) {
		 	if ($("#card-data").is(":visible") ) {
     			$('input:submit').attr("disabled", false);
     		}

  		}
	});

	$('#b-form-checkout').submit(function() {
		if($("#b-form-checkout").valid()) {
			$('.block-load').fadeIn();
			$('input:submit').attr("disabled", true);
		}
	});

});
