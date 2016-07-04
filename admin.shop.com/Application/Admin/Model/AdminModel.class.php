<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/2
 * Time: 11:03
 */

namespace Admin\Model;


use Org\Util\String;
use Think\Model;
use Think\Page;
use Think\Verify;

class AdminModel extends Model
{
    //开启批量验证
    protected $patchValidate=true;
    //自动验证条件
    protected $_validate=[
      ['username','require','管理员名称不能为空'],
      ['username','','管理员名称以被占用',self::EXISTS_VALIDATE,'unique','register'],
      ['password','6,16','密码长度不合法',self::EXISTS_VALIDATE,'length','register'],
      ['password','require','密码不能为空',self::EXISTS_VALIDATE,"",'register'],
      ['repassword','password','两次密码不一样',self::EXISTS_VALIDATE,'confirm','register'],
      ['repassword','require','重复密码不能为空',self::EXISTS_VALIDATE,"",'register'],
      ['email','email','邮箱不合法',self::EXISTS_VALIDATE,"",'register'],
      ['email','require','邮箱不能为空',self::EXISTS_VALIDATE,"",'register'],
      ['email','','邮箱已被占用',self::EXISTS_VALIDATE,'unique','register'],
      ['repassword','password','两次密码不一样',self::EXISTS_VALIDATE,'confirm','resetpassword'],
     // ['captcha','checkCaptcha','验证码不正确',self::EXISTS_VALIDATE,'callback'],

    ];

    /**
     * 搜索分页
     * @param array $cond 条件
     * @return mixed 执行结果
     */
    public function getPageResult(array $cond=[]){
        //合并条件
        $cond = array_merge(['status'=>1],$cond);
        //获取分页配置
        $pageSetting = C('PAGE_SETTING');
        //通过条件获取总条数
        $count = $this->where($cond)->count();
        //实例化
        $page = new Page($count,$pageSetting['PAGE_SIZE']);
        //设置要显示的内容
        $page->setConfig('theme',$pageSetting['PAGE_THEME']);
        //获取html代码
        $page_html = $page->show();
        //获取分页的数据
        $rows = $this->where($cond)->page(I('get.p',1),$pageSetting['PAGE_SIZE'])->select();
        //返回结果
        return compact('rows','page_html');

    }
    //自动完成
    protected $_auto=[
        ['password','repassword','resetpassword','callback'],
        ['salt','\Org\Util\String::randString','register','function'],
        ['salt','\Org\Util\String::randString','resetpassword','function'],
        ['add_time',NOW_TIME,'register']
    ];

