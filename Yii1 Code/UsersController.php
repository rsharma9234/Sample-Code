<?php
	/**
	*
	*UsersController class.
	*
	**/


	class UsersController extends CController
	{
		
		public $layout = 'main';

		public function actionIndex()
		{
			//default function..
		}
		
		
		public function getParameter()
		{
			return !empty($_REQUEST['usertype']) ? $_REQUEST['usertype'] : 'founder';
		}
		
		public function renderForm($form)
		{
			return $this->renderPartial($form, 
						array(
							'user_type' => $this->getParameter(),
							"states"=>get_states(),
							"ref"=>$this->getReferrer(),
							"accredited_investor" => $this->getAccredited()
						), true);
		}

		public function getReferrer()
		{
			$user = get_user_by_id(intval($_REQUEST['referrer']));
			return $user;
		}

		public function getAccredited()
		{
			if(isset($_REQUEST['accredited_investor']))
				$accredited_investor = $_REQUEST['accredited_investor'];
			else
				$accredited_investor = '0';
			return $accredited_investor;
		}
		public function actionRegisterForm()
		{
			//echo $this->getParameter();
			$this->render("main",array(
							"content"=>$this->renderForm($this->getParameter()."_registration_form")
							));
		}
		
		public function actionSaveRegistrationForm(){
			
		}
		
		
		function user_login($email,$pass,$already_encrypted=false)
		{
			$Criteria = new CDbCriteria();
			$Criteria->condition = "email = '{$email}' && password='".encrypt_pass($pass)."'";
			$user=users::model()->find($Criteria);

			if(count($user)){

				//remember Last login date
				$user->last_login=sql_date();
				$user->save();
				
				//start sessions
				$this->on_login($user);
				return true;
			}
			return false;
		}
		
		function remember_login($pass,$already_encrypted=false)
		{
			
			$uid= new CHttpCookie("uid",Yii::app()->session['uid']);
			
			$uid->expire=$this->get_time();
			
			yii::app()->request->cookies['uid']=$uid;
			
			$pass= new CHttpCookie("pass",!$already_encrypted?encrypt_pass($pass):$pass);
			
			$pass->expire=$this->get_time();
			
			yii::app()->request->cookies['pass']=$pass ;
			
		}
			
		
		function get_time()
		{
			return  time()+60*60*24*7;
		}
		
		
		function on_login($user)
		{
			
			Yii::app()->session['uid'] = $user->id;
			
			Yii::app()->session['fname'] = stripslashes($user->fname);
			
			Yii::app()->session['lname'] = stripslashes($user->lname);
			
			Yii::app()->session['user_type'] = $user->user_type;
			
			if(!empty($user->username))
			Yii::app()->session['username'] = $user->username;
			
			Yii::app()->session['biography'] = stripslashes($user->biography);
			
			Yii::app()->session['state_id'] = $user->state_id;
			
			Yii::app()->session['state'] = get_state($user->state_id);
			
			Yii::app()->session['website'] = stripslashes($user->website);
			
			Yii::app()->session['prof_pic'] = stripslashes($user->prof_pic);
			
			Yii::app()->session['company_name'] = stripslashes($user->company_name);
						
			Yii::app()->session['company_logo'] = stripslashes($user->company_logo);
			
			Yii::app()->session['name']=Yii::app()->session['fname']." ".Yii::app()->session['lname'];
			
			return $user;
			
		}
		
	
	//function to log a given user in without having to enter email and password
	function auto_login($user)
	{
		return $this->on_login(user_obj($user));
	}
	
	
	function check_login(){
		
		//if cookies are found on client's system
		if(!empty(yii::app()->request->cookies['uid']) && !empty(yii::app()->request->cookies['pass'])){
		
			//get the user's email
			$email=get_user_by_id(yii::app()->request->cookies['uid'])->email;
			
			//log the user in
			if($this->user_login($email,yii::app()->request->cookies['pass'],true)){
				//renew cookies for n days more
				$this->remember_login(yii::app()->request->cookies['pass'],true);
			}
		}
	}
}
?>