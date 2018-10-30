<?php
/**
 * [MyCMS] Ver:2.2 (C) 2018 mysqli.class.php Mr.Kwok
 * Created Time:2018/9/20 10:07
 */
if (!defined('IN_MYCMS')) {
    exit('Access Denied');
}

class MyCMS_DataBase
{
    private $link = null;
    public $querynum = 0;//统计查询数量

    public function __construct($host = null, $username = null, $password = null, $db = null, $port = null, $charset = 'utf8', $socket = null)
    {
        mb_internal_encoding('UTF-8');
        mb_regex_encoding('UTF-8');
        mysqli_report(MYSQLI_REPORT_STRICT);//抛出异常mysqli_sql_exception的方式替换警告错误。
        try {
            $this->link = new mysqli($host, $username, $password, $db, $port);
            $this->link->set_charset($charset);
        } catch (Exception $e) {
            die('未成功连接到数据库，请检查数据库配置信息！');
        }
    }

    //安全处理，对要写入的数据进行SQL安全处理，$user_name = $_MGLOBAL['db']->filter( $_POST['user_name'] );或者数组：$data = $_MGLOBAL['db']->filter( array( 'name' => $_POST['name'], 'email' => 'email@address.com' ))
    public function filter($data, $extra = true)
    {
        if (!is_array($data)) {
            $data = $this->link->real_escape_string($data);
            if ($extra) {
                $data = trim(htmlentities($data, ENT_QUOTES, 'UTF-8', false));;//当$extra=false时不对数据进行额外处理,默认删除空格并实体化html标签;
            }
        } else {
            $data = array_map(array($this, 'filter'), $data);//如果是数组的话回调自己函数处理
        }
        return $data;
    }

    //主查询结口
    public function query($query)
    {
        $full_query = $this->link->query($query);
        $this->querynum++;
        if ($this->link->error) {
            $this->halt($this->link->error, $this->link->errno, $query);
        } else {
            return $full_query;
        }
    }

    public function fetch_array($query, $result_type = MYSQLI_ASSOC)
    {
        return $query ? $query->fetch_array($result_type) : null;//查询返回数组
    }

    public function fetch_first($sql)
    {
        return $this->fetch_array($this->query($sql));//只返回第1行结果
    }

    //返回第几行结果，从0开始
    public function result($query, $row = 0)
    {
        if ($query) {
            $query->data_seek($row);
            $assocs = $query->fetch_row();
            return $assocs[0];
        } else {
            return null;
        }
    }

    //返回最新写入的数据ID
    public function insert_id()
    {
        return $this->link->insert_id;
    }

    public function __destruct()
    {
        if ($this->link) {
            $this->disconnect();//关闭mysqli连接
        }
    }

    //记录并根据用户组类型返回错误信息
    private function halt($message = '', $code = 0, $sql = '')
    {
        //查询出错管理员才能看到详细并记录到日志
        global $_MGLOBAL;
        errorlog('mysqlerror', json_encode(array('message' => $message, 'code' => $code, 'sql' => $sql, 'time' => $_MGLOBAL['timestamp'])), 0);
        if ($_MGLOBAL['member']['groupid'] == 1) {
            exit("<h1 style='margin:100px auto'> <b>MyCMS MYSQL error info</b>:<br />$message code= $code SQL= $sql </h1>");
        } else {
            exit('<h1>数据库查询错误并记录，请联系管理员处理！</h1>');
        }

    }

    //获取数目
    public function getcount($tablename, $wherearr, $get = 'COUNT(*)')
    {
        if (empty($wherearr)) {
            $wheresql = '1';
        } else {
            $wheresql = $mod = '';
            foreach ($wherearr as $key => $value) {
                $wheresql .= $mod . "`$key`='$value'";
                $mod = ' AND ';
            }
        }
        return $this->result($this->query("SELECT $get FROM " . tname($tablename) . " WHERE $wheresql"), 0);
    }

    //删除数据库表里内容
    public function deletetable($tablename, $wheresqlarr)
    {
        $this->query('DELETE FROM ' . tname($tablename) . ' WHERE ' . $this->getwheresql($wheresqlarr));
    }

    //插入数据库
    public function inserttable($tablename, $insertsqlarr, $returnid = 0, $replace = false, $silent = 0)
    {
        $insertkeysql = $insertvaluesql = $comma = '';
        foreach ($insertsqlarr as $insert_key => $insert_value) {
            $insertkeysql .= $comma . '`' . $insert_key . '`';
            $insertvaluesql .= $comma . '\'' . $this->filter($insert_value, false) . '\'';
            $comma = ', ';
        }
        $method = $replace ? 'REPLACE' : 'INSERT';
        $this->query($method . ' INTO ' . tname($tablename) . ' (' . $insertkeysql . ') VALUES (' . $insertvaluesql . ') ', $silent ? 'SILENT' : '');
        if ($returnid && !$replace) {
            return $this->insert_id();
        }
    }

    //更新数据库
    public function updatetable($tablename, $setsqlarr, $wheresqlarr)
    {
        $setsql = $comma = '';
        foreach ($setsqlarr as $set_key => $set_value) {
            $setsql .= $comma . $set_key . '=\'' . $set_value . '\'';
            $comma = ', ';
        }
        $this->query('UPDATE ' . tname($tablename) . ' SET ' . $setsql . ' WHERE ' . $this->getwheresql($wheresqlarr));
    }

    //组合查询语句
    private function getwheresql($wheresqlarr)
    {
        $result = $comma = '';
        if (empty($wheresqlarr)) {
            $result = '1';
        } elseif (is_array($wheresqlarr)) {
            foreach ($wheresqlarr as $key => $value) {
                $result .= $comma . $key . '=\'' . $this->filter($value, false) . '\'';
                $comma = ' AND ';
            }
        } else {
            $result = $wheresqlarr;
        }
        return $result;
    }

    //MYSQL版本信息
    public function version()
    {
        return $this->link->server_info;
    }

    // __destruct将会调用关闭数据库
    public function disconnect()
    {
        $this->link->close();
    }
}