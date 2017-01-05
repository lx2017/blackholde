<?php

namespace Home\Controller\Saleman;

use Home\Common\SalemanagerHelper;
use Home\Common\SalemanHelper;
use Home\Controller\Saleman\CommonController;

class SalemanController extends CommonController {

    public function index() {
        switch ($this->_salemanInfo['role_id']) {
            case 0:
                //总部
                redirect('/Home/SaleManager/Head/index');
                exit;
                break;
            case 1:
                //大区经理
                redirect('/Home/SaleManager/Salemanager/index');
                exit;
                break;
            case 2:
                //省总
                redirect('/Home/SaleManager/Province/index');
                exit;
                break;
            case 3:
                //地总
                redirect('/Home/SaleManager/City/index');
                exit;
                break;
            case 4:
                //县总
                redirect('/Home/SaleManager/County/index');
                exit;
                break;
            default:
                break;
        }

        //用户ID加密
        $salemanId = think_ucenter_encrypt($this->_salemanInfo['id']);

        //获取消息数量
        $msgTotalNum = 0;
        $salemanNoticeModel = M('saleman_notice');
        $where = array(
            'type' => array('in', array(1, 2)),
            'accept_saleman_id' => array('eq', $this->_salemanInfo['id']),
            'is_look' => array('eq', 0)
        );
        $msgTotalNum = $salemanNoticeModel->where($where)->count();

        $this->assign('salemanInfo', $this->_salemanInfo);
        $this->assign('salemanId', $salemanId);
        $this->assign('msgTotalNum', $msgTotalNum);
        $this->display();
    }

