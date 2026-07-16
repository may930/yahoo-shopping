<?php
/* srchBeans.php(検索データ) 
@author 自分の名前 
@version 2.0 
@date 作成日

*/
class Beans
{
    /* 変数 */
    private $user_id;
    private $name;
    private $phone_number;
    private $mail_address;
    private $password;
    private $sex;
    private $birthday;
    private $zipcode;
    private $address;
    private $created_at;
    private $deleted_at;
    private $producer_id;
    private $company_name;
    private $representative_name;
    private $store_name;
    private $store_name_kana;
    private $introduction;
    private $related_store;
    private $notes;
    private $store_open_day;
    private $category_id;
    private $category_name;
    private $parent_category_id;
    private $product_id;
    private $product_name;
    private $information;
    private $variation_id;
    private $variation_name;
    private $option_id;
    private $option_name;
    private $price;
    private $stock;
    private $image_id;
    private $image_url;
    private $display_order;
    private $coupon_id;
    private $coupon_name;
    private $discount_rate;
    private $is_discount;
    private $min_order_amount;
    private $maximum_discount;
    private $start_date;
    private $end_date;
    private $is_infinite;
    private $max_use;
    private $is_used;
    private $used_at;
    private $get_at;
    private $address_id;
    private $shipping_name;
    private $cart_id;
    private $quantity;
    private $order_id;
    private $order_date;
    private $coupon_discount_amount;
    private $total_price;
    private $pay;
    private $shipping_status;
    private $order_detail_id;
    private $review_id;
    private $rating;
    private $title;
    private $comment;
    private $views_count;
    private $like_count;
    private $product_favorites_id;
    private $add_at;
    private $producer_favorites_id;
    private $inquiry_category_id;
    private $inquiry_category_name;
    private $inquiry_id;
    private $inquiry_contents;
    private $user_mailaddress;
    private $inquiry_at;
    private $privacy;
    private $inquiry_history_id;
    private $contents;
    private $sender;
    private $is_read;
    private $send_time;
    private $view_time;

    /* コンストラクタ */
    public function __construct()
    { //「_(アンダースコア)」2個
        $user_id = 0;
        $name = '';
        $phone_number = '';
        $mail_address = '';
        $password = '';
        $sex = '';
        $birthday = null;
        $zipcode = '';
        $address = '';
        $created_at = null;
        $deleted_at = null;
        $producer_id = 0;
        $company_name = null;
        $representative_name = '';
        $store_name = '';
        $store_name_kana = '';
        $introduction = '';
        $related_store = '';
        $notes = '';
        $store_open_day = null;
        $category_id = 0;
        $category_name = '';
        $parent_category_id = null;
        $product_id = 0;
        $product_name = '';
        $information = '';
        $variation_id = null;
        $variation_name = '';
        $option_id = 0;
        $option_name = '';
        $price = 0;
        $stock = 0;
        $image_id = 0;
        $image_url = '';
        $display_order = 0;
        $coupon_id = 0;
        $coupon_name = '';
        $discount_rate = 0;
        $is_discount = 0;
        $min_order_amount = 0;
        $maximum_discount = 0;
        $start_date = null;
        $end_date = null;
        $is_infinite = 0;
        $max_use = 0;
        $created_at = null;
        $used_at = null;
        $get_at = null;
        $address_id = 0;
        $shipping_name = '';
        $cart_id = 0;
        $quantity = 0;
        $order_id = 0;
        $order_date = null;
        $coupon_discount_amount = 0;
        $total_price = 0;
        $pay = '';
        $shipping_status = '';
        $order_detail_id = 0;
        $review_id = 0;
        $rating = 0;
        $title = '';
        $comment = '';
        $views_count = 0;
        $like_count = 0;
        $product_favorites_id = 0;
        $add_at = null;
        $producer_favorites_id = 0;
        $inquiry_category_id = 0;
        $inquiry_category_name = '';
        $inquiry_id = 0;
        $inquiry_contents = '';
        $user_mailaddress = '';
        $inquiry_at = null;
        $privacy = 0;
        $inquiry_history_id = 0;
        $contents = '';
        $sender = 0;
        $is_read = 0;
        $send_time = null;
        $view_time = null;    
    }

