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
wp_register_style( 'photoldr1-css', plugins_url( '/style(1).css', __FILE__ ) );
wp_enqueue_style( 'photoldr1-css' );
wp_register_style( 'photoldr6-css', plugins_url( '/style(6).css', __FILE__ ) );
wp_enqueue_style( 'photoldr6-css' );

// Create a admin accessible menu
add_action( 'admin_menu', 'photoldr' );

// create a action so that xml can be genrate
add_action('template_redirect','photoldr_check_url');

function photoldr_check_url()  { 
 $pagePath   = $_SERVER["REQUEST_URI"]; 
 if(strchr($pagePath,'photoldrstructure.xml')) {    
  // Generate the XML file 
   generateXML();
 }
 if(strchr($pagePath,'photoldr.php')) {    
  photoldr_postdata();
 }
 
 return; 
}

function photoldr() {
	add_options_page( 'Photoldr', 'PhotoLDR', 'manage_options', 'photoldr', 'photoldrs' );
}

// To be called when Menu is clicked. Main processing function that saves and retrieves form values
function photoldrs() {
    
	// Check the permission level
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
        
        $post_types=get_post_types();
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
            
            
            // add all the information in database 
            foreach ($post_types as $nodeweight){ 
                
            $title = array("photoldr_node_weight_".$nodeweight,"photoldr_node_view_".$nodeweight,"photoldr_item_max_".$nodeweight,"photoldr_item_age_max_".$nodeweight,
                           "photoldr_item_order_".$nodeweight,"photoldr_item_orderby_".$nodeweight);
            
            $nodeweightfor  = $_POST["photoldr_node_weight_".$nodeweight];
            $nodeview       = $_POST["photoldr_node_view_".$nodeweight];
            $nodeitem_max   = $_POST["photoldr_item_max_".$nodeweight];
            $nodeage_max    = $_POST["photoldr_item_age_max_".$nodeweight];
            $nodeitem_order = $_POST["photoldr_item_order_".$nodeweight];
            $nodeorderby    = $_POST["photoldr_item_orderby_".$nodeweight];
            
            $weight = array($nodeweightfor,$nodeview,$nodeitem_max,$nodeage_max,$nodeitem_order,$nodeorderby);
            
                for($k=0;$k<count($title);$k++)
                 {
                    add_option($title[$k], $weight[$k]);

                    update_option($title[$k], $weight[$k]);        
                 }
             
            }
            
            
            
            
            $name = array('FQDN', 'Site Name', 'Expiration Date', 'Unpublished Content', 'Node Types', 'Image style', 'Icon style', 'Type of item to post as default');
            $value = array($fqdn, $site_name, $exp_date, $unpublished, $type, $image_style, $icon_style, $post_type);
                for($i=0;$i<count($name);$i++)
				  {
					  // Add the Option first time around	
					  add_option($name[$i], $value[$i]);
					  // Update the Options table
					  update_option($name[$i], $value[$i]);
				  }                                                 
						 
	  } 
          
?>
<form name="form1" action="" method="post">
<div style="float: left; margin-left: 51px;width: 95%;">

    <div class="form-item">
        <h1>PhotoLDR</h1>
        <h2 class="xml"><a href="<?php bloginfo('url'); ?>/photoldrstructure.xml" target="_blank">PhotoLDR XML Data</a></h2>
	 <label class="labels">FQDN </label>
 <input type="text" maxlength="128" size="30" value="<?php echo get_option('FQDN');?>" name="FQDN" id="fdqn" />
<div>Fully Qualified Domain Name of this web site.  Ex. www.Example.com</div>
</div>
   
    <div class="form-item">
  <label class="labels">Site Name </label>
 <input type="text" class="sitename_photo" value="<?php echo get_option('Site Name');?>" name="site_name" id="site-name" />
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
    
    <div style="clear: both;">
        &nbsp;
    </div> 
