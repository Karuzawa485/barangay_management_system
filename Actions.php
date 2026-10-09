<?php 
session_start();
require_once('DBConnection.php');

Class Actions extends DBConnection{
    function __construct(){
        parent::__construct();
        $this->create_certificate_table();
    }
    function __destruct(){
        parent::__destruct();
    }
    function create_certificate_table(){
        $sql = "CREATE TABLE IF NOT EXISTS certificate_list (
            certificate_id INTEGER PRIMARY KEY AUTOINCREMENT,
            control_number TEXT NOT NULL,
            type TEXT NOT NULL,
            full_name TEXT NOT NULL,
            address TEXT NOT NULL,
            age INTEGER NOT NULL,
            civil_status TEXT NOT NULL,
            purpose TEXT NOT NULL,
            date_issued DATE NOT NULL,
            punong_barangay TEXT NOT NULL,
            receipt_number TEXT NOT NULL,
            cedula_number TEXT,
            or_number TEXT,
            expiration_date DATE,
            specific_purpose TEXT,
            certification_body TEXT
        )";
        $this->query($sql);
    }
    function login(){
        extract($_POST);
        $sql = "SELECT * FROM admin_list where username = '{$username}' and `password` = '".md5($password)."' ";
        @$qry = $this->query($sql)->fetchArray();
        if(!$qry){
            $resp['status'] = "failed";
            $resp['msg'] = "Invalid username or password.";
        }else{
            $resp['status'] = "success";
            $resp['msg'] = "Login successfully.";
            foreach($qry as $k => $v){
                if(!is_numeric($k))
                $_SESSION[$k] = $v;
            }
        }
        return json_encode($resp);
    }
    function logout(){
        session_destroy();
        header("location:./admin");
    }
    function save_admin(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
        if(!in_array($k,array('id'))){
            if(!empty($id)){
                if(!empty($data)) $data .= ",";
                $data .= " `{$k}` = '{$v}' ";
                }else{
                    $cols[] = $k;
                    $values[] = "'{$v}'";
                }
            }
        }
        if(empty($id)){
            $cols[] = 'password';
            $values[] = "'".md5($username)."'";
        }
        if(isset($cols) && isset($values)){
            $data = "(".implode(',',$cols).") VALUES (".implode(',',$values).")";
        }
        

       
        @$check= $this->query("SELECT count(admin_id) as `count` FROM admin_list where `username` = '{$username}' ".($id > 0 ? " and admin_id != '{$id}' " : ""))->fetchArray()['count'];
        if(@$check> 0){
            $resp['status'] = 'failed';
            $resp['msg'] = "Username already exists.";
        }else{
            if(empty($id)){
                $sql = "INSERT INTO `admin_list` {$data}";
            }else{
                $sql = "UPDATE `admin_list` set {$data} where admin_id = '{$id}'";
            }
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                if(empty($id))
                $resp['msg'] = 'New User successfully saved.';
                else
                $resp['msg'] = 'User Details successfully updated.';
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Saving User Details Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function delete_admin(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `admin_list` where rowid = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'User successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function update_credentials(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id','old_password')) && !empty($v)){
                if(!empty($data)) $data .= ",";
                if($k == 'password') $v = md5($v);
                $data .= " `{$k}` = '{$v}' ";
            }
        }
        if(!empty($password) && md5($old_password) != $_SESSION['password']){
            $resp['status'] = 'failed';
            $resp['msg'] = "Old password is incorrect.";
        }else{
            $sql = "UPDATE `admin_list` set {$data} where admin_id = '{$_SESSION['admin_id']}'";
            @$save = $this->query($sql);
            if($save){
                $resp['status'] = 'success';
                $_SESSION['flashdata']['type'] = 'success';
                $_SESSION['flashdata']['msg'] = 'Credential successfully updated.';
                foreach($_POST as $k => $v){
                    if(!in_array($k,array('id','old_password')) && !empty($v)){
                        if(!empty($data)) $data .= ",";
                        if($k == 'password') $v = md5($v);
                        $_SESSION[$k] = $v;
                    }
                }
            }else{
                $resp['status'] = 'failed';
                $resp['msg'] = 'Updating Credentials Failed. Error: '.$this->lastErrorMsg();
                $resp['sql'] =$sql;
            }
        }
        return json_encode($resp);
    }
    function save_settings(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k=> $v){
            if(!is_numeric($v))
            $v = $this->escapeString($v);
            if(!empty($data)) $data .=", ";
            $data .= "('{$k}','{$v}')";
        }
        $sql = "INSERT INTO `system_info` (`meta_field`,`meta_value`) VALUES {$data}";
        if(!empty($data))
        $this->query("DELETE FROM `system_info`");
        $save = $this->query($sql);
        if($save){
            $resp['status'] = "success";
            $resp['msg'] = "Settings successfully updated.";
            foreach($_POST as $k=> $v){
                $_SESSION['system_info'][$k] = $v;
            }
            if(isset($_FILES['logo']['tmp_name']) && !empty($_FILES['logo']['tmp_name'])){
                $fname = __DIR__."/uploads/logo.png";
                $upload = $_FILES['logo']['tmp_name'];
                $type = mime_content_type($upload);
                $allowed = array('image/png','image/jpeg');
                if(!in_array($type,$allowed)){
                    $resp['msg'].=" But Image failed to upload due to invalid file type.";
                }else{
                    $gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
                    if($gdImg){
                         if(is_file($fname))
                         unlink($fname);
                         $uploaded = imagepng($gdImg,$fname);
                         imagedestroy($gdImg);
                    }else{
                    $resp['msg'].=" But Image failed to upload due to unkown reason.";
                    }
                }
            }
            $_SESSION['flashdata']['type'] = "success";
            $_SESSION['flashdata']['msg'] = $resp['msg'];
        }else{
            $resp['status'] = "failed";
            $resp['msg'] = "Failed to update settings.";
        }
        return json_encode($resp);
    }
    function save_position(){
        extract($_POST);
        $data = "";
        if(isset($is_approver)){
            $_POST['is_approver'] = 1;
        }else{
            $_POST['is_approver'] = 0;
        }
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        
        if(empty($id)){
            $sql = "INSERT INTO `position_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `position_list` set {$data} where position_id = '{$id}'";
        }

        $check = $this->query("SELECT count(position_id) as `count` FROM `position_list` where `position` = '{$position}' ".($id > 0 ? " and position_id != '{$id}'" : ""))->fetchArray()['count'];
        if($check >0){
            $resp['status']="failed";
            $resp['msg'] = "Position name is already exists.";
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id)){
                    $resp['msg'] = "Position successfully saved.";
                    $pid = $this->query("SELECT last_insert_rowid()")->fetchArray()[0];
                }else{
                    $resp['msg'] = "Position successfully updated.";
                    $pid = $id;
                }
                if($is_approver == 1){
                    $this->query("UPDATE `position_list` set is_approver = 0 where position_id != '{$id}' ");
                }
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Position Failed.";
                else
                    $resp['msg'] = "Updating Position Failed.";
                    $resp['error']=$this->lastErrorMsg();
                    $resp['sql']=$sql;
            }
        }

        return json_encode($resp);
    }
    function delete_position(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `position_list` where position_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Position successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_purok(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        
        if(empty($id)){
            $sql = "INSERT INTO `purok_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `purok_list` set {$data} where purok_id = '{$id}'";
        }

        $check = $this->query("SELECT count(purok_id) as `count` FROM `purok_list` where `purok` = '{$purok}' ".($id > 0 ? " and purok_id != '{$id}'" : ""))->fetchArray()['count'];
        if($check >0){
            $resp['status']="failed";
            $resp['msg'] = "Purok name is already exists.";
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Purok successfully saved.";
                else
                    $resp['msg'] = "Purok successfully updated.";
                $_SESSION['flashdata']['type'] = "success";
                $_SESSION['flashdata']['msg'] = $resp['msg'];
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Purok Failed.";
                else
                    $resp['msg'] = "Updating Purok Failed.";
                    $resp['error']=$this->lastErrorMsg();
                    $resp['sql']=$sql;
            }
        }

        return json_encode($resp);
    }
    function delete_purok(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `purok_list` where purok_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Purok successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_household(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        
        if(empty($id)){
            $sql = "INSERT INTO `household_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `household_list` set {$data} where household_id = '{$id}'";
        }

        $check = $this->query("SELECT count(household_id) as `count` FROM `household_list` where `house_no` = '{$house_no}' ".($id > 0 ? " and household_id != '{$id}'" : ""))->fetchArray()['count'];
        if($check >0){
            $resp['status']="failed";
            $resp['msg'] = "Household number is already exists.";
        }else{
            @$save = $this->query($sql);
            if($save){
                $resp['status']="success";
                if(empty($id))
                    $resp['msg'] = "Household successfully saved.";
                else
                    $resp['msg'] = "Household successfully updated.";
            }else{
                $resp['status']="failed";
                if(empty($id))
                    $resp['msg'] = "Saving New Household Failed.";
                else
                    $resp['msg'] = "Updating Household Failed.";
                    $resp['error']=$this->lastErrorMsg();
                    $resp['sql']=$sql;
            }
        }

        return json_encode($resp);
    }
    function delete_household(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `household_list` where household_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Household successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_complaint(){
        if(!isset($_POST['complainant_name'])) $_POST['complainant_name'] = '';
        if(!isset($_POST['appellant'])) $_POST['appellant'] = '';
        if(!isset($_POST['description'])) $_POST['description'] = '';
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        
        if(empty($id)){
            $sql = "INSERT INTO `complaint_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `complaint_list` set {$data} where complaint_id = '{$id}'";
        }

        @$save = $this->query($sql);
        if($save){
            $resp['status']="success";
            if(empty($id))
                $resp['msg'] = "Resident address successfully added.";
            else
                $resp['msg'] = "Resident addresssuccessfully updated.";
                
                $_SESSION['flashdata']['type'] = "success";
                $_SESSION['flashdata']['msg'] = $resp['msg'];
        }else{
            $resp['status']="failed";
            if(empty($id))
                $resp['msg'] = "Saving New Complaint Failed.";
            else
                $resp['msg'] = "Updating Complaint Failed.";
                $resp['error']=$this->lastErrorMsg();
                $resp['sql']=$sql;
        }

        return json_encode($resp);
    }
    function delete_complaint(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `complaint_list` where complaint_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Complaint Details successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function save_certificate(){
        extract($_POST);
        $data = "";
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id'))){
                $v = trim($v);
                $v = $this->escapeString($v);
                $$k = $v;
            if(empty($id)){
                $cols[] = "`{$k}`";
                $vals[] = "'{$v}'";
            }else{
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
            }
        }
        if(isset($cols) && isset($vals)){
            $cols_join = implode(",",$cols);
            $vals_join = implode(",",$vals);
        }
        
        if(empty($id)){
            $sql = "INSERT INTO `certificate_list` ({$cols_join}) VALUES ($vals_join)";
        }else{
            $sql = "UPDATE `certificate_list` set {$data} where certificate_id = '{$id}'";
        }

        @$save = $this->query($sql);
        if($save){
            $resp['status']="success";
            if(empty($id))
                $resp['msg'] = "Certificate successfully saved.";
            else
                $resp['msg'] = "Certificate successfully updated.";
        }else{
            $resp['status']="failed";
            if(empty($id))
                $resp['msg'] = "Saving New Certificate Failed.";
            else
                $resp['msg'] = "Updating Certificate Failed.";
            $resp['error']=$this->lastErrorMsg();
        }

        return json_encode($resp);
    }
    function delete_certificate(){
        extract($_POST);

        @$delete = $this->query("DELETE FROM `certificate_list` where certificate_id = '{$id}'");
        if($delete){
            $resp['status']='success';
            $_SESSION['flashdata']['type'] = 'success';
            $_SESSION['flashdata']['msg'] = 'Certificate successfully deleted.';
        }else{
            $resp['status']='failed';
            $resp['error']=$this->lastErrorMsg();
        }
        return json_encode($resp);
    }
    function get_resident_stats(){
        $resp['status'] = 'success';
        
        // Get total population
        $total_query = $this->query("SELECT COUNT(*) as total FROM household_list");
        $resp['data']['total_population'] = (int)$total_query->fetchArray()['total'];
        
        // Get purok statistics
        $purok_query = $this->query("SELECT p.purok, COUNT(h.household_id) as count FROM purok_list p LEFT JOIN household_list h ON p.purok_id = h.purok_id GROUP BY p.purok_id, p.purok ORDER BY p.purok");
        $resp['data']['purok'] = ['labels' => [], 'data' => []];
        while($row = $purok_query->fetchArray(SQLITE3_ASSOC)) {
            $resp['data']['purok']['labels'][] = $row['purok'];
            $resp['data']['purok']['data'][] = (int)$row['count'];
        }
        
        // Get sex statistics
        $sex_query = $this->query("SELECT sex, COUNT(*) as count FROM household_list WHERE sex IS NOT NULL AND sex != '' GROUP BY sex");
        $resp['data']['sex'] = ['male' => 0, 'female' => 0];
        while($row = $sex_query->fetchArray(SQLITE3_ASSOC)) {
            if(strtolower($row['sex']) == 'male') {
                $resp['data']['sex']['male'] = (int)$row['count'];
            } elseif(strtolower($row['sex']) == 'female') {
                $resp['data']['sex']['female'] = (int)$row['count'];
            }
        }
        
        // Get civil status statistics
        $civil_query = $this->query("SELECT civil_status, COUNT(*) as count FROM household_list WHERE civil_status IS NOT NULL AND civil_status != '' GROUP BY civil_status");
        $resp['data']['civil_status'] = ['Single' => 0, 'Married' => 0, 'Widowed' => 0, 'Divorced' => 0, 'Separated' => 0];
        while($row = $civil_query->fetchArray(SQLITE3_ASSOC)) {
            if(array_key_exists($row['civil_status'], $resp['data']['civil_status'])) {
                $resp['data']['civil_status'][$row['civil_status']] = (int)$row['count'];
            }
        }
        
        return json_encode($resp);
    }
}
$a = isset($_GET['a']) ?$_GET['a'] : '';
$action = new Actions();
switch($a){
    case 'login':
        echo $action->login();
    break;
    case 'logout':
        echo $action->logout();
    break;
    case 'save_admin':
        echo $action->save_admin();
    break;
    case 'delete_admin':
        echo $action->delete_admin();
    break;
    case 'update_credentials':
        echo $action->update_credentials();
    break;
    case 'save_settings':
        echo $action->save_settings();
    break;
    case 'save_position':
        echo $action->save_position();
    break;
    case 'delete_position':
        echo $action->delete_position();
    break;
    case 'save_purok':
        echo $action->save_purok();
    break;
    case 'delete_purok':
        echo $action->delete_purok();
    break;
    case 'save_household':
        echo $action->save_household();
    break;
    case 'delete_household':
        echo $action->delete_household();
    break;
    case 'save_complaint':
        echo $action->save_complaint();
    break;
    case 'delete_complaint':
        echo $action->delete_complaint();
    break;
    case 'get_resident_stats':
        echo $action->get_resident_stats();
    break;
    case 'save_certificate':
        echo $action->save_certificate();
    break;
    case 'delete_certificate':
        echo $action->delete_certificate();
    break;
    default:
    // default action here
    break;
}