    /**
     * 工作安排
     */
    public function workArgument() {
        //获取当前时间
        $currentTime = time();
        $currentDay = date('Y-m-d', $currentTime);
        $tomorrowDay = date('Y-m-d', $currentTime + 24 * 60 * 60);

        $salemanTripModel = M('saleman_trip');
        if (IS_AJAX) {
            $op = $_GET['op'];
            switch ($op) {
                //添加工作
                case 'add':
                    $type = $_POST['type'];
                    $content = $_POST['content'];
                    /*
                      做相关验证
                    */
                    if($this->verify($type,$content)==FALSE)
                    {
                          responseJson(0,"请勿重复添加,相同的工作记录");
                          exit;
                    }
                    if (!isset($type) || !in_array($type, array(1, 2)) || !isset($content) || empty($content)) {
                        responseJson(0, '添加失败，参数无效');
                        exit;
                    }

                    //组装数据
                    $data = array(
                        'saleman_id' => $this->_salemanInfo['id'],
                        'content' => $content,
                        'type' => $type,
                        'is_send' => 0,
                        'add_time' => $currentTime
                    );

                    switch ($type) {
                        //今日完成工作
                        case 1:
                            $data['work_day'] = $currentDay;
                            $excNum = $salemanTripModel->data($data)->add();
                            $workDay = $currentDay;
                            break;
                        case 2:
                            $data['work_day'] = $tomorrowDay;
                            $excNum = $salemanTripModel->data($data)->add();
                            $workDay = $tomorrowDay;
                            break;
                        default:
                            $excNum = 0;
                            break;
                    }
                    if ($excNum <= 0) {
                        responseJson(0, '添加失败');
                        exit;
                    }

                    //返回
                    $data = array(
                        'content' => $content,
                        'work_day' => $workDay
                    );
                    responseJson(1, '添加成功', $data);
                    exit;
                    break;
                //发送给上级
                case 'send':
                    //查询今日添加的今日完成工作和明日安排的集合
                    $where = array(
                        'saleman_id' => array('eq', $this->_salemanInfo['id']),
                        'is_send' => array('eq', 0)
                    );
                    $map['add_time'] = array('egt', strtotime($currentDay));
                    $map['add_time'] = array('lt', strtotime($tomorrowDay));
                    $map['_logic'] = 'or';
                    $where['_complex'] = $map;
                    $tripList = $salemanTripModel->where($where)->select();
                    if (!check_array($tripList)) {
                        responseJson(0, '暂无需要提交的工作');
                        exit;
                    }

                    //开启事务
                    $salemanTripModel->startTrans();

                    //修改数据
                    $salemanNoticeModel = M('saleman_notice');
                    foreach ($tripList as $key => $value) {
                        //修改工作是否已读
                        $data = array(
                            'is_send' => 1
                        );
                        $where = array(
                            'id' => array('eq', $value['id'])
                        );
                        $excNum = $salemanTripModel->where($where)->data($data)->save();
                        if ($excNum <= 0) {
                            $salemanTripModel->rollback();
                            responseJson(0, '提交失败');
                            exit;
                        }

                        //添加下级消息
                        if ($value['type'] == 1) {
                            $title = "今日完成工作";
                            $trip_type = 1;
                        } elseif ($value['type'] == 2) {
                            $title = "明日计划";
                            $trip_type = 2;
                        } else {
                            continue;
                        }
                        $data = array(
                            'title' => $title,
                            'content' => $value['content'],
                            'type' => 3,
                            'trip_type' => $trip_type,
                            'send_user_id' => $this->_salemanInfo['id'],
                            'send_name' => $this->_salemanInfo['real_name'],
                            'send_time' => $currentTime,
                            'accept_saleman_id' => $this->_salemanInfo['superior_id'],
                            'is_look' => 0,
                            'look_time' => 0
                        );
                        $excNum = $salemanNoticeModel->data($data)->add();
                        if ($excNum <= 0) {
                            $salemanTripModel->rollback();
                            responseJson(0, '提交失败');
                            exit;
                        }
                    }

                    //提交事务
                    $salemanTripModel->commit();

                    //返回
                    responseJson(1, '提交成功');
                    exit;

                    break;
                //发送记录
                case 'sendlog':
                    //获取每页显示条数
                    $limit = C('DEF_LIMIT');

                    //获取传递参数
                    $page = $_POST['page'];
                    if (!is_numeric($page) || intval($page) < 0) {
                        $page = 0;
                    }

                    $where = array(
                        'saleman_id' => array('eq', $this->_salemanInfo['id']),
                        'type' => array('eq', 1),
                        'is_send' => array('eq', 1)
                    );
                    $order = array(
                        'add_time' => 'desc'
                    );

                    //查询总记录
                    $salemanTripModel = M('saleman_trip');
                    $count = $salemanTripModel->where($where)->count();
                    //获取总页数
                    if (intval($count % $limit) > 0) {
                        $totalPage = intval($count / $limit) + 1;
                    } else {
                        $totalPage = intval($count / $limit);
                    }
                    if ($page > $totalPage) {
                        responseJson(0, '暂无更多记录');
                        exit;
                    }

                    $startNum = $page * $limit;

                    //查询
                    $tripList = $salemanTripModel->where($where)->order($order)->limit($startNum, $limit)->distinct(TRUE)->field("work_day")->select();
                    
                    if (!check_array($tripList)) {
                        responseJson(0, '暂无更多记录');
                        exit;
                    }

                    //返回数据
                    responseJson(1, 'success', $tripList);
                    break;
                default:
                    break;
            }
        }

        //查询今日完成工作
        $where = array(
            'saleman_id' => array('eq', $this->_salemanInfo['id']),
            'type' => array('eq', 1),
            'is_send' => array('eq', 0),
            'work_day' => array('eq', $currentDay)
        );
        $tripTodayList = $salemanTripModel->where($where)->select();

        //查询明日安排工作
        $where = array(
            'saleman_id' => array('eq', $this->_salemanInfo['id']),
            'type' => array('eq', 2),
            'is_send' => array('eq', 0),
            'work_day' => array('eq', $tomorrowDay)
        );
        $tripTomorrowList = $salemanTripModel->where($where)->select();

        $this->assign(
                array(
                    'tripTodayList' => $tripTodayList,
                    'tripTomorrowList' => $tripTomorrowList
                )
        );
        $this->display();
    }
    /*
     数据防止重复简单处理
     @param:$type:提交信息类型
     $content:提交内容
    */
    private function verify($type,$content)
    {
        $flag = M('saleman_trip')->where(array('type'=>$type,'content'=>$content,'saleman_id'=>$this->_salemanInfo['id']))->count();
        if($flag>0)
        {
            return FALSE;
        }
        return TRUE;
    } 
    /**
     * 目标诊所
     */
    public function targetClinic() {


        $this->display();
    }
    /*
     查看工作记录详情
    */
     public function workrecord()
     {
        
        $where = array(
            'saleman_id' => array('eq', $this->_salemanInfo['id']),
            'type' => array('eq', 1),
            'is_send' => array('eq', 1),
            'work_day' =>$_GET['day']
        );
        $result = M('saleman_trip')->where($where)->select();
        unset($where);
        $this->assign("result",$result);
        $this->display();
     }
    /**
     * 我的诊所
     */
    public function clinic() {
        if (IS_AJAX) {

            //删除诊所
            $clinicId = $_GET['clinicid'];
            if (empty($clinicId)) {
                responseJson(0, '数据无效');
                exit;
            }
            $clinicModel = M('clinic');
            $where = array(
                'saleman_id' => array('eq', $this->_salemanInfo['id']),
                'id' => array('eq', $clinicId)
            );
            $data = array(
                'is_delete' => 1
            );
            $excNum = $clinicModel->where($where)->data($data)->save();
            if ($excNum <= 0) {
                responseJson(0, '删除失败');
                exit;
            }
            responseJson(1, '删除成功');
            exit;
        }

        //查询我的诊所（有效诊所，未删除）
        $clinicModel = M('clinic');
        $where = array(
            'saleman_id' => array('eq', $this->_salemanInfo['id']),
            'is_delete' => array('eq', 0)
        );
        $clinicList = $clinicModel->where($where)->select();

        $this->assign(
                array(
                    'clinicList' => $clinicList
                )
        );
        $this->display();
    }

    /**
     * 诊所详情
     */
    public function clinichome() {
        $id = empty($_GET['id']) ? 0 : $_GET['id'];
        //查询诊所（有效诊所，未删除）
        $clinicModel = M('clinic');
        $where = array(
            'saleman_id' => array('eq', $this->_salemanInfo['id']),
            'is_delete' => array('eq', 0),
            'id' => array('eq', $id)
        );
        $clinic = $clinicModel->where($where)->find();

        //查询当前诊所医生
        $doctorModel = M('doctor');
        $where = array(
            'clinic_id' => array('eq', $id)
        );
        $doctorList = $doctorModel->where($where)->select();

        $this->assign(
                array(
                    'clinic' => $clinic,
                    'doctorList' => $doctorList
                )
        );
        $this->display();
    }

