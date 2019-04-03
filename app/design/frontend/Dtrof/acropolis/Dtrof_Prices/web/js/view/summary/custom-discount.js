define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component,quote,totals) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Dtrof_Prices/summary/custom-discount'
            },
            totals: quote.getTotals(),
            isDisplayedCustomdiscountTotal : function () {
                return this.getPureValue();
            },
            getCustomdiscountTotal : function () {
                var price = 0;
                if (this.totals() && totals.getSegment('customdiscount')) {
                    price = totals.getSegment('customdiscount').value;
                }
                return this.getFormattedPrice(price);
            },
            getPureValue: function () {
                if(this.totals() && totals.getSegment('customdiscount')) {
                    return true;
                }else{
                    return false;
                }
            }
        });
    }
);
