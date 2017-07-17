function($){
    $(document).ready(function(){
        $('.trigger .more_icon').click(function(){
            $(this).children('.toggle').toggleClass('show');
        })
    });
}(jQuery)