    /* クリアメソッド */
    public function BeansClear()
    {
        $user_id = 0;
        $name = '';
        $phone_number = '';
        $mail_address = '';
        $password = '';
        $sex = '';
        $birthday = null;
        $zipcode = '';
        $address = '';
        $created_at = null;
        $deleted_at = null;
        $producer_id = 0;
        $company_name = null;
        $representative_name = '';
        $store_name = '';
        $store_name_kana = '';
        $introduction = '';
        $related_store = '';
        $notes = '';
        $store_open_day = null;
        $category_id = 0;
        $category_name = '';
        $parent_category_id = null;
        $product_id = 0;
        $product_name = '';
        $information = '';
        $variation_id = null;
        $variation_name = '';
        $option_id = 0;
        $option_name = '';
        $price = 0;
        $stock = 0;
        $image_id = 0;
        $image_url = '';
        $display_order = 0;
        $coupon_id = 0;
        $coupon_name = '';
        $discount_rate = 0;
        $is_discount = 0;
        $min_order_amount = 0;
        $maximum_discount = 0;
        $start_date = null;
        $end_date = null;
        $is_infinite = 0;
        $max_use = 0;
        $created_at = null;
        $used_at = null;
        $get_at = null;
        $address_id = 0;
        $shipping_name = '';
        $cart_id = 0;
        $quantity = 0;
        $order_id = 0;
        $order_date = null;
        $coupon_discount_amount = 0;
        $total_price = 0;
        $pay = '';
        $shipping_status = '';
        $order_detail_id = 0;
        $review_id = 0;
        $rating = 0;
        $title = '';
        $comment = '';
        $views_count = 0;
        $like_count = 0;
        $product_favorites_id = 0;
        $add_at = null;
        $producer_favorites_id = 0;
        $inquiry_category_id = 0;
        $inquiry_category_name = '';
        $inquiry_id = 0;
        $inquiry_contents = '';
        $user_mailaddress = '';
        $inquiry_at = null;
        $privacy = 0;
        $inquiry_history_id = 0;
        $contents = '';
        $sender = 0;
        $is_read = 0;
        $send_time = null;
        $view_time = null;    
    }

    public function getuser_id() 
    {
        return $this->user_id;
    }           
    public function setuser_id($user_id) 
    {
        $this->user_id = $user_id;
    }

    public function getname() 
    {
        return $this->name;
    }           
    public function setname($name) 
    {
        $this->name = $name;
    }

    public function getphone_number() 
    {
        return $this->phone_number;
    }           
    public function setphone_number($phone_number) 
    {
        $this->phone_number = $phone_number;
    }

    public function getmail_address() 
    {
        return $this->mail_address;
    }           
    public function mail_address($mail_address) 
    {
        $this->mail_address = $mail_address;
    }

    public function getpassword() 
    {
        return $this->password;
    }           
    public function setpassword($password) 
    {
        $this->password = $password;
    }

    public function getsex() 
    {
        return $this->sex;
    }           
    public function setsex($sex) 
    {
        $this->sex = $sex;
    }

    public function getbirthday() 
    {
        return $this->birthday;
    }           
    public function setbirthday($birthday) 
    {
        $this->birthday = $birthday;
    }

    public function getzipcode() 
    {
        return $this->zipcode;
    }           
    public function setzipcode($zipcode) 
    {
        $this->zipcode = $zipcode;
    }

    public function getaddress() 
    {
        return $this->address;
    }           
    public function setaddress($address) 
    {
        $this->address = $address;
    }

    public function getcreated_at() 
    {
        return $this->created_at;
    }           
    public function setcreated_at($created_at) 
    {
        $this->created_at = $created_at;
    }

    public function getdeleted_at() 
    {
        return $this->deleted_at;
    }           
    public function setdeleted_at($deleted_at) 
    {
        $this->deleted_at = $deleted_at;
    }

