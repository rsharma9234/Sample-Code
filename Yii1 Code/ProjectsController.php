<?php


class ProjectsController extends CController{

		public $layout = 'main';
		
		//flag to show/hide offering documents pop by default
		private $show_offering_document_popup=false,
		
		//array to keep track of different kind of documents uploaded
		$arr_docs=array(),
		
		//flag to show success popup by default
		$file_success_popup=false;


		

function actionIndex(){
	
	//get project to be displayed
	$project=$this->get_project();
	
	if(empty($project))
		$this->redirect(Yii::app()->createAbsoluteUrl(DISCOVER));
	
	$getUserType = user_obj(uid()); //get user Type
	
	// project access property
	$exeTeam = executive_team::model()->find("`proj_id`=$project->id AND `user_id`=$getUserType->id");
	
	$assistor = get_assist_finders($project);

	if($project->created_by != uid() && $project->finder_id != uid() && !$exeTeam && ($project->payment_made == '0' || $project->approved_by_Admin=='0') && !in_array($getUserType->id, $assistor) && ($getUserType->user_type != FINDER))
		$this->redirect(Yii::app()->createAbsoluteUrl(DISCOVER));
	
	// --- start: increase the count of users viewing projects --//
	if($getUserType->user_type == FUNDER) //funder
	{
		$project->investorViews+=1;
		$project->save();
	}
	else if($getUserType->user_type == FINDER) //finder
	{
		$project->seatHolderViews=$project->seatHolderViews + 1;
		$project->save();
	}
	else //founder
	{
		$project->enterpreneurViews+=1;
		$project->save();	
	}
	
	//-- add project impressions --//
	$project->project_Impressions+=1;
	$project->save();
	
	//-- add all project views --//
	//addProjectViewCount($project->id);
		// --- end: increase the count of users viewing projects --//
	//set page title
	$this->pageTitle=prepare_title($project->title); 
	
	//Yii::app()->clientScript->registerMetaTag(project_url($project->id), null, null, array('property' => "og:url"), '1');
	Yii::app()->clientScript->registerMetaTag(prepare_title($project->title), null, null, array('property' => "og:title"), '2');
	Yii::app()->clientScript->registerMetaTag($project->description, null, null, array('property' => "og:description"), '3');
	Yii::app()->clientScript->registerMetaTag(project_company_logo($project->company_logo), null, null, array('property' => "og:image"), '4');
	
	$this->render("main",array(
	
	//get content
	"content"=>get_controller(PROFILE)->founder_project_pane(array($project)),
	"offering_document_popup"=>$this->get_offering_docs_poup(),
	"success_popup"=>$this->get_success_popup(),
	"document_set_popup"=>$this->document_set_popup($project)

	));
	
	}


function get_success_popup(){
	
	if($this->file_success_popup){
		
		return $this->renderPartial("success_popup",array("docs"=>$this->arr_docs),true);
		
		}
	
	return false;
	}

function get_offering_docs_poup(){
	
	return $this->renderPartial("offering_docs",array(
	
	"model"=>$this->upload_offering_docs(),
	"show_popup"=>$this->show_offering_document_popup,
	"proj_id"=>$this->project_id()
	
	),true);
	
	}


