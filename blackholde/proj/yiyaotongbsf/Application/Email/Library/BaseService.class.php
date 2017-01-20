<?php

/**
 * Created by PhpStorm.
 * User: mafengli
 * Date: 16/4/7
 * Time: 上午11:42
 */
namespace Email\Library;
interface BaseService
{
    public function sendTemplateEmail(array $params);
}