    public function getproducer_id() 
    {
        return $this->producer_id;
    }           
    public function setproducer_id($producer_id) 
    {
        $this->producer_id = $producer_id;
    }

    public function getcompany_name() 
    {
        return $this->company_name;
    }           
    public function setcompany_name($company_name) 
    {
        $this->company_name = $company_name;
    }

    public function getrepresentative_name() 
    {
        return $this->representative_name;
    }           
    public function setrepresentative_name($representative_name) 
    {
        $this->representative_name = $representative_name;
    }

    public function getstore_name() 
    {
        return $this->store_name;
    }           
    public function setstore_name($store_name) 
    {
        $this->store_name = $store_name;
    }

    public function getstore_name_kana() 
    {
        return $this->store_name_kana;
    }           
    public function setstore_name_kana($store_name_kana) 
    {
        $this->store_name_kana = $store_name_kana;
    }

    public function getintroduction() 
    {
        return $this->introduction;
    }           
    public function setintroduction($introduction) 
    {
        $this->introduction = $introduction;
    }

    public function getrelated_store() 
    {
        return $this->related_store;
    }           
    public function setrelated_store($related_store) 
    {
        $this->related_store = $related_store;
    }

    public function getnotes() 
    {
        return $this->notes;
    }           
    public function setnotes($notes) 
    {
        $this->notes = $notes;
    }

    public function getstore_open_day() 
    {
        return $this->store_open_day;
    }           
    public function setstore_open_day($store_open_day) 
    {
        $this->store_open_day = $store_open_day;
    }

    public function getcategory_id() 
    {
        return $this->category_id;
    }           
    public function setcategory_id($category_id) 
    {
        $this->category_id = $category_id;
    }

    public function getcategory_name() 
    {
        return $this->category_name;
    }           
    public function setcategory_name($category_name) 
    {
        $this->category_name = $category_name;
    }

    public function getparent_category_id() 
    {
        return $this->parent_category_id;
    }           
    public function setparent_category_id($parent_category_id) 
    {
        $this->parent_category_id = $parent_category_id;
    }

    public function getproduct_id() 
    {
        return $this->product_id;
    }           
    public function setproduct_id($product_id) 
    {
        $this->product_id = $product_id;
    }

    public function getproduct_name() 
    {
        return $this->product_name;
    }           
    public function setproduct_name($product_name) 
    {
        $this->product_name = $product_name;
    }

    public function getinformation() 
    {
        return $this->information;
    }           
    public function setinformation($information) 
    {
        $this->information = $information;
    }

    public function getvariation_id() 
    {
        return $this->variation_id;
    }           
    public function setvariation_id($variation_id) 
    {
        $this->variation_id = $variation_id;
    }

    public function getvariation_name() 
    {
        return $this->variation_name;
    }           
    public function setvariation_name($variation_name) 
    {
        $this->variation_name = $variation_name;
    }

    public function getoption_id() 
    {
        return $this->option_id;
    }           
    public function setoption_id($option_id) 
    {
        $this->option_id = $option_id;
    }

    public function getoption_name() 
    {
        return $this->option_name;
    }           
    public function setoption_name($option_name) 
    {
        $this->option_name = $option_name;
    }

    public function getprice() 
    {
        return $this->price;
    }           
    public function setprice($price) 
    {
        $this->price = $price;
    }

    public function getstock() 
    {
        return $this->stock;
    }           
    public function setstock($stock) 
    {
        $this->stock = $stock;
    }

    public function getimage_id() 
    {
        return $this->image_id;
    }           
    public function setimage_id($image_id) 
    {
        $this->image_id = $image_id;
    }

    public function getimage_url() 
    {
        return $this->image_url;
    }           
    public function setimage_url($image_url) 
    {
        $this->image_url = $image_url;
    }

    public function getdisplay_order() 
    {
        return $this->display_order;
    }           
    public function setdisplay_order($display_order) 
    {
        $this->display_order = $display_order;
    }