    /**
     * 签到拜访
     */
    public function sign() {
        $lng = 104.153645; //经度
        $lat = 30.6313; //纬度

       

        if (IS_AJAX) {

            $op = $_GET['op'];
            switch ($op) {
                //可签到诊所
                case 'cansign':
                    //获取当前用户的的定位坐标
                    $lng = $_POST['lng'];
                    $lat = $_POST['lat'];
                    if (empty($lng) || empty($lat)) {
                        responseJson(0, '定位失败，暂无法获取诊所');
                        exit;
                    }

                    $singDistance = C('SING_DISTANCE'); //可签到距离
                    $nearDistance = C('NEAR_DISTANCE'); //附近诊所距离
                    $range = 180 / pi() * $singDistance / 6372.797; //里面的 0.1 就代表搜索 100m 之内，单位km
                    $lngR = $range / cos($lat * pi() / 180);
                    $maxLat = $lat + $range; //最大纬度
                    $minLat = $lat - $range; //最小纬度
                    $maxLng = $lng + $lngR; //最大经度
                    $minLng = $lng - $lngR; //最小经度
                    //获取当前日期
                    $currentDate = date('Y-m-d', time());


                    $where = array(
                        'saleman_id' => array('eq', $this->_salemanInfo['id']),
                        'is_delete' => array('eq', 1),
                        'type' => array('eq', 0),
                        'clinic_lng' => array('between', array($minLng, $maxLng)),
                        'clinic_lat' => array('between', array($minLat, $maxLat))
                    );

                    //查询今天已签到的诊所
                    $salemanSignModel = M('saleman_sign');
                    $whereSign = array(
                        'sign_date' => array('eq', $currentDate),
                        'saleman_id' => array('eq', $this->_salemanInfo['id'])
                    );
                    $signList = $salemanSignModel->where($whereSign)->select();
                    if (check_array($signList)) {
                        $clinicIdList = array_keys(set_array_key($signList, 'clinic_id'));
                        $where['id'] = array('not in', $clinicIdList);
                    }

                    $order = array(
                        'add_time' => 'desc'
                    );

                    //查询
                    $clinicModel = M('clinic');
                    $clinicList = $clinicModel->where($where)->order($order)->select();
                    if (!check_array($clinicList)) {
                        responseJson(0, '暂无更多记录');
                        exit;
                    }

                    //返回数据
                    responseJson(1, 'success', $clinicList);
                    exit;

                    break;
                //附近诊所
                case 'nearby':
                    //获取当前用户的的定位坐标
                    $lng = $_POST['lng'];
                    $lat = $_POST['lat'];
                    if (empty($lng) || empty($lat)) {
                        responseJson(0, '定位失败，暂无法获取诊所');
                        exit;
                    }
                    $nearDistance = C('NEAR_DISTANCE'); //附近诊所距离
                    $range = 180 / pi() * $nearDistance / 6372.797; //里面的 0.1 就代表搜索 100m 之内，单位km
                    $lngR = $range / cos($lat * pi() / 180);
                    $maxLat = $lat + $range; //最大纬度
                    $minLat = $lat - $range; //最小纬度
                    $maxLng = $lng + $lngR; //最大经度
                    $minLng = $lng - $lngR; //最小经度
                    //获取当前日期
                    $currentDate = date('Y-m-d', time());

                    $where = array(
                        'saleman_id' => array('eq', $this->_salemanInfo['id']),
                        'is_delete' => array('eq', 1),
                        'type' => array('eq', 0),
                        'clinic_lng' => array('between', array($minLng, $maxLng)),
                        'clinic_lat' => array('between', array($minLat, $maxLat))
                    );

                    //查询今天已签到的诊所
                    $salemanSignModel = M('saleman_sign');
                    $whereSign = array(
                        'sign_date' => array('eq', $currentDate),
                        'saleman_id' => array('eq', $this->_salemanInfo['id'])
                    );
                    $signList = $salemanSignModel->where($whereSign)->select();
                    if (check_array($signList)) {
                        $clinicIdList = array_keys(set_array_key($signList, 'clinic_id'));
                        $where['id'] = array('not in', $clinicIdList);
                    }

                    $order = array(
                        'add_time' => 'desc'
                    );

                    //查询
                    $clinicModel = M('clinic');
                    $clinicList = $clinicModel->where($where)->order($order)->select();
                    if (!check_array($clinicList)) {
                        responseJson(0, '暂无更多记录');
                        exit;
                    }

                    //返回数据
                    responseJson(1, 'success', $clinicList);
                    exit;

                    break;
                //已签到诊所
                case 'alreadysign':
                    //获取当前日期
                    $currentDate = date('Y-m-d', time());
                    $where = array(
                        'saleman_id' => array('eq', $this->_salemanInfo['id']),
                        'is_delete' => array('eq', 1),
                        'type' => array('eq', 0)
                    );

                    //查询今天已签到的诊所
                    $salemanSignModel = M('saleman_sign');
                    $where = array(
                        'sign_date' => array('eq', $currentDate),
                        'saleman_id' => array('eq', $this->_salemanInfo['id'])
                    );
                    $signList = $salemanSignModel->where($where)->select();
                    if (!check_array($signList)) {
                        responseJson(0, '今日暂无已签到诊所');
                        exit;
                    }

                    //获取已签到的诊所ID集合
                    $clinicIdList = array_keys(set_array_key($signList, 'clinic_id'));
                    $where['id'] = array('in', $clinicIdList);

                    $order = array(
                        'add_time' => 'desc'
                    );
                    
                    //查询
                    $clinicModel = M('clinic');
                    $clinicList = $clinicModel->where($where)->order($order)->select();
                    if (!check_array($clinicList)) {
                        responseJson(0, '暂无更多记录');
                        exit;
                    }

                    //返回数据
                    responseJson(1, 'success', $clinicList);
                    exit;

                    break;
                //签到
                case 'sign':
                    $clinicId = $_POST['clinic_id'];
                    if (empty($clinicId)) {
                        responseJson(0, '诊所无效');
                        exit;
                    }

                    //获取当前时间
                    $currentTime = time();
                    $currentDate = date('Y-m-d', $currentTime);

                    //查询诊所是否存在
                    $clinicModel = M('clinic');
                    $where = array(
                        'id' => $clinicId,
                        'saleman_id' => $this->_salemanInfo['id'],
                        'is_delete' => array('eq', 1),
                        'type' => array('eq', 0)
                    );
                    $clinic = $clinicModel->where($where)->find();
                    if (!check_array($clinic)) {
                        responseJson(0, '签到失败，诊所无效');
                        exit;
                    }

                    //判断经纬度(暂时未做)
                    //查询诊所今天是否已签到
                    $salemanSignModel = M('saleman_sign');
                    $where = array(
                        'sign_date' => array('eq', $currentDate),
                        'clinic_id' => array('eq', $clinicId),
                        'saleman_id' => array('eq', $this->_salemanInfo['id'])
                    );
                    $signList = $salemanSignModel->where($where)->find();
                    if (check_array($signList)) {
                        responseJson(0, '诊所今日 已签到');
                        exit;
                    }

                    //签到
                    $data = array(
                        'clinic_id' => $clinic['id'],
                        'clinic_name' => $clinic['clinic_name'],
                        'saleman_id' => $this->_salemanInfo['id'],
                        'sign_time' => $currentTime,
                        'sign_date' => $currentDate
                    );
                    $excNum = $salemanSignModel->data($data)->add();
                    if ($excNum <= 0) {
                        responseJson(0, '诊所签到失败');
                        exit;
                    }

                    //返回数据
                    responseJson(1, 'success');
                    exit;

                    break;
                default:
                    break;
            }
            exit;
        }

        $this->assign('userId', $this->_salemanInfo['id']);
        $this->display();
    }

