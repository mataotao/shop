<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/7/9
 * Time: 16:25
 */

namespace Home\Model;


use Think\Model;

class AddressModel extends Model{
	/**
	 * 开启批量验证
	 * @var bool
	 */
	protected $patchValidate = true;
	/**
	 * @var array 设置自动验证的条件
	 */
	protected $_validate = [
		['name' , 'require' , '收货人不能为空'] ,
		['province_id' , 'require' , '省不能为空'] ,
		['city_id' , 'require' , '城市不能为空'] ,
		['area_id' , 'require' , '区县不能为空'] ,
		['detail_address' , 'require' , '详细地址不能为空'] ,
		['tel' , 'require' , '手机号码不能为空'] ,
	];

	/**获取当前用户的所有地址
	 * 1.获取当前登录用户
	 * 2.通过条件查询并返回
	 * @return mixed
	 */
	public function getList(){
		$userinfo = login();
		return $this->where(['member_id' => $userinfo['id']])->select();
	}

	/**
	 * 添加地址
	 * 1.获取当前登录用户
	 * 2.设置要添加地址的用户为当前登用户
	 * 3.检测是否设置默认地址按钮是否选择
	 * 4.添加
	 * @return mixed
	 */
	public function addAddress(){
		$userinfo                = login();
		$this->data['member_id'] = $userinfo['id'];
		if($this->data['is_default']){
			$this->where(['member_id' => $userinfo['id']])->setField(['is_default' => 0]);
		}
		return $this->add();
	}

	/**
	 * 获取要修改的地址
	 * 1.接受传来的id
	 * 2.获取当前登录用户(安全)
	 * 3.通过条件查询并返回结果
	 * @param $id integer 要修改的地址信息
	 * @return mixed
	 */
	public function getAddressInfo($id,$field="*"){
		$userinfo = login();
		return $this->field($field)->where(['id' => $id , 'member_id' => $userinfo['id']])->find();
	}

	/**
	 * 修改地址
	 * 1.接受要修改的id
	 * 2.获取当前登录用户
	 * 3.检测是否设置默认地址按钮是否选择
	 * 4.通过条件修改数据
	 * @param $id integer
	 */
	public function editAddress($id){
		$userinfo = login();
		if($this->data['is_default']){
			$this->where(['member_id' => $userinfo['id']])->setField(['is_default' => 0]);
		}
		$this->where(['id' => $id])->save();
	}

	/**
	 * 设置默认地址
	 * 1.接受要修改的id
	 * 2.获取当前登录用户
	 * 3.设置当前用户所有的地址为非默认状态
	 * 4.根据条件设置对应的地址为默认地址
	 * @param $id integer 要设置的地址id
	 * @return bool
	 */
	public function defaultAddress($id){
		$userinfo = login();
		$this->where(['member_id' => $userinfo['id']])->setField(['is_default' => 0]);
		$cond = [
			'member_id' => $userinfo['id'] ,
			'id' => $id
		];
		return $this->where($cond)->setField(['is_default' => 1]);
	}
}