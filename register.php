<?php require_once('./config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php require_once('inc/header.php') ?>
 <script>
    start_loader()
  </script>
  <style>
    html, body{
        width:100%;
        height:100% !important;
    }
    body{
      background-image: url("<?php echo validate_image($_settings->info('cover')) ?>");
      background-size:cover;
      background-repeat:no-repeat;
      backdrop-filter: contrast(1);
      overflow-x:hidden
    }
    #page-title{
      text-shadow: 6px 4px 7px black;
      font-size: 3.5em;
      color: #fff4f4 !important;
      background: #8080801c;
    }
    img#cimg{
      height: 5em;
      width: 5em;
      object-fit: cover;
      border-radius: 100% 100%;
    }
    .strength-weak {
            color: red;
        }
        .strength-medium {
            color: orange;
        }
        .strength-strong {
            color: green;
        }
  </style>
  <?php
      $number1 = rand(100,999);
      $number2 = rand(100,999);
      $code = 'KIUT-'.$number1.'-'.$number2;
      
  ?>
<body class="">
  <div class="d-flex flex-column align-items-center justify-content-center h-100 w-100">
  <h1 class="text-center text-white px-4 py-5" id="page-title"><b><?php echo $_settings->info('name') ?></b></h1>
  <div class="col-lg-6 col-md-8 col-sm-12 col-xs-12">
    <!-- /.login-logo -->
    <div class="card card-navy my-2 rounded-0">
      <div class="card-header rounded-0">
          <h4 class="card-title">Registration</h4>
      </div>
      <div class="card-body rounded-0">
        <form id="register-form" action="" method="post">
            <input type="hidden" name="id">
            <input type="hidden" name="type" value="2">
          <div class="row">
              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                  <div class="form-group">
                      <label for="firstname" class="control-label">First Name</label>
                      <input type="text" class="form-control form-control-sm rounded-0"  name="firstname" id="firstname">
                  </div>
                  
                  <div class="form-group">
                      <label for="lastname" class="control-label">Last Name</label>
                      <input type="text" class="form-control form-control-sm rounded-0"  name="lastname" id="lastname">
                  </div>
                  <div class="form-group">
                      <label for="lastname" class="control-label">Studied Programme Name</label>
                      <input type="text" class="form-control form-control-sm rounded-0" required name="program_name" id="program_name">
                  </div>
                  <div class="form-group">
                    <label for="password" class="control-label">Password</label>
                      <div class="input-group input-group-sm">
                          <input type="password" class="form-control form-control-sm rounded-0"  name="password" id="password" >
                          <button tabindex="-1" class="btn btn-outline-secondary btn-sm rounded-0 pass_view" type="button"><i class="fa fa-eye-slash"></i></button>
                      </div>
                        <div id="strength-message"></div>
                      
                  </div>
                  <div class="form-group">
                      <input type="text" style="display: none;" class="form-control form-control-sm rounded-0" required name="secret_code" value="<?php echo $code;?>" readonly id="secret_code">
                  </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                <div class="form-group">
                      <label for="middlename" class="control-label">Middle Name</label>
                      <input type="text" class="form-control form-control-sm rounded-0" name="middlename" id="middlename">
                  </div>
                  <div class="form-group">
                      <label for="username" class="control-label">Username</label>
                      <input type="text" class="form-control form-control-sm rounded-0" placeholder="eg. KIUT/BCS/2012/2014"  name="username" id="username">
                  </div>
                  
                  <div class="form-group">
                        <label for="description" class="control-label">Appropriate Faculty</label>
                        <select name="faculty_name" id="faculty_name" class="form-control form-control-sm rounded-0" required>
                            <option>Select Appropriate Faculty</option>
                        <?php
                            $conn= mysqli_connect("localhost","root","","academic_collaboration_platform");
                            $select_for = mysqli_query($conn, "SELECT DISTINCT(faculty_name) FROM faculty");
                            if (mysqli_num_rows($select_for) > 0) {
                                while ($row = mysqli_fetch_assoc($select_for)) {
                                    ?>
                                        <option value="<?php echo $row['faculty_name']?>"><?php echo $row['faculty_name']?></option>
                                    <?php
                                }
                            }
                        ?>
                        </select>
                    </div>
                  <div class="form-group">
                      
                      <label for="cpassword" class="control-label">Confirm Password</label>
                      <div class="input-group input-group-sm">
                          <input type="password" class="form-control form-control-sm rounded-0"  id="cpassword">
                          <button tabindex="-1" class="btn btn-outline-secondary btn-sm rounded-0 pass_view" type="button"><i class="fa fa-eye-slash"></i></button>
                      </div>
                  </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                  <div class="form-group">
                  <label for="" class="control-label">Avatar</label>
                  <div class="custom-file">
                    <input type="file" class="custom-file-input rounded-0" id="customFile" name="img" onchange="displayImg(this,$(this))" accept="image/png, image/jpeg">
                    <label class="custom-file-label rounded-0" for="customFile">Choose file</label>
                  </div>
          </div>
              </div>
              <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                  <div class="form-group d-flex justify-content-center">
            <img src="<?php echo validate_image('') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
          </div>
              </div>
          </div>
          <div class="row">
            <div class="col-8">
              <a href="./login.php">Already hava an Account</a>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="submit" id="submit-button" class="btn btn-primary btn-block" disabled>Create Account</button>
            </div>
            <!-- /.col -->
          </div>
        </form>
        <!-- /.social-auth-links -->

        <!-- <p class="mb-1">
          <a href="forgot-password.html">I forgot my password</a>
        </p> -->
        
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->
  </div>
  <!-- jQuery -->
  <script src="<?= base_url ?>plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?= base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?= base_url ?>dist/js/adminlte.min.js"></script>

        
