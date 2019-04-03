define(
    [
        'Dtrof_Prices/js/view/summary/custom-discount'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Dtrof_Prices/cart/totals/custom-discount'
            },
            /**
             * @override
             *
             * @returns {boolean}
             */
            isDisplayed: function () {
                return true;
            }
        });
    }
);