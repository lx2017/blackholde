<?php

class Member extends Base
{

	public function index()
	{
		$this->display("index");
	}
	public function test()
	{
		$test = array(1,2,3,4);
		$this->assign("hello",$test);
		$this->display("index");
	}
}
?>