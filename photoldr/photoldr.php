<?php
/*
Plugin Name:Photoldr
Version: 1.3
Author: 
Description: PhotoLDR makes it possible to easily add photos and content to Wordpress from an iOS device.
*/

// Add style sheet
wp_register_style( 'photoldr-css', plugins_url( '/style.css', __FILE__ ) );
wp_enqueue_style( 'photoldr-css' );	

// Include the recaptcha library
include('recaptchalib.php');

// Create a admin accessible menu
add_action( 'admin_menu', 'photoldr' );


function photoldr() {
	add_options_page( 'Photoldr', 'PhotoLDR', 'manage_options', 'photoldr', 'photoldrs' );
}

// To be called when Menu is clicked. Main processing function that saves and retrieves form values
function photoldrs() {
    
	// Check the permission level
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
    // Once the form is submitted
        if(isset($_POST['submit']))
        {
			// Receive the form values assuming user has filled all of them
            $fqdn		= $_POST['FQDN']; 
            $site_name	= $_POST['site_name']; 
            $exp_date	= $_POST['exp_date']; 
            $unpublished= $_POST['unpublished']; 
            $types		= $_POST['types'];
            $type		= implode(",", $types);
            $image_style= $_POST['image_style']; 
            $icon_style = $_POST['icon_style']; 
            $post_type  = $_POST['post_type'];
           
		    // Validate recaptcha
            $resp = recaptcha_check_answer ('6LcJrNsSAAAAABZyDYlQ6HyDjLBSYZTi6_lljltj',
                                        $_SERVER["REMOTE_ADDR"],
                                        $_POST["recaptcha_challenge_field"],
                                        $_POST["recaptcha_response_field"]);
			 
			if (!$resp->is_valid) {
				// What happens when the CAPTCHA was entered incorrectly
				$error = "The reCAPTCHA wasn't entered correctly.";
			  }
			else  { 
				$name = array('FQDN', 'Site Name', 'Expiration Date', 'Unpublished Content', 'Node Types', 'Image style', 'Icon style', 'Type of item to post as default');
				$value = array($fqdn, $site_name, $exp_date, $unpublished, $type, $image_style, $icon_style, $post_type);
                for($i=0;$i<count($name);$i++)
				  {
					  // Add the Option first time around	
					  add_option($name[$i], $value[$i]);
					  // Update the Options table
					  update_option($name[$i], $value[$i]);
				  }
				 $error = '';
			 }
		}
        
		if( $error != '' || (!isset($_POST['submit']) ) ) { 
		
		if($error != '') echo '<span style="color:crimson;">'.$error. '</span>';
?>
<form name="form1" action="" method="post">
    <div class="form-item">
        <h1>PhotoLDR</h1>
	 <label class="labels">FQDN </label>
 <input type="text" maxlength="128" size="30" value="<?php echo get_option('FQDN');?>" name="FQDN" id="fdqn" />
<div>Fully Qualified Domain Name of this web site.  Ex. www.Example.com</div>
</div>
   
    <div class="form-item">
  <label class="labels">Site Name </label>
 <input type="text" maxlength="128" size="30" value="<?php echo get_option('Site Name');?>" name="site_name" id="site-name" />
<div>Site name.  Remember this will be displayed on iPhone and iPad, so best to keep this short. (v2)</div>
</div>
    
    <div class="form-item">
  <label class="labels">Expiration Date </label>
 <input type="text" maxlength="128" size="30" value="<?php echo get_option('Expiration Date');?>" name="exp_date" id="exp-date" />
<div>Enter a relative string like +3 months, +90 days, +1 year, or a static date as YYYY-MM-DD. <br> Expiration Date for data that is cached in the iOS app.  An expired domains data is removed from the app.  Useful if you are migrating to a new site, or shutting down a site, or have data that is time sensative. The app should attemt to refresh the data every day, but if the data is not refreshed for (+3 months, +90 days, +1 year) then the data is cleared from the app.</div>
</div>
    
    <div class="form-item">
  <label class="labels">Unpublished Content </label>
 <select class="form-select" name="unpublished" id="unpublished"><option value="yes">Yes</option><option value="no">No</option></select>
<div>Allow content editors with permission to work with unpublished content. (v2)</div>
</div>
    
    <div class="form-item">
  <label class="labels">Node Types </label>
 <select size="2" id="types" name="types[]" multiple="multiple"><option selected="selected" value="article">Posts</option><option value="page">Pages</option><option value="plugins">Plugins</option><option selected="selected" value="blog">Blog</option><option value="event">Event</option></select>
<div>Select the types of nodes used by PhotoLDR. CTRL-click to select multiple.</div>
</div>
    
    <div class="form-item">
  <label class="labels">Image style </label>
 <select name="image_style" id="image-style"><option value="thumbnail">thumbnail</option><option value="medium">medium</option><option value="large">large</option></select>
<div>Select the style of images displayed by PhotoLDR.</div>
</div>
    
   <div class="form-item">
  <label class="labels">Icon style </label>
 <select name="icon_style" id="icon-style"><option value="thumbnail">thumbnail</option><option value="medium">medium</option><option value="large">large</option></select>
<div>Select the image style of icon displayed by PhotoLDR.</div>
</div>
    
    <div class="form-item">
  <label class="labels">Type of item to post as default </label>
 <select name="post_type" id="post-type"><option value="posts">Posts</option><option value="pages">Pages</option><option value="blog">Blog</option><option value="book">Book page</option><option value="event">Event</option><option value="plugins">Plugins</option><option value="tools">Tools</option></select>
<div>Select the types of nodes to use PhotoLDR.  Revisit this page after setting this for more settings.</div>
</div>
    
    <div class="recaptcha">
         <div style="display: none;">The reCAPTCHA wasn't entered correctly.</div>
       <?php echo recaptcha_get_html('6LcJrNsSAAAAACMcOqVOpDgUGP51qopiEeNJvehv',null,false); 
       ?>
       
        </div>
    
    <div class="form-item">

<input  type="submit" name="submit" value="Save" style="width:120px;" />
    </div>
     
</form>
<?php
} else {
			echo "<h1> Data Saved Successfully </h1>";
}
}
?>