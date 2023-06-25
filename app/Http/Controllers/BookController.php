<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\BookCategory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     *
     * @var string
     */
    protected $sucsess = "";

    /**
     *
     * @var string
     */
    protected $error = 500;

    /**
     *
     * @var string
     */
    protected $bookAddMsg = "Book saved.";

    /**
     *
     * @var string
     */
    protected $bookEditMsg = "Book updated.";

    /**
     *
     * @var string
     */
    protected $bookDelMsg = "Book deleted.";

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categoryList = $this->categoryList();
        return view('admin.book.index')->with([
            'categoryList' => $categoryList
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $ar_search_cols = ["id", "name" ,"category_id" , "price" , "sequence" ,"created_at"];
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
        $totalRecords = Book::select('count(id) as allcount')->count();
        $totalRecordswithFilter = Book::select('count(id) as allcount')->where(function($query) use ($ar_search_cols , $searchValue ) {
            if($searchValue != ''){
                foreach ($ar_search_cols as $col) {
                    $query->orWhere($col,'like', '%' .$searchValue . '%');
                }
            }
        })->count();

        // Fetch records
        $books = Book::where(function($query) use ($ar_search_cols , $searchValue) {
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
        foreach($books as $book){
           $data_arr[] = array(
                "pid" => $book->id,
                "id" => '<a data-pid="'.$book->id.'" class="book-edit text-center text-success" href="javascript:void(0)"><ion-icon name="eye-outline"></ion-icon></a>',
                "category" => $book->category->name,
                "name" => $book->name,
                "price" => $book->price,
                "sequence" => $book->sequence,
                "created_at" => date('Y-m-d H:i:s', strtotime($book->created_at))
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'book_name' => ['required' ,'min:2' , 'unique:App\Models\Book,name'],
            'book_cat' => ['required'],
            'book_price' => ['required' ,'numeric'],
            'book_seq' => ['required' ,'numeric']
        ],[],$this->attributes());

        $response_ar = [];

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response_ar['status'] = false;
            $response_ar['title'] = $errors->first();
            return response()->json($response_ar);    
        }
                
        try {
            $book = new Book;
            $book->name = $request->get('book_name');
            $book->description = $request->get('book_descriptionh');
            $book->category_id = $request->get('book_cat');
            $book->price = $request->get('book_price');
            $book->sequence = $request->get('book_seq');
            $book->created_at = date('Y-m-d H:i:s');
            $book->save();

            if ($request->hasFile('book_images')) {
                $ar_paths = [];
                foreach ($request->file('book_images') as $key => $book_image) {
                    $path = $request->file('book_images')[$key]->store(
                        'books', 'public'
                    );
                    $ar_paths[] = $path;
                }
                $book->images = json_encode($ar_paths);
                $book->save();
            }
            $response_ar['status'] = true;
            $response_ar['title'] = $this->bookAddMsg;
            return response()->json($response_ar);

        } catch (\Throwable $th) {

            $response_ar['status'] = false;
            $response_ar['title'] = $th->getMessage();
            return response()->json($response_ar);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $categoryList = $this->categoryList();
        $book = Book::find($request->get('pid'));
        if(empty($book)){
            abrot(404);
        }
        return view('admin.book.edit')->with(
            [
                'book' => $book,
                'url_base' => config('app.url'),
                'categoryList' => $categoryList
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'book_name' => ['required' ,'min:2' ,  Rule::unique('App\Models\Book' , 'name')->ignore($request->get('pid'))],
            'book_cat' => ['required'],
            'book_price' => ['required' ,'numeric'],
            'book_seq' => ['required' ,'numeric']
        ],[],$this->attributes());

        $response_ar = [];

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response_ar['status'] = false;
            $response_ar['title'] = $errors->first();
            return response()->json($response_ar);    
        }
                
        try {
            $book = Book::find($request->get('pid'));
            $book->name = $request->get('book_name');
            $book->description = $request->get('ebook_descriptionh');
            $book->category_id = $request->get('book_cat');
            $book->price = $request->get('book_price');
            $book->sequence = $request->get('book_seq');
            $book->updated_at = date('Y-m-d H:i:s');
            $book->save();

            if ($request->hasFile('book_images')) {
                $ar_paths = [];
                foreach ($request->file('book_images') as $key => $book_image) {
                    $path = $request->file('book_images')[$key]->store(
                        'books', 'public'
                    );
                    $ar_paths[] = $path;
                }
                if(empty($book->images)){
                    $book->images = json_encode($ar_paths);
                }else{
                    $ar_prev = json_decode($book->images);
                    $book->images = json_encode(array_merge($ar_prev , $ar_paths));
                }
                $book->save();
            }
            $response_ar['status'] = true;
            $response_ar['title'] = $this->bookEditMsg;
            return response()->json($response_ar);

        } catch (\Throwable $th) {

            $response_ar['status'] = false;
            $response_ar['title'] = $th->getMessage();
            return response()->json($response_ar);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'pid' => ['required']
        ],[],$this->attributes());

        $response_ar = [];

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response_ar['status'] = false;
            $response_ar['title'] = $errors->first();
            return response()->json($response_ar);    
        }
                
        try {
            $book = Book::find($request->get('pid'));
            if(!empty($book->images)){
                foreach (json_decode($book->images) as $bookimage) {
                    Storage::disk('public')->delete($bookimage);
                }
            }
            $book->delete();

            $response_ar['status'] = true;
            $response_ar['title'] = $this->bookDelMsg;
            return response()->json($response_ar);

        } catch (\Throwable $th) {
            $response_ar['status'] = false;
            $response_ar['title'] = $th->getMessage();
            return response()->json($response_ar);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroyImage(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'pid' => ['required'],
            'pimage' => ['required'],
        ],[],$this->attributes());

        $response_ar = [];

        if ($validator->fails()) {
            $errors = $validator->errors();
            $response_ar['status'] = false;
            $response_ar['title'] = $errors->first();
            return response()->json($response_ar);    
        }
                
        try {
            $book = Book::find($request->get('pid'));
            if(!empty($book->images)){
                $ar_images = [];
                foreach (json_decode($book->images) as $bookimage) {
                    if($bookimage != $request->get('pimage')){
                        $ar_images[] = $bookimage;
                        continue;
                    }
                    Storage::disk('public')->delete($bookimage);
                }
                $book->images = json_encode($ar_images);
                $book->updated_at = date('Y-m-d H:i:s');
                $book->save();
            }

            $response_ar['status'] = true;
            $response_ar['title'] = $this->bookDelMsg;
            return response()->json($response_ar);

        } catch (\Throwable $th) {
            $response_ar['status'] = false;
            $response_ar['title'] = $th->getMessage();
            return response()->json($response_ar);
        }
    }
    

    /**
     * Get the specified resource from storage.
     */
    public function categoryList()
    {
        return BookCategory::get(['id' ,'name']);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'book_name' => 'Name',
            'book_cat' => 'Category',
            'book_price' => 'Price',
            'book_seq' => 'Sequence No'
        ];
    }
}
