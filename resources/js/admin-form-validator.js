$(document).ready(function() {

	jQuery.validator.addMethod("tokenDifereConsumerKey", function(value, element) {
		return $("input[name=consumer_key]").val() != $("input[name=bcash_token]").val();
	}, "Consumer key e Token não podem ser iguais.");

	jQuery.validator.addMethod("isPercentValid", function(value, element) {
		var regexp = /^(\d{1,2})$|(^(\d{1,2})(\.|\,)\d{1}$)|^$/;
		return regexp.test(value);

	}, "Formato inválido. (Ex.:10.0)");

	$("#adminform").validate({
		ignoreTitle: true,
		rules : {
			email : {
				required : true,
				email : true
			},
			consumer_key : {
				required : true,
				tokenDifereConsumerKey : true
			},
			bcash_token : {
				required : true,
				tokenDifereConsumerKey : true
			},
			desconto_boleto : {
				isPercentValid : true
			},
			desconto_tef : {
				isPercentValid : true
			},
			desconto_credito : {
				isPercentValid : true
			},
			campo_cpf : {
				required : true
			},
			campo_cpf_select : {
				required : true
			},
			tableAjax : {
				required : true
			}
		},
		messages : {
			email : {
				required : "Campo email é obrigatório",
				email : "Informe um email válido."
			},
			consumer_key : {
				required : "Campo consumer key é obrigatório"
			},
			bcash_token : {
				required : "Campo token é obrigatório"
			},
			campo_cpf : {
				required : "Selecione como o módulo deve tratar o cpf."
			},
			campo_cpf_select : {
				required : "Selecione a tabela e o campo para usar como cpf."
			},
			tableAjax : {
				required : "Selecione a tabela e o campo para usar como cpf."
			}
		}
	});


	function mvalor(v){
	    v=v.replace(/\D/g,"");//Remove tudo o que não é dígito
	    v=v.replace(/^(\d{1})(\d{1})(\d{1})(\d)$/,"$1$2$3");//coloca a virgula antes dos 2 últimos dígitos
		v=v.replace(/^(\d{1,2})(\d{1})$/,"$1.$2");//coloca a virgula antes dos 2 últimos dígitos
	    return v;
	}
	$('input[name=desconto_boleto]').keypress(function(){
		setTimeout(
			function(){
				$('input[name=desconto_boleto]').val(mvalor($('input[name=desconto_boleto]').val()));
			},1);
	});
	$('input[name=desconto_tef]').keypress(function(){
		setTimeout(
			function(){
				$('input[name=desconto_tef]').val(mvalor($('input[name=desconto_tef]').val()));
			},1);
	});
	$('input[name=desconto_credito]').keypress(function(){
		setTimeout(
			function(){
				$('input[name=desconto_credito]').val(mvalor($('input[name=desconto_credito]').val()));
			},1);
	});

	$('input[name=sandbox]').change(function(){
	     if($(this).attr('checked')){
	          $(this).val('TRUE');
	     }else{
	          $(this).val('FALSE');
	     }
	});

	$('input[name=campo_cpf]').change(function(){
	    var value = $( this ).val();

		if (value == 'specified') {
			$('#cpf-spec').show('slow');
		} else {
			$('#tableAjax').val('');
			$('#cpf-spec').hide('slow');
			$(".select-column").hide('slow');
			$('#campo_cpf_select').find('option').remove()
			$('#where_cpf_select').find('option').remove()
		}
	});

});
