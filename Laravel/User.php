<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	protected $primaryKey = 'id';
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');
    protected $fillable = array('facebookSocialId','fname','lname','email','password','role','note','user_type','access_level','newsletter','wallet_balance','activate_token','status');
    //validation rules//

    public static $rules = array(
        'fname'             => 'required',                        // just a normal required validation
        'email'            => 'required|email|unique:users',     // required and must be unique in the ducks table
        'password'         => 'required|min:6',
    );

    public function profile()
    {
        return $this->hasOne('Profile');
    }

    public function role()
    {
        return $this->belongsTo('Roles','role','rolRoleNo');
    }

    public function booking()
    {
        return $this->hasMany('UserBooking');
    }

    public function cartBooking()
    {
        return $this->hasMany('AddCart');
    }
    public function wallet()
    {
        return $this->hasMany('Wallet','mwlWalletUserNo','id');
    }
    public function customer_payment()
    {
        return UserBooking::where('user_id',$this->id)->where('status','4')->sum('booking_price');
    }

    public function full_name(){
	   if($this->fname!='' & $this->lname!=''):
        return ucfirst($this->fname.' '.$this->lname);
	   else:
	   return '';
	   endif;
    }

    public function is_client(){
        return ($this->role == CLIENT );
    }

    public function is_admin(){
        return ($this->role == ADMIN );
    }

    public function business(){

        if($this->is_client()){
            return $this->hasOne('Business');
        }
        else
        {
            return false;
        }

    }


    public static function get_booking_by_id($user_id=false){
        if($user_id){
            $user = User::find($user_id);

            return $user->booking()->get();
        }
        else
            return false;

    }
    public function review(){
        return $this->hasMany('Review','mraUserId','id');
    }
	public function comment(){
        return $this->hasMany('Comment','mcoUserId','id');
    }


    public function Order(){
        return $this->hasMany("Order");
    }
	  /*
     * query scope to search created users within a day or rage of dates.
     * */
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
