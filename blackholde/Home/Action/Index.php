<?php
class Index extends Base
{
      public  $a;
     
	  public function add()
	  {

	  	   echo "hello add function...";
	  }
	 
	  public function delete()
	  {
            $this->assign('name','hello');
            $this->assign('age',20);
            $this->display("a");
	  }
	 
	  
}
?>