    /**
     * 申请支持
     */
    public function appliceSupport() {
        //获取会议类型
        $applyCateList = C('DEF_APPLY_CATE');

        if (IS_AJAX) {
            //获取会议类型ID
            $applyCateIdList = array_keys($applyCateList);
            

            $type = $_POST['type'];
            $apply_content = $_POST['apply_content'];
            if (!in_array($type, $applyCateIdList) || empty($apply_content)) {

                responseJson(0, '添加失败');
                exit;
            }

            //获取当前用户的总部用户ID
            $superior = SalemanHelper::getSuperiorById($this->_salemanInfo['id'], 0);

            if (!check_array($superior)) {
                responseJson(0, '未找到总部,无法抄送');
                exit;
            }

            //组装数据
            $data = array(
                'saleman_id' => $this->_salemanInfo['id'],
                'type' => $type,
                'apply_content' => $apply_content,
                'superior_id' => $this->_salemanInfo['superior_id'],
                'reply_status' => 0,
                'reply_content' => '',
                'apply_time' => time(),
                'reply_time' => 0,
                'copy_id' => $superior['id']
            );

            //添加
            $salemanApplyModel = M('saleman_apply');
            $excNum = $salemanApplyModel->data($data)->add();
            if ($excNum <= 0) {
                responseJson(0, '添加失败');
                exit;
            }

            //返回
            responseJson(1, '添加申请成功');
            exit;
        }

        $this->assign('applyCateList', $applyCateList);
        $this->display();
    }

