<?php
/**
 *  Render "Bulk Return" form
 *
 * @return string Template
 */
function mylerz_display_bulkReturn_button()
{
    $get_userdata = get_userdata(get_current_user_id());
    if (
        !$get_userdata->allcaps['edit_shop_order'] || !$get_userdata->allcaps['read_shop_order'] || !$get_userdata->allcaps['edit_shop_orders'] || !$get_userdata->allcaps['edit_others_shop_orders']
        || !$get_userdata->allcaps['publish_shop_orders'] || !$get_userdata->allcaps['read_private_shop_orders']
        || !$get_userdata->allcaps['edit_private_shop_orders'] || !$get_userdata->allcaps['edit_published_shop_orders']
    ) {
        return false;
    }
?>
    <div class="loader" style="display: none;"></div>

    <script type="text/javascript">
        jQuery.noConflict();
        (function($) {
            $(document).ready((async () => {
                $('.page-title-action').first().after("<button type= 'button' class=' page-title-action' style='margin-left:15px;' id='bulkReturn'><?php echo esc_html__('Bulk Return by Mylerz', 'mylerz'); ?> </button>");

                $('#bulkReturn').click(async () => {

                    try {
                        $(".loader").css("display", "block");
                        $("#bulkPrintAWB").prop("disabled",true);
                        $("#createPickup").prop("disabled",true);
                        $("#bulkFulfillment").prop("disabled", true);
                        $("#bulkCancelAWB").prop("disabled", true);
                        $("#bulkReturn").prop("disabled", true);

                        await bulkReturn();

                        $(".loader").css("display", "none");
                        $("#bulkPrintAWB").prop("disabled",false);
                        $("#createPickup").prop("disabled",false);
                        $("#bulkFulfillment").prop("disabled", false);
                        $("#bulkCancelAWB").prop("disabled", false);
                        $("#bulkReturn").prop("disabled", false);
                    } catch (error) {
                        $(".loader").css("display", "none");
                        $("#bulkPrintAWB").prop("disabled",false);
                        $("#createPickup").prop("disabled",false);
                        $("#bulkFulfillment").prop("disabled", false);
                        $("#bulkCancelAWB").prop("disabled", false);
                        $("#bulkReturn").prop("disabled", false);

                        alert("Something Went Wrong!")

                        console.log(error);
                    }
                })

            })());


            var bulkReturn = async () => {
                let selectedToReturn = [];
                let selected = [];

                $('.status-wc-refunded input:checked').each(function() {
                    selectedToReturn.push($(this).val());
                });
                //WC new Versions
                $('.status-refunded input:checked').each(function() {
                    selectedToReturn.push($(this).val());
                });
                $('tr.type-shop_order input:checked').each(function() {
                    selected.push($(this).val());
                });



                console.log("selected --->", selected);

                if (selected.length === 0) {

                    alert("<?php echo esc_html__('Select orders, please'); ?>");

                } else if (selectedToReturn.length === 0 || selectedToReturn.length !== selected.length) {
                    alert("<?php echo esc_html__('All Selected Orders must have refunded status'); ?>");
                } else {

                    let postData = {
                        action: 'mylerzBulkReturnOrdersById',
                        ordersIds: selected,
                    }
                    await jQuery.post(ajaxurl, postData, function(response) {
                        let result = JSON.parse(response);


                        console.log("end Request ---->", result);

                        if (result["Status"] == "Failed") {
                            alert(result["Message"])
                        } else {
                            alert("Selected Orders Return Created Successfully")

                            window.location.reload();
                        }
                    });

                }

            }
        })(jQuery);
    </script>
<?php
} ?>
