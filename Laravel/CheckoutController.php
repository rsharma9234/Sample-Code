<?php
/**
 * Created by PhpStorm.
 * User: arun
 * Date: 25/9/15
 * Time: 10:05 PM
 */


class CheckoutController extends BaseController{
    public $layout = 'layouts.main';

    public function index($business_id = ''){
        $current_user = Auth::id();
        $token = Session::get("_token");
        //get salon from db of particular business id//
        $total_booking = AddCart::where('business_id',$business_id)
                                    ->where('token',$token)->get();

        $total_booking_withBookingingDate = AddCart::where('business_id',$business_id)
                        ->where('token',$token)
                        ->where('booking_date',"!=","0000-00-00 00:00:00")
                        ->get();

        if(isset($total_booking) && count($total_booking)){
            $data['total_bookings'] = $total_booking;
        }else{
            return Redirect::to('/');
        }

        $data['total_booking_withBookingingDate'] = $total_booking_withBookingingDate;
        $data['total_bookings'] = $total_booking;
        $data['employee_listing'] = Employee::where('business_id' ,$business_id)->get();
        $data['business_id'] = $business_id;
        $this->layout->nest('content', 'checkout.index',$data);
    }

    public function insert_cart($business_id = ""){
        $token = Session::get("_token");

        if($business_id == ""){
            return Redirect::to('/search');
        }else{
            //check if ths service related to business
            $service_id = base64_decode($_POST['sid']);
            $checkServiceExistBusiness = Services::where('business_id',$business_id)->where('id' ,$service_id)->first();
            if(isset($checkServiceExistBusiness) && count($checkServiceExistBusiness) > 0){
                if(Auth::id()){
                    $user_id = Auth::id();
                }else{
                    $user_id = 0;
                }

                //delete from cart if this user had added another sevice from another business//

                AddCart::where('token',$token)
                        ->where('business_id','!=',$business_id)
                        ->delete();

                AddCart::where('token',$token)
                        ->where('booking_type','=',"2")
                        ->delete();
                //check for existing same cart service

                $alreadyInCart = AddCart::where('token',$token)
                                            ->where('business_id','=',$business_id)
                                            ->where('service_id','=',$service_id)
                                            ->get();
                if(isset($alreadyInCart) && count($alreadyInCart) > 0){
                       return Redirect::to('/checkout/'.$business_id)->with("message","This Service already exist in cart!");
                }else{
                    if(isset($_POST['mple'])){
                        $mple = explode("|",$_POST['mple']);
                        if(isset($checkServiceExistBusiness) && count($checkServiceExistBusiness) > 0){
                            $multipleduration =  $checkServiceExistBusiness->multipleduration;
                            $multipledurationArray = explode(",",$multipleduration);
                            if($checkServiceExistBusiness->pricing_type == "by-employee-cat"){
                                $multiple_pricesArray = array();
                                $multiplesale_priceArray = array();
                                $multipledurationArray = array();
                                $multipleCaptionArray = array();

                                $elevelprice = $checkServiceExistBusiness->elevelprice;
                                $elevelsale_price = $checkServiceExistBusiness->elevelsale_price;
                                $elevelcaption = $checkServiceExistBusiness->elevelcaption;
                                $elevelduration = $checkServiceExistBusiness->elevelduration;

                                $multipledurationArray = explode(",",$elevelduration);
                            }
                            $original_price = base64_decode($mple[3]);
                            $caption = $mple[2];
                            $price = base64_decode($mple[1]);
                            $duration = $multipledurationArray[$mple[0]];
                            $duration = Helper::minutestoTime($duration);

                            $insertCheckout['employeePricingLevel'] = $caption;
                        }
                    }else{
                        $original_price = $checkServiceExistBusiness->price;
                        $price = $checkServiceExistBusiness->price_after_all_discount;
                        $duration = $checkServiceExistBusiness->duration;
                    }

                    Session::set('bid',$business_id);
                    $insertCheckout['price'] = $price;
                    $insertCheckout['original_price'] = $original_price;
                    $insertCheckout['duration'] = $duration;
                    $insertCheckout['business_id'] = $business_id;
                    $insertCheckout['service_id'] = $service_id;
                    $insertCheckout['user_id'] = $user_id;
                    $insertCheckout['token'] = $token;
                    $insertCheckout['booking_type'] = "1";

                    AddCart::create($insertCheckout);
                    Session::set("booking_type","1");
                    return Redirect::to('/checkout/'.$business_id);
                }
            }else{
                return Redirect::to('/checkout/'.$business_id);
            }
        }
    }

