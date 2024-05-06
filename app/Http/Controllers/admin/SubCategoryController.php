<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $subcategories=SubCategory::select('sub_categories.*','categories.name as categoryName')
                                    ->latest('sub_categories.id')
                                    ->leftJoin('categories','categories.id','sub_categories.category_id');
        if(!empty($request->get('keyword')))
        {
            $subcategories=$subcategories->where('sub_categories.name','like','%'.$request->get('keyword').'%')->orWhere('categories.name','like','%'.$request->get('keyword').'%');
        }
        $subcategories=$subcategories->paginate(10); //get order list in descending order it is a shortcut of orderBy('created_at', 'DESC')
        // dd($subcategories);
        // $data["subcategories"]=$subcategories;
        return view('admin/sub_category/list',compact('subcategories'));
    }

    public function create(){
        $categories= Category::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        return view('admin.sub_category.create',$data);
    }

    public function store(Request $request)
    {
        $validator=validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:sub_categories',
            'category'=>'required',
            'status'=>'required',
        ]);
        if($validator->passes()){
            $category=new SubCategory;
            $category->name=$request->name;
            $category->slug=$request->slug;
            $category->status=$request->status;
            $category->category_id=$request->category;
            $category->save();

            $request->session()->flash('success','Sub Category Added Successfully');

            return response()->json([
                'status' => true,
                'message'=>"Sub Category Added Successfully"
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
        $subCategory=SubCategory::find($id);
        if(empty(($subCategory)))
        {
            $request->session()->flash('error','Sub Category Not Found ');
            return redirect()->route('sub-categories.index');
        }
        $categories= Category::orderBy('name','ASC')->get();
        $data['categories']=$categories;
        $data['subCategory']=$subCategory;
        return view('admin.sub_category.edit',$data);
    }

    public function update($id, Request $request)
    {
        {
            $subCategory=SubCategory::find($id);
            if(empty(($subCategory)))
            {
                $request->session()->flash('error','Sub Category Not Found ');
                return response()->json([
                    'status' => false,
                    'not found'=>true,
                    'message'=>"Sub-Category not found"
                ]);
            }
            $validator=validator::make($request->all(),[
                'name'=>'required',
                'slug'=>'required|unique:categories,slug,'.$subCategory->id.',id',
                'category'=>'required',
                'status'=>'required',
            ]);
            if($validator->passes()){
                $subCategory->name=$request->name;
                $subCategory->slug=$request->slug;
                $subCategory->status=$request->status;
                $subCategory->category_id=$request->category;
                $subCategory->save();
                
                $request->session()->flash('message','Sub-Category Updated Successfully');
                return response()->json([
                    'status' => true,
                    'message'=>"Sub-Category Updated Successfully"
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
        $subcategory=SubCategory::find($id);
        if(empty($subcategory))
        {
            $request->session()->flash('Error','Sub Category Not Found');
            return response()->json([
                'status' => true,
                'message'=>"Sub Category not found"
            ]);
        }

            $subcategory->delete();
            $request->session()->flash('success','Sub Category Deleted Successfully');
            return response()->json([
                'status' => true,
                'message'=>"Sub Category Deleted Successfully"
            ]);
    }
}
