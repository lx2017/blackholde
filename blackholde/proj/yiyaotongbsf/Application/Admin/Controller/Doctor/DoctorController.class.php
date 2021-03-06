<?php
/**
 * 医生管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:31
 */
namespace Admin\Controller\Doctor;

use Admin\Model\Doctor\DoctorModel;
use Admin\Controller\AdminController;
class DoctorController extends AdminController{

    /**
     * 医生列表
     */
    public function index()
    {
        //添加查询条件
        $name = I('get.name');
        $where = array();
        if($name!==''){
            $where['a.name'] = array('like','%'.$name.'%');
        }
        //查询列表信息
        $DoctorModel = new DoctorModel();
        $list = $DoctorModel->pageList($where);
        //记录用户当前页面
        $this->getAuth();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->assign('list', $list['rows']);
        $this->assign('page',$list['pageHtml']);
        $this->assign('upload_conf',C('UPLOADIFY_EXCEL_CONF'));
        $this->display();
    }


    /**
     * 新增数据
     */
    public function add(){
        $DoctorModel = new DoctorModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $DoctorModel->addData($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$DoctorModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>U('index',array('key'=>'DoctorLIST'))));
            }
        }else{
            $this->display();
        }
    }

    /**
     * 编辑数据
     */
    public function edit($id){
        $DoctorModel = new DoctorModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $DoctorModel->saveData($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$DoctorModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>Cookie('__forward__')));
            }
        }else{
            //得到单个信息
            $case = $DoctorModel->find($id);
            //处理图片
            if($case){
                $temp = explode(',',$case['licence']);
                $case['imgs'] = array_filter($temp);
            }
            $this->assign($case);
            $this->display('add');
        }
    }

    /**
     * 修改数据状态(可用于伪删除)
     * @param $id int
     * @param $value mixed 修改后的状态值
     */
    public function changeStatus($id,$value){
        $DoctorModel = new DoctorModel();
        $rst = $DoctorModel->changeStatus($id,$value);
        if ($rst !== false) {
            $this->ajaxReturn(array('code' => 0));
        } else {
            $this->ajaxReturn(array('code' => 1, 'msg' => $DoctorModel->getError()));
        }
    }

}