<?php
/*Plugin Name: Product Enquiry for WooCommerce
Description: Allows prospective customers or visitors to make enquiry about a product, right from within the product page.
Version: 1.0.0
Author: WisdmLabs
Author URI: https://wisdmlabs.com
Plugin URI: https://wordpress.org/plugins/product-enquiry-for-woocommerce
License: GPL2
Text Domain: wdm-product-enquiry
*/

// add plugin upgrade notification
add_action('in_plugin_update_message-product-enquiry-for-woocommerce/product-enquiry-for-woocommerce.php', 'showProductEnquiryUpgradeNotification', 10, 2);
function showProductEnquiryUpgradeNotification($currentPluginMetadata, $newPluginMetadata){
   // check "upgrade_notice"
   if (isset($newPluginMetadata->upgrade_notice) && strlen(trim($newPluginMetadata->upgrade_notice)) > 0){
        echo '<p style="background-color: #d54e21; padding: 10px; color: #f9f9f9; margin-top: 10px">'. __( '<strong>This is a MAJOR Update Product Enquiry for WooCommerce has been completely revamped. The button position has been updated to be displayed below the add to cart button. Take a look at the screenshot at wordpress.org</strong>' , 'wdm-product-enquiry' );
        echo esc_html($newPluginMetadata->upgrade_notice), '</p>';
   }
}

