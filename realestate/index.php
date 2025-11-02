<?php
include_once "connection.php";

$query = "select * from properties";
$result = mysqli_query($con, $query);

if(!$result){
	echo "Error Found!!!";
}
?>

<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from thebootstrapthemes.com/live/thebootstrapthemes-realestate/index.php by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 11 Apr 2017 02:43:16 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
<title>Developed by Yatin Kumar S ... Real Estate Management System</title>
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

<script src='assets/google_analytics_auto.js'></script></head>

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
               <li class="active"><a href="index.php">Home</a></li>
                <li><a href="about.html">About</a></li>
                <li><a href="contact.php">Contact</a></li>
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
<!-- <a href="index.php"><img src="images/header.png" alt=" SLNP Realestate"> --><!-- </a> -->

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
</div>
<div class="">


            <div id="slider" class="sl-slider-wrapper">

        <div class="sl-slider">
        <?php
        // Fetch exactly 5 available properties for the slider (availablility = 0 means available)
        $slider_query = "SELECT * FROM properties WHERE availablility = 0 ORDER BY RAND() LIMIT 5";
        $slider_result = mysqli_query($con, $slider_query);
        
        // Get all available properties
        $properties = [];
        while($prop = mysqli_fetch_assoc($slider_result)) {
            $properties[] = $prop;
        }
        
        $total_properties = count($properties);
        
        // If no available properties, show a message
        if ($total_properties === 0) {
            $properties[] = [
                'property_id' => '#',
                'property_title' => 'No Properties Available',
                'property_address' => 'Please check back later',
                'property_details' => 'We currently have no available properties. Please check back soon for new listings.',
                'price' => 0,
                'property_img' => 'images/properties/default.jpg',
                'bed_room' => 0,
                'liv_room' => 0,
                'parking' => 0
            ];
            $total_properties = 1;
        }
        
        // Expanded slider animation configurations for more variety
        $animations = [
            ['horizontal', -25, -25, 2, 2],    // Slide in from right
            ['vertical', 10, -15, 1.5, 1.5],   // Slide in from bottom
            ['horizontal', 3, 3, 2, 1],        // Zoom in
            ['vertical', -5, 25, 2, 1],        // Slide in from top
            ['horizontal', -5, 10, 2, 1],      // Slide in from left
            ['vertical', 15, -10, 1.8, 1.2],   // Slide in from bottom right
            ['horizontal', -15, 15, 2.2, 1.5], // Diagonal slide
            ['vertical', 5, -20, 1.7, 1.3]     // Slide in from bottom left
        ];
        
        
        $i = 0;
        foreach($properties as $property) {
            $animation = $animations[$i % count($animations)];
            $i++;
            
            // Get the first image if property_img contains multiple images
            $images = explode(',', $property['property_img']);
            $main_image = !empty($images[0]) ? $images[0] : 'images/properties/default.jpg';
            ?>
            <div class="sl-slide" 
                 data-orientation="<?php echo $animation[0]; ?>" 
                 data-slice1-rotation="<?php echo $animation[1]; ?>" 
                 data-slice2-rotation="<?php echo $animation[2]; ?>" 
                 data-slice1-scale="<?php echo $animation[3]; ?>" 
                 data-slice2-scale="<?php echo $animation[4]; ?>">
                <div class="sl-slide-inner">
                    <div class="bg-img" style="background-image: url('<?php echo $main_image; ?>');"></div>
                    <h2><a href="property-detail.php?id=<?php echo $property['property_id']; ?>">
                        <?php echo htmlspecialchars($property['property_title']); ?>
                    </a></h2>
                    <blockquote>
                        <p class="location">
                            <span class="glyphicon glyphicon-map-marker"></span> 
                            <?php echo htmlspecialchars($property['property_address']); ?>
                        </p>
                        <p><?php echo substr(strip_tags($property['property_details']), 0, 150); ?>...</p>
                        <cite>$ <?php echo number_format($property['price']); ?></cite>
                    </blockquote>
                </div>
            </div>
            <?php
        }
        if ($i == 0) {
            // Fallback in case no properties are found
            echo '<div class="sl-slide">
                    <div class="sl-slide-inner">
                        <div class="bg-img bg-img-1"></div>
                        <h2><a href="#">No Properties Available</a></h2>
                        <blockquote>
                            <p>Check back soon for new property listings.</p>
                        </blockquote>
                    </div>
                </div>';
        }
        ?>
        </div><!-- /sl-slider -->



        <nav id="nav-dots" class="nav-dots">
            <?php
            // Generate navigation dots based on actual number of slides
            $dots_to_show = min(8, $total_properties); // Show max 8 dots
            if ($dots_to_show > 0) {
                echo '<span class="nav-dot-current"></span>';
                for ($i = 1; $i < $dots_to_show; $i++) {
                    echo '<span></span>';
                }
            }
            ?>
        </nav>

      </div><!-- /slider-wrapper -->
</div>



