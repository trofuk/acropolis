/*
* Created by Oleh 14.12.2016
**/

define(['jquery'], function($){
    $.widget('oyi.priceTooltip',{
        options:{
            messageWidth: 230,
            messageArrow: '.message-arrow',
            message: '.price-message',
            inOwl: false
        },
        _create: function(){
            var self = this;
            var $messageBlock = self.element.find(self.options.message),
                $messageArrow = self.element.find(self.options.messageArrow);
            if(self.element.closest('.products-wrap').length){
                self.options.inOwl = true;
                self.element.attr('data-tooltip','bottom');
            }
            self.element.hover(
                function(){
                    if(!self.options.inOwl){
                        var position = $(this).offset(),
                            wh = window.innerHeight,
                            ww = window.innerWidth,
                            scrollTop = window.pageYOffset,
                            positionTop = 0,
                            positionLeft;

                        if(position.left+25 + self.options.messageWidth > ww){//if position left is OUTSIDE window view
                            positionLeft = ww - position.left - self.options.messageWidth - 25;
                            self.element.addClass('right');
                            $messageBlock.css({'left': positionLeft+'px'});
                        }
                    }
                    $messageBlock.show();
                },
                function(){
                    $messageBlock.hide().removeAttr('style');
                    self.element.removeClass('right')
                }
            );
        }
    });
    return $.oyi.priceTooltip;
});