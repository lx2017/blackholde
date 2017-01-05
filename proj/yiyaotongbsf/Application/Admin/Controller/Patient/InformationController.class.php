<?php
namespace Admin\Controller\Patient;

use Admin\Model\Patient\InformationModel;
use Admin\Controller\AdminController;

class InformationController extends AdminController{
    /**
     * 资讯列表
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
        $informationModel = new InformationModel();
        $list = $informationModel->getAdminList($where);
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
        $informationModel = new InformationModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $informationModel->addInformation($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$informationModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>U('index',array('key'=>'INFORMATIONLIST'))));
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
        $informationModel = new InformationModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $informationModel->saveInformation($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$informationModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>Cookie('__forward__')));
            }
        }else{
            //得到单个信息
            $case = $informationModel->find($id);
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
        $informationModel = new InformationModel();
        $rst = $informationModel->remove($id,$file);
        if ($rst !== false) {
            $this->ajaxReturn(array('code' => 0));
        } else {
            $this->ajaxReturn(array('code' => 1, 'msg' => $informationModel->getError()));
        }
    }
}