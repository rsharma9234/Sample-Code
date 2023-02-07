<?php
class ProductOrders extends Eloquent {
    protected $fillable = ['user_id','business_id','product_id','status','order_id', 'price','qty'];

    protected $table = "user_product_order";

    public static function rules(){

        return array(
            'product_id'    => 'required',
            'qty'   => 'required',
        );
    }

    public function business(){
        return $this->belongsTo('Business','business_id','id');
    }

    public function customer(){
        return $this->belongsTo('User','user_id','id');
    }

    public function product(){
        return $this->belongsTo('Products','product_id');
    }

    public function order(){
        return $this->belongsTo('Order','order_id');
    }



	public function scopeBydate($query, $date=false, $date2=false,$column='created_at' )
    {
        if($date && $date2){
            $date1 = date('Y-m-d H:i:s',strtotime("midnight", strtotime($date)));
            $date2 = date('Y-m-d H:i:s',strtotime("tomorrow", strtotime($date2)) - 1);

            return $query->whereBetween($column, array($date1, $date2));
        }
        elseif($date){
            $date = strtotime($date);
            $date1 = date('Y-m-d H:i:s',strtotime("midnight", $date));
            $date2 = date('Y-m-d H:i:s',strtotime("tomorrow", $date) - 1);

            return $query->whereBetween($column, array($date1, $date2));
        }
        else
        {
            $date =  time();
            $date1 = date('Y-m-d H:i:s',strtotime("midnight", $date));
            $date2 = date('Y-m-d H:i:s',strtotime("tomorrow", $date) - 1);

            return $query->whereBetween($column, array($date1, $date2));
        }

    }
    
}
