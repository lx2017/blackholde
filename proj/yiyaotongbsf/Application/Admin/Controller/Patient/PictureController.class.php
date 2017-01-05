<?php
namespace Admin\Controller\Patient;

use Admin\Model\Patient\PictureModel;
use Admin\Controller\AdminController;

class PictureController extends AdminController{
    /**
     * 患者端轮播图列表
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
        $pictureModel = new PictureModel();
        $list = $pictureModel->getAdminList($where);
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
        $pictureModel = new PictureModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $pictureModel->addPicture($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$pictureModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>U('index',array('key'=>'PICTURELIST'))));
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
        $pictureModel = new PictureModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $pictureModel->savePicture($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$pictureModel->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>Cookie('__forward__')));
            }
        }else{
            //得到单个信息
            $case = $pictureModel->find($id);
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
        $pictureModel = new PictureModel();
        $rst = $pictureModel->remove($id,$file);
        if ($rst !== false) {
            $this->ajaxReturn(array('code' => 0));
        } else {
            $this->ajaxReturn(array('code' => 1, 'msg' => $pictureModel->getError()));
        }
    }
}