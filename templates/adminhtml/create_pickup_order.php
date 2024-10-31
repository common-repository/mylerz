
<?php
/**
 *  Render "Bulk" form
 *
 * @return string Template
 */
function mylerz_display_create_pickup_order_button()
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
                $('.page-title-action').first().after("<button type= 'button' class=' page-title-action' style='margin-left:15px;' id='createPickup'><?php echo esc_html__('Create PickupOrder', 'mylerz'); ?> </button>");

                // let warehouses = await validate()

                $('#createPickup').click(async () => {
                    try {
                        $(".loader").css("display", "block");
                        $("#bulkPrintAWB").prop("disabled",true);
                        $("#createPickup").prop("disabled",true);
                        $("#bulkFulfillment").prop("disabled", true);
                        $("#bulkCancelAWB").prop("disabled", true);

                        await createPickupOrder();

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



            var validate = async () => {

                let postData = {
                    action: 'mylerzValidateAndGenerateNewToken',
                }

                try {

                    let warehousesArray = await jQuery.post(ajaxurl, postData, function(response) {
                        let result = JSON.parse(response);
                        console.log("Validate Request ---->", result);


                        if (result["Status"] == "Failed") {
                            alert(result["Message"])
                        } else {
                            // let warehouseDropDown = $('#warehousedropdown')
                            let warehouses = result["Warehouses"]
                            $('#warehousedropdown').append(new Option("Select Warehouse", "", true))
                            warehouses.forEach(warehouse => {
                                $('#warehousedropdown').append(new Option(warehouse, warehouse, ))

                            });
                            return result
                        }
                    });

                    return JSON.parse(warehousesArray)["Warehouses"];

                } catch (error) {
                    console.log(error);
                }

            }

            var createPickupOrder = async () => {


                console.log("in createPickupOrder");
                let selectedFulfilled = [];
                let selected = [];

                $('.status-wc-fulfilled input:checked').each(function() {
                    selectedFulfilled.push($(this).val());
                });
                //WC new versions
                $('.status-fulfilled input:checked').each(function() {
                    selectedFulfilled.push($(this).val());
                });
                $('tr.type-shop_order input:checked').each(function() {
                    selected.push($(this).val());
                });


                if (selectedFulfilled.length === 0 || selectedFulfilled.length !== selected.length) {

                    alert("<?php echo esc_html__('All Selected Orders must be fulfilled by mylerz'); ?>");

                } else {

                    console.log("selectedFulfilled --->", selectedFulfilled);
                    console.log("selected --->", selected);
                    console.log("cond --->", (selectedFulfilled.length === selected.length));


                    let postData = {
                        action: 'mylerzCreatePickupOrder',
                        ordersIds: selectedFulfilled
                    }
                    await jQuery.post(ajaxurl, postData, function(response) {
                        console.log("end Request ---->", response);

                        let result = JSON.parse(response);

                        if (result["Status"] == "Failed") {
                            alert(result["Error"])
                        } else {
                            alert("Pickup Order Created Successfully")

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
