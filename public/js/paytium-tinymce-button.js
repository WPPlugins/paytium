(function() {
	tinymce.PluginManager.add('paytiumshortcodes', function( editor, url ) {
		editor.addButton( 'paytiumshortcodes', {
			title: 'Paytium shortcodes',
			type: 'menubutton',
			icon: 'icon paytium-shortcodes-icon',
			menu: [
                {
                    text: 'Basic product, static amount',
                    onclick: function () {
                        editor.insertContent('[paytium name="Your webshop name" description="Your product or description" amount="19,95" button_label="Pay"]' +'<br>'+'[paytium_total /]'+'<br>'+ '[/paytium]');
                    }
                },
                {
                    text: 'Basic product or donation, open amount',
                    onclick: function() {
                        editor.insertContent('[paytium name="Your webshop name" description="Donations" button_label="Donate"]'+'<br>'+'[paytium_amount label="Donation Amount:" default="25" /]' +'<br>'+'[paytium_total label="Donate" /]'+'[/paytium]');
                    }
                },
                {
                    text: 'Dropdown with multiple amounts',
                    onclick: function(e) {
                        e.stopPropagation();
                        editor.insertContent('[paytium name="Your webshop name" description="Your product or description" button_label="Pay"]'+'<br>'+'[paytium_dropdown label="Options" options="9,95/19,95/29,95" options_are_amounts="true" /]' +'<br>'+'[paytium_total /]'+'<br>'+'[/paytium]');
                    }
                },
                {
                    text: 'Radio buttons with multiple amounts',
                    onclick: function(e) {
                        e.stopPropagation();
                        editor.insertContent('[paytium name="Your webshop name" description="Your product or description" button_label="Pay"]'+'<br>'+'[paytium_radio label="Options" options="9,95/19,95/29,95" options_are_amounts="true" /]' +'<br>'+'[paytium_total /]'+'<br>'+'[/paytium]');
                    }
                },
                {
                	text: 'Require customer email address',
                    onclick: function() {
                        editor.insertContent('[paytium name="My Store" description="My Product" amount="19,95" button_label="Pay"]' +'<br>'+'[paytium_field type="email" label="Your email" required="true" /]'+'<br>'+'[paytium_total /]'+'<br>'+ '[/paytium]');
                    }
                },
                {
                	text: 'Field examples (text, textarea, email)',
                    onclick: function() {
                        editor.insertContent('[paytium name="My Store" description="My Product" amount="19,95" button_label="Pay"]' +'<br>'+'[paytium_field type="text" label="Your text" required="true" /]'+'<br>'+'[paytium_field type="textarea" label="Your text area" required="true" /]'+'<br>'+'[paytium_field type="email" label="Your email" required="true" /]'+'<br>'+'[paytium_total /]'+'<br>'+ '[/paytium]');
                    }
                },
                // TODO David - Add example with all customer details fields (NAW) and remove customer_details="true" option
                {
                    text: 'Ask for customer details (name, address etc)',
                    onclick: function() {
                        editor.insertContent('[paytium name="My Store" description="My Product" amount="19,95" customer_details="true" button_label="Pay"]' +'<br>'+'[paytium_total label="Start now!" /]'+'<br>'+ '[/paytium]');
                    }
                },
                {
                    text: 'Subscription/recurring payment',
                    onclick: function() {
                editor.insertContent('[paytium name="Subscription store" description="Some subscription" amount="99,00" button_label="Subscribe"]' +'<br>'+'[paytium_subscription interval="1 days" times="99" /]' +'<br>'+'[paytium_field type="name" label="Volledige naam" /]' +'<br>'+'[paytium_field type="email" label="Your email" required="true" /]' +'<br>'+'[paytium_total label="Subscribe" /]' +'<br>'+'[/paytium]'+'<br><br><br>'+'Parameter interval in [paytium_subscription /] is required and can be days, weeks, months. For example if you want charge the customer every 3 days, set it to "3 days". Parameter times in [paytium_subscription /] is not required. Times are the total number of charges for the subscription to complete. Leave empty for an on-going subscription. The fields with type name and email are also required. Make sure you enable payment methods that support recurring payments in your Mollie account, for example creditcard and SEPA Direct Debit. You can remove this paragraph when you are done. ;)');
                }
                }
			]
		});
	});
})();