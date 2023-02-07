<?php
   /**
    * Created by PhpStorm.
    * User: arun
    * Date: 25/9/15
    * Time: 10:05 PM
    */


   class AjaxController extends BaseController{

   public function index()
       {
           $qusers = User::all();
           return View::make('users.index', compact('users'));
       }

    public function new_password(){
        if(Request::ajax()){
            if (Input::All()) {
                $rules = array(
                    'password' => 'required|min:6',
                    'password_confirmation' => 'required'
                );
                $data = Input::All();

                $validation = Validator::make(Input::All(), $rules);
                if ($validation->fails()) {
                    $result = json_encode(array('errors' => $validation->getMessageBag()->toArray()));
                } else {
                    if (Input::get('password') != Input::get('password_confirmation')) {
    $result = json_encode(array('errors' => "Passwords do not match!"));

                    } else {
                        $email = Session::get("email_reset_email");
                        $where = array(
                            "email" => $email
                        );
                        $data  = array(
                            "password" => Hash::make(Input::get('password'))
                        );
                        User::where($where)->update($data);
                        $result = json_encode(array('success' => true));
                    }
                }
                echo $result;
                exit();
            }
        }
    }

    public function forgot_password()
    {
        if(Request::ajax()){
            if (Input::All()) {
                $rules      = array(
                    'email' => 'required|email'
                );
                $data       = Input::All();
                $validation = Validator::make(Input::All(), $rules);
                if ($validation->fails()) {
                    $result = json_encode(array('errors' => $validation->getMessageBag()->toArray()));
                    //return Redirect::to('/')->withErrors($validation);
                } else {
                    $where        = array(
                        "email" => Input::get('email')
                    );
                    $getOneRecord = User::where($where)->first();
                    if (count($getOneRecord) == 1) {
                        //send confirmation link to email to reset password//
                        $link = md5(rand("111111", "9999999"));
                        Session::set("email_reset_code", $link);
                        Session::set("email_reset_email", $getOneRecord->email);
                        $firstname    = $getOneRecord->fname . ' ' . $getOneRecord->lname;
                        $messageEmail = "";
                        $messageEmail .= 'Please <a href="' . url() . '/reset/' . $link . '">Click here</a> to reset your password <br>';
                        $helperClass = new Helper;
                        $helperClass->email_template("", $getOneRecord->email, $messageEmail, 'Reset Password', $firstname);
                         $result = array('success' => true);
                        //return Redirect::to('/')->withMessage("One confirmation link has been send to your email , Please check!");

                    } else {
                        $result = array('errors' => "Email not exist!");
                        //return Redirect::to('/')->withErrors("Email not exist!");
                    }
                }
                return Response::json($result);
                exit();
            }
        }
    }
    private function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
       public function signup_ajax(){

           if(Request::ajax()){
               if(Input::All()){
                   $user['email'] = Input::get('email');
                   $user['password'] = Hash::make(Input::get('password'));
                   $user['fname'] = Input::get('fname');
                   $user['lname'] = Input::get('lname');
                   if(Input::get('newsletter')=='on'){
                     $news_l=1;
                   }
                   else{
                     $news_l=0;
                   }
                   $user['newsletter'] = $news_l;
                   $user['role'] = 3;
                   $user['status'] = 0;
                   $random_string = $this->generateRandomString(20);
                   $user['activate_token']=$random_string;
                   $rules = array('email' => 'required|email|unique:users',"password" => "required|min:6|confirmed",'password_confirmation' => 'required|min:6',"fname" => "required");


		$messages = array(
                                'email.required' => 'Email is required.',
				'password.required' => 'Password is required.',
				'password_confirmation.required' => 'Confirm Password is required.',
				'password.confirmed' => 'Password confirmation does not match.',
			);


                   $validator = Validator::make(Input::All(),$rules,$messages);
                   if($validator->fails()){
                       $result = json_encode(array('errors' => $validator->getMessageBag()->toArray()));
                   }else{
                       $userObj = new User;
                       $userSave = $userObj->create($user);
                       $profile['user_id'] = $userSave->id;
                       $profile['gender'] = Input::get('gender');
                       // $profile['postal_code'] = Input::get('postal_code');
                       $date1 = Input::get('dob');
                       $birthday_date = date('Y-m-d H:i:s', strtotime($date1));
                       $profile['birthday'] = $birthday_date;
                       $profile['phone_number'] = Input::get('phone_number');
                       $profile['country'] = Input::get('country');
                       $profile['city'] = Input::get('city');
                       $profile['state'] = Input::get('state');
                       //Upload profile pic//

                       if (Input::hasFile('profile_pic')) {
                           $profile_pic    = Input::file('profile_pic');
                           //echo public_path();die;
                           $imageUploadDir = Helper::user_profile_path();
                           $profile_pic->move($imageUploadDir, $profile_pic->getClientOriginalName());
                           $profile['profile_pic'] = $profile_pic->getClientOriginalName();
                       }
                       $profileObj = new Profile;
                       $profileSave = $profileObj->create($profile);
                       //Auth::loginUsingId($userSave->id);
                       //new user register
                       $firstname = $user['fname'] .' '.$user['lname'] ;

                       $rowCustomer = array('NAME' => $firstname,
                                            'EMAIL' => $user['email'] ,
                                            'PASSWORD' => Input::get('password'),
                                            'activate_token' => $random_string
                                            );

                        $where = array("email" => $user['email'], 'password' => Input::get('password'));

                        if(Auth::attempt($where)){
                            $template = EmailTemplate::where('etmTemplateName','Frontend salon Registeration')->first();
                            $content_customer = Helper::bind_to_template($rowCustomer,$template->etmTemplate);
                            Helper::mailto($user['email'],'Sign up successfully',$content_customer);
                           $result = json_encode(array('success' => true));
                       }
                   }
                   echo $result;
                   exit();
               }
           }
       }

       public function employee_avaliable(){
           if(Request::ajax()){
               if(Input::All()){
                   $resultFinal = array();
                   //get on which days this employee booked already
                   $eid = Input::get("eid");
                   if($eid > 0){
                       $result = DB::select( DB::raw("SELECT date(booking_date) as booking_date,group_concat(time(booking_date)) as booking_start_time,group_concat(time(booking_date_end)) as booking_end_time FROM user_booking WHERE employee_id=$eid group by date(booking_date)") );

                       $last_query = DB::getQueryLog();
                       //echo "<pre>";print_r($last_query);die;
                       if(isset($result) && count($result) > 0){
                           foreach($result as $user_avaliableKey => $user_avaliableVal){
                               $bookingDate = $user_avaliableVal->booking_date;
                               $bookingStartTime = date('H-i',strtotime($user_avaliableVal->booking_start_time)) .','.date('H-i',strtotime($user_avaliableVal->booking_end_time)) ;
                               $resultFinal[] = array("bookingDate" =>  date('d/m/Y',strtotime($bookingDate)) ,"bookingStartTime" => $bookingStartTime);
                           }
                       }else{
                           $resultFinal = array();
                       }

                       echo json_encode($resultFinal);
                       exit();
                   }

               }
           }
       }

       public function login(){
           if(Request::Ajax()){
               if(Input::All()){
                   $user['email'] = Input::get('email');
                   $user['password'] = Hash::make(Input::get('password'));

                   $rules = array('email' => 'required|email',"password" => "required");
                   $validator = Validator::make(Input::All(),$rules);
                   if($validator->fails()){
                       $result = json_encode(array('errors' => false));
                   }else{
                       $remember_me = Input::get('remember_me');
                       $remember = False;
                       if($remember_me){
                           $remember = True;
                       }
                       $where = array("email" => Input::get('email'), 'password' => Input::get('password'),"status" => "1");
                       if(Auth::attempt($where,$remember)){
                            $auth_id = Auth::id();
                            $role = Auth::user()->role;

                            if(Input::get('current')!='')
              							{
              							 Session::set("current",Input::get('current'));
              							}
                            $profile_postal_code_check = Auth::user()->profile->postal_code;

                            if($profile_postal_code_check == "" ){
                                Session::set("postal_code_filled","yes");
                            }

                            $lang = Auth::user()->preferred_lang != null && !empty(Auth::user()->preferred_lang) ? Auth::user()->preferred_lang : 'english';
                            // set language
                            Session::set("language", $lang);

                            if($auth_id > 0){

                               //return Redirect::back();
                               return Response::json(array('success' => true,'role' => $role));
                              // $result = json_encode(array('success' => true));
                            }else{
                               $result = json_encode(array('success' => false));
                               echo $result;
                               exit();
                            }
                       }else{
                           $result = json_encode(array('success' => false));
                           echo $result;
                           exit();
                       }
                   }
                   exit;
               }
           }
       }

       public function employee_timing(){
           if(Request::Ajax()){
               $employee_id = Input::get("employee_id");
               $date = Input::get("date");
               $bid = Input::get("bid");
               $sid = Input::get("sid");
               $interval = Input::get("ival");

               return Employee::is_availWithExistDate($bid,$sid,$employee_id,$date,$interval,'',"");
               exit;
           }
       }

       public function calendar_booking(){
            if(Request::Ajax()){
                $employee_id = Input::get("employee_id");
                $date = date("Y-m-d",strtotime(Input::get("date")));
                $time = Input::get("time");
                $booking_date = date("Y-m-d H:i:s",strtotime($date.' '.$time));
                $bid = Input::get("bid");
                $sid = Input::get("sid");
                $interval = Input::get("ival");

                //check price discount//
                $price = Helper::simplePriceGetCheckout($bid,$sid,$booking_date);
                $where = array("business_id" => $bid,"service_id" => base64_decode($sid));
                $data = array("booking_date" => $booking_date,"employee_id" => $employee_id,"price" => $price);
                AddCart::where($where)->update($data);
                $booking_date = date("H:i A d M",strtotime($booking_date));
               // $last_query = DB::getQueryLog();
               // echo "<pre>";print_r($last_query);
                $data = array("success" => "1","booking_date" => $booking_date,"price" => $price);
                echo json_encode($data);
                exit;
            }
       }

       public function showCalendar(){
        if(Request::Ajax()){
            $bid = Input::get("bid");
            $sid = Input::get("sid");
            $date = Input::get("date");
            $token = Input::get("_token");
            $services = Services::where('id',base64_decode(Input::get("sid")))->first();
            $employee_id = Input::get("employee_id");
            $s_price = "";
            //$s_price = Input::get("s_price");
            $interval = Input::get("ival");
            $date = Input::get("date");

            $cart = AddCart::where('business_id',$bid)
                                ->where('service_id',base64_decode($sid))
                                ->where("token",$token)
                                ->first();
            if(!$cart)
            {
              $cart = Services::where('id',$sid)->get();
              $sid = base64_encode($sid);
              return Employee::is_availWithExistDate($bid,$sid,$employee_id,$date,$interval,'',$s_price,$cart[0]->price);
              exit;
            }
            else {
              return Employee::is_availWithExistDate($bid,$sid,$employee_id,$date,$interval,'',$s_price,$cart->original_price);
              exit;
            }


        }
   }

//    public function showCalendar(){
//     if(Request::Ajax()){
//         $bid = Input::get("bid");
//         $sid = Input::get("sid");
//         $date = Input::get("date");
//         $token = Input::get("_token");
//         $services = Services::where('id',base64_decode(Input::get("sid")))->first();
//         $employee_id = Input::get("employee_id");
//
//         $s_price = Input::get("s_price");
//         $interval = Input::get("ival");
//         $date = Input::get("date");
//
//         $cart = Services::where('id',$sid)->get();
//         $sid = base64_encode($sid);
//         return Employee::is_availWithExistDate($bid,$sid,$employee_id,$date,$interval,'',$s_price,$cart[0]->price);
//         exit;
//
//     }
// }

       public function employee_timing_exist(){
           if(Request::Ajax()){
               $employee_id = Input::get("employee_id");
               $date = Input::get("date");
               $time = Input::get("time");
               $bid = Input::get("bid");
               return Employee::is_availWithExistDate($bid,$employee_id,$date,$time,"");
               exit;
           }
       }

       public function sort_by_search(){
           if(Request::Ajax()){
               $min_price = 0;
               $max_price = 0;

               $price_start = Input::get('price_start');
               $price_end = Input::get('price_end');

               $token = Input::get('_token');
               $action = Input::get('action');
               $allSalon = "";

               $sort_by = Input::get('sort_by');
               $location = Input::get('venue');
               $pagination = Input::get('pagination');
               $availability = Input::get('availability');
               $dp1 = Input::get('calendar');

               $treatment = Input::get('treatment');

               $areas = Input::get('areas');

               if($availability == "M"){
                   $start_date = $dp1." 00:00:00";
                   $end_date = $dp1." 12:00:00";
               }elseif($availability == "N"){
                   $start_date = $dp1." 12:00:00";
                   $end_date = $dp1." 17:00:00";
               }elseif($availability == "E"){
                   $start_date = $dp1." 17:00:00";
                   $end_date = $dp1." 23:59:59";
               }else{
                   $start_date = $dp1." 00:00:00";
                   $end_date = $dp1." 23:59:59";
               }

               if($action == "sort_by" || $action == "sort_by_treatment"){

                   $venueFind = Venue::whereIn('state', $areas)
                                       ->get();
                   //echo "<pre>";print_r($venueFind);die;
                   $business_ids = array();
                   foreach($venueFind as $vanue => $vanueVal){
                        $activeBusiness = Business::where("id",$vanueVal->business_id)
                                                    ->where("salon_frontend_status","1")
                                                    ->get();
                        if(isset($activeBusiness) && count($activeBusiness) > 0){
                            $business_ids[] =  $vanueVal->business_id;
                        }
                   }
				   $collect_buss_id = array();
				   $collect_buss_id_datetime = array();
					if(Input::get('calendar')!='' && Input::get('calendar')!='yy/mm/dd' && count($business_ids)>0)
					{
					 //echo  strtoupper(date('l',strtotime($book_date)));
					  foreach($business_ids as $bid):
						if(Helper::is_salon_open_by_date(Input::get('calendar'),$bid)):
						  $collect_buss_id[] = $bid;
						endif;
					  endforeach;
					  $business_ids = $collect_buss_id;
					  if(count($business_ids)>0):
					   foreach($business_ids as $bid):
						if(Helper::is_salon_open_by_datetime($start_date,$end_date,Input::get('calendar'),$bid)):
						  $collect_buss_id_datetime[] = $bid;
						endif;
					   endforeach;
					   $business_ids = $collect_buss_id_datetime;
					  endif;
					}


                   if($sort_by == "L"){
                   // echo "A";
                        $allSalon = Services::whereIn('business_id', $business_ids)
                                            ->whereIn('service_category_id', $treatment)
                                            ->whereBetween('price', array($price_start, $price_end))
                                            ->groupBy("business_id")
                                            ->orderBy("price","asc")
                                            ->take($pagination)
                                            ->skip(0)
                                            ->get();

                   }elseif($sort_by == "H"){
                    //echo "B";
                       $allSalon= Services::whereIn('business_id', $business_ids)
                                            ->whereIn('service_category_id', $treatment)
                                            ->whereBetween('price', array($price_start, $price_end))
                                            ->groupBy("business_id")
                                            ->orderBy("price","desc")
                                            ->take($pagination)
                                            ->skip(0)
                                            ->get();

                   }elseif($sort_by == "P"){
                       // echo "C";
                       $allSalon= Services::whereIn('business_id', $business_ids)
                                            ->whereIn('service_category_id', $treatment)
                                            ->whereBetween('price', array($price_start, $price_end))
                                            ->groupBy("business_id")
                                            ->orderBy("business_id","desc")
                                            ->take($pagination)
                                            ->skip(0)
                                            ->get();
                   }else{
                      //  echo "D";
                      $allSalon= Services::whereIn('business_id', $business_ids)
                                            ->whereIn('service_category_id', $treatment)
                                            ->whereBetween('price', array($price_start, $price_end))
                                            ->groupBy("business_id")
                                            ->orderBy("business_id","desc")
                                            ->take($pagination)
                                            ->skip(0)
                                            ->get();
                   }
               }
           // $last_query = DB::getQueryLog();
             //echo "<pre>";print_r($allSalon);die;
               ?>
<script src="<?php echo URL::asset('assets/js/responsiveTabs.js') ?> "></script>
<script>
   $(document).ready(function() {
   	RESPONSIVEUI.responsiveTabs();
   })
   jQuery(function($) {
       // Asynchronously Load the map API
       var script = document.createElement('script');
       script.src = "http://maps.googleapis.com/maps/api/js?sensor=false&callback=initialize";
       document.body.appendChild(script);
   });
</script>
<?php
$imploded_map = "";
$latLong = "";
   if(isset($allSalon) && count($allSalon) > 0){
       foreach($allSalon as $vanueKey => $vanueVal){
           //echo "<pre>";print_r($vanueVal->venue);die;
           $city = $vanueVal->business->venue->city;
           //get map
           $show_salon = $vanueVal->business->salon_frontend_status;
           if($show_salon == "1"){
               $address = $vanueVal->business->venue->address;
               $string = trim(preg_replace('/\s\s+/', ' ', str_replace(","," ",$address)));
               $string = str_replace("'","",$string);
               $Address = urlencode($string);
               $request_url = "http://maps.googleapis.com/maps/api/geocode/xml?address=".$Address."&key=".GOOGLE_MAP_API;
               $xml = simplexml_load_file($request_url) or die("url not loading");
               $status = $xml->status;
               if ($status=="OK") {
                   $Lat = $xml->result->geometry->location->lat;
                   $Lon = $xml->result->geometry->location->lng;
               }else{
                   $request_url = "http://maps.googleapis.com/maps/api/geocode/xml?address=".$vanueVal->business->venue->postal_code."&key=".GOOGLE_MAP_API;
                    $xml = simplexml_load_file($request_url) or die("url not loading");
                    $status = $xml->status;
                    if ($status == "OK") {
                        $Lat = $xml->result->geometry->location->lat;
                        $Lon = $xml->result->geometry->location->lng;
                    }else{
                        $Lat = "";
                        $Lon = "";
                    }
               }

               $latLong = $Lat.",".$Lon;
               $map[] = "['".$string."',".$Lat.','.$Lon.']';
               $imploded_map = implode(",",$map);
           }
           ?>
<script>
   function initialize() {
       var map;
       var bounds = new google.maps.LatLngBounds();
        var mapOptions = {
           mapTypeId: 'roadmap',
           center: new google.maps.LatLng(<?php echo $latLong ?>),
             zoomControl:true,
             zoomControlOptions: {
            position: google.maps.ControlPosition.LEFT_CENTER
           },
               };

       // Display a map on the page
       $("#map_canvas").html("");
       map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);
       map.setTilt(45);

       // Multiple Markers
       var markers = [
           //['London Eye', 51.503454,-0.119562],
           //['Palace of Westminster', 51.499633,-0.124755]
           <?php echo $imploded_map; ?>
       ];

       // Info Window Content
       var infoWindowContent = [
           ['<div class="info_content">' +
           '<h3>London Eye</h3>' +
           '<p>The London Eye is a giant Ferris wheel situated on the banks of the River Thames. The entire structure is 135 metres (443 ft) tall and the wheel has a diameter of 120 metres (394 ft).</p>' +        '</div>'],
           ['<div class="info_content">' +
           '<h3>Palace of Westminster</h3>' +
           '<p>The Palace of Westminster is the meeting place of the House of Commons and the House of Lords, the two houses of the Parliament of the United Kingdom. Commonly known as the Houses of Parliament after its tenants.</p>' +
           '</div>']
       ];

       // Display multiple markers on a map
       var infoWindow = new google.maps.InfoWindow(), marker, i;

       // Loop through our array of markers & place each one on the map
       for( i = 0; i < markers.length; i++ ) {
           var position = new google.maps.LatLng(markers[i][1], markers[i][2]);
           bounds.extend(position);
           marker = new google.maps.Marker({
               position: position,
               map: map,
               title: markers[i][0]
           });

           // Allow each marker to have an info window
           google.maps.event.addListener(marker, 'click', (function(marker, i) {
               return function() {
                   infoWindow.setContent(infoWindowContent[i][0]);
                   infoWindow.open(map, marker);
               }
           })(marker, i));

           // Automatically center the map fitting all markers on the screen
           map.fitBounds(bounds);
       }
       map.getCenter(<?php echo $latLong ?>);
       // Override our map zoom level once our fitBounds function runs (Make sure it only runs once)
       var boundsListener = google.maps.event.addListener((map), 'bounds_changed', function(event) {
          // this.setZoom(17);
           google.maps.event.removeListener(boundsListener);
       });

   }

</script>
<?php
}}
   //echo "<pre>";print_r($map);die;
   //echo "<pre>";print_r($allSalon);die;
   foreach($allSalon as $allSalonKey => $allSalonVal){
       $salonAddress = $allSalonVal->business->getUser()->profile->address;
       $fullAddress = $allSalonVal->business->full_address();

       $salon_id = $allSalonVal->business->id;
       $salon_slug = $allSalonVal->business->slug;
      // echo "bid ".$salon_id;
       $salon_image = $allSalonVal->business->image;

       ?>
<div class="property-content-list">
   <div class="property-image-list">
   <?php
    $salonFeatured = Helper::isFeaturedSalon($salon_id);
    if($salonFeatured){
        ?>
        <div class="ribbon-wrapper-green"><div class="ribbon-green">Recommended</div></div>
        <?php
    }
    ?>
      <?php
         if($salon_image != ""){
             ?>
      <img src="<?php echo URL::to('/assets/uploads/salon_profile_pic').'/'.$salon_image; ?>" class="salon_profile_pic" alt="mikha real estate theme">
      <?php
         }else{
             ?>
      <img src="<?php echo URL::to('/assets/images/store-deafult.jpg') ?>" class="salon_profile_pic" alt="mikha real estate theme">
      <?php
         }
         ?>

   </div>
   <div class="property-text">
      <h3><a href="<?php echo url('venue/desc').'/'.$salon_slug; ?>">
         <?php echo $allSalonVal->business->name; ?>
         </a> <small><?php echo $salonAddress;  ?></small>
         <span><?php echo Helper::get_stars(Helper::star_rating('main',$salon_id));?></span>
      </h3>
      <p>
         <a href="<?php echo url('venue/desc').'/'.$salon_slug; ?>">
            <?php echo substr( $allSalonVal->business->description,0,175) ?>...
         </a>
      </p>
      <p><a href="<?php echo url('venue/desc').'/'.$salon_slug; ?>" class="btn btn-warning">More Detail &raquo;</a></p>
   </div>
</div>
<table class="table table-responsive discount-price">
   <tbody>
      <?php
      $explodeElevelcaption = array();
         $salon_user =  $allSalonVal->business->getUser();


         //echo "<pre>";print_r($salonVal);die;
         $salon_title = $allSalonVal->business->name;

         $services = $allSalonVal->business->service()->where("business_id",$salon_id)
                                                    ->where("status","1")
                                                    ->where("offer_distributionchannels","1")
                                                    ->orderBy("feature","desc")
                                                    ->get();
         $limit = 1;
         foreach($services as $servicesKey => $servicesVal){
           $service_id = $servicesVal['id'];
          // echo "<pre>";print_r($servicesVal);die;
           $servicesVal = $servicesVal->where("id",$service_id)
                                        //->where("status","1")
                                       ->where("business_id",$salon_id)
                                       ->whereIn('service_category_id', $treatment)
                                       ->whereBetween('price', array($price_start, $price_end))
                                       ->first();
           if(isset($servicesVal) && count($servicesVal) > 0){
               //show service as per salon condition//
                $showService = Helper::ShowServiceUnderCondition($servicesVal['list_online'],strtotime($servicesVal['offer_visiblefrom']),strtotime($servicesVal['offer_visibleto']));
                if($showService && $limit <= 3){
                   //get minimum and maxium price for slidr//

                   $service_price = $servicesVal['price'];

                   if($service_price > $max_price){
                       $max_price = $service_price;
                       if($min_price == 0){
                           $min_price = $service_price;
                       }
                   }

                   if($service_price < $min_price){
                       $min_price = $service_price;
                   }

                 //

                   $duration  = date("H:i:s", strtotime($servicesVal['duration']));
                   $current_user = Auth::id();
                   $token = Session::get("_token");
                   $bookedService = "";
                   $buttondisabled = "";
                   $alreadytBooked = Helper::check_user_booking($token,$salon_id,$service_id);
                   if($alreadytBooked == true){
                       $bookedService = "disable-btn";
                       $buttondisabled = "disabled";
                   }
                   //GetallPrice accordng to morning evening aftternon//
                   $serviceGroup = $servicesVal->group;
                   $fulfillmenttypes = $servicesVal['fulfillmenttypes'];

                   //$fulfillmenttypesArray = explode(",",$fulfillmenttypes);

                   $multiple_prices =  $servicesVal['multipleprice'];
                   $multiple_pricesArray = explode(",",$multiple_prices);

                   $multipleduration =  $servicesVal['multipleduration'];
                   $multipledurationArray = explode(",",$multipleduration);

                   $multipleCaption =  $servicesVal['multiplecaption'];
                   $multipleCaptionArray = explode(",",$multipleCaption);

                   $multiplesale_price = $servicesVal['multiplesale_price'];
                   $multiplesale_priceArray = explode(",",$multiplesale_price);
                   $leastPrice = 0;
               if($servicesVal['pricing_type'] == "by-employee-cat"){
                    $multiple_pricesArray = array();
                    $multiplesale_priceArray = array();
                    $multipledurationArray = array();
                    $multipleCaptionArray = array();

                    $elevelprice = $servicesVal['elevelprice'];
                    $elevelsale_price = $servicesVal['elevelsale_price'];
                    $elevelcaption = $servicesVal['elevelcaption'];
                    $elevelduration = $servicesVal['elevelduration'];

                    $multiple_pricesArray = explode(",",$elevelprice);
                    $multiplesale_priceArray = explode(",",$elevelsale_price);
                    $multipledurationArray = explode(",",$elevelduration);
                    $explodeElevelcaption = explode(",",$elevelcaption);

                    $leastPrice = min($multiple_pricesArray);

                    $min_duration = min($multipledurationArray);
                    $max_duration = max($multipledurationArray);

                    if(count($multiplesale_priceArray) > 0){
                        if(min($multiplesale_priceArray) > 0){
                            $leastPrice = min($multiplesale_priceArray);
                        }

                    }

                    foreach($explodeElevelcaption as $captionKey => $captionVal){
                        $captionID = $explodeElevelcaption[$captionKey];
                        $employeePricingCaption = EmployeePricingLevel::where("mepEmployeePricingLevelNo",$captionID)->first();
                        $multipleCaptionArray[] = $employeePricingCaption->mepEmployeePricingLevelName;
                    }
                }

               $mrng_time = $serviceGroup->morningallday;

               $noon_time = $serviceGroup->afternoonallday;

               $evng_time = $serviceGroup->eveningallday;

               $getCurrentTym = date("H:i");

               $daynum = date("w")-1;

               $business_afternoon_start_time = $allSalonVal->business->afternoon_start_time;

               $business_eveng_start_time = $allSalonVal->business->evening_start_time;

               if(strtotime($getCurrentTym) <= strtotime($business_afternoon_start_time)){
                   $allday = explode(",",$mrng_time);
               }else if(strtotime($getCurrentTym) >= strtotime($business_afternoon_start_time) && strtotime($getCurrentTym) < strtotime($business_eveng_start_time)){
                   $allday = explode(",",$noon_time);
               }else if(strtotime($getCurrentTym) >= strtotime($business_eveng_start_time)){
                   $allday = explode(",",$evng_time);
               }
               $res = (count($allday)=='7' && array_key_exists($daynum,$allday))?$allday[$daynum]:'';
               //
               ?>

      <tr class="a-open-offer-popup service-pop-cursor <?php echo $bookedService ?>" data-offer-id="653387" data-venue-id="285731" data-booking-options="{&quot;timeOfDay&quot;:&quot;&quot;,&quot;date&quot;:&quot;&quot;,&quot;bt&quot;:&quot;&quot;}" data-toggle="modal" data-target="<?php echo "#modal-booking".$service_id; ?>">
         <td class="offer-title">
            <a href="/service/653387-cut-and-blow-dry-with-treatment-at-marc-scot/" class="action-uri">
            <span class="title-value"><?php echo $servicesVal['title']; ?> </span>
            <span class="global-duration">
            <span class="icon site-duration"></span>
            <span class="text"></span>
            <?php
            if($leastPrice > 0){
               echo Helper::format_timer_result(Helper::minutestoTime($min_duration)) .'-'. Helper::format_timer_result(Helper::minutestoTime($max_duration));
            }else{
                echo Helper::format_timer_result($duration) ;
            }
            ?>
            </span>
            </a>
         </td>
         <td class="offer-price">
            <!--span class="in-basket-label">In the basket</span-->
            <span class="price">
                <?php
                if($leastPrice > 0){
                    ?>
                    <button class='button main-button mini-button btn btn-warning pull-right <?php echo $buttondisabled; ?>'>
                        <span class='price'>
                        <span class='value'>
                        <span class='price-integer'>
                           From &pound;<?php echo $leastPrice; ?>
                        </span>
                        </span>
                        </span>
                    </button>
                    <?php
                }else{
                    echo Helper::showPriceWithoutModal($service_id,$res,$servicesVal['sale_price'],$servicesVal['price'],$buttondisabled);
                }
                ?>
               </span>
                </td>
            </tr>
            <tr>
                <td style="border: none!important;padding: 0px;">


            <!-- end:modal-open booking popup-1 -->
            <div class="modal fade" id="modal-booking<?php echo $service_id; ?>" tabindex="-1" role="dialog" aria-labelledby="modal-signin" aria-hidden="true">
               <div class="modal-dialog-book-now">
                  <div class="modal-content">
                     <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"><?php echo $salon_title; ?></h4>
                     </div>
                     <div class="modal-body">
                        <div class="row book-now-bg">
                           <!--<div class="col-md-6">
                              <img src="<?php echo URL::to('/assets/images/img02.jpg'); ?>" alt="arillo real estate theme">
                              </div>-->
                           <div class="col-md-6">
                              <div class="rating-popup bg-salon<?php echo $servicesVal['id']; ?>">
                                 <div class="widget-header">
                                    <strong> Venue rating</strong>
                                    <div class="row">
                                       <div class="col-md-12">
                                          <div class="col-md-7">
                                             <div class="rating-bigtxt"><?php echo  number_format(Helper::star_rating('main',$salon_id),'1','.', '');?></div>
                                             <br>
                                             <span> <?php echo Helper::get_stars(Helper::star_rating('main',$salon_id));?></span> <span class="text-center"><?php echo Helper::star_rating_count('main',$salon_id);?> reviews</span>
                                          </div>
                                          <div class="col-md-5">
                                             <p><?php if(Helper::star_rating('feature',$salon_id,1)){?>
                                                Ambience <br>
                                                <?php echo Helper::get_stars(Helper::star_rating('feature',$salon_id,1));?><br>
                                                <?php } ?>
                                                <?php if(Helper::star_rating('feature',$salon_id,2)){ ?>
                                                Cleanliness <br>
                                                <?php echo Helper::get_stars(Helper::star_rating('feature',$salon_id,2));?><br>
                                                <?php } ?>
                                                <?php if(Helper::star_rating('feature',$salon_id,3)){ ?>
                                                Staff <br>
                                                <?php echo Helper::get_stars(Helper::star_rating('feature',$salon_id,3));?><br>
                                                <?php } ?>
                                                <?php if(Helper::star_rating('feature',$salon_id,4)){ ?>
                                                Value <br>
                                                <?php echo Helper::get_stars(Helper::star_rating('feature',$salon_id,3));?> <br>
                                                <?php } ?>
                                             </p>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="responsive-tabs">
                                 <?php
                                    if($fulfillmenttypes == "AE"){
                                    ?>
                                 <h2>Book Appointment</h2>
                                 <div class="book-now-tabs">
                                    <form method="post" accept-charset="UTF-8" action="<?php echo url(); ?>/checkout/insert_cart/<?php echo $salon_id ?>">
                                       <ul>
                                          <?php
                                             if(count($multiple_pricesArray) > 0 && $multiple_pricesArray[0] > 0){
                                                foreach($multiple_pricesArray as $multiple_pricesArrayKey => $multiple_pricesArrayVal){
                                                    $caption = "";
                                                    if(isset($explodeElevelcaption) && count($explodeElevelcaption) > 0){
                                                        $caption = $explodeElevelcaption[$multiple_pricesArrayKey];
                                                    }
                                                    ?>
                                          <li>
                                             <span>
                                             <input type="hidden" name="sid" value="<?php echo base64_encode($service_id) ?>"/>
                                             <input type="radio" name="mple" value="<?php echo $multiple_pricesArrayKey.'|'.base64_encode(Helper::simplePriceGet($servicesVal->id,$res,$multiplesale_priceArray[$multiple_pricesArrayKey],$multiple_pricesArray[$multiple_pricesArrayKey])).'|'.$caption.'|'.base64_encode($multiple_pricesArray[$multiple_pricesArrayKey]) ?>" checked/>
                                             </span>
                                             <span>
                                             <?php echo $multipleCaptionArray[$multiple_pricesArrayKey]; ?>
                                             </span>
                                             <span>
                                             <?php echo Helper::format_timer_result(Helper::minutestoTime($multipledurationArray[$multiple_pricesArrayKey])) ?>

                                             </span>
                                             </li>
                                         <li class="book-now-tabs-bold">
                                              <?php
                                              echo Helper::showPriceWithModal($service_id,$res,$multiplesale_priceArray[$multiple_pricesArrayKey],$multiple_pricesArray[$multiple_pricesArrayKey],$buttondisabled);
                                              ?>

                                          </li>
                                          <?php
                                             }
                                             } else{
                                             ?>
                                             <li><?php echo $servicesVal['title']; ?></li>
                                          <li>
                                          <?php echo Helper::format_timer_result($duration); ?>
                                          <li class="book-now-tabs-bold"><span class="book-now-tabs-linetxt"></span>
                                             <input type="hidden" name="sid" value="<?php echo base64_encode($service_id) ?>"/>
                                                <?php
                                                echo Helper::showPriceWithModal($servicesVal['id'],$res,$servicesVal['sale_price'],$servicesVal['price'],$buttondisabled);
                                                ?>
                                          </li>
                                          <?php
                                             }
                                             ?>
                                       </ul>
                                       <input type="submit" name="submit" value="Book Now"  onclick="return checkCartType('1');" id="checkCartType<?php echo $servicesVal['id']; ?>"    class="btn btn-warning btn-block btn-lg"/>
                                       <!--a href="<?php echo url().'/checkout/insert_cart/'.$salon_id.'/'.$service_id?>" id="checkCartType"  onclick="return checkCartType('1');" class="btn btn-warning btn-block btn-lg">Book Now</a-->
                                    </form>
                                 </div>
                                 <h2>Buy Evoucher</h2>
                                 <div class="book-now-tabs">
                                    <form method="post" accept-charset="UTF-8" action="<?php echo url(); ?>/checkout/evoucher_cart/<?php echo $salon_id ?>">
                                       <ul>
                                          <?php
                                             if(count($multiple_pricesArray) > 0 && $multiple_pricesArray[0] > 0 ){
                                                foreach($multiple_pricesArray as $multiple_pricesArrayKey => $multiple_pricesArrayVal){
                                                    $caption = "";
                                                    if(isset($explodeElevelcaption) && count($explodeElevelcaption) > 0){
                                                        $caption = $explodeElevelcaption[$multiple_pricesArrayKey];
                                                    }
                                                    ?>
                                          <li>
                                             <span>
                                             <input type="hidden" name="sid" value="<?php echo base64_encode($service_id) ?>"/>
                                             <input type="radio" name="mple" value="<?php echo $multiple_pricesArrayKey.'|'.base64_encode(Helper::simplePriceGet($servicesVal->id,$res,$multiplesale_priceArray[$multiple_pricesArrayKey],$multiple_pricesArray[$multiple_pricesArrayKey])).'|'.$caption.'|'.base64_encode($multiple_pricesArray[$multiple_pricesArrayKey]) ?>" checked/>
                                             </span>
                                             <span>
                                             <?php echo $multipleCaptionArray[$multiple_pricesArrayKey]; ?>
                                             </span>
                                             <span>
                                             <?php echo Helper::format_timer_result(Helper::minutestoTime($multipledurationArray[$multiple_pricesArrayKey])) ?>
                                             </span>
                                             </li>
                                             <li class="book-now-tabs-bold">
                                            <?php
                                            echo Helper::showPriceWithModal($service_id,$res,$multiplesale_priceArray[$multiple_pricesArrayKey],$multiple_pricesArray[$multiple_pricesArrayKey],$buttondisabled);
                                            ?>
                                          </li>
                                          <?php
                                             }
                                             }else{
                                             ?>
                                             <li><?php echo $servicesVal['title']; ?></li>
                                          <li>
                                          <?php echo Helper::format_timer_result($duration); ?>
                                          </li>
                                          <li class="book-now-tabs-bold"><span class="book-now-tabs-linetxt"></span>
                                             <input type="hidden" name="sid" value="<?php echo base64_encode($service_id) ?>"/>
                                             <?php
                                            echo Helper::showPriceWithModal($servicesVal['id'],$res,$servicesVal['sale_price'],$servicesVal['price'],$buttondisabled);
                                            ?>
                                          </li>
                                          <?php
                                             }
                                             ?>
                                       </ul>
                                       <input type="submit" name="submit" value="Buy Now"  onclick="return checkCartType('2');" class="btn btn-warning btn-block btn-lg"/>
                                    </form>
                                    <!--a href="<?php echo url().'/checkout/evoucher_cart/'.$salon_id.'/'.$service_id?>" onclick="return checkCartType('2');" class="btn btn-warning btn-block btn-lg">Buy Now</a-->
                                 </div>
                                 <?php
                                    }elseif($fulfillmenttypes == "A"){
                                        ?>
                                 <h2>Book Appointment</h2>
                                 <div class="book-now-tabs">
                                    <form method="post" accept-charset="UTF-8" action="<?php echo url(); ?>/checkout/insert_cart/<?php echo $salon_id ?>">
                                       <ul>
                                          <?php
                                             if(count($multiple_pricesArray) > 0 && $multiple_pricesArray[0] > 0 ){
                                                foreach($multiple_pricesArray as $multiple_pricesArrayKey => $multiple_pricesArrayVal){
                                                    $caption = "";
                                                    if(isset($explodeElevelcaption) && count($explodeElevelcaption) > 0){
                                                        $caption = $explodeElevelcaption[$multiple_pricesArrayKey];
                                                    }
                                                    ?>
                                          <li>
                                             <span>
                                             <input type="hidden" name="sid" value="<?php echo base64_encode($service_id) ?>"/>
                                             <input type="radio" name="mple" value="<?php echo $multiple_pricesArrayKey.'|'.base64_encode(Helper::simplePriceGet($servicesVal->id,$res,$multiplesale_priceArray[$multiple_pricesArrayKey],$multiple_pricesArray[$multiple_pricesArrayKey])).'|'.$caption.'|'.base64_encode($multiple_pricesArray[$multiple_pricesArrayKey]) ?>" checked/>
                                             </span>
                                             <span>
                                             <?php echo $multipleCaptionArray[$multiple_pricesArrayKey]; ?>
                                             </span>
                                             <span>
                                                <?php echo Helper::format_timer_result(Helper::minutestoTime($multipledurationArray[$multiple_pricesArrayKey])) ?>
                                             </span>
                                             </li>
                                             <li class="book-now-tabs-bold">
                                            <?php
                                              echo Helper::showPriceWithModal($service_id,$res,$multiplesale_priceArray[$multiple_pricesArrayKey],$multiple_pricesArray[$multiple_pricesArrayKey],$buttondisabled);
                                              ?>
                                          </li>
                                          <?php
                                             }
                                             }else{
                                             ?>
                                             <li><?php echo $servicesVal['title']; ?></li>
                                          <li>
                                            <?php echo Helper::format_timer_result($duration); ?>
                                        </li>
                                          <li class="book-now-tabs-bold"><span class="book-now-tabs-linetxt"></span>
                                             <input type="hidden" name="sid" value="<?php echo base64_encode($service_id) ?>"/>
                                             <?php
                                            echo Helper::showPriceWithModal($servicesVal['id'],$res,$servicesVal['sale_price'],$servicesVal['price'],$buttondisabled);
                                            ?>
                                          </li>
                                          <?php
                                             }
                                             ?>
                                       </ul>
                                       <input type="submit" name="submit" value="Book Now"  onclick="return checkCartType('1');" id="checkCartType<?php echo $servicesVal['id']; ?>"    class="btn btn-warning btn-block btn-lg"/>
                                    </form>
                                    <!--a href="<?php echo url().'/checkout/insert_cart/'.$salon_id.'/'.$service_id?>" id="checkCartType"  onclick="return checkCartType('1');" class="btn btn-warning btn-block btn-lg">Book Now</a-->
                                 </div>
                                 <?php
                                    }elseif($fulfillmenttypes == "E"){
                                        ?>
                                 <h2>Buy Evoucher</h2>
                                 <div class="book-now-tabs">
                                    <form method="post" accept-charset="UTF-8" action="<?php echo url(); ?>/checkout/evoucher_cart/<?php echo $salon_id ?>">
                                       <ul>
                                          <?php
                                             if(count($multiple_pricesArray) > 0 && $multiple_pricesArray[0] > 0 ){
                                                foreach($multiple_pricesArray as $multiple_pricesArrayKey => $multiple_pricesArrayVal){
                                                    $caption = "";
                                                    if(isset($explodeElevelcaption) && count($explodeElevelcaption) > 0){
                                                        $caption = $explodeElevelcaption[$multiple_pricesArrayKey];
                                                    }
                                                    ?>
                                          <li>
                                             <span>
                                             <input type="hidden" name="sid" value="<?php echo base64_encode($service_id) ?>"/>
                                            <input type="radio" name="mple" value="<?php echo $multiple_pricesArrayKey.'|'.base64_encode(Helper::simplePriceGet($servicesVal->id,$res,$multiplesale_priceArray[$multiple_pricesArrayKey],$multiple_pricesArray[$multiple_pricesArrayKey])).'|'.$caption.'|'.base64_encode($multiple_pricesArray[$multiple_pricesArrayKey]) ?>" checked/>
                                             </span>
                                             <span>
                                             <?php echo $multipleCaptionArray[$multiple_pricesArrayKey]; ?>
                                             </span>
                                             <span>
                                             <?php echo Helper::format_timer_result(Helper::minutestoTime($multipledurationArray[$multiple_pricesArrayKey])) ?>
                                             </span>
                                         </li>
                                         <li class="book-now-tabs-bold">
                                             <?php
                                              echo Helper::showPriceWithModal($service_id,$res,$multiplesale_priceArray[$multiple_pricesArrayKey],$multiple_pricesArray[$multiple_pricesArrayKey],$buttondisabled);
                                              ?>
                                          </li>
                                          <?php
                                             }
                                             }else{
                                             ?>
                                             <li><?php echo $servicesVal['title']; ?></li>
                                          <li><?php echo Helper::format_timer_result($duration); ?> </li>
                                          <li class="book-now-tabs-bold"><span class="book-now-tabs-linetxt"></span>
                                             <input type="hidden" name="sid" value="<?php echo base64_encode($service_id) ?>"/>
                                             <?php
                                            echo Helper::showPriceWithModal($servicesVal['id'],$res,$servicesVal['sale_price'],$servicesVal['price'],$buttondisabled);
                                            ?>
                                          </li>
                                          <?php
                                             }
                                             ?>
                                       </ul>
                                       <input type="submit" name="submit" value="Buy Now"  onclick="return checkCartType('2');"  class="btn btn-warning btn-block btn-lg"/>
                                    </form>
                                    <!--a href="<?php echo url().'/checkout/evoucher_cart/'.$salon_id.'/'.$service_id?>" onclick="return checkCartType('2');" class="btn btn-warning btn-block btn-lg">Buy Now</a-->
                                 </div>
                                 <?php
                                    }else{
                                        ?>
                                 <h2>Book Appointment</h2>
                                 <div class="book-now-tabs">
                                    <form method="post" accept-charset="UTF-8" action="<?php echo url(); ?>/checkout/insert_cart/<?php echo $salon_id ?>">
                                       <ul>
                                          <?php
                                             if(count($multiple_pricesArray) > 0 && $multiple_pricesArray[0] > 0 ){
                                                foreach($multiple_pricesArray as $multiple_pricesArrayKey => $multiple_pricesArrayVal){
                                                    $caption = "";
                                                    if(isset($explodeElevelcaption) && count($explodeElevelcaption) > 0){
                                                        $caption = $explodeElevelcaption[$multiple_pricesArrayKey];
                                                    }
                                                    ?>
                                          <li>
                                             <span>
                                             <input type="hidden" name="sid" value="<?php echo base64_encode($service_id) ?>"/>
                                             <input type="radio" name="mple" value="<?php echo $multiple_pricesArrayKey.'|'.base64_encode(Helper::simplePriceGet($servicesVal->id,$res,$multiplesale_priceArray[$multiple_pricesArrayKey],$multiple_pricesArray[$multiple_pricesArrayKey])).'|'.$caption.'|'.base64_encode($multiple_pricesArray[$multiple_pricesArrayKey]) ?>" checked/>
                                             </span>
                                             <span>
                                             <?php echo $multipleCaptionArray[$multiple_pricesArrayKey]; ?>
                                             </span>
                                             <span>
                                             <?php echo Helper::format_timer_result(Helper::minutestoTime($multipledurationArray[$multiple_pricesArrayKey])) ?>
                                             </span>
                                         </li>
                                         <li class="book-now-tabs-bold">
                                             <?php
                                              echo Helper::showPriceWithModal($service_id,$res,$multiplesale_priceArray[$multiple_pricesArrayKey],$multiple_pricesArray[$multiple_pricesArrayKey],$buttondisabled);
                                              ?>
                                          </li>
                                          <?php
                                             }
                                             }else{
                                             ?>
                                             <li><?php echo $servicesVal['title']; ?></li>
                                          <li><?php echo Helper::format_timer_result($duration); ?> </li>
                                          <li class="book-now-tabs-bold"><span class="book-now-tabs-linetxt"></span>
                                             <input type="hidden" name="sid" value="<?php echo base64_encode($service_id) ?>"/>
                                             <?php
                                                echo Helper::showPriceWithModal($servicesVal['id'],$res,$servicesVal['sale_price'],$servicesVal['price'],$buttondisabled);
                                                ?>
                                          </li>
                                          <?php
                                             }
                                             ?>
                                       </ul>
                                       <input type="submit" name="submit" value="Book Now"  onclick="return checkCartType('1');" id="checkCartType<?php echo $servicesVal['id']; ?>"  class="btn btn-warning btn-block btn-lg"/>
                                    </form>
                                    <!--a href="<?php echo url().'/checkout/insert_cart/'.$salon_id.'/'.$service_id?>" id="checkCartType"  onclick="return checkCartType('1');" class="btn btn-warning btn-block btn-lg">Book Now</a-->
                                 </div>
                                 <h2>Buy Evoucher</h2>
                                 <div class="book-now-tabs">
                                    <form method="post" accept-charset="UTF-8" action="<?php echo url(); ?>/checkout/evoucher_cart/<?php echo $salon_id ?>">
                                       <ul>
                                          <?php
                                             if(count($multiple_pricesArray) > 0 && $multiple_pricesArray[0] > 0){
                                                foreach($multiple_pricesArray as $multiple_pricesArrayKey => $multiple_pricesArrayVal){
                                                    $caption = "";
                                                    if(isset($explodeElevelcaption) && count($explodeElevelcaption) > 0){
                                                        $caption = $explodeElevelcaption[$multiple_pricesArrayKey];
                                                    }
                                                    ?>
                                          <li>
                                             <span>
                                             <input type="hidden" name="sid" value="<?php echo base64_encode($service_id) ?>"/>
                                            <input type="radio" name="mple" value="<?php echo $multiple_pricesArrayKey.'|'.base64_encode(Helper::simplePriceGet($servicesVal->id,$res,$multiplesale_priceArray[$multiple_pricesArrayKey],$multiple_pricesArray[$multiple_pricesArrayKey])).'|'.$caption.'|'.base64_encode($multiple_pricesArray[$multiple_pricesArrayKey]) ?>" checked/>
                                             </span>
                                             <span>
                                             <?php echo $multipleCaptionArray[$multiple_pricesArrayKey]; ?>
                                             </span>
                                             <span>
                                             <?php echo Helper::format_timer_result(Helper::minutestoTime($multipledurationArray[$multiple_pricesArrayKey])) ?>
                                             </span>
                                             </li>
                                             <li class="book-now-tabs-bold">
                                             <span class="book-now-tabs-linetxt"></span>
                                             <?php
                                                if( $multiplesale_priceArray[$multiple_pricesArrayKey] > 0){
                                                  ?>
                                             <span class="book-now-tabs-linetxt">&pound;<?php echo $multiple_pricesArray[$multiple_pricesArrayKey]; ?></span>
                                             &nbsp; &pound;<?php echo $multiplesale_priceArray[$multiple_pricesArrayKey]; ?>
                                             <?php
                                                }else{
                                                  ?>
                                             <?php echo $multiple_pricesArray[$multiple_pricesArrayKey]; ?>
                                             <?php
                                                }
                                                ?>
                                          </li>
                                          <?php
                                             }
                                             ?>

                                    <!--a href="<?php echo url().'/checkout/evoucher_cart/'.$salon_id.'/'.$service_id?>" onclick="return checkCartType('2');" class="btn btn-warning btn-block btn-lg">Buy Now</a-->
                                 </div>
                                 <?php
                                    }else{
                                       ?>
                                       <li><?php echo $servicesVal['title']; ?></li>
                                 <li><?php echo Helper::format_timer_result($duration); ?> </li>
                                 <li class="book-now-tabs-bold"><span class="book-now-tabs-linetxt"></span>
                                    <input type="hidden" name="sid" value="<?php echo base64_encode($service_id) ?>"/>
                                    <?php
                                       if( $servicesVal['sale_price'] > 0){
                                         ?>
                                    <span class="book-now-tabs-linetxt">&pound;<?php echo $servicesVal['price']; ?></span>
                                    &nbsp; &pound;<?php echo $servicesVal['sale_price']; ?>
                                    <?php
                                       }else{
                                         ?>
                                    &pound;<?php echo $servicesVal['price']; ?>
                                    <?php
                                       }
                                       ?>
                                 </li>
                                 <?php
                                 }
                                   ?>
                                   </ul>
                                 <input type="submit" name="submit" value="Buy Now"  onclick="return checkCartType('2');" class="btn btn-warning btn-block btn-lg"/>
                                 </form>
                                   <?php
                                 } ?>
                              </div>
                           </div>
                           <hr />
                           <div class="">
                              <div class="col-md-12">
                                 <p><?php echo $servicesVal->description; ?></p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            </td>
            </tr>
            <!-- end:book-now -->
            <!-- end:modal-open booking popup-1 -->
            <?php
            $limit = $limit+1;
               }

               }
                if($salon_image == ""){
                    $salon_image = "store-deafult.jpg";
                }
               ?>
            <style>
                .bg-salon<?php echo $servicesVal['id']; ?>{
                     width: 100%;
					 position: relative;
					 padding: 15px;
					 margin: 15px 0px 15px 0px;
					 z-index: 5;
                 }
				.bg-salon<?php echo $servicesVal['id']; ?>::before
                {
                    content: "";
                    position: absolute;
                    z-index: -1;
                    top: 0;
                    bottom: 0;
                    left: 0;
                    right: 0;
                    border-radius: 30px 6px;
                    opacity: .2;
                    background: rgba(0, 0, 0, 0) url("<?php echo URL::to('/assets/uploads/salon_profile_pic').'/'.$salon_image; ?>") repeat scroll 0 0 / 100% 100%;
                    background-size: 100% 100%;
                }
            </style>
            <script>
               $(function(){
                    service_id = "<?php echo $servicesVal['id']; ?>";
                    $("#checkCartType"+service_id).click(function(){
                        return checkCartType(type_id);
                    })
                })
            </script>
            <?php
               }
               ?>

   </tbody>
</table>
<div class="prod_seprator" style='clear:both'></div>
<?php
   }

   }
   }


   // exheck code exist while order checkout//

   public function CheckoutCodeExist(){
   if(Request::ajax()){
   $result = array();
   $wallet_used = 0;
   $code = Input::get("code");
   $wallet = Input::get("wallet");
   $subtotal = Session::get("subtotal");
   $discountAmt = 0;
   $discountAmtPercentage = 0;
   $discountAmtActual = 0;
   $codeExistID = 0;
   $totalAfterDiscount = 0;
   $discountType = "";
   $usedWallet = 0;

   if($wallet > 0){
   if(Auth::id() > 0){
   if(!$totalAfterDiscount > 0){
       $totalAfterDiscount = $subtotal;
   }
   $wallet_amount = Auth::user()->wallet_balance;
   if($wallet <= $wallet_amount){
       if($wallet <= $totalAfterDiscount){
           $totalAfterDiscount = $totalAfterDiscount - $wallet;
       }elseif($totalAfterDiscount <= $wallet){
           $totalAfterDiscount = $totalAfterDiscount;
       }
       $result = array("success" => 1,"codeID" => $codeExistID,"discount" => $discountAmtActual,"subtotal" => $subtotal,"withDiscount" => $totalAfterDiscount,"discountType" => $discountType,"usedWallet" => $wallet_used);
    }else{
       $result = array("success" => 1,"codeID" => $codeExistID,"discount" => $discountAmtActual,"subtotal" => $subtotal,"withDiscount" => $totalAfterDiscount,"discountType" => $discountType,"usedWallet" => 0,"msg" => "Please fill valid amount in wallet!","error" => "W");
    }
   }else{
   $result = array("success" => 1,"codeID" => $codeExistID,"discount" => $discountAmtActual,"subtotal" => $subtotal,"withDiscount" => $totalAfterDiscount,"discountType" => $discountType,"usedWallet" => $wallet_used,"msg" => "Please Login First!","error" => "W");
   }
   }

   if($code != ""){
   //check code from database if its exist as promo code or gift card codes//
   $codeType = array("1","3");
   $codeExist = Voucher::where("mvcVoucherCode" , $code)
               ->where("mvcStatus","0")
               ->whereIn('mvcVoucherTypeNo', $codeType)
               ->first();

   //SELECT * FROM `Mvoucher` WHERE `mvcVoucherCode` like 'nn1uu4x7hy' and `mvcStatus` = '0' and (`mvcVoucherTypeNo` = '6' or `mvcVoucherTypeNo` = '3')
   //echo "<pre>";print_r($codeExist);die;
   if(isset($codeExist) && count($codeExist) > 0){
   $code_type = $codeExist->mvcVoucherTypeNo;
   if($code_type == "1"){
       //if generic codes
       $start_date = $codeExist->mvcStartDate;
       $end_date = $codeExist->mvcEndDate;

       $current_date = date("Y-m-d H:i");
       if(strtotime($current_date) >= strtotime($start_date) && strtotime($end_date) >= strtotime($current_date)){
           $discountType = $codeExist->mvcDiscountType;
           $discountAmtActual = $codeExist->mvcDiscount;

           //echo "A ".$discountAmtPercentage;
           if($discountType == "0"){
               $discountAmt = $codeExist->mvcDiscount;
           }else if($discountType == "1"){
               $discountAmtPercentage = $codeExist->mvcDiscount;
           }

           if($discountAmtPercentage > 0){
               $totalAfterDiscount = $discountAmtPercentage/100*$subtotal;
           }elseif($discountAmt > 0){
               if($subtotal >= $discountAmt){
                   $totalAfterDiscount = $subtotal - $discountAmt;
               }elseif($discountAmt >= $subtotal){
                   $totalAfterDiscount = $discountAmt - $subtotal;
               }
           }

           $codeExistID = $codeExist->mvcVoucherNo;

           $result = array("success" => 1,"codeID" => $codeExistID,"discount" => $discountAmtActual,"subtotal" => $subtotal,"withDiscount" => $totalAfterDiscount,"discountType" => $discountType,"usedWallet" => $wallet);
       }else{
           $result = array("success" => 1,"codeID" => $codeExistID,"discount" => $discountAmtActual,"subtotal" => $subtotal,"withDiscount" => $totalAfterDiscount,"discountType" => $discountType,"usedWallet" => $wallet,"msg" => "Invalid/Expire Code!","error" => "V");
       }
   }else{
       $discountType = $codeExist->mvcDiscountType;
       $discountAmtActual = $codeExist->mvcDiscount;

       //echo "A ".$discountAmtPercentage;
       if($discountType == "0"){
           $discountAmt = $codeExist->mvcDiscount;
       }else if($discountType == "1"){
           $discountAmtPercentage = $codeExist->mvcDiscount;
       }

       if($discountAmtPercentage > 0){
           $totalAfterDiscount = $discountAmtPercentage/100*$subtotal;
       }elseif($discountAmt > 0){
           if($subtotal >= $discountAmt){
               $totalAfterDiscount = $subtotal - $discountAmt;
           }elseif($discountAmt >= $subtotal){
               $totalAfterDiscount = $discountAmt - $subtotal;
           }
       }
   }





   }else{
   $result = array("success" => 1,"codeID" => $codeExistID,"discount" => $discountAmtActual,"subtotal" => $subtotal,"withDiscount" => $totalAfterDiscount,"discountType" => $discountType,"usedWallet" => $wallet,"msg" => "Invalid/Expire Code!","error" => "V");
   }
   }

   echo json_encode($result);
   exit;
   }
   }

   public function CheckoutWalletExist(){
   if(Request::ajax()){
   $wallet_used = 0;
   $code = Input::get("code");
   $wallet = Input::get("wallet");
   $subtotal = Session::get("subtotal");
   $discountAmt = 0;
   $discountAmtPercentage = 0;
   $discountAmtActual = 0;
   $codeExistID = 0;
   $totalAfterDiscount = 0;
   $discountType = "";
   $usedWallet = 0;

   if($code != ""){
   //check code from database if its exist as promo code or gift card codes//
   $codeType = array("1","3");
   $codeExist = Voucher::where("mvcVoucherCode" , $code)
               ->where("mvcStatus","0")
               ->whereIn('mvcVoucherTypeNo', $codeType)
               ->first();

   //SELECT * FROM `Mvoucher` WHERE `mvcVoucherCode` like 'nn1uu4x7hy' and `mvcStatus` = '0' and (`mvcVoucherTypeNo` = '6' or `mvcVoucherTypeNo` = '3')

   if(isset($codeExist) && count($codeExist) > 0){
   //check if generic and expire//

   $code_type = $codeExist->mvcVoucherTypeNo;
   if($code_type == "1"){
       //if generic codes
       $start_date = $codeExist->mvcStartDate;
       $end_date = $codeExist->mvcEndDate;

       $current_date = date("Y-m-d H:i");
       if(strtotime($current_date) >= strtotime($start_date) && strtotime($end_date) >= strtotime($current_date)){
           $discountType = $codeExist->mvcDiscountType;
           $discountAmtActual = $codeExist->mvcDiscount;

           //echo "A ".$discountAmtPercentage;
           if($discountType == "0"){
               $discountAmt = $codeExist->mvcDiscount;
           }else if($discountType == "1"){
               $discountAmtPercentage = $codeExist->mvcDiscount;
           }

           if($discountAmtPercentage > 0){
               $totalAfterDiscount = $subtotal - $discountAmtPercentage/100*$subtotal;
           }elseif($discountAmt > 0){
               if($subtotal >= $discountAmt){
                   $totalAfterDiscount = $subtotal - $discountAmt;
               }elseif($discountAmt >= $subtotal){
                   $totalAfterDiscount = $discountAmt - $subtotal;
               }
           }

           $codeExistID = $codeExist->mvcVoucherNo;

           $result = array("success" => 1,"codeID" => $codeExistID,"discount" => $discountAmtActual,"subtotal" => $subtotal,"withDiscount" => $totalAfterDiscount,"discountType" => $discountType,"usedWallet" => $wallet);
       }else{
           $result = array("success" => 1,"codeID" => $codeExistID,"discount" => $discountAmtActual,"subtotal" => $subtotal,"withDiscount" => $totalAfterDiscount,"discountType" => $discountType,"usedWallet" => $wallet,"msg" => "Invalid/Expire Code!","error" => "V");
       }

   }else{
       $discountType = $codeExist->mvcDiscountType;
       $discountAmtActual = $codeExist->mvcDiscount;

       //echo "A ".$discountAmtPercentage;
       if($discountType == "0"){
           $discountAmt = $codeExist->mvcDiscount;
       }else if($discountType == "1"){
           $discountAmtPercentage = $codeExist->mvcDiscount;
       }

       if($discountAmtPercentage > 0){
           $totalAfterDiscount = $discountAmtPercentage/100*$subtotal;
       }elseif($discountAmt > 0){
           if($subtotal >= $discountAmt){
               $totalAfterDiscount = $subtotal - $discountAmt;
           }elseif($discountAmt >= $subtotal){
               $totalAfterDiscount = $discountAmt - $subtotal;
           }
       }
   }

   }else{
   $result = array("success" => 1,"codeID" => $codeExistID,"discount" => $discountAmtActual,"subtotal" => $subtotal,"withDiscount" => $totalAfterDiscount,"discountType" => $discountType,"usedWallet" => $wallet,"msg" => "Invalid/Expire Code!","error" => "V");
   }
   }

   if($wallet > 0){
   if(Auth::id() > 0){
   if(!$totalAfterDiscount > 0){
       $totalAfterDiscount = $subtotal;
   }
   //echo $totalAfterDiscount;die;
   $wallet_amount = Auth::user()->wallet_balance;
   if($wallet <= $wallet_amount){
       if($wallet <= $totalAfterDiscount){
           $wallet_used = $wallet;
           $totalAfterDiscount = $totalAfterDiscount - $wallet;
       }elseif($totalAfterDiscount <= $wallet){
           $wallet_used = $totalAfterDiscount;
           $totalAfterDiscount = $wallet;
       }else{
            $totalAfterDiscount = 0;
            $wallet_used = $wallet;
       }
       $result = array("success" => 1,"codeID" => $codeExistID,"discount" => $discountAmtActual,"subtotal" => $subtotal,"withDiscount" => $totalAfterDiscount,"discountType" => $discountType,"usedWallet" => $wallet_used);
    }else{
       $result = array("success" => 1,"codeID" => $codeExistID,"discount" => $discountAmtActual,"subtotal" => $subtotal,"withDiscount" => $totalAfterDiscount,"discountType" => $discountType,"usedWallet" => 0,"msg" => "Please fill valid amount in wallet!","error" => "W");
    }
   }else{
   $result = array("success" => 1,"codeID" => $codeExistID,"discount" => $discountAmtActual,"subtotal" => $subtotal,"withDiscount" => $totalAfterDiscount,"discountType" => $discountType,"usedWallet" => $wallet_used,"msg" => "Please Login First!","error" => "W");
   }
   }

   echo json_encode($result);
   exit;
   }
   }


   public function postcomment()
   {
   if(Request::Ajax())
   {
   if(Auth::id()){
   if(Input::All()){
   $temp['mcoComment']    = Input::get('comment');
   $temp['mcoNotify']     = Input::get('notify');
   $temp['mcoUserId']     = Auth::id();
   $temp['mcoReviewId']   = Input::get('review_id');


   $rules = array("comment" => "required");
   $validator = Validator::make(Input::All(),$rules);
   if($validator->fails()){
   //$result = json_encode(array('errors' => $validator->getMessageBag()->toArray()));
   echo '0';
   }else{
   $commentObj = new Comment;
   $commentSave = $commentObj->create($temp);

   $comment['comment_id'] = $commentSave->id;
   $user = User::where('id',$temp['mcoUserId'])->first();

   /*-----------Send Mail to review owner----------*/
     $review = Review::where('mraReviewId',$temp['mcoReviewId'])->first();
	 if($review->mraNotify=='true'){
     $row = array('URL' =>URL::asset('/venue/desc/'.$review->mraBussinessId.'/review'),'SALON' => $review->business->name,'USER' =>$commentSave->user->fname.' '.$commentSave->user->lname,'COMMENT' =>$temp['mcoComment']);

		$template = EmailTemplate::where('etmTemplateName','Comment Notification')->first();
		$content_customer =  Helper::bind_to_template($row,$template->etmTemplate);
		Helper::mailto($review->user->email,'Someone Comment on Your Review',$content_customer);
	 }
   /*-----------Send Mail to review owner----------*/

   ?>
	<div class="row comments">
	   <div class="col-md-2">
		  <img src="<?php echo $user->profile->profile_pic();?>" />
	   </div>
	   <div class="col-md-10">
		  <div class="row">
			 <div class="col-md-6">
				<span class="author" itemprop="author">
				<?php echo $user->fname.' '.$user->lname ;?></span>
			 </div>
		  </div>
		  <div class="row">
			 <div class="col-md-12">
				<?php echo $commentSave->mcoComment;?>
				<div class="row pull-right">
				   <div class="col-md-12">
					  <p class="actions">
						 <a href="<?php echo url('/salon/report/comment/'.$commentSave->id);?>" class="txt-link"> Report as inappropriate </a>
					  </p>
				   </div>
				</div>
			 </div>
		  </div>
	   </div>
	</div>
	<div class="account-bottomline"></div>
<?php



   }
     }
   }
   else
   {
   echo '0';
   }
   }
   }
   public function getfreeservice()
   {
    if(Request::Ajax())
   {
          if(Auth::id()){
         $userLoyaltyPoints = Helper::userLoyaltyPointsCount();
         //$userLoyaltyPoints = 10;
         $loyalcounter      = LoyaltyPoint::first();
            $counter           = $loyalcounter->tloLoyalCounter;




               if($userLoyaltyPoints<$counter){
   	echo '0';
               }else{
   	$average = UserBooking::where('status','4')->where('user_id',Auth::id())->where('loyalty_status','0')->skip(0)->take($counter)->avg('booking_price');
   	$average       = number_format($average,2);
   	$temp['mwlWalletAmount']  = $average;
   	$temp['mwlWalletUserNo']  = Auth::id();
   	$temp['mwlWalletType']    = '2';
   	$temp['mwlWalletOrderNo'] = '0';

   	Wallet::create($temp);
   	$data['loyalty_status'] = '1';
   	UserBooking::where('status','4')->where('user_id',Auth::id())->where('loyalty_status','0')->skip(0)->take($counter)->update($data);
	$wallet_amount = ltrim($average,"-") + Auth::user()->wallet_balance;
	if($wallet_amount > 0)
	{
        User::where("id",Auth::id())->update(array("wallet_balance" => $wallet_amount));
    }
   	echo $average;
   }

   }

   }
   }
   public function getautocomplete()
   {
    if(Request::Ajax())
   {

    		$keyword =     Input::get('keyword');

   $result  = Venue::where('postal_code','LIKE','%'.$keyword.'%')->groupBy('postal_code')->get();

   $result1 = Venue::where('state','LIKE','%'.$keyword.'%')->groupBy('state')->get();

   ?>
<ul id="country-list" class="country-list">
   <?php
      foreach($result as $country) {
      ?>
   <li onClick="selectCountry('<?php echo $country->postal_code; ?>');"><?php echo $country->postal_code; ?></li>
   <?php } ?>
   <?php
      foreach($result1 as $country1) {
      ?>
   <li onClick="selectCountry('<?php echo $country1->state; ?>');"><?php echo $country1->state; ?></li>
   <?php } ?>
</ul>
<?php
   }
   }

   public function getautocompletesalon()
   {
   if(Request::Ajax())
   {

    		$keyword =     Input::get('keyword');

   $result  = Business::where('name','LIKE','%'.$keyword.'%')->where('salon_frontend_status', "1")->groupBy('name')->get();



   ?>
<ul id="country-list">
   <?php
      foreach($result as $salon) {
      ?>
   <li class="selectsalon" data="<?php echo $salon->name; ?>">
       <?php
       if(strlen($salon->name) <= 20){
         echo $salon->name;
       }else{
            echo substr($salon->name,0,20).'...';
       }
       ?>
   </li>
   <?php } ?>
</ul>
<?php
   }
   }

   public function get_area()
   {
	   if(Request::Ajax())
	   {
	     $city = City::where('mciCityName',Input::get('city'))->first();

		 $area_list = 	Area::where('marCityNo',$city->mciCityNo)->get();
		 $str ='<option value="">Choose Area</option>';
		 foreach($area_list as $area)
		 {
		     $str.='<option value="'.$area->marAreaName.'">'.$area->marAreaName.'</option>';
		 }
		 echo $str;
	   }
   }

   public function set_area(){
        if(Request::Ajax()){
            $city_id = Input::get('city_id');
            if($city_id == ""){
                $area_list = Area::where("display_search_status","1")
                                    ->get();
            }else{
                $area_list = Area::where('marCityNo',$city_id)
                                    ->where("display_search_status","1")
                                    ->get();
            }

            $str ='';
            foreach($area_list as $area)
            {
                $str .= '<li>';
                $str .= '<input type="checkbox" name="areas" id="areas" value="'.$area->marAreaName .' "checked > ';
                $str .= '<label for="areas"><span></span>'. $area->marAreaName .'</label>';
                $str .= '</li>';
            }
            echo $str;
        }
        exit;
   }

   public function set_area_profile(){
        if(Request::Ajax()){
            $city_id = Input::get('city_id');
            $area_list = Area::where('marCityNo',$city_id)
                            ->where("display_search_status","1")
                            ->get();
            $str ='';
            foreach($area_list as $area)
            {
                $str .= '<option value="'.$area->marAreaName .'">'.$area->marAreaName;
                $str .= '</option>';
            }
            echo $str;
        }
        exit;
   }

   public function employeeListingOnCheckout(){
        if(Request::Ajax()){
            $str ='';
            $bid = Input::get('bid');
            $sid = Input::get('sid');
            $token = Input::get("_token");
            $services = Services::where('id',base64_decode($sid))->first();
            if($services->pricing_type == "by-employee-cat"){
                $cart = AddCart::where('business_id',$bid)
                                ->where('service_id',base64_decode($sid))
                                ->where("token",$token)
                                ->first();

                $employeePricingCaption = EmployeePricingLevel::where("mepEmployeePricingLevelNo",$cart->employeePricingLevel)->first();
                $employeeArray = json_decode($employeePricingCaption->mepTeamMembers);

                foreach($employeeArray as $employeeArrayKey => $employeeArrayVal){
                    $employee = Employee::where("id",$employeeArray[$employeeArrayKey])->first();
                    $str .= "<option value='". $employeeArray[$employeeArrayKey] ."'>". $employee->fname.' '.$employee->lname ."</option>";
                }
                ?>
                <?php
            }else{
                $employeeListing = Employee::where('business_id' ,$bid)->get();
                foreach($employeeListing as $caption => $value){
                    $str .= "<option value='". $value->id ."'>". $value->fname ."</option>";
                }
            }

            echo $str;
        }
        exit;
   }

//-----------------------------------------asap ajax-----------------------------------------------//
   public function language_change(){
        if(Request::Ajax()){
            $language = Input::get('L');
            if(isset(Auth::User()->id) && !empty(Auth::User()->id)){
              $id = Auth::User()->id;
              //update preffered lanuage in db
              DB::table('users')
              ->where('id', $id)
              ->update(array('preferred_lang' => $language));
            }
            // set in session also
            Session::set("language",$language);
            return "1";
            exit;
        }
   }
}
?>
