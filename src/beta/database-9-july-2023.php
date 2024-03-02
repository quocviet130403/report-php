<?php
require_once('config.php');

class Database
{
    private $db_name;
    private $db_user;
    private $db_pass;
    private $db_host;
    private $connection;

    function __construct()
    {
        $this->db_name = DB_NAME;
        $this->db_user = DB_USER;
        $this->db_pass = DB_PASS;
        $this->db_host = DB_HOST;

        $dsn = "mysql:host=" . $this->db_host . ";dbname=" . $this->db_name . ";charset=utf8";
        $this->connection = null;

        try {
            $this->connection = new PDO($dsn, $this->db_user, $this->db_pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    //Close Database connection
    function close()
    {
        $this->connection = null;
    }

    //Get connection
    function get_connection()
    {
        return $this->connection;
    }

    //Write to database
    //$info as 2d array: each row represents tuple 
    //and column represents key value in database.
    function write_data($info, $table, $replace = false, $get_id = false)
    {
        $sql = "";
        if ($replace) {
            $sql .= "REPLACE INTO $table(";
        } else {
            $sql .= "INSERT INTO $table(";
        }

        foreach ($info as $key) {
            $sql .= $key[0] . ", ";
        }
        $sql[strlen($sql) - 2] = ")";
        $sql .= " VALUES(";
        foreach ($info as $value) {
            $sql .= "?, ";
        }
        $sql[strlen($sql) - 2] = ")";
        $add_data = $this->connection->prepare($sql);

        $value_count = 1;
        foreach ($info as $value) {
            $add_data->bindParam($value_count, $value[1]);
            $value_count++;
        }
        if ($add_data->execute()) {
            if ($get_id === true)
                return $this->connection->lastInsertId();
            else
                return true;
        }
        return false;
    }

    //Retrieve single item from Database if exists identified by key/value.
    public function get_data($key, $val, $table, $get = false)
    {
        $check_data = $this->connection->prepare("SELECT * FROM $table WHERE $key=?");
        $check_data->bindParam(1, $val);
        $check_data->execute();
        if ($check_data->rowCount() > 0) {
            if ($get) {
                return $check_data->fetch(PDO::FETCH_ASSOC);
            } else {
                return true;
            }
        } else return false;
    }
    
     public function get_data_new($key, $val, $table, $get = true)
    {
        $check_data = $this->connection->prepare("SELECT * FROM $table WHERE $key=$val");
        $check_data->bindParam(1, $val);
        $check_data->execute();
        if ($check_data->rowCount() > 0) {
            if ($get) {
                return $check_data->fetch(PDO::FETCH_ASSOC);
            } else {
                return true;
            }
        } else return false;
    }
    
    

    public function get_data_multiple($key, $val, $table, $get = false)
    {
        $check_data = $this->connection->prepare("SELECT * FROM $table WHERE $key IN ($val)");
        $check_data->execute();
        if ($check_data->rowCount() > 0) {
            return $check_data->fetchAll(PDO::FETCH_ASSOC);

        } else return false;
    }

    public function update_data_multiple($key, $val, $table)
    {
        $up_data = $this->connection->prepare("UPDATE $table SET assigned = 1 WHERE $key IN ($val)");
        $up_data->execute();
        if ($up_data->execute()) {
            $this->update_data_multiple_2('company');
            return true;
        }
        return false;
    }

    public function update_data_multiple_2($table)
    {
        $consultant_companies = $this->get_multiple_data(false, false, 'consultant');
        if (!empty($consultant_companies)){
            $consultant_companies_ids = '';
            foreach ($consultant_companies as $consultant) {
                $consultant_companies_ids .= $consultant['consultant_companies'] . ',';
            }
            if (!empty($consultant_companies_ids)) {
                $consultant_companies_array = explode(',', $consultant_companies_ids);
                if (is_array($consultant_companies_array) && !empty($consultant_companies_array)) {
                    $ids_str = "'" . implode("','", $consultant_companies_array) . "'";
                    $up_data = $this->connection->prepare("UPDATE company SET assigned = 0 WHERE company_id NOT IN ($ids_str)");
                }
            } else {
                $up_data = $this->connection->prepare("UPDATE company SET assigned = 0");
            }
            $up_data->execute();

        }
        if ($up_data->execute()) {
            return true;
        }
        return false;
    }

    //Retrieve multiple items from Database if exists identified by key/value.
    public function get_multiple_data($key, $val, $table, $comp_op = '=', $get = true, $order = false, $limit = false)
    {
        $check_data = '';

        $limit_clause = '';
        $order_clause = '';
        $where_clause = '';
        if ($order) {
            $order_clause = " ORDER BY $order";
        }
        if ($limit) {
            $limit_clause = " LIMIT $limit";
        }
        if ($key !== false && $val !== false) {
            $where_clause = " WHERE $key $comp_op :value";
        }

        $check_data = $this->connection->prepare("SELECT * FROM $table" . $where_clause . $order_clause . $limit_clause . ";");

        if ($key !== false && $val !== false) {
            $check_data->bindParam(":value", $val);
        }

        $check_data->execute();
        if ($check_data->rowCount() > 0) {
            if ($get) {
                return $check_data->fetchAll(PDO::FETCH_ASSOC);
            } else {
                return true;
            }
        } else return false;
    }

    //Update data
    public function update_data($info, $id, $value, $table)
    {
        $sql = "";
        $count = 0;
        $sql .= "UPDATE $table SET ";

        foreach ($info as $key) {
            $sql .= $key[0] . " = :value" . $count . ",";
            $count++;
        }

        $sql[strlen($sql) - 1] = ' ';

        if ($info && $id) {
            $sql .= "WHERE " . $id . " = :id_value";
        }

        $up_data = $this->connection->prepare($sql);

        for ($x = 0; $x < count($info); $x++) {
            $up_data->bindParam(':value' . $x, $info[$x][1]);
        }
        $up_data->bindParam(':id_value', $value);

        if ($up_data->execute()) {
            return true;
        }
        return false;
    }
    
    
        //Update data
    public function update_data_check($info, $id, $sec, $value,$secvalue, $table)
    {
        $sql = "";
        $count = 0;
        $sql .= "UPDATE $table SET ";

        foreach ($info as $key) {
            $sql .= $key[0] . " = :value" . $count . ",";
            $count++;
        }

        $sql[strlen($sql) - 1] = ' ';

        if ($info && $id) {
            $sql .= "WHERE " . $id . " = :id_value";
        }
        
        if ($info && $sec) {
            $sql .= " AND " . $sec . " = :sec_value";
        }

        $up_data = $this->connection->prepare($sql);
        
        //echo $sql;
        //die();

        for ($x = 0; $x < count($info); $x++) {
            $up_data->bindParam(':value' . $x, $info[$x][1]);
        }
        $up_data->bindParam(':id_value', $value);
        
        $up_data->bindParam(':sec_value', $secvalue);

        if ($up_data->execute()) {
            return true;
        }
        return false;
    }

    //Execute query
    public function executeQuery($sql)
    {

        $statement = $this->connection->prepare($sql);
        if ($statement->execute()) {
            return true;
        } else {
            return false;
        }
    }

    //Delete item form database identified by key/value
    public function delete_data($key, $val, $table)
    {
        $delete_data = $this->connection->prepare("DELETE FROM $table WHERE $key=?");
        $delete_data->bindParam(1, $val);
        if ($delete_data->execute()) {
            return true;
        } else return false;
    }

    public function get_terms_condition($lang)
    {
        $result = "";
        $check_data = $this->connection->prepare("SELECT * FROM tos WHERE tos_company_id is null and lang_code=?");
        $check_data->bindParam(1, $_SESSION['trans']);
        $check_data->execute();
        $tos_data = null;
        if ($check_data->rowCount() > 0) {
            $tos_array = $check_data->fetchAll(PDO::FETCH_ASSOC);
            if ($tos_array) {
                foreach ($tos_array as $single_tos) {
                    $tos_data = $single_tos;
                }
            }
        }

        return $tos_data;
    }

    public function get_backoffice_email()
    {
        $result = "";
        $check_data = $this->connection->prepare("SELECT admin_email FROM admin limit 1");
        $check_data->execute();
        $email = '';
        if ($check_data->rowCount() > 0) {
            $adminData = $check_data->fetchAll(PDO::FETCH_ASSOC);
            if ($adminData) {
                foreach ($adminData as $admin) {
                    $email = $admin['admin_email'];
                }
            }
        }

        return $email;
    }

    public function get_name_by_id($type, $field, $key)
    {

        $query = "";
        $result = "";
        if ($type == 'company') {
            $query = "select " . $field . " from company where company_id=" . $key;
        } elseif ($type == 'user') {
            $query = "select " . $field . " from user where user_id=" . $key;
        }elseif($type =='consultant')
        {
           $query = "select " . $field . " from consultant where consultant_id=" . $key;
  
        }
        $check_data = $this->connection->prepare($query);
        //$check_data -> bindParam("val", $lang);
        $check_data->execute();
        $result = null;
        if ($check_data->rowCount() > 0) {
            $data = $check_data->fetch(PDO::FETCH_ASSOC);
            $result = $data[$field];
        }
        return $result;
    }

    public function createNotificationForUser($primaryId, $table, $notification)
    {
        // create notification
        $info = array(
            array('ref_id', $primaryId),
            array('ref_type', $table),
            array('message_en', $notification['en']),
            array('message_nor', $notification['nor']),
            array('timestamp', Date("Y-m-d H:m:i"))
        );

        $this->write_data($info, 'notifications');
    }


    //Retrieve Notification
    /*
    public function get_notification($ref_id, $ref_type, $noti_type, $get = false)
    {
        $check_data = $this->connection->prepare(
            "SELECT * FROM notifications"
            . " WHERE ref_id=? and ref_type=? and noti_type=? and status='New'");
        $check_data->bindParam(1, $ref_id);
        $check_data->bindParam(2, $ref_type);
        $check_data->bindParam(3, $noti_type);
        $check_data->execute();
        if ($check_data->rowCount() > 0) {
            if ($get) {
                return $check_data->fetch(PDO::FETCH_ASSOC);
            } else {
                return true;
            }
        } else {
            return false;
        }
    }
    */

    //get industry type data

    public function get_industry_content()
    {
        $result = "";
        $check_data = $this->connection->prepare(
            "SELECT * FROM industry_content WHERE lang_code=?");
        $check_data->bindParam(1, $_SESSION['trans']);
        $check_data->execute();
        if ($check_data->rowCount() > 0) {
            $data = $check_data->fetchAll(PDO::FETCH_ASSOC);
        }

        return $data;
    }

    //Retrieve multiple items from Database if exists identified by key/value.
    public function get_data_by_query($query)
    {
        $check_data = '';


        $check_data = $this->connection->prepare($query);

        $check_data->execute();

        return $check_data->fetchAll(PDO::FETCH_ASSOC);
    }

    //delete company
    public function delete_company($companyId)
    {

        $query = "DELETE FROM ticket WHERE ticket_company_id='$companyId'";
        $delete_data = $this->connection->prepare($query);
        $delete_data->execute();

        $query = "DELETE FROM user WHERE user_company_id='$companyId'";
        $delete_data = $this->connection->prepare($query);
        $delete_data->execute();

        $query = "DELETE FROM company WHERE company_id='$companyId'";
        $delete_data = $this->connection->prepare($query);
        $delete_data->execute();

        if ($delete_data) {
            return true;
        } else {
            return false;
        }


    }

}

?>