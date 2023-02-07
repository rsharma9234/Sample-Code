<style>
.bootbox-alert .modal-dialog {
    margin: 195px auto;
    width: 400px;
}

.modal-body {
    font-size: 16px;
    margin: 0 auto;
    padding: 15px;
    position: relative;
    width: auto;
}

.modal-footer {
    border-top: 1px solid #e5e5e5;
    padding: 12px;
}
</style>

 @if ($errors->has())
<div class="alert alert-danger">
    @foreach ($errors->all() as $error)
        {{ $error }}<br>
    @endforeach
</div>
@endif
<script>
function set_pay_venue(){
    $(".tab_default_1_class").click(function(){
        $("#pay_at_venue").val("0");
        $("#pay_with_paypal").val("0");
        $("#pay_with_card").val("1");
    })

    $(".tab_default_2_class").click(function(){
        $("#pay_at_venue").val("1");
        $("#pay_with_paypal").val("0");
        $("#pay_with_card").val("0");
    })

    $(".tab_default_3_class").click(function(){
        $("#pay_at_venue").val("0");
        $("#pay_with_paypal").val("1");
        $("#pay_with_card").val("0");
    })
}
$(window).load(function(){
     $("#pay_at_venue").val("0");
     $("#pay_with_paypal").val("0");
     $("#pay_with_card").val("1");
})
$(function(){

    $("input").keypress(function(){
        $("input").removeClass("alert-danger");
    })

    $("#checkout_form").submit(function(){
        fname = $("input[name=fname]").val();
        lname = $("input[name=lname]").val();
        email = $("#form_email").val();

        if(fname == ""){
            $("input[name=fname]").focus();
            $("input[name=fname]").addClass("alert-danger");
           // alert("Please enter your First Name");
           // return false;
           bootbox.alert("Please enter your First Name!", function() {
                //alert("Please enter your Email");
           });
           return false;
        }
        $("input").removeClass("alert-danger");

        if(lname == ""){
            $("input[name=lname]").addClass("alert-danger");
            $("input[name=lname]").focus();
            //alert("Please enter your Last Name");
            //return false;
            bootbox.alert("Please enter your Last Name!", function() {
                //alert("Please enter your Email");
            });
            return false;
        }

        $("input").removeClass("alert-danger");

        if(email == ""){
            $("input[name=email]").addClass("alert-danger");
            $("input[name=email]").focus();
            bootbox.alert("Please enter your Email!", function() {
                //alert("Please enter your Email");
            });
            return false;
        }

        $("input").removeClass("alert-danger");

    })

    set_pay_venue();
})
</script>

@if (Session::has('message'))
<div class="alert alert-info">
    {{ Session::get('message')}}
</div>
@endif
<?php
$user = Auth::user();
if(isset($user) && count($user) > 0){
    $wallet_balance = $user->wallet_balance;
}else{
    $wallet_balance = 0;
}
?>
<script>
/*function checkWallet(userInputWallet,__walletBalance){
    userInputWallet = $("input[name=wallet]").val();
    $("#discount").html(userInputWallet+' &pound;');
    if(userInputWallet > __walletBalance){
        alert("Please fill less than or equal "+__walletBalance+" amount.");
        $("input[name=wallet]").val("0");
        $("#discount").html('0 &pound;');
    }
}

$(function(){
    __walletBalance = "";
    userInputWallet = "";

    var __walletBalance = {{ $wallet_balance; }};
    var userInputWallet = $("input[name=wallet]").val();
    $("input[name=wallet]").blur(function(){
       checkWallet(userInputWallet,__walletBalance);
    })
})

$(window).on("load",function(){
    var __walletBalance = {{ $wallet_balance; }}
    var userInputWallet = $("input[name=discount]").val();
    checkWallet(userInputWallet,__walletBalance);
})*/
</script>


<!-- begin:header -->
<div id="header" class="heading">
   <div class="container">
      <div class="row">
         <ol class="breadcrumb">
            <li><a href="{{ url(); }}">Home</a></li>
			@if(isset($bid))
            <li><a href="{{url('/checkout/'.$bid)}}">Order</a></li>
			@else
			<li><a href="{{url('/gift-card')}}">Order</a></li>
			@endif
            <li class="active">Payment</li>
         </ol>
      </div>
   </div>
</div>