    /**
     * 诊所订单
     */
    public function clinicOrder() {
        if (IS_AJAX) {
            //获取每页显示条数
            $limit = C('DEF_LIMIT');

            //获取传递参数
            $status = $_POST['status'];
            $page = $_POST['page'];
            if (!is_numeric($page) || intval($page) < 0) {
                $page = 0;
            }

            $where = array(
                'saleman_id' => array('eq', $this->_salemanInfo['id']),
                'type' => array('eq', 0)
            );
            $order = array(
                'add_time' => 'desc'
            );

            $salemanOrderModel = M('saleman_order');

            switch ($status) {
                //查询所有的订单
                case -1:
                    //查询总记录
                    $count = $salemanOrderModel->where($where)->count();
                    //获取总页数
                    if (intval($count % $limit) > 0) {
                        $totalPage = intval($count / $limit) + 1;
                    } else {
                        $totalPage = intval($count / $limit);
                    }
                    if ($page > $totalPage) {
                        responseJson(0, '暂无更多记录');
                        exit;
                    }

                    $startNum = $page * $limit;

                    //查询
                    $orderList = $salemanOrderModel->where($where)->order($order)->limit($startNum, $limit)->select();
                    if (!check_array($orderList)) {
                        responseJson(0, '暂无更多记录');
                        exit;
                    }

                    //获取诊所ID
                    $clinicIdList = array_keys(set_array_key($orderList, 'send_id'));
                    $clinicList = array();
                    if (check_array($clinicIdList)) {
                        //查询诊所
                        $clinicModel = M('clinic');
                        $where = array(
                            'id' => array('in', $clinicIdList)
                        );
                        $field = array(
                            'id', 'clinic_name'
                        );
                        $clinicList = $clinicModel->where($where)->field($field)->select();
                        $clinicList = set_array_key($clinicList, 'id');
                    }

                    //组装数据
                    foreach ($orderList as $key => $value) {
                        $orderList[$key]['clinic_name'] = isset($clinicList[$value['send_id']]) ? $clinicList[$value['send_id']]['clinic_name'] : '';
                    }

                    //返回数据
                    responseJson(1, 'success', $orderList);
                    exit;
                    break;
                //查询未处理订单
                case 0:
                    $where['status'] = $status;
                    //查询总记录
                    $count = $salemanOrderModel->where($where)->count();
                    //获取总页数
                    if (intval($count % $limit) > 0) {
                        $totalPage = intval($count / $limit) + 1;
                    } else {
                        $totalPage = intval($count / $limit);
                    }
                    if ($page > $totalPage) {
                        responseJson(0, '暂无更多记录');
                        exit;
                    }

                    $startNum = $page * $limit;

                    //查询
                    $orderList = $salemanOrderModel->where($where)->order($order)->limit($startNum, $limit)->select();
                    if (!check_array($orderList)) {
                        responseJson(0, '暂无更多记录');
                        exit;
                    }

                    //获取诊所ID
                    $clinicIdList = array_keys(set_array_key($orderList, 'send_id'));
                    $clinicList = array();
                    if (check_array($clinicIdList)) {
                        //查询诊所
                        $clinicModel = M('clinic');
                        $where = array(
                            'id' => array('in', $clinicIdList)
                        );
                        $field = array(
                            'id', 'clinic_name'
                        );
                        $clinicList = $clinicModel->where($where)->field($field)->select();
                        $clinicList = set_array_key($clinicList, 'id');
                    }

                    //组装数据
                    foreach ($orderList as $key => $value) {
                        $orderList[$key]['clinic_name'] = isset($clinicList[$value['send_id']]) ? $clinicList[$value['send_id']]['clinic_name'] : '';
                    }

                    //返回数据
                    responseJson(1, 'success', $orderList);
                    exit;
                    break;
                //查询已完成订单
                case 1:
                    $where['status'] = $status;
                    //查询总记录
                    $count = $salemanOrderModel->where($where)->count();
                    //获取总页数
                    if (intval($count % $limit) > 0) {
                        $totalPage = intval($count / $limit) + 1;
                    } else {
                        $totalPage = intval($count / $limit);
                    }
                    if ($page > $totalPage) {
                        responseJson(0, '暂无更多记录');
                        exit;
                    }

                    $startNum = $page * $limit;

                    //查询
                    $orderList = $salemanOrderModel->where($where)->order($order)->limit($startNum, $limit)->select();
                    if (!check_array($orderList)) {
                        responseJson(0, '暂无更多记录');
                        exit;
                    }

                    //获取诊所ID
                    $clinicIdList = array_keys(set_array_key($orderList, 'send_id'));
                    $clinicList = array();
                    if (check_array($clinicIdList)) {
                        //查询诊所
                        $clinicModel = M('clinic');
                        $where = array(
                            'id' => array('in', $clinicIdList)
                        );
                        $field = array(
                            'id', 'clinic_name'
                        );
                        $clinicList = $clinicModel->where($where)->field($field)->select();
                        $clinicList = set_array_key($clinicList, 'id');
                    }

                    //组装数据
                    foreach ($orderList as $key => $value) {
                        $orderList[$key]['clinic_name'] = isset($clinicList[$value['send_id']]) ? $clinicList[$value['send_id']]['clinic_name'] : '';
                    }

                    //返回数据
                    responseJson(1, 'success', $orderList);
                    exit;
                    break;
                //查询退货订单
                case 2:
                    $where['status'] = $status;
                    //查询总记录
                    $count = $salemanOrderModel->where($where)->count();
                    //获取总页数
                    if (intval($count % $limit) > 0) {
                        $totalPage = intval($count / $limit) + 1;
                    } else {
                        $totalPage = intval($count / $limit);
                    }
                    if ($page > $totalPage) {
                        responseJson(0, '暂无更多记录');
                        exit;
                    }

                    $startNum = $page * $limit;

                    //查询
                    $orderList = $salemanOrderModel->where($where)->order($order)->limit($startNum, $limit)->select();
                    if (!check_array($orderList)) {
                        responseJson(0, '暂无更多记录');
                        exit;
                    }

                    //获取诊所ID
                    $clinicIdList = array_keys(set_array_key($orderList, 'send_id'));
                    $clinicList = array();
                    if (check_array($clinicIdList)) {
                        //查询诊所
                        $clinicModel = M('clinic');
                        $where = array(
                            'id' => array('in', $clinicIdList)
                        );
                        $field = array(
                            'id', 'clinic_name'
                        );
                        $clinicList = $clinicModel->where($where)->field($field)->select();
                        $clinicList = set_array_key($clinicList, 'id');
                    }

                    //组装数据
                    foreach ($orderList as $key => $value) {
                        $orderList[$key]['clinic_name'] = isset($clinicList[$value['send_id']]) ? $clinicList[$value['send_id']]['clinic_name'] : '';
                    }

                    //返回数据
                    responseJson(1, 'success', $orderList);
                    exit;
                    break;
                default:
                    //返回数据
                    responseJson(0, '暂无更多数据');
                    exit;
                    break;
            }
        }

        $this->display();
    }