    public function evoucher_cart($business_id = ""){
        $token = Session::get("_token");

        if($business_id == ""){
            return Redirect::to('/search');
        }else{
            //check if ths service related to business
            $service_id = base64_decode($_POST['sid']);
            $checkServiceExistBusiness = Services::where('business_id',$business_id)->where('id' ,$service_id)->get();
            if(isset($checkServiceExistBusiness) && count($checkServiceExistBusiness) > 0){
                if(Auth::id()){
                    $user_id = Auth::id();
                }else{
                    $user_id = 0;
                }

                //delete from cart if this user had added another sevice from another business//

                AddCart::where('token',$token)
                        ->where('business_id','!=',$business_id)
                        ->delete();

                AddCart::where('token',$token)
                        ->where('booking_type','=',"1")
                        ->delete();
                //check for existing same cart service

                $alreadyInCart = AddCart::where('token',$token)
                                        ->where('business_id','=',$business_id)
                                        ->where('service_id','=',$service_id)
                                        ->get();

                $last_query = DB::getQueryLog();

               // echo "<pre>";print_r($last_query);die;

                if(isset($alreadyInCart) && count($alreadyInCart) > 0){
                       return Redirect::to('/checkout/'.$business_id)->with("message","This Service already exist in cart!");
                }else{
                    $checkServiceExistBusiness = Services::where('id' ,$service_id)->first();

                    if(isset($_POST['mple'])){
                        $mple = explode("|",$_POST['mple']);
                        if(isset($checkServiceExistBusiness) && count($checkServiceExistBusiness) > 0){
                            $multipleduration =  $checkServiceExistBusiness->multipleduration;
                            $multipledurationArray = explode(",",$multipleduration);
                            $price = base64_decode($mple[1]);
                            $duration = $multipledurationArray[$mple[0]];
                            $original_price = base64_decode($mple[3]);
                        }
                    }else{
                        $original_price = $checkServiceExistBusiness->price;
                        $price = $checkServiceExistBusiness->price_after_all_discount;
                        $duration = $checkServiceExistBusiness->duration;
                    }

                    Session::set('bid',$business_id);
                    $insertCheckout['price'] = $price;
                    $insertCheckout['original_price'] = $original_price;
                    $insertCheckout['duration'] = $duration;
                    $insertCheckout['business_id'] = $business_id;
                    $insertCheckout['service_id'] = $service_id;
                    $insertCheckout['user_id'] = $user_id;
                    $insertCheckout['token'] = $token;
                    $insertCheckout['booking_type'] = "2";

                    AddCart::create($insertCheckout);
                    Session::set("booking_type","2");
                    return Redirect::to('/checkout/'.$business_id);
                }

            }else{
                return Redirect::to('/checkout/'.$business_id);
            }
        }
    }

    public function delete($bid='',$sid=''){
        $token = Session::get("_token");
        if($bid == '' && $sid == ''){
            return Redirect::to('/');
        }else{
           // echo $bid.' '.$sid;die;
            $current_user = Auth::id();
            AddCart::where('business_id',$bid)
                        ->where('service_id',$sid)
                        ->where('token',$token)
                        ->delete();
            return Redirect::to('/checkout/'.$bid)->with('message','Successfully Deleted Service from your basket!');
        }
    }
    public function reschedule(){
      if(Input::All())
      {
        $data = Input::All();
        $id = Input::get('bid');
        $sid = Input::get('sid');
        $eid = Input::get('eid');
        $userid = Input::get('userid');
        $booking_date = Input::get('booking_date');
        $booking_price = Input::get('price');
        $createdAt = Input::get('created_at');

        $hrsStart = intval(substr(Input::get('bookedDate'),0,2));
        //converted mintues to hrs
        $minStart = intval(substr(Input::get('bookedDate'),3,2))/60;
        $finalBookingDate = date("Y-m-d h:i:s", (strtotime(Input::get('order_date')) + ($hrsStart + $minStart) * 60 * 60));


        $hrs = intval(substr(Input::get('int'),0,2));
        //converted mintues to hrs
        $min = intval(substr(Input::get('int'),3,2))/60;
        $finalBookingEndDate = strtotime($finalBookingDate) + ($hrs + $min) * 60 * 60;
        $finalBookingEndDate = date('Y-m-d h:i:s',$finalBookingEndDate);
        UserBooking::where('business_id',$id)
                    ->where('created_at',$createdAt)
                    ->where('booking_date',$booking_date)
                    ->where('booking_price',$booking_price)
                    ->where('service_id',$sid)
                    ->where('user_id',$userid)
                    ->where('employee_id',$eid)
                    ->update(['booking_date'=>$finalBookingDate,'booking_date_end'=>$finalBookingEndDate]);
        return Redirect::to('/profile')->with('message','Reschedule Successful');

      }
    }

