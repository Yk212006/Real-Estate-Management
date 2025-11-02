<?php
$success = isset($_GET['success']) ? $_GET['success'] : '';
$error = isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from thebootstrapthemes.com/live/thebootstrapthemes-realestate/contact.php by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 11 Apr 2017 02:45:10 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
<title>Contact us - Real Estate Management System</title>
<meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

 	<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css" />
  <link rel="stylesheet" href="assets/style.css"/>
  <script src="assets/jquery-1.9.1.min.js"></script>
	<script src="assets/bootstrap/js/bootstrap.js"></script>
  <script src="assets/script.js"></script>



<!-- Owl stylesheet -->
<link rel="stylesheet" href="assets/owl-carousel/owl.carousel.css">
<link rel="stylesheet" href="assets/owl-carousel/owl.theme.css">
<script src="assets/owl-carousel/owl.carousel.js"></script>
<!-- Owl stylesheet -->


<!-- slitslider -->
    <link rel="stylesheet" type="text/css" href="assets/slitslider/css/style.css" />
    <link rel="stylesheet" type="text/css" href="assets/slitslider/css/custom.css" />
    <script type="text/javascript" src="assets/slitslider/js/modernizr.custom.79639.js"></script>
    <script type="text/javascript" src="assets/slitslider/js/jquery.ba-cond.min.js"></script>
    <script type="text/javascript" src="assets/slitslider/js/jquery.slitslider.js"></script>
<!-- slitslider -->

<script src='assets/google_analytics_auto.js'></script>
<style>
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}
.alert-success {
    color: #3c763d;
    background-color: #dff0d8;
    border-color: #d6e9c6;
}
.alert-danger {
    color: #a94442;
    background-color: #f2dede;
    border-color: #ebccd1;
}
.alert-dismissible {
    padding-right: 35px;
}
.alert-dismissible .close {
    position: relative;
    top: -2px;
    right: -21px;
    color: inherit;
}
</style>
</head>

<body>


<!-- Header Starts -->
<div class="navbar-wrapper">

        <div class="navbar-inverse" style="background-color: #0BE0FD">
          <div class="container">
            <div class="navbar-header">


              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>

            </div>


            <!-- Nav Starts -->
            <div class="navbar-collapse  collapse">
              <ul class="nav navbar-nav navbar-right">
               <li><a href="index.php">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li class="active"><a href="contact.php">Contact</a></li>
              </ul>
            </div>
            <!-- #Nav Ends -->

          </div>
        </div>

    </div>
<!-- #Header Starts -->





<div class="container">

<!-- Header Starts -->
<div class="header">
<!-- <a href="index.php"><img src="images/header.png" alt="Realestate"></a> -->

            <div class="menu">
              <ul class="pull-right">
              	<li><a href="index.php">Home</a></li>
                <li><a href="list-properties.php">Properties</a>
                	 <ul class="dropdown">
                    	<li><a href="sale.php">Properties on Sale</a></li>
                        <li><a href="rent.php">Properties on Rent</a></li>
                    </ul>
                </li>

              </ul>
           </div>
</div>
<!-- #Header Starts -->
</div><!-- banner -->
<div class="inside-banner">
  <div class="container">
    <h2>Contact Us</h2>
</div>
</div>
<!-- banner -->


<div class="container">
<div class="spacer">
    
    <?php if ($success == '1'): ?>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>Success!</strong> Your message has been sent successfully. We'll get back to you soon.
    </div>
    <?php elseif ($error): ?>
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <strong>Error!</strong> <?php echo htmlspecialchars($error); ?>
    </div>
    <?php endif; ?>
    
<div class="row contact">
  <div class="col-lg-6 col-sm-6 ">
        <form action="contact_process.php" method="post" id="contactForm">
            <input type="text" name="fullname" class="form-control" placeholder="Full Name" required>
            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
            <input type="text" name="phone" class="form-control" placeholder="Contact Number">
            <textarea name="message" rows="6" class="form-control" placeholder="Message" required></textarea>
            <button type="submit" class="btn btn-success" name="Submit">Send Message</button>
        </form>
  </div>
  <div class="col-lg-6 col-sm-6 ">
      <div class="well">
        <h4>Contact Information</h4>
        <p><strong>Address:</strong> Brgy. Enclaro, Binalbagan Negros Occidental Philippines</p>
        <p><strong>Email:</strong> contact@vibeproperties.com</p>
        <p><strong>Phone:</strong> +91 1234567890</p>
        <br>
        <p><a href="https://www.google.com/maps/dir/17.5164108,78.3793127/Petro+Bunk,+Khammam,+Telangana+507003" target="_blank" rel="noopener">View on Google Maps →</a></p>
      </div>
  </div>
