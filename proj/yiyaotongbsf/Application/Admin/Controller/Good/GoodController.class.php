<?php

namespace Admin\Controller\Good;

// use Admin\Model\Employee\MemberModel;
// use Admin\Model\Employee\UcenterMemberModel;
// use UserMember\Api\UserMemberApi;
use Admin\Model\Good\LableModel;
use Admin\Model\Good\GoodModel;
use Admin\Model\Good\ClassifyModel;
use Admin\Controller\AdminController;

class GoodController extends AdminController
{
	private $model;
	public function __construct(){
		parent::__construct();
		$this->model=new GoodModel();
	}
	public function index(){
		$goodModel = new GoodModel();
		$lists = $goodModel->lists();
		$goodClassiry = new ClassifyModel();
		$goodLable = new LableModel();
		$key=I('key');
		$actions = $this->getMenuActions($key);
        $actionInfos = [];
        foreach ($actions as $action) {
            $actionInfos[$action['key']] = $action;
        }
		foreach($lists as &$list){
			$classifys = $list['classify'];
			$classifys = explode(",", $classifys);
			$list['classify']='';
			foreach($classifys as &$classify)
			{	
				$condition = array('id' => $classify);
				if($classify)
				{
					$list['classify'] = $list['classify'].$goodClassiry->find($condition).'/';
				}
			}
			$list['classify'] = mb_substr($list['classify'],0,-1);
			$lables = $list['lable'];
			$lables = explode(",", $lables);
			$list['lable'] = '';
			foreach($lables as $lable){
				$condition = array('id' => $lable);
				if($lable){
					$list['lable'] = $list['lable'].$goodLable->find($condition).'/';
				}
			}
			$list['lable'] = mb_substr($list['lable'], 0,-1);
		}
		foreach($lists as $k=>$v){
			$lists[$k]['pic']="http://".$_SERVER['HTTP_HOST'].$v['pic'];
		}
		//dump($lists);die;
		$this->assign('actions', $actionInfos);
		$this->assign('_list', $lists);
        $this->display();
	}

	public function delete()
	{
		$id = array_unique((array)I('id', 0));
		$id = is_array($id) ? implode(',', $id) : $id;
		if (empty($id)) {
			$this->error('请选择要操作的数据!');
		}
		$map['id'] = array('in',explode(',', $id));
		$changeState = new GoodModel();
		$result = $changeState->deleteGoods($map);
		if(false !== $result){
			$this->success('success');
		}else{
			$this->error('系统异常，联系系统管理员');
		}
	}

