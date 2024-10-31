
<?php
/**
 *  Render "Bulk" form
 *
 * @return string Template
 */
function mylerz_display_bulkCancelAWB_button()
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
                $('.page-title-action').first().after("<button type= 'button' class=' page-title-action' style='margin-left:15px;' id='bulkCancelAWB'><?php echo esc_html__('Bulk Cancel AWB', 'mylerz'); ?> </button>");

                // let warehouses = await validate()

                $('#bulkCancelAWB').click(async () => {
                    try {
                        $(".loader").css("display", "block");
                        $("#bulkPrintAWB").prop("disabled",true);
                        $("#createPickup").prop("disabled",true);
                        $("#bulkFulfillment").prop("disabled", true);
                        $("#bulkCancelAWB").prop("disabled", true);

                        await bulkCancelAWB();

                        $(".loader").css("display", "none");
                        $("#bulkPrintAWB").prop("disabled",false);
                        $("#createPickup").prop("disabled",false);
                        $("#bulkFulfillment").prop("disabled", false);
                        $("#bulkCancelAWB").prop("disabled", false);
                    } catch (error) {
                        $(".loader").css("display", "none");
                        $("#bulkPrintAWB").prop("disabled",false);
                        $("#createPickup").prop("disabled",false);
                        $("#bulkFulfillment").prop("disabled", false);
                        $("#bulkCancelAWB").prop("disabled", false);

                        alert("Something Went Wrong!")

                        console.log(error);
                    }
                })
            })());

            var bulkCancelAWB = async () => {

                console.log("in bulkCancelAWB");
                let selectedToCancel = [];
                let selected = [];

                $('.status-wc-created input:checked').each(function() {
                    selectedToCancel.push($(this).val());
                });
                $('.status-wc-fulfilled input:checked').each(function() {
                    selectedToCancel.push($(this).val());
                });
                $('.status-wc-mylerz-return input:checked').each(function() {
                    selectedToCancel.push($(this).val());
                });
                // WC new versions
                $('.status-created input:checked').each(function() {
                    selectedToCancel.push($(this).val());
                });
                $('.status-fulfilled input:checked').each(function() {
                    selectedToCancel.push($(this).val());
                });
                $('.status-mylerz-return input:checked').each(function() {
                    selectedToCancel.push($(this).val());
                });
                $('tr.type-shop_order input:checked').each(function() {
                    selected.push($(this).val());
                });


                if (selectedToCancel.length === 0 || selectedToCancel.length !== selected.length) {

                    alert("<?php echo esc_html__('All Selected Orders must have pickup order created, fulfilled or returned by mylerz'); ?>");

                } else {

                    console.log("selectedToCancel --->", selectedToCancel);
                    console.log("selected --->", selected);
                    console.log("cond --->", (selectedToCancel.length === selected.length));


                    let postData = {
                        action: 'mylerzBulkCancelAWB',
                        ordersIds: selectedToCancel
                    }
                    console.log("postData -->", postData);
                    await jQuery.post(ajaxurl, postData, function(response) {
                        console.log("end Request ---->", response);

                        let result = JSON.parse(response);

                        if (result["Status"] == "Failed") {
                            alert(result["Error"]);
                            window.location.reload();
                        } else {
                            alert("Selected Orders Cancelled Successfully")

                            window.location.reload();
                        }

                        $(".loader").css("display", "none");

                    });

                    console.log("last Line");
                }
            }

        })(jQuery);
    </script>
<?php
} ?>
