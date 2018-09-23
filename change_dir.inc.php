<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/23
 * Time: 10:23
 */
function url_to_absolute($base,$relative='')
{
    $base=trim($base);//去两边空格
    if(substr(trim($relative),0,1)=='/'){
        return "不是相对路径！";
    }
    $base=str_replace("\\","/",$base);//替换\为/
    $base_last=$base{strlen($base)-1};//字符串最后一个
    if($base_last=='/'){
        $base=substr($base,0,-1);//去除最后一个/
    }
    $info=explode("/",$base);
    $num=preg_match_all("/\.\.\//",$relative,$match);//匹配../个数
    $relative=str_replace("../","",$relative);//去除../
    $relative=trim($relative);
    if($num){
        //上级目录
        if($info[0]==''){
            $info[0]='/';
        }
        if($num<count($info)){
            for($i=0;$i<$num;$i++){
                array_pop($info);//去除数组最后一个
            }
        }else{
            return "无效目录!";
        }
        return implode("/",$info)."/".$relative;
    }else{
        //下级目录
        $relative=str_replace("./","",$relative);//去除./
        return $base."/".$relative;
    }
}