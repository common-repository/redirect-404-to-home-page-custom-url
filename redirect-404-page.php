<?php
/*
Plugin Name: Redirect 404 to Home Page - Custom URL
Description: This Wordpress Plugin fixes 404 Errors in Google Webmasters by Redirecting all 404 URLs to Home Page or a Custom URL.   
Version: 1.0
Author: Daniel Bolander
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if(!class_exists('RDT_Custom_Page'))
{
  class RDT_Custom_Page{
    
    public function __construct()
    {
      if(is_admin())
      {
        add_action('admin_menu',array($this,'rdtcp_setting_page'));
		add_filter('plugin_action_links', array($this,'rdctp__settings_link'), 10, 2);
      }
      add_action( 'template_redirect', array($this,'rdctp_template_redirect'));       
    }
    
    public function rdctp_template_redirect()
    {
      if(is_404())
      {
        $rdtcp_enable=get_option('rdtcp_enable');
        $rdtcp_redirect_type=get_option('rdtcp_redirect_type');
        $redirect_url=get_option('rdtcp_custom_page');
        if($rdtcp_enable=="on" && !empty($redirect_url))
        {
          $redirect_url=get_option('rdtcp_custom_page');
          wp_redirect($redirect_url,$rdtcp_redirect_type);
          die;
        }
      } 
    
    }
	
	
function rdctp__settings_link($links, $file) {
  
    if (plugin_basename(__FILE__) == $file) {
       
        $settings_link = '<a href="' . admin_url('admin.php?page=rdtcp_custom_page') . '">Settings</a>';
        array_push($links, $settings_link);
    }
    return $links;
}

	
	public static function rdt_active()
	{
		$home_page=get_site_url();
		update_option('rdtcp_custom_page',$home_page);
        update_option('rdtcp_enable','on');
        update_option('rdtcp_redirect_type','301');
	}
    
    public function rdtcp_setting_page()
    {
    
      add_options_page('Redirect 404','404s Redirect','manage_options','rdtcp_custom_page',array($this,'rdtcp_custom_page'));
    
    }
    
    public function rdtcp_custom_page()
    {
    
      $notification_html="";
      if(isset($_POST['submit']))
      {
        if ( !wp_verify_nonce( $_POST['rdtcp_nonce'], 'rdtcp_nonce_action' ) ) {
            die("wp noonce not matched!!!");
        }
        
        if ( ! current_user_can( 'manage_options') ) {
            die("You have not access to save this options");
          
        } 
        
        $rdtcp_enable=sanitize_text_field($_POST['rdtcp_enable']);
        $rdtcp_custom_page=sanitize_text_field($_POST['rdtcp_custom_page']);
        $rdtcp_redirect_type=sanitize_text_field($_POST['rdtcp_redirect_type']);
        
        if(!(strstr($rdtcp_custom_page,"http://") || strstr($rdtcp_custom_page,"https://")))
				$rdtcp_custom_page="http://".$rdtcp_custom_page;
		
        update_option('rdtcp_custom_page',$rdtcp_custom_page);
        update_option('rdtcp_enable',$rdtcp_enable);
        update_option('rdtcp_redirect_type',$rdtcp_redirect_type);
        
        $notification_html='<div id="message" class="updated fade" style="margin:0px 20px 10px 2px;"><p><strong>Options Saved </strong></p></div>';
        
      }
      
      
      $rdtcp_enable=get_option('rdtcp_enable');
      $rdtcp_custom_page=get_option('rdtcp_custom_page');
      $rdtcp_redirect_type=get_option('rdtcp_redirect_type');
      
      ?>
        
                  <div  id="poststuff">
        <?php echo $notification_html; ?>
        <div class="postbox">
        <h3 class="hndle">
        <span>Redirect all 404 URLs to Custom URL</span>
        </h3>
        <div class="inside">
          
          
        <div>
        <form action="" method="post">
        <?php wp_nonce_field('rdtcp_nonce_action', 'rdtcp_nonce'); ?>
          








<table style=" padding-bottom: 100px; ">


<tr>  
   <div class="col-4 form-group option_container">
     
     <td class="option_section selected_item">

 
         <label class="tag-title">Redirection [Enable/Disable]:</label>
        
         <p>Disabling this will Stop 404 Redirect</p>


</td>

<td>
   <ul class="tg-list" style="list-style: none;">
            <li class="tg-list-item">
               <input style="display: none;" class="tgl tgl-flip" id="cb5"  type="checkbox" name="rdtcp_enable" <?php if($rdtcp_enable=="on") { echo "checked"; }?>/>
               <label class="tgl-btn" data-tg-off="Redirect Disabled" data-tg-on="Redirect Enabled!" for="cb5"></label>
            </li>
         </ul>

       </td>

      </div>
 


</tr>    
   
 
 <tr>   
   <div style="max-width: 100%; flex: 0 0 75%;" class="col-4 form-group option_container">
      <td class="option_section selected_item">
         <label class="tag-title">Redirect to:</label>


                  <p>404 Pages will be redirected to this URL</p>


       </td><td>
         
         <div  class="tg-list-item tg-list" style="   position: relative; ">
         



         <select style=" width: 12em; " onchange="changeRedirectURL(event)">
            <option value="">--Select--</option>
            <option value="<?php echo get_site_url();?>">Home Page</option>
            <?php
               $pages = get_pages( 'post_status=publish' );
               foreach( $pages as $page ) {
                
               $permalink = get_permalink( $page->ID );
                echo '<option value="'.$permalink.'">'.$page->post_title.'</option>';
               
               }
               ?>
            <option value="<?php echo get_site_url();?>" selected>Custom Redirect URL</option>
         </select> 
         
         
         
          
   
          
    <div class="floating-label">      
      <input class="floating-input" type="text"  name="rdtcp_custom_page" id="rdtcp_custom_page" value="<?php echo $rdtcp_custom_page;?>"  class="effect-16" type="text"  size="50">
      <span class="border"></span>
      <label>404s will redirect to below URL <span class="dashicons dashicons-arrow-down-alt"></span></label>
    </div>
 
         
         
         
          
      
        
        
        
         
        </div>

      </td>
   </div>
  
        
   </tr>     
       
     
<tr> 

         <div class="col-4 form-group option_container" style="max-width: 100%;flex: 0 0 63%;">
      <td class="option_section selected_item">
      <label class="tag-title">Redirect Type:</label>
     

        <p>301 Redirect is Recommended for SEO</p>



      </td><td>
     <div  class="tg-list-item tg-list redirect-type-radio" style=" font-weight: normal !important;   ">
     
 
   <div class="radio">
    <input type="radio" name="rdtcp_redirect_type" id="301" value="301" <?php if($rdtcp_redirect_type=="301"){ echo "checked"; } ?>>
    <label for="301">
      <div class="checker"></div>
      301 - Moved Permanently
    </label>
  </div>  
      
  
      
  <div class="radio">
    <input type="radio" name="rdtcp_redirect_type" id="302" value="302" <?php if($rdtcp_redirect_type=="302"){ echo "checked"; } ?>>
    <label for="302">
      <div class="checker"></div>
      302 - Moved Temporarily
    </label>
  </div>  
  
  
  
     </div>
    
       
      </td>
   </div>
   
    </tr>   
    <tr>  
   
   <td colspan="2">
   
          
   <input type="submit" name="submit" style="width: 100%;height: 35px;font-size: 130%;font-weight: 500;" class="button-success" value="Update Settings" /> 
   </td>
   </tr>
   
   
<table>
   </form>
</div>








</div></div></div>





<script>
// JavaScript for label effects only
  $(window).load(function(){
    $(".col-3 input").val("");
    
    $(".input-effect input").focusout(function(){
      if($(this).val() != ""){
        $(this).addClass("has-content");
      }else{
        $(this).removeClass("has-content");
      }
    })
  });
    
    </script>
        <style>
      
          
      
.row {
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    margin-right: -15px;
    margin-left: -15px;
}

.col-form-label {
    padding-top: calc(0.375rem + 1px);
    padding-bottom: calc(0.375rem + 1px);
    margin-bottom: 0;
    font-size: inherit;
    line-height: 1.5;
}

.form-label {
    font-size: 14px;
}

.form-label-inner {
    padding-left: 10px;
}

.text-right {
    text-align: right ;
}

.tg-list{float:left}

.col-4 {
    -ms-flex: 0 0 40%;
    flex: 0 0 40%;
    max-width: 40%;
    
        margin-left: 25px;
}

.redirect-type-radio label{font-weight:500 !important}
.option_section {
    background-color: transparent;
    border: 1px dashed transparent;
    border-radius: 4px;
    padding: 8px 10px;

}


.option_container p {
    font-weight: 200;
    font-style: italic;
}


.option_section label {
    background-color: transparent;
    width: 100%;
}
.selected_item label {
    font-weight: bold;
        color: black;
}


.tg-list {
  text-align: center;
  display: flex;
  align-items: center;
}

.tg-list-item {
  margin: 0 2em;
}




.tgl {
  display: none;
}
.tgl, .tgl:after, .tgl:before, .tgl *, .tgl *:after, .tgl *:before, .tgl + .tgl-btn {
  box-sizing: border-box;
}
.tgl::-moz-selection, .tgl:after::-moz-selection, .tgl:before::-moz-selection, .tgl *::-moz-selection, .tgl *:after::-moz-selection, .tgl *:before::-moz-selection, .tgl + .tgl-btn::-moz-selection {
  background: none;
}
.tgl::selection, .tgl:after::selection, .tgl:before::selection, .tgl *::selection, .tgl *:after::selection, .tgl *:before::selection, .tgl + .tgl-btn::selection {
  background: none;
}
.tgl + .tgl-btn {
  outline: 0;
  display: block;
  width: 10em;
  height: 2em;
  position: relative;
  cursor: pointer;
  -webkit-user-select: none;
     -moz-user-select: none;
      -ms-user-select: none;
          user-select: none;
}
.tgl + .tgl-btn:after, .tgl + .tgl-btn:before {
  position: relative;
  display: block;
  content: "";
  width: 50%;
  height: 100%;
}
.tgl + .tgl-btn:after {
  left: 0;
}
.tgl + .tgl-btn:before {
  display: none;
}
.tgl:checked + .tgl-btn:after {
  left: 50%;
}


 

.radio {
    margin-right: 15px;
}




.radio input {
  position: absolute;
  pointer-events: none;
  visibility: hidden;
}
.radio input:focus + label {
  background: #eeeeff;
}
.radio input:focus + label .checker {
  border-color: #4CAF50;
}
.radio input:checked + label .checker {
  box-shadow: inset 0 0 0 6px #4CAF50;
}
.radio label {
  display: flex;
  align-items: center;
  height: 28px;
  border-radius: 14px;
  margin: 10px;
  padding: 0 8px 0 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}
.radio label:hover {
 /* background: #eeeeff; */
 background: #ddf3de;
}
.radio label:hover .checker {
  box-shadow: inset 0 0 0 2px #4CAF50;
}
.radio .checker {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  margin-right: 8px;
  box-shadow: inset 0 0 0 2px #ccc;
  transition: box-shadow 0.3s ease;
}










 
 
 

