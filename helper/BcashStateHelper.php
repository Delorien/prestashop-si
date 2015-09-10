<?php

class BcashStateHelper
{
	public static function getStateAbbreviation($state) {

		$estados = array(
			"acre" => "AC",
			"alagoas" => "AL",
			"amazonas" => "AM",
			"amapa" => "AP",
			"bahia" => "BA",
			"ceara" => "CE",
			"distrito_federal" => "DF",
			"espirito_santo" => "ES",
			"goias" => "GO",
			"maranhao" => "MA",
			"mato_grosso" => "MT",
			"mato_grosso_do_sul" => "MS",
			"minas_gerais" => "MG",
			"para" => "PA",
			"paraiba" => "PB",
			"parana" => "PR",
			"pernambuco" => "PE",
			"piaui" => "PI",
			"rio_de_janeiro" => "RJ",
			"rio_grande_do_norte" => "RN",
			"rondonia" => "RO",
			"rio_grande_do_sul" => "RS",
			"roraima" => "RR",
			"santa_catarina" => "SC",
			"sergipe" => "SE",
			"sao_paulo" => "SP",
			"tocantins" => "TO"
		);

 		$state = strtolower($state);

 		switch ($state) {
 			case "amapá":
 				return "AP";
 			case "são paulo":
 				return "SP";
 			case "ceará":
 				return "CE";
 			case "espírito santo":
 				return "ES";
 			case "goiás":
 				return "GO";
 			case "maranhão":
 				return "MA";
 			case "pará":
 				return "PA";
 			case "paraíba":
 				return "PB";
 			case "paraná":
 				return "PR";
 			case "piauí":
 				return "PI";
			case "rondônia":
				return "RO";
 		}

 		$state = str_replace(" ", "_", $state);
		$state = $estados[$state];

		return $state;
	}
}
