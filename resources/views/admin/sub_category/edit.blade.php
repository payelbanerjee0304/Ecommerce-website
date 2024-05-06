@extends('admin.layouts.app')
<!-- Content Header (Page header) -->
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Edit Sub Category</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href="{{route('sub-categories.index')}}" class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
					<div class="container-fluid">
                        <form action="" name="subCategoryForm" id="subCategoryForm">
                            <div class="card">
                                <div class="card-body">								
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category">Category</label>
                                                <select name="category" id="category" class="form-control">
                                                    <option value="">Select</option>
                                                    @if($categories->isNotEmpty())
                                                    @foreach($categories as $category)
                                                    <option {{($subCategory->category_id==$category->id) ? 'selected': ''}} value="{{$category->id}}">{{$category->name}}</option>
                                                    @endforeach
                                                    @endif
                                                </select>
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name">Name</label>
                                                <input type="text" name="name" id="name" class="form-control" placeholder="Name" value="{{$subCategory->name}}">	
                                                <p></p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="slug">Slug</label>
                                                <input type="text" name="slug" id="slug" class="form-control" placeholder="Slug" readonly  value="{{$subCategory->slug}}">	
                                                <p></p>
                                            </div>
                                        </div>									
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status">Status</label>
                                                <select name="status" id="status"  class="form-control">
                                                    <option {{($subCategory->status==1)? 'selected': '' }} value="1">Active</option>
                                                    <option {{($subCategory->status==0)? 'selected': '' }} value="0">Block</option>
                                                </select>	
                                                <p></p>
                                            </div>
                                        </div>									
                                    </div>
                                </div>							
                            </div>
                            <div class="pb-5 pt-3">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <button type="reset" class="btn btn-outline-dark ml-3">Cancel</button>
                                <!-- <a href="subcategory.html" class="btn btn-outline-dark ml-3">Cancel</a> -->
                            </div>
                        </form>
					</div>
					<!-- /.card -->
				</section>
				<!-- /.content -->
@endsection

@section('customJs')

<script>

$('#subCategoryForm').submit(function(e){
    e.preventDefault();

    $('button[type=submit]').prop('disabled',true);

    $.ajax({
        url:"{{Route('sub-categories.update', $subCategory->id)}}",
        type: 'put',
        data:$(this).serializeArray(),
        datatype: 'json',
        success: function(response){
            $('button[type=submit]').prop('disabled',false);
            if(response['status']==true){

                Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                                customClass: {
                                    popup: 'my-swal-popup',
                                    icon: 'my-swal-icon',
                                    title: 'my-swal-title',
                                    content: 'my-swal-content',
                                    text: 'my-swal-content',
                                    confirmButton: 'my-swal-confirm-button'
                                }
                            }).then((result) => {
                            if (result.isConfirmed) {
                                // Redirect to dashboard
                                window.location.href='{{route("sub-categories.index")}}';
                            }
                        });
                

                $('#name').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html('');

                    $('#slug').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html('');

                    $('#category').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html('');

            }else{
                if(response['notFound']==true){
                    window.location.href='{{route("sub-categories.index")}}';
                    return false;
                }

                var errors= response['errors'];
                if(errors['name'] ){
                    $('#name').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['name']);
                }else{
                    $('#name').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html('');
                }

                if(errors['slug'] ){
                    $('#slug').addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback').html(errors['slug']);
                }else{
                    $('#slug').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html('');
                }
                if(errors['category'] ){
                    $('#category').addClass('is-invalid')
                    .siblings('p')
                    .addClass('invalid-feedback').html(errors['category']);
                }else{
                    $('#category').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html('');
                }
            }
        },
        error: function(jqXHR, exception) {
            console.log("Something Went Wrong");
        }
    })
})

//To copy name field to slug
$("#name").change(function(){
    $('button[type=submit]').prop('disabled',true);
    $.ajax({
            url:"{{Route('getSlug')}}",
            type: 'get',
            data:{title:$(this).val()},
            datatype: 'json',
            success: function(response){
                $('button[type=submit]').prop('disabled',false);

                if(response['status']==true){
                    $("#slug").val(response['slug']);
                }
            }
    });
})
</script>
@endsection 