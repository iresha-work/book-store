<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.orders.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $ar_search_cols = ["id","created_at"];
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page

        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value

        // Total records
        $totalRecords = Order::select('count(id) as allcount')->count();
        $totalRecordswithFilter = Order::select('count(id) as allcount')->where(function($query) use ($ar_search_cols , $searchValue ) {
            if($searchValue != ''){
                foreach ($ar_search_cols as $col) {
                    $query->orWhere($col,'like', '%' .$searchValue . '%');
                }
            }
        })->count();

        // Fetch records
        $orders = Order::where(function($query) use ($ar_search_cols , $searchValue) {
            if($searchValue != ''){
                foreach ($ar_search_cols as $col) {
                    $query->orWhere($col,'like', '%' .$searchValue . '%');
                }
            }
        })->select('*')
        ->skip($start)
        ->take($rowperpage)
        ->orderby($ar_search_cols[$columnIndex] , $columnSortOrder)
        ->get();

        $data_arr = array();
        foreach($orders as $order){
            $amountBook = 0;
            $amountBookRaw = 0;
            if(isset($order->items)){
                foreach ($order->items as $item) {
                    $amountBook = $item->price;
                    if(isset($item->discount)){
                        if($item->discount->discount_type == "percentage"){
                            $amountBook = (($item->price * $item->qty ) * 
                            (100 - $item->discount->discount_value) / 100);
                        }else{
                            $amountBook = (($item->price * $item->qty ) - $item->discount->discount_value);
                        }
                    }

                    $amountBookRaw += $amountBook;
                }
                $copcodet_detail = false;
                if(!empty($order->copun_id)){
                    $copcodet_detail = Coupon::withTrashed()
                    ->where(['id' => $order->copun_id])
                    ->first();
                }

                if ($copcodet_detail){
                    if($copcodet_detail->cupon_type == "percentage"){
                        $amountBookRaw = ($netTotalBookAll * 
                        (100 - $copcodet_detail->cupon_value) / 100);
                    }else{
                        $amountBookRaw = ($amountBookRaw - $copcodet_detail->cupon_value);
                    }
                }
            }
            
           $data_arr[] = array(
                "pid" => $order->id,
                "cname" => $order->customer->first_name.' '.$order->customer->last_name,
                "cemail" => $order->customer->email,
                "cmob" => $order->customer->contact_no,
                "bcount" => $order->items->count('qty'),
                "amounod" => number_format($amountBookRaw,2,".",","),
                "id" => '<a target="_blank" class="order-view text-center text-success" href="'.url('/admin/orders/detail/'.$order->id).'"><ion-icon name="eye-outline"></ion-icon></a>',
                "created_at" => date('Y-m-d H:i:s', strtotime($order->created_at))
           );
        }

        $response = array(
           "draw" => intval($draw),
           "iTotalRecords" => $totalRecords,
           "iTotalDisplayRecords" => $totalRecordswithFilter,
           "aaData" => $data_arr
        );

        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     */
    public function orderDetail(Request $request , $id)
    {
        $order = Order::find($id);
        if(empty($order)){
            abort(404);
        }

        $copcodet_detail = FALSE;
        if(!empty($order->copun_id)){
            $copcodet_detail = Coupon::withTrashed()->where(['id' => $order->copun_id])
        ->first();
        }
        
        return view('admin.orders.detail')->with([
            'url_base' => config('app.url'),
            'order_detail' => $order,
            'copcodet_detail' => $copcodet_detail
        ]);
    }
}
