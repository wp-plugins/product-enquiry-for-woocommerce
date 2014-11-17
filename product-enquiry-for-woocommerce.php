<?php
/*
Plugin Name: Product Enquiry for WooCommerce
Description: Allows prospective customers or visitors to make enquiry about a product, right from within the product listing page.
Version: 0.3.3
Author: WisdmLabs
Author URI: http://wisdmlabs.com
License: GPL2
*/

$form_init_data = get_option( 'wdm_form_data');

if(!empty($form_init_data))
{
    if(isset($form_init_data['show_after_summary']))
    {
    if($form_init_data['show_after_summary'] == 1)
    {
	//show ask button after a single product summary
        add_action('woocommerce_after_single_product_summary', 'ask_about_product');
    }
    }
    
    if(isset($form_init_data['show_at_page_end']))
    {
    if($form_init_data['show_at_page_end'] == 1)
    {
	//show ask button at the end of the page of a single product
        add_action('woocommerce_after_single_product', 'ask_about_product');
    }
    }
}
else
{
    //show ask button after a single product summary as default
    add_action('woocommerce_after_single_product_summary', 'ask_about_product');
}

function ask_about_product()
{
    $form_data = get_option( 'wdm_form_data');
    ?>
     <br />
     <!-- Page styles -->
     <?php
            wp_enqueue_style("wdm-contact-css", plugins_url("css/contact.css", __FILE__));
     ?>

    <div id="contact-form">
            <input type="button" name="contact" value="<?php echo empty($form_data['custom_label']) ? 'Make an enquiry for this product' : $form_data['custom_label'];?>" class="contact wpi-button" />
    </div>		
		<!-- preload the images -->
		<div style='display:none'>
			<img src='<?php echo plugins_url("img/contact/loading.gif", __FILE__)?>' alt='' />
		</div>
   
    <!-- Load JavaScript files -->
   <?php
        wp_enqueue_script("wdm-simple-modal", plugins_url("js/jquery.simplemodal.js", __FILE__), array("jquery"));
        wp_enqueue_script("wdm-contact", plugins_url("js/contact.js", __FILE__), array("jquery"));	

		

        
	//pass parameters to contact.php file
        $wdm_translation_array = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),'product_name'=>get_the_title(), 'contact_url' => plugins_url("data/contact.php", __FILE__), 'form_dataset' => $form_data, 'admin_email' => get_option('admin_email'), 'site_name' => get_bloginfo('name'));
        wp_localize_script( 'wdm-contact', 'object_name', $wdm_translation_array );

}

add_action('admin_menu', 'create_ask_product_menu');

function create_ask_product_menu()
{
    //create a submenu under Woocommerce 'Products' menu
    add_submenu_page('edit.php?post_type=product', 'Product Enquiry', 'Product Enquiry', 'manage_options', 'pefw', 'add_ask_product_settings' );
}
add_action('wp_ajax_contact_ajax','contact_status');
add_action('wp_ajax_nopriv_contact_ajax','contact_status');
function contact_status(){
   include("data/contact.php");
   die();
}
add_action('wp_ajax_send','contact_email');
add_action('wp_ajax_nopriv_send','contact_email');
function contact_email(){
   include("data/contact.php");
   die();
}
add_action('admin_init', 'reg_form_setting' );

function reg_form_setting()
{
    //register plugin settings
    register_setting('wdm_form_options','wdm_form_data');
}

