/**
 * Paytium Public JS
 *
 * @package PT
 * @author  David de Boer <david@davdeb.com>
 */

/* global jQuery, pt_script */

(function ($) {
    'use strict';

    // Set debug flag.
    var script_debug = ( (typeof pt_script != 'undefined') && pt_script.script_debug == false );

    $(function () {

        var $body = $('body');

        if (script_debug) {
            console.log('pt_script', pt_script);
        }

        var ptFormList = $body.find('.pt-checkout-form');

        // Run Pikaday Datepicker method on each date field in each Paytium form.
        // Requires Moment JS, Pikaday and Pikaday jQuery plugin.
        var ptDateFields = ptFormList.find('.pt-cf-date');

        ptDateFields.pikaday({
            format: 'D/M/YYYY'
        });

        // Make sure each checkbox change sets the appropriate hidden value (Yes/No) to record
        // to Paytium payment records.
        var ptCheckboxFields = ptFormList.find('.pt-cf-checkbox');

        ptCheckboxFields.change(function () {

            var checkbox = $(this);

            var checkboxId = checkbox.prop('id');

            // Hidden ID field is simply "_hidden" appended to checkbox ID field.
            var hiddenField = $body.find('#' + checkboxId + '_hidden');

            // Change to "Yes" or "No" depending on checked or not.
            hiddenField.val(checkbox.is(':checked') ? 'Yes' : 'No');
        });

        //Process the form(s)
        ptFormList.each(function () {
            var ptForm = $(this);

            //
            // START - Paytium No Payment
            //

            function isPaytiumNoPayment() {

                ptForm.find("[id^=pt-paytium-no-payment]").each(function () {
                    return true;
                });
            }

            //
            // END - Paytium No Payment
            //

            var newAmount = '';

            // Get the "pt-id" ID of the current form as there may be multiple forms on the page.
            var formId = ptForm.data('pt-id') || '';

            // [paytium_radio & [paytium_dropdown - For getting & saving amount/option details
            var ptnewAmountLabel;
            var ptnewAmountOption;

            // [paytium_radio - Try to get first option in radio buttons to use as default
            if (ptForm.find('input[type=radio]:first').data('pt-price') && ( newAmount === '' )) {

                var CustomOption = ptForm.find('input[type=radio]:first').data('pt-price');

                if (isNaN(CustomOption)) {
                    newAmount = ptForm.find('input[type=radio]:first').data('pt-price').split(' ')[0];
                } else {
                    newAmount = ptForm.find('input[type=radio]:first').data('pt-price');
                }

                // Get custom label and amount/option for custom radio amounts when none selected yet
                ptnewAmountLabel = document.getElementById('pt-cf-radio-label').textContent;
                ptnewAmountOption = ptForm.find("input[type='radio']:checked").val();

                pt_script[formId].originalAmount = newAmount;
                pt_script[formId].amount = newAmount;
                ptForm.find('.pt-total-amount').text('€ ' + newAmount);
                if (script_debug) {
                    console.log("Amount of first radio button: " + newAmount);
                }
            }


            var ptAmountDropdown = ptForm.find('.pt-cf-amount');

            //
            // START - Process custom amount, label & option for custom dropdown/radio
            //

            if (ptAmountDropdown.length > 0) {
                ptAmountDropdown.on('change', function () {

                    // [paytium_radio & [paytium_dropdown - Get selected amounts and process in form
                    if (ptAmountDropdown.is('input[type="radio"]')) {

                        var RadioCustomOption = ptForm.find("input[type='radio']:checked").data('pt-price');

                        if (isNaN(RadioCustomOption)) {
                            newAmount = ptForm.find("input[type='radio']:checked").data('pt-price').split(' ')[0];
                        } else {
                            newAmount = ptForm.find("input[type='radio']:checked").data('pt-price');
                        }

                        // Get custom label and amount/option for custom radio amounts
                        ptnewAmountLabel = document.getElementById('pt-cf-radio-label').textContent;
                        ptnewAmountOption = ptForm.find("input[type='radio']:checked").val();

                        //if (script_debug) { console.log( 'newAmount' + newAmount ); }
                    } else {

                        var DropdownCustomOption = ptForm.find('.pt-cf-dropdown option:selected').data('pt-price');

                        if (isNaN(DropdownCustomOption)) {
                            newAmount = ptForm.find('.pt-cf-dropdown option:selected').data('pt-price').split(' ')[0];
                        } else {
                            newAmount = ptForm.find('.pt-cf-dropdown option:selected').data('pt-price');
                        }

                        // Get custom label and amount/option for custom dropdown amounts
                        ptnewAmountLabel = document.getElementById('pt-cf-dropdown-label').textContent;
                        ptnewAmountOption = ptForm.find('.pt-cf-dropdown option:selected').val();

                        //if (script_debug) { console.log( 'newAmount' + newAmount ); }
                    }

                    pt_script[formId].originalAmount = newAmount;
                    pt_script[formId].amount = newAmount;
                    ptForm.find('.pt-total-amount').text('€ ' + newAmount);
                    if (script_debug) {
                        console.log('Checked or selected amount: ' + newAmount);
                    }

                });
            }

            //
            // END - Process custom amount, label & option for custom dropdown/radio
            //


            //
            // START - Update total when user enters amount (custom amount)
            //

            ptForm.find('.pt-uea-custom-amount').on('keyup', function () {

                ptForm.find('.pt-total-amount').html('&euro; ' + $(this).val());

            });

            //
            // END - Update total when user enters amount (custom amount)
            //

            // Store original amount.
            pt_script[formId].originalAmount = pt_script[formId].amount;

            // Parsley JS prevents form submit by default.
            // So we need to explicitly call .validate() instead of auto-binding forms with data-parsley-form.
            // http://parsleyjs.org/doc/index.html#psly-usage-form

            // Update 5/20/2015: Fire off form/Parsley JS validation with button click.
            // Needed for some mobile browsers like Chrome iOS.
            // Using event subscription method won't work for them (i.e. .subscribe('parsley:form:validate'... ).

            function submitFormProcessing() {

                if (script_debug) {
                    console.log('click.ptPaymentBtn fired');
                }
                if (script_debug) {
                    console.log('Check form valid:', ptForm.parsley().validate());
                }

                if (ptForm.parsley().validate()) {

                    // Amount already preset in basic [paytium] shortcode (or default of 1).
                    var finalAmount = pt_script[formId].amount;

                    // If user-entered amount found in form, use it's amount instead of preset/default.
                    var ptUeaAmount = ptForm.find('.pt-uea-custom-amount').val();

                    if (ptUeaAmount) {

                        finalAmount = Math.round(parseFloat(ptUeaAmount) * 100);

                        finalAmount = ptUeaAmount.replace(".", ",");// replace decimal point character

                    }

                    if (script_debug) {
                        console.log("finalAmount: " + finalAmount);
                    }

                    var ptQuantity = ptForm.find('.pt-cf-quantity');

                    if (ptQuantity.length > 0) {
                        // First we need to set the value
                        // We need to check if it is a radio button so we can grab the selected value

                        if (ptQuantity.is('input[type="radio"]')) {
                            ptQuantity = ptForm.find('.pt-cf-quantity:checked').val();
                        } else {
                            ptQuantity = ptForm.find('.pt-cf-quantity').val();
                        }

                        if (ptQuantity > 0) {
                            finalAmount = parseInt(ptQuantity) * finalAmount;
                        }
                    }

                    // pt_script from localized script values from PHP.

                    // Set the amount on the hidden field to pass via POST when submitting the form.
                    ptForm.find('.pt_amount').val(finalAmount);

                    //
                    // David de Boer - Paytium 1.1.0
                    // Process (custom) fields
                    //

                    // Initialize Object that will contain processed fields
                    var ProcessFailed = '';

                    // Process individual fields
                    $(ptForm.find("[id^=pt-field-]")).each(function (index, element) {

                        // Get the field value
                        var ptFieldValue = $(element).val();

                        // Get the user defined field label
                        var ptUserLabel = document.getElementById(this.id).dataset.ptUserLabel;

                        // Get the field type
                        var ptFieldType = document.getElementById(this.id).dataset.ptFieldType;

                        // Get required attribute
                        var required = $(element).attr("required");

                        // Validate that required fields are filled
                        if ((required == 'required') && ptFieldValue == '') {

                            window.alert(paytium_localize_script_vars.field_is_required.replace('%s', ptUserLabel));
                            ProcessFailed = true;
                            return false;
                        }

                        // Validate email fields
                        if (ptFieldType == 'email') {
                            var ptEmailreg = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,15})+$/;

                            if ((ptEmailreg.test(ptFieldValue) == false)) {
                                window.alert(paytium_localize_script_vars.no_valid_email.replace('%s', ptUserLabel));
                                ProcessFailed = true;
                                return false;
                            }
                        }


                        // Log everything to Console when troubleshooting
                        if (script_debug) {
                            console.log($(element));
                            console.log('Processing field (type, label, value, id): ' + ptFieldType + ', ' + ptUserLabel + ', ' + ptFieldValue + ', ' + this.id);
                        }

                        // Add the user's field label to form post data,
                        // so it can be used as user-facing identifier for that field

                        // Create unique field ID for the user's field label
                        var ptUserLabelLabel = this.id + "-label";

                        // Add the unique field ID and user's label to the form post data
                        $("<input>", {type: "hidden", name: ptUserLabelLabel, value: ptUserLabel}).appendTo(ptForm);

                    });


                    // If processing fields failed, also stop processing form
                    if (ProcessFailed && script_debug) {
                        console.log('ProcessFailed :' + ProcessFailed);
                        return false;
                    }

                    //
                    // End processing (custom) fields
                    //


                    //
                    // David de Boer - Paytium 1.3.0
                    // Process subscription fields
                    //
                    $(ptForm.find("[id^=pt-subscription-]")).each(function (index, element) {

                        // Get the field value
                        var ptFieldValue = $(element).val();

                        // Get the field type
                        // this.id is field type

                        // Log everything to Console when troubleshooting
                        if (script_debug) {
                            console.log($(element));
                            console.log('Processing ' + this.id + ', ' + ptFieldValue);
                        }

                        // Add the unique field ID and user's label to the form post data
                        $("<input>", {type: "hidden", name: this.id, value: ptFieldValue}).appendTo(ptForm);

                    });

                    //
                    // End processing subscription fields
                    //

                    //
                    // David de Boer - Paytium 1.4.0
                    // START - Add to POST: custom label & option for custom dropdown/radio amount
                    // [paytium_radio /] & [paytium_dropdown /]
                    //
                    if (ptAmountDropdown.length > 0) {

                        // Add the unique field ID and user's label to the form post data
                        $("<input>", {
                            type: "hidden",
                            name: 'pt-field-amount-label',
                            value: ptnewAmountLabel
                        }).appendTo(ptForm);

                        // Add the unique field ID and user's label to the form post data
                        $("<input>", {
                            type: "hidden",
                            name: 'pt-field-amount',
                            value: ptnewAmountOption
                        }).appendTo(ptForm);

                    }

                    //
                    // END - Add to POST: custom label & option for custom dropdown/radio amount
                    //


                    // David de Boer - Paytium 1.0.4
                    // If there is no amount entered or amount is too low to be processed by Mollie
                    // block execution of script and show an alert. Why 1 euro? Lower amounts
                    // are just not logical! https://api.mollie.nl/v1/methods
                    if ((finalAmount <= '1') && (isPaytiumNoPayment() == false)) {
                        window.alert(paytium_localize_script_vars.amount_too_low);
                        return false;
                    }

                    var args = '';

                    // Add customer fields values if the customer details are filled
                    // TODO David: What is this for, it's not used anywhere? Not to add stuff to $_POST?
                    if (!$.isEmptyObject(args)) {
                        ptForm.find('.pt-customer-details-name').val(args.customer_name);
                        ptForm.find('.pt-customer-details-street').val(args.customer_street);
                        ptForm.find('.pt-customer-details-house_number').val(args.customer_house_number);
                        ptForm.find('.pt-customer-details-city').val(args.customer_city);
                        ptForm.find('.pt-customer-details-postcode').val(args.customer_postcode);
                        ptForm.find('.pt-customer-details-country').val(args.customer_country);

                        ptForm.find('.pt-customer-details-email').val(args.customer_email);
                    }

                    //Unbind original form submit trigger before calling again to "reset" it and submit normally.
                    ptForm.unbind('submit');
                    ptForm.submit();

                    //Disable original payment button and change text for UI feedback while POST-ing to Mollie
                    ptForm.find('.pt-payment-btn')
                        .prop('disabled', true)
                        .find('span')
                        .text(paytium_localize_script_vars.processing_please_wait);

                    // TODO David - I think this is all Stripe specific, remove it?
                    var paytiumParams = {
                        name: ( pt_script[formId].name != -1 ? pt_script[formId].name : '' ),
                        description: ( pt_script[formId].description != -1 ? pt_script[formId].description : '' ),
                        amount: finalAmount,
                        panelLabel: ( pt_script[formId].panelLabel != -1 ? pt_script[formId].panelLabel : 'Betaal {{amount}}' ),
                        customerDetails: ( pt_script[formId].customerDetails == 'true' || pt_script[formId].customerDetails == 1 ? true : false ),
                        customerEmail: ( pt_script[formId].customerEmail == 'true' || pt_script[formId].customerEmail == 1 ? true : false ),
                    };

                    // When using do_shortcode() the prefill_email option not being set causes some errors and issues and this fixes it
                    // by not including the 'email=""' if prefill_email is not set. Having it set to blank is what causes the issue.
                    if (pt_script[formId].email != -1) {
                        paytiumParams.email = pt_script[formId].email;
                    }

                }

                event.preventDefault();
            }

            //
            // START - Paytium Links
            //

            ptForm.find("[id^=pt-paytium-links]").each(function () {

                // Create an object with all data
                function getSearchParameters() {
                    var prmstr = window.location.search.substr(1);
                    prmstr = decodeURIComponent(prmstr);
                    return prmstr != null && prmstr != "" ? transformToAssocArray(prmstr) : {};
                }

                function transformToAssocArray(prmstr) {
                    var params = {};
                    var prmarr = prmstr.split("&");
                    for (var i = 0; i < prmarr.length; i++) {
                        var tmparr = prmarr[i].split("=");
                        params[tmparr[0]] = tmparr[1];
                    }
                    return params;
                }

                var params = getSearchParameters();

                $.each(params, function (key, valueObj) {

                    $(ptForm.find("[id^=pt-field-]")).each(function (index, element) {

                        // Get the user defined field label
                        var ptUserLabel = document.getElementById(this.id).dataset.ptUserLabel;


                        if (ptUserLabel == key) {
                            $(element).val(valueObj);
                        }

                    });

                    if (key.toLowerCase() == 'bedrag' || key.toLowerCase() == 'amount') {
                        ptForm.find("[name*='pt-amount']").val(valueObj);
                        ptForm.find('.pt-uea-custom-amount').val(valueObj).prop("disabled", true);
                        ptForm.find('.pt-total-amount').text('€ ' + valueObj);
                    }

                });

                ptForm.find("[id^=pt-paytium-links-auto-redirect]").each(function () {
                    ptForm.find('.pt-payment-btn').click(submitFormProcessing());
                });

            });

            //
            // END - Paytium Links
            //

            ptForm.find('.pt-payment-btn').on('click.ptPaymentBtn', submitFormProcessing);

        });

        // Use with total amount.
        function currencyFormattedAmount(amount, currency) {

            // David - Convert all amounts to float internally
            amount = amount.replace(",", ".");// replace decimal point character with
            amount = "€ " + amount;


            return amount;
        }
    });

}(jQuery));