    public function order($order_id=''){
        $token = Session::get("_token");
		 $bid ='';
        if(Input::All()){

            if(Input::get("type") == "gift_card"){

				$data = Input::all();

				$rules = array(
					"amount" => "required|numeric",
					"quantity" => "required|numeric"
				);

				if($data['send_via']=='email')
				{
					$rules1 = array(
						"email" => "required|email"
					);
					$rules = array_merge($rules,$rules1);
				}

				$validator = Validator::make($data,$rules);

				if($validator->fails()){
					return Redirect::to('/gift-card')->withErrors($validator);
				}else{

                $whr = array("token" => $token,"booking_type" => 3);
                $whrGiftCard = AddCart::where($whr)->get();
                if(isset($whrGiftCard) && count($whrGiftCard) > 0){

                }else{
                    $data['token'] = $token;
                    $data['booking_type'] = 3;
                    AddCart::create($data);
                }

                Session::set("amount",Input::get("amount"));
                Session::set("quantity",Input::get("quantity"));
                Session::set("send_via",Input::get("send_via"));
				if(isset($data['email']) && !empty($data['email'])){
                 Session::set("send_to_email",Input::get("email"));
				}
                Session::set("booking_type","3");
                $this->layout->nest('content', 'checkout.order');
				}
            }else{
                $current_user = Auth::id();
                $employee_id = Input::get('employee_id');
                $bid = Input::get('bid');
                $order_date = Input::get('order_date').' '.Input::get('bookedDate');

                //$avaliablity = Employee::is_avail($employee_id,$order_date,'');
               /* if($avaliablity == false){
                    return Redirect::to('/checkout/'.$bid)->with('message','Already Booked, Please choose another date.');
                }*/
               // $order_date = date("Y-m-d H:i:s",strtotime($order_date));

               /* if($avaliablity == true){

                    AddCart::where('business_id',$bid)
                                ->where('token',$token)
                                ->update(array('employee_id' => $employee_id,'booking_date' => $order_date));
                }*/
            }
        }

        $data['bid'] = $bid;
        $data['order_id'] = "";
        $order_id_decoded = $order_id;
        $order_id = base64_decode($order_id_decoded);
        $data['order_id'] = $order_id_decoded;
        $cart = AddCart::where('token',$token)
                        ->where('booking_date',"!=","0000-00-00 00:00:00")
                        ->get();
        //if already order exist
        $orderExist = UserBooking::where("order_id",$order_id)->get();
        if(isset($orderExist) && count($orderExist) > 0){
            $data['cart'] = $orderExist;
        }else{
            if(isset($cart) && count($cart) > 0){
                //  echo "B";die;
                $data['cart'] = $cart;
            }else{
                //echo "C";die;
                return Redirect::to('/')->with('message','Your Cart is empty.');
            }
        }
        if(null !== Auth::id()){
          $current_user_id = Auth::id();
          $data['wallet_amount']=User::find($current_user_id)->wallet_balance;
        }
        $this->layout->nest('content', 'checkout.order',$data);
    }

    public function evoucher_order(){
        $token = Session::get("_token");
        $cart = AddCart::where('token',$token)->get();
        if(isset($cart) && count($cart) > 0){
          //  echo "B";die;
            $data['cart'] = $cart;
            $this->layout->nest('content', 'checkout.order',$data);
        }else{
            //echo "C";die;
            return Redirect::to('/')->with('message','Your Cart is empty.');
        }
    }

