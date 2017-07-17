(function($){
    $(document).ready(function(){
        console.log('ready');
        $('.trigger .more_icon').click(function(){
            console.log($(this).parent().find('.toggle'));
            $(this).parents('.trigger').find('.toggle').toggleClass('show');
        })
    });
})(jQuery);
