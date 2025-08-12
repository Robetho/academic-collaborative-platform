<?php 
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM `post_list` where id= '{$_GET['id']}' and user_id = '{$_settings->userdata('id')}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_array() as $k => $v){
            if(!is_numeric($k)){
                $$k = $v;
            }
        }
    }
}
?> 
<style>
    .form-group.note-form-group.note-group-select-from-files {
        display: none;
    }
    /* Optional: Style for file preview */
    .file-preview-container {
        margin-top: 10px;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f9f9f9;
        display: none; /* Hidden by default */
    }
    .file-preview-container img,
    .file-preview-container video,
    .file-preview-container iframe {
        max-width: 100%;
        height: auto;
        display: block;
        margin-bottom: 10px;
    }
    .file-preview-container iframe {
        width: 100%;
        height: 300px; /* Adjust height for PDF viewer */
    }
</style>
<section class="py-4">
    <div class="container">
        <div class="card rounded-0 shadow">
            <div class="card-header">
                <h5 class="card-title"><?= !isset($id) ? "Add New Topic" : "Update Topic Details" ?></h5>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <form action="" id="post-form" enctype="multipart/form-data"> <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
                        <div class="form-group">
                            <label for="title" class="control-label">Title</label>
                            <input type="text" class="form-control rounded-0" name="title" id="title" value="<?= isset($title) ? $title : "" ?>" required>
                        </div>
                        <div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12 px-0">
                            <label for="category_id" class="control-label">Category</label>
                            <select class="form-control rounded-0" name="category_id" id="category_id" required>
                                <option value="" disabled <?= !isset($category_id) ? "selected" : '' ?>>Please Select Category Here</option>
                                <?php 
                                // Make sure this query fetches categories related to the user or all if applicable
                                $category = $conn->query("SELECT * FROM category_list where delete_flag = '0' AND status = '1'");
                                while($row = $category->fetch_array()):
                                ?>
                                <option value="<?php echo $row['id'] ?>" <?= isset($category_id) && $category_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <?php 
                        $users = $conn->query("SELECT * FROM users where users.id = '{$_settings->userdata('id')}'");
                        $info = $users->fetch_array();
                        ?>
                        <div class="form-group">
                            <label for="program_name" class="control-label">Program Name</label>
                            <input type="text" class="form-control rounded-0" readonly name="program_name" id="program_name" value="<?= isset($info['program_name']) ? $info['program_name'] : "" ?>">
                        </div>
                        <div class="form-group">
                            <label for="faculty_name" class="control-label">Your Faculty</label>
                            <input type="text" class="form-control rounded-0" readonly name="faculty_name" id="faculty_name" value="<?= isset($info['faculty_name']) ? $info['faculty_name'] : "" ?>">
                        </div>
                        <div class="form-group">
                            <label for="content" class="control-label">Content</label>
                            <textarea type="text" class="form-control rounded-0" name="content" id="content"><?= isset($content) ? $content : "" ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="attached_file" class="control-label">Attach File</label>
                            <input type="file" class="form-control rounded-0" name="attached_file" id="attached_file" accept=".pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx, image/*">
                            <small class="text-muted">Accepted formats: PDF, Word, Excel, PowerPoint, Images (JPG, JPEG, PNG, GIF, WEBP)</small>
                        </div>

                        <div id="file-preview" class="file-preview-container">
                            <?php if(isset($file_path) && !empty($file_path)): ?>
                                <?php
                                    $existing_file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
                                    $existing_file_url = base_url . $file_path;
                                ?>
                                <?php if (in_array(strtolower($existing_file_extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                    <img src="<?= $existing_file_url ?>" class="img-fluid rounded border" alt="Attached Image">
                                <?php elseif (strtolower($existing_file_extension) == 'pdf'): ?>
                                    <iframe src="<?= $existing_file_url ?>" class="rounded border"></iframe>
                                <?php else: ?>
                                    <p class="text-muted"><i class="fa fa-file"></i> Current File: <a href="<?= $existing_file_url ?>" target="_blank"><?= basename($file_path) ?></a></p>
                                <?php endif; ?>
                                <button type="button" class="btn btn-sm btn-danger mt-2" id="remove_current_file"><i class="fa fa-times"></i> Remove Current File</button>
                                <input type="hidden" name="current_file_path" value="<?= $file_path ?>">
                            <?php endif; ?>
                            <p class="file-name text-muted"></p>
                        </div>

                        <div class="form-group">
                            <div class="icheck-primary d-inline">
                                <input type="checkbox" id="status" name='status' value="1" <?= isset($status) && $status == 1 ? 'checked' : '' ?>>
                                <label for="status">
                                </label>
                            </div>
                            <label for="status" class="control-label">Published</label>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-footer py-1 text-center">
                <button class="btn btn-flat btn-sm btn-primary bg-gradient-primary rounded-0" form="post-form"><i class="fa fa-save"></i> Save</button>
                <a class="btn btn-flat btn-sm btn-light bg-gradient-light border rounded-0" href="./?p=posts"><i class="fa fa-angle-left"></i> Cancel</a>
            </div>
        </div>
    </div>
</section>

<script>
    $(function(){
        // Pre-select category if editing
        <?php if(isset($category_id)): ?>
            $('#category_id').val('<?= $category_id ?>');
        <?php endif; ?>

        $('#category_id').select2({
            placeholder:"Please Select Category Here",
            width:'100%',
            containerCssClass:'form-control rounded-0'
        });

        $('#content').summernote({
            height:"20em",
            placeholder:"Write your content here",
            toolbar: [
                [ 'style', [ 'style' ] ],
                [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
                [ 'fontname', [ 'fontname' ] ],
                [ 'fontsize', [ 'fontsize' ] ],
                [ 'color', [ 'color' ] ],
                [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
                [ 'table', [ 'table' ] ],
                [ 'insert', [ 'picture' ] ],
                [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
            ]
        });

        // File Preview Logic
        const attachedFileInput = $('#attached_file');
        const filePreviewContainer = $('#file-preview');
        const fileNameDisplay = filePreviewContainer.find('.file-name');
        const removeCurrentFileButton = $('#remove_current_file');
        const currentFilePathInput = $('[name="current_file_path"]');

        function displayFilePreview(file) {
            if (!file) {
                filePreviewContainer.hide();
                return;
            }

            const fileType = file.type;
            const reader = new FileReader();
            filePreviewContainer.empty(); // Clear previous content

            if (fileType.startsWith('image/')) {
                reader.onload = function(e) {
                    filePreviewContainer.append('<img src="' + e.target.result + '" class="img-fluid rounded border" alt="Attached Image">');
                    filePreviewContainer.append('<p class="file-name text-muted">' + file.name + '</p>');
                    filePreviewContainer.append('<button type="button" class="btn btn-sm btn-danger mt-2" id="remove_uploaded_file"><i class="fa fa-times"></i> Remove Uploaded File</button>');
                    filePreviewContainer.show();
                };
                reader.readAsDataURL(file);
            } else if (fileType === 'application/pdf') {
                filePreviewContainer.append('<iframe src="' + URL.createObjectURL(file) + '" class="rounded border"></iframe>');
                filePreviewContainer.append('<p class="file-name text-muted">' + file.name + '</p>');
                filePreviewContainer.append('<button type="button" class="btn btn-sm btn-danger mt-2" id="remove_uploaded_file"><i class="fa fa-times"></i> Remove Uploaded File</button>');
                filePreviewContainer.show();
            } else {
                filePreviewContainer.append('<p class="text-muted"><i class="fa fa-file"></i> Attached File: ' + file.name + '</p>');
                filePreviewContainer.append('<button type="button" class="btn btn-sm btn-danger mt-2" id="remove_uploaded_file"><i class="fa fa-times"></i> Remove Uploaded File</button>');
                filePreviewContainer.show();
            }
            
            // Add event listener for removing newly uploaded file
            filePreviewContainer.off('click', '#remove_uploaded_file').on('click', '#remove_uploaded_file', function() {
                attachedFileInput.val(''); // Clear the input
                filePreviewContainer.hide().empty(); // Hide and clear preview
            });
        }

        // Display existing file on page load if available
        <?php if(isset($file_path) && !empty($file_path)): ?>
            filePreviewContainer.show();
        <?php endif; ?>

        // Event listener for new file selection
        attachedFileInput.change(function() {
            if (this.files && this.files[0]) {
                displayFilePreview(this.files[0]);
                // If a new file is selected, effectively mark current_file_path for deletion
                currentFilePathInput.val(''); 
            } else {
                // If no new file, check if there was an existing one to show
                <?php if(isset($file_path) && !empty($file_path)): ?>
                    filePreviewContainer.show(); // Re-show existing if nothing new selected
                    currentFilePathInput.val('<?= $file_path ?>'); // Keep existing path
                <?php else: ?>
                    filePreviewContainer.hide();
                <?php endif; ?>
            }
        });

        // Event listener for removing current (existing) file
        removeCurrentFileButton.click(function() {
            if (confirm("Are you sure you want to remove the current attached file?")) {
                currentFilePathInput.val('REMOVE'); // Special value to indicate deletion
                attachedFileInput.val(''); // Clear any newly selected file
                filePreviewContainer.hide().empty(); // Hide and clear preview
            }
        });


        $('#post-form').submit(function(e){
            e.preventDefault();
            var _this = $(this);
            var el = $('<div>');
            el.addClass('alert alert-danger err_msg');
            el.hide();
            $('.err_msg').remove();
            if(_this[0].checkValidity() == false){
                _this[0].reportValidity();
                return false;
            }
            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=save_post",
                method:'POST',
                type:'POST',
                data:new FormData($(this)[0]),
                dataType:'json',
                cache:false,
                processData:false,
                contentType: false,
                error:err=>{
                    console.log(err);
                    alert('An error occurred');
                    end_loader();
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.replace('./?p=posts/view_post&id='+resp.pid);
                    }else if(!!resp.msg){
                        el.html(resp.msg);
                        el.show('slow');
                        _this.prepend(el);
                        $('html, body').scrollTop(0);
                    }else{
                        alert('An error occurred');
                        console.log(resp);
                    }
                    end_loader();
                }
            });
        });
    });
</script>