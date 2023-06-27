<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BookController;
use App\Models\Book;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Discount;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class FrontController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categoryList = (new BookController)->categoryList();
        $bookList = (new BookController)->bookList($request);
        $books_map = [];
        $category_map = [];
        foreach ($bookList as $book) {
            $books_map[$book->category->id][] = $book;
            $category_map[$book->category->id] = $book->category;
        }

        return view('front.content')->with([
            'categoryList' => $categoryList,
            'bookList' => $books_map,
            'url_base' => config('app.url'),
            'category_map' => $category_map
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function bookDetail(Request $request)
    {
        $categoryList = (new BookController)->categoryList();
        $book = Book::find($request->get('pid'));
        return view('front.detail')->with([
            'categoryList' => $categoryList,
            'book' => $book,
            'url_base' => config('app.url'),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function addToCart(Request $request)
    {
        try {
            $up_time = date('Y-m-d H:i:s');
            $cart_item_id = 0;
            $date_now = date('Y-m-d');

            $cart = Cart::where([
                'session_id' => $request->get('is_cart_id')
            ])->first();
            
            if(empty($cart)){
                $newcart = new Cart;
                $newcart->session_id = $request->get('is_cart_id');
                $newcart->created_at = $up_time;
                $newcart->save();
                $newcart_id = $newcart->id;

                $cart_item = new CartItem;   
                $cart_item->cart_id = $newcart_id;
                $cart_item->book_id = $request->get('pid');
                $cart_item->qty = 1;
                $cart_item->created_at = $up_time;
                $cart_item->save();
                $cart_item_id = $cart_item->id;

            }else{
                
                $cart->created_at = $up_time;
                $cart->save();

                $cart_item = CartItem::where([
                    'cart_id' => $cart->id,
                    'book_id' => $request->get('pid'),
                ])->first();

                if(empty($cart_item)){
                    $cart_itemnw = new CartItem;
                    $cart_itemnw->qty = 1;
                    $cart_itemnw->book_id = $request->get('pid');
                    $cart_itemnw->cart_id = $cart->id;
                    $cart_itemnw->updated_at = $up_time;
                    $cart_itemnw->created_at = $up_time;
                    $cart_itemnw->save();
                    $cart_item_id = $cart_itemnw->id; 
                }else{
                    $cart_item->qty = ($cart_item->qty + 1);
                    $cart_item->updated_at = $up_time;
                    $cart_item->save();
                    $cart_item_id = $cart_item->id; 
                }
                  
            }

            $book = Book::find($request->get('pid'));
            $category_id = $book->category->id;
           
            $cart_item_discount = Cart::where([
                'session_id' => $request->get('is_cart_id'),
            ])->with('items', function($query) use($request , $category_id){
                $query->where('book_id', $request->get('pid'))
                ->with('book', function($q) use($request ,$category_id){
                    $q->where('category_id', $category_id);
                });
            })->first();

            $all_aty = 0;
            if(!empty($cart_item_discount)){
                $all_aty = $cart_item_discount->items->sum('qty');
            }
            
            $discounts_categorywise = Discount::whereRaw('"'.$date_now.'" BETWEEN start_date AND end_date')
            ->select([
                'id'
            ])->where('buy_qty' , '<=',$all_aty)
            ->where('map_category_id' , $category_id)
            ->orderby('sequence' , 'desc')
            ->first();

            if(!empty($discounts_categorywise)){
                $cart_item_dis = CartItem::find($cart_item_id);
                $cart_item_dis->discount_id = $discounts_categorywise->id;
                $cart_item_dis->save();
            }

            $response_ar['status'] = true;
            $response_ar['title'] = "Item Added";
            return response()->json($response_ar);

        } catch (\Throwable $th) {
            $response_ar['status'] = false;
            $response_ar['title'] = $th->getMessage();
            return response()->json($response_ar);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function getCartQty(Request $request)
    {
        
        $cart_item = Cart::where([
            'session_id' => $request->get('is_cart_id'),
        ])->first();

        $all_aty = 0;
        if(!empty($cart_item)){
            $all_aty = $cart_item->items->sum('qty');
        }
        echo '<i class="fas fa-shopping-cart addedToCart"><div id="badge"> '.$all_aty.' </div>';
    }

    /**
     * Display a listing of the resource.
     */
    public function getCart(Request $request)
    {
        $categoryList = (new BookController)->categoryList();
        return view('front.cart')->with([
            'url_base' => config('app.url'),
            'categoryList' => $categoryList
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function getCartAjax(Request $request)
    {
        $date_now = date('Y-m-d');
        $cart_detail = Cart::where([
            'session_id' => $request->get('is_cart_id'),
        ])->with(['items'])->first();

        $copcodet_detail = FALSE;
        if(!empty($request->get('copcode'))){
            $copcodet_detail = Coupon::where([
                'cupon_code' => $request->get('copcode'),
            ])->whereRaw('"'.$date_now.'" BETWEEN start_date AND end_date')
            ->first();

            $cart_detail->copun_id = isset($copcodet_detail->id) ? $copcodet_detail->id: null;
            $cart_detail->save();
        }else{
            
            if(isset($cart_detail->copun)){
                $copcodet_detail = $cart_detail->copun;
                $request->merge(["copcode" => $cart_detail->copun->cupon_code]); 
            }
        }
        if($request->get('copcode') == -1){
            $cart_detail->copun_id = null;
            $cart_detail->save();
            $request->merge(["copcode" => null]);
        }
        return view('front.cartdetail')->with([
            'url_base' => config('app.url'),
            'cart_detail' => $cart_detail,
            'copcode' => $request->get('copcode'),
            'copcodet_detail' => $copcodet_detail
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'fname' => ['required' ,'min:2' ],
            'lname' => ['required','min:2'],
            'cemail' => ['required' ,'email'],
            'cmob' => ['required' ,'numeric'],
            'caddress' => ['required' ,'min:10'],
            'is_cart_idh' => 'required'
        ],[],$this->attributes());

        $response_ar = [];

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response_ar['status'] = false;
            $response_ar['title'] = $errors->first();
            return response()->json($response_ar);    
        }
       
        DB::beginTransaction();
        $date_now = date('Y-m-d H:i:s');

        try {
            $cart_detail = Cart::where([
                'session_id' => $request->get('is_cart_idh'),
            ])->with(['items'])->first();

            $copcodet_detail = false;
            if(!empty($order->copun_id)){
                $copcodet_detail = Coupon::find($order->copun_id)
                ->first();
            }

            $amountBookRaw = 0;
            $netTotalBookAll = 0;
            dd($cart_detail->items);
            if ($copcodet_detail){
                if($copcodet_detail->cupon_type == "percentage"){
                    $amountBookRaw = ($netTotalBookAll * 
                    (100 - $copcodet_detail->cupon_value) / 100);
                }else{
                    $amountBookRaw = ($amountBookRaw - $copcodet_detail->cupon_value);
                }
            }

            $customer = new Customer;
            $customer->first_name = $request->get('fname');
            $customer->last_name = $request->get('lname');
            $customer->email = $request->get('cemail');
            $customer->contact_no = $request->get('cmob');
            $customer->billing_address = $request->get('caddress');
            $customer->created_at = $date_now;
            $customer->save();
            $customerid = $customer->id;

            $order = new Order;
            $order->customer_id = $customerid;
            $order->cart_id = $cart_detail->id;
            $order->session_id = $cart_detail->session_id;
            $order->copun_id = $cart_detail->copun_id;
            $order->copun_val = $amountBookRaw;
            $order->created_at = $date_now;
            $order->save();
            $orderid = $order->id;



            $ar_items = [];
            foreach ($cart_detail->items as $keym => $item) {
                $discount_item = 0;
                if(isset($item->discount)){
                    if($item->discount->discount_type == "percentage"){
                        $discount_item = (($item->book->price * $item->qty ) * 
                        (100 - $item->discount->discount_value) / 100);
                    }else{
                        $discount_item = (($item->book->price * $item->qty ) - $item->discount->discount_value);
                    }
                    
                }
                $ar_items[] = [
                    "order_id" => $orderid,
                    "book_id" => $item->book_id,
                    "discount_id" => $item->discount_id,
                    "qty" => $item->qty,
                    "price" => $item->book->price,
                    "discountval" => $discount_item,
                    "created_at" => $date_now,
                ];
            }
            OrderItem::insert($ar_items);   
            DB::commit();
            $response_ar['status'] = true;
            $response_ar['title'] = "Order Placed Successfully";
            return response()->json($response_ar);

        } catch (\Throwable $th) {
            DB::rollback();
            $response_ar['status'] = false;
            $response_ar['title'] = $th->getMessage();
            return response()->json($response_ar);
        }
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'fname' => 'First Name',
            'lname' => 'Last Name',
            'cemail' => 'Email',
            'cmob' => 'Phone',
            'caddress' => 'Address'
        ];
    }

}
