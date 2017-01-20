<?php
/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/6/30
 * Time: 下午6:25
 */

namespace Home\Controller\Test;


use Think\Controller;
use Home\Model\SaleManager\County\CountyModel;


class TestController extends Controller
{

    public function index()
    {
        $countryMod = new CountyModel();
        $data = array(
        		'description'=>'测试描述1',
        		'user_id'=>'1',
        		'status'=>1,
        );
        $lists = $countryMod->add($data);
        print_r($lists);
    }

    public function test(){
        $rst = D('Huanxin')->test();
        dump($rst);
    }
}