    public function process(){
        if(Input::All()){
            $order_id = base64_decode(Input::get("order_id"));
            $data = Input::All();
            $payAtVenue = Input::get("pay_at_venue");

            $current_user = Auth::id();
            if(!isset($current_user) && $current_user == ""){

                //new user register//
                $pass = "123456";

                $user['fname'] = $data['fname'];
                $user['lname'] = $data['lname'];
                $user['email'] = $data['email'];
                $user['role'] = 3;
                $user['password'] = Hash::make($pass);

                $rules = array('email' => 'required|email||unique:users','fname' => 'required','lname' => 'required');
                $validator = Validator::make(Input::All(),$rules);
                if($validator->fails()){
                    return Redirect::to('/Checkout/order')->with("error","Email Already Exists!")->withInput();
                }else{
                    $userObj = new User;
                    $userSave = $userObj->create($user);

                    $profile['user_id'] = $userSave->id;


                    $profileObj = new Profile;
                    $profileSave = $profileObj->create($profile);
                    //email send to user when registered
                    $firstname = $user['fname'] .' '.$user['lname'] ;
                    $messageEmail = "";
                    $messageEmail = 'Your Login Credentials:<br>';
                    $messageEmail .= 'Email : '.$user['email'].'<br>';
                    $messageEmail .= 'Password : '.$pass.'<br>';

                    Helper::email_template($data['email'],'',$messageEmail,'Signed up succesflly',$firstname );
                    //

                    //login into site//
                    $where = array("email" => $data['email'], 'password' => $pass);
                    Auth::attempt($where);
                }
            }

            //get user services from add to cart//
            Session::set("wallet",Input::get("wallet"));
            $token = Session::get("_token");
            $booking_type = Session::get("booking_type");
            $current_user = Auth::id();

            if($booking_type == "3"){
                $cart = AddCart::where('token',$token)
                                ->where('booking_date',"!=","0000-00-00 00:00:00")
                                ->get();
            }else{
                $cart = AddCart::where('token',$token)
                                ->get();
            }
            $totalCart = 0;
            $orderExist = Order::where("id",$order_id)->get();
            if(isset($cart) || count($cart) > 0 || isset($orderExist) || count($orderExist) > 0){
                $pay_with_paypal = Input::get("pay_with_paypal");
                $pay_at_venue = Input::get("pay_at_venue");
                $pay_with_card = Input::get("pay_with_card");

                if($pay_with_card == 1){
                    $code = Input::get("cardCode_card");
                    $wallet = Input::get("wallet_card");
                }elseif($pay_with_paypal == 1){
                    $code = Input::get("cardCode_card");
                    $wallet = Input::get("wallet_card");
                }else{
                    $code = "";
                    $wallet = "";
                }

                //wallet and gift card check simuntaneously//

                $subtotal = Session::get("subtotal");
                $discountAmt = 0;
                $discountAmtPercentage = 0;
                $discountAmtActual = 0;
                $codeExistID = 0;
                $totalAfterDiscount = $subtotal;
                $discountType = "";
                $usedWallet = 0;
                $restMoney = 0;

               if(Session::get("booking_type") == 3){
                    $booking_type = 3;
                    $order['order_status'] = 1;
                    $order['card_price'] = Session::get("amount");
                    $order['quantity'] = Session::get("quantity");
                    $order['send_via'] = Session::get("send_via");
					if($order['send_via']=='email')
					{
                     $order['send_via_email']       = Session::get("send_to_email");
					 $orderUpdate['send_via_email'] = Session::get("send_to_email");
                    }
                    $orderUpdate['order_status'] = 1;
                    $orderUpdate['card_price'] = Session::get("amount");
                    $orderUpdate['quantity'] = Session::get("quantity");
                    $orderUpdate['send_via'] = Session::get("send_via");
                    $totalAfterDiscount = Session::get("amount")*Session::get("quantity");
                }else{
                    if(isset($orderExist) && count($orderExist) > 0){
                        $orderUpdate['order_status'] = 1;
                        $orderUpdate['card_price'] = Session::get("amount");
                        $orderUpdate['quantity'] = Session::get("quantity");
                        $totalAfterDiscount = Session::get("amount")*Session::get("quantity");
                    }else{
                        if(isset($cart) && count($cart) > 0){
                            $order['business_id'] = $cart[0]->business_id;
                            foreach($cart as $cartKey => $cartVal){

                                $booking_type = $cartVal->booking_type;
                               // $totalCart = $cart[$cartKey]->price + $totalCart;
                            }
                        }
                    }
                }
                if($code != ""){
                    //check code from database if its exist as promo code or gift card codes//
                    $codeType = array("1","3");
                    $codeExist = Voucher::where("mvcVoucherCode" , $code)
                                        ->where("mvcStatus","0")
                                        ->whereIn('mvcVoucherTypeNo', $codeType)
                                        ->first();



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
                                if($discountType == "0"){
                                    $discountAmt = $codeExist->mvcDiscount;
                                    $order['evoucherDiscount']  =  $discountAmt;
                                    $orderUpdate['evoucherDiscount']  =  $discountAmt;

                                }elseif($discountType == "1"){
                                    $discountAmtPercentage = $codeExist->mvcDiscount;
                                }
                                if($discountAmtPercentage > 0){
                                    $discountAmt = $discountAmtPercentage/100 * $subtotal;
                                    $order['evoucherDiscount']  =  $discountAmt;
                                    $orderUpdate['evoucherDiscount']  =  $discountAmt;
                                    $totalAfterDiscount = $subtotal - $discountAmt;
                                }else{
                                    if($subtotal >= $discountAmt){
                                        $totalAfterDiscount = $subtotal - $discountAmt;
                                    }elseif($discountAmt >= $subtotal){
                                        $totalAfterDiscount = 0;
                                        $restMoney = $discountAmt - $subtotal;

                                        //rest money should go to wallet
                                    }
                                }

                            }
                        }else{
                            $discountType = $codeExist->mvcDiscountType;
                            $discountAmtActual = $codeExist->mvcDiscount;
                            if($discountType == "0"){
                                $discountAmt = $codeExist->mvcDiscount;
                                $order['evoucherDiscount']  =  $discountAmt;
                                $orderUpdate['evoucherDiscount']  =  $discountAmt;
                            }else if($discountType == "1"){
                                $discountAmtPercentage = $codeExist->mvcDiscount;

                            }
                            if($discountAmtPercentage > 0){
                                $deductedCodeAmt = $discountAmtPercentage/100 * $subtotal;
                                $order['evoucherDiscount']  =  $deductedCodeAmt;
                                $orderUpdate['evoucherDiscount']  =  $deductedCodeAmt;

                                $totalAfterDiscount = $subtotal - $deductedCodeAmt;
                            }elseif($discountAmt > 0){
                                if($subtotal >= $discountAmt){
                                    $totalAfterDiscount = $subtotal - $discountAmt;
                                }elseif($discountAmt >= $subtotal){
                                    $totalAfterDiscount = 0;
                                    $restMoney = $discountAmt - $subtotal;

                                    //rest money should go to wallet
                                }
                            }
                        }

                        //echo "C".$totalAfterDiscount;die;
                        //used voucher update
                       Voucher::where('mvcVoucherNo',$codeExist->mvcVoucherNo)->update(array('mvcStatus' => "3"));

                       $order['evoucher_id']  = $codeExist->mvcVoucherNo ;
                       $orderUpdate['evoucher_id']  = $codeExist->mvcVoucherNo ;
                    }
                }

