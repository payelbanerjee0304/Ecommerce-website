@extends('admin.layouts.app')
<!-- Content Header (Page header) -->
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header" background-color="red">					
					<div class="container-fluid my-2">
						<div class="row mb-2">
							<div class="col-sm-6">
								<h1>Create Category</h1>
							</div>
							<div class="col-sm-6 text-right">
								<a href="{{route('categories.index')}}" class="btn btn-primary">Back</a>
							</div>
						</div>
					</div>
					<!-- /.container-fluid -->
				</section>
				<!-- Main content -->
				<section class="content">
					<!-- Default box -->
					<div class="container-fluid">
                        <form action="" method="post" id="categoryForm" name="categoryForm">
                            <div class="card">
                                <div class="card-body">								
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="name">Name</label>
                                                <input type="text" name="name" id="name" class="form-control" placeholder="Name">	
                                                <p></p>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="slug">Slug</label>
                                                <input type="text" readonly name="slug" id="slug" class="form-control" placeholder="Slug">	
                                                <p></p>
                                            </div>                                           
                                        </div>		
                                        <div class="col-md-6">
                                            <div class="mb-3">	
                                                <input type="text" hidden name="image_id" id="image_id" value="">
                                                <label for="image">Image</label>
                                                <div id="image" class="dropzone dz-clickable">
                                                    <div class="dz-message needsclick">    
                                                        <br>Drop files here or click to upload.<br><br>                                            
                                                    </div>
                                                </div>		
                                            </div>                                           
                                        </div>					
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="status">Status</label>
                                                <select name="status" id="status"  class="form-control">
                                                    <option value="1">Active</option>
                                                    <option value="0">Block</option>
                                                </select>	
                                            </div>
                                            <div class="mb-3">
                                                <label for="showHome">Show on Home</label>
                                                <select name="showHome" id="showHome"  class="form-control">
                                                    <option value="1">Active</option>
                                                    <option value="0">Block</option>
                                                </select>	
                                            </div>
                                        </div>					
															
                                    </div>
                                </div>							
                            </div>
                            <div class="pb-5 pt-3">
                                <button type="submit" class="btn btn-success">Create</button>
                                <button type="reset" class="btn btn-outline-dark ml-3">Cancel</button>
                                <!-- <a href="#" class="btn btn-outline-dark ml-3">Cancel</a> -->
                            </div>
                        </form>
					</div>
					<!-- /.card -->
				</section>
				<!-- /.content -->
@endsection

@section('customJs')

<script>

$('#categoryForm').submit(function(e){
    e.preventDefault();
    $('button[type=submit]').prop('disabled',true);
    $.ajax({
        url:"{{Route('categories.store')}}",
        type: 'post',
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
                                window.location.href='{{route("categories.index")}}';
                            }
                        });
                

                $('#name').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html('');

                    $('#slug').removeClass('is-invalid')
                    .siblings('p')
                    .removeClass('invalid-feedback').html('');
            }else{
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
            }
        },
        error: function(jqXHR, exception) {
            console.log("Something Went Wrong");
        }
    })
})
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
Dropzone.autoDiscover = false;    
const dropzone = $("#image").dropzone({ 
    init: function() {
        this.on('addedfile', function(file) {
            if (this.files.length > 1) {
                this.removeFile(this.files[0]);
            }
        });
    },
    url:  "{{ route('temp-images.create') }}",
    maxFiles: 1,
    paramName: 'image',
    addRemoveLinks: true,
    acceptedFiles: "image/jpeg,image/png,image/gif",
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }, success: function(file, response){
        $("#image_id").val(response.image_id);
        //console.log(response)
    }
});
</script>
@endsection