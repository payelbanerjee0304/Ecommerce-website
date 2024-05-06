<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Image;
use Illuminate\Support\Facades\Session;
// use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories=Category::latest();
        if(!empty($request->get('keyword')))
        {
            $categories=$categories->where('name','like','%'.$request->get('keyword').'%');
        }
        $categories=$categories->paginate(10); //get order list in descending order it is a shortcut of orderBy('created_at', 'DESC')
        // dd($categories);
        // $data["categories"]=$categories;
        return view('admin/category/list',compact('categories'));
    }
    public function create()
    {
        // echo "category";
        return view('admin/category/create');
    }
    public function store(Request $request)
    {
        $validator=validator::make($request->all(),[
            'name'=>'required',
            'slug'=>'required|unique:categories',
        ]);
        if($validator->passes()){
            $category=new category;
            $category->name=$request->name;
            $category->slug=$request->slug;
            $category->status=$request->status;
            $category->save();

            //Save image here
            if(!empty($request->image_id)){
                $tempImage =TempImage::find($request->image_id);
                $extArray=explode('.',$tempImage->name);
                $ext=last($extArray);

                $newImageName=$category->id.'.'.$ext;
                $sPath=public_path().'/temp/'.$tempImage->name;
                $dPath=public_path().'/uploads/category/'.$newImageName;
                File::copy($sPath,$dPath);

                // //Generate Image thubnail
                // $dPath=public_path().'/uploads/category/thumb'.$newImageName;
                // $img = Image::make($sPath);
                // $img->resize(450,600);
                // $img->save($dPath);

                $category->image=$newImageName;
                $category->save();

                
                

            }

            $request->session()->flash('success','Category Added Successfully');
            return response()->json([
                'status' => true,
                'message'=>"Category Added Successfully"
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors'=>$validator->errors()
            ]);
        }
    }
    public function edit($categoryId, Request $request)
    {
        $category=Category::find($categoryId);
        if(empty(($category)))
        {
            $request->session()->flash('error','Category Not Found ');
            return redirect()->route('categories.index');
        }
        return view('admin.category.edit',compact('category'));
    }
    public function update($categoryId, Request $request)
    {
        {
            $category=Category::find($categoryId);
            if(empty(($category)))
            {
                $request->session()->flash('error','Category Not Found ');
                return response()->json([
                    'status' => false,
                    'not found'=>true,
                    'message'=>"Category not found"
                ]);
            }
            $validator=validator::make($request->all(),[
                'name'=>'required',
                'slug'=>'required|unique:categories,slug,'.$category->id.',id',
            ]);
            if($validator->passes()){
                $category->name=$request->name;
                $category->slug=$request->slug;
                $category->status=$request->status;
                $category->save();
                $oldImage= $category->image;
                //Save image here
                if(!empty($request->image_id)){
                    $tempImage =TempImage::find($request->image_id);
                    $extArray=explode('.',$tempImage->name);
                    $ext=last($extArray);
                    $newImageName=$category->id.'-'.time().'.'.$ext;
                    $sPath=public_path().'/temp/'.$tempImage->name;
                    $dPath=public_path().'/uploads/category/'.$newImageName;
                    File::copy($sPath,$dPath);
                    // //Generate Image thubnail
                    // $dPath=public_path().'/uploads/category/thumb'.$newImageName;
                    // $img = Image::make($sPath);
                    // $img->resize(450,600);
                    // $img->save($dPath);
                    $category->image=$newImageName;
                    $category->save();
                    //Delete old images here
                    File::delete(public_path().'/uploads/category/'.$oldImage);
                }
                $request->session()->flash('message','Category Updated Successfully');
                return response()->json([
                    'status' => true,
                    'message'=>"Category Updated Successfully"
                ]);
            }else{
                return response()->json([
                    'status' => false,
                    'errors'=>$validator->errors()
                ]);
            }
        }
    }
    public function destroy($categoryId, Request $request)
    {
        $category=Category::find($categoryId);
        if(empty($category))
        {
            $request->session()->flash('Error','Category Not Found');
            return response()->json([
                'status' => true,
                'message'=>"Category not found"
            ]);
        }
        //Delete old images here
            File::delete(public_path().'/uploads/category/'.$category->image);

            $category->delete();
            $request->session()->flash('success','Category Deleted Successfully');
            return response()->json([
                'status' => true,
                'message'=>"Category Deleted Successfully"
            ]);
    }
}