	function upload_offering_docs(){
		
		$model=false;
			
		//array to keep track of files uploaded
		$files_uploaded=array();
		
		//array to keep track of entries which ar enot files
		$other_docs=array();
			
		//get model if already exists
		if(!empty($_POST['offering_documents']['project_id'])){
			$model=offering_documents::model()->find("project_id={$_POST['offering_documents']['project_id']}");
		}
		
		if(!$model || $model==NULL){
			$model=new offering_documents();
		}
		
		//$model=offering_documents::model->find("project_id=");	
			
		if(isset($_POST['offering_documents']))
    	{
	        $model->attributes=$_POST['offering_documents'];
	        
			//get project's directory
			$proj_dir=get_project_dir($_POST['offering_documents']['project_id']);
			
			//set project id
			$model->project_id=$_POST['offering_documents']['project_id'];
			
			//get Offering_circular
			$Offering_circular=CUploadedFile::getInstance($model,'Offering_circular');
			if($Offering_circular!=NULL){
				//var_dump($Offering_circular->getExtensionName());die();
				$Offering_circular_name="Offering_circular.".$Offering_circular->getExtensionName();
				$Offering_circular->saveAs($proj_dir."/".$Offering_circular_name);
				$model->Offering_circular=$Offering_circular_name;
				$files_uploaded[]="offering circular";
			}
			

			//get presentation deck
			$presentation_deck=CUploadedFile::getInstance($model,'presentation_deck');
			if($presentation_deck !=NULL){
				$presentation_deck_name="presentation_deck.".$presentation_deck->getExtensionName();
				$presentation_deck->saveAs($proj_dir."/".$presentation_deck_name);
				$model->presentation_deck=$presentation_deck_name;
				$files_uploaded[]="presentation deck";
			}
			
			//get Private Placement Memorandum
			$private_placement_memorandum=CUploadedFile::getInstance($model,'private_placement_memorandum');
			if($private_placement_memorandum !=NULL){	
				$private_placement_memorandum_name="private_placement_memorandum.".$private_placement_memorandum->getExtensionName();
				$private_placement_memorandum->saveAs($proj_dir."/".$private_placement_memorandum_name);
				$model->private_placement_memorandum=$private_placement_memorandum_name;
				$files_uploaded[]="private placement memorandum";
			}
						
			//get EDGAR Filing
			if(!empty($_POST['offering_documents']['EDGAR_Filing'])){
				$model->EDGAR_Filing=$_POST['offering_documents']['EDGAR_Filing'];
				$other_docs[]="EDGAR Filing";			
			}
			//get Other file if any
			$misc_other=CUploadedFile::getInstance($model,'misc_other');
			if($misc_other!=NULL){
				$misc_other_name="misc_other.".$misc_other->getExtensionName();
				$misc_other->saveAs($proj_dir."/".$misc_other_name);
				$model->misc_other=$misc_other_name;
				$files_uploaded[]="misc/other";
			}
			
			//update other fields
			$model->uploaded_by=uid();
			$model->last_updated=sql_date();
			
	        if($model->save())
	        {
	        	$this->file_success_popup=true;		
				
				if(!empty($files_uploaded)){
					//function to set logs for upload docs
					get_controller(USERLOG)->set_user_logs($model->uploaded_by,$GLOBALS['logs']['log23'].implode(',', $files_uploaded).') for project ',$GLOBALS['logType']['update'],logName($GLOBALS['logs']['log23']),json_encode(array('proj_id'=>$model->project_id)));
				}
				
				/*if all the documents are uploaded, notify founder about it*/	
				if(if_all_docs_uploaded($model)){
					$this->noti_all_docs_uploaded($model);
					
					get_controller(ADMIN_NOTIFICATION)->set_notification(uid(),$GLOBALS['admin_notifications']['ALL_DOCS_UPLOADED'],ADD,json_encode(array("proj_id"=>$model->project_id)));
				  	//$GLOBALS['admin_notifications']['ALL_DOCS_UPLOADED']
				}
				else { //notify founder that some files have been uploaded
					if(count($files_uploaded) || count($other_docs)){
						$this->noti_all_docs_uploaded($model,"some");
					}
				}
						
	        }
			else {
				$this->show_offering_document_popup=true;
			}
    	}
		
		//update documents arry
		$this->arr_docs=array(
		
		"files"=>$files_uploaded,
		"others"=>$other_docs
		
		);
		
		return $model;
				
	}

//method to notify founder that all the documents have been uploaded
function noti_all_docs_uploaded($model,$count="all"){
	
	get_controller(NOTIFICATIONS)->set_notification(
					
					uid(),//triggerer
					get_project_by_id($model->project_id)->created_by, //receiver
					($count=="all"?$GLOBALS['notifications']['ALL_DOCS_UPLOADED']:$GLOBALS['notifications']['SOME_DOCS_UPLOADED']), //notification string
					ADD,//action
					json_encode(array("project_id"=>$model->project_id)) 
					);//extra info, in this case - project_id
					
					

	}


function get_project(){
	
	return get_project_by_id($this->project_id());
	
	}

function project_id(){
	
	return $_GET['proj_id'];
	
	}

function get_random_projects($total=4){
	
	return projects::model()->findAll(
	
		array(
		
			'select'=>'*, rand() as rand',
			'condition'=>'payment_made=1',
			'limit'=>$total,
			'order'=>'rand',
			 
		)
	
	);
	
	}
	
	
	function get_projects_by_cat_id($cat_id,$project_type = 1){
		
		$cats= categories::model()->findAll();//get_project_categories($project_type);


		if($cat_id < end($cats)->id){
			$projs=projects::model()->findAll("project_type={$project_type} AND cat_id={$cat_id} AND payment_made=1");
			return $projs!=NULL?$projs:array();
		}
		else{
			if($cat_id != ((int)end($cats)->id+1)){
				$id = (int)$project_type * 100 + 1;
				$k1 = $cat_id - $id;
				switch ($k1) {
					case 1:
						$projs=projects::model()->findAll("project_type={$project_type} AND payment_made=1 ORDER BY date DESC");
						break;

					case 2:
						$projs=projects::model()->findAll("project_type={$project_type} AND payment_made=1 ORDER BY all_project_views DESC");
						break;

					case 3:
						$projs=projects::model()->findAll("project_type={$project_type} AND staff_picks=1  AND payment_made=1");
						break;

					case 4:
						$projs=projects::model()->findAll("project_type={$project_type} AND payment_made=1 ORDER BY funding_collected DESC");
						break;

					case 5:
						$projs=projects::model()->findAll("project_type={$project_type} AND payment_made=1 ORDER BY funding_needed");
						break;
					
					default:
						$projs = array();
						break;
				}
				return $projs!=NULL?$projs:array();

			}else{
				return array();
			}
		}
	}

function random_projects(){
	
	return $this->get_project_blocks(
	
	$this->get_random_projects()
	
	);
	
	}


function get_project_blocks($projects){

	
	
	return $this->renderPartial("project_blocks",array(
	
	"title"=>"Some Random Projects",
	"projects"=>$projects,
	
	//"projectId"=>$projectId
	),true);
	
	}


function get_comments($proj_id){
	
	return comments::model()->findAllByAttributes(array("project_id"=>$proj_id));
	
	}

//get comment view for given project

function get_comments_view($proj_id){
	
	//get comments
	$comments=$this->get_comments($proj_id);
	
	return 
	
	array(
	
	"view"=>$this->renderPartial("comments",array(
	
	"comments"=>$comments,
	"can_make_comments"=>is_logged_in()
	
	
	),true),
	
	
	"total"=>count($comments)
	
	);
	
	}


function get_funders($proj_id){
	
	return funding::model()->findAll("project_id={$proj_id} AND payment_successful=1 group by funding_by");
	
	}

function get_funders_view($proj_id){
	
	//get funders
	$funders=$this->get_funders($proj_id);
	//-- get array of funders id ---//
	$funder_arr = array();
	foreach($funders as $funder)
	{
		$funder_arr[]=$funder['funding_by'];
	}
	//-- get array of funders id END ---//
	//
	return 
	
	array(
	
	"view"=>$this->renderPartial("funders",array(
	
	"funders"=>$funders,
	
	),true),
	
	"total"=>count($funders)?count($funders):0,
	"funders"=>$funder_arr
	);
	
	
	}


//method returns the Updates view for given project
		
