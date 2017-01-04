<?php

class Member extends Base
{

	public function index()
	{
		require_once(HOLDE_ROUTE."DB.php");
		//$url = "mysql://username:passwd@localhost:3306/DbName?param1=val1&param2=val2#utf8";
		$data=DB::getConfig();
		$where =  array(
               'name'=>'jack'
			);
	   $data->where($where)->join("JOIN acd name on a.id=b.id")->join("JOIN jkjk on a.id=b.id")->where_filter();
	}
}
?>