    /**
     * 订单详情
     */
    public function orderInfo() {
        if (IS_AJAX) {
            $orderId = $_POST['id'];
            $status = $_POST['status'];
            if (empty($orderId) || $status != 1) {
                responseJson(0, '操作失败');
                exit;
            }

            //查询订单是否存在
            $salemanOrderModel = M('saleman_order');
            $where = array(
                'saleman_id' => array('eq', $this->_salemanInfo['id']),
                'id' => array('eq', $orderId),
                'status' => array('eq', 0),
                'type' => array('eq', 0)
            );
            $order = $salemanOrderModel->where($where)->find();
            if (!check_array($order)) {
                responseJson(0, '操作失败');
                exit;
            }

            //修改
            $data = array(
                'status' => $status
            );
            $excNum = $salemanOrderModel->where($where)->data($data)->save();
            if ($excNum <= 0) {
                responseJson(0, '操作失败');
                exit;
            }

            //返回
            responseJson(1, '操作成功');
            exit;
        }

        $id = $_GET['id'];
        //查询订单信息
        $salemanOrderModel = M('saleman_order');
        $where = array(
            'id' => array('eq', $id),
            'saleman_id' => array('eq', $this->_salemanInfo['id']),
            'type' => array('eq', 0)
        );
        $order = $salemanOrderModel->where($where)->find();

        //查询订单详情
        $salemanOrderDetailModel = M('saleman_order_detail');
        $where = array(
            'order_id' => array('eq', $id)
        );
        $orderDetailList = $salemanOrderDetailModel->where()->select();

        //查询诊所
        $clinicModel = M('clinic');
        $where = array(
            'id' => $order['send_id']
        );
        $clinic = $clinicModel->where($where)->find();

        $this->assign(array(
            'order' => $order,
            'orderDetailList' => $orderDetailList,
            'clinic' => $clinic
        ));
        $this->display();
    }

    /**
     * 订购药品
     */
    public function orderMedicine() {

        if (IS_AJAX) {

            $op = $_GET['op'];
            switch ($op) {
                //执行药品下拉加载的方法
                case 'list':
                    //获取每页显示条数
                    $limit = C('DEF_LIMIT');

                    //获取传递参数
                    $page = $_POST['page'];
                    if (!is_numeric($page) || intval($page) < 0) {
                        $page = 0;
                    }

                    $where = array(
                        'is_valid' => array('eq', 1)
                    );
                    $order = array(
                        'sort'
                    );

                    //查询总记录
                    $drugsModel = M('drugs');
                    $count = $drugsModel->where($where)->count();
                    //获取总页数
                    if (intval($count % $limit) > 0) {
                        $totalPage = intval($count / $limit) + 1;
                    } else {
                        $totalPage = intval($count / $limit);
                    }
                    if ($page > $totalPage) {
                        responseJson(0, '暂无更多记录');
                        exit;
                    }

                    $startNum = $page * $limit;

                    //查询
                    $drugsList = $drugsModel->where($where)->order($order)->select();
                    if (!check_array($drugsList)) {
                        responseJson(0, '暂无更多记录');
                        exit;
                    }

                    //返回数据
                    responseJson(1, 'success', $drugsList);
                    exit;

                    break;
                //执行药品列表页的药品加入购物车的方法
                case 'cart':
                    $drugsId = $_POST['drugs_id'];
                    $num = $_POST['num'];
                    if (empty($drugsId)) {
                        responseJson(0, '加入购物车失败');
                        exit;
                    }

                    if (!is_numeric($num) || $num <= 0) {
                        $num = 1;
                    }

                    //查询当前药品是否存在
                    $drugsModel = M('drugs');
                    $where = array(
                        'id' => array('eq', $drugsId)
                    );
                    $drugs = $drugsModel->where($where)->find();
                    if (!check_array($drugs)) {
                        responseJson(0, '加入购物车失败');
                        exit;
                    }

                    //查询当前药品是否已存在购物车中
                    $drugsCartModel = M('drugs_cart');
                    $where = array(
                        'add_user_id' => $this->_salemanInfo['id'],
                        'type' => array('eq', 1),
                        'drugs_id' => array('eq', $drugsId)
                    );
                    $cart = $drugsCartModel->where($where)->find();

                    if (check_array($cart)) {
                        //修改数量
                        $data = array(
                            'num' => $cart['num'] + 1,
                            'add_time' => time()
                        );
                        $excNum = $drugsCartModel->where($where)->data($data)->save();
                    } else {
                        //新增
                        $data = array(
                            'add_user_id' => $this->_salemanInfo['id'],
                            'type' => 1,
                            'drugs_id' => $drugsId,
                            'drugs_name' => $drugs['name'],
                            'num' => $num,
                            'add_time' => time()
                        );
                        $excNum = $drugsCartModel->data($data)->add();
                    }
                    if ($excNum <= 0) {
                        responseJson(0, '加入购物车失败');
                        exit;
                    }

                    //查询当前用户购物车里的信息集合
                    $where = array(
                        'add_user_id' => $this->_salemanInfo['id'],
                        'type' => 1
                    );
                    $cartList = $drugsCartModel->where($where)->select();

                    //获取药品ID集合
                    $cartList = set_array_key($cartList, 'drugs_id');
                    $drugsIdList = array_keys($cartList);

                    //查询购物车里药品信息
                    $where = array(
                        'id' => array('in', $drugsIdList),
                        'is_valid' => array('eq', 1)
                    );
                    $drugsList = $drugsModel->where($where)->select();

                    //组装有效药品集合和药品总金额和总数量
                    $return = array(
                        'totalPrice' => 0,
                        'totalNum' => 0
                    );
                    foreach ($drugsList as $key => $value) {
                        $drugsNum = $cartList[$value['id']]['num'];
                        $drugsPrice = $drugsNum * $value['price'];
                        $return['totalPrice']+=$drugsPrice;
                        $return['totalNum']+=$drugsNum;
                    }

                    responseJson(1, '加入购物车成功', $return);
                    exit;

                    break;
                default:
                    break;
            }
            exit;
        }
        //查询购物车信息
        $drugsCartModel = M('drugs_cart');
        $where = array(
            'add_user_id' => array('eq', $this->_salemanInfo['id']),
            'type' => array('eq', 1)
        );
        $drugsCartList = $drugsCartModel->where($where)->select();
        $cartList = array(
            'num' => 0,
            'total_price' => 0
        );

        if (check_array($drugsCartList)) {
            //获取所有药品ID集合
            $drugsIdList = array_keys(set_array_key($drugsCartList, 'drugs_id'));

            //查询药品集合信息
            $drugsModel = M('drugs');
            $where = array(
                'id' => array('in', $drugsIdList)
            );
            $field = array(
                'id', 'price'
            );
            $drugsList = $drugsModel->where($where)->field($field)->select();
            $drugsList = set_array_key($drugsList, 'id');

            foreach ($drugsCartList as $key => $value) {
                $price = isset($drugsList[$value['drugs_id']]) ? $drugsList[$value['drugs_id']]['price'] : 0;
                $totalPrice = $value['num'] * $price;
                $cartList['num']+=$value['num'];
                $cartList['total_price']+=$totalPrice;
            }
        }

        $this->assign('cartList', $cartList);
        $this->display();
    }

