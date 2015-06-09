$(function()
{
    //菜单滑动
    $('.swipeWrap').each(function()
    {
        var t = $(this);
        var menuSwiper = new Swiper('.swipeWrap',
        {
            slidesPerView:'auto',
            offsetPxBefore: 0,
            offsetPxAfter: 0,
            calculateHeight: true,
            onTouchEnd:function(swiper)
            { 
                var swiperIndex = menuSwiper.activeIndex;
                if( swiperIndex==0 )
                {
                    t.siblings('.nextBtn').removeClass('lastPage');    
                }
                else
                {
                    t.siblings('.nextBtn').addClass('lastPage');   
                }
                   
            }
        });
    });
    //全屏图片滑动
    var mySwiper = new Swiper('#slider',
    {
        pagination: '#position',
        loop: true,
        grabCursor: true,
        paginationClickable: true,
        autoplay: 5000,
        autoplayDisableOnInteraction: false
    });
});