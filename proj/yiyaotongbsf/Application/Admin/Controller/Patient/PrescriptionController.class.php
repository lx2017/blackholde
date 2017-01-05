<?php
namespace Admin\Controller\Patient;

use Admin\Model\Patient\PrescriptionModel;
use Admin\Controller\AdminController;

class PrescriptionController extends AdminController{
    /**
     * 明方验方类型列表
     * @author jiaolele
     */
    public function index()
    {
        //添加查询条件
        $name = I('get.name');
        $where = array();
        if($name!==''){
            $where['name'] = array('like','%'.$name.'%');
        }
        //查询列表信息
        $prescriptionModel = new PrescriptionModel();
        $list = $prescriptionModel->getAdminList($where);
        //记录用户当前页面
        $this->getAuth();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->assign('list', $list['rows']);
        $this->assign('page',$list['pageHtml']);
        $this->display();
    }


    /*  * 新增
     * @author jiaolele
     */
    public function add(){
        $prescriptionModel = new PrescriptionModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $prescriptionModel->addPrescription($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$prescriptionModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>U('index',array('key'=>'PRESCRIPTIONLIST'))));
            }
        }else{
            $chufang = M('PrescriptionCategary')->field('id,prescription_type_name')->select();//查询所有处方类别
            $this->assign('chufang',$chufang);
            $this->display();
        }
    }

    /**
     * 编辑数据
     * @param $id
     * @author jiaolele
     */
    public function edit($id){
        $prescriptionModel = new PrescriptionModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $prescriptionModel->savePrescription($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$prescriptionModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>Cookie('__forward__')));
            }
        }else{
            //得到单个信息
            $case = $prescriptionModel->find($id);
            $this->assign($case);
            $chufang = M('PrescriptionCategary')->field('id,prescription_type_name')->select();//查询所有处方类别
            $this->assign('chufang',$chufang);
            $this->display('add');
        }
    }


    /**
     * 删除数据
     * @author jiaolele
     */
    public function remove($id,$file = '')
    {
        $prescriptionModel = new PrescriptionModel();
        $rst = $prescriptionModel->remove($id,$file);
        if ($rst !== false) {
            $this->ajaxReturn(array('code' => 0));
        } else {
            $this->ajaxReturn(array('code' => 1, 'msg' => $prescriptionModel->getError()));
        }
    }
}