	public function add()
	{
		$key=I('key');
		$actions = $this->getMenuActions($key);
        $actionInfos = [];
        foreach ($actions as $action) {
            $actionInfos[$action['key']] = $action;
        }
		//获得顶级分类
		$model=new ClassifyModel();
		$top=$model->classify_1st();
		//dump($top);die;
		//获得所有标签
		$lableModel=new LableModel();
		$lableData=$lableModel->lists();
		$this->assign('top', $top);
		$this->assign('lableData', $lableData);
		$this->assign('actions', $actionInfos);
		//dump($actionInfos);die;
        $this->display();
	}
	/**
	 * 新增商品--异步获得分类操作
	 * */
	public function getClassify()
	{
		$id=I("post.id",0,'intval');
		if(!$id){
			$this->error("参数不正确");exit;
		}
		//获得下一级分类
		$model=new ClassifyModel();
		$botData=$model->getBottom($id);
		if($botData){
			$this->ajaxReturn($botData);
		}else{
			$this->ajaxReturn("empty");
		}
		//dump($botData);die;
	}
	/**
	 * 新增商品--添加商品
	 * */
	public function addGoods()
	{
		//$data=I("post.");()
		$data=array();
		$gid=I("post.gid",0,'intval');
		$name=I("post.name");
		$price=I("post.price");
		$pic=I("post.pic");
		$lable=implode(I("post.lable"), ',');
		$s1=I("post.s1",0,'intval');
		$s2=I("post.s2",0,'intval');
		$s3=I("post.s3",0,'intval');
		$content=I("post.content");
		if($s3){
			$data['classify']=$s3;
		}elseif(!$s3  and $s2){
			$data['classify']=$s2;
		}elseif(!$s3 and !$s2 and $s1){
			$data['classify']=$s1;
		}
		
		$data['gid']=$gid;
		$data['name']=$name;
		$data['price']=$price;
		$data['lable']=$lable;
		$data['content']=$content;
		$data['pic']=$pic;
		$data['state']=-1;
		if($this->model->addData($data)){
			$this->ajaxReturn("ok");
		}
	}
	/**
	 * uploadify上传
	 */
	public function uploadify(){
		
	     $upload = new \Think\Upload();
		 is_dir('./Goods/' . date('Y-m-d'))  || mkdir('./Goods/' . date('Y-m-d'),0777,true);
		
		 $dir='Uploads/Goods/'.date('Y-m-d');
	  	 $upload->savePath  = './Goods/'; // 设置附件上传（子）目录
	  	 $file = $upload->upload();
		 
		 if (empty($file)) {
		 	$this->ajaxReturn("上传失败");
		 } else {
		 	//编辑上传新图片时，原来的图片删除
		 	//来这个地方可以缩略图处理生成缩略图,按照原图的比例生成一个最大为50*50的缩略图并保存为thumb.jpg
			$image = new \Think\Image();
			$a=$image->open("./".$dir."/".$file['Filedata']['savename']);
			$a=$image->thumb(50, 50)->save("./".$dir."/"."Thumb_".$file['Filedata']['savename']);
			//$file['Filedata']['thumb']="http://".$_SERVER['HTTP_HOST'].__ROOT__."/".$dir."/"."Thumb_".$file['Filedata']['savename'];
			//$file['Filedata']['url']="http://".$_SERVER['HTTP_HOST'].__ROOT__."/".$dir."/".$file['Filedata']['savename'];
			//$file['Filedata']['savepath']="./Uploads".ltrim($file['Filedata']['savepath'],'.').$file['Filedata']['savename'];
			
			$file['Filedata']['show']=__ROOT__."/".$dir."/"."Thumb_".$file['Filedata']['savename'];
			$file['Filedata']['thumb']="./".$dir."/"."Thumb_".$file['Filedata']['savename'];
			
			$file['Filedata']['url']=__ROOT__."/".$dir."/".$file['Filedata']['savename'];
			$file['Filedata']['savepath']="./Uploads".ltrim($file['Filedata']['savepath'],'.').$file['Filedata']['savename'];
			
		 	/*foreach($files as $k=>$v){
		 		$files[$k]['Filedata']['url']="http://".$_SERVER['HTTP_HOST'].__ROOT__."/".$dir."/".$v['Filedata']['savename'];
		 	}*/
		 	$data = $file['Filedata'];
		 	
			$this->ajaxReturn($data);
		 }
	}
	/**
	 * 异步删除图片
	 */
	public function delImg(){
	    $path = I('post.path');
		$thumb = I('post.thumb');
		//删除图片
		unlink($path);
		unlink($thumb);
	}
	/**
	 * 异步删除图片--删除原来的
	 */
	public function delImg2(){
		if(IS_AJAX){
		$thumb = I('post.thumb');
		$gid = I('post.gid',0,'intval');
		$thumb=ltrim($thumb,'/');
		$num=strpos($thumb,'/');
		$new=substr($thumb,$num);
		$new='.'.$new;
		//删除图片
		/*dump($new);
		dump($gid);die;*/
		
		if(unlink($new)){
			$this->model->setOneField($gid,'pic','');
		}
		}
	}
	/**
	 * 显示商品更改分类页面
	 * */
	public function changeClassify()
	{
		$key=I('key');
		$actions = $this->getMenuActions($key);
        $actionInfos = [];
        foreach ($actions as $action) {
            $actionInfos[$action['key']] = $action;
        }
		$this->assign('actions', $actionInfos);
		$id=I("get.id",0,'intval');
		//获得对应商品的数据
		$data=$this->model->getOne($id);
		//获得原有数据的顶级分类
		$clamodel=new ClassifyModel();
		$top=$clamodel->classify_1st();
		//dump($actionInfos);die;
		$this->assign('data', $data);
		$this->assign('top', $top);
		/*dump($data);
		dump($id);die;*/
		$this->display('changeClassify');
	}
	/**
	 * 页面传过来分类父级id获得他的下级
	 * */
	public function gotBottom()
	{
		
		$id=I("post.id",0,'intval');
		$clamodel=new ClassifyModel();
		$data=$clamodel->getBottom($id);
		if(!$data){
			$data=0;
		}
		//dump($data);die;
		$this->ajaxReturn($data);
	}
	/**
	 * 商品确定更改分类
	 * */
	public function realAdd()
	{
		
		$id=I("post.id",0,'intval');
		$gid=I("post.gid",0,'intval');
		/*dump($gid);
		dump($id);die;*/
		//dump($data);die;
		if($this->model->where("id=".$gid)->setField("classify",$id)){
			$this->ajaxReturn('ok');
		}else{
			$this->ajaxReturn('fail');
		}
		
	}
	public function edit()
	{
		$key=I('key');
		$actions = $this->getMenuActions($key);
        $actionInfos = [];
        foreach ($actions as $action) {
            $actionInfos[$action['key']] = $action;
        }
		$this->assign('actions', $actionInfos);
		$id = I('id');
		$goodModel = new GoodModel();
		$goodInfo = $goodModel->findRow($id);
		
		$goodClassiry = new ClassifyModel();
		$goodLable = new LableModel();
		//获得所有标签
		$lableData=$goodLable->lists();
		//获得商品原来标签id
		$yuanLable=$goodInfo['lable'];
		$yuanLable=explode(',', $yuanLable);
		/*dump($goodInfo);
		dump($lableData);
		die;*/
		//获得顶级分类
		$model=new ClassifyModel();
		$top=$model->classify_1st();
		//dump($top);die;
		
		$this->assign('top', $top);
		$this->assign('yuanLable', $yuanLable);
		$this->assign('goodInfo', $goodInfo);
		$this->assign('lableData', $lableData);
		$this->display('edit');
	}
	/**
	 * 编辑商品--确定添加
	 * */
	public function editAdd()
	{
		//$data=I("post.");
		
		$data=array();
		$gid=I("post.gid",0,'intval');
		$id=I("post.id",0,'intval');
		$name=I("post.name");
		$price=I("post.price");
		$pic=I("post.pic");
		$lable=implode(I("post.lable"), ',');
		$s1=I("post.s1",0,'intval');
		$s2=I("post.s2",0,'intval');
		$s3=I("post.s3",0,'intval');
		$content=I("post.content");
		//获得该商品原有分类
		$classify=$this->model->getOneField($id,'classify');
		
		if($s3){
			$data['classify']=$s3;
		}elseif(!$s3  and $s2){
			$data['classify']=$s2;
		}elseif(!$s3 and !$s2 and $s1){
			$data['classify']=$s1;
		}elseif(!$s3 and !$s2 and !$s1){
			$data['classify']=$classify;
		}
		if($pic){
			$data['pic']=$pic;
		}
		$data['id']=$id;
		$data['gid']=$gid;
		$data['name']=$name;
		$data['price']=$price;
		$data['lable']=$lable;
		$data['content']=$content;
		
		//dump($data);die;
		if($this->model->updata($data)){
			$this->ajaxReturn("ok");
		}
	}
	public function off()
	{
		$id = array_unique((array)I('id', 0));
		$id = is_array($id) ? implode(',', $id) : $id;
		if (empty($id)) {
			$this->error('请选择要操作的数据!');
		}
		$map['id'] = array('in',explode(',', $id));
		$state = array('state' => -1);
		$changeState = new GoodModel();
		$result = $changeState->states($map,$state);
		if(false !== $result){
			$this->success('success');
		}else{
			$this->error('系统异常，联系系统管理员');
		}
	}
	/**
	 * 
	 *更改标签--显示页面
	 * */
	public function changeLabel()
	{
		$key=I('key');
		$actions = $this->getMenuActions($key);
        $actionInfos = [];
        foreach ($actions as $action) {
            $actionInfos[$action['key']] = $action;
        }
		$this->assign('actions', $actionInfos);
		//dump($actionInfos);die;
		$id=I("get.id",0,'intval');
		
		$data=$this->model->getOne($id);
		//dump($data);
		//获得商品原来标签id
		$yuanLable=$data['lable'];
		$yuanLable=explode(',', $yuanLable);
		//获得所有标签
		$lableModel=new LableModel();
		$lableData=$lableModel->lists();
		//dump($yuanLable);
		//dump($lableData);die;
		$this->assign('data', $data);
		$this->assign('yuanLable', $yuanLable);
		$this->assign('lableData', $lableData);
		
		$this->display('changeLabel');
	}
	/**
	 * 修改商品标签--点击确定
	 * */
	public function changeLabelUpdate()
	{
		$arr=I("post.arr");
		$id=I("post.id",0,'intval');
		if(!$id){
			$this->error("商品id无法标示");exit;
		}
		if($arr){
			$lable=implode($arr, ',');
		}else{
			$lable=0;
		}
		if($this->model->setOne($id,$lable)){
			$this->ajaxReturn("成功");
		}
		//dump($lable);die;
	}
	public function on()
	{
		$id = array_unique((array)I('id', 0));
		$id = is_array($id) ? implode(',', $id) : $id;
		if (empty($id)) {
			$this->error('请选择要操作的数据!');
		}
		$map['id'] = array('in',explode(',', $id));
		$state = array('state' => 1);
		$changeState = new GoodModel();
		$result = $changeState->states($map,$state);
		if(false !== $result){
			$this->success('success');
		}else{
			$this->error('系统异常，联系系统管理员');
		}
	}
}






