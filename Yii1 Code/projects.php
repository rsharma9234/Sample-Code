<?php

class projects extends CActiveRecord
{
    public $picture,$title,$description,$finder_id,$launch_date,$funding_end,$funding_needed,$category,$edited,$price_per_share,$company_logo, $video,$picture2,$picture3,$company,$funding_round,$authorized_share,$founder_share,$issued_share,$outstanding_share,$offered_share,$min_pledge_limit;
    public $fname,$lname,$street_address,$street_address_2,$city,$state,$zip,$country,$area_code,$phone,$email;
    // ... other attributes
 
    public function rules()
    {
        return array(
            
			/*array('picture', 'file', 'types'=>'jpg, jpeg,gif, png','maxSize'=>2100000, 'allowEmpty'=>true, 'on'=>'update'),
			array('picture2', 'file', 'types'=>'jpg, jpeg,gif, png','maxSize'=>2100000, 'allowEmpty'=>true, 'on'=>'update'),
			array('picture3', 'file', 'types'=>'jpg, jpeg,gif, png','maxSize'=>2100000, 'allowEmpty'=>true, 'on'=>'update'),
			array('company_logo', 'file', 'types'=>'jpg, jpeg,gif, png','maxSize'=>2100000, 'allowEmpty'=>true, 'on'=>'update'),*/
			array('video', 'file', 'types'=>'avi,mp4,flv,sfw','allowEmpty'=>true, 'on'=>'update'), 
			array('company,funding_round', 'required'),
			array('description','required'),
			array('project_type,category','required'),
			array('launch_date','required'),
			array('funding_end','required'),
			array('funding_needed','required'),
			array('price_per_share,finder_id','required'),
			array('price_per_share','compare','compareValue'=>'0.00','operator'=>'>','allowEmpty'=>false ,'message'=>'{attribute} must be at least $0.01'),
			array('finder_id','valid_finder'),
			array('finder_id','already_have'),
			array('fname,lname,street_address,city,state,zip,country,area_code,phone,email,authorized_share,founder_share,issued_share,outstanding_share,offered_share,prev_investor_share,outstanding_options,min_pledge_limit', 'required'),
			array('street_address_2','length','max'=>500),
			array('email', 'email'),
			
        );
    }
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function tableName()
	{
		return 'projects';
	}

	function already_have(){
		$finders = get_finders($this->created_by);
		if(!in_array($this->finder_id, $finders) && count($finders)>= $GLOBALS['max_finder_request']){
			$this->addError('finder_id','You already have '.$GLOBALS['max_finder_request'].' seat holders. Please remove one from them to add this.');
		}
	}


	function valid_finder(){
		$finders = is_finder_exist($this->finder_id);
		if(empty($finders)){
			$this->addError('finder_id','This is not a valid seat number. Please enter a valid seat number.');
		}
	}
	
	public function primaryKey()
	{
    return 'id';
    }
	
}


?>