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
function get_error(\Think\Model $model) {
    $errors = $model->getError();
    if (!is_array($errors)) {
        $errors = [$errors];
    }

    $html = '<ol>';
    foreach ($errors as $error) {
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
function arr2select(array $data, $value_name = 'id', $value_val = 'name', $name = '', $default_value = '') {
    $html = "<select name='" . $name . "' class='" . $name . "'>";
    $html .= "<option value='' >请选择</option>";
    foreach ($data as $val) {
        if ((string)$val["$value_name"] == $default_value) {
            $html .= "<option value='" . $val[$value_name] . "' selected='selected'>" . $val[$value_val] . "</option>";
        } else {
            $html .= "<option value='" . $val[$value_name] . "'>" . $val[$value_val] . "</option>";
        }
    }
    $html .= "</select>";
    return $html;
}

/**
 * 加盐加密
 * @param $salt
 * @param $password
 * @return string
 */
function salt_mcrypt($salt, $password) {
    return md5(md5($password) . $salt);
}

/** 存储用户登录信息
 * @param null $data
 * @return mixed
 */
function login($data = null) {
    if (is_null($data)) {
        return session('USERINFO');
    } else {
        session('USERINFO', $data);
    }
}

/**
 * 获取权限信息
 * @param null $paths
 * @return array|mixed|null
 */
function permission_pathes($paths = null) {
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
function permission_pids($pids = null) {
    if (is_null($pids)) {
        $pids = session('PERMISSION_IDS');
        if (!is_array($pids)) {
            $pids = [];
        }
        return $pids;
    } else {
        session('PERMISSION_IDS', $pids);
    }

}

/**
 * 发送邮件
 * @param $email
 * @param $subject
 * @param $content
 * @return array
 * @throws phpmailerException
 */
function sendMail($email, $subject, $content) {
    Vendor('PHPMailer.PHPMailerAutoload');
    $mail = new \PHPMailer;

    $mail->isSMTP();                                      // Set mailer to use SMTP
    $mail->Host = 'smtp.qq.com';  //填写发送邮件的服务器地址
    $mail->SMTPAuth = true;                               // 使用smtp验证
    $mail->Username = 'mt1014824538@vip.qq.com';                 // 发件人账号名
    $mail->Password = 'qtbdmhqeribqbchf';                           // 密码
    $mail->SMTPSecure = 'ssl';                            // 使用协议,具体是什么根据你的邮件服务商来确定
    $mail->Port = 465;                                    // 使用的端口

    $mail->setFrom('mt1014824538@vip.qq.com', 'ayiyayo');//发件人,注意:邮箱地址必须和上面的一致
    $mail->addAddress($email);     // 收件人

    $mail->isHTML(true);                                  // 是否是html格式的邮件

    $mail->Subject = $subject;//标题
    $mail->Body = $content;//正文
    $mail->CharSet = 'UTF-8';

    if ($mail->send()) {
        return [
            'status' => 1,
            'msg' => '发送成功',
        ];
    } else {
        return [
            'status' => 0,
            'msg' => $mail->ErrorInfo,
        ];

    }
}

function get_redis() {
    $redis = new Redis();
    $redis->connect(C('REDIS_HOST'), C('REDIS_PORT'));
    return $redis;
}

function locate_number_format($number) {
    return number_format($number, 2, ".", "");
}