    public function getcoupon_id() 
    {
        return $this->coupon_id;
    }           
    public function setcoupon_id($coupon_id) 
    {
        $this->coupon_id = $coupon_id;
    }

    public function getcoupon_name() 
    {
        return $this->coupon_name;
    }           
    public function setcoupon_name($coupon_name) 
    {
        $this->coupon_name = $coupon_name;
    }

    public function getdiscount_rate() 
    {
        return $this->discount_rate;
    }           
    public function setdiscount_rate($discount_rate) 
    {
        $this->discount_rate = $discount_rate;
    }

    public function getis_discount() 
    {
        return $this->is_discount;
    }           
    public function setis_discount($is_discount) 
    {
        $this->is_discount = $is_discount;
    }

    public function getmin_order_amount() 
    {
        return $this->min_order_amount;
    }           
    public function setmin_order_amount($min_order_amount) 
    {
        $this->min_order_amount = $min_order_amount;
    }

    public function getmaximum_discount() 
    {
        return $this->maximum_discount;
    }           
    public function setmaximum_discount($maximum_discount) 
    {
        $this->maximum_discount = $maximum_discount;
    }

    public function getstart_date() 
    {
        return $this->start_date;
    }           
    public function setstart_date($start_date) 
    {
        $this->start_date = $start_date;
    }

    public function getend_date() 
    {
        return $this->end_date;
    }           
    public function setend_date($end_date) 
    {
        $this->end_date = $end_date;
    }

    public function getis_infinite() 
    {
        return $this->is_infinite;
    }           
    public function setis_infinite($is_infinite) 
    {
        $this->is_infinite = $is_infinite;
    }

    public function getmax_use() 
    {
        return $this->max_use;
    }           
    public function setmax_use($max_use) 
    {
        $this->max_use = $max_use;
    }

    public function getis_used() 
    {
        return $this->is_used;
    }           
    public function setis_used($is_used) 
    {
        $this->is_used = $is_used;
    }

    public function getused_at() 
    {
        return $this->used_at;
    }           
    public function setused_at($used_at) 
    {
        $this->used_at = $used_at;
    }

    public function getget_at() 
    {
        return $this->get_at;
    }           
    public function setget_at($get_at) 
    {
        $this->get_at = $get_at;
    }

    public function getaddress_id() 
    {
        return $this->address_id;
    }           
    public function setaddress_id($address_id) 
    {
        $this->address_id = $address_id;
    }

    public function getshipping_name() 
    {
        return $this->shipping_name;
    }           
    public function setshipping_name($shipping_name) 
    {
        $this->shipping_name = $shipping_name;
    }

    public function getcart_id() 
    {
        return $this->cart_id;
    }           
    public function setcart_id($cart_id) 
    {
        $this->cart_id = $cart_id;
    }

    public function getquantity() 
    {
        return $this->quantity;
    }           
    public function setquantity($quantity) 
    {
        $this->quantity = $quantity;
    }

    public function getorder_id() 
    {
        return $this->order_id;
    }           
    public function setorder_id($order_id) 
    {
        $this->order_id = $order_id;
    }

    public function getorder_date() 
    {
        return $this->order_date;
    }           
    public function setorder_date($order_date) 
    {
        $this->order_date = $order_date;
    }

    public function getcoupon_discount_amount() 
    {
        return $this->coupon_discount_amount;
    }           
    public function setcoupon_discount_amount($coupon_discount_amount) 
    {
        $this->coupon_discount_amount = $coupon_discount_amount;
    }

    public function gettotal_price() 
    {
        return $this->total_price;
    }           
    public function settotal_price($total_price) 
    {
        $this->total_price = $total_price;
    }

    public function getpay() 
    {
        return $this->pay;
    }           
    public function setpay($pay) 
    {
        $this->pay = $pay;
    }

    public function getshipping_status() 
    {
        return $this->shipping_status;
    }           
    public function setshipping_status($shipping_status) 
    {
        $this->shipping_status = $shipping_status;
    }

    public function getorder_detail_id() 
    {
        return $this->order_detail_id;
    }           
    public function setorder_detail_id($order_detail_id) 
    {
        $this->order_detail_id = $order_detail_id;
    }

