<?php
/**
 * Created by PhpStorm.
 * User: arun
 * Date: 25/9/15
 * Time: 10:05 PM
 */


class GeneralController extends BaseController{
    public $layout = 'layouts.main';
    function index(){
        
    }
    
	public function faq(){
        $this->layout->nest('content', 'general.faq'); 
    }
    public function about_us(){
        $this->layout->nest('content', 'general.about'); 
    }
    
    public function contact_us(){
        $this->layout->nest('content', 'general.contact'); 
    } 
	
	public function save_contact(){
        if(Input::all())
        {
            $data = Input::all();

            $rules = array(
                "person" => "required",
                "name" => "required",
                "email" => "required|email",
                "description" => "required"
            );
			$messages = array(
				'person.required' => 'Please select person type.',
				'name.required' => 'Please enter name.',
				'email.required' => 'Please enter email.',
				'email.email' => 'Enter valid email address.',
				'description.required' => 'Please enter descrption.',
			);
            $validator = Validator::make($data ,$rules,$messages);

            if($validator->fails()){
                return Redirect::to('/contact-us')->withErrors($validator);
            }else{
			 $contacting = '';
			 $person =  $data['person'];
			 if($person=='C')
			 {
			  $contacting = 'Customer';
			 }else
			 {
			  $contacting = 'Salon';
			 }
			 $row = array('PERSON' =>$contacting,'NAME' => $data['name'],'EMAIL' =>$data['email'],'PHONE' =>$data['phone'],'DESCRIPTION' =>$data['description']);
			 $template = EmailTemplate::where('etmTemplateName','Contact Us')->first();
			 $content_customer =  Helper::bind_to_template($row,$template->etmTemplate);
			 Helper::mailto(ADMIN_EMIAL,'Someone Contact You',$content_customer);
			  return Redirect::to('/contact-us')->with('message', 'Contact info submited successfully.');
			}
		}
    }
	
	
     public function privacy(){
        $this->layout->nest('content', 'general.privacy'); 
    }
     public function terms_conditions(){
        $this->layout->nest('content', 'general.terms_conditions'); 
    }
 
}