$(document).ready(function() {

	$('#bcash .bandeira input[type=radio]').each(function() {

		var radio = $('input[type=radio][id=' + this.id + ']');

		radio.change(function() {

			resetAllLabels();

			radio.parent().addClass("checked");

			showCardForm(this);
		});
	});

	function resetAllLabels() {
		$('.bandeira').each(function() {
			$(this).removeClass('checked');

		});
	};

	function resetAllFields() {
		$('#card-data').find('input:text').val('');
		$('#card-data').find('input:radio').attr('checked', false);
		$('#validade_ano_cartao').prop('selectedIndex',0);
		$('#validade_mes_cartao').prop('selectedIndex',0);

		var validator = $( "#b-form-checkout" ).validate();
		validator.resetForm();
	};

	function showCardForm(radio) {
		if ($('#' + radio.id, '#credit_list').length == 1) {

			$('#card-data').show('slow');

			$('.card-installments').hide('fast');
			$('#card-installment-' + radio.value).show('fast');

			$('.b-button-sucess').hide('fast');
			$('#b-button-sucess-credit').show('fast');

			$('.bcash-termos').hide('fast');
			$('#b-termos-credit').show('fast');

			if (radio.id == 'payment-method-45' ) {
				$('.codigo-seguranca').hide('fast');
			} else if (radio.id == 'payment-method-37') {
				$('#card-security-code').attr('maxlength', '4');
				$('.codigo-seguranca').show('fast');
			} else {
				$('#card-security-code').attr('maxlength', '3');
				$('.codigo-seguranca').show('fast');
			}

		} else {
			$('#card-data').hide('slow');

			resetAllFields();

			if ($('#' + radio.id, '#tef_list').length == 1) {
				$('.b-button-sucess').hide('fast');
				$('#b-button-sucess-tef').show('fast');

				$('.bcash-termos').hide('fast');
				$('#b-termos-tef').show('fast');
			}else {
				$('.b-button-sucess').hide('fast');
				$('#b-button-sucess-bankslip').show('fast');

				$('.bcash-termos').hide('fast');
				$('#b-termos-bankslip').show('fast');
			}
		}
	};

	$('#card-security-code').keypress(function() {
  		return event.charCode >= 48 && event.charCode <= 57;
	});

	$('#card-number').keypress(function() {
  		return event.charCode >= 48 && event.charCode <= 57;
	});

	$('#card-owner-name').keypress(function(event) {
		if (event.which < 48 || 
	    	(event.which > 57 && event.which < 65) || 
	    	(event.which > 90 && event.which < 97) ||
	    	event.which > 122) {
	    	event.preventDefault();
		}
	});
});
//document.ready

$(window).load(function() {
	if ($.isFunction($.uniform.restore)) {
		$.uniform.restore(".noUniform");
	}
}); 