.button-success {
    background: #4CAF50;
    border-color: #028607 #10a516 #10a516;
    box-shadow: 0 1px 0 #10a516;
    color: #fff;
    text-decoration: none;
    text-shadow: 0 -1px 1px #10a516, 1px 0 1px #10a516, 0 1px 1px #10a516, -1px 0 1px #10a516;
    cursor: pointer;

}

.button-success:hover {
    background: #1ba720;
    border-color: #60ab09;
    color: #fff;
}



 
 






.floating-input ~ .border {
 position: absolute;
 bottom: 0;
 left: 0;
 width: 0;
 height: 2px;
 background-color: #27ad8a;
}

.floating-input:focus ~ .border {
 width: 100%;
 transition: 0.5s;
}



.option_section p {
    font-weight: 200;
    font-style: italic;
}





 
/****  floating-Lable style start ****/
.floating-label { 
  position:relative;      margin-left: 10px;
 
}
.floating-input , .floating-select {
  font-size:14px;
  padding:4px 4px;
  display:block;
  width:100%;
  height:30px;
  background-color: transparent;
  border:none;
  border-bottom:1px solid #757575;
  
   border: 0 !important;
    padding: 4px 0;
    border-bottom: 1px solid #ccc !important;
    background-color: transparent !important;
    box-shadow: none !important;
    
}