		function get_updates_view($project){
			
			$proj_timeline=$this->get_project_timline($project);
			
				return
				
				array(
				
				"view"=> $this->renderPartial("project_updates",array(
				
				"timeline"=>$proj_timeline
				
				),true),
				
				"count"=>count($proj_timeline)
				
				);
				
				;
			
			}
		
		
		function get_project_timline($project){
			
			//when this project was started
			$elm[]=array(
			
			"user"=>get_user_by_id($project->created_by),
			"string"=>"Started this project.",
			"date"=>$project->date
			
			);
			
			//whether Finder has uploaded all the documents
			if(project_all_docs_uploaded($project->id)){
				
				//get model
				$od_model=get_offering_docs_by_proj_id($project->id);
				
				$elm[]=array(
				
				"user"=>get_user_by_id($od_model->uploaded_by),
				"string"=>"Uploaded all the required documents.",
				"date"=>$od_model->last_updated
				
				
				);
				
				
				}
			
			
			//get all the Funding records for this project
			$funding=get_funding_by_project_id($project->id);
			
			if($funding){
				
				foreach ($funding as $fund){
					
					$elm[]=array(
					
					"user"=>get_user_by_id($fund->funding_by),
					"string"=> 'Funded this project with $'.showIn_numberFormat($fund->funding).'.',
					"date"=>$fund->date,
					
					
					);
					
					}
				
				
				}
			
			
			
			return $elm;
			
			}