function add_ask_product_settings()
{
    //settings page
    
    wp_enqueue_script('wdm_wpi_validation', plugins_url("js/wdm-jquery-validate.js", __FILE__), array('jquery'));
	
    /*wp_enqueue_style( 'icomoon-icons', 'http://i.icomoon.io/public/temp/f775f44665/UntitledProject1/style.css' ); */
    ?>
    
      <div class="wrap wdm_leftwrap">
      	<div class='wdm-pro-notification'>
      
      <div class='wdm-title-layer'>
		<h4>Get The New Premium Version</h4>
      </div> <!--wdm-title-layer ends-->
      
      <div class="wdm-content-layer">
      	<div class="wdm-left-content">
       		<img src='<?php echo plugins_url('img/PEP_new.png',__FILE__); ?>' class='wdm_pro_logo'>
			<div class="wdm-actionBtn">
        		<a class='wdm_upgrade_pro_link' href='http://wisdmlabs.com/demo/product/woocommerce-product-enquiry-plugin/' target='_blank'>View Demo </a>
            </div>
        </div>
        <div class="wdm-right-content">	
        	<div class="wdm-features">
            	<h3 class='wdm_feature_heading'>New Features In Pro Version</h3>
               
			   <div class='wdm-feature-list'>
				
						<div class="wdm-feature">
							<span class="wdmiconfilter"></span>
							<p>Filter <br> enquires</p>
						</div>
					
						<div class="wdm-feature">
							<span class="wdmiconexpand"></span>
							<p>Responsive</p>
						</div>
																					
						<div class="wdm-feature">
							<span class="wdmiconpaint-format"></span>
							<p>Custom <br> styling</p>
						</div>
						 <div class="wdm-feature">
							<span class="wdmiconearth"></span>
							<p>WPML <br> Compatible</p>
						 </div>
						  <div class="wdm-feature">
							<span class="wdmiconprofile"></span>
							<p>Customizable <br> Enquiry Form</p>
						  </div>
					
					<div class="clear"></div>
				</div>
				
				<!-- <div class='wdm-feature-list'>
					
							<div class="wdm-feature">
								<span class="wdmiconeye"></span>
								<p>Enquiries in dashboard</p>
							</div>
							
							<div class="wdm-feature">
								<span class="wdmiconarchive"></span>
								<p>Export enquiry records</p>
							</div>	
							
							<div class="wdm-feature">
								<span class="wdmiconbubbles"></span>
								<p>Localization ready</p>
							</div>
						
							<div class="clear"></div>
				</div> -->
				
				<div class="wdm_coupon">
						<a class='wdm_upgrade_pro_link' href='http://wisdmlabs.com/woocommerce-product-enquiry-pro/' target='_blank'>UPGRADE TO PRO </a>
				</div>
            </div>
        </div>
        <div class='clear'></div>
      </div>
      <div class='clear'></div>
	
    </div> <!--wdm-pro-notification ends-->
    
        <h2>Product Enquiry</h2>
<br />
	<?php
	if( isset( $_GET[ 'tab' ] ) )   
            $active_tab = $_GET[ 'tab' ];  
	else	    
            $active_tab = 'form';
        
        ?>
            <h2 class="nav-tab-wrapper">  
                <a href="edit.php?post_type=product&page=pefw&tab=form" class="nav-tab <?php echo $active_tab == 'form' ? 'nav-tab-active' : ''; ?>">Enquiry Settings</a>
		<a href="edit.php?post_type=product&page=pefw&tab=entry" class="nav-tab <?php echo $active_tab == 'entry' ? 'nav-tab-active' : ''; ?>">Enquiry Details</a>
		 <a href="edit.php?post_type=product&page=pefw&tab=contact" class="nav-tab <?php echo $active_tab == 'contact' ? 'nav-tab-active' : ''; ?>">Product Enquiry Ideas</a>
		 <a href="edit.php?post_type=product&page=pefw&tab=hireus" class="nav-tab <?php echo $active_tab == 'hireus' ? 'nav-tab-active' : ''; ?>">Hire Us</a>
            </h2>  
	
    <?php if($active_tab === 'entry'){
	
	?>
	<div id='entry_dummy'>
	    <div class="layer_parent">
		    <div class="pew_upgrade_layer">
			<div class="pew_uptp_cont">
			    <p> This feature is available in the PRO version. Click below to know more.</p>
			<a class="wdm_view_det_link" href="http://wisdmlabs.com/woocommerce-product-enquiry-pro/" target="_blank"> View Details </a>
			</div>
		    </div>
		    <img src="<?php echo plugins_url('/img/entries.png', __FILE__); ?>" style='width:100%;height: 623px'/>
	    </div>
	    
	</div>
	<?php
	 
    }
	 elseif($active_tab === 'contact'){ ?>
<div class="wdm-tab-container">
<div class="wdm-container">
<fieldset>
<!--<legend>About</legend>
<div class="col-3 wdm-abt">
<a href="http://www.wisdmlabs.com" target="_blank">
<div id="wdm-logo"></div>
</a>
<div class="wdm-rating">
<span>Rate this plugin : <a href="http://wordpress.org/support/view/plugin-reviews/<?php echo $wdm_plugin_slug; ?>" target="_blank" id="rating-stars"></a>
</span>
</div>
</div>-->
<div class="col-1 wdm-abt" >
<p class="wdm-about" style="text-align:center">
Product Enquiry Pro is one of WisdmLabs early plugins and a very successful one. With over <b class="wdm-color" >100 satisfied customers</b> we continue to improve PEP and give the best to our customers. We stand by our products and make sure we give our customers what they are looking for with great quality and even better features.
<br><br>
<b class="wdm-color" style="width: 100%; margin: 0px auto; text-align: center; font-size: 16px;">THIS IS WHERE WE NEED YOU!</b>
<br><br>
We need you, the users of PEP to <b style="color:#961914;">pitch in your ideas </b>for the plugin. Based on the number of interested users, we will incorporate the feature and make it available at a minimal cost.
<br>
</p>
<!--<hr width="100%">
<p>
For support click <a href="#" class="wdm-link" title="Wisdmlabs support">here</a>
</p>-->
</div>
<div class="clear"></div>
</fieldset>
</div>
</div>
<div class="wdm-container wdm-services-offered clearfix">
<?php global $current_user;
get_currentuserinfo();
?>
<ul class="wdm-services-list clearfix">
<li class="wdm-services-item">
<div class="wdm-services-icon wdm-custom-eq-form" ></div>
<h3>Customize Your Enquiry Form</h3>
<p class="wdm-services-desc">
Flexibility to create your own fields within the enquiry form.
</p>
<input type="button" class="wdm-services-button wdm-Customize " value="Request Feature" />
<div class="hide_class">
<?php echo "<h4 class='wdm-req-title'>" . $current_user->user_login . ", You have been Requested this Freature </h4>"; ?>
<form class="wdm-req-form customize-your-enquiry-form" >
<br><small>Confirm Email-id :</small>
<input type="text" class="wdm-req-text" name="wdm-req-email" value="<?php echo $current_user->user_email ?>" />
<input type="button" class="wdm-req-button" value="Request Feature" name="request-feature" />
<input type="hidden" class="id" name="id" value="customize-your-enquiry-form" />

<div class="loading"></div>
</form>
<span class="wdm-close" ></span>
</div>
</li>
<li class="wdm-services-item">
<div class="wdm-services-icon wdm-display-eq-button" ></div>
<h3>Display Enquiry Button On Shop</h3>
<p class="wdm-services-desc">
Allow visitors to enquire about your products directly from the shop page.
</p>
<input type="button" class="wdm-services-button" value="Request Feature" />
<div class="hide_class">
<?php echo "<h4 class='wdm-req-title'>" . $current_user->user_login . ", You have been Requested this Freature </h4>"; ?>
<form class="wdm-req-form display-enquiry-button-on-shop" >
<br><small>Confirm Email-id :</small>
<input type="text" class="wdm-req-text" name="wdm-req-email" value="<?php echo $current_user->user_email ?>" />
<input type="button" class="wdm-req-button" value="Request Feature" name="request-feature" />
<input type="hidden" class="id" name="id" value="display-enquiry-button-on-shop" />
<div class="loading"></div>
</form>
<span class="wdm-close" ></span>
</div>
</li>
<li class="wdm-services-item" style="margin-right: 0">
<div class="wdm-services-icon wdm-create-cu-email" ></div>
<h3>Create a Custom Email Template</h3>
<p class="wdm-services-desc">
Style and create templates for your enquiry emails from your dashboard.
</p>
<input type="button" class="wdm-services-button one" value="Request Feature" />
<div class="hide_class">
<?php echo "<h4 class='wdm-req-title'>" . $current_user->user_login . ", You have been Requested this Freature </h4>"; ?>
<form class="wdm-req-form create-a-custom-email" >
<br><small>Confirm Email-id :</small>
<input type="text" class="wdm-req-text" name="wdm-req-email" value="<?php echo $current_user->user_email ?>" />
<input type="button" class="wdm-req-button" value="Request Feature" name="request-feature" />
<input type="hidden" class="id" name="id" value="create-a-custom-email" />

<div class="loading"></div>
</form>
<span class="wdm-close" ></span>
</div>
</li>
<!---for selerating two rows---->
<!--<div class="clear"></div> -->
<li class="wdm-services-item">
<div class="wdm-services-icon wdm-hide-re-cart" ></div>
<h3>Hide or Replace Add-to-Cart button</h3>
<p class="wdm-services-desc">
Replace the add-to-cart button with the enquiry button for your products.
</p>
<input type="button" class="wdm-services-button" value="Request Feature" />
<div class="hide_class">
<?php echo "<h4 class='wdm-req-title'>" . $current_user->user_login . ", You have been Requested this Freature </h4>"; ?>
<form class="wdm-req-form hide-or-replace-add-to-cart-button" >
<br><small>Confirm Email-id :</small>
<input type="text" class="wdm-req-text" name="wdm-req-email" value="<?php echo $current_user->user_email ?>" />
<input type="button" class="wdm-req-button" value="Request Feature" name="request-feature" />
<input type="hidden" class="id" name="id" value="hide-or-replace-add-to-cart-button" />

<div class="loading"></div>
</form>
<span class="wdm-close" ></span>
</div>
</li>
<li class="wdm-services-item">
<div class="wdm-services-icon wdm-analytics-eq" ></div>
<h3>Analytics For Your Enquiries</h3>
<p class="wdm-services-desc">
Get detailed analytics for your enquiries based on products and other attributes.
</p>
<input type="button" class="wdm-services-button" value="Request Feature" />
<div class="hide_class">
<?php echo "<h4 class='wdm-req-title'>" . $current_user->user_login . ", You have been Requested this Freature </h4>"; ?>
<form class="wdm-req-form Analytics-for-your-enquiry" >
<br><small>Confirm Email-id :</small>
<input type="text" class="wdm-req-text" name="wdm-req-email" value="<?php echo $current_user->user_email ?>" />
<input type="button" class="wdm-req-button" value="Request Feature" name="request-feature" />
<input type="hidden" class="id" name="id" value="Analytics-for-your-enquiry" />

<div class="loading"></div>
</form>
<span class="wdm-close" ></span>
</div>
</li>
<li class="wdm-services-item" style="margin-right: 0">
<div class="wdm-services-icon wdm-newsletter-int" ></div>
<h3>Newsletter Integration With PEP</h3>
<p class="wdm-services-desc">
Get your newsletter plugin integrated seamlessly with PEP.
</p>
<input type="button" class="wdm-services-button" value="Request Feature" />
<div class="hide_class">
<?php echo "<h4 class='wdm-req-title'>" . $current_user->user_login . ", You have been Requested this Freature </h4>"; ?>
<form class="wdm-req-form newsletter-integration-with-pep" >
<br><small>Confirm Email-id :</small>
<input type="text" class="wdm-req-text" name="wdm-req-email" value="<?php echo $current_user->user_email ?>" />
<input type="button" class="wdm-req-button" value="Request Feature" name="request-feature" />
<input type="hidden" class="id" name="id" value="newsletter-integration-with-pep" />

<div class="loading"></div>
</form>
<span class="wdm-close" ></span>
</div>
</li>
</ul>
<!-- //code for displaying the hidden send button -->
<script type="text/javascript">
//jQuery(this.className).click(function() {
// jQuery(this.className).css('display','block');
// console.log(this.className);
//});
jQuery('.wdm-services-button').click(function (e) {
if (((e.target.id == 'hide_class') || (e.target.id == 'wdm-req-form') || (e.target.nodeName == 'H4') || (e.target.nodeName == 'INPUT') || (e.target.nodeName == 'SMALL'))) {
console.log(jQuery(e.target));
jQuery(".hide_class").hide();
//jQuery(!jQuery(e.target).siblings(".hide_class")).hide();
jQuery(e.target).siblings(".hide_class").fadeIn();
console.log('yes');
} else {
jQuery(".hide_class").fadeIn("slow");
console.log('no');
}
});
//jQuery('#').css('border','none');
jQuery('.wdm-close').click(function (event) {
console.log('clicked');
jQuery(event.target).parent('.hide_class').fadeOut("slow");
});
//jQuery('#wdm-close').css('display','block');
</script>
</div>
<div class="clear"></div>
<div class="wdm-container wdm-services-details">
<fieldset>
<!--<legend>Details</legend>-->
<div class="wdm-details">
<h2 class="wdm-color" style="text-align: center;" >Get A Feature Developed in PEP</h2>
<hr>
Select a feature you want us to develop and leave us a note about it. We will get in touch with you and keep you posted on its progress.<br><br>
<b class="wdm-color">Need a custom feature developed in PEP ?</b>
<br><br><a href="http://wisdmlabs.com/contact-us/" target="_blank" class="wdm-contact-button" title="Wisdmlabs" >Contact Us</a>
</div>
</fieldset>
</div>
<?php }
		elseif($active_tab === 'hireus'){ ?>
		<div class="wdm-tab-container">
		<div class="wdm-container">
		<fieldset class="wdm-plugin-customize">
		<h2 class="wdm-color">Plugin Development and Customization</h2><hr>
		<p>Our area of expertise is WordPress plugins. We specialize in creating bespoke plugin solutions for your business needs. Our competence with plugin development and customization has been certified by WordPress biggies like WooThemes and Event Espresso.<br><br>
		<a class="wdm-contact-button" style="padding:8px 30px; margin-top:10px;" href="http://wisdmlabs.com/contact-us/" target="_blank">Contact Us</a>
		</p>
		</fieldset>
		<div class="wdm-container">
		<h2 style="text-align: center;" class="wdm-color">Our Premium Plugins</h2>
		<ul class="wdm-premium-plugins">
		<li class="wdm-plugins-item">
		<h3>Customer Specific Pricing for WooCommerce</h3>
		<p style="text-align: center;">
		This simple yet powerful plugin, allows you to set a different price for a WooCommerce product on a per customer basis. <br><br>
		</p>
		<a class="wdm-contact-button wdm-know-more" href="http://wisdmlabs.com/woocommerce-user-specific-pricing-extension/" target="_blank">Know more </a>
		</li>
		<li class="wdm-plugins-item">
		<h3>WooCommerce Custom Product Boxes</h3>
		<p style="text-align: center;">
		This plugin allows customers, to select and create their own product bundles, which can be purchased as a single product!
		</p>
		<a class="wdm-contact-button wdm-know-more" href="http://wisdmlabs.com/assorted-bundles-woocommerce-custom-product-boxes-plugin/" target="_blank">Know more </a>
		</li>
		<li class="wdm-plugins-item">
		<h3 style="min-height:54px;">Instagram WooCommerce Integration  </h3>
		<p style="text-align: center;">
		A perfect solution, to create collages using Instagram images, right from your WooCommerce store.
		</p>
		<a class="wdm-contact-button wdm-know-more" href="http://wisdmlabs.com/instagram-woocommerce-integration-solution/" target="_blank">Know more </a>
		</li>
		<li class="wdm-plugins-item">
		<h3>WooCommerce Moodle Integration</h3>
		<p style="text-align: center;">
		Want to sell Moodle Courses in your WooCommerce store? This plugin allows you to do the same and much more.
		</p>
		<a class="wdm-contact-button wdm-know-more" href="http://wisdmlabs.com/woocommerce-moodle-integration-solution/" target="_blank">Know more </a>
		</li>
		</ul>
		</div>
		<div class="clear"></div>
		<fieldset class="wdm-bouquet-of-services">
		<h2 class="wdm-color">Entire Array of Services</h2><hr>
		<p>We specialize in WordPress website development and customization with an entire range of services under our belt. We have expertise in domains such as eCommerce, LMS, Event Management System, etc. Explore our services now and cater to all your WordPress requirements under one roof.<br><br>
		<a class="wdm-contact-button" style="padding:8px 30px; margin-top:10px;" href="http://wisdmlabs.com/services/" target="_blank">View Our Services</a>
		</p>
		</fieldset>
		</div>
		</div>
		<?php }
    else{
	$pro = "<span title='Pro Feature' class='pew_pro_txt'> [Available in PRO] </span>";
    ?>
     <form name="ask_product_form" id="ask_product_form" method="POST" action="options.php" style="background: #fff; padding: 10px 15px 0 15px;">
        <?php
            settings_fields('wdm_form_options');
            $default_vals =   array('show_after_summary'=>1        
                                    );
            $form_data = get_option( 'wdm_form_data', $default_vals);
            ?>
            
      <div id="ask_abt_product_panel">
	<fieldset>
	    <?php echo '<legend>'. __("Emailing Information",'pep_text_domain').'</legend>'; ?>
	<div class="fd">
	<div class='left_div'>
            <label for="wdm_user_email"> Recipient's Email </label> 
	</div>
	<div class='right_div'>
	    <input type="text" class="wdm_wpi_input wdm_wpi_text email" name="wdm_form_data[user_email]" id="wdm_user_email" value="<?php echo empty($form_data['user_email']) ? get_option('admin_email') : $form_data['user_email'];?>" />
	    <span class='email_error'> </span>
	</div>
	<div class='clear'></div>
	</div >
	<div class="fd">
	<div class='left_div'>
	    <label for="wdm_default_sub"> Default Subject </label>
	</div>
	<div class='right_div'>		
	    <input type="text" class="wdm_wpi_input wdm_wpi_text" name="wdm_form_data[default_sub]" id="wdm_default_sub" value="<?php echo empty($form_data['default_sub']) ? 'Enquiry for a product from '.get_bloginfo('name') : $form_data['default_sub'];?>"  />
        <br>
	    <?php echo '<em> Will be used if the customer does not enter a subject </em>'; ?>
	</div>
	<div class='clear'></div>
	</div>
        </fieldset>
	<br/>
	    <fieldset>
	
	 <?php echo '<legend>'. __("Form Options",'pep_text_domain').'</legend>'; ?>
	<div class="fd">
			<div class='left_div'>
            <label for="custom_label"> Button-Text for enquiry button </label> 
	    </div>
			<div class='right_div'>
            <input type="text" class="wdm_wpi_input wdm_wpi_text" name="wdm_form_data[custom_label]" value="<?php echo empty($form_data['custom_label']) ? 'Make an enquiry for this product' : $form_data['custom_label'];?>" id="custom_label"  />
        </div>
			<div class='clear'></div>
		</div>
	<div class="fd">
			<div class='left_div'>
	    <label> Enquiry Button Location </label>
	    </div>
			<div class='right_div'>
			    
            
	   <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" name="wdm_form_data[show_after_summary]" value="1" <?php echo (isset($form_data["show_after_summary"]) ? "checked" : "" );?> id="show_after_summary" /> 
	    <label for="show_after_summary"> After single product summary </label>
	    <br />
	    <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" name="wdm_form_data[show_at_page_end]" value="1" <?php echo (isset($form_data["show_at_page_end"]) ? "checked" : "" );?> id="show_at_page_end" />
		   
	   
	    <label for="show_at_page_end"> At the end of single product page </label>
	    
        </div>
	    <div class='clear'></div>
	</div>
        
	<div class="fd">
	    <div class='left_div'>
		<label> Enable sending an email copy </label>
		 </div>
	    <div class='right_div'>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" name="wdm_form_data[enable_send_mail_copy]" value="1" <?php echo (isset($form_data["enable_send_mail_copy"]) ? "checked" : "" );?> id="enable_send_mail_copy" />
	  
	    <label for="enable_send_mail_copy"> Enable option on the enquiry form to send an email copy to customer </label>
        </div>
			<div class='clear'></div>
	</div>
	
	<div class="fd">
	    <div class='left_div'>
	    <label for="link">
		Display 'Powered by WisdmLabs'
	    </label>
	    </div>
	    <div class='right_div'>
	    <input type="checkbox" disabled class="wdm_wpi_input wdm_wpi_checkbox" value="1" id="show_powered_by_link" />
		 <?php echo $pro; ?>
	    </div>
	<div class="clear"></div>
	</div>
	
	<div class="fd">
	    <div class='left_div'>
	    <label for="enable_telephone_no_txtbox">
		Display Telephone Number Field
	    </label>
	    </div>
	    <div class='right_div'>
		<input type="checkbox" disabled class="wdm_wpi_input wdm_wpi_checkbox" value="1" id="enable_telephone_no_txtbox" />
		     <?php echo $pro; ?>
	    </div>
	<div class="clear"></div>
	</div>
	</fieldset>
	    <br>
	    <fieldset>
		    <legend>Styling Options </legend>
			<div class='fd'>
				    <div class='left_div'>
				    <label for="enable_telephone_no_txtbox">
				    Custom Styling
				   </label>
				    </div>
				    <div class='right_div'>
				    <input type="radio" class="wdm_wpi_input wdm_wpi_checkbox" value="theme_css" name="wdm_radio_data" id="theme_css" checked />
																					    <em> Use Activated Theme CSS </em><br>
										 
				    <input type="radio" class="wdm_wpi_input wdm_wpi_checkbox" value="manual_css" name="wdm_radio_data" id="manual_css" />
					
				    <em>Manually specify color settings</em>
				  </div>
				    <div class="clear"></div>
			</div>
	    </fieldset>  
	   <br />
	   <div name="Other_Settings" id="Other_Settings" style="display: none;">
	    <fieldset style="padding: 0;">
		<?php echo '<legend>'. __("Specify CSS Settings ",'pep_text_domain').'</legend>';?>
	    <br />
		<div class="layer_parent">
		    <div class="pew_upgrade_layer">
			<div class="pew_uptp_cont">
			    <p> This feature is available in the PRO version. Click below to know more.</p>
			<a class="wdm_view_det_link" href="http://wisdmlabs.com/woocommerce-product-enquiry-pro/" target="_blank"> View Details </a>
			</div>
		    </div>
		    <img src="<?php echo plugins_url('img/buttons-css.png',  __FILE__);?>" />
		</div>
	    </fieldset>
	    </div>
      
        <p>
            <input type="submit" class="wdm_wpi_input button-primary" value="Save Changes" id="wdm_ask_button" />
        </p>
        </div>
	<br/>
     </form>
     
         <script type="text/javascript">
	jQuery(document).ready(
			       function($)
			       {
					$("#ask_product_form").validate();
					
					if($("#manual_css").is(':checked')) {
					    $("#Other_Settings").show();
					}
					else{
					   $("#Other_Settings").hide(); 
					}
					
					$("#theme_css").click(function(){$("#Other_Settings").hide();});
					$("#manual_css").click(function(){$("#Other_Settings").show();});
				}
				);
    </script>
	 
     <?php } ?>
    </div>
    
      <?php
      //add styles for settings page
      wp_enqueue_style("wdm-admin-css", plugins_url("css/wpi_admin.css", __FILE__));
      
      //include WisdmLabs sidebar
    
    $plugin_data  = get_plugin_data(__FILE__);
    $plugin_name = $plugin_data['Name'];
    $wdm_plugin_slug = 'product-enquiry-for-woocommerce';
    
   // include_once('wisdm_sidebar/wisdm_sidebar.php');
   // pew_create_wisdm_sidebar($plugin_name, $wdm_plugin_slug);
}
?>