.floating-input:focus , .floating-select:focus {
     outline:none;
     border-bottom:2px solid #4CAF50; 
}

.floating-label label {
  color:#999; 
  font-size:14px;
  font-weight:normal;
  position:absolute;
  pointer-events:none;
  left:0px;
  top:5px;
  transition:0.2s ease all; 
  -moz-transition:0.2s ease all; 
  -webkit-transition:0.2s ease all; 
    font-weight: 400;
    color: #4caf50;
    text-align: left;
}

.floating-input:focus ~ label, .floating-input:not(:placeholder-shown) ~ label {
  top:-18px;
  font-size:14px;
  color:#4CAF50;
}

.floating-select:focus ~ label , .floating-select:not([value=""]):valid ~ label {
  top:-18px;
  font-size:14px;
  color:#4CAF50;
}

/* active state */
.floating-input:focus ~ .bar:before, .floating-input:focus ~ .bar:after, .floating-select:focus ~ .bar:before, .floating-select:focus ~ .bar:after {
  width:50%;
}

*, *:before, *:after {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
}

.floating-textarea {
   min-height: 30px;
   max-height: 260px; 
   overflow:hidden;
  overflow-x: hidden; 
}
 
/* active state */
.floating-input:focus ~ .highlight , .floating-select:focus ~ .highlight {
  -webkit-animation:inputHighlighter 0.3s ease;
  -moz-animation:inputHighlighter 0.3s ease;
  animation:inputHighlighter 0.3s ease;
}

