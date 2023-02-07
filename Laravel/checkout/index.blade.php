<!-- begin:header -->
<div id="header" class="heading">
   <div class="container">
      <div class="row">
         <ol class="breadcrumb">
            <li><a href="{{ url(); }}">Home</a></li>
            <li><a href="{{url('/venue/desc/'.$business_id)}}">Salon</a></li>
            <li class="active">Order</li>
         </ol>
      </div>
   </div>
</div>
<!-- end:header -->
<!-- end:content -->
<!-- begin:news -->
<?php
$action = "";
   $type_id = Session::get("booking_type");
   if($type_id == 1){
       $action =  url().'/checkout/order';
	   $columnbootClass1 = 'col-md-5';
	   $columnbootClass2 = 'col-md-4';
   }elseif($type_id == 2){
       $action =  url().'/checkout/evoucher_order';
	    $columnbootClass1 = 'col-md-6';
	   $columnbootClass2 = 'col-md-6';
   }
   ?>
<form method="post" name="" action="{{ $action }}">

    <div class="row">
     <?php
     $sessionBid = Session::get('bid');
     $current_user = Auth::id();
     ?>
        <div class="col-md-12">
			<div class="heading-title">
				<h2>{{ Helper::get_language($id=18) }}</h2>
				<br />
				<h5> <em>{{ Helper::get_language($id=126) }}.</em></h5>
			</div>
        </div>
        <div class="col-lg-12">
            <div class="panel info-graphics">
                <div class="row">
                    <div class="container">
                        <div class="{{$columnbootClass1}}">
                            <div class="row">
                                <h4>1. {{ Helper::get_language($id=122) }}</h4>

								<div class="col-md-12">
								 <div class="well">
								   <h3>
									 <?php
									 if(isset($total_bookings) && count($total_bookings)){
										 echo $total_bookings[0]->business->name;
									 }
									 ?>
								   </h3>
									<table id="cart" class="table table-hover table-condensed">
									  <tbody>
										 <?php
											$total_price = 0;
											$total_Original_price = 0;
											foreach($total_bookings as $total_bookings_key => $total_bookings_val){
												$bid = $total_bookings_val->business_id;
												$sid = $total_bookings_val->service_id;
												$total_Original_price = $total_bookings_val->service->price + $total_Original_price;
												$total_price = $total_bookings_val->price + $total_price;
												?>
										 <tr>
											<?php
											if($type_id == "1"){
												?>
												<td>
												<input type="radio" name="checkbox_cal" data-ival="<?php echo $total_bookings_val->duration; ?>" data-p="<?php echo $total_bookings_val->price; ?>" data-id="<?php echo base64_encode($sid); ?>" value="" class="form-control checkbox_cal" checked/>
											</td>
												<?php
											}
											?>
											<td data-th="Product">
											   <div class="row">
												  <div class="col-sm-12">
													 <p class="nomargin">{{ $total_bookings_val->service->title }}</p>
												  </div>
											   </div>
											</td>
											<td data-th="Price">
											   <p>&pound; {{ $total_bookings_val->price; }} </p>
											</td>
											<td>
											   <p>{{ (strtotime($total_bookings_val->booking_date) > 0) ? date("H:i A d M",strtotime($total_bookings_val->booking_date)) : "" }}</p>
											</td>
											<td class="actions" data-th="">
											   <a href="javascript:void(0);" data-link="{{ url().'/checkout/delete/'.$bid.'/'.$sid }}" class="remove-service"><span class="glyphicon glyphicon-remove-circle btn-sm" style=" font-size:24px;"></span></a>
											</td>
										 </tr>
										 <?php
											}
											($total_Original_price >  $total_price) ? $setTimePriceHtml = "<b class='book-now-tabs-linetxt'>&pound;".$total_Original_price."</b> &pound;".$total_price : $setTimePriceHtml = ' &pound;'.$total_price;
											?>
									  </tbody>
								   </table>
								   <hr />
								   <div class="row">
									  <div class="col-lg-12">
										 <a href="{{ url().'/venue/desc/'.Helper::getSlugFromId($bid) }}">
											<h3> <span class="glyphicon glyphicon-plus-sign btn-sm"></span> {{ Helper::get_language($id=66) }}</h3>
										 </a>
									  </div>
								   </div>
								   <hr />
								   <div class="row">
									  <div class="col-sm-12">
										 <a href="#" class="pull-right">
											<h4 class="nomargin order-total-color order_total">{{ Helper::get_language($id=67) }} = &pound;{{ $total_price; }} </h4>
											<h4 class="pull-right order-total-color order_total"></h4>
										 </a>
									  </div>

								   </div>
								   </div>
								</div>
                            </div>
                        </div>
                        <div class="{{$columnbootClass2}}">
                            <div class="row">
			                    <h4>2. {{ Helper::get_language($id=123) }}</h4>
								<div class="col-md-12">
									<div class="panel panel-default">
										<div class="panel-body">
										    <div class="dropdown">
											  {{ Form::token(); }}
											  <?php
											  if($type_id == 1){
											  ?>
											  <div class="btn btn-warning dropdown-toggle form-control" type="button" data-toggle="dropdown">
												 <select class="form-control form-employee" id="employee_id" name="employee_id">
													<?php
													   foreach($employee_listing as $employee_key => $employee_val){
														?>
													<option value="{{ $employee_val->id }}">{{ $employee_val->fname }}</option>
													<?php
													   }
													   ?>
												 </select>
											  </div>
											  <hr />
											  <input type="hidden" name="_token" value="'.Session::get('_token').'"/>

											  <input type="text" id="datetimepicker3"  name="order_date" value="" value="<?php echo date('Y-m-d'); ?>" />
											  <input type="hidden" name="bid" value="{{ $sessionBid; }}"/>
										    </div>
										</div>
									</div>
								</div>
                            </div>
                        </div>

                        <div class="col-md-3 column">
							<div class="row">

					            <h4>3. {{ Helper::get_language($id=124) }}</h4>

								<div class="col-md-12">
								    <div class="list-group">
									  <p id="feedback">
										<!--span>{{ Helper::get_language($id=65) }}:</span> <span id="select-result">{{ Helper::get_language($id=125) }}</span-->.
									  </p>
										<div class="slot-list">
											<ol id="selectable">
												<strong>Please choose date first</strong>
											</ol>
										</div>
										<style>
										  #feedback { font-size: 1.1em; }
										  #selectable .ui-selecting { background: #D86452; }
										  #selectable .ui-selected { background: #E9573F; color: white; }
										  #selectable { list-style-type: none; margin: 0; padding: 0; width: 100%; height: 308px;}
										  #selectable li { margin: 3px; padding: 0.4em; font-size: 1.1em; height: 39px; }
										  .slot-list{margin: 0; padding: 0; width: 100%; height: 308px; overflow: scroll;}
										</style>
										<script>

										</script>
								    </div>
								</div>
							</div>
                        </div>

                       <script>
                        $(window).load(function(){
                            sid = $("input[name=checkbox_cal]:checked").data("id");
                            bid = $("input[name=bid]").val();
                            token = $("input[name=_token]").val();
                            datePicker = $("#datetimepicker3").val();
                            s_price = $("input[name=checkbox_cal]:checked").data("p");
                            //total_price = $("#pHtml").html();
                            interval = $("input[name=checkbox_cal]:checked").data("ival");
                            $(".loading").fadeIn();

                            //update employee listing//
                                $.ajax({
                                    url: __base_url + '/ajax/employeeListingOnCheckout',
                                    data: {"_token": token, "bid": bid, "sid": sid},
                                    type: 'post',
                                    async : false,
                                    success: function (resp) {
                                        $('#employee_id').empty().html(resp);
                                    }
                                })
                            //show calendar

                             employee_id = $("#employee_id").val();

                            $.ajax({
                                url: __base_url + '/ajax/showCalendar',
                                data: {"_token": token, "employee_id": employee_id, "date": datePicker, "ival": interval, "bid": bid, "sid": sid, "s_price": s_price},
                                type: 'post',
                                async : false,
                                success: function (resp) {
                                    $(".loading").fadeOut();
                                    var json = $.parseJSON(resp);
                                    if (json.success == true) {
                                        // var timeInterVals = setIntervals(json.open_hour,json.to_hour,"15");
                                        $('#selectable').empty().html(json.timing);
                                        //$(".price").html(total_price);
                                    } else {
                                        $('#selectable').empty().html("Sorry we are closed this day, please choose another day.");
                                    }
                                }
                            })
                        })
                        $(function(){
                            $("#datetimepicker3").datepicker({changeYear: false}).datepicker("setDate", new Date());
                            datePicker = $("#datetimepicker3").val();

                        })
                      </script>
                     <?php
                        }elseif($type_id == 2){
                        ?>
                     <input type="hidden" name="btype" value="2"/>
                     <div class="evoucher-graphic">
                        <img src="{{ URL::to('/assets/images/basket.png') }}" alt="">
                     </div>
                     You'll need to contact the venue directly to book your appointment once you've received your order confirmation.
                     <?php
					 echo '</div></div></div></div></div></div>';
                        }
                        ?>
                        <div class="col-lg-12 check-btn">
						 <p>
							<?php
							if($type_id == 1){
								?>
								<input id="checkout" type="submit" name="submit" value="{{ Helper::get_language($id=68) }}" class="btn btn-warning btn-lg pull-right"  <?php echo (count($total_booking_withBookingingDate) > 0) ? "enabled" : "disabled"; ?>/>
								<?php
							}else{
								?>
								<input id="checkout" type="submit" name="submit" value="{{ Helper::get_language($id=68) }}" class="btn btn-warning btn-lg pull-right"/>
								<?php
							}
							?>

						 </p>
						</div>
                        <input type="hidden" name="bookedDate" value="" id="bookedDate"/>

                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
