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
            $fqdn       = $_POST['FQDN']; 
            $site_name	= $_POST['site_name']; 
            $exp_date	= $_POST['exp_date']; 
            $unpublished= $_POST['unpublished']; 
            $types      = $_POST['types'];
            $type       = implode(",", $types);
            $image_style= $_POST['image_style']; 
            $icon_style = $_POST['icon_style']; 
            $post_type  = $_POST['post_type'];
            		
            $name = array('FQDN', 'Site Name', 'Expiration Date', 'Unpublished Content', 'Node Types', 'Image style', 'Icon style', 'Type of item to post as default');
            $value = array($fqdn, $site_name, $exp_date, $unpublished, $type, $image_style, $icon_style, $post_type);
                for($i=0;$i<count($name);$i++)
				  {
					  // Add the Option first time around	
					  add_option($name[$i], $value[$i]);
					  // Update the Options table
					  update_option($name[$i], $value[$i]);
				  }

			// Generate the XML file 

			generateXML();
				 
	  }
			$post_types=get_post_types();

             
?>

<form name="form1" action="" method="post">
    <div class="form-item">
        <h1>PhotoLDR</h1>
        <h2 class="xml"><a href="<?php bloginfo('url'); ?>/wp-content/xmlfiles/photoldrstructure.xml" target="_blank">PhotoLDR XML Data</a></h2>
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
   
 <select class="form-select" name="unpublished" id="unpublished">
     <option  <?php if(get_option('Unpublished Content') && get_option('Unpublished Content')=='yes'){ ?> value="<?php echo get_option('Unpublished Content'); ?>" selected=selected" <?php } else { ?> value="yes" <?php } ?> >Yes</option>
     <option  <?php if(get_option('Unpublished Content') && get_option('Unpublished Content')=='no'){ ?> value="<?php echo get_option('Unpublished Content'); ?>" selected=selected" <?php } else { ?> value="no" <?php } ?> >No</option>
 </select>
<div>Allow content editors with permission to work with unpublished content. (v2)</div>
</div>
    
    <div class="form-item">
  <label class="labels">Node Types </label>
  <?php $options=explode(",",get_option('Node Types')); ?>
 <select size="2" id="types" name="types[]" multiple="multiple">
     
     <?php foreach ($post_types as $post_type){?>
     <option value="<?php echo $post_type?>" <?php if(in_array($post_type,$options)){ echo "selected='selected'";}?>><?php echo $post_type ?></option>
        
       <?php }?>
</select>
<div>Select the types of nodes used by PhotoLDR. CTRL-click to select multiple.</div>
</div>
    
    <div class="form-item">
  <label class="labels">Image style </label>
 <select name="image_style" id="image-style">
     <option  <?php if(get_option('Image style') && get_option('Image style')=='thumbnail'){ ?> value="<?php echo get_option('Image style'); ?>" selected=selected" <?php } else { ?> value="thumbnail" <?php } ?> >thumbnail</option>
     <option  <?php if(get_option('Image style') && get_option('Image style')=='medium'){ ?> value="<?php echo get_option('Image style'); ?>" selected=selected" <?php } else { ?> value="medium" <?php } ?> >medium</option>
     <option  <?php if(get_option('Image style') && get_option('Image style')=='large'){ ?> value="<?php echo get_option('Image style'); ?>" selected=selected" <?php } else { ?> value="large" <?php } ?> >large</option>
 </select>
<div>Select the style of images displayed by PhotoLDR.</div>
</div>
    
   <div class="form-item">
  <label class="labels">Icon style </label>
 <select name="icon_style" id="icon-style">
     <option  <?php if(get_option('Icon style') && get_option('Icon style')=='thumbnail'){ ?> value="<?php echo get_option('Icon style'); ?>" selected=selected" <?php } else { ?> value="thumbnail" <?php } ?> >thumbnail</option>
     <option  <?php if(get_option('Icon style') && get_option('Icon style')=='medium'){ ?> value="<?php echo get_option('Icon style'); ?>" selected=selected" <?php } else { ?> value="medium" <?php } ?> >medium</option>
     <option  <?php if(get_option('Icon style') && get_option('Icon style')=='large'){ ?> value="<?php echo get_option('Icon style'); ?>" selected=selected" <?php } else { ?> value="large" <?php } ?> >large</option>
 </select>
<div>Select the image style of icon displayed by PhotoLDR.</div>
</div>
    
    <div class="form-item">
  <label class="labels">Type of item to post as default </label>
 <select name="post_type" id="post-type">
     
     <option  <?php if(get_option('Type of item to post as default') && get_option('Type of item to post as default')=='posts'){ ?> value="<?php echo get_option('Type of item to post as default'); ?>" selected=selected" <?php } else { ?> value="posts" <?php } ?> >Posts</option>
     <option  <?php if(get_option('Type of item to post as default') && get_option('Type of item to post as default')=='pages'){ ?> value="<?php echo get_option('Type of item to post as default'); ?>" selected=selected" <?php } else { ?> value="pages" <?php } ?> >Pages</option>
     <option  <?php if(get_option('Type of item to post as default') && get_option('Type of item to post as default')=='blog'){ ?> value="<?php echo get_option('Type of item to post as default'); ?>" selected=selected" <?php } else { ?> value="blog" <?php } ?> >Blog</option>
     <option  <?php if(get_option('Type of item to post as default') && get_option('Type of item to post as default')=='book'){ ?> value="<?php echo get_option('Type of item to post as default'); ?>" selected=selected" <?php } else { ?> value="book" <?php } ?> >Book Page</option>
     <option  <?php if(get_option('Type of item to post as default') && get_option('Type of item to post as default')=='event'){ ?> value="<?php echo get_option('Type of item to post as default'); ?>" selected=selected" <?php } else { ?> value="event" <?php } ?> >Event</option>
     <option  <?php if(get_option('Type of item to post as default') && get_option('Type of item to post as default')=='Plugins'){ ?> value="<?php echo get_option('Type of item to post as default'); ?>" selected=selected" <?php } else { ?> value="Plugins" <?php } ?> >Plugins</option>
     <option  <?php if(get_option('Type of item to post as default') && get_option('Type of item to post as default')=='tools'){ ?> value="<?php echo get_option('Type of item to post as default'); ?>" selected=selected" <?php } else { ?> value="tools" <?php } ?> >Tools</option>
 </select>
<div>Select the types of nodes to use PhotoLDR.  Revisit this page after setting this for more settings.</div>
</div>
    
    <div class="form-item">
    <input  type="submit" name="submit" value="Save" style="width:120px;" />
    </div>
     
</form>
<?php
}

function generateXML() { 
    
  $post_types = get_post_types();  
  $totaluser  = get_users(); 
  $coutuser   = count($totaluser);
  $allpost    = get_posts();
  $countpost  = count($allpost);
  $allpages   = get_pages();
  $countpages = count($allpages);
  
  $coutnode   = $countpost+$countpages;
  
  $exp_date = get_option('Expiration Date', date("Y-m-d", strtotime('+3 year')));
  
  $nodes = array('FQDN','site_name','published','cms','standalone','date','exp_date','exp_url','post_url');
  
  $pub['FQDN'] = get_option('FQDN');
  $pub['site_name'] = get_option('Site Name');
  $pub['published'] = "1";
  $pub['cms'] = "word press";
  $pub['standalone'] = "1";
  $pub['date'] = date("Y-m-d H:i");
  $pub['exp_date'] = date("Y-m-d", strtotime($exp_date));
  $pub['exp_url'] = get_option('FQDN', $_SERVER['HTTP_HOST']);
  $pub['post_url'] = "http://" . $pub['FQDN'] . "/?q=photoldr.php";
  
  // <app_options> exposes site specific options for user settings
  // in the iOS app.
  $pub['app_options'][1] = "app:Username:username:textfield:";
  $pub['app_options'][2] = "app:Password:password:password:";
  $pub['app_options'][3] = "app:Publish:status:checkbox:";
  
  
  
	//MAKE xmlfiles DIRECTORY IF IT DOESN'T EXIST
	if (!is_dir(WP_CONTENT_DIR . "/xmlfiles/")) {
	   mkdir(WP_CONTENT_DIR . "/xmlfiles/");
	} 
	//START BUFFER
	ob_start();
	echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        echo "<domains src='photoldr_node_structure_page'>\n"; 
	echo "<domain>\n"; 
		// start wordpress loop
		for($k=0;$k<count($nodes);$k++)
                 {
		
			echo "<$nodes[$k]>";
			echo $pub[$nodes[$k]];
			echo "</$nodes[$k]>";                        
                 }
                 
                 echo"<app_options>\n";
                     for($l=1;$l<=count($pub['app_options']);$l++)
                     {                     
                         echo "<option>";
                         echo $pub['app_options'][$l];
                         echo "</option>";
                     }
                 echo"</app_options>";
                 
                 echo"<form_items>\n";                 
                   echo "<option>";echo 'article'.':'.'Title'.':'.'title'.':'.'textfield';echo "</option>";
                   echo "<option>";echo 'article'.':'.'Body'.':'.'body'.':'.'textarea';echo "</option>";
                   echo "<option>";echo 'article'.':'.'Image'.':'.'field_image'.':'.'image';echo "</option>";
                   echo "<option>";echo 'article'.':'.'Replace Photos'.':'.'image_overwrite'.':'.'checkbox';echo "</option>";
                   echo "<option>";echo 'page'.':'.'Title'.':'.'title'.':'.'textfield';echo "</option>";
                   echo "<option>";echo 'page'.':'.'Body'.':'.'body'.':'.'textarea';echo "</option>";
                   echo "<option>";echo 'blog'.':'.'Title'.':'.'title'.':'.'textfield';echo "</option>";
                   echo "<option>";echo 'blog'.':'.'Body'.':'.'body'.':'.'textarea';echo "</option>";                     
                 echo"</form_items>";
                 
               echo"<node_types>\n";  
                    foreach($post_types as $type)
                     {                     
                         echo "<option>";
                         echo $type .":table" ;
                         echo "</option>";
                     }
               echo"</node_types>";
               echo"<name/>";
               echo"<email/>";
               
               echo"<user count='$coutuser'>\n";  
                   for($k=0;$k<$coutuser;$k++)
                   {
                       echo "<uid>";
                       echo $totaluser[$k]->ID ;
                       echo "</uid>";
                       echo "<name>";
                       echo $totaluser[$k]->user_nicename;
                       echo "</name>";
                       echo "<mail>";
                       echo $totaluser[$k]->user_email;
                       echo "</mail>";
                       echo "<created>";
                       echo $totaluser[$k]->user_registered;
                       echo "</created>";
                       echo "<status>";
                       echo $totaluser[$k]->user_status;
                       echo "</status>";
                   }
               echo"</user>";
               
               echo"<nodes  count='$coutnode'>\n"; 
                   for($k=0;$k<$countpost;$k++)
                   {
                         echo "<node>\n";
                           echo "<vid>";
                           echo $allpost[$k]->ID;
                           echo "</vid>";
                           echo "<uid>";
                           echo $allpost[$k]->post_author;
                           echo "</uid>";
                           echo "<title>";
                           echo $allpost[$k]->post_title;
                           echo "</title>";
                           echo "<status>";
                           echo $allpost[$k]->post_status;
                           echo "</status>";
                           echo "<comment>";
                           echo $allpost[$k]->comment_count;
                           echo "</comment>";
                           echo "<nid>";
                           echo $allpost[$k]->ID;
                           echo "</nid>";
                           echo "<type>";
                           echo $allpost[$k]->post_type;
                           echo "</type>";
                           echo "<created>";
                           echo $allpost[$k]->post_date;
                           echo "</created>";
                           echo "<changed>";
                           echo $allpost[$k]->post_modified;
                           echo "</changed>";
                           echo "<body>";
                           echo $allpost[$k]->post_content;
                           echo "</body>";
                         echo "</node>";
                   }
                   
                  for($k=0;$k<$countpages;$k++)
                   {
                        echo "<node>\n";
                           echo "<vid>";
                           echo $allpages[$k]->ID;
                           echo "</vid>";
                           echo "<uid>";
                           echo $allpages[$k]->post_author;
                           echo "</uid>";
                           echo "<title>";
                           echo $allpages[$k]->post_title;
                           echo "</title>";
                           echo "<status>";
                           echo $allpages[$k]->post_status;
                           echo "</status>";
                           echo "<comment>";
                           echo $allpages[$k]->comment_count;
                           echo "</comment>";
                           echo "<nid>";
                           echo $allpages[$k]->ID;
                           echo "</nid>";
                           echo "<type>";
                           echo $allpages[$k]->post_type;
                           echo "</type>";
                           echo "<created>";
                           echo $allpages[$k]->post_date;
                           echo "</created>";
                           echo "<changed>";
                           echo $allpages[$k]->post_modified;
                           echo "</changed>";
                           echo "<body>";
                           echo $allpages[$k]->post_title;
                           echo "</body>";
                        echo "</node>";
                   }
               echo"</nodes>";
	echo '</domain>';
	echo '</domains>';
        
	$page = ob_get_contents();
	// EXPORT THE BUFFER AS A FILE WITH AN XML EXTENSION
	$fp = fopen(WP_CONTENT_DIR . "/xmlfiles/photoldrstructure.xml","w");
	fwrite($fp,$page);
	// CLEAN BUFFER SO XML IT WON'T PRINT ON POST PAGE
	ob_end_clean();
}

?>