/* animation */
@-webkit-keyframes inputHighlighter {
  from { background:#4CAF50; }
  to  { width:0; background:transparent; }
}
@-moz-keyframes inputHighlighter {
  from { background:#4CAF50; }
  to  { width:0; background:transparent; }
}
@keyframes inputHighlighter {
  from { background:#4CAF50; }
  to  { width:0; background:transparent; }
}
 




 
 
 
 
 
 
 
 
 






.tgl-flip + .tgl-btn {
  padding: 2px;
  transition: all .2s ease;
  font-family: sans-serif;
  -webkit-perspective: 100px;
          perspective: 100px;
}
.tgl-flip + .tgl-btn:after, .tgl-flip + .tgl-btn:before {
  display: inline-block;
  transition: all .4s ease;
  width: 100%;
  text-align: center;
  position: absolute;
  line-height: 2em;
  font-weight: bold;
  color: #fff;
  position: absolute;
  top: 0;
  left: 0;
  -webkit-backface-visibility: hidden;
          backface-visibility: hidden;
  border-radius: 4px;
}
.tgl-flip + .tgl-btn:after {
  content: attr(data-tg-on);
  background: #02C66F;
  -webkit-transform: rotateY(-180deg);
          transform: rotateY(-180deg);
}
.tgl-flip + .tgl-btn:before {
  background: #FF3A19;
  content: attr(data-tg-off);
}
.tgl-flip + .tgl-btn:active:before {
  -webkit-transform: rotateY(-20deg);
          transform: rotateY(-20deg);
}
.tgl-flip:checked + .tgl-btn:before {
  -webkit-transform: rotateY(180deg);
          transform: rotateY(180deg);
}
.tgl-flip:checked + .tgl-btn:after {
  -webkit-transform: rotateY(0);
          transform: rotateY(0);
  left: 0;
  background: #4CAF50;
}
.tgl-flip:checked + .tgl-btn:active:after {
  -webkit-transform: rotateY(20deg);
          transform: rotateY(20deg);
}
          
      </style>
        <script>
        function changeRedirectURL(e) {
          
           document.getElementById("rdtcp_custom_page").value = e.target.value;
        }
        </script>
      <?php 
    }
  
  
  
  }


  new RDT_Custom_Page;
  register_activation_hook( __FILE__, array( 'RDT_Custom_Page', 'rdt_active' ) );	
}