</div>
</div>
</div>




<div style="background-color: #0BE0FD">

<div class="container">



<div class="row">
            <div class="col-lg-3 col-sm-3">
                   <h4>Information</h4>
                   <ul class="row">
                <li class="col-lg-12 col-sm-12 col-xs-3"><a href="index.php">Home</a></li>
                <li class="col-lg-12 col-sm-12 col-xs-3"><a href="about.html">About</a></li>
                <li class="col-lg-12 col-sm-12 col-xs-3"><a href="contact.php">Contact</a></li>
              </ul>
            </div>

            <div class="col-lg-3 col-sm-3">
                    <h4>Newsletter</h4>
                    <p>Get notified about the latest properties in our marketplace.</p>
                    <div id="newsletter-message"></div>
                    <form class="form-inline" role="form" id="newsletterForm">
                            <input type="email" name="email" id="newsletter-email" placeholder="Enter Your email address" class="form-control" required>
                                <button class="btn btn-success" type="submit">Notify Me!</button></form>
            </div>

            <div class="col-lg-3 col-sm-3">
                    <h4>Follow us</h4>
                    <a href="#"><img src="images/facebook.png" alt="facebook"></a>
                    <a href="#"><img src="images/twitter.png" alt="twitter"></a>
                    <a href="#"><img src="images/linkedin.png" alt="linkedin"></a>
                    <a href="#"><img src="images/instagram.png" alt="instagram"></a>
            </div>

             <div class="col-lg-3 col-sm-3">
                    <h4>Contact us</h4>
                    <p><b>IT SOURCECODE</b><br>
<span class="glyphicon glyphicon-map-marker"></span>Brgy. Enclaro , Binalbagan Negros Occidental Philippines<br>
<span class="glyphicon glyphicon-envelope"></span>www.itsourcecode.com<br>
<span class="glyphicon glyphicon-earphone"></span> +639272777334</p>
            </div>
        </div>
<p class="copyright">Copyright 2021. All rights reserved. </p>


</div></div>




<!-- Modal -->
<div id="loginpop" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="row">
        <div class="col-sm-6 login">
        <h4>Login</h4>
          <form class="" role="form">
        <div class="form-group">
          <label class="sr-only" for="exampleInputEmail2">Email address</label>
          <input type="email" class="form-control" id="exampleInputEmail2" placeholder="Enter email">
        </div>
        <div class="form-group">
          <label class="sr-only" for="exampleInputPassword2">Password</label>
          <input type="password" class="form-control" id="exampleInputPassword2" placeholder="Password">
        </div>
        <div class="checkbox">
          <label>
            <input type="checkbox"> Remember me
          </label>
        </div>
        <button type="submit" class="btn btn-success">Sign in</button>
      </form>
        </div>
        <div class="col-sm-6">
          <h4>New User Sign Up</h4>
          <p>Join today and get updated with all the properties deal happening around.</p>
          <button type="submit" class="btn btn-info"  onclick="window.location.href='register.html'">Join Now</button>
        </div>

      </div>
    </div>
  </div>
</div>
<!-- /.modal -->

<script>
// Newsletter subscription handler
$(document).ready(function() {
    $('#newsletterForm').on('submit', function(e) {
        e.preventDefault();
        
        var email = $('#newsletter-email').val();
        var messageDiv = $('#newsletter-message');
        
        // Clear previous messages
        messageDiv.html('');
        
        // Disable submit button
        var submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Subscribing...');
        
        $.ajax({
            url: 'newsletter_subscribe.php',
            type: 'POST',
            data: { email: email },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    messageDiv.html('<div class="alert alert-success" style="margin-top: 10px; font-size: 12px; padding: 8px;">' + response.message + '</div>');
                    $('#newsletter-email').val(''); // Clear input
                } else {
                    messageDiv.html('<div class="alert alert-danger" style="margin-top: 10px; font-size: 12px; padding: 8px;">' + response.message + '</div>');
                }
                
                // Auto-hide message after 5 seconds
                setTimeout(function() {
                    messageDiv.fadeOut('slow', function() {
                        $(this).html('').show();
                    });
                }, 5000);
            },
            error: function() {
                messageDiv.html('<div class="alert alert-danger" style="margin-top: 10px; font-size: 12px; padding: 8px;">An error occurred. Please try again.</div>');
            },
            complete: function() {
                // Re-enable submit button
                submitBtn.prop('disabled', false).text('Notify Me!');
            }
        });
    });
});
</script>

</body>

<!-- Mirrored from thebootstrapthemes.com/live/thebootstrapthemes-realestate/contact.php by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 11 Apr 2017 02:45:10 GMT -->
</html>
