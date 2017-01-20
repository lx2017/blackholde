<?php
namespace Admin\Controller\Patient;

use Admin\Model\Patient\ActivityModel;
use Admin\Controller\AdminController;

class ActivityController extends AdminController{
    /**
     * 活动列表
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
        $activityModel = new ActivityModel();
        $list = $activityModel->getAdminList($where);
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
        $activityModel = new ActivityModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $activityModel->addDate($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$activityModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>U('index',array('key'=>'ACTIVITYLIST'))));
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
        $activityModel = new ActivityModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $activityModel->saveDate($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$activityModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>Cookie('__forward__')));
            }
        }else{
            //得到单个信息
            $case = $activityModel->find($id);
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
        $activityModel = new ActivityModel();
        $rst = $activityModel->remove($id,$file);
        if ($rst !== false) {
            $this->ajaxReturn(array('code' => 0));
        } else {
            $this->ajaxReturn(array('code' => 1, 'msg' => $activityModel->getError()));
        }
    }
}