    /**
     * 药品详情
     */
    public function medicineInfo() {
        if (IS_AJAX) {

            $drugsId = $_POST['drugs_id'];
            $num = $_POST['num'];
            if (empty($drugsId)) {
                responseJson(0, '加入购物车失败');
                exit;
            }

            if (!is_numeric($num) || $num <= 0) {
                $num = 1;
            }

            //查询当前药品是否
            $drugsModel = M('drugs');
            $where = array(
                'id' => array('eq', $drugsId)
            );
            $drugs = $drugsModel->where($where)->find();
            if (!check_array($drugs)) {
                responseJson(0, '加入购物车失败');
                exit;
            }

            //查询当前药品是否已存在购物车中
            $drugsCartModel = M('drugs_cart');
            $where = array(
                'add_user_id' => $this->_salemanInfo['id'],
                'type' => array('eq', 1),
                'drugs_id' => array('eq', $drugsId)
            );
            $cart = $drugsCartModel->where($where)->find();

            if (check_array($cart)) {
                //修改数量
                $data = array(
                    'num' => $cart['num'] + 1,
                    'add_time' => time()
                );
                $excNum = $drugsCartModel->where($where)->data($data)->save();
            } else {
                //新增
                $data = array(
                    'add_user_id' => $this->_salemanInfo['id'],
                    'type' => 1,
                    'drugs_id' => $drugsId,
                    'drugs_name' => $drugs['name'],
                    'num' => $num,
                    'add_time' => time()
                );
                $excNum = $drugsCartModel->data($data)->add();
            }
            if ($excNum <= 0) {
                responseJson(0, '加入购物车失败');
                exit;
            }

            responseJson(1, '加入购物车成功');
            exit;
        }

        $id = $_GET['id'];
        //查询药品详情
        $drugsModel = M('drugs');
        $where = array(
            'id' => array('eq', $id),
            'is_valid' => array('eq', 1)
        );
        $drugs = $drugsModel->where($where)->find();

        $this->assign('drugs', $drugs);
        $this->display();
    }

