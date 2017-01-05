<?php
namespace Admin\Controller\Patient;

use Admin\Model\Patient\PatientModel;

use Admin\Controller\AdminController;
/**
 * 档案管理下的患者资料管理控制器
 * Class PatientController
 * @package Admin\Controller\Patient
 * @author jiaolele
 */
class PatientController extends AdminController{
    /**
     * 患者列表
     * @author jiaolele
     */
    public function index()
    {
        //添加查询条件
        $name = I('get.name');
        $family_id= I('get.family_id');
        $where = array();
        if($name!==''){
            $where['name'] = array('like','%'.$name.'%');
        }
        if($family_id!==''){
            $where['family_id'] = $family_id;
        }
        //查询列表信息
        $patientModel = new PatientModel();
        $list = $patientModel->getAdminList($where);
        //记录用户当前页面
        $this->getAuth();
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->assign('_list', $list['rows']);
        $this->assign('_page',$list['pageHtml']);
        $this->display();
    }


    /**
     * 新增患者
     * @author jiaolele
     */
    public function add(){
        $patientModel = new PatientModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $patientModel->addPatient($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$patientModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>U('index',array('key'=>'PATIENTLIST'))));
            }
        }else{
            $this->display();
        }
    }

    /**
     * 编辑数据
     * @param $id
     * @author jiaolele
     */
    public function edit($id){
        $patientModel = new PatientModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $patientModel->savePatient($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$patientModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>Cookie('__forward__')));
            }
        }else{
            //得到单个信息
            $case = $patientModel->find($id);
            $this->assign($case);
            $this->display('add');
        }
    }


    /**
     * 删除数据
     * @author jiaolele
     */
    public function remove($id,$file = '')
    {
        $patientModel = new PatientModel();
        $rst = $patientModel->remove($id,$file);
        if ($rst !== false) {
            $this->ajaxReturn(array('code' => 0));
        } else {
            $this->ajaxReturn(array('code' => 1, 'msg' => $patientModel->getError()));
        }
    }



}