<div class="banner-search">
  <div class="container">
    <!-- banner -->
    <h3>Buy, Sale & Rent</h3>
    <div class="searchbar">
      <div class="row">
        <div class="col-lg-6 col-sm-6">
        <form action="search.php" method="post">
          <input name="search" type="text" class="form-control" placeholder="Search of Properties">
          <div class="row">
            <div class="col-lg-3 col-sm-3 ">
              <select name="delivery_type" class="form-control">
                <option value="Rent">Rent</option>
                <option value="Sale">Sale</option>
              </select>
            </div>
            <div class="col-lg-3 col-sm-4">
             <select name="search_price" class="form-control">
                <option>Price</option>
                <option value="1">$5000 - $50,000</option>
                <option value="2">$50,000 - $100,000</option>
                <option value="3">$100,000 - $200,000</option>
                <option value="4">$200,000 - above</option>
              </select>
            </div>
            <div class="col-lg-3 col-sm-4">
           <select name="property_type" class="form-control">
                <option>Property Type</option>
                <option value="Apartment">Apartment</option>
                <option value="Building">Building</option>
                <option value="Office-Space">Office-Space</option>
              </select>
              </div>
              <div class="col-lg-3 col-sm-4">
              <button type="submit" name="submit" class="btn btn-success">Find Now</button>
              </form>
              </div>
          </div>


        </div>
        <div class="col-lg-5 col-lg-offset-1 col-sm-6">
          <p>Get updated with all the latest property deals.</p>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- banner -->
<div class="container">
  <div class="properties-listing spacer"> <a href="list-properties.php" class="pull-right viewall">View All Listing</a>
    <h2>Featured Properties</h2>
    <div id="owl-example" class="owl-carousel">



      <?php
	  	while($property_result = mysqli_fetch_assoc($result)){
			$id = $property_result['property_id'];
			$property_title = $property_result['property_title'];
			$delivery_type = $property_result['delivery_type'];
			$availablility = $property_result['availablility'];
			$price = $property_result['price'];
			$property_img = $property_result['property_img'];
			$bed_room = $property_result['bed_room'];
			$liv_room = $property_result['liv_room'];
			$parking = $property_result['parking'];
			$kitchen = $property_result['kitchen'];
			$utility = $property_result['utility'];

	  ?>
      <div class="properties">
        <div class="image-holder"><img src="<?php echo $property_img; ?>" class="img-responsive" alt="properties">
          <?php if($availablility == 0){ ?><div class="status sold">Available</div> <?php } else { ?>
          <div class="status new">Not Available</div>
          <?php } ?>
        </div>
        <h4><a href="property-detail.php?id=<?php echo $id; ?>"><?php echo $property_title;  ?></a></h4>
        <p class="price">Price: $<?php echo $price; ?></p>
        <p class="price">Delivery Type: <?php echo $delivery_type; ?></p>
        <p class="price">Utilities: <?php echo $utility; ?></p>
        <div class="listing-detail">
        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Bed Room"><?php echo $bed_room; ?></span>
        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Living Room"><?php echo $liv_room; ?></span>
        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Parking"><?php echo $parking; ?></span>
        <span data-toggle="tooltip" data-placement="bottom" data-original-title="Kitchen"><?php echo $kitchen; ?></span>
        </div>
        <a class="btn btn-primary" href="property-detail.php?id=<?php echo $id; ?>">View Details</a>
      </div>

      <?php } ?>

    </div>
  </div>
  <div class="spacer">
    <div class="row">
      <div class="col-lg-12 col-sm-12 recent-view">
        <h3>About Us</h3>
        <p>At SLNP Real Estate, you are number one. Whether you are a property owner, tenant, or buyer, we value your business and will provide you with the individual attention and service you deserve. We believe in a strict Code of Ethics. We believe in integrity, commitment to excellence, a professional attitude, and personalized care.<br><a href="about.html">Learn More</a></p>
         <p>At SLNP Real Estate, you are number one. Whether you are a property owner, tenant, or buyer, we value your business and will provide you with the individual attention and service you deserve. We believe in a strict Code of Ethics. We believe in integrity, commitment to excellence, a professional attitude, and personalized care.<br><a href="about.html">Learn More</a></p>
          <p>At SLNP Real Estate, you are number one. Whether you are a property owner, tenant, or buyer, we value your business and will provide you with the individual attention and service you deserve. We believe in a strict Code of Ethics. We believe in integrity, commitment to excellence, a professional attitude, and personalized care.<br><a href="about.html">Learn More</a></p>

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
                <li class="col-lg-12 col-sm-12 col-xs-3"><a href="contact.html">Contact</a></li>
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
<span class="glyphicon glyphicon-map-marker"></span>contact@vibeproperties.com, Brgy. Enclaro, Binalbagan Negros Occidental Philippines<br>
<span class="glyphicon glyphicon-envelope"></span>www.vibeproperties.com<br>
<span class="glyphicon glyphicon-earphone"></span> +91 1234567890</p>
            </div>
        </div>
<p class="copyright">Copyright 2021. All rights reserved.	</p>


</div></div>





<script>
// Newsletter subscription handler
$(document).ready(function() {
    $('#newsletterForm').on('submit', function(e) {
        e.preventDefault();
        
        var email = $('#newsletter-email').val();
        var messageDiv = $('#newsletter-message');
        
        messageDiv.html('');
        
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
                    $('#newsletter-email').val('');
                } else {
                    messageDiv.html('<div class="alert alert-danger" style="margin-top: 10px; font-size: 12px; padding: 8px;">' + response.message + '</div>');
                }
                
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
                submitBtn.prop('disabled', false).text('Notify Me!');
            }
        });
    });
});
</script>

</body>

<!-- Mirrored from thebootstrapthemes.com/live/thebootstrapthemes-realestate/index.php by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 11 Apr 2017 02:43:16 GMT -->
</html>
