<?php

// arModule('Func')->test();

class FuncModule
{
    public function test()
    {
        echo 'test module';

    }


    // 对时间处理
    public function viewTime($viewTime)
    {
        if ($viewTime < 3601) {
            $viewTime = $viewTime / 60;
            $result = floor($viewTime) . '分钟前';
        } elseif ($viewTime > 3600 && $viewTime < 86401) {
            $viewTime = $viewTime / 60 / 60;
            $result = floor($viewTime) . '小时前';
        } elseif ($viewTime > 86400 && $viewTime < 172801) {
            $result = '昨天';
        } else {
            $viewTime = $viewTime / 60 / 60 / 24 - 1;
            $result = floor($viewTime) . '天前';
        }

        return $result;

    }

}
