<?php

namespace Admin\Controller\Good;

use Admin\Model\Good\LableModel;

class NRLableController extends \Think\Controller
{
	public function checkLable()
	{
		$lableName = I('Lablename');
		if($lableName){
			$lableModel = new LableModel();
			$count = $lableModel->countByLableName($lableName);
			if($count == 0){
				echo json_encode(true);
			}else{
				echo json_encode(false);
			}
		}
	}
}