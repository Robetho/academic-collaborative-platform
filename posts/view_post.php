<?php
// PHP Logic to fetch post details
if(isset($_GET['id'])){
    // MODIFIED: Added p.file_path to the SELECT query
    $qry = $conn->query("SELECT p.*, u.username,u.secret_code, u.avatar, c.name as `category` FROM `post_list` p inner join category_list c on p.category_id = c.id inner join `users` u on p.user_id = u.id where p.id= '{$_GET['id']}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_array() as $k => $v){
            if(!is_numeric($k)){
                $$k = $v;
            }
        }
    }else{
        echo '<script> alert("Post ID is not recognized."); location.replace("./?p=posts");</script>';
    }
}else{
    echo '<script> alert("Post ID is required"; location.replace("./?p=posts");</script>';
}
?>
<style>
    .post-user, .comment-user{
        width: 1.8em;
        height: 1.8em;
        object-fit:cover;
        object-position:center center;
    }
    /* Style for recording buttons and players */
    #audio-recorder-section, #video-recorder-section, #file-upload-section {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 15px;
        border-radius: 5px;
    }
    #audio-player, #recorded-video {
        margin-top: 10px;
        border: 1px solid #ced4da;
        border-radius: 5px;
    }

    /* Increased height for video display in comments */
    .comment-list video {
        max-height: 300px; /* Increased from 200px to 300px */
        object-fit: contain; /* Ensures the entire video is visible within the bounds */
    }

    /* Increased height for the recorded video preview before submission */
    #recorded-video {
        height: 25vh !important; /* Increased from 15vh to 25vh, using !important for override */
        max-height: 300px; /* Optional: set a max-height for large screens */
        object-fit: contain;
    }

    /* Style for embedded PDF/Image containers */
    .pdf-embed-container {
        position: relative;
        width: 100%;
        padding-bottom: 75%; /* 4:3 aspect ratio, adjust as needed for content */
        height: 0;
        overflow: hidden;
        background-color: #eee; /* Placeholder background */
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .pdf-embed-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none; /* Removed inner border */
    }
    .comment-list img, .post-file-preview img { /* Changed to .post-file-preview */
        max-width: 100%;
        max-height: 150px; /* MODIFIED: Smaller height for preview */
        height: auto;
        display: block; /* Ensures it takes its own line */
        margin-bottom: 10px;
        object-fit: contain; /* Ensures image fits within bounds */
        cursor: pointer; /* Indicate clickable */
    }
    .post-file-preview { /* New class for the preview container */
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed #ccc;
    }

    /* Styles for the modal */
    #fileViewerModal .modal-dialog {
        max-width: 90%;
        height: 90%;
    }
    #fileViewerModal .modal-content {
        height: 100%;
    }
    #fileViewerModal .modal-body {
        height: calc(100% - 110px); /* Adjust based on header/footer height */
        overflow: auto;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    #fileViewerModal .modal-body img,
    #fileViewerModal .modal-body iframe,
    #fileViewerModal .modal-body video,
    #fileViewerModal .modal-body audio {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    #fileViewerModal .modal-body iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    /* NEW: Shake animation for modal */
    @keyframes shake {
        10%, 90% {
            transform: translate3d(-1px, 0, 0);
        }
        20%, 80% {
            transform: translate3d(2px, 0, 0);
        }
        30%, 50%, 70% {
            transform: translate3d(-4px, 0, 0);
        }
        40%, 60% {
            transform: translate3d(4px, 0, 0);
        }
    }

    .shake {
        animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
        transform: translate3d(0, 0, 0);
        backface-visibility: hidden;
        perspective: 1000px;
    }
