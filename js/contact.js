
jQuery(function($) {
    
     dialog = $( "#contact-form" ).dialog({
     autoOpen: false,
     height: 'auto',
     width: 'auto',
     modal: true,
     resizable: false,
     //draggable: false,
   //opacity:0.75,
     show: function() {$(this).fadeIn(300);},
     hide:function() {$(this).fadeOut(300);},
     });
     $( "#enquiry input.contact" ).click(function() {
          dialog.dialog( "open" );
	//  $("#contact-form").closest(".ui-dialog").css("position","fixed");
     });
      $( "#cancel" ).click(function() {
       dialog.dialog( "close" );
      });
   
     $('#send-btn').click(function() {
        //e.preventDefault();
      
       $('#enquiry-form').validate(
       {
        errorElement: "div",
        //onkeyup: false,
       // onfocusout: false,
	rules:
	    {
               wdm_customer_name:{
	       required: true,
                  },
                  
               wdm_customer_email:{
		   required:true,
	           email:true
			},
               wdm_enquiry:{
               required: true,
               minlength:10,
             },
	       agree:"required"
             },
            
       messages:{
	           
		wdm_customer_name:object_name.wdm_customer_name,
                wdm_customer_email:object_name.wdm_customer_email,
                wdm_enquiry:object_name.wdm_enquiry,
	       },
              
               errorPlacement: function(error, element) {
                        error.appendTo("div#errors");
               },
   submitHandler:function(form)
               {
                   
                    var name=$("[name='wdm_customer_name']").val();
                    var emailid=$("[name='wdm_customer_email']").val();
                    var subject=$("[name='wdm_subject']").val();
                    var enquiry=$("[name='wdm_enquiry']").val();
                    var cc=$("[name='cc']").is(':checked') ? 1 : 0;
                    var product_name=$("#wdm_product_name").html();
                    var product_url=window.location.href;
                    var security=$("[name='product_enquiry']").val();
                    dialog.dialog( "close" );
                    $( "#loading" ).dialog({
                              create: function( event, ui ) {
                                             var dialog = $(this).closest(".ui-dialog");
                                             /*dialog.find(".ui-dialog-titlebar-close").appendTo(dialog)
                                                   .css({
                                                     position: "absolute",
                                                     top: 0,
                                                     right: 0,
                                                     margin: "3px"
                                                   });*/
                                   dialog.find(".ui-dialog-titlebar").remove();},
                              resizable: false,
                              width:'auto',
                              height:'auto',
                              modal: true,
                              //draggable: false
                        });
                    $.ajax({
                     url: object_name.ajaxurl,
                     type:'POST',
                     data: {action:'wdm_send',security:security,wdm_name:name,wdm_emailid:emailid,wdm_subject:subject,wdm_enquiry:enquiry,wdm_cc:cc,wdm_product_name:product_name,wdm_product_url:product_url},
                     success: function(response) {
                     $( "#send_mail" ).hide();
                     $( "#loading" ).text(response);
                     $( "#loading" ).dialog( "option", "buttons", {"OK": function() { $(this).dialog("close"); } });
                     }
                       });
                     
                     form.reset();
               }
      });
     
  });
     
      $(".ui-dialog").addClass("wdm-enquiry-modal");
     
     
 });