<!-- end:header -->
<!-- end:content -->
<!-- begin:news -->
<div id="news-main">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
         <div class="text-center account-margins">
            <?php
            if(isset($user) && count($user) > 0){

            }else{
                ?>
                Already have account?<a href="#" class="signin signin-pad-extra" data-toggle="modal" data-target="#loginBox">Click here to login</a> or fill following details to place order.
                <?php
            }
            ?>
            </div>
            <div class="heading-title">
               <h2>{{ Helper::get_language($id=128) }}</h2>
            </div>

         </div>
      </div>
    <?php
    //echo "<pre>";print_r(Input::All());die;
    ?>
      <div class="row">
         <div class="col-lg-12">
            <div class="panel">
               <div class="row">
                  <div class="col-md-8">
                     <div class="col-lg-12">
                        <div class="panel panel-default">
                           <div class="panel-body">
                              <h3><i class="fa fa-user"></i> {{ Helper::get_language($id=69) }}</h3>
                              <hr />
                              {{ Form::open(array('url' => url().'/checkout/process','method' => 'post','id' => 'checkout_form')); }}
                                 <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                       <input type="text" name="fname" placeholder="First Name" class="form-control input-md" value="<?php if(isset($user) && count($user) > 0) : echo Auth::user()->fname; endif; ?>" >
                                    </div>
                                 </div>

                                 <div class="col-md-6 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                       <input type="text" name="lname" placeholder=" Last Name" class="form-control input-md" value="<?php if(isset($user) && count($user) > 0) : echo Auth::user()->lname; endif; ?>" >
                                    </div>
                                 </div>

                                 <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                       <input type="email" id="form_email" name="email" placeholder="Email" class="form-control input-md" value="<?php if(isset($user) && count($user) > 0) : echo Auth::user()->email; endif; ?>" >
                                    </div>
                                 </div>

                                 <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="form-group">
                                       <div class="row">
                                          <div class="Check_btn">
                                             <input type="checkbox"  id="c1">
                                             <div id="c2"></div>
                                          </div>
                                          <span>{{ Helper::get_language($id=127) }}</span>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                        <div class="form-group payment-method bottom-extra">
                                           <div class="row">
                                              <div class="radio-chck">
                                                 <span class="radio-yes radio-top-space">{{ Helper::get_language($id=71) }} </span>
                                                 <div class="radio_btn">
                                                    <input type="radio" name="email_receipt" id="b1" value="Y" checked />
                                                    <div id="a1"></div>
                                                 </div>
                                                 <span class="radio-yes radio-top-space">{{ Helper::get_language($id=82) }}</span>
                                                 <div class="radio_btn">
                                                    <input type="radio" name="email_receipt" value="N" id="b2"/>
                                                    <div id="a2"></div>
                                                 </div>
                                                 <span class="radio-no radio-top-space">{{ Helper::get_language($id=131) }}</span>
                                              </div>
                                           </div>
                                        </div>
                                     </div>
                                     <div class="col-md-6 col-sm-6 col-xs-6">
                                        <div class="form-group payment-method">
                                           <div class="row">
                                           <div class="col-md-12">
                                                <span class="radio-yes radio-top-space">
                                                    <input type="text" class="form-group form-control" value="" name="receipt_name" id="receipt" placeholder="Please Enter Recipient Name" style="display: none;"/>
                                                </span>
                                            </div>
                                           </div>
                                        </div>
                                     </div>
                                 </div>

                                 <div class="col-md-12 col-sm-12 col-xs-12 nopadding">
                                      <div class="col-xs-6 col-md-6">
                                          <div class="form-group">
                                              <label for="cardCode"><span class="hidden-xs">{{ Helper::get_language($id=76) }} / {{ Helper::get_language($id=129) }}</span><span class="visible-xs-inline">EXP</span> DATE</label>
                                              <input type="text" class="form-control cardCode" name="cardCode_card" value="" />
                                          </div>
                                      </div>
                                      <div class="col-xs-4 col-md-4">
                                          <div class="form-group">
                                              <label for="cardExpiry"><span class="hidden-xs">{{ Helper::get_language($id=78) }}</span></label>
                                              <input type="text" class="form-control wallet_used" value="" name="wallet_card" autocomplete="cc-exp" oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" />
                                          </div>
                                      </div>
                                      <div class="col-xs-2 col-md-2">
                                          <div class="form-group">
                                              <label for="wallet"><span class="hidden-xs"> Your wallet </span></label>
                                              <input type="text" class="form-control"  name="wallet_amount" value="<?php if(isset($wallet_amount) && !empty($wallet_amount)){ echo $wallet_amount; } else { echo "0"; }  ?>" readonly />
                                          </div>
                                      </div>
                                  </div>

                                  <div class="col-md-12 col-sm-12 col-xs-12  payment-method">
                                        <div class="col-xs-3 col-md-3 nopadding">
                                              <strong>{{ Helper::get_language($id=130) }}</strong>
                                        </div>
                                        <div class="col-xs-3 col-md-3 nopadding">
                                            <div class="radio_btn">
                                                <input type="radio" name="paypal_method" id="id11" value="0" class="tab_default_1_class" checked="checked"/>
                                                <div id="id1"></div>
                                             </div>
                                            <i class="fa fa-credit-card"></i> {{ Helper::get_language($id=72) }}
                                        </div>
										<div class="col-xs-3 col-md-3 nopadding">
                                            <div class="radio_btn">
                                                <input type="radio" name="paypal_method" id="id33" value="0" class="tab_default_3_class"/>
                                                <div id="id3"></div>
                                             </div>
                                            <i class="fa fa-paypal"></i> {{ Helper::get_language($id=74) }}
                                        </div>
                                        <div class="col-xs-3 col-md-3 nopadding">
                                             <?php
                                             if(Session::get("booking_type") == "1" && $cart[0]->business->pay_at_venue == 1){
                                                ?>
                                                <div class="radio_btn">
                                                    <input type="radio" name="paypal_method" id="id22" value="0" class="tab_default_2_class"/>
                                                    <div id="id2"></div>
                                                 </div>
                                                <i class="fa fa-map-marker"></i> {{ Helper::get_language($id=73) }}
                                                <?php
                                             }
                                             ?>
                                        </div>
                                  </div>
                           </div>
                        </div>

                        <input type="hidden" name="pay_with_card" value="1" id="pay_with_card"/>
                        <input type="hidden" name="pay_at_venue" value="0" id="pay_at_venue"/>
                        <input type="hidden" name="pay_with_paypal" value="0" id="pay_with_paypal"/>


						<div class="place-order-btn-text">
    						<input type="submit" value="Place Order" name="submit" class="btn btn-default form-control" style="background:#EBEBEB;"/>
                            </form>
						</div>
                     </div>
                  </div>
                  <div class="col-md-4 well">
                     <table class="table table-responsive">
                        <tr>
                           <td>
                              <div class="when1">
                                <div class="row">
                                    <div class="col-md-6 checkout-time">
                                <?php
                                if(Session::get("booking_type") == 1){
                                    ?>
                                    <div class="time">
                                        <?php
                                        echo date('H:i',strtotime($cart[0]->booking_date)).' '. date('A',strtotime($cart[0]->booking_date));
                                        ?>
                                     </div>
                                     </div>
                                     <div class="col-md-6 checkout-border-left">
                                     <div class="date">
                                        <span class="month">
                                        <?php
                                           echo date('d F',strtotime($cart[0]->booking_date));
                                           ?>
                                        </span><br>
                                        <span class="weekday">
                                        <?php
                                           echo date('l',strtotime($cart[0]->booking_date));
                                           ?>
                                        </span>
                                     </div>
                                     </div>
                                    <?php
                                }elseif(Session::get("booking_type") == 2){
                                    ?>
                                    <h3>Evoucher</h3>
                                    <?php
                                }elseif(Session::get("booking_type") == 3){
                                    ?>
                                    <h3>Gift Card</h3>
                                    <?php
                                }
                                ?>

          </div>
                              </div>
                           </td>
                        </tr>

                        <tr>
                           <td>
                              <h4>
                                <?php
                                if(Session::get("booking_type") == 3){
                                    echo "EverFabs Gift Card";
                                }else{
                                    echo $cart[0]->business->name;
                                }
                                ?>
                              </h4>
                           </td>
                        </tr>
                        <?php
                        if(Session::get("booking_type") == 3){
                            $cancel_date = date("Y-m-d H:i:s",strtotime('+11 months'));
                            $amount = Input::get("amount");
                            $quantity = Input::get("quantity");
                            $total_price = $amount*$quantity;
                            $orderCreate = $cart[0]->booking_date;


                            $calcelPolicy = "";

                            ?>
                            <tr>
                               <td><span class="pull-left">White</span><span class="pull-right"><strong>&pound;{{ $amount }}</strong></span></td>
                            </tr>
                            <tr>
                               <td><span class="pull-left">Quantity</span><span class="pull-right"><strong>{{ $quantity }}</strong></span></td>
                            </tr>

                            <tr id="codeDiscount" style="display: none;"></tr>
                            <tr id="walletDiscount" style="display: none;">
                               <td><span class="pull-left">Wallet</span><span class="pull-right"> <strong id="discount">&pound;</strong></span></td>
                            </tr>
                            <tr id="final_total">
                               <td><span class="pull-left">ORDER SUBTOTAL</span><span class="pull-right"> <strong>&pound;{{ $total_price; }}</strong></span></td>
                            </tr>
                            <?php
                        }else{
                           $total_price = 0;
                           $business = $cart[0]->business;
                           $calcelPolicy = $business->cancellation_policy;
                           //admin policy
                           $adminCancelPolicyArray = Policy::first();
                           $adminCancelPolicy = $adminCancelPolicyArray->tapCancellationPolicy;
                           //

                           $total_cancel = $calcelPolicy + $adminCancelPolicy;

                           foreach($cart as $cartKey => $cartVal){
                                $orderCreate = $cartVal->booking_date;
                                $price = 0;
                                if($cart[$cartKey]->price > 0){
                                    $price = $cart[$cartKey]->price;
                                }elseif($cart[$cartKey]->booking_price > 0){
                                    $price = $cart[$cartKey]->booking_price;
                                }
                                $total_price = $price + $total_price;
                           ?>
                            <tr>
                                <td>
                                    {{ $cart[$cartKey]->service->title; }}<br><br>
                                    <?php
                                      $get_cleanup_time=Services::where('business_id',$cart[$cartKey]->business_id)->where('id' ,$cart[$cartKey]->service_id)->first();


                                      $duration  = $cart[$cartKey]->duration;
                                      $cleanup = $get_cleanup_time->cleanup_time;
                                      $duration = (strtotime($duration) - strtotime($cleanup));
                                    ?>
                                        <p><a href="#"><span class="pull-left">{{ Helper::format_timer_result($duration) }}{{-- Helper::format_timer_result($cart[$cartKey]->duration) --}}</span> <span class="pull-right">&pound;{{ $price }}</span></a></p>
                                </td>
                            </tr>

                            <?php
                            }
                            //echo "<pre>";print_r($cartVal);die;
                            $cancel_date = date("Y-m-d H:i:s",strtotime($orderCreate.'-'.$total_cancel.' Hours '));
                            if(Session::get("booking_type") == 2){
                                $cancel_date = date("Y-m-d H:i:s",strtotime($cartVal->created_at.'+12 Days'));
                            }
                            //echo 'embossed '.$cancel_date.'<br>';
                            ?>
                            <tr id="codeDiscount" style="display: none;"></tr>
                            <tr id="walletDiscount" style="display: none;">
                               <td><span class="pull-left">Wallet</span><span class="pull-right"> <strong id="discount">&pound;</strong></span></td>
                            </tr>
                            <tr id="final_total">
                               <td><span class="pull-left">{{ Helper::get_language($id=84) }}</span><span class="pull-right"> <strong>&pound;{{ $total_price; }}</strong></span></td>
                            </tr>
                            <?php
                            if(isset($cart[0]->employee->fname) && $cart[0]->employee->fname != ""){
                                ?>
                                <tr>
                                   <td><span class="pull-left">{{ Helper::get_language($id=85) }}</span><span class="pull-right"><strong>{{ $cart[0]->employee->fname.' '.$cart[0]->employee->lname }}</strong></span></td>
                                </tr>
                                <?php
                            }
                        }
                        Session::set("subtotal",$total_price);
                        ?>
                        <tr>
                           <td>
                              <small> {{ Helper::get_language($id=86) }}</small>
                              <?php
                              if(strtotime($cancel_date) > 0){
                                    ?>
                                    <h4>Free cancellation/re-scheduling before {{ date('d F Y,H:i A',strtotime($cancel_date)) }} </h4>
                                    <?php
                              }else{
                                    echo "Non-Refundable";
                               }
                              ?>
                           </td>
                        </tr>
                        <tr>
                           <td>
                            <?php
                            if(strtotime($cancel_date) > 0){
                                $saloncancel_date = date("Y-m-d H:i:s",strtotime($orderCreate.'-'.$calcelPolicy.' Hours '));
                                if(Session::get("booking_type") == 1){
                                ?>

                                  <a href="javascript:void(0);" id="show_more_checkout">{{ Helper::get_language($id=89) }}</a>
                                  <ul style="display: none;" id="checkout_condition">
                                     <li>
                                        Full refund if cancelled before {{ date('d F Y,H:i A',strtotime($cancel_date)) }}.
                                     </li>
                                    <?php
                                    if(strtotime($saloncancel_date) > 0){
                                        ?>
                                        <li>
                                            Full refund in wallet if cancelled before {{ date('d F Y,H:i A',strtotime($saloncancel_date)) }}.
                                         </li>
                                         <li>
                                            If cancelled after {{ date('d F Y,H:i A',strtotime($saloncancel_date)) }} or in case of no-show, order will not be refunded.
                                         </li>
                                        <?php
                                    }else{
                                        ?>
                                        <li>
                                            If cancelled after {{ date('d F Y,H:i A',strtotime($cancel_date)) }} or in case of no-show, order will not be refunded.
                                         </li>
                                        <?php
                                    }
                                    ?>

                                   </ul>
                                <?php
                                }
                            }
                            ?>
                           </td>
                        </tr>
                     </table>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
