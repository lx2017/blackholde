<?php
/**
 * 系统消息管理
 * Created by PhpStorm.
 * User: dower
 * Date: 2016/7/26 0026
 * Time: 下午 15:31
 */
namespace Admin\Controller\Doctor;

use Admin\Model\Doctor\SystemMessageModel;
use Admin\Controller\AdminController;
class SystemMessageController extends AdminController{

    /**
     * 系统消息列表
     */
    public function index()
    {
        //添加查询条件
        $where = array('accept_id'=>0);
        //查询列表信息
        $feedbackModel = new SystemMessageModel();
        $list = $feedbackModel->pageList($where);
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
        $model = new SystemMessageModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $model->addData($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$model->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>U('index',array('key'=>'SYSTEMMESSAGELIST'))));
            }
        }else{
            $this->display();
        }
    }

    /**
     * 编辑数据
     */
    public function edit($id){
        $model = new SystemMessageModel();
        if(IS_POST){
            $params = I('post.');
            $rst = $model->saveData($params);
            if($rst===false){
                $this->ajaxReturn(array('code'=>1,'msg'=>$model->getError()));
            }else{
                $this->ajaxReturn(array('code'=>0,'url'=>Cookie('__forward__')));
            }
        }else{
            //得到单个信息
            $case = $model->find($id);
            $this->assign($case);
            $this->display('add');
        }
    }


    /**
     * 删除数据(真删除)
     * @param $id
     * @param string $file 数据库中的文件字段名,多个逗号分隔
     */
    public function remove($id,$file = '')
    {
        $model = new SystemMessageModel();
        $rst = $model->remove($id,$file);
        if ($rst !== false) {
            $this->ajaxReturn(array('code' => 0));
        } else {
            $this->ajaxReturn(array('code' => 1, 'msg' => $model->getError()));
        }
    }

    /**
     * 修改数据状态(可用于伪删除)
     * @param $id int
     * @param $value mixed 修改后的状态值
     */
    public function changeStatus($id,$value){
        $model = new SystemMessageModel();
        $rst = $model->changeStatus($id,$value);
        if ($rst !== false) {
            $this->ajaxReturn(array('code' => 0));
        } else {
            $this->ajaxReturn(array('code' => 1, 'msg' => $model->getError()));
        }
    }
}