    /**
     * 结算订单
     */
    public function setelOrder() {
        if (IS_AJAX) {
            $op = $_GET['op'];
            switch ($op) {
                //删除购物车药品
                case 'delete':
                    $drugsId = $_POST['drugs_id'];

                    //查询购物车是否存在指定药品的信息
                    $drugsCartModel = M('drugs_cart');
                    $where = array(
                        'add_user_id' => $this->_salemanInfo['id'],
                        'type' => 1,
                        'drugs_id' => array('eq', $drugsId)
                    );
                    $cart = $drugsCartModel->where($where)->find();
                    if (!check_array($cart)) {
                        responseJson(0, '删除失败');
                        exit;
                    }

                    //删除
                    $excNum = $drugsCartModel->where($where)->delete();
                    if ($excNum <= 0) {
                        responseJson(0, '删除失败');
                        exit;
                    }

                    responseJson(1, '删除成功');
                    exit;
                    break;
                //提交订单
                case 'submit':
                    $address = $_POST['address'];
                    if (empty($address)) {
                        responseJson(0, '请填写地址');
                        exit;
                    }

                    //查询当前用户购物车里的信息集合
                    $drugsCartModel = M('drugs_cart');
                    $where = array(
                        'add_user_id' => $this->_salemanInfo['id'],
                        'type' => 1
                    );
                    $cartList = $drugsCartModel->where($where)->select();
                    if (!check_array($cartList)) {
                        responseJson(0, '购物车里暂无药品');
                        exit;
                    }

                    //获取药品ID集合
                    $cartList = set_array_key($cartList, 'drugs_id');
                    $drugsIdList = array_keys($cartList);

                    //查询购物车里药品信息
                    $drugsModel = M('drugs');
                    $where = array(
                        'id' => array('in', $drugsIdList),
                        'is_valid' => array('eq', 1)
                    );
                    $drugsList = $drugsModel->where($where)->select();
                    if (!check_array($drugsList)) {
                        responseJson(0, '购物车里暂无有效药品');
                        exit;
                    }

                    //组装有效药品集合和药品总金额和总数量
                    $dataDrugsList = array();
                    $totalPrice = 0;
                    $totalNum = 0;
                    foreach ($drugsList as $key => $value) {
                        $drugsNum = $cartList[$value['id']]['num'];
                        $drugsPrice = $drugsNum * $value['price'];
                        $totalPrice+=$drugsPrice;
                        $totalNum+=$drugsNum;
                        $dataDrugsList[] = array(
                            'drugs_id' => $value['id'],
                            'drugs_name' => $value['name'],
                            'price' => $value['price'],
                            'num' => $drugsNum,
                            'total_price' => $drugsPrice
                        );
                    }

                    //开启事务
                    $drugsCartModel->startTrans();

                    //清空购物车
                    $cartIdList = array_keys(set_array_key($cartList, 'id'));
                    $where = array(
                        'id' => array('in', $cartIdList)
                    );
                    $delNum = $drugsCartModel->where($where)->delete();
                    if ($delNum != count($cartList)) {

                        $drugsCartModel->rollback();
                        responseJson(0, '订单提交失败');
                        exit;
                    }

                    //获取订单ID
                    $date = date('Y-m-d');
                    $orderId = SalemanHelper::makeOrderId($date);

                    //添加订单
                    $salemanOrderModel = M('saleman_order');
                    $data = array(
                        'id' => $orderId,
                        'send_id' => $this->_salemanInfo['id'],
                        'type' => 1,
                        'status' => 0,
                        'saleman_id' => $this->_salemanInfo['superior_id'],
                        'total_price' => $totalPrice,
                        'num' => $totalNum,
                        'add_date' => $date,
                        'add_time' => time(),
                        'address' => $address
                    );
                    

                    $excNum = $salemanOrderModel->add($data);
                    
                    if ($excNum <= 0) {
                        $drugsCartModel->rollback();
                        responseJson(0, '订单提交失败');
                        exit;
                    }


                    //添加订单详情
                    $salemanOrderDetailModel = M('saleman_order_detail');
                    foreach ($dataDrugsList as $key => $value) {
                        $data = array(
                            'order_id' => $orderId,
                            'drugs_id' => $value['drugs_id'],
                            'drugs_name' => $value['drugs_name'],
                            'price' => $value['price'],
                            'num' => $value['num'],
                            'total_price' => $value['total_price'],
                            'add_time' => time()
                        );
                        $excNum = $salemanOrderDetailModel->data($data)->add();
                        if ($excNum <= 0) {
                            $drugsCartModel->rollback();
                            responseJson(0, '订单提交失败');
                            exit;
                        }
                    }

                    //提交事务
                    $drugsCartModel->commit();

                    //返回
                    responseJson(1, 'success');
                    exit;
                    break;
                default:
                    break;
            }
            exit;
        }

        //查询当前用户购物车信息
        $drugsCartModel = M('drugs_cart');
        $where = array(
            'add_user_id' => $this->_salemanInfo['id'],
            'type' => 1
        );
        $cartList = $drugsCartModel->where($where)->select();
        $totalPrice = 0;

        if (is_array($cartList)) {
            //获取所有的药品ID集合
            $drugsIdList = array_keys(set_array_key($cartList, 'drugs_id'));

            //查询药品集合
            $drugsModel = M('drugs');
            $where = array(
                'id' => array('in', $drugsIdList)
            );
            $field = array(
                'id', 'name', 'price', 'is_valid'
            );
            $drugsList = $drugsModel->field($field)->where($where)->select();
            $drugsList = set_array_key($drugsList, 'id');

            //组装数据
            foreach ($cartList as $cart_key => $cart_value) {
                if (!isset($drugsList[$cart_value['drugs_id']]) || $drugsList[$cart_value['drugs_id']]['is_valid'] != 1) {
                    unset($cartList[$cart_key]);
                    continue;
                }
                $cartList[$cart_key]['price'] = $drugsList[$cart_value['drugs_id']]['price'];
                $cartList[$cart_key]['totalPrice'] = $drugsList[$cart_value['drugs_id']]['price'] * $cart_value['num'];
                $totalPrice+=$cartList[$cart_key]['totalPrice'];
            }
        }

        $this->assign(array(
            'cartList' => $cartList,
            'totalPrice' => $totalPrice
        ));
        $this->display();
    }

    /**
     * 消息推送
     */
    public function pushHistory() {
        $this->display();
    }

    /**
     * 工作汇总
     */
    public function workSummaryo() {
        $this->display();
    }

    /**
     * 个人设置
     */
    public function personData() {

        if (IS_POST) {
            $salemanInfo = $this->_salemanInfo;
            $salemanId = $salemanInfo['id'];

            //组装数据
            $data ['phone'] = intval($_POST ['phone']);
            $data ['real_name'] = $_POST ['real_name'];
            $data ['card_number'] = $_POST ['card_number'];
            $data ['sex'] = $_POST ['sex'];
            $data ['age'] = $_POST ['age'];

            $re = SalemanagerHelper::upload(); //附件上传(包括身份证正反面，头像),字段名称跟数据库字段一样即可
            if ($re) {
                foreach ($re as $key => $url) {
                    $data[$key] = $url;
                }
            }

            //修改
            $salemanModel = D('SaleMan\Saleman');
            $result = $salemanModel->EditSaleman($salemanId, $data);
            if ($result['code'] != 200) {
                $this->assign('code_msg', $result['msg']);
            }

            parent::_getSaleman();
        }

        $this->assign(
                array(
                    'salemanInfo' => $this->_salemanInfo
                )
        );
        $this->display();
    }

}
