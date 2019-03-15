<?php
/**
 * Created by PhpStorm.
 * User: wolfbolin
 * Date: 2019/3/15
 * Time: 1:44
 */

namespace WolfBolin\Everyclass\Tools;

function week_encode($week_list) {
    // 自带去重排序效果（仅增强健壮性，不可依赖）
    $week_list = array_values(array_unique($week_list));
    // 判断异常
    if (count($week_list) == 0) {
        return "";
    }
    // 判断一周的课程
    if (count($week_list) == 1) {
        return $week_list[0] . '/全周';
    }
    // 判断连续的周次
    $dt = [];
    for ($i = 1; $i < count($week_list); $i++) {
        $dt [] = $week_list[$i] - $week_list[$i - 1];
    }
    $dt = array_values(array_unique($dt));
    if (count($dt) == 1) {
        // 说明周次是有规律的
        if ($dt[0] == 1) {
            return reset($week_list) . '-' . end($week_list) . '/全周';
        } elseif ($dt[0] == 2 && $dt[0] % 2 == 1) {
            return reset($week_list) . '-' . end($week_list) . '/单周';
        } elseif ($dt[0] == 2 && $week_list[0] % 2 == 2) {
            return reset($week_list) . '-' . end($week_list) . '/双周';
        }
    }
    // 完成相邻数字聚合
    $week_list [] = 999;
    $result = [];
    $beg = $week_list[0];
    for ($i = 1; $i < count($week_list); $i++) {
        if ($week_list[$i] - $week_list[$i - 1] != 1) {
            // 说明发生了不连续的情况
            if ($week_list[$i - 1] == $beg) {
                $result [] = $week_list[$i - 1];
            } else {
                $result [] = $beg . '-' . $week_list[$i - 1];
            }
            $beg = $week_list[$i];
        }
    }
    return join(',', $result);
}
