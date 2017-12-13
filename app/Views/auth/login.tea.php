<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php print isset($title) ? $title : "Tea Messenger"; ?></title>
    <link rel="shortcut icon" href="{{ asset('assets/img/logo-ice-tea.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/css/fontawesome.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/login.css') }}">
</head>
<body>
<div class="container">
		<div class="row">
            <div class="col-sm-6 col-md-4 col-md-offset-4">
               <div class="login-wall">
				<h2 class="text-center login-tile">Tea Messenger</h2>
                  <div class="col-md-12">
                     <img class="profile-img" src="<?php print asset("assets/img/logo-ice-tea.png"); ?>" alt="">
                     <p class="text-center ice-tea">Sign in</p>
                  </div>
                  <form method="post" action="javascript:void(0);" id="form-login" class="form-horizontal form-signin">
			         <div class="form-group">
				        <input type="text" name="username" id="username" class="form-control" placeholder="Username">
			         </div>
			         <div class="form-group">
				        <input type="password" name="username" id="password" class="form-control" placeholder="Password">
			         </div>
			         <div class="form-group">
				        <div id="csrf_field">
				        </div>
				        <input type="submit" name="submit" value="Sign In" class="btn btn-lg btn-primary btn-block">
			         </div>
                     <p class="text-center">
			            <a href="/forgot-password">Forgot Password</a>
						<br>
						<span>
							Need an account? <a href="/register">Sign up.</a>
						</span>
                     </p>
		          </form>
               </div>
            </div>
		</div>
   </div>
<script type="text/javascript">
</script>
</body>
</html>