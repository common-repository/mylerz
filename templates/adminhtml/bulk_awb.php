<?php
/**
 *  Render "Bulk" form
 *
 * @return string Template
 */
function mylerz_display_bulkPrintAWB_button()
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
    <div id="myModal" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <!-- <button onclick="printJS('canvasDiv', 'html')">Print</button> -->
            <span class="close">&times;</span>
            <div id="canvasDiv">

            </div>
        </div>
    </div>

    <div class="loader" style="display: none;"></div>


    <script type="text/javascript">
        jQuery.noConflict();
        (function($) {
            $(document).ready(function() {
                $('.page-title-action').first().after("<button type='button' class=' page-title-action' style='margin-left:15px;' id='bulkPrintAWB'><?php echo esc_html__('Bulk Print AWB', 'mylerz'); ?> </button>");

                $('#bulkPrintAWB').click(async () => {
                    console.log("OnClick print awb");

                    try {
                        $(".loader").css("display", "block");
                        $("#bulkPrintAWB").prop("disabled",true);
                        $("#createPickup").prop("disabled",true);
                        $("#bulkFulfillment").prop("disabled", true);
                        $("#bulkCancelAWB").prop("disabled", true);


                        await bulkPrintAWB();

                        $("#bulkPrintAWB").prop("disabled",false);
                        $("#createPickup").prop("disabled",false);
                        $("#bulkFulfillment").prop("disabled", false);
                        $(".loader").css("display", "none");
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

                });

                $('.close').first().click(async () => {
                    console.log("close clicked");
                    $("#myModal").hide()
                });


            });
            function _base64ToArrayBuffer(base64) {
            var binary_string = window.atob(base64);
            var len = binary_string.length;
            var bytes = new Uint8Array(len);
            for (var i = 0; i < len; i++) {
                bytes[i] = binary_string.charCodeAt(i);
            }
            return bytes.buffer;
        }
             var mergeAllPDFs = async (awbs) =>{

                const pdfDoc = await PDFLib.PDFDocument.create();
                const numDocs = awbs.length;

                for(var i = 0; i < numDocs; i++) {

                    // const donorPdfBytes = _base64ToArrayBuffer(awbs[i]);
                    // const donorPdfBytes = new Uint8Array(awbs[i]);
                    const donorPdfBytes = Uint8Array.from(atob(awbs[i]), c => c.charCodeAt(0))
                    const donorPdfDoc = await PDFLib.PDFDocument.load(donorPdfBytes);
                    const docLength = donorPdfDoc.getPageCount();
                    for(var k = 0; k < docLength; k++) {
                        const [donorPage] = await pdfDoc.copyPages(donorPdfDoc, [k]);
                        //console.log("Doc " + i+ ", page " + k);
                        pdfDoc.addPage(donorPage);
                    }
                }

                const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
                console.log(pdfDataUri);

                // strip off the first part to the first comma "data:image/png;base64,iVBORw0K..."
                var data_pdf = pdfDataUri.substring(pdfDataUri.indexOf(',')+1);

                printJS({printable: data_pdf, type: 'pdf', base64: true})
            }



            var bulkPrintAWB = async () => {

                console.log("in printAWB");
                let selectedFulfilled = [];
                let selected = [];

                $('.status-wc-fulfilled input:checked').each(function() {
                    selectedFulfilled.push($(this).val());
                });
                // WC new Versions
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
                        action: 'mylerzBulkPrintAWB',
                        ordersIds: selectedFulfilled
                    }
                    await jQuery.post(ajaxurl, postData, function(response) {
                        console.log("end Request ---->", response);
                        let awbList = JSON.parse(response).AWBList
                        awbList.map((awb, index) => {
                            $('#canvasDiv').append(`<canvas id='canvas_${index}' style="width:100%;"></canvas>`)
                            viewAWB(awb, `canvas_${index}`)
                            return awb
                        })

                        mergeAllPDFs(awbList)


                        $("#myModal").show()
                    });


                    console.log("last Line");
                }
            }




        })(jQuery);
    </script>
<?php
} ?>