</div>   
    
   
    
    
    <div style="float: left; margin-left: 51px;width: 900px;">
    <hr/>
    <h3>Save Settings after changing any above options to refresh the available options bellow.</h3>
    <input type="submit" id="edit-submit" name="submit" value="Save and Refresh" class="form-submit" />
    <hr/>
  
 <?php foreach ($post_types as $post_type){?>
 <div class="form-item form-type-select form-item-photoldr-node-weight-article">
        
 <label for="edit-photoldr-node-weight-article"><?php echo $post_type ?> - Weight</label>
 <select id="edit-photoldr-node-weight-<?php echo $post_type ?>" name="photoldr_node_weight_<?php echo $post_type ?>" class="form-select">
      <?php for($pp=1; $pp<=50;$pp++) { ?>
          <option value="<?php echo $pp;?>" <?php if(get_option("photoldr_node_weight_".$post_type) && get_option("photoldr_node_weight_".$post_type)== $pp ) { ?> selected="selected"  <?php  } else{ if(get_option("photoldr_node_weight_".$post_type)=='') { if($pp==10) { ?> selected="selected" <?php } }} ?> ><?php echo $pp;?> </option>     
      <?php } ?>
 </select>    

<div class="description">This will set the order that node types are presented to the iOS app.</div>
</div>

<?php } foreach ($post_types as $post_type){?>
<table>
    <tr>
            <td class="table_photo_lod">
                <div class="form-item form-type-select form-item-photoldr-node-view-article photo_lod_div">
                      <label for="edit-photoldr-node-view-article"><?php echo $post_type ?> - View Type </label>
                      <select id="edit-photoldr-node-view-<?php echo $post_type ?>" name="photoldr_node_view_<?php echo $post_type ?>" class="form-select">
                          <option value="table" selected="selected">Table</option>
                          <option value="map" <?php if(get_option("photoldr_node_view_".$post_type) && get_option("photoldr_node_view_".$post_type) == 'Map -coming soon') {?> selected="selected" <?php }?> >Map -coming soon</option></select>
                      <div class="description">This will set the view type in the iOS app. CURRENTLY Disabled.  Only Tableview is used no matter what is set.</div>
               </div>
           </td>
           <td class="table_photo_lod">
               <div class="form-item form-type-select form-item-photoldr-item-max-article photo_lod_div">
                  <label for="edit-photoldr-item-max-article"><?php echo $post_type ?> - Max items </label>

                 <select id="edit-photoldr-item-max-<?php echo $post_type ?>" name="photoldr_item_max_<?php echo $post_type ?>" class="form-select">
                     <?php for($pp=1; $pp<=100;$pp++) { ?>                     
                     <option value="<?php echo $pp;?>" <?php if(get_option("photoldr_item_max_".$post_type)== $pp ) { ?> selected="selected"  <?php  } else{ if(get_option("photoldr_item_max_".$post_type)=='') {if($pp==15) { ?> selected="selected" <?php } } }?> ><?php echo $pp;?> </option>
                     <?php } ?>
                 </select>

                <div class="description">This will set the maximum number of items presented to the user.</div>
             </div>
             <div class="form-item form-type-select form-item-photoldr-item-age-max-article photo_lod_div">
                 <label for="edit-photoldr-item-age-max-article"><?php echo $post_type ?> - Max age (weeks) </label>
                 <select id="edit-photoldr-item-age-max-<?php echo $post_type ?>" name="photoldr_item_age_max_<?php echo $post_type ?>" class="form-select">
                 <?php for($pp=0; $pp<=52;$pp++) { ?>                     
                     <option value="<?php echo $pp;?>" <?php if(get_option("photoldr_item_age_max_".$post_type) && get_option("photoldr_item_age_max_".$post_type)== $pp ) { ?> selected="selected"  <?php  } else{  if(get_option("photoldr_item_age_max_".$post_type)=='') { if($pp==0) { ?> selected="selected" <?php } } }?> ><?php echo $pp;?> </option>     
                     <?php } ?>    
                 </select>

                <div class="description">This will set the maximum allowable age in weeks of the displayed items.  0 is no max.</div>
             </div>
          </td>
          <td class="table_photo_lod">
             <div class="form-item form-type-select form-item-photoldr-item-order-article photo_lod_div">
                <label for="edit-photoldr-item-order-article"><?php echo $post_type ?> - Sort Order </label>
                <select id="edit-photoldr-item-order-<?php echo $post_type ?>" name="photoldr_item_order_<?php echo $post_type ?>" class="form-select">
                    <option value="DESC" <?php if(get_option("photoldr_item_order_".$post_type) && get_option("photoldr_item_order_".$post_type) == 'DESC') {?> selected="selected" <?php }?>  >Descending Z-A or Newest First</option>
                    <option value="ASC"  <?php if(get_option("photoldr_item_order_".$post_type) && get_option("photoldr_item_order_".$post_type) == 'ASC') {?>  selected="selected"  <?php } elseif(get_option("photoldr_item_order_".$post_type) == '') { ?>   selected="selected" <?php } ?> >Ascending A-Z or Oldest First</option></select>
                <div class="description">This will set the order which items are returned to the App.</div>
            </div>
            <div class="form-item form-type-select form-item-photoldr-item-orderby-article photo_lod_div">
              <label for="edit-photoldr-item-orderby-article"><?php echo $post_type ?> - Sort By </label>
              <select id="edit-photoldr-item-orderby-<?php echo $post_type ?>" name="photoldr_item_orderby_<?php echo $post_type ?>" class="form-select">
                  <option value="created" <?php if(get_option("photoldr_item_orderby_".$post_type) && get_option("photoldr_item_orderby_".$post_type) == 'created') {?>  selected="selected" <?php }?>  >Created</option>
                  <option value="changed" <?php if(get_option("photoldr_item_orderby_".$post_type) && get_option("photoldr_item_orderby_".$post_type) == 'changed') {?>  selected="selected" <?php }?>  >Changed</option>
                  <option value="title"   <?php if(get_option("photoldr_item_orderby_".$post_type) && get_option("photoldr_item_orderby_".$post_type) == 'title') {?>    selected="selected" <?php }  elseif(get_option("photoldr_item_orderby_".$post_type) == '') { ?> selected="selected"  <?php } ?> >Title</option>
                  <option value="nid"     <?php if(get_option("photoldr_item_orderby_".$post_type) && get_option("photoldr_item_orderby_".$post_type) == 'nid') {?>      selected="selected" <?php }?>  >Node ID</option>
              </select>
              <div class="description">This will set the field used to sort.</div>
           </div>
         </td>
    </tr> 
</table>
    
<?php } ?>
    
    <input type="submit" id="edit-submit" name="submit" value="Save configuration" class="form-submit" />
    </div>
