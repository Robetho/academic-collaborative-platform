<?php 
if(isset($_GET['id'])){
    $user = $conn->query("SELECT * FROM faculty where id ='{$_GET['id']}' ");
    foreach($user->fetch_array() as $k =>$v){
        $meta[$k] = $v;
    }
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline rounded-0 card-navy">
	<div class="card-body">
		<div class="container-fluid">
			<div id="msg"></div>
			<form action="" id="manage-user">	
				<input type="hidden" name="id" value="<?= isset($meta['id']) ? $meta['id'] : '' ?>">
				<div class="form-group">
					<label for="name">Faculty Name</label>
					<input type="text" name="faculty_name" id="faculty_name" class="form-control" value="<?php echo isset($meta['faculty_name']) ? $meta['faculty_name']: '' ?>" required>
				</div>
			</form>
		</div>
	</div>
	<div class="card-footer">
			<div class="col-md-12">
				<div class="row">
					<button class="btn btn-sm btn-primary rounded-0 mr-3" form="manage-user">Save Faculty Details</button>
					<a href="./?page=faculty/lists" class="btn btn-sm btn-default border rounded-0" form="manage-user"><i class="fa fa-angle-left"></i> Cancel</a>
				</div>
			</div>
		</div>
</div>
<style>
	img#cimg{
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}
</style>
<script>
    $(function(){
        $('#manage-user').submit(function(e){
            e.preventDefault()
            var _this = $(this)
            var el = $('<div>')
                el.addClass('alert alert-danger err_msg')
                el.hide()
            $('.err_msg').remove()
            if(_this[0].checkValidity() == false){
                _this[0].reportValidity();
                return false;
            }
            start_loader()
            $.ajax({
                url:_base_url_+"classes/Master.php?f=save_faculty",
                method:'POST',
                type:'POST',
                data:new FormData($(this)[0]),
                dataType:'json',
                cache:false,
                processData:false,
                contentType: false,
                error:err=>{
                    console.log(err)
                    alert('An error occurred')
                    end_loader()
                },
                success:function(resp){
                    if(resp.status == 'success'){
                    location.replace('./?page=faculty/lists')
                    }else if(!!resp.msg){
                        el.html(resp.msg)
                        el.show('slow')
                        _this.prepend(el)
                        $('html, body').scrollTop(0)
                    }else{
                        alert('An error occurred')
                        console.log(resp)
                    }
                    end_loader()
                }
            })
        })
    })
</script>