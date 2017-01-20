<?php

/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/4/7
 * Time: 上午11:42
 */
namespace Sms\Library;
interface BaseService
{
    public function sendTemplateSMS(array $params);
}