    /**
     * 添加管理员
     * @return bool 执行结果
     */
    public function addAdmin(){
        //管理员表
        $this->startTrans();
        //调用加盐加密函数完成加盐加密
        $this->data['password']=salt_mcrypt($this->data['salt'],$this->data['password']);
        //删除主键
        unset($this->data[$this->getPk()]);
        //获取id
        $id = $this->add();
        if($id===false){
            $this->error='管理员信息添加失败';
            $this->rollback();
            return false;
        }//管理员角色表
        //获取选中的角色
        $roles = I('post.role_id');
        $data=[];
        foreach ($roles as $role){
            $data[]=[
                'admin_id'=>$id,
                'role_id'=>$role
            ];
        }
        if($data){
            $admin_role_model = M('AdminRole');
            //添加到管理员角色表
            if($admin_role_model->addAll($data)===false){
                $this->error='管理员角色关联添加失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }

    /**
     * 获取要编辑的数据
     * @param $id integer 要编辑的id
     * @return mixed 执行结果
     */
    public function getInfo($id){
        //管理员表
        $row = $this->find($id);
        //管理员角色表
        $admin_role_model = M('AdminRole');
        //转换成json
        $row['role_ids']=json_encode($admin_role_model->where(['admin_id'=>$id])->getField('role_id',true));
        return $row;
    }

    /**
     * 编辑
     * @param $id int 编辑的id
     * @return bool 执行结果
     */
    public function editAdmin($id){
        //管理员角色关联表
        $admin_role_model = M('AdminRole');
        //先删除在添加
        if($admin_role_model->where(['admin_id'=>$id])->delete()===false){
            $this->error='管理员历史角色关联删除失败';
            $this->rollback();
            return false;
        }
        //获取选中角色
        $roles = I('post.role_id');
        $data=[];
        foreach ($roles as $role){
            $data[]=[
                'admin_id'=>$id,
                'role_id'=>$role
            ];
        }
        if($data){
            if($admin_role_model->addAll($data)===false){
                $this->error='管理员角色关联添加失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }

    /**
     * 删除数据
     * @param $id int 删除的id
     * @return bool 执行结果
     */
    public function deleteAdmin($id){
        $this->startTrans();
        //删除基本信息
        if($this->delete($id)===false){
            $this->error='管理员信息删除失败';
            $this->rollback();
            return false;
        }
        //管理员角色关联表
        $admin_role_model = M('AdminRole');
        //删除关联信息
        if($admin_role_model->where(['admin_id'=>$id])->delete()===false){
            $this->error='管理员角色关联删除失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;

    }

    /**
     * @param $password string 输入的密码
     * @return string 密码
     */
    protected function repassword($password){
        //判断有没有输入,如果有直接返回输入的
        if($password){
            return $password;
        }
            
        $string = new String();
        //获取随机字符串长度为10
        $str = $string::randString(10);
        return $str;

    }

    /**
     * 重置密码
     * @return bool 执行结果
     */
    public function resetAdmin(){
        //保存没有加密的密码,方便传送给用户
        $row['password'] = $this->data['password'];
        $this->data['password']=salt_mcrypt($this->data['salt'],$this->data['password']);
        if($this->save()===false){
            $this->error='重置密码失败';
            return false;
        }
        return $row;
    }

    /**
     * @param $captcha string 输入的验证码
     * @return bool
     */
    public function checkCaptcha($captcha){
        $verify = new Verify();
        return $verify->check($captcha);
    }
    public function login(){
        $username = $this->data['username'];
        $password = $this->data['password'];
        //通过用户名获取对应的信息
        $userInfo = $this->getByUsername($username);
        if(!$username){
            $this->error='用户名或密码错误';
            return false;
        }
        //把用户输入的密码加盐加密后对比
        if(salt_mcrypt($userInfo['salt'],$password)!=$userInfo['password']){
            $this->error='密码错误';
            return false;
        }
        //设置最后登录时间和ip保存到数据表
        $cond=[
            'id'=>$userInfo['id'],
            'last_login_time'=>NOW_TIME,
            'last_login_ip'=>get_client_ip(1)
        ];
        $this->save($cond);
        //保存用户信息
         login($userInfo);

        //SELECT DISTINCT path from admin_role as ar JOIN role_permission as rp on ar.role_id=rp.role_id join permission as p on p.id=rp.permission_id where admin_id=2 and path<>''
//       $paths = M()->distinct()->table('admin_role')->alias('ar')->field('path')->join('__ROLE_PERMISSION__ as rp on ar.role_id=rp.role_id')->join('__PERMISSION__ as p on p.id=rp.permission_id')->where("admin_id={$userInfo['id']} and path<>''")->select();
//        $table = [
//            'admin_role'=>'ar',
//            'role_permission'=>'rp',
//            'permission'=>'p',
//        ];
//        $cond=[];
//        $cond=[
//            'ar.role_id'=>['exp','=rp.role_id'],
//            'p.id'=>['exp','=rp.permission_id'],
//            'ar.admin_id'=>['exp',"={$userInfo['id']}"],
//            'p.path' => ['neq',""]
//
//        ];
//        $paths = M()->distinct()->table($table)->field('path')->where($cond)->select();
//
//
//        dump(M()->getLastSql());exit;
        //获取对应的权限通过id
        $this->getPermissions($userInfo['id']);
        $admin_token_model = M('AdminToken');
        $admin_token_model->delete($userInfo['id']);
        //判断是否记住密码
        if(I('post.remember')){
            //设置cookie数据
            $data = [
              'admin_id'=>$userInfo['id'],
              'token'=>\Org\Util\String::randString(40),
            ];
            cookie('USER_AUTO_LOGIN_TOKEN',$data,604800);
            //把数据添加到对应的数据表方便对比
            $admin_token_model->add($data);
        }

//        $data=cookie('USER_AUTO_LOGIN_TOKEN');
//      dump($data);exit;

        return true;

    }


    public function getPermissions($admin_id){
        //设置要查询的表和别名
        $table = [
            'admin_role'=>'ar',
            'role_permission'=>'rp',
            'permission'=>'p',
        ];
        $cond=[];
        //设置查询条件
        $cond=[
            'ar.role_id'=>['exp','=rp.role_id'],
            'p.id'=>['exp','=rp.permission_id'],
            'ar.admin_id'=>['exp',"={$admin_id}"],
            'p.path' => ['neq',""]

        ];
        $permissions = M()->distinct()->table($table)->field('path,p.id')->where($cond)->select();
        //dump($permissions);exit;
        $path=[];
        $pid=[];
        foreach ($permissions as $permission){
            $path[]= $permission['path'];
            $pid[]= $permission['id'];
        }
        //dump($permissions);
        //保存权限id
        permission_pids($pid);
        //保存权限路径
        permission_pathes($path);
        return true;

    }

    /**
     * 自动登录
     * @return bool|mixed
     */
    public function autologin()
    {
        //获取cookie
        $data=cookie('USER_AUTO_LOGIN_TOKEN');
        if(!$data){
            return false;
        }
        //调用数据表的数据来和cookie对比
        $admin_token_model = M('AdminToken');
        if($admin_token_model->where($data)->count()===false){
            return false;
        }
        //删除上次的cookie数据
        $admin_token_model->delete($data['admin_id']);
        //设置条件储存新的
            $data = [
                'admin_id'=>$data['admin_id'],
                'token'=>\Org\Util\String::randString(40),
            ];
            cookie('USER_AUTO_LOGIN_TOKEN',$data);
            $admin_token_model->add($data);
        //查询到用户信息
        $userInfo = $this->find($data['admin_id']);
        //保存
        login($userInfo);
        //获取到所有的权限
        $this->getPermissions($userInfo['id']);
        return $userInfo;

    }
}