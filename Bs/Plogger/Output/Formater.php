<?php
class Bs_Plogger_Output_Formater
{
	public function format($params)
	{
		$msg = "[".$params["id"]."::".$params["file"]."::{$params["line"]}][{$params["time"]}]".$params["msg"]."\n";
		return $msg;
	}
}
?>