<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products=Product::latest('id')->with('product_images');
        // dd($products);
        if(!empty($request->get('keyword')))
        {
            $products=$products->where('title','like','%'.$request->get('keyword').'%');
        }
        $products=$products->paginate(10); //get order list in descending order it is a shortcut of orderBy('created_at', 'DESC')
        return view('admin/products/list',compact('products'));
    }

    public function create()
    {
        $data=[];
        $categories= Category::orderBy('name','ASC')->get();
        $brands= Brand::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        $data['brands']=$brands;
        return view('admin.products.create',$data);
    }
    public function store(Request $request)
    {
        
        if (!empty($request->track_qty) && $request->track_qty=='Yes') {
            $rules=[
                'title'=>'required',
                'slug'=>'required|unique:products',
                'price'=>'required|numeric',
                'sku'=>'required|unique:products',
                'track_qty'=>'required|in:Yes,No',
                'category'=>'required|numeric',
                'is_featured'=>'required|in:Yes,No',
                'qty'=>'required|numeric'
            ];
            
        }else{
            $rules=[
                'title'=>'required',
                'slug'=>'required|unique:products',
                'price'=>'required|numeric',
                'sku'=>'required|unique:products',
                'track_qty'=>'required|in:Yes,No',
                'category'=>'required|numeric',
                'is_featured'=>'required|in:Yes,No',
            ];
        }

        $validator=Validator::make($request->all(),$rules);

        if($validator->passes()){
            $product=new Product;
            $product->title=$request->title;
            $product->slug=$request->slug;
            $product->description=$request->description;
            $product->price=$request->price;
            $product->compare_price=$request->compare_price;
            $product->sku=$request->sku;
            $product->track_qty=$request->track_qty;
            $product->barcode=$request->barcode;
            $product->qty=$request->qty;
            $product->status=$request->status;
            $product->category_id=$request->category;
            $product->sub_category_id=$request->sub_category;
            $product->brand_id=$request->brand;
            $product->is_featured=$request->is_featured;
            
            $product->save();

            //Save image here
            if(!empty($request->image_array)){
                foreach($request->image_array as $temp_image_id){

                    $tempImageInfo =TempImage::find($temp_image_id);
                    $extArray=explode('.',$tempImageInfo->name);
                    $ext=last($extArray);

                    $productImage= new ProductImage();
                    $productImage->product_id=$product->id;

                    $productImage->image="NULL";
                    $productImage->save();

                    $imageName=$product->id.'-'.$productImage->id.'-'.time().'.'.$ext;

                    $sPath=public_path().'/temp/'.$tempImageInfo->name;
                    $dPath=public_path().'/uploads/product/'.$imageName;
                    File::copy($sPath,$dPath);

                    $productImage->image=$imageName;
                    $productImage->save();

                }
                
            }

            $request->session()->flash('success','Products Added Successfully');

            return response()->json([
                'status' => true,
                'message'=>"Products Added Successfully"
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors'=>$validator->errors()
            ]);
        }
    }
    public function edit($id, Request $request)
    {
        $product=Product::find($id);
        if(empty(($product)))
        {
            // $request->session()->flash('error','Product Not Found ');
            return redirect()->route('products.index')->with('error','Product Not Found ');
        }

        //Fetch Product Images
        $productImages=ProductImage::where('product_id',$product->id)->get();
        
        $subCategories=SubCategory::where('category_id',$product->category_id)->get();

        $data=[];
        $categories= Category::orderBy('name','ASC')->get();
        $brands= Brand::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        $data['brands']=$brands;
        $data['product']=$product;
        $data['subCategories']=$subCategories;
        $data['productImages']=$productImages;
        
        if(empty(($product)))
        {
            $request->session()->flash('error','Category Not Found ');
            return redirect()->route('products.index');
        }
        return view('admin.products.edit',$data);
    }
    public function update($id, Request $request)
    {
        $product=Product::find($id);
        
        if (!empty($request->track_qty) && $request->track_qty=='Yes') {
            $rules=[
                'title'=>'required',
                'slug'=>'required|unique:products,slug,'.$product->id.',id',
                'price'=>'required|numeric',
                'sku'=>'required|unique:products,sku,'.$product->id.',id',
                'track_qty'=>'required|in:Yes,No',
                'category'=>'required|numeric',
                'is_featured'=>'required|in:Yes,No',
                'qty'=>'required|numeric'
            ];
            
        }else{
            $rules=[
                'title'=>'required',
                'slug'=>'required|unique:products,slug,'.$product->id.',id',
                'price'=>'required|numeric',
                'sku'=>'required|unique:products,sku,'.$product->id.',id',
                'track_qty'=>'required|in:Yes,No',
                'category'=>'required|numeric',
                'is_featured'=>'required|in:Yes,No',
            ];
        }

        $validator=Validator::make($request->all(),$rules);

        if($validator->passes()){

            $product->title=$request->title;
            $product->slug=$request->slug;
            $product->description=$request->description;
            $product->price=$request->price;
            $product->compare_price=$request->compare_price;
            $product->sku=$request->sku;
            $product->track_qty=$request->track_qty;
            $product->barcode=$request->barcode;
            $product->qty=$request->qty;
            $product->status=$request->status;
            $product->category_id=$request->category;
            $product->sub_category_id=$request->sub_category;
            $product->brand_id=$request->brand;
            $product->is_featured=$request->is_featured;
            
            $product->save();

            

            $request->session()->flash('success','Products Updated Successfully');

            return response()->json([
                'status' => true,
                'message'=>"Products Updated Successfully"
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors'=>$validator->errors()
            ]);
        }
    }
    public function destroy($id, Request $request)
    {
        $product=Product::find($id);

        if(empty($product))
        {
            $request->session()->flash('Error','Product Not Found');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message'=> "Product Not Found"
            ]);
        }
        //Delete old images here
        $productImages=productImage::where('product_id',$id)->get();

        if(!empty($productImages)){
            foreach($productImages as $productImage){
                File::delete(public_path().'/uploads/product/'.$productImage->image);
            }
            productImage::where('product_id',$id)->delete();
        }
        
        $product->delete();

        $request->session()->flash('success','Product Deleted Successfully');
        return response()->json([
            'status' => true,
            'message'=> "Product Deleted Successfully"
        ]);

    }
}