                if($wallet > 0){
                    $wallet_amount = Auth::user()->wallet_balance;
                    if($wallet <= $wallet_amount){
                        if($wallet <= $totalAfterDiscount){
                            echo "A";
                            $totalAfterDiscount = $totalAfterDiscount - $wallet;
                            $wallet_used = $wallet;
                        }elseif($totalAfterDiscount <= $wallet){
                            $wallet_used = $wallet;
                            $totalAfterDiscount = $totalAfterDiscount - $wallet;
                            echo "C";
                        }else{
                            echo "D";
                            $totalAfterDiscount = 0;
                            $wallet_used = $wallet;
                        }
                     }
                     $orderUpdate['wallet_used']  = $wallet_used ;
                     $order['wallet_used'] = $wallet_used;
                }

            }else{
                return Redirect::to("/")->with("message","Your Cart has been empty!");
            }

            //insert into order//

            if(isset($payAtVenue) && $payAtVenue == "1"){

                $order['payment_type'] = 'Venue';
                $order['order_status'] = 1;
                $order['payment_status'] = 0;
                $order['business_id'] = $cart[0]->business_id;
            }else{

                $order['booking_type'] = $booking_type;
                $order['payment_type'] = 'Paypal';
                $order['order_status'] = 0;
                $order['payment_status'] = 1;
				$order['business_id'] = $cart[0]->business_id;
            }

            $order['total_amount'] = $totalAfterDiscount;

            $order['user_id'] = $current_user;

            if(Input::get("email_receipt") == "Y"){
                $receipt = 2;//not receipt, user himself will order
            }else{
                $receipt = 1;
            }


            $order['for_receipt'] = $receipt;
            $order['receipt_name'] = Input::get("receipt_name");
            if($order_id > 0){
                $orderExist = Order::where("id",$order_id)->get();
                if(isset($orderExist) && count($orderExist) > 0){
                    //order_update
                    Order::where("id",$order_id)
                           ->update($orderUpdate);
                }else{
                    $orderInserted =  Order::create($order);
                    $order_id = $orderInserted->id;
                }
            }else{
                if($booking_type == "1" || $booking_type == "2"){
                    $order['business_id'] = $cart[0]->business_id;
                }

                $orderInserted =  Order::create($order);
                $order_id = $orderInserted->id;
            }



            //deduct from wallet//
             //update wallet
            if(isset($wallet_used) && $wallet_used > 0){
                $transaction['mwlWalletUserNo'] = Auth::id();
                $transaction['mwlWalletAmount'] = '-'.$wallet_used;
                $transaction['mwlWalletType'] = 1;
                $transaction['mwlWalletOrderNo'] = $order_id;
                $transaction['mwtTransactionNo'] = "Wallet";

                $wallet = Wallet::create($transaction);

                $total_wallet = Wallet::where("mwlWalletUserNo",$current_user)
                                        ->get();

                $wallet_amount = 0.0;
                $wallet_amount = Auth::user()->wallet_balance - $wallet_used;

                if($wallet_amount > 0 || $wallet_amount < 0 || $wallet_amount == 0){
                User::where("id",$current_user)
                    ->update(array("wallet_balance" => $wallet_amount));
                }
            }

            //update rest coupon money in total wallet balance
            if(isset($restMoney) && $restMoney > 0){
                $transaction['mwlWalletUserNo'] = Auth::id();
                $transaction['mwlWalletAmount'] = $restMoney;
                $transaction['mwlWalletType'] = 1;
                $transaction['mwlWalletOrderNo'] = $order_id;
                $transaction['mwtTransactionNo'] = "Rest Money";

                $wallet = Wallet::create($transaction);

                $wallet_amount = 0.0;
                $wallet_amount = Auth::user()->wallet_balance + $restMoney;

                if($wallet_amount > 0 || $wallet_amount < 0 || $wallet_amount == 0){
                User::where("id",$current_user)
                    ->update(array("wallet_balance" => $wallet_amount));
                }

            }
            //update order wallet used

            //update order items//
            if(isset($booking_type) && $booking_type == "1" ||$booking_type == "2"){
            // set order id in session, it will be used at the time when user wil return from paypal withput doing payment.
            Session::set("orderid",$order_id);
                foreach($cart as $cartKey => $cartVal){
                    $duration = $cart[$cartKey]->duration;

                    $duration = Helper::totalMinutes($duration);

                    $price = $cart[$cartKey]->price;
                   // if(strtotime($cart[$cartKey]->booking_date) > 0){
					    $encript =  md5(time());

                        $booking_end = date('Y-m-d H:i:s',strtotime('+'.$duration.' minutes',strtotime($cart[$cartKey]->booking_date)));

                        $orderItem['service_duration'] = $cart[$cartKey]->duration;
                        $orderItem['booking_price'] = $price;
                        $orderItem['booking_type'] = $cart[$cartKey]->booking_type;
                        $orderItem['order_id'] = $order_id;
                        $orderItem['business_id'] = $cart[$cartKey]->business_id;
                        $orderItem['service_id'] = $cart[$cartKey]->service->id;
                        $orderItem['booking_date'] = $cart[$cartKey]->booking_date;
                        $orderItem['employee_id'] = $cart[$cartKey]->employee_id;
                        $orderItem['booking_date_end'] = $booking_end;
                        $orderItem['status'] = 1;
                        $orderItem['user_id'] = $current_user;
                        $orderItem['channel'] = "UKSALON";
						$orderItem['authmail'] =  $encript;
                        //echo "<pre>";print_r($orderItem);
                        if(count($orderExist) == 0){
                            $orderInserted =  UserBooking::create($orderItem);
                        }
                    //}
                }
              }
            if(isset($payAtVenue) && $payAtVenue == "1"){

                $token = Session::get("_token");
                //delete cart
                AddCart::where('token',$token)->delete();
                Session::set("order_id",$order_id);
                return Redirect::to('payment/success');
                exit;
            }

            return Redirect::to('payment/'.$order_id);
            exit;
        }
    }
}