</style>
<div class="section py-5">
    <div class="container">
        <div class="card rounded-0 shadow">
            <div class="card-header">
                <h4 class="card-title">Post Details</h4>
                <?php if($_settings->userdata('id') == $user_id): ?>
                    <div class="card-tools">
                        <a href="./?p=posts/manage_post&id=<?= $id ?>" class="btn btn-sm btn-flat bg-gradient-primary btn-primary"><i class="fa fa-edit"></i> Edit Post</a>
                        <button type="button" id="delete_post" class="btn btn-sm btn-flat bg-gradient-danger btn-danger"><i class="fa fa-trash"></i> Delete</button>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <div class="contrain-fluid">
                    <?php if($_settings->userdata('id') == $user_id): ?>
                    <div class="mb-2 text-right">
                        <?php if($status == 1): ?>
                            <small class="badge badge-light border text-dark rounded-pill px-3"><i class="fa fa-circle text-primary"></i> Published</small>
                        <?php else: ?>
                            <small class="badge badge-light border text-dark rounded-pill px-3"><i class="fa fa-circle text-secondary"></i> Unpublished</small>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <div style="line-height:1em" class="mb-3">
                        <h2 class="font-weight-bold mb-0 border-bottom"><?= $title ?></h2>
                        <div class="py-1">
                            <small class="badge badge-light border text-dark rounded-pill px-3 me-2"><i class="far fa-circle"></i> <?= $category ?></small>
                            <span class="me-2"><img src="<?= validate_image($avatar) ?>" alt="" class="img-thumbnail border border-dark post-user rounded-circle p-0"></span>
                            <span class=""><?= $secret_code ?></span>
                        </div>
                    </div>
                    <div>
                        <?= $content ?>
                    </div>

                    <?php if(!empty($file_path)):
                        $post_file_extension = pathinfo($file_path, PATHINFO_EXTENSION);
                        $post_file_url = base_url . $file_path;
                        $post_file_name = basename($file_path);
                    ?>
                        <div class="post-file-preview mt-4 text-center">
                            <h5>Attached File:</h5>
                            <?php if (in_array(strtolower($post_file_extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                <img src="<?= $post_file_url ?>" class="img-fluid rounded border mb-2 file-preview-image" alt="Attached Image for Post" data-file-url="<?= $post_file_url ?>" data-file-type="image" data-file-name="<?= $post_file_name ?>">
                            <?php elseif (strtolower($post_file_extension) == 'pdf'): ?>
                                <i class="fa fa-file-pdf fa-5x text-danger mb-2 file-preview-icon" data-file-url="<?= $post_file_url ?>" data-file-type="pdf" data-file-name="<?= $post_file_name ?>"></i><br>
                                <small class="text-muted"><?= $post_file_name ?></small>
                            <?php else: ?>
                                <i class="fa fa-file fa-5x text-secondary mb-2 file-preview-icon" data-file-url="<?= $post_file_url ?>" data-file-type="other" data-file-name="<?= $post_file_name ?>"></i><br>
                                <small class="text-muted"><?= $post_file_name ?></small>
                            <?php endif; ?>
                            <div class="d-flex justify-content-center mt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary me-2 view-file-btn"
                                        data-file-url="<?= $post_file_url ?>"
                                        data-file-type="<?= in_array(strtolower($post_file_extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : (strtolower($post_file_extension) == 'pdf' ? 'pdf' : 'other') ?>"
                                        data-file-name="<?= $post_file_name ?>">
                                    <i class="fa fa-eye"></i> View
                                </button>
                                <a href="<?= $post_file_url ?>" download class="btn btn-sm btn-outline-success"><i class="fa fa-download"></i> Download</a>
                            </div>
                        </div>
                    <?php endif; ?>
                    <hr class="mx-n3">
                    <h4 class="font-weight-bolder">Comments:</h4>
                    <div class="list-group comment-list mb-3 rounded-0">
                        <?php
                        // Fetch comments including audio_path, video_path, and file_path
                        $comments = $conn->query("SELECT c.*, u.username, u.secret_code, u.avatar FROM `comment_list` c inner join `users` u on c.user_id = u.id where c.post_id ='{$id}' order by abs(unix_timestamp(c.date_created)) asc ");
                        while($row = $comments->fetch_assoc()):
                        ?>
                        <?php if($row['user_id'] == $_settings->userdata('id')): ?>
                        <div class="list-group-item list-group-item-action mb-1 border-top" style="text-align: right;">
                            <a href="javascript:void(0)" class="text-danger text-decoration-none delete-comment" data-id = '<?= $row['id'] ?>'><i class="fa fa-trash"></i></a>
                            <div class="d-flex align-items-center w-100">
                                <div class="col-auto flex-shrink-1 flex-grow-1">
                                    <div style="line-height:1em">
                                        <div class="font-weight-bolder"><?= $row['secret_code'] ?></div>
                                        <div><small class="text-muted"><i><?= date("Y-m-d h:i a", strtotime($row['date_created'])) ?></i></small></div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div><?= html_entity_decode($row['comment']) ?></div>
                            <?php if(!empty($row['audio_path'])): ?>
                                <div class="mt-2">
                                    <audio controls src="<?= base_url . $row['audio_path'] ?>" style="width: 100%;"></audio>
                                </div>
                            <?php endif; ?>
                            <?php if(!empty($row['video_path'])): ?>
                                <div class="mt-2">
                                    <video controls src="<?= base_url . $row['video_path'] ?>" style="width: 100%; max-height: 300px;"></video>
                                </div>
                            <?php endif; ?>
                            <?php if(!empty($row['file_path'])):
                                $file_extension = pathinfo($row['file_path'], PATHINFO_EXTENSION);
                                $file_url = base_url . $row['file_path'];
                                $file_name = basename($row['file_path']);
                            ?>
                                <div class="mt-2 text-center">
                                    <?php if (in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                        <img src="<?= $file_url ?>" class="img-fluid rounded border mb-2 file-preview-image" alt="Attached Image" data-file-url="<?= $file_url ?>" data-file-type="image" data-file-name="<?= $file_name ?>">
                                    <?php elseif (strtolower($file_extension) == 'pdf'): ?>
                                        <i class="fa fa-file-pdf fa-3x text-danger mb-2 file-preview-icon" data-file-url="<?= $file_url ?>" data-file-type="pdf" data-file-name="<?= $file_name ?>"></i><br>
                                        <small class="text-muted"><?= $file_name ?></small>
                                    <?php else: ?>
                                        <i class="fa fa-file fa-3x text-secondary mb-2 file-preview-icon" data-file-url="<?= $file_url ?>" data-file-type="other" data-file-name="<?= $file_name ?>"></i><br>
                                        <small class="text-muted"><?= $file_name ?></small>
                                    <?php endif; ?>

                                    <div class="d-flex justify-content-center mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-2 view-file-btn"
                                                data-file-url="<?= $file_url ?>"
                                                data-file-type="<?= in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : (strtolower($file_extension) == 'pdf' ? 'pdf' : 'other') ?>"
                                                data-file-name="<?= $file_name ?>">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                        <a href="<?= $file_url ?>" download class="btn btn-sm btn-outline-success"><i class="fa fa-download"></i> Download</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php else: /* For other users' comments */ ?>
                        <div class="list-group-item list-group-item-action mb-1 border-top">
                            <div class="d-flex align-items-center w-100">
                                <div class="col-auto">
                                    <img src="<?= validate_image($row['avatar']) ?>" alt="" class="comment-user rounded-circle img-thumbnail p-0 border">
                                </div>
                                <div class="col-auto flex-shrink-1 flex-grow-1">
                                    <div style="line-height:1em">
                                        <div class="font-weight-bolder"><?= $row['secret_code'] ?></div>
                                        <div><small class="text-muted"><i><?= date("Y-m-d h:i a", strtotime($row['date_created'])) ?></i></small></div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div><?= html_entity_decode($row['comment']) ?></div>
                            <?php if(!empty($row['audio_path'])): ?>
                                <div class="mt-2">
                                    <audio controls src="<?= base_url . $row['audio_path'] ?>" style="width: 100%;"></audio>
                                </div>
                            <?php endif; ?>
                            <?php if(!empty($row['video_path'])): ?>
                                <div class="mt-2">
                                    <video controls src="<?= base_url . $row['video_path'] ?>" style="width: 100%; max-height: 300px;"></video>
                                </div>
                            <?php endif; ?>
                            <?php if(!empty($row['file_path'])):
                                $file_extension = pathinfo($row['file_path'], PATHINFO_EXTENSION);
                                $file_url = base_url . $row['file_path'];
                                $file_name = basename($row['file_path']);
                            ?>
                                <div class="mt-2 text-center">
                                    <?php if (in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])): ?>
                                        <img src="<?= $file_url ?>" class="img-fluid rounded border mb-2 file-preview-image" alt="Attached Image" data-file-url="<?= $file_url ?>" data-file-type="image" data-file-name="<?= $file_name ?>">
                                    <?php elseif (strtolower($file_extension) == 'pdf'): ?>
                                        <i class="fa fa-file-pdf fa-3x text-danger mb-2 file-preview-icon" data-file-url="<?= $file_url ?>" data-file-type="pdf" data-file-name="<?= $file_name ?>"></i><br>
                                        <small class="text-muted"><?= $file_name ?></small>
                                    <?php else: ?>
                                        <i class="fa fa-file fa-3x text-secondary mb-2 file-preview-icon" data-file-url="<?= $file_url ?>" data-file-type="other" data-file-name="<?= $file_name ?>"></i><br>
                                        <small class="text-muted"><?= $file_name ?></small>
                                    <?php endif; ?>

                                    <div class="d-flex justify-content-center mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-2 view-file-btn"
                                                data-file-url="<?= $file_url ?>"
                                                data-file-type="<?= in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : (strtolower($file_extension) == 'pdf' ? 'pdf' : 'other') ?>"
                                                data-file-name="<?= $file_name ?>">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                        <a href="<?= $file_url ?>" download class="btn btn-sm btn-outline-success"><i class="fa fa-download"></i> Download</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                        <?php endwhile; ?>
                    </div>
                    <?php if($_settings->userdata('id') == ''): ?>
                        <h5 class="text-center text-muted"><i>Login to Post a Comment</i></h5>
                    <?php else: ?>
                    <div class="card rounded-0 shadow">
                        <div class="card-body">
                            <div class="container-fluid">
                                <form action="" id="comment-form">
                                    <input type="hidden" name="post_id" value="<?= $id ?>">

                                    <div class="mb-3">
                                        <label for="audio-recorder-section">Voice Comment:</label>
                                        <div id="audio-recorder-section">
                                            <button type="button" id="start-recording" class="btn btn-sm btn-primary"><i class="fa fa-microphone"></i> Start Recording</button>
                                            <button type="button" id="stop-recording" class="btn btn-sm btn-danger" disabled><i class="fa fa-stop"></i> Stop Recording</button>
                                            <audio id="audio-player" controls style="display: none; height: 5vh; width: 100%;"></audio>
                                            <input type="file" name="audio" id="audio-file-input" accept="audio/*" style="display:none;">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="video-recorder-section">Video Comment:</label>
                                        <div id="video-recorder-section">
                                            <button type="button" id="start-video-recording" class="btn btn-sm btn-primary"><i class="fa fa-video"></i> Start Recording</button>
                                            <button type="button" id="stop-video-recording" class="btn btn-sm btn-danger" disabled><i class="fa fa-stop"></i> Stop Recording</button>
                                            <video id="recorded-video" controls style="display: none; height: 25vh; width: 100%;"></video>
                                            <input type="file" name="video" id="video-file-input" accept="video/*" style="display:none;">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="file-upload-section">Attach File:</label>
                                        <div id="file-upload-section">
                                            <input type="file" name="uploaded_file" id="uploaded-file-input" accept=".pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx, image/*" class="form-control-file">
                                            <small class="text-muted">Accepted formats: PDF, Word, Excel, PowerPoint, Images</small>
                                        </div>
                                    </div>
                                    <textarea class="form-control form-control-sm rouned-0" name="comment" id="comment" rows="4" placeholder="Write your text comment here"></textarea>
                                </form>
                            </div>
                        </div>
                        <div class="card-footer py-1 text-right">
                            <button class="btn btn-primary btn-flat btn-sm bg-gradient-primary" form="comment-form"><i class="fa fa-save"></i> Save</button>
                            <button class="btn btn-light btn-flat btn-sm bg-gradient-light border" type="reset" form="comment-form">Cancel</button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fileViewerModal" tabindex="-1" aria-labelledby="fileViewerModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fileViewerModalLabel">File Viewer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <a id="modalDownloadBtn" href="#" download class="btn btn-success"><i class="fa fa-download"></i> Download</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Audio Recording
    let audioRecorder;
    let audioChunks = [];
    let audioStream; // To hold the media stream for audio
    const startRecordingButton = document.getElementById('start-recording');
    const stopRecordingButton = document.getElementById('stop-recording');
    const audioPlayer = document.getElementById('audio-player');
    const audioFileInput = document.getElementById('audio-file-input'); // Hidden input for file

    startRecordingButton.addEventListener('click', async () => {
        try {
            audioStream = await navigator.mediaDevices.getUserMedia({ audio: true });
            audioRecorder = new MediaRecorder(audioStream);
            audioChunks = []; // Reset chunks for new recording

            audioRecorder.ondataavailable = event => {
                audioChunks.push(event.data);
            };

            audioRecorder.onstop = () => {
                const audioBlob = new Blob(audioChunks, { type: 'audio/wav' }); // You can choose format, e.g., 'audio/webm'
                const audioUrl = URL.createObjectURL(audioBlob);
                audioPlayer.src = audioUrl;
                audioPlayer.style.display = 'block'; // Show player
                
                // Assign blob to hidden file input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(new File([audioBlob], 'audio_comment.wav', { type: 'audio/wav' }));
                audioFileInput.files = dataTransfer.files;

                // Stop media stream tracks
                if (audioStream) {
                    audioStream.getTracks().forEach(track => track.stop());
                }
            };

            audioRecorder.start();
            stopRecordingButton.disabled = false;
            startRecordingButton.disabled = true;
            audioPlayer.style.display = 'none'; // Hide player during recording
            audioFileInput.value = ''; // Clear previous file selection
        } catch (error) {
            console.error('Error starting audio recording:', error);
            alert('Could not start audio recording. Please check microphone permissions.');
        }
    });

    stopRecordingButton.addEventListener('click', () => {
        if (audioRecorder && audioRecorder.state === 'recording') {
            audioRecorder.stop();
        }
        stopRecordingButton.disabled = true;
        startRecordingButton.disabled = false;
    });

    // Video Recording
    let videoRecorder;
    let videoChunks = [];
    let videoStream; // To hold the media stream for video
    const startVideoRecordingButton = document.getElementById('start-video-recording');
    const stopVideoRecordingButton = document.getElementById('stop-video-recording');
    const recordedVideo = document.getElementById('recorded-video');
    const videoFileInput = document.getElementById('video-file-input'); // Hidden input for file

    startVideoRecordingButton.addEventListener('click', async () => {
        try {
            videoStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
            recordedVideo.srcObject = videoStream; // Show live stream in video element
            recordedVideo.style.display = 'block';
            recordedVideo.play();

            videoRecorder = new MediaRecorder(videoStream);
            videoChunks = []; // Reset chunks for new recording

            videoRecorder.ondataavailable = event => {
                videoChunks.push(event.data);
            };

            videoRecorder.onstop = () => {
                const videoBlob = new Blob(videoChunks, { type: 'video/webm' }); // WebM is common for browser recording
                const videoUrl = URL.createObjectURL(videoBlob);
                recordedVideo.srcObject = null; // Clear live stream
                recordedVideo.src = videoUrl;
                recordedVideo.load(); // Load the recorded video
                
                // Assign blob to hidden file input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(new File([videoBlob], 'video_comment.webm', { type: 'video/webm' }));
                videoFileInput.files = dataTransfer.files;

                // Stop media stream tracks
                if (videoStream) {
                    videoStream.getTracks().forEach(track => track.stop());
                }
            };

            videoRecorder.start();
            stopVideoRecordingButton.disabled = false;
            startVideoRecordingButton.disabled = true;
            videoFileInput.value = ''; // Clear previous file selection
        } catch (error) {
            console.error('Error starting video recording:', error);
            alert('Could not start video recording. Please check camera/microphone permissions.');
        }
    });

    stopVideoRecordingButton.addEventListener('click', () => {
        if (videoRecorder && videoRecorder.state === 'recording') {
            videoRecorder.stop();
        }
        stopVideoRecordingButton.disabled = true;
        startVideoRecordingButton.disabled = false;
    });


    // Existing Comment Form Submission Logic and other functions
    $(function(){
        $('.delete-comment').click(function(){
            _conf("Are your sure to delete this comment?", "delete_comment", [$(this).attr('data-id')])
        })
        $('#delete_post').click(function(){
            _conf("Are your sure to delete this post?", "delete_post", ['<?= isset($id) ? $id : '' ?>'])
        })
        // Summernote for text comments
        $('#comment').summernote({
            height:"15em",
            placeholder:"Write your text comment here", // Updated placeholder
            toolbar: [
                [ 'style', [ 'style' ] ],
                [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
                [ 'fontname', [ 'fontname' ] ],
                [ 'fontsize', [ 'fontsize' ] ],
                [ 'color', [ 'color' ] ],
                [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
                [ 'table', [ 'table' ] ],
                [ 'view', [ 'codeview'] ]
            ]
        })
        $('#comment-form').submit(function(e){
            e.preventDefault();
            var _this = $(this);
            var el = $('<div>');
            el.addClass('alert alert-danger err_msg');
            el.hide();
            $('.err_msg').remove();

            // Check if any content (text, audio, video, or file) is provided
            var textComment = $('#comment').val().trim();
            var audioFile = $('#audio-file-input').prop('files')[0];
            var videoFile = $('#video-file-input').prop('files')[0];
            var uploadedFile = $('#uploaded-file-input').prop('files')[0]; // Check for attached file

            if (textComment === '' && !audioFile && !videoFile && !uploadedFile) {
                el.html('Comment cannot be empty. Please provide text, audio, video, or attach a file.');
                _this.prepend(el);
                el.show('slow');
                $('html, body').scrollTop(_this.offset().top + 15);
                return false;
            }

            start_loader();
            $.ajax({
                url:_base_url_+"classes/Master.php?f=save_comment",
                method:'POST',
                data:new FormData(_this[0]), // Use _this[0] to get the native form element for FormData
                dataType:'json',
                cache:false,
                processData:false, // Required for FormData
                contentType: false, // Required for FormData
                error:err=>{
                    console.log(err)
                    alert('An error occurred')
                    end_loader()
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.reload()
                    }else if(!!resp.msg){
                        el.html(resp.msg)
                        el.show('slow')
                        _this.prepend(el)
                        $('html, body').scrollTop(_this.offset().top + 15)
                    }else{
                        alert('An error occurred')
                        console.log(resp)
                    }
                    end_loader()
                }
            })
        })

        // File Viewer Modal Logic
        $('.view-file-btn, .file-preview-image, .file-preview-icon').on('click', function(){
            var fileUrl = $(this).data('file-url');
            var fileType = $(this).data('file-type');
            var fileName = $(this).data('file-name');
            var modalBody = $('#fileViewerModal .modal-body');
            var modalTitle = $('#fileViewerModalLabel');
            var modalDownloadBtn = $('#modalDownloadBtn');

            modalBody.empty(); // Clear previous content
            modalTitle.text('Viewing: ' + fileName);
            modalDownloadBtn.attr('href', fileUrl);

            if (fileType === 'image') {
                modalBody.append('<img src="' + fileUrl + '" class="img-fluid" alt="' + fileName + '">');
            } else if (fileType === 'pdf') {
                modalBody.append('<iframe src="' + fileUrl + '" allowfullscreen></iframe>');
            } else if (fileType === 'video') {
                modalBody.append('<video controls src="' + fileUrl + '" class="w-100"></video>');
            } else if (fileType === 'audio') {
                modalBody.append('<audio controls src="' + fileUrl + '" class="w-100"></audio>');
            } else {
                modalBody.append('<p class="text-center">Cannot preview this file type directly. Please download to view.</p>');
                modalBody.append('<p class="text-center"><i class="fa fa-file fa-4x text-muted"></i></p>');
            }
            $('#fileViewerModal').modal('show');
        });

        // NEW: Shake effect when clicking outside the modal
        $('#fileViewerModal').on('hide.bs.modal', function(e) {
            // Check if the dismissal was triggered by a click outside or Escape key
            if ($(this).hasClass('shake-active')) {
                // Prevent modal from hiding if shake was just activated
                e.preventDefault();
                // Remove shake class after animation
                $(this).removeClass('shake-active');
                $(this).find('.modal-dialog').removeClass('shake');
            }
        });

        $('#fileViewerModal').on('show.bs.modal', function() {
            // Remove any lingering shake class when modal is shown again
            $(this).find('.modal-dialog').removeClass('shake');
            $(this).removeClass('shake-active');
        });

        $('#fileViewerModal').on('click', function(e) {
            // Check if the click occurred directly on the modal backdrop
            // and not on the modal-content itself
            if ($(e.target).hasClass('modal-dialog')) {
                $(this).find('.modal-dialog').addClass('shake');
                $(this).addClass('shake-active'); // Mark that shake was activated
                setTimeout(() => {
                    $(this).find('.modal-dialog').removeClass('shake');
                }, 500); // Remove the shake class after the animation duration
            }
        });

        // Ensure data-backdrop="static" and data-keyboard="false" are set on the modal HTML.
        // This makes the modal not close on outside click or ESC key press by default.
        // Then, our custom click handler adds the shake effect.
    })

    // Existing delete functions (kept as is)
    function delete_post($id){
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master.php?f=delete_post",
            method:"POST",
            data:{id: $id},
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("An error occured.",'error');
                end_loader();
            },
            success:function(resp){
                if(typeof resp== 'object' && resp.status == 'success'){
                    location.replace('./?p=posts');
                }else{
                    alert_toast("An error occured.",'error');
                    end_loader();
                }
            }
        })
    }
    
    function delete_comment($id){
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master.php?f=delete_comment",
            method:"POST",
            data:{id: $id},
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("An error occured.",'error');
                end_loader();
            },
            success:function(resp){
                if(typeof resp== 'object' && resp.status == 'success'){
                    location.reload();
                }else{
                    alert_toast("An error occured.",'error');
                    end_loader();
                }
            }
        })
    }
</script>