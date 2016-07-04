<?php

/**
 * 将模型的错误信息转换成一个有序列表。
 * @param \Think\Model $model 模型对象
 * @return string
 */

/**
 * 通过模型获取错误
 * @param \Think\Model $model
 * @return string
 */
function get_error(\Think\Model $model){
    $errors = $model->getError();
    if(!is_array($errors)){
        $errors = [$errors];
    }
    
    $html = '<ol>';
    foreach($errors as $error){
        $html .= '<li>' . $error . '</li>';
    }
    $html .= '</ol>';
    return $html;
    
}

/**
 * 下拉菜单
 * @param array $data 数据
 * @param string $value_name
 * @param string $value_val
 * @param string $name
 * @param string $default_value
 * @return string
 */
function arr2select(array $data,$value_name='id',$value_val='name',$name='',$default_value=''){
    $html = "<select name='".$name."' class='".$name."'>";
    $html .= "<option value='' >请选择</option>";
    foreach ($data as $val){
        if((string)$val["$value_name"]==$default_value){
            $html.="<option value='".$val[$value_name]."' selected='selected'>".$val[$value_val]."</option>";
        }else{
            $html.="<option value='".$val[$value_name]."'>".$val[$value_val]."</option>";
        }
    }
    $html.="</select>";
    return $html;
}

/**
 * 加盐加密
 * @param $salt
 * @param $password
 * @return string
 */
function salt_mcrypt($salt,$password){
    return md5(md5($password).$salt);
}

/** 存储用户登录信息
 * @param null $data
 * @return mixed
 */
function login($data=null){
    if(is_null($data)){
        return session('USERINFO');
    }else{
         session('USERINFO',$data);
    }
}

/**
 * 获取权限信息
 * @param null $paths
 * @return array|mixed|null
 */
function permission_pathes($paths=null)
{
    if (is_null($paths)) {
        $paths = session('PERMISSION_PATHES');
        if (!is_array($paths)) {
            $paths = [];
        }
        return $paths;
    } else {
        session('PERMISSION_PATHES', $paths);
    }
}
    //permission_pids
/**
 * 获取权限id
 *
 * @param null $pids
 * 
 * @return array|mixed|null
 */
    function permission_pids($pids=null){
        if(is_null($pids)){
            $pids = session('PERMISSION_IDS');
            if(!is_array($pids)){
                $pids=[];
            }
            return $pids;
        }else{
            session('PERMISSION_IDS',$pids);
        }

}
