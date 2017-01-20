<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2013 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Common\Behavior;
use Think\Behavior;
use Think\Hook;
defined('THINK_PATH') or exit();

// 初始化钩子信息
class InitHookBehavior extends Behavior {

    // 行为扩展的执行入口必须是run
    public function run(&$content){
        if(isset($_GET['m']) && $_GET['m'] === 'Install') return;
        
        $data = S('hooks');
        if(!$data){
            $hooks = M('HooksAddons')->field('hook_name,addon_name')->select();
            foreach ($hooks as  $value) {
                if($value){
                    $map['status']  =   1;
                    $map['name']    =   $value['addon_name'];
                    $data = M('Addons')->where($map)->getField('name');
                    if($data){
//                        $addons = array_intersect($names, $data);
                        Hook::add($value['hook_name'],$data);
                    }
                }
            }
            S('hooks',Hook::get());
        }else{
            Hook::import($data,false);
        }
    }
}