</form>
<?php
}

function generateXML() { 
  
   
  // Authenticate the user. & getting uid
    
   $uid = photoldr_user_auth();
     
  // Get all the post type os the site               
  $post_types = get_post_types();  
  
  // Count the total no. of user of site
  $totaluser  = get_users(); 
  $coutuser   = count($totaluser);
  
  // options of xml  
  $exp_date = get_option('Expiration Date', date("Y-m-d", strtotime('+3 year')));
  
  $nodes = array('FQDN','site_name','published','cms','standalone','date','exp_date','exp_url','post_url');
  
  $pub['FQDN']       = get_option('FQDN');
  $pub['site_name']  = get_option('Site Name');
  $pub['published']  = "1";
  $pub['cms']        = "wordpress";
  $pub['standalone'] = "1";
  $pub['date']       = date("Y-m-d H:i");
  $pub['exp_date']   = date("Y-m-d", strtotime($exp_date));
  $pub['exp_url']    = get_option('FQDN', $_SERVER['HTTP_HOST']);
  $pub['post_url']   = "http://" . $pub['FQDN'] . "/?q=photoldr.php";
  
  // <app_options> exposes site specific options for user settings
  // in the iOS app.
  $pub['app_options'][1] = "app:Username:username:textfield:";
  $pub['app_options'][2] = "app:Password:password:password:";
  $pub['app_options'][3] = "app:Publish:status:checkbox:";
  
  
	
        header('Content-type: text/xml');
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
                 
                 // start app_options loop
                 echo"<app_options>\n";
                     for($l=1;$l<=count($pub['app_options']);$l++)
                     {                     
                         echo "<option>";
                         echo $pub['app_options'][$l];
                         echo "</option>";
                     }
                 echo"</app_options>";
                 
                 // start form_items loop
                 echo"<form_items>\n";  
                 foreach($post_types as $type)
                     {
                        $useredit = check_role($uid,$type);                        
                       if($useredit)
                       {
                           echo "<option>";echo $type.':Hidden:posturl:'.$pub['post_url'];echo "</option>\n";
                       }
                       echo "<option>";echo $type.':'.'Title'.':'.'title'.':'.'textfield'.':'.'#required';echo "</option>";
                       echo "<option>";echo $type.':'.'Body'.':'.'body'.':'.'textarea';echo "</option>";                       
                     }
                 echo"</form_items>";
                 
                  // start option loop
                 $countnode = 0;
               echo"<node_types>\n";  
                    foreach($post_types as $type)
                     {                     
                         echo "<option>";
                         echo $type .":table" ;
                         echo "</option>";
                         
                         $querytype = array(
                            'post_type' => $type,                
                            'post_status' => array('publish','trash') ,
                        );
                         
                         $allpost    = new WP_Query($querytype);                        
                         $countnode += count($allpost->posts);
                     }
                    
               echo"</node_types>";
               echo"<name/>";
               echo"<email/>";
               
               
                // start user count loop
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
               
               
                // start nodes loop
               
               echo"<nodes  count='$countnode'>\n"; 
               
               $newarray= array();
               $K = 0;
               
               foreach ($post_types as $nodeweight){               
                $newarray[$K]['post'] =$nodeweight;
                $newarray[$K]['weight'] =get_option("photoldr_node_weight_".$nodeweight); 
                $K++;
                }
               $post_sort =  array_sort($newarray, 'weight', $order=SORT_DESC);
               
               foreach ($post_sort as $key=>$value)
               {    
                    $posttype       = $value['post'];
                    
                    $nodeview       = get_option("photoldr_node_view_".$posttype);
                    $nodeitem_max   = get_option("photoldr_item_max_".$posttype);
                    $nodeage_max    = get_option("photoldr_item_age_max_".$posttype);
                    $nodeitem_order = get_option("photoldr_item_order_".$posttype);
                    $nodeorderby    = get_option("photoldr_item_orderby_".$posttype);
                    
                    $querytype = array(
                        'post_type' => $posttype,                
                        'post_status' => array('publish','trash') ,
                        'posts_per_page' => $nodeitem_max,                
                        'orderby' => $nodeorderby,
                        'order' => $nodeitem_order,
                        
                    );
                    
                    $allpost = new WP_Query($querytype);                    
                    $postw   = $allpost->posts;
                    foreach ($postw as $key)
                      {
                           $arg     = array('post_id' =>"$key->ID",'orderby' => 'id','order' => 'DESC');
                           $comment = get_comments($arg);
                           
                       if($key->post_status=='trash')
                       {
                            echo "<unpublished_nodes>\n";
                       }
                       else
                       {
                         echo "<node>\n";
                       }
                         $useredit = check_role($uid,$posttype);   
                         if($useredit)
                         {
                           echo "<userdelete>";
                           echo "1";
                           echo "</userdelete>";
                           
                           echo "<useredit>";
                           echo "1";
                           echo "</useredit>";
                         } 
                           echo "<vid>";
                           echo "0";
                           echo "</vid>";
                           
                           echo "<uid>";
                           echo $key->post_author;
                           echo "</uid>";
                           
                           echo "<title>";
                           echo htmlspecialchars($key->post_title);
                           echo "</title>";
                           
                           echo "<log/>";
                       
                           echo "<status>";
                       if($key->post_status=='trash')
                       {
                           echo '0';
                       }
                       else{
                           echo '1';
                       }
                           echo "</status>";
                           
                           echo "<comment>";
                           echo $key->comment_count;
                           echo "</comment>";
                           
                           echo "<promote>";
                           echo "0";
                           echo "</promote>";
                           
                           echo "<sticky>";
                           echo "0";
                           echo "</sticky>";
                           
                           echo "<nid>";
                           echo $key->ID;
                           echo "</nid>";
                           
                           echo "<type>";
                           echo $key->post_type;
                           echo "</type>";
                           
                           echo "<language/>";
                           
                           echo "<created>";
                           echo $key->post_date;
                           echo "</created>";
                           
                           echo "<changed>";
                           echo $key->post_modified;
                           echo "</changed>";
                           
                           echo "<tnid>";
                           echo "0";
                           echo "</tnid>";
                           
                           echo "<translate>";
                           echo "0";
                           echo "</translate>";
                           
                           echo "<revision_timestamp>";
                           echo "0";
                           echo "</revision_timestamp>";
                           
                           echo "<revision_uid>";
                           echo "0";
                           echo "</revision_uid>";
                           
                           echo "<body>";
                           echo htmlspecialchars($key->post_content);
                           echo "</body>";
                           
                           echo "<cid>";
                           echo $comment[0]->comment_ID;
                           echo "</cid>";
                           
                           echo "<last_comment_timestamp>";
                           echo $comment[0]->comment_date;
                           echo "</last_comment_timestamp>";
                           
                           echo "<last_comment_name/>";
                           
                           echo "<last_comment_uid>";
                           echo $comment[0]->user_id;
                           echo "</last_comment_uid>";
                           
                           echo "<comment_count>";
                           echo $key->comment_count;
                           echo "</comment_count>";
                           
                           echo "<name>";
                           echo $comment[0]->comment_author;
                           echo "</name>";
                           
                           echo "<picture>";
                           echo "0";
                           echo "</picture>";
                        
                      if($key->post_status=='trash')
                       {
                            echo "</unpublished_nodes>\n";
                       }
                       else
                       {
                         echo "</node>\n";
                       }
                         
                   }
               }                   
               echo"</nodes>";
	echo '</domain>';
	echo '</domains>';
        exit;     
        
}

