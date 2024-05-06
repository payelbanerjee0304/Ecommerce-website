<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Brand;
use Illuminate\Support\Facades\Session;

class BrandsController extends Controller
{
    public function index(Request $request)
    {
        $brands=Brand::latest();
        if(!empty($request->get('keyword')))
        {
            $request->session()->flash('Error','Sub Category Not Found');
            $brands=$brands->where('name','like','%'.$request->get('keyword').'%');
        }
        $brands=$brands->paginate(10); //get order list in descending order it is a shortcut of orderBy('created_at', 'DESC')
        // dd($brands);
        // $data["brands"]=$brands;
        return view('admin/brands/list',compact('brands'));
    }
    public function create()
    {
        return view('admin.brands.create');
    }
    public function store(Request $request)
    {
        $validator=validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:brands',
        ]);
        if($validator->passes()){
            $brands=new brand;
            $brands->name=$request->name;
            $brands->slug=$request->slug;
            $brands->status=$request->status;
            $brands->save();

            $request->session()->flash('success','Brands Added Successfully');

            return response()->json([
                'status' => true,
                'message'=>"Brands Added Successfully"
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
        $brands= Brand::find($id);
        if(empty(($brands)))
        {
            $request->session()->flash('error','Sub Category Not Found ');
            return redirect()->route('sub-categories.index');
        }
        $categories= Brand::orderBy('name','ASC')->get();
        $data['brands']=$brands;
        return view('admin.brands.edit',$data);
    }

    public function update($id, Request $request)
    {
        {
            $brands=Brand::find($id);
            if(empty(($brands)))
            {
                $request->session()->flash('error','Brand Not Found ');
                return response()->json([
                    'status' => false,
                    'not found'=>true,
                    'message'=>"Brand not found"
                ]);
            }
            $validator=validator::make($request->all(),[
                'name'=>'required',
                'slug'=>'required|unique:brands,slug,'.$brands->id.',id',
                'status'=>'required',
            ]);
            if($validator->passes()){
                $brands->name=$request->name;
                $brands->slug=$request->slug;
                $brands->status=$request->status;
                $brands->save();
                
                $request->session()->flash('success','Brand Updated Successfully');
                return response()->json([
                    'status' => true,
                    'message'=>"Brand Updated Successfully"
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'errors'=>$validator->errors()
                ]);
            }
        }
    }

    public function destroy($id, Request $request)
    {
        $brand=Brand::find($id);
        if(empty($brand))
        {
            $request->session()->flash('Error','Brand Not Found');
            return response()->json([
                'status' => true,
                'message'=>"Brand not found"
            ]);
        }

            $brand->delete();
            $request->session()->flash('success','Brand Deleted Successfully');
            return response()->json([
                'status' => true,
                'message'=>"Brand Deleted Successfully"
            ]);
    }
}
