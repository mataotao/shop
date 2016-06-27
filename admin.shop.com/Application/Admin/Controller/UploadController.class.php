<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/6/27
 * Time: 17:48
 */

namespace Admin\Controller;
use Think\Controller;
use Think\Upload;

class UploadController extends Controller
{
    public function uploadImg(){
        $upload = new Upload(C('UPLOAD_SETTING'));
        $file_info = $upload->uploadOne($_FILES['file_data']);
        if($file_info){
            if($upload->driver=='Qiniu'){
                $file_url = $file_info['url'];
            }else{
                $file_url = BASE_URL.'/'.$file_info['savepath'].$file_info['savename'];
            }
            $return = [
                'file_url' => $file_url,
                'msg' => '上传成功',
                'status'=>1
            ];
        }else{
            $return = [
                'file_url' => "",
                'msg' => $upload->getError(),
                'status'=>0
            ];
        }
        $this->ajaxReturn($return);
    }

}