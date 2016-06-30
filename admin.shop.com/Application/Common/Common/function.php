<?php

/**
 * 将模型的错误信息转换成一个有序列表。
 * @param \Think\Model $model 模型对象
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
function arr2select(array $data,$value_name='id',$value_val='name',$name='',$default_value=''){
    $html = "<select name='".$name."' class='".$name."'>";
    $html .= "<option value='' >请选择</option>";
    foreach ($data as $val){
        if($val["$value_name"]==$default_value){
            $html.="<option value='".$val[$value_name]."' selected='selected'>".$val[$value_val]."</option>";
        }else{
            $html.="<option value='".$val[$value_name]."'>".$val[$value_val]."</option>";
        }
    }
    $html.="</select>";
    return $html;
}
