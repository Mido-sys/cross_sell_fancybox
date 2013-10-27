<script type="text/javascript"><!--//

jQuery(document).ready(function() {
	<?php if (CSFB_LOAD_SELECTORS != '') { ?>
	jQuery("<?php echo CSFB_SELECTORS; ?>").each(function() {
		jQuery(this).attr('href', jQuery(this).attr('href') + ' <?php echo CSFB_LOAD_SELECTORS; ?>');
		//alert(jQuery(this).attr('href')); 
	});
	<?php } ?>
	jQuery("<?php echo CSFB_SELECTORS; ?>").fancybox({	
    'autoSize'      : true,
    'autoResize'    : true,
    'autoCenter'    : false,
    'closeClick'    : true,
    'openEffect'    : 'elastic',
    'closeEffect'   : 'elastic',
    'closeClick'    : false,
    'type'          : 'ajax',
    'scrollOutside' : false,                                                                                                      
    'scrolling'     : 'auto',
    <?php if (AATC_STATUS == 'true') { ?>
		afterShow: function() {
			// do an AJAX get to the current page to avoid navigation issues
			jQuery.get('ajax/product_info.php?products_id=<?php echo (int)$_GET['products_id']; ?>');
			// bind AJAX add to cart functionality to form submit
			jQuery('.fancybox-wrap form[name="cart_quantity"]').submit(function() {
 				var returnValue = performAATC(jQuery(this));
 				jQuery.fancybox.close(true);
 				return returnValue;
			});
		}    
    <?php } ?>
	});
});
//--></script>