// function to sort the weight array

function array_sort($array, $on, $order=SORT_ASC)
    {
                // To sort an array two empty array
                $new_array = array();
                $sortable_array = array();

                if (count($array) > 0) {
                    // treversing the array 
                    foreach ($array as $k => $v) {
                        if (is_array($v)) {
                            foreach ($v as $k2 => $v2) {
                                if ($k2 == $on) {
                                    $sortable_array[$k] = $v2;
                                }
                            }
                        } else {
                            $sortable_array[$k] = $v;
                        }
                    }
                    // Switch according to order ASC and DESC 
                    switch ($order) {
                        case SORT_ASC:
                            asort($sortable_array);
                        break;
                        case SORT_DESC:
                            arsort($sortable_array);
                        break;
                    }

                    foreach ($sortable_array as $k => $v) {
                        $new_array[$k] = $array[$k];
                    }
                }
                // return sort array
                return $new_array;
 }
            
            
 // To check the role with access
 function check_role($uid=null,$type=null)
 {
     
     $user = new WP_User($uid);
     
     // Getting the role of the user
     $caps  = get_user_meta($uid, 'wp_capabilities', true);
     $roles = array_keys((array)$caps);
     $roles = $roles[0]; 
     
     //access of the page
     $access = 'edit_' . $type.'s';

     // switch according to role with the access
     switch ($roles)
      {
       case 'subscriber ':   
         $usercreate = FALSE;
         break;
       case 'contributor':
          if($user->has_cap($access)) 
          {
              $usercreate = TRUE;
          }
          else
          {
              $usercreate = FALSE;
          }
         break;
       case 'author':
         if($user->has_cap($access)) 
          {
              $usercreate = TRUE;
          }
          else
          {
              $usercreate = FALSE;
          }
         break;
       case 'editor':
         if($user->has_cap($access)) 
          {
              $usercreate = TRUE;
          }
          else
          {
              $usercreate = FALSE;
          }
         break;
       case 'administrator': 
         if($user->has_cap($access)) 
          {
              $usercreate = TRUE;
          }
          else
          {
              $usercreate = FALSE;
          }          
         break;
     }
     return $usercreate;
  }
  
  function photoldr_postdata()
  { 
      global $language;
      $langcode = isset($langcode) ? $langcode : $language->language;
      $restrictedfields = array('pass', 'data', 'rdf_mapping', 'roles');

      // Give the user feedback.  Accumulate througouht process.
      $feed_user = "";

      // Decide on Get or Post  Post preffered.
      if (isset($_POST['username'])) {
        $data = $_POST;
      }
      elseif (isset($_GET['username'])) {
        $data = $_GET;
      }
      else {
        return;
      }
      
      $default_type = 'post';
      $ntype        = isset($data['type']) ? $data['type'] : $default_type;
      
      $uid = photoldr_user_auth();
      
      if ($uid == FALSE) {    
        return;
      }
      else { 
        $account    = get_userdata($uid);
        $capability = $account->allcaps;
        
       
        
       $access = 'edit_'.$ntype.'s';

        if (array_key_exists($access, $capability)) {
          $usercreate  = TRUE;
          $usereditany = TRUE;
        }
        else {
          $usercreate  = FALSE;
          $usereditany = FALSE;
        } 

        $access = 'delete_' . $ntype .'s';
        if (array_key_exists($access, $capability)) {
          $userdeleteany = TRUE;
        }
        else {
          $userdeleteany = FALSE;
        }

        if (!($usercreate)) {
              echo 'access_denied';
              print "<!doctype html><html><head><meta charset=\"UTF-8\"><title>Access Denied</title></head><body>Access Denied</body></html>";
              return;
            }            
       }
       
       $type = get_post_types();  
       
       if (!isset($type[$ntype]))
          {      
              echo "$ntype is not a valid node type on this site.<br /> ".$_SERVER['REMOTE_HOST']."<br/>".$_SERVER["REMOTE_ADDR"];    
              return;
          }
      
      // Since we have a valid node type, decide if we are
      // modifying or creating a node.
      $nid = (isset($data['nid'])) ? $data['nid'] : 'new';
      
      // Load existing node by node id.
      if ($nid != 'new') {

        $node = get_post($nid);
        if (!$node) {
          // Could not load the nid, create a new node.
          $nid = 'new';
        }
        elseif ($node->post_type != $ntype) {
          // Node types do not match, so do not mangle
          // the node, create a new node.
          $nid = 'new';
        }

        if (($node->ID != $uid) && ($usereditany == FALSE)) {
          // Only the owner of the node can modify it.
          echo 'access_denied';
          print "<!doctype html><html><head><meta charset=\"UTF-8\"><title>Access Denied</title></head><body>Access Denied</body></html>";
          return;
        }
      }
      
      // Create blank node object in memory.
      if ($nid == 'new') {
        // Setup the node structure.
        $node = new stdClass();

        $node->nid = NULL;

        // Insert new data.
        $node->post_type = $ntype;
        //node_object_prepare($node);
        $node->post_author = $uid;
        //$node->name = $account->data->user_nicename;
        $node->post_status = 'publish';
        $node->post_date_gmt = date("Y-m-d H:i");
      }
      
      //echo "<pre>";print_r($data);exit;
      // Loop through $data array and fill in the node values.
      foreach ($data as $k => $v) {
        if (is_array($v)) {
          // If $v is an array, make it a string.
          $v = implode('; ', $v);
        }

        switch($k) {      

          case 'title':
            $node->post_title = ($v) ? ($v) : $node->post_title;
            break;

          case 'body':
            $node->post_content = isset($v) ? $v : $node->post_content;
            break;

            // END switch case.
        }
        // END foreach $data.
      }

      wp_insert_post($node);
      
      
      
  }
  
  
  function photoldr_user_auth() {
  $uid = FALSE;
  if (isset($_POST['username'])) {
    $data = $_POST;
  }
  elseif (isset($_GET['username'])) {
    $data = $_GET;
  }
  else
  {
       $uid = get_current_user_id();
  }

  // Authenticate the user.
  if (isset($data['username']) && isset($data['password'])) {
    $usern = $data['username'];
    $passw = $data['password'];
    $data  = wp_authenticate($usern, $passw);
    $uid   = $data->ID;
  }
  else {
    $temp =  'No DATA moved from Post or Get.';
  }
  
  return $uid;
}
  

?>