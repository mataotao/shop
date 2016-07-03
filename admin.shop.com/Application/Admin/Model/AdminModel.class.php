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

class AdminModel extends Model
{
    protected $patchValidate=true;

    protected $_validate=[
      ['username','require','管理员名称不能为空',self::EXISTS_VALIDATE,"",'register'],
      ['username','','管理员名称以被占用',self::EXISTS_VALIDATE,'unique',],
      ['password','6,16','密码长度不合法',self::EXISTS_VALIDATE,'length','register'],
      ['password','require','密码不能为空',self::EXISTS_VALIDATE,"",'register'],
      ['repassword','password','两次密码不一样',self::EXISTS_VALIDATE,'confirm','register'],
      ['repassword','require','重复密码不能为空',self::EXISTS_VALIDATE,"",'register'],
      ['email','email','邮箱不合法',self::EXISTS_VALIDATE,"",'register'],
      ['email','require','邮箱不能为空',self::EXISTS_VALIDATE,"",'register'],
      ['email','','邮箱已被占用',self::EXISTS_VALIDATE,'unique','register'],
      ['repassword','password','两次密码不一样',self::EXISTS_VALIDATE,'confirm','resetpassword'],

    ];
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
    protected $_auto=[
        ['password','repassword','resetpassword','callback'],
        ['salt','\Org\Util\String::randString','register','function'],
        ['salt','\Org\Util\String::randString','resetpassword','function'],
        ['add_time',NOW_TIME,'register']
    ];
    public function addAdmin(){
        //管理员表
        $this->startTrans();
        $this->data['password']=salt_mcrypt($this->data['salt'],$this->data['password']);
        unset($this->data[$this->getPk()]);
        $id = $this->add();
        if($id===false){
            $this->error='管理员信息添加失败';
            $this->rollback();
            return false;
        }//管理员角色表
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
            if($admin_role_model->addAll($data)===false){
                $this->error='管理员角色关联添加失败';
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }

    public function getInfo($id){
        //管理员表
        $row = $this->find($id);
        //管理员角色表
        $admin_role_model = M('AdminRole');
        $row['role_ids']=json_encode($admin_role_model->where(['admin_id'=>$id])->getField('role_id',true));
        return $row;
    }

    public function editAdmin($id){
        $admin_role_model = M('AdminRole');
        if($admin_role_model->where(['admin_id'=>$id])->delete()===false){
            $this->error='管理员历史角色关联删除失败';
            $this->rollback();
            return false;
        }
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

    }

    public function deleteAdmin($id){
        $this->startTrans();
        if($this->delete($id)===false){
            $this->error='管理员信息删除失败';
            $this->rollback();
            return false;
        }
        $admin_role_model = M('AdminRole');
        if($admin_role_model->where(['admin_id'=>$id])->delete()===false){
            $this->error='管理员角色关联删除失败';
            $this->rollback();
            return false;
        }
        $this->commit();
        return true;

    }

    protected function repassword($password){
        if($password){
            return $password;
        }

        $string = new String();
        $str = $string::randString(10);
        return $str;

    }
    
    public function resetAdmin(){
        $row['password'] = $this->data['password'];
        $this->data['password']=salt_mcrypt($this->data['salt'],$this->data['password']);
        if($this->save()===false){
            $this->error='重置密码失败';
            return false;
        }
        return $row;
    }
}