add_action('plugins_loaded', 'wdm_pe_init');
function wdm_pe_init(){
    load_plugin_textdomain('wdm-product-enquiry', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action( 'admin_notices', 'check_woo_dependency' );

//Check whether WooCommerce is active or not

function check_woo_dependency() { 
    if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

        echo "<div class='error'><p>". __( '<strong>WooCommerce</strong> plugin is not active. In order to make <strong>Product Enquiry</strong> plugin work, you need to install and activate <strong>WooCommerce</strong> first' , 'wdm-product-enquiry' ). "</p></div>";
    }
}

/*deactivation of pro version*/
register_activation_hook( __FILE__, 'fun_create_tbl_enquiry' );

function fun_create_tbl_enquiry() {

    $my_plugin = 'product-enquiry-pro/product_enquiry_pro.php';


    if ( is_plugin_active( $my_plugin ) ) {

        add_action( 'update_option_active_plugins', 'deactivate_dependent_product_enquiry_pro' );
    }
}
    
function deactivate_dependent_product_enquiry_pro() {
    $my_plugin = 'product-enquiry-pro/product_enquiry_pro.php';

    deactivate_plugins( $my_plugin );
}

add_action( 'wp_head', 'wdm_display_btn_func', 11 );

function wdm_display_btn_func(){

$form_init_data = get_option( 'wdm_form_data');

if(!empty($form_init_data))
{
    if(isset($form_init_data['show_after_summary']))
    {
    if($form_init_data['show_after_summary'] == 1)
    {
	//show ask button after a single product summary
        add_action('woocommerce_single_product_summary', 'ask_about_product_button',30);
	
    }
    }
    
    if(isset($form_init_data['show_at_page_end']))
    {
    if($form_init_data['show_at_page_end'] == 1)
    {
	//show ask button at the end of the page of a single product
        add_action('woocommerce_after_single_product', 'ask_about_product_button',10);
    }
    }
}
else
{
    //show ask button after a single product summary as default
    add_action('woocommerce_single_product_summary', 'ask_about_product_button',30);
}

}

function ask_about_product_button()
{
    $form_data = get_option( 'wdm_form_data');
 ?>
     <div id="enquiry">
            <input type="button" name="contact" value="<?php echo empty($form_data['custom_label']) ?
__('Make an enquiry for this product','wdm-product-enquiry'): $form_data['custom_label'];?>" class="contact wpi-button" />
     </div>
		
<?php }

add_action('wp_footer', 'ask_about_product');

function ask_about_product(){
    
  $form_data = get_option( 'wdm_form_data');
    ?>
     <br />
     <!-- Page styles -->
     <?php
           // wp_enqueue_style("wdm-contact-css", plugins_url("css/contact.css", __FILE__));
	   wp_enqueue_style("wdm-juery-css", plugins_url("css/wdm-jquery-ui.css", __FILE__));
     ?>
    <?php if(is_singular('product')/*&&(!empty($form_data['show_at_page_end']))||(!empty($form_data['show_after_summary']))*/){ ?>
    <div id="contact-form" title="<?php _e("Product Enquiry","wdm-product-enquiry");?>" style="display:none;">
    <form id="enquiry-form" action="#" method="POST">
    <label id="wdm_product_name" for='product_name'> <?php echo get_the_title();?> </label>
	    <div class="wdm-pef-form-row">
		<label for='contact-name'>*<?php _e("Name:","wdm-product-enquiry");?></label>
		<input type='text' id='contact-name' class='contact-input' name='wdm_customer_name' value=""/>
	    </div>
	    <div class="wdm-pef-form-row">
		<label for='contact-email'>*<?php _e("Email:","wdm-product-enquiry");?></label>
		<input type='text' id='contact-email' class='contact-input' name='wdm_customer_email'  />
	    </div>
	    <div class="wdm-pef-form-row">
		<label for='contact-subject'><?php _e("Subject:","wdm-product-enquiry");?></label>
		<input type='text' id='contact-subject' class='contact-input' name='wdm_subject' value=''  />
	    </div>
	    <div class="wdm-pef-form-row">
		<label for='contact-message'>*<?php _e("Enquiry:","wdm-product-enquiry");?></label>
		<textarea id='contact-message' class='contact-input' name='wdm_enquiry' cols='40' rows='4' style="resize:none"></textarea>
	    </div>
	    <?php if(!empty($form_data['enable_send_mail_copy'])){?>
	    <div class="wdm-pef-send-copy">
		<input type='checkbox' id='contact-cc' name='cc' value='1' /> <span class='contact-cc'>
		<?php _e("Send me a copy","wdm-product-enquiry");?></span>
	    </div>
	    <?php }?>
	    <div id="errors"></div>
	    <div class="wdm-enquiry-action-btns">
		<button id="send-btn" type='submit' class='contact-send contact-button' ><?php _e("Send","wdm-product-enquiry");?></button>
		<button id="cancel" type='button' class='contact-cancel contact-button' ><?php _e("Cancel","wdm-product-enquiry");?></button>
	    </div>
	    <?php echo wp_nonce_field( 'enquiry_action', 'product_enquiry',true,false ); ?>
	    
  </form>
 <?php
 
$site_url=site_url();
$domain_name=htmlspecialchars(url_to_domain($site_url));
$domain_name_value=ord($domain_name);
if($domain_name_value>=97 && $domain_name_value<=102)
{
$display_url="https://wisdmlabs.com/";
}
else if($domain_name_value>=103 && $domain_name_value<=108)
{
$display_url="https://wisdmlabs.com/wordpress-development-services/plugin-development/";
}
elseif($domain_name_value>=109 && $domain_name_value<=114)
{
$display_url="https://wisdmlabs.com/woocommerce-extension-development-customization-services/";
}
else{
$display_url="https://wisdmlabs.com/woocommerce-product-enquiry-pro/";
}
?>
<div class='contact-bottom'><a href='<?php echo $display_url ?>' target='_blank'><?php _e("Powered by WisdmLabs","wdm-product-enquiry");?></a></div>
  </div>
  <!-- preload the images -->
	    
		<div id="loading" style='display:none'>
			<div id="send_mail"><p><?php _e("Sending...","wdm-product-enquiry");?></p>
			<img src='<?php echo plugins_url("img/contact/loading.gif", __FILE__)?>' alt='' />
			</div>
		</div> <?php } ?>
    <!-- Load JavaScript files -->
   <?php
    
    wp_enqueue_script("jquery");
    wp_enqueue_script("jquery-ui-core",array("jquery"));
    wp_enqueue_script("jquery-ui-dialog",array("jquery"));
    wp_enqueue_script("wdm-validate", plugins_url("js/wdm_jquery.validate.min.js", __FILE__));
    //wp_enqueue_script("wdm-validate", plugins_url("js/jquery.validate.min.js", __FILE__));
    wp_enqueue_script("wdm-contact", plugins_url("js/contact.js", __FILE__), array("jquery"));
    wp_localize_script( 'wdm-contact', 'object_name',
		       array('ajaxurl' => admin_url( 'admin-ajax.php' ),
			    'product_name'=>get_the_title(),
			    'wdm_customer_name' => __('Name is required.','wdm-product-enquiry'),
			    'wdm_customer_email'=>__('Enter valid Email Id.','wdm-product-enquiry'),
			    'wdm_enquiry' => __('Enquiry length must be atleast 10 characters.','wdm-product-enquiry') ));
}
/* Thanks to davejamesmiller*/
function url_to_domain($site_url)
{
    $host = @parse_url($site_url, PHP_URL_HOST);

    // If the URL can't be parsed, use the original URL
    // Change to "return false" if you don't want that
    if (!$host)
        $host = $site_url;

    // The "www." prefix isn't really needed if you're just using
    // this to display the domain to the user
    if (substr($host, 0, 4) == "www.")
        $host = substr($host, 4);

    // You might also want to limit the length if screen space is limited
    if (strlen($host) > 50)
        $host = substr($host, 0, 47) . '...';

    return $host;
}


add_action('admin_menu', 'create_ask_product_menu');

function create_ask_product_menu()
{
    //create a submenu under Woocommerce 'Products' menu
    add_submenu_page('edit.php?post_type=product', __('Product Enquiry','wdm-product-enquiry'), __('Product Enquiry','wdm-product-enquiry'), 'manage_options', 'pefw', 'add_ask_product_settings' );
}

function add_ask_product_settings()
{
    //settings page
    
    wp_enqueue_script('wdm_wpi_validation', plugins_url("js/wdm_jquery.validate.min.js", __FILE__), array('jquery'));
    
    ?>
    
      <div class="wrap wdm_leftwrap">
      	<div class='wdm-pro-notification'>
      
      <div class='wdm-title-layer'>
		<h4><?php _e("Get The New Premium Version","wdm-product-enquiry");?></h4>
      </div> <!--wdm-title-layer ends-->
      
      <div class="wdm-content-layer">
      	<div class="wdm-left-content">
       		<img src='<?php echo plugins_url('img/PEP_new.png',__FILE__); ?>' class='wdm_pro_logo'>
			<div class="wdm_upgrade">
						<a class='wdm_upgrade_pro_link' href='https://wisdmlabs.com/woocommerce-product-enquiry-pro/' target='_blank'><?php _e("UPGRADE TO PRO","wdm-product-enquiry");?> </a>
				</div>
        </div>
        <div class="wdm-right-content">	
        	<div class="wdm-features">
            	<h3 class='wdm_feature_heading'><?php _e("Features In Pro Version","wdm-product-enquiry");?></h3>
               
			   <div class='wdm-feature-list'>
				
						<div class="wdm-feature">
							<span class="wdmicon-filter"></span>
							<p><?php _e("Filter","wdm-product-enquiry");?> <br><?php _e(" enquires","wdm-product-enquiry");?></p>
						</div>
					
						<div class="wdm-feature">
							<span class="wdmicon-enlarge"></span>
							<p><?php _e("Responsive","wdm-product-enquiry");?></p>
						</div>
																					
						<div class="wdm-feature">
							<span class="wdmicon-paint-format"></span>
							<p><?php _e("Custom","wdm-product-enquiry");?> <br> <?php _e("styling","wdm-product-enquiry");?></p>
						</div>
						 <div class="wdm-feature">
							<span class="wdmicon-earth"></span>
							<p><?php _e("WPML","wdm-product-enquiry");?> <br> <?php _e("Compatible","wdm-product-enquiry");?></p>
						 </div>
						<div class="wdm-feature">
							<span class="wdmicon-eye"></span>
							<p><?php _e("Enquiries","wdm-product-enquiry");?><br><?php _e("in dashboard","wdm-product-enquiry");?></p>
						</div>
							
						<div class="wdm-feature">
							<span class="wdmicon-drawer2"></span>
							<p><?php _e("Export","wdm-product-enquiry");?><br><?php _e("enquiry records","wdm-product-enquiry");?></p>
						</div>	
							
						<div class="wdm-feature">
							<span class="wdmicon-bubbles3"></span>
							<p><?php _e("Localization","wdm-product-enquiry");?><br><?php _e("ready","wdm-product-enquiry");?></p>
						</div>

						<div class="wdm-feature">
							<span class="wdmicon-pencil2"></span>
							<p><?php _e("Customizable","wdm-product-enquiry");?><br><?php _e("Enquiry Form","wdm-product-enquiry");?></p>
						</div>

					<div class="clear"></div>
				</div>
				

	    </div>
        </div>
        <div class='clear'></div>
      </div>
      <div class='clear'></div>
	
    </div> <!--wdm-pro-notification ends-->
    
    

        <h2><?php _e("Product Enquiry","wdm-product-enquiry");?></h2>
<br />
	<?php
	if( isset( $_GET[ 'tab' ] ) )   
            $active_tab = $_GET[ 'tab' ];  
	else	    
            $active_tab = 'form';
        
        ?>
            <h2 class="nav-tab-wrapper">  
                <a href="edit.php?post_type=product&page=pefw&tab=form" class="nav-tab <?php echo $active_tab == 'form' ? 'nav-tab-active' : ''; ?>"><?php _e("Enquiry Settings","wdm-product-enquiry");?></a>
		<a href="edit.php?post_type=product&page=pefw&tab=entry" class="nav-tab <?php echo $active_tab == 'entry' ? 'nav-tab-active' : ''; ?>"><?php _e("Enquiry Details","wdm-product-enquiry");?></a>
		<a href="edit.php?post_type=product&page=pefw&tab=contact" class="nav-tab <?php echo $active_tab == 'contact' ? 'nav-tab-active' : ''; ?>"><?php _e("Product Enquiry Ideas","wdm-product-enquiry");?></a>
		<a href="edit.php?post_type=product&page=pefw&tab=hireus" class="nav-tab <?php echo $active_tab == 'hireus' ? 'nav-tab-active' : ''; ?>"><?php _e("Hire Us","wdm-product-enquiry");?></a>
            </h2>  
	
    <?php if($active_tab === 'entry'){
	
	?>
	<div id='entry_dummy'>
	    <div class="layer_parent">
		    <div class="pew_upgrade_layer">
			<div class="pew_uptp_cont">
			    <p> <?php _e("This feature is available in the PRO version. Click below to know more.","wdm-product-enquiry");?></p>
			<a class="wdm_view_det_link" href="https://wisdmlabs.com/woocommerce-product-enquiry-pro/" target="_blank"> <?php _e("View Details","wdm-product-enquiry");?> </a>
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

<div class="col-1 wdm-abt" >
<p class="wdm-about" style="text-align:center">
 <?php _e("Product Enquiry Pro is one of WisdmLabs early plugins and a very successful one. With over ","wdm-product-enquiry");?><b class="wdm-color" ><?php _e("200 satisfied customers","wdm-product-enquiry");?></b> <?php _e("we continue to improve PEP and give the best to our customers. We stand by our products and make sure we give our customers what they are looking for with great quality and even better features. ","wdm-product-enquiry");?>
<br><br>
<b class="wdm-color" style="width: 100%; margin: 0px auto; text-align: center; font-size: 16px;"><?php _e("THIS IS WHERE WE NEED YOU! ","wdm-product-enquiry");?></b>
<br><br>
<?php _e("We need you, the users of PEP to ","wdm-product-enquiry");?> <b style="color:#961914;"><?php _e("pitch in your ideas  ","wdm-product-enquiry");?></b><?php _e("for the plugin. Based on the number of interested users, we will incorporate the feature and make it available at a minimal cost. ","wdm-product-enquiry");?>
<br>
</p>

</div>
<div class="clear"></div>
</fieldset>
</div>

<div class="wdm-container wdm-services-offered clearfix">
<?php global $current_user;
get_currentuserinfo();
?>
<ul class="wdm-services-list clearfix">
    <li class="wdm-services-item">
	<div class="wdm-services-icon wdm-custom-eq-form" ></div>
	<h3><?php _e("Customize Your Enquiry Form ","wdm-product-enquiry");?></h3>
	<p class="wdm-services-desc">
	    <?php _e("Flexibility to create your own fields within the enquiry form. ","wdm-product-enquiry");?>
	</p>
	<a class="wdm_upgrade_pro_link" href="https://wisdmlabs.com/woocommerce-product-enquiry-pro/" target="_blank"><?php _e("UPGRADE TO PRO","wdm-product-enquiry"); ?></a>
    </li>
    <li class="wdm-services-item">
    <div class="wdm-services-icon wdm-display-eq-button" ></div>
    <h3><?php _e("Display Enquiry Button On Shop ","wdm-product-enquiry");?></h3>
    <p class="wdm-services-desc">
    <?php _e("Allow visitors to enquire about your products directly from the shop page. ","wdm-product-enquiry");?>
    </p>
    <input type="button" class="wdm-services-button" value="Request Feature" />
    <div class="hide_class">
    <?php echo "<h4 class='wdm-req-title'>Please confirm your feature request</h4>"; ?>
    <form class="wdm-req-form display-enquiry-button-on-shop" >
    <br><small><?php _e("Confirm Email-id : ","wdm-product-enquiry");?></small>
    <input type="text" class="wdm-req-text" name="wdm-req-email" value="<?php echo $current_user->user_email ?>" />
    <input type="button" class="wdm-req-button" value="Send Request" name="request-feature" />
    <input type="hidden" class="id" name="id" value="display-enquiry-button-on-shop" />
    <div class="loading"></div>
    </form>
    <span class="wdm-close" ></span>
    </div>
    </li>
    <li class="wdm-services-item">
    <div class="wdm-services-icon wdm-create-cu-email" ></div>
    <h3><?php _e("Create a Custom Email Template ","wdm-product-enquiry");?></h3>
    <p class="wdm-services-desc">
    <?php _e("Style and create templates for your enquiry emails from your dashboard. ","wdm-product-enquiry");?>
    </p>
    <input type="button" class="wdm-services-button one" value="Request Feature" />
    <div class="hide_class">
    <?php echo "<h4 class='wdm-req-title'>Please confirm your feature request</h4>"; ?>
    <form class="wdm-req-form create-a-custom-email" >
    <br><small><?php _e("Confirm Email-id : ","wdm-product-enquiry");?></small>
    <input type="text" class="wdm-req-text" name="wdm-req-email" value="<?php echo $current_user->user_email ?>" />
    <input type="button" class="wdm-req-button" value="Send Request" name="request-feature" />
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
    <h3><?php _e("Hide or Replace Add-to-Cart Button ","wdm-product-enquiry");?></h3>
    <p class="wdm-services-desc">
    <?php _e("Replace the add-to-cart button with the enquiry button for your products. ","wdm-product-enquiry");?>
    </p>
    <a class="wdm_upgrade_pro_link" href="https://wisdmlabs.com/woocommerce-product-enquiry-pro/" target="_blank"><?php _e("UPGRADE TO PRO","wdm-product-enquiry"); ?></a>
    </li>
    <li class="wdm-services-item">
    <div class="wdm-services-icon wdm-analytics-eq" ></div>
    <h3><?php _e("Analytics For Your Enquiries ","wdm-product-enquiry");?></h3>
    <p class="wdm-services-desc">
    <?php _e("Get detailed analytics for your enquiries based on products and other attributes. ","wdm-product-enquiry");?>
    </p>
    <input type="button" class="wdm-services-button" value="Request Feature" />
    <div class="hide_class">
    <?php echo "<h4 class='wdm-req-title'>".__("Please confirm your feature request","wdm-product-enquiry")."</h4>"; ?>
    <form class="wdm-req-form Analytics-for-your-enquiry" >
    <br><small><?php _e("Confirm Email-id : ","wdm-product-enquiry");?></small>
    <input type="text" class="wdm-req-text" name="wdm-req-email" value="<?php echo $current_user->user_email ?>" />
    <input type="button" class="wdm-req-button" value="Send Request" name="request-feature" />
    <input type="hidden" class="id" name="id" value="Analytics-for-your-enquiry" />
    
    <div class="loading"></div>
    </form>
    <span class="wdm-close" ></span>
    </div>
    </li>
    <li class="wdm-services-item">
    <div class="wdm-services-icon wdm-newsletter-int" ></div>
    <h3><?php _e("Newsletter Integration With PEP ","wdm-product-enquiry");?></h3>
    <p class="wdm-services-desc">
    <?php _e("Get your newsletter plugin integrated seamlessly with PEP. ","wdm-product-enquiry");?>
    </p>
    <input type="button" class="wdm-services-button" value="Request Feature" />
    <div class="hide_class">
    <h4 class='wdm-req-title'><?php _e("Please confirm your feature request","wdm-product-enquiry");?></h4>
    <form class="wdm-req-form newsletter-integration-with-pep" >
    <br><small><?php _e("Confirm Email-id : ","wdm-product-enquiry");?></small>
    <input type="text" class="wdm-req-text" name="wdm-req-email" value="<?php echo $current_user->user_email ?>" />
    <input type="button" class="wdm-req-button" value="Send Request" name="request-feature" />
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
</div><!-- /.wdm-services-offered -->

<div class="wdm-container wdm-services-details">
    <fieldset>
    <!--<legend>Details</legend>-->
	<div class="wdm-details">
	    <h2 class="wdm-color" style="text-align: center;" ><?php _e("Get A Feature Developed in PEP ","wdm-product-enquiry");?></h2>
	    <hr>
	    <?php _e("Select a feature you want us to develop and leave us a note about it. We will get in touch with you and keep you posted on its progress. ","wdm-product-enquiry");?><br><br>
	    <b class="wdm-color"><?php _e("Need a custom feature developed in PEP ? ","wdm-product-enquiry");?></b>
	    <br><br><a href="https://wisdmlabs.com/contact-us/" target="_blank" class="wdm-contact-button" title="Wisdmlabs" ><?php _e("Contact Us ","wdm-product-enquiry");?></a>
	</div>
    </fieldset>
</div><!-- /.wdm-services-details -->
</div><!-- /.wdm-tab-container -->
<?php }
		elseif($active_tab === 'hireus'){ ?>
		<div class="wdm-tab-container">
		<div class="wdm-container">
		<fieldset class="wdm-plugin-customize">
		<h2 class="wdm-color"><?php _e("Plugin Development and Customization ","wdm-product-enquiry");?></h2><hr>
		<p><?php _e("Our area of expertise is WordPress plugins. We specialize in creating bespoke plugin solutions for your business needs. Our competence with plugin development and customization has been certified by WordPress biggies like WooThemes and Event Espresso. ","wdm-product-enquiry");?><br><br>
		<a class="wdm-contact-button" style="padding:8px 30px; margin-top:10px;" href="https://wisdmlabs.com/contact-us/" target="_blank"><?php _e("Contact Us ","wdm-product-enquiry");?></a>
		</p>
		</fieldset>
		<div class="wdm-container">
		<h2 style="text-align: center;" class="wdm-color"><?php _e("Our Premium Plugins ","wdm-product-enquiry");?></h2>
		<ul class="wdm-premium-plugins">
		    <li class="wdm-plugins-item first">
			<h3><?php _e("Customer Specific Pricing for WooCommerce ","wdm-product-enquiry");?></h3>
			<p style="text-align: center;">
			    <?php _e("This simple yet powerful plugin, allows you to set a different price for a WooCommerce product on a per customer basis. ","wdm-product-enquiry");?> <br><br>
			</p>
			<a class="wdm-contact-button wdm-know-more" href="https://wisdmlabs.com/woocommerce-user-specific-pricing-extension/" target="_blank"><?php _e("Know more ","wdm-product-enquiry");?> </a>
		    </li>
		    <li class="wdm-plugins-item">
			<h3><?php _e("WooCommerce Custom Product Boxes ","wdm-product-enquiry");?></h3>
			<p style="text-align: center;">
			    <?php _e("This plugin allows customers, to select and create their own product bundles, which can be purchased as a single product! ","wdm-product-enquiry");?>
			</p>
			<a class="wdm-contact-button wdm-know-more" href="https://wisdmlabs.com/assorted-bundles-woocommerce-custom-product-boxes-plugin/" target="_blank"><?php _e("Know more ","wdm-product-enquiry");?> </a>
		    </li>
		    <li class="wdm-plugins-item">
			<h3 style="min-height:54px;"><?php _e("Instagram WooCommerce Integration ","wdm-product-enquiry");?>  </h3>
			<p style="text-align: center;">
			    <?php _e("A perfect solution, to create collages using Instagram images, right from your WooCommerce store. ","wdm-product-enquiry");?>
			</p>
			<a class="wdm-contact-button wdm-know-more" href="https://wisdmlabs.com/instagram-woocommerce-integration-solution/" target="_blank"><?php _e("Know more ","wdm-product-enquiry");?> </a>
		    </li>
		    <li class="wdm-plugins-item last">
			<h3><?php _e("WooCommerce Moodle Integration","wdm-product-enquiry");?></h3>
			<p style="text-align: center;">
			    <?php _e("Want to sell Moodle Courses in your WooCommerce store? This plugin allows you to do the same and much more. ","wdm-product-enquiry");?>
			</p>
			<a class="wdm-contact-button wdm-know-more" href="https://wisdmlabs.com/woocommerce-moodle-integration-solution/" target="_blank"><?php _e("Know more ","wdm-product-enquiry");?> </a>
		    </li>
		</ul>
		<ul class="wdm-premium-plugins three-grids">
		    <li class="wdm-plugins-item first">
			<h3><?php _e("WooCommerce Scheduler","wdm-product-enquiry");?></h3>
			<p style="text-align: center;">
			    <?php _e("With the WooCommerce Scheduler Plugin, you can manage product availability as per your business needs.","wdm-product-enquiry");?>
			</p>
			<a class="wdm-contact-button wdm-know-more" href="https://wisdmlabs.com/woocommerce-scheduler-plugin-for-product-availability/" target="_blank"><?php _e("Know more ","wdm-product-enquiry");?> </a>
		    </li>
		    <li class="wdm-plugins-item">
			<h3><?php _e("WooCommerce Bookings: Availability Search Widget","wdm-product-enquiry");?></h3>
			<p style="text-align: center;">
			    <?php _e("An WooCommerce Bookings plugin extension, which allows customers to search for Available Bookings on requested dates.","wdm-product-enquiry");?>
			</p>
			<a class="wdm-contact-button wdm-know-more" href="https://wisdmlabs.com/woocommerce-bookings-availability-search-widget/" target="_blank"><?php _e("Know more ","wdm-product-enquiry");?> </a>
		    </li>
		    <li class="wdm-plugins-item last">
			<h3><?php _e("Empty Cart Timer for WooCommerce","wdm-product-enquiry");?></h3>
			<p style="text-align: center;">
			    <?php _e("Empty Cart Timer for WooCommerce deletes products from the cart after a certain predefined amount of expiration time. ","wdm-product-enquiry");?>
			</p>
			<a class="wdm-contact-button wdm-know-more" href="https://wisdmlabs.com/empty-cart-timer-woocommerce-plugin/" target="_blank"><?php _e("Know more ","wdm-product-enquiry");?> </a>
		    </li>
		</ul>
		</div>
		<div class="clear"></div>
		<fieldset class="wdm-bouquet-of-services">
		<h2 class="wdm-color"><?php _e("Entire Array of Services ","wdm-product-enquiry");?></h2><hr>
		<p><?php _e("We specialize in WordPress website development and customization with an entire range of services under our belt. We have expertise in domains such as eCommerce, LMS, Event Management System, etc. Explore our services now and cater to all your WordPress requirements under one roof. ","wdm-product-enquiry");?><br><br>
		<a class="wdm-contact-button" style="padding:8px 30px; margin-top:10px;" href="https://wisdmlabs.com/services/" target="_blank"><?php _e("View Our Services ","wdm-product-enquiry");?></a>
		</p>
		</fieldset>
		</div>
		</div>
		<?php }
    else{
	$pro = "<span title='Pro Feature' class='pew_pro_txt'>".__('[Available in PRO]','wdm-product-enquiry')."</span>";
    ?>
     <form name="ask_product_form" id="ask_product_form" method="POST" action="options.php" style="background: #fff; padding: 10px 15px 0 15px;">
        <?php
            settings_fields('wdm_form_options');
            $default_vals =   array('show_after_summary'=>1);
            $form_data = get_option( 'wdm_form_data', $default_vals);
            ?>
      <div id="ask_abt_product_panel">
	<fieldset>
	    <?php echo '<legend>'. __("Emailing Information",'wdm-product-enquiry').'</legend>'; ?>
	<div class="fd">
	<div class='left_div'>
            <label for="wdm_user_email"><?php _e(" Recipient's Email ","wdm-product-enquiry");?> </label> 
	</div>
	<div class='right_div'>
	    <input type="text" class="wdm_wpi_input wdm_wpi_text email" name="wdm_form_data[user_email]" id="wdm_user_email" value="<?php echo empty($form_data['user_email']) ? get_option('admin_email') : $form_data['user_email'];?>" />
	    <span class='email_error'> </span>
	</div>
	<div class='clear'></div>
	</div >
	<div class="fd">
	<div class='left_div'>
	    <label for="wdm_default_sub"> <?php _e("Default Subject ","wdm-product-enquiry");?> </label>
	</div>
	<div class='right_div'>		
	    <input type="text" class="wdm_wpi_input wdm_wpi_text" name="wdm_form_data[default_sub]" id="wdm_default_sub" value="<?php echo empty($form_data['default_sub']) ? __('Enquiry for a product from ','wdm-product-enquiry').get_bloginfo('name') : $form_data['default_sub'];?>"  />
        <br>
	    <?php echo '<em>'.__(' Will be used if the customer does not enter a subject','wdm-product-enquiry').'</em>'; ?>
	</div>
	<div class='clear'></div>
	</div>
        </fieldset>
	<br/>
	    <fieldset>
	
	 <?php echo '<legend>'. __("Form Options",'wdm-product-enquiry').'</legend>'; ?>
	<div class="fd">
			<div class='left_div'>
            <label for="custom_label"> <?php _e("Button-Text for enquiry button","wdm-product-enquiry");?> </label> 
	    </div>
			<div class='right_div'>
            <input type="text" class="wdm_wpi_input wdm_wpi_text" name="wdm_form_data[custom_label]" value="<?php echo empty($form_data['custom_label']) ? __('Make an enquiry for this product','wdm-product-enquiry') : $form_data['custom_label'];?>" id="custom_label"  />
        </div>
			<div class='clear'></div>
		</div>
	<div class="fd">
			<div class='left_div'>
	    <label> <?php _e("Enquiry Button Location","wdm-product-enquiry");?> </label>
	    </div>
			<div class='right_div'>
			    
            
	   <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" name="wdm_form_data[show_after_summary]" value="1" <?php echo (isset($form_data["show_after_summary"]) ? "checked" : "" );?> id="show_after_summary" /> 
	    <label for="show_after_summary"><?php _e(" After single product summary ","wdm-product-enquiry");?></label>
	    <br />
	    <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" name="wdm_form_data[show_at_page_end]" value="1" <?php echo (isset($form_data["show_at_page_end"]) ? "checked" : "" );?> id="show_at_page_end" />
		   
	   
	    <label for="show_at_page_end"> <?php _e("At the end of single product page ","wdm-product-enquiry");?></label>
	    
        </div>
	    <div class='clear'></div>
	</div>
        
	<div class="fd">
	    <div class='left_div'>
		<label> <?php _e("Enable sending an email copy","wdm-product-enquiry");?> </label>
		 </div>
	    <div class='right_div'>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" name="wdm_form_data[enable_send_mail_copy]" value="1" <?php echo (isset($form_data["enable_send_mail_copy"]) ? "checked" : "" );?> id="enable_send_mail_copy" />
	  
	    <label for="enable_send_mail_copy"><?php _e(" Enable option on the enquiry form to send an email copy to customer","wdm-product-enquiry");?> </label>
        </div>
			<div class='clear'></div>
	</div>
	
	<div class="fd">
	    <div class='left_div'>
	    <label for="link">
		<?php _e("Display 'Powered by WisdmLabs'","wdm-product-enquiry");?>
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
		<?php _e("Display 'Telephone Number Field'","wdm-product-enquiry");?>
	    </label>
	    </div>
	    <div class='right_div'>
		<input type="checkbox" disabled class="wdm_wpi_input wdm_wpi_checkbox" value="1" id="enable_telephone_no_txtbox" />
		     <?php echo $pro; ?>
	    </div>
	<div class="clear"></div>
	</div>
	
	<div class="fd">
	    <div class='left_div'>
	    <label for="enable_telephone_no_txtbox">
		<?php _e("Show enquiry button only when product is out of stock","wdm-product-enquiry");?>
	    </label>
	    </div>
	    <div class='right_div'>
		<input type="checkbox" disabled class="wdm_wpi_input wdm_wpi_checkbox" value="1" id="enable_for_out_stock" />
		     <?php echo $pro; ?>
	    </div>
	<div class="clear"></div>
	</fieldset>
	    <br>
	    <fieldset>
		    <legend><?php _e("Styling Options","wdm-product-enquiry");?> </legend>
			<div class='fd'>
				    <div class='left_div'>
				    <label for="enable_telephone_no_txtbox">
				    <?php _e("Custom Styling","wdm-product-enquiry");?>
				   </label>
				    </div>
				    <div class='right_div'>
				    <input type="radio" class="wdm_wpi_input wdm_wpi_checkbox" value="theme_css" name="wdm_radio_data" id="theme_css" checked />
																					    <em> <?php _e("Use Activated Theme CSS","wdm-product-enquiry");?> </em><br>
										 
				    <input type="radio" class="wdm_wpi_input wdm_wpi_checkbox" value="manual_css" name="wdm_radio_data" id="manual_css" />
					
				    <em><?php _e("Manually specify color settings","wdm-product-enquiry");?></em>
				  </div>
				    <div class="clear"></div>
			</div>
	    </fieldset>  
	   <br />
	   <div name="Other_Settings" id="Other_Settings" style="display: none;">
	    <fieldset style="padding: 0;">
		<?php echo '<legend>'. __("Specify CSS Settings ",'wdm-product-enquiry').'</legend>';?>
	    <br />
		<div class="layer_parent">
		    <div class="pew_upgrade_layer">
			<div class="pew_uptp_cont">
			    <p><?php _e(" This feature is available in the PRO version. Click below to know more. ","wdm-product-enquiry");?></p>
			<a class="wdm_view_det_link" href="https://wisdmlabs.com/woocommerce-product-enquiry-pro/" target="_blank"><?php _e("View Details ","wdm-product-enquiry");?> </a>
			</div>
		    </div>
		    <img src="<?php echo plugins_url('img/buttons-css.png',  __FILE__);?>" />
		</div>
	    </fieldset>
	    </div>
           <br>
	    <div id="available">
	    <div class="layer_parent">
		    <div class="pew_upgrade_layer">
		       <div class="pew_uptp_cont">
			    <p><?php _e(" This feature is available in the PRO version. Click below to know more. ","wdm-product-enquiry");?></p>
			<a class="wdm_view_det_link" href="https://wisdmlabs.com/woocommerce-product-enquiry-pro/" target="_blank"><?php _e("View Details ","wdm-product-enquiry");?> </a>
			</div>
		   </div>	 
	    <fieldset>
		
		    <legend><?php _e("Enable/Disable Add to Cart for all Products","wdm-product-enquiry");?> </legend>
	     	    <img  src="<?php echo plugins_url('/img/img3.png', __FILE__); ?>" style='width:1048px;height:59px'/>
         </fieldset>
	    <br />
	    <br>
	 <fieldset>
		    <legend><?php _e("Enable/Disable PEP for all Products","wdm-product-enquiry");?> </legend>
			<img src="<?php echo plugins_url('/img/img2.png', __FILE__); ?>" style='width:953px;height:47px'/>
	  </fieldset>  
	   <br />
	   <br>
	    <fieldset>
		    <legend><?php _e("Redirect Page URL","wdm-product-enquiry");?> </legend>
	     <br>	
	     <img  src="<?php echo plugins_url('/img/img1.png', __FILE__); ?>" style='width:735px;height:68px'/>
             <br/> 
	    </fieldset>
    
	    </div>
	</div>
	   
	 <p>
            <input type="submit" class="wdm_wpi_input button-primary" value="Save Changes" id="wdm_ask_button" />
        </p>
        
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

add_action('wp_ajax_wdm_send','contact_email');
add_action('wp_ajax_nopriv_wdm_send','contact_email');
function contact_email(){
         
      if(isset( $_POST['security'])&&check_ajax_referer( 'enquiry_action', 'security',false))
	{
	$form_data = get_option( 'wdm_form_data');
        $name = isset($_POST['wdm_name']) ? $_POST['wdm_name'] : "";
	$to = isset($_POST['wdm_emailid']) ? $_POST['wdm_emailid'] : "";
	$subject = (isset($_POST['wdm_subject'])&&!empty($_POST['wdm_subject'])) ? $_POST['wdm_subject'] :$form_data['default_sub'];
	$message = isset( $_POST['wdm_enquiry']) ?  $_POST['wdm_enquiry'] : "";
	$cc = isset($_POST['wdm_cc']) ? $_POST['wdm_cc'] : "";
	$product_name=isset($_POST['wdm_product_name']) ? $_POST['wdm_product_name'] : "";
	$product_url = isset($_POST['wdm_product_url']) ? $_POST['wdm_product_url'] : "";
	$admin_email=get_option('admin_email');
	$site_name =get_bloginfo('name');
	$recipient_email=(!empty($form_data['user_email']))?$form_data['user_email']:"";
	if(empty($recipient_email)){
	   $recipient_email=$admin_email;
	}
	//$group_emails = array($recipient_email,$admin_email);
	$headers = "Reply-To: $to \n";
        if ($cc == 1) {
		$headers .= "CC: $to\n";
	}
	
	//UTF-8
	if (function_exists('mb_encode_mimeheader')) {
		$subject = mb_encode_mimeheader($subject, "UTF-8", "B", "\n");
	}
	else {
		// you need to enable mb_encode_mimeheader or risk 
		// getting emails that are not UTF-8 encoded
	}
	$headers .= "MIME-Version: 1.0\n";
	$headers .= "Content-type: text/html; charset=utf-8\n";
	$headers .= "Content-Transfer-Encoding: quoted-printable\n";
	
	// Set and wordwrap message body
	$body = __('Product Enquiry from','wdm-product-enquiry')." <strong>". $site_name . "</strong> <br /><br />";
	$body .= "<strong>".__('Product Name:','wdm-product-enquiry')."</strong> '". $product_name ."'<br /><br />";
	$body .= "<strong>".__('Product URL:','wdm-product-enquiry')."</strong> ". $product_url ."<br /><br />";
	$body .= "<strong>".__('Customer Name:','wdm-product-enquiry')."</strong> ". $name ."<br /><br />";
	$body .= "<strong>".__('Customer Email:','wdm-product-enquiry')."</strong> ". $to ."<br /><br />";
	$body .= "<strong>".__('Message:','wdm-product-enquiry')."</strong> <br />". $message;
	$body = wordwrap($body, 100);
	$send_mail=wp_mail( $recipient_email, $subject, $body, $headers ); //or  die(__("Unfortunately, a server issue prevented delivery of your message.","wdm-product-enquiry"));
	
	if($send_mail)
	{
	    _e("Enquiry was sent succesfully","wdm-product-enquiry");
	    
	}
	else{
	   
	   _e("Unfortunately, a server issue prevented delivery of your message.","wdm-product-enquiry");
	}
	die();
	}
	
	die(__("Unauthorized access.","wdm-product-enquiry"));
      
    }

add_action('admin_init', 'reg_form_settings' );

function reg_form_settings()
{
    //register plugin settings
    register_setting('wdm_form_options','wdm_form_data');
}

add_action( 'admin_footer', 'wdm_action_javascript' ); // Write our JS below here

function wdm_action_javascript() {
if(isset($_GET["page"]) && $_GET["page"] == "pefw"){ ?>
<script type="text/javascript" >
jQuery(document).ready(function($) {
jQuery(".wdm-req-button").click(function() {
var data = {};
data.email = $(this).siblings(".wdm-req-text").val();
data.id = $(this).siblings( ".id" ).val();
data.updates = $(this).siblings(".wdm-req-confirm").prop("checked");
data.action = "wdm_pe_action";
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
</script>
<?php
}
}

add_action( 'wp_ajax_wdm_pe_action', 'wdm_pe_action_callback' );

function wdm_pe_action_callback() {
global $wpdb; // this is how you get access to the database
$email = $_POST['email'];
$id = $_POST['id'];
$updates = $_POST['updates'];
$subscribes_message = "";
$subscribes_message_client = "";
if($updates == "true") {
$subscribes_message = __("And Congrats! They have subscribed to your newsletter too!",'wdm-product-enquiry');
$subscribes_message_client =__( "We'll keep you updated with the latest developments.",'wdm-product-enquiry');
}
else {
//$subscribes_message = "Oh shoot! They haven't subscribed.";
//$subscribes_message_client = "To stay up-to-date with the latest developments, do subscribe to our newsletter!";
}
$message =__("
Hi,A feature request has been made for Product enquiry free, by $email.Requested Feature: $id
$subscribes_message
Thanks and Regards,This is an automated mail, sent by WisdmLabs","wdm-product-enquiry");

$message_client = printf(__("Hi there,Thank you for requesting the %s feature for Product Enquiry for WooCommerce.We will keep you updated with the latest developments.Thanks and Regards,WisdmLabs","wdm-product-enquiry"),$id);

//echo $email . " id is --:" . $id;
wp_mail( 'support@wisdmlabs.com','PE Feature Request', $message);
//mail for client
$client_header[] = 'From: WisdmLabs <donotreply@wisdmlabs.com>';
wp_mail( $email,'Your Feature Request Has Been Sent!', $message_client, $client_header );
echo "." . $id;
die(); // this is required to terminate immediately and return a proper response
}
?>