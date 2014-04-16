<?php
/*
Plugin Name: Product Enquiry for WooCommerce
Description: Allows prospective customers or visitors to make enquiry about a product, right from within the product listing page.
Version: 0.3
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
        add_action('woocommerce_single_product_summary', 'ask_about_product');
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
    add_action('woocommerce_single_product_summary', 'ask_about_product');
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
        $wdm_translation_array = array( 'product_name'=>get_the_title(), 'contact_url' => plugins_url("data/contact.php", __FILE__), 'form_dataset' => $form_data, 'admin_email' => get_option('admin_email'), 'site_name' => get_bloginfo('name'));
        wp_localize_script( 'wdm-contact', 'object_name', $wdm_translation_array );

}

add_action('admin_menu', 'create_ask_product_menu');

function create_ask_product_menu()
{
    //create a submenu under Woocommerce 'Products' menu
    add_submenu_page('edit.php?post_type=product', 'Product Enquiry', 'Product Enquiry', 'manage_options', 'pefw', 'add_ask_product_settings' );
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
        		<a class='wdm_upgrade_pro_link' href='http://wisdmlabs.com/woocommerce-product-enquiry-pro/' target='_blank'>UPGRADE TO PRO </a>
            </div>
        </div>
        <div class="wdm-right-content">	
        	<div class="wdm-features">
            	<h3 class='wdm_feature_heading'>New Features In Pro Version</h3>
               
			   <div class='wdm-feature-list'>
				
						<div class="wdm-feature">
							<span class="wdmiconfilter"></span>
							<p>Filter enquires</p>
						</div>
					
						<div class="wdm-feature">
							<span class="wdmiconexpand"></span>
							<p>Responsive</p>
						</div>
																					
						<div class="wdm-feature">
							<span class="wdmiconpaint-format"></span>
							<p>Custom styling</p>
						</div>
					
					<div class="clear"></div>
				</div>
				
				<div class='wdm-feature-list'>
					
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
				</div>
				
				<div class="wdm_coupon">
						<p>USE THIS COUPON CODE AND GET A 25% DISCOUNT*</p>
							<div>
								<h1>PEPDISCOUNT25</h1>
						</div>
						<p>*Valid till 31st May 2014</p>
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
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" name="wdm_form_data[show_after_summary]" value="1" <?php checked( 1, $form_data["show_after_summary"] );?> id="show_after_summary" />
	    
	    <label for="show_after_summary"> After single product summary </label>
	    <br />
	    <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" name="wdm_form_data[show_at_page_end]" value="1" <?php checked( 1, $form_data["show_at_page_end"] );?> id="show_at_page_end" />
	   
	    <label for="show_at_page_end"> At the end of single product page </label>
	    
        </div>
	    <div class='clear'></div>
	</div>
        
	<div class="fd">
	    <div class='left_div'>
		<label> Enable sending an email copy </label>
		 </div>
	    <div class='right_div'>
            <input type="checkbox" class="wdm_wpi_input wdm_wpi_checkbox" name="wdm_form_data[enable_send_mail_copy]" value="1" <?php checked( 1, $form_data["enable_send_mail_copy"] );?> id="enable_send_mail_copy" />
	  
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
    
    include_once('wisdm_sidebar/wisdm_sidebar.php');
    pew_create_wisdm_sidebar($plugin_name, $wdm_plugin_slug);
}
?>
