<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 2017-08-20
 * Time: 1:44 PM
 */
header("Content-type:text/html;charset=utf-8");





$area = array(
    array('id'=>1,'name'=>'山东','parent'=>0),
    array('id'=>2,'name'=>'青岛','parent'=>1),
    array('id'=>3,'name'=>'东城区派出所','parent'=>6),
    array('id'=>4,'name'=>'西安','parent'=>5),
    array('id'=>5,'name'=>'陕西','parent'=>0),
    array('id'=>6,'name'=>'东城区','parent'=>4),
    array('id'=>7,'name'=>'市南区','parent'=>2),
    array('id'=>8,'name'=>'市南区市政府','parent'=>7)
);

/**
 * 取出所有层的子分类
 * @param array $arr
 * @param int $id
 * @param int $level
 * @return array
 */
function subTree($arr,$id=0,$level=1)
{
    static $subArr = [];  //static = 只初始化一次

    foreach($arr as $v)
    {
        if($v['parent']==$id)
        {
            //$v['level']=$level;
            $subArr[] = subTree($arr,$v['id'],$level+1);
        }else{
            return $v;
        }
    }
   // return $subArr;
}


//例子
$arr = subTree($area);
print_r($arr);
//foreach($arr as $v)
//{
//    echo str_repeat("&nbsp;&nbsp;",$v['level']).$v['name']."<br>";
//}
?>