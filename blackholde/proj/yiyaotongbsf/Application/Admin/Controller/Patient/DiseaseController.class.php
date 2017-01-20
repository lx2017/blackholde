<?php
namespace Admin\Controller\Patient;

use Admin\Model\Patient\DiseaseModel;
use Admin\Controller\AdminController;

class DiseaseController extends AdminController{
    /**
     * 疾病库列表
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
        $diseaseModel = new DiseaseModel();
        $list = $diseaseModel->getAdminList($where);
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
        $diseaseModel = new DiseaseModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $diseaseModel->addDisease($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$diseaseModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>U('index',array('key'=>'DISEASELIST'))));
            }
        }else{
            $keshi = M('DiseaseCategary')->field('id,type_name')->where(array('category'=>0))->select();//查询所有科室分类
            $renqun= M('DiseaseCategary')->field('id,type_name')->where(array('category'=>1))->select();//查询所有人群分类
            $this->assign('keshi',$keshi);
            $this->assign('renqun',$renqun);
            $this->display();
        }
    }

    /**
     * 编辑数据
     * @param $id
     * @author jiaolele
     */
    public function edit($id){
        $diseaseModel = new DiseaseModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $diseaseModel->saveDisease($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$diseaseModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>Cookie('__forward__')));
            }
        }else{
            //得到单个信息
            $case = $diseaseModel->find($id);
            $keshi = M('DiseaseCategary')->field('id,type_name')->where(array('category'=>0))->select();//查询所有科室分类
            $renqun= M('DiseaseCategary')->field('id,type_name')->where(array('category'=>1))->select();//查询所有人群分类
            $this->assign('keshi',$keshi);
            $this->assign('renqun',$renqun);
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
        $diseaseModel = new DiseaseModel();
        $rst = $diseaseModel->remove($id,$file);
        if ($rst !== false) {
            $this->ajaxReturn(array('code' => 0));
        } else {
            $this->ajaxReturn(array('code' => 1, 'msg' => $diseaseModel->getError()));
        }
    }
}