<?php
/**
 * Created by PhpStorm.
 * User: mttm
 * Date: 2016/6/28
 * Time: 11:30
 */

namespace Admin\Logic;


class MySQLLogic implements DbMysql
{
    public function connect()
    {
        // TODO: Implement connect() method.
        echo __METHOD__;
        dump(func_get_args());
        echo "<hr>";
    }

    public function disconnect()
    {
        // TODO: Implement disconnect() method.
        echo __METHOD__;
        dump(func_get_args());
        echo "<hr>";
    }

    public function free($result)
    {
        // TODO: Implement free() method.
        echo __METHOD__;
        dump(func_get_args());
        echo "<hr>";
    }

    /**
     * 执行简单的sql语句
     * @param string $sql
     * @param array $args
     * @return mixed
     */
    public function query($sql, array $args = array())
    {
        // TODO: Implement query() method.\
        $args = func_get_args();//获所有传入的参数
        $sql = array_shift($args);//弹出数组的第一个数据
        $params = preg_split('/\?[NFT]/',$sql); //正则匹配，并通过匹配到的分割成一个数组
        $sql = ''; //从新设置sql（前面的sql(变量),已经没有用）
        foreach ($params as $key=>$value){  //遍历数组
            $sql.=$value.$args[$key];  //拼接完整的sql语句
        }
        return M()->execute($sql);  //因为是写语句，所以esecute执行比较好

    }

    /**
     * 新增一条数据
     * @param string $sql
     * @param array $args
     * @return bool
     */
    public function insert($sql, array $args = array())
    {
        // TODO: Implement insert() method.
        $args = func_get_args();//获取所有参数
        $sql = $args[0]; //获取二维数组的第一个数据
        $table_name = $args[1];//获取二维数组的第二个数据
        $params = $args[2];//获取二维数组的第三个数据
        $sql = str_replace('?T',$table_name,$sql); //替换字符串
        $tmp = [];//设置一个空数组来保存遍历后的插入部分
        foreach ($params as $key=>$val){//遍历数组
            $tmp[].=$key."='".$val."'";//把要插入的数据放到数组中
        }
        $sql = str_replace('?%',implode(",",$tmp),$sql); //tmp数组通过逗号连接成字符串然后在替穿sql字符串穿?%部分
        if(M()->execute($sql)!==false){ //判断执行的结果，因为是写语句，所以esecute执行比较好
            return M()->getLastInsID(); //返回插入的id
        }else{
            return false;//返回错误
        }
    }

    public function update($sql, array $args = array())
    {
        // TODO: Implement update() method.
        echo __METHOD__;
        dump(func_get_args());
        echo "<hr>";
    }

    public function getAll($sql, array $args = array())
    {
        // TODO: Implement getAll() method.
        echo __METHOD__;
        dump(func_get_args());
        echo "<hr>";
    }

    public function getAssoc($sql, array $args = array())
    {
        // TODO: Implement getAssoc() method.
        echo __METHOD__;
        dump(func_get_args());
        echo "<hr>";
    }

    /**
     * 获取一行数据
     * @param string $sql
     * @param array $args
     * @return mixed
     */
    public function getRow($sql, array $args = array())
    {
        // TODO: Implement getRow() method.
        $args = func_get_args();//获取传入的所有参数
        $sql = array_shift($args);//弹出数组中的第一个
        $params = preg_split('/\?[NFT]/',$sql);//用正则表达式分割字符串成一个数组
        array_pop($params);//弹出数组中的最后一个
        $sql='';//从新设置sql（前面的sql(变量),已经没有用）
        foreach ($params as $key=>$val){ //遍历数组
            $sql.=$val.$args[$key];  //拼凑完整的sql语句
        }
        $rows = M()->query($sql); //因为是读语句，所以query执行比较好
        return array_shift($rows);  //弹出一行数据

    }

    public function getCol($sql, array $args = array())
    {
        // TODO: Implement getCol() method.
        echo __METHOD__;
        dump(func_get_args());
        echo "<hr>";
    }

    /**
     * 获取第一行一个字段的值
     * @param string $sql
     * @param array $args
     * @return mixed
     */
    public function getOne($sql, array $args = array())
    {
        // TODO: Implement getOne() method.
        $args = func_get_args();//获取传入的所有参数
        $sql = array_shift($args);//弹出数组中的第一个
        $params = preg_split('/\?[FT]/',$sql);//用正则匹配来分割字符串成数组
        $sql='';//变量sql已经没有用了，可以重新设置
        foreach ($params as $key=>$value){  //遍历数组
            $sql.=$value.$args[$key];  //拼凑完整的sql语句
        }
        $rows = M()->query($sql);  //因为是读语句，所以用query执行
         $row=array_shift($rows); //弹出第一行数据
        return array_shift($row); //弹出第一格数据
    }

}