	// get recently added projects

	function get_recent_projects($total=4){

		$criteria = new CDbCriteria();
		$criteria->condition = 'payment_made = 1';
		$criteria->order = 'date DESC';


		$item_count = projects::model()->count($criteria);
		$pages = new CPagination($item_count);
		
		$pages->pageSize = $total;
		$pages->applyLimit($criteria);
		//var_dump($pages);

		$project = projects::model()->findAll($criteria);
		return array('project'=>$project, 'pages'=>$pages);

	}

	function recent_projects($pageSize = 2){
		$get_recent_projects = $this->get_recent_projects($pageSize);
		
		return array($this->get_project_blocks(
		
		$get_recent_projects['project']), $get_recent_projects['pages']
		
		);
	
	}


	// get most funded projects

	function get_most_funded_projects($total=2){

		$criteria = new CDbCriteria();
		$criteria->condition = 'payment_made = 1';
		$criteria->order = 'funding_collected DESC';


		$item_count = projects::model()->count($criteria);
		$pages = new CPagination($item_count);
		
		$pages->pageSize = $total;
		$pages->applyLimit($criteria);
		//var_dump($pages);

		$project = projects::model()->findAll($criteria);
		return array('project'=>$project, 'pages'=>$pages);
		
		

	}

	function most_funded_projects($pageSize = 2){
		$get_most_funded_projects = $this->get_most_funded_projects($pageSize);
		return array($this->get_project_blocks(
		
		$get_most_funded_projects['project']), $get_most_funded_projects['pages']
		
		);
	
	}



	// get Popular projects

	function get_popular_projects($total=2){

		$criteria = new CDbCriteria();
		$criteria->condition = 'payment_made = 1';
		$criteria->order = 'all_project_views DESC';


		$item_count = projects::model()->count($criteria);
		$pages = new CPagination($item_count);
		
		$pages->pageSize = $total;
		$pages->applyLimit($criteria);
		//var_dump($pages);

		$project = projects::model()->findAll($criteria);
		return array('project'=>$project, 'pages'=>$pages);

	}

	function popular_projects($pageSize = 2){

		$get_popular_projects = $this->get_popular_projects($pageSize);
		
		return array($this->get_project_blocks(
		
		$get_popular_projects['project']), $get_popular_projects['pages']
		
		);
	
	}


	// get Staff picks projects

	function get_staff_picks_projects($total=2){
		$criteria = new CDbCriteria();
		$criteria->condition = 'payment_made=1 AND staff_picks=1';
		$item_count = projects::model()->count($criteria);

		$pages = new CPagination($item_count);
		$pages->pageSize = $total;
		$pages->applyLimit($criteria);

		$projects = projects::model()->findAll($criteria);




		return array('projects'=>$projects, 'pages'=>$pages);
	}

	function staff_picks_projects($pageSize = 2){
		$get_staff_picks_projects = $this->get_staff_picks_projects($pageSize);
		
		return array($this->get_project_blocks(
		
		$get_staff_picks_projects['projects']), $get_staff_picks_projects['pages']
		
		);
	
	}


	// get Small projects

