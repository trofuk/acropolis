var config = {
    deps: ['js/main'],
    map: {
        '*': {
            myvar: 'js/myvar',
            parallax: 'js/vendor/paraxify.min',
            select2: 'js/vendor/select2.min',
            jqueryForm : "js/vendor/jquery.form.min",
            owlCarousel2: 'js/vendor/owl.carousel.min',
            changePrice: 'js/widgets/changePrice',
            initOwl: 'js/widgets/initOwl',
            initSelect2: 'js/widgets/initSelect2',
            oyiCompare: 'js/widgets/oyiCompare'
        }
    },
    shim: {
        owlCarousel2:{
            deps: ['jquery']
        }
    }
};