    public function getreview_id() 
    {
        return $this->review_id;
    }           
    public function setreview_id($review_id) 
    {
        $this->review_id = $review_id;
    }

    public function getrating() 
    {
        return $this->rating;
    }           
    public function setrating($rating) 
    {
        $this->rating = $rating;
    }

    public function gettitle() 
    {
        return $this->title;
    }           
    public function settitle($title) 
    {
        $this->title = $title;
    }

    public function getcomment() 
    {
        return $this->comment;
    }           
    public function setcomment($comment) 
    {
        $this->comment = $comment;
    }

    public function getviews_count() 
    {
        return $this->views_count;
    }           
    public function setviews_count($views_count) 
    {
        $this->views_count = $views_count;
    }

    public function getlike_count() 
    {
        return $this->like_count;
    }           
    public function setlike_count($like_count) 
    {
        $this->like_count = $like_count;
    }

    public function getproduct_favorites_id() 
    {
        return $this->product_favorites_id;
    }           
    public function setproduct_favorites_id($product_favorites_id) 
    {
        $this->product_favorites_id = $product_favorites_id;
    }

    public function getadd_at() 
    {
        return $this->add_at;
    }           
    public function setadd_at($add_at) 
    {
        $this->add_at = $add_at;
    }

    public function getproducer_favorites_id() 
    {
        return $this->producer_favorites_id;
    }           
    public function setproducer_favorites_id($producer_favorites_id) 
    {
        $this->producer_favorites_id = $producer_favorites_id;
    }

    public function getinquiry_category_id() 
    {
        return $this->inquiry_category_id;
    }           
    public function setinquiry_category_id($inquiry_category_id) 
    {
        $this->inquiry_category_id = $inquiry_category_id;
    }

    public function getinquiry_category_name() 
    {
        return $this->inquiry_category_name;
    }           
    public function setinquiry_category_name($inquiry_category_name) 
    {
        $this->inquiry_category_name = $inquiry_category_name;
    }

    public function getinquiry_id() 
    {
        return $this->inquiry_id;
    }           
    public function setinquiry_id($inquiry_id) 
    {
        $this->inquiry_id = $inquiry_id;
    }

    public function getinquiry_contents() 
    {
        return $this->inquiry_contents;
    }           
    public function setinquiry_contents($inquiry_contents) 
    {
        $this->inquiry_contents = $inquiry_contents;
    }

    public function getuser_mailaddress() 
    {
        return $this->user_mailaddress;
    }           
    public function setuser_mailaddress($user_mailaddress) 
    {
        $this->user_mailaddress = $user_mailaddress;
    }

    public function getinquiry_at() 
    {
        return $this->inquiry_at;
    }           
    public function setinquiry_at($inquiry_at) 
    {
        $this->inquiry_at = $inquiry_at;
    }

    public function getprivacy() 
    {
        return $this->privacy;
    }           
    public function setprivacy($privacy) 
    {
        $this->privacy = $privacy;
    }

    public function getinquiry_history_id() 
    {
        return $this->inquiry_history_id;
    }           
    public function setinquiry_history_id($inquiry_history_id) 
    {
        $this->inquiry_history_id = $inquiry_history_id;
    }

    public function getcontents() 
    {
        return $this->contents;
    }           
    public function setcontents($contents) 
    {
        $this->contents = $contents;
    }

    public function getsender() 
    {
        return $this->sender;
    }           
    public function setsender($sender) 
    {
        $this->sender = $sender;
    }

    public function getis_read() 
    {
        return $this->is_read;
    }           
    public function setis_read($is_read) 
    {
        $this->is_read = $is_read;
    }

    public function getsend_time() 
    {
        return $this->send_time;
    }           
    public function setsend_time($send_time) 
    {
        $this->send_time = $send_time;
    }

    public function getview_time() 
    {
        return $this->view_time;
    }           
    public function setview_time($view_time) 
    {
        $this->view_time = $view_time;
    }

}

?>