	function get_small_projects($total = 2){
		$criteria = new CDbCriteria();
		$criteria->condition = 'payment_made=1';
		$criteria->order = 'funding_needed';
		$item_count = projects::model()->count($criteria);

		$pages = new CPagination($item_count);
		$pages->pageSize = $total;
		$pages->applyLimit($criteria);

		$projects = projects::model()->findAll($criteria);




		return array('projects'=>$projects, 'pages'=>$pages);

	}

	function small_projects($pageSize = 2){
		$get_small_projects = $this->get_small_projects($pageSize);
		//var_dump($get_small_projects['pages']);
		 return array($this->get_project_blocks(
		 	$get_small_projects['projects']), $get_small_projects['pages']);
	
	}

	// get How to Report a violation! content
 
	function get_report($type,$user_id=false){
		return $this->renderPartial("report_project_popup",array("type"=>$type, "uid"=>$user_id),true);
	}

	function document_set_popup($project){
		$doc_set = document_set::model()->findAllByAttributes(array("proj_id"=>$project->id, "founder_id"=>$project->created_by/*, "finder_id"=>uid()*/));
		if(!empty($doc_set)){
			$service = json_decode($doc_set[0]->services,true);
		}
		else{
			$service['ids'] = array();
			$service['charges'] = array();
		}
		return $this->renderPartial("document_set_popup",array(
	
		"services_requested_options"=>services_requested_options::model()->findAll(array(
					"condition"=>"status='active'",'order'=>'header,id')),
		"project" => $project,
		"already_set"=> $service['ids'],
		"already_set_charge"=> $service['charges'],
		"already_paid"=>getPaidServices($project->created_by,$project->id),
		
		),true);
	}

	function actionSendDocumentSet(){
		if (isset($_POST['proj_id']) && isset($_POST['founder_id']) && uid()) {

			$services = array();
			if(!empty($_POST['servicesRequested'])){
				$services['ids'] = $_POST['servicesRequested'];
				//var_dump($_POST['servicesRequested']);
				foreach ($_POST['servicesRequested'] as $key => $value) {
					$services['text'][] = $_POST['services_text'][$value-1];
					$services['charges'][] = $_POST['set_service_price'][$value-1];
					$services['tooltip'][] = $_POST['tooltip_text'][$value-1];
				}
				
			}
			else{
				$services['ids'] = array();
				$services['text'] = array();
				$services['charges'] = array();
				$services['tooltip'] = array();
			}
			
			$document_set = document_set::model()->findAllByAttributes(array("proj_id"=>intval($_POST['proj_id']), "founder_id"=>intval($_POST['founder_id'])/*,"finder_id"=>uid()*/));

			if (!empty($document_set)) {

				$document_set[0]->finder_id = uid();
				$document_set[0]->proj_id = intval($_POST['proj_id']);
				$document_set[0]->founder_id = intval($_POST['founder_id']);
				$document_set[0]->updated = sql_date();
				$document_set[0]->services = json_encode($services);
				
				if ($document_set[0]->save() && !empty($_POST['servicesRequested']) ){
					get_controller(NOTIFICATIONS)->set_notification(uid(),intval($_POST['founder_id']), $GLOBALS['notifications']['SELECT_DOCUMENT_SET'], ADD);
				}
			}
			else{
				$document_set = new document_set;

				$document_set->finder_id = uid();
				$document_set->proj_id = intval($_POST['proj_id']);
				$document_set->founder_id = intval($_POST['founder_id']);
				$document_set->created = sql_date();
				$document_set->services = json_encode($services);
				
				if ($document_set->save() && !empty($_POST['servicesRequested'])) {
					get_controller(NOTIFICATIONS)->set_notification(uid(),intval($_POST['founder_id']), $GLOBALS['notifications']['SELECT_DOCUMENT_SET'], ADD);
				}
			}header("location:".project_url(intval($_POST['proj_id'])));
		}
		
	}

	// view of sharing a project via email
	function proj_share_by_email($pid){
		return $this->renderPartial("project_sharing_email",array("pid"=>$pid),true);
	}

}

?>