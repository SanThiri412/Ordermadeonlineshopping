$(function(){
    let $li = $('#slide-items > li');
    let count = 0;

    function slideshow(){
        count++;

        $li.removeClass('current');

        $li.eq(count % 4).addClass('current');
    }
    setInterval(slideshow,2000);
});