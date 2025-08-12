<div id="carouselExampleControls"  class="carousel slide " data-ride="carousel">
                    <div class="carousel-inner h-150"  >
                        <?php 
                            $upload_path = "uploads/banner";
                            if(is_dir(base_app.$upload_path)): 
                            $file= scandir(base_app.$upload_path);
                            $_i = 0;
                                foreach($file as $img):
                                    if(in_array($img,array('.','..')))
                                        continue;
                            $_i++;
                                
                        ?>
                        <div  class="carousel-item h-10 <?php echo $_i == 1 ? "active" : '' ?>">
                            <img height="250%" src="<?php echo validate_image($upload_path.'/'.$img) ?>"  class="d-block w-100" alt="<?php echo $img ?>" >
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-target="#carouselExampleControls" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-target="#carouselExampleControls" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                    </div>
<?php if($row['user_id'] == $student['id']): ?>
                                                    <div class="list-group-item list-group-item-action mb-1 border-top" style="text-align: right;">
                                                    <a href="javascript:void(0)" class="text-danger text-decoration-none delete-comment" data-id = '<?= $row['id'] ?>'><i class="fa fa-trash"></i></a>
                                                <div class="d-flex align-items-center w-100">
                                                    <div class="col-auto flex-shrink-1 flex-grow-1" >
                                                        <div style="line-height:1em;" >
                                                            <div class="font-weight-bolder"><?= $row['username'] . ' ( '. $row['role_as'] . ')'?></div>
                                                            <div><small class="text-muted"><i><?= date("Y-m-d h:i a", strtotime($row['date_created'])) ?></i></small></div>
                                                        </div>
                                                    </div>
                                                    
                                                        
                                                        </div>
                                                <hr>
                                                <div><?= $row['comment'] ?></div>
                                                </div>
                                                <?php endif; ?>
                                                <?php if($row['user_id'] != $student['id']): ?>
                                                    <div class="list-group-item list-group-item-action mb-1 border-top" >
                                                <div class="d-flex align-items-center w-100">
                                                    <div class="col-auto">
                                                        <img src="images/logo/logo.png" width="45" alt="" class="comment-user rounded-circle img-thumbnail p-0 border">
                                                    </div>
                                                    <div class="col-auto flex-shrink-1 flex-grow-1" >
                                                        <div style="line-height:1em;" >
                                                            <div class="font-weight-bolder"><?= $row['username'] . ' ( '. $row['role_as'] . ')'?></div>
                                                            <div><small class="text-muted"><i><?= date("Y-m-d h:i a", strtotime($row['date_created'])) ?></i></small></div>
                                                        </div>
                                                    </div>
                                                        </div>
                                                        <hr>
                                                <div><?= $row['comment'] ?></div>
                                                </div>
                                                <?php endif; ?>