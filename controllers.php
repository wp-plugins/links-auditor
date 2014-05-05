<?php
class Redirects {
	

	function delete(){
		
		global $wpdb;
		$sql = "DELETE FROM la_redirects";
		$wpdb->query($sql);
		
	}
	
	function edit($title, $section, $new_link, $old_link){
		
		global $wpdb;
		$sql = $wpdb->prepare("INSERT INTO la_redirects (title, section, new_link, old_link) VALUES ('%s', '%s', '%s', '%s')", array($title, $section, $new_link, $old_link));
		$result = $wpdb->query($sql);
	}
	
	function getFields($id){
		
		global $wpdb;
		$sql = $wpdb->prepare("SELECT * FROM la_redirects WHERE id = '%s'", array($id));
		$result = $wpdb->query($sql);
		if($result!==0){
			
			foreach($wpdb->get_results($sql) as $row){
				$fields['title'] = $row->title;
				$fields['section'] = $row->section;
				$fields['new_link'] = $row->new_link;
				$fields['old_link'] = $row->old_link;
			}
			
			return $fields;
			
		} else {
			
			return false;
		
		}
	}

	function createRedirectsTable(){
	
		global $wpdb;
		$sql = "CREATE TABLE la_redirects (id BIGINT(20) PRIMARY KEY AUTO_INCREMENT,title TEXT,section TEXT, new_link TEXT, old_link TEXT)";
		$result = $wpdb->query($sql);
	}

	function checkForRedirectsTable(){
		
		global $wpdb;
		$sql = "SHOW TABLES LIKE 'la_redirects'";
		$result = $wpdb->query($sql);
		if($result==1) {
		
		} else {
		
			$this->createRedirectsTable();

		}

	}
	
	function getAll(){

		global $wpdb;
		$this->checkForRedirectsTable();

		$sql = "SELECT * FROM la_redirects ORDER by id ASC";
		$result = $wpdb->query($sql);
		if($result!==0){
			
			$id_arr = array();
			foreach($wpdb->get_results($sql) as $row){
				$id_arr[] = $row->id;
			}
			
			return $id_arr;
			
		} else {
			
			return false;
		
		}
	}
	
	function remove($custom_id){
		
		global $wpdb;
		$sql = $wpdb->prepare("DELETE FROM la_redirects WHERE id = '%s'", array($custom_id));
		$wpdb->query($sql);
	}
	
}

$redirects = new Redirects;


/* Processes
========================= */

if(isset($_POST['custom_id']) && isset($_POST['delete_custom'])){
	$custom_id = $_POST['custom_id'];
	$redirects->remove($custom_id);
	die();
}

if(isset($_POST['links_audit_submit']) && !isset($_POST['delete_custom'])){

	$redirects->delete();
	
	$redirect_arr = $_POST['title'];

	foreach($redirect_arr as $key => $redirect_title){

		$title = $redirect_title;
		$section = $_POST['section'][$key];
		$new_link = $_POST['new_link'][$key];
		$old_link = $_POST['old_link'][$key];

		$redirects->edit($title, $section, $new_link, $old_link);
	}


	header("Location: ?page=links-auditor&message=success");

}
