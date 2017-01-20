<?php

namespace Home\Controller\Saleman;

use Think\Controller;

class PublicController extends Controller {

    public function UpCoordinate() {
        $userId = $_GET['uid'];
        $longitude = $_GET['longitude'];
        $latitude = $_GET['latitude'];
        if (empty($longitude)) {
            responseJson(0, '未获取到经度');
            exit;
        }
        if (empty($latitude)) {
            responseJson(0, '未获取到纬度');
            exit;
        }
        if (empty($userId)) {
            responseJson(0, '未获取到用户ID');
            exit;
        }

        $data = array(
            'uid' => $userId,
            'longitude' => $longitude,
            'latitude' => $latitude
        );
        session('saleman_coordinate', $data);

        responseJson(1, 'success');
        exit;
    }

}
