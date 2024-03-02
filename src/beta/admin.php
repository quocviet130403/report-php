#!/usr/local/bin/php
<?php
require_once('config.php');
require_once('database.php');
$Database = new Database();

if(isset($argv[1]) && $argv[1] == 'add'){
    

    $validation = true;
    if(!isset($argv[2]) || !isset($argv[3]) || !isset($argv[4]) || !isset($argv[5])){
        $validation = $validation & false;
    }
    else if(!filter_var($argv[3], FILTER_VALIDATE_EMAIL)){
        $validation = $validation & false;
    }
    else if($argv[5] != 'support' && $argv[5] != 'super'){
        $validation = $validation & false;
    }
    
    if($validation){
        $name = $argv[2];
        $email = $argv[3];
        $password = $argv[4];
        $role = $argv[5];

        $admin_exist = $Database->get_data('admin_email', $email, 'admin');
        if(!$admin_exist){
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $info = array(
                array('admin_name', $name),
                array('admin_email', $email),
                array('admin_password', $password_hash),
                array('admin_role', $role)
            );
            $added = $Database->write_data($info, 'admin', false);
            if($added)
                echo "Admin account added successfully!";
            else
                echo "Failed to add admin account!";
        }
        else
            echo "Admin already exist!";

        
    }
    else{
        echo "Invalid arguments!";
    }
}
else if(isset($argv[1]) && $argv[1] == 'delete'){
    if(!isset($argv[2]) || !filter_var($argv[2], FILTER_VALIDATE_EMAIL)){
        echo "Invalid arguments!";
    }
    else{
        $email = $argv[2];
        $user_exist = $Database->get_data('admin_email', $email, 'admin');
        
        if($user_exist){
            $deleted = $Database->delete_data('admin_email', $email, 'admin');
            if($deleted)
                echo "Account successfully deleted!";
            else
                echo "Failed to delete admin account!";
        }
        else
            echo "Account is not exist!";
    }
}
else{
    echo "Invalid operation!";
}
?>