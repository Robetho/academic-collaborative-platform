<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function delete_img(){
		extract($_POST);
		if(is_file($path)){
			if(unlink($path)){
				$resp['status'] = 'success';
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = 'failed to delete '.$path;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = 'Unkown '.$path.' path';
		}
		return json_encode($resp);
	}
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `category_list` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Category Name already exists.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `category_list` set {$data} ";
		}else{
			$sql = "UPDATE `category_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$bid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New Category successfully saved.";
			else
				$resp['msg'] = " Category successfully updated.";
			
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_category(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `category_list` set `delete_flag` = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," category successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}

	//faculty section
	
	function save_faculty(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		//$check = $this->conn->query("SELECT * FROM `faculty` where `faculty_name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		//if($this->capture_err())
			//return $this->capture_err();
		//if($check > 0){
			//$resp['status'] = 'failed';
			//$resp['msg'] = "Category Name already exists.";
			//return json_encode($resp);
			//exit;
		//}
		if(empty($id)){
			$sql = "INSERT INTO `faculty` set {$data} ";
		}else{
			$sql = "UPDATE `faculty` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$bid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "Faculty Information successfully saved.";
			else
				$resp['msg'] = "Faculty Information successfully updated.";
			
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}


	public function delete_faculty(){
		extract($_POST);
		$qry = $this->conn->query("DELETE FROM faculty where id = $id");
		if($qry){
			$this->settings->set_flashdata('success','Faculty Information successfully deleted.');
			return 1;
		}else{
			return false;
		}
	}

	function save_post(){
        extract($_POST);
        $data = "";
        $file_path = null; // Initialize file_path to null

        // Handle file upload
        if(isset($_FILES['attached_file']) && $_FILES['attached_file']['error'] == 0){
            $upload_path = "uploads/files/"; // Define your upload directory
            if(!is_dir(base_app.$upload_path))
                mkdir(base_app.$upload_path);

            $file_name = $_FILES['attached_file']['name'];
            $file_tmp = $_FILES['attached_file']['tmp_name'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $new_file_name = time() . '_' . uniqid() . '.' . $file_ext; // Generate unique file name

            if(move_uploaded_file($file_tmp, base_app . $upload_path . $new_file_name)){
                $file_path = $upload_path . $new_file_name;
            } else {
                $resp['status'] = 'failed';
                $resp['msg'] = "Failed to upload file.";
                return json_encode($resp);
            }
        } elseif (isset($_POST['current_file_path']) && $_POST['current_file_path'] === 'REMOVE') {
            // User explicitly requested to remove the existing file
            // Delete the old file if it exists
            if(isset($id)){ // Only if it's an update operation
                $old_file_query = $this->conn->query("SELECT file_path FROM `post_list` WHERE id = '{$id}'");
                if($old_file_query->num_rows > 0){
                    $old_file_data = $old_file_query->fetch_assoc();
                    if(!empty($old_file_data['file_path']) && is_file(base_app . $old_file_data['file_path'])){
                        unlink(base_app . $old_file_data['file_path']);
                    }
                }
            }
            $file_path = ''; // Set file_path to empty to clear it from DB
        } elseif (isset($_POST['current_file_path']) && !empty($_POST['current_file_path'])) {
            // Keep the existing file if no new file uploaded and not marked for removal
            $file_path = $_POST['current_file_path'];
        }

        // Prepare data for SQL query
        foreach($_POST as $k => $v){
            if(!in_array($k,array('id', 'attached_file', 'current_file_path'))){ // Exclude 'attached_file' and 'current_file_path'
                if(!empty($data)) $data .=",";
                $v = $this->conn->real_escape_string($v);
                $data .= " `{$k}`='{$v}' ";
            }
        }

        // Add file_path to data string if it's set
        if($file_path !== null){ // Only add if it was handled (new upload, removal, or kept old)
            if(!empty($data)) $data .=",";
            $data .= " `file_path`='{$this->conn->real_escape_string($file_path)}' ";
        }

        if(empty($id)){
            $_POST['user_id'] = $this->settings->userdata('id'); // Set user_id for new posts
            if(!empty($data)) $data .=",";
            $data .= " `user_id`='{$this->conn->real_escape_string($_POST['user_id'])}' ";
            $sql = "INSERT INTO `post_list` set {$data} ";
        }else{
            $sql = "UPDATE `post_list` set {$data} where id = '{$id}' ";
        }
        
        $save = $this->conn->query($sql);

        if($save){
            $pid = !empty($id) ? $id : $this->conn->insert_id;
            $resp['pid'] = $pid;
            $resp['status'] = 'success';
            if(empty($id))
                $resp['msg'] = "New Post successfully saved.";
            else
                $resp['msg'] = " Post successfully updated.";
            
        }else{
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error."[{$sql}]";
        }
        if($resp['status'] == 'success')
            $this->settings->set_flashdata('success',$resp['msg']);
        return json_encode($resp);
    }
	function delete_post(){
		extract($_POST);
		$del = $this->conn->query("UPDATE `post_list` set `delete_flag` = 1 where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Post successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
 
	}

    // UPDATED save_comment() function
	function save_comment(){
        // Ensure post_id is set
        if (!isset($_POST['post_id'])) {
            return json_encode(array("status" => "error", "msg" => "Post ID is missing."));
        }

        $user_id = $this->settings->userdata('id'); // Correct way to access user data
        $post_id = $_POST['post_id'];
        // Sanitize the text comment
        $comment_text = isset($_POST['comment']) ? $this->conn->real_escape_string(htmlentities($_POST['comment'])) : '';

        $audio_path = null;
        $video_path = null;
        $file_path = null; // New variable for general file path

        // Base directory for uploads relative to the web root (e.g., "uploads/comments/")
        $upload_base_web = "uploads/comments/";
        // Full server path for uploads (e.g., "/path/to/your/app/uploads/comments/")
        // Make sure base_app is correctly defined in config.php (e.g., define('base_app',str_replace('\\','/',dirname(__DIR__)).'/'); )
        $upload_base_server = base_app . "uploads/comments/";

        // Handle Audio Upload
        if(isset($_FILES['audio']) && $_FILES['audio']['error'] == 0){
            $audio_dir_server = $upload_base_server . "audio/";
            if(!is_dir($audio_dir_server)) mkdir($audio_dir_server, 0777, true); // Ensure directory exists and is writable

            $file_extension = pathinfo($_FILES['audio']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $destination_server = $audio_dir_server . $new_filename;
            $destination_web = $upload_base_web . "audio/" . $new_filename;

            if(move_uploaded_file($_FILES['audio']['tmp_name'], $destination_server)){
                $audio_path = $destination_web; // Store web path in DB
            } else {
                return json_encode(array("status" => "error", "msg" => "Failed to upload audio file."));
            }
        }

        // Handle Video Upload
        if(isset($_FILES['video']) && $_FILES['video']['error'] == 0){
            $video_dir_server = $upload_base_server . "video/";
            if(!is_dir($video_dir_server)) mkdir($video_dir_server, 0777, true); // Ensure directory exists and is writable

            $file_extension = pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $destination_server = $video_dir_server . $new_filename;
            $destination_web = $upload_base_web . "video/" . $new_filename;

            if(move_uploaded_file($_FILES['video']['tmp_name'], $destination_server)){
                $video_path = $destination_web; // Store web path in DB
            } else {
                return json_encode(array("status" => "error", "msg" => "Failed to upload video file."));
            }
        }

        // Handle General File Upload (PDF, Word, Image, etc.)
        if(isset($_FILES['uploaded_file']) && $_FILES['uploaded_file']['error'] == 0){
            $file_dir_server = $upload_base_server . "files/"; // Create a new subdirectory for general files
            if(!is_dir($file_dir_server)) mkdir($file_dir_server, 0777, true);

            $file_extension = pathinfo($_FILES['uploaded_file']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $destination_server = $file_dir_server . $new_filename;
            $destination_web = $upload_base_web . "files/" . $new_filename;

            if(move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $destination_server)){
                $file_path = $destination_web; // Store web path in DB
            } else {
                return json_encode(array("status" => "error", "msg" => "Failed to upload file."));
            }
        }

        // Check if at least one type of comment content is provided
        if (empty($comment_text) && empty($audio_path) && empty($video_path) && empty($file_path)) {
            return json_encode(array("status" => "error", "msg" => "Comment cannot be empty. Please provide text, audio, video, or a file."));
        }

        // Use prepared statements for better security
        $stmt = $this->conn->prepare("INSERT INTO `comment_list` (`user_id`, `post_id`, `comment`, `audio_path`, `video_path`, `file_path`) VALUES (?, ?, ?, ?, ?, ?)");
        // 'iissss' means integer, integer, string, string, string, string
        $stmt->bind_param("iissss", $user_id, $post_id, $comment_text, $audio_path, $video_path, $file_path);

        $save = $stmt->execute();

        if($save){
            $resp['status'] = 'success';
            $resp['msg'] = "New Comment successfully added.";
        } else {
            $resp['status'] = 'failed';
            $resp['err'] = $this->conn->error . "[SQL: " . $stmt->sql . "]"; // More detailed error
        }
        $stmt->close(); // Close the prepared statement

		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_comment(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `comment_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Comment successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_transaction(){
		if(empty($_POST['id'])){
			$_POST['user_id'] = $this->settings->userdata('id');
			$prefix = date("Ymd");
			$code = sprintf("%'.04d", 1);
			while(true){
				$check = $this->conn->query("SELECT * FROM `transaction_list` where code = '{$prefix}{$code}' ")->num_rows;
				if($check > 0){
					$code = sprintf("%'.04d", abs($code) + 1);
				}else{
					$_POST['code'] = $prefix.$code;
					break;
				}
			}
		}
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id')) && !is_array($_POST[$k])){
				if(!empty($data)) $data .=",";
				$v = $this->conn->real_escape_string($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `transaction_list` set {$data} ";
		}else{
			$sql = "UPDATE `transaction_list` set {$data} where id = '{$id}' ";
		}
			$save = $this->conn->query($sql);
		if($save){
			$tid = !empty($id) ? $id : $this->conn->insert_id;
			$resp['tid'] = $tid;
			$resp['status'] = 'success';
			if(empty($id))
				$resp['msg'] = "New Transaction successfully saved.";
			else
				$resp['msg'] = " Transaction successfully updated.";
			if(isset($category_id)){
				$data = "";
				foreach($category_id as $k =>$v){
					$sid = $v;
					$price = $this->conn->real_escape_string($category_price[$k]);
					if(!empty($data)) $data .= ", ";
					$data .= "('{$tid}', '{$sid}', '{$price}')";
				}
				if(!empty($data)){
					$this->conn->query("DELETE FROM `transaction_categorys` where transaction_id = '{$tid}'");
					$sql_category = "INSERT INTO `transaction_categorys` (`transaction_id`, `category_id`, `price`) VALUES {$data}";
					$save_categorys = $this->conn->query($sql_category);
					if(!$save_categorys){
						$resp['status'] = 'failed';
						$resp['sql'] = $sql_category;
						$resp['error'] = $this->conn->error;
						if(empty($id)){
							$resp['msg'] = "Transaction has failed save.";
							$this->conn->query("DELETE FROM `transaction_categorys` where transaction_id = '{$tid}'");
						}else{
							$resp['msg'] = "Transaction has failed update.";
						}
						return json_encode($resp);
					}
				}
			}
			if(isset($comment_id)){
				$data = "";
				foreach($comment_id as $k =>$v){
					$pid = $v;
					$price = $this->conn->real_escape_string($comment_price[$k]);
					$qty = $this->conn->real_escape_string($comment_qty[$k]);
					if(!empty($data)) $data .= ", ";
					$data .= "('{$tid}', '{$pid}', '{$qty}', '{$price}')";
				}
				if(!empty($data)){
					$this->conn->query("DELETE FROM `transaction_comments` where transaction_id = '{$tid}'");
					$sql_comment = "INSERT INTO `transaction_comments` (`transaction_id`, `comment_id`,`qty`, `price`) VALUES {$data}";
					$save_comments = $this->conn->query($sql_comment);
					if(!$save_comments){
						$resp['status'] = 'failed';
						$resp['sql'] = $sql_comment;
						$resp['error'] = $this->conn->error;
						if(empty($id)){
							$resp['msg'] = "Transaction has failed save.";
							$this->conn->query("DELETE FROM `transaction_comments` where transaction_id = '{$tid}'");
						}else{
							$resp['msg'] = "Transaction has failed update.";
						}
						return json_encode($resp);
					}
				}
			}
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success',$resp['msg']);
			return json_encode($resp);
	}
	function delete_transaction(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `transaction_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Transaction successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function update_status(){
		extract($_POST);
		$update = $this->conn->query("UPDATE `transaction_list` set `status` = '{$status}' where id = '{$id}'");
		if($update){
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = "Transaction's status has failed to update.";
		}
		if($resp['status'] == 'success')
			$this->settings->set_flashdata('success', 'Transaction\'s Status has been updated successfully.');
		return json_encode($resp);
	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'delete_img':
		echo $Master->delete_img();
	break;
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_category':
		echo $Master->delete_category();
	break;
	case 'save_faculty':
		echo $Master->save_faculty();
	break;
	case 'delete_faculty':
		echo $Master->delete_faculty();
	break;
	case 'save_post':
		echo $Master->save_post();
	break;
	case 'delete_post':
		echo $Master->delete_post();
	break;
	case 'save_comment':
		echo $Master->save_comment();
	break;
	case 'delete_comment':
		echo $Master->delete_comment();
	break;
	case 'save_inventory': // This case seems to be missing a function in your Master class
		// echo $Master->save_inventory();
	break;
	case 'delete_inventory': // This case seems to be missing a function in your Master class
		// echo $Master->delete_inventory();
	break;
	case 'save_transaction':
		echo $Master->save_transaction();
	break;
	case 'delete_transaction':
		echo $Master->delete_transaction();
	break;
	case 'update_status':
		echo $Master->update_status();
	break;
	default:
		// echo $sysset->index();
		break;
}