<?php
add_action( 'admin_footer', 'my_action_javascript' ); // Write our JS below here
function my_action_javascript() { ?>
<script type="text/javascript" >
jQuery(document).ready(function($) {
jQuery(".wdm-req-button").click(function() {
var data = {};
data.email = $(this).siblings(".wdm-req-text").val();
data.id = $(this).siblings( ".id" ).val();
data.updates = $(this).siblings(".wdm-req-confirm").prop("checked");
data.action = "my_action";
var email = $(this).siblings(".wdm-req-text").val();
var txt = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
if (!txt.test(email)) {
$(this).siblings("input.wdm-req-text").css('border','1px solid red');
e.preventDefault();
} else {
$(this).siblings("input.wdm-req-text").css('border','1px solid #ccc');
}
var id = $(this).siblings( ".id" ).val();
var loading = $(this).siblings(".loading").show();
// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
$.post(ajaxurl, data, function(response) {
//alert('Got this from the server: ' + response + '<br>id is :' + ids);
console.log( response );
var id = jQuery.trim(response);
//jQuery(this).siblings('.wdm-req-title').hide();
jQuery('.loading').hide();
alert('Your request has been sent');
jQuery(id).find("div.loading").hide();
});
});
});
</script> <?php
}
add_action( 'wp_ajax_my_action', 'my_action_callback' );
function my_action_callback() {
global $wpdb; // this is how you get access to the database
$email = $_POST['email'];
$id = $_POST['id'];
$updates = $_POST['updates'];
$subscribes_message = "";
$subscribes_message_client = "";
if($updates == "true") {
$subscribes_message = "And Congrats! They have subscribed to your newsletter too!";
$subscribes_message_client = "We'll keep you updated with the latest developments.";
}
else {
//$subscribes_message = "Oh shoot! They haven't subscribed.";
//$subscribes_message_client = "To stay up-to-date with the latest developments, do subscribe to our newsletter!";
}
$message = <<<SUBS
Hi,
A feature request has been made for Product enquiry free, by $email.
Requested Feature: $id
$subscribes_message
Thanks and Regards,
This is an automated mail, sent by WisdmLabs
SUBS;
$message_client = <<<SUSBCLIENT
Hi there,

Thank you for requesting the $id feature for Product Enquiry for WooCommerce.

We will keep you updated with the latest developments.

Thanks and Regards,
WisdmLabs
SUSBCLIENT;
//echo $email . " id is --:" . $id;
wp_mail( 'support@wisdmlabs.com','PE Feature Request', $message);
//mail for client
$client_header[] = 'From: WisdmLabs <donotreply@wisdmlabs.com>';
wp_mail( $email,'Your Feature Request Has Been Sent!', $message_client, $client_header );
echo "." . $id;
die(); // this is required to terminate immediately and return a proper response
}
?>
