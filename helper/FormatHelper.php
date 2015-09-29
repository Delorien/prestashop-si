<?php

class FormatHelper
{
	static public function monetize($price) 
	{
		return number_format(Tools::ps_round($price, 2), 2, '.', '');
	}

}
