<?php

class users extends CActiveRecord
{
	public $verify_pw;
	public $verify_email;
	public $finder_id1;
	public $finder_id2;
	public $finder_id3;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function rules()
    {
        return array(
           array('password,verify_pw,verify_email','required','on'=>'admin'),
           array('email,fname,lname','required'),
           array('email','unique'),
           array('email','match','pattern'=>'/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/','message'=>'Invalid email address'),
           //array('password,verify_pw','required','on'=>'updateProfile'),
           //array('verify_pw', 'compare', 'compareAttribute'=>'password','on'=>'updateProfile'),
           array('email','length', 'max'=>255),
           array('zip','numerical','integerOnly'=>true,'on'=>'admin'),
		   array('verify_pw', 'compare', 'compareAttribute'=>'password','on'=>'admin'),
		   array('verify_email', 'compare', 'compareAttribute'=>'email','on'=>'admin'),
           array('prof_pic', 'file', 'types'=>'jpg, gif, png','maxSize'=>2100000,'allowEmpty'=>true),
           array('user_type,street,city,state_id,country,pin,username,website,biography,company_name,company_logo,finder_ids,reg_date,last_login,status,finder_chosen_date,hash,approved_by_admin,membership_activated,logged_after_register,finder_membership,profile_lock,locked_pic,group_id,is_member,noti,sms,accredited_investor,how_accredited,accredited_by','safe'),
        );
    }

	public function tableName()
	{
		return 'users';
	}

		
	public function attributeLabels()
	{
		return array(
		
			'id' => 'id',
			'fname'=>'First Name',
			'lname'=>'Last Name',
			'state_id'=>'State',
			'verify_pw'=>'Confirm Password',
			'verify_email'=>'Confirm Email',
			'prof_pic'=>'Profile Picture'

		);
	}
	public function primaryKey()
	{
    return 'id';
    }


    function verifyPassword(){
        return true;
    }
	

    function verifyEmail(){
        return true;
    }
    public function findByEmail($email)
	{
	    return self::model()->findByAttributes(array('email' => $email));
	}
	
}