<script>

document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const submitButton = document.getElementById('submit-button');
    const strengthMessage = document.getElementById('strength-message');

    passwordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const strength = checkPasswordStrength(password);

        updateStrengthMessage(strength);
        toggleButtonState(strength);
    });

    function checkPasswordStrength(password) {
        const minLength = 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /[0-9]/.test(password);
        const hasSpecialChars = /[!@#$%^&*(),.?":{}|<>]/.test(password);

        let strength = 'weak';
        if (password.length >= minLength && hasUpperCase && hasLowerCase && hasNumbers && hasSpecialChars) {
            strength = 'strong';
        } else if (password.length >= minLength && (hasUpperCase || hasLowerCase) && (hasNumbers || hasSpecialChars)) {
            strength = 'medium';
        }

        return strength;
    }

    function updateStrengthMessage(strength) {
        strengthMessage.textContent = `Password strength: ${strength}`;
        strengthMessage.className = ''; // Reset the class
        if (strength === 'weak') {
            strengthMessage.classList.add('strength-weak');
        } else if (strength === 'medium') {
            strengthMessage.classList.add('strength-medium');
        } else if (strength === 'strong') {
            strengthMessage.classList.add('strength-strong');
        }
    }

    function toggleButtonState(strength) {
        submitButton.disabled = (strength !== 'strong');
    }
});


</script>
      <script> 

        // function GetDetail(str) { 

        //     if (str.length == 0) { 

        //         document.getElementById("faculty_name").value = ""; 

        //         return; 

        //     } 

        //     else { 

        //         var xmlhttp = new XMLHttpRequest(); 

        //         xmlhttp.onreadystatechange = function () {
        //             if (this.readyState == 4 &&  

        //                     this.status == 200) { 

        //                 var myObj = JSON.parse(this.responseText); 
        //                 document.getElementById( 

        //                     "faculty_name").value = myObj[0]; 

        //             } 

        //         }; 
        //         xmlhttp.open("GET", "gfg.php?program_short_name=" + str, true); 

                  

        //         // Sends the request to the server 

        //         xmlhttp.send(); 

        //     } 

        // } 

    </script> 
<script>
function displayImg(input,_this) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#cimg').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
    }else{
        $('#cimg').attr('src', "<?php echo validate_image('') ?>");
    }
}
  $(document).ready(function(){
    end_loader();
    $('.pass_view').click(function(){
        var input = $(this).siblings('input')
        var type = input.attr('type')
        if(type == 'password'){
            $(this).html('<i class="fa fa-eye"></i>')
            input.attr('type','text').focus()
        }else{
            $(this).html('<i class="fa fa-eye-slash"></i>')
            input.attr('type','password').focus()
        }
    })
    $('#register-form').submit(function(e){
        e.preventDefault()
        var _this = $(this)
        var el = $('<div>')
            el.addClass('alert alert-danger err_msg')
            el.hide()
        $('.err_msg').remove()
        if($('#password').val() != $('#cpassword').val()){
            el.text('Password does not match')
            _this.prepend(el)
            el.show('slow')
            $('html, body').scrollTop(0)
            return false;
        }
        if(_this[0].checkValidity() == false){
            _this[0].reportValidity();
            return false;
        }
        start_loader()
        $.ajax({
            url:_base_url_+"classes/Users.php?f=registration",
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
                  location.href = ('./login.php')
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
</body>
</html>