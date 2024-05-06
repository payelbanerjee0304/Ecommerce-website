<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductImage;
use Illuminate\Support\Facades\File;

class ProductImageController extends Controller
{
    public function update(Request $request){

        $image=$request->image;
        $ext=$image->getClientOriginalExtension();
        $tempImageLocation=$image->getPathName();

        //insert image
        $productImage= new ProductImage();
        $productImage->product_id=$request->product_id;
        $productImage->image="NULL";
        $productImage->save();

        //update image
        $imageName=$request->product_id.'-'.$productImage->id.'-'.time().'.'.$ext;
        // $productImage->image=$imageName;
        // productImage->save();

        $sPath=$tempImageLocation;
        $dPath=public_path().'/uploads/product/'.$imageName;
        File::copy($sPath,$dPath);

        $productImage->image=$imageName;
        $productImage->save();

        return Response()->json(['status'=>true,'image_id'=>$productImage->id,'ImagePath'=>asset('/uploads/product/'.$imageName),'message'=>'Image successfully updated']);
    }
    public function destroy(Request $request)
    {
        $productImage=ProductImage::find($request->id);
        if(empty($productImage)){
            return Response()->json(['status'=>false,'message'=>'Image not found']);
        }

        //Delete image from folder
        File::delete(public_path('/uploads/product/'.$productImage->image));

        //Delete image from Database
        $productImage->delete();
        return Response()->json(['status'=>true,'message'=>'Image successfully deleted']);
    }
}
