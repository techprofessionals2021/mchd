$('.review-slick').slick({
    slidesToShow: 3,
    centerMode: true,
    dots: false,
    arrows: true,
    responsive: [
        {
            breakpoint: 1300,
            settings: {
                slidesToShow: 2,
                centerMode: true,
                dots: false,
                arrows: true,
            }
        },
        {
            breakpoint: 700,
            settings: {
                slidesToShow: 1,
                centerMode: true,
                dots: false,
                arrows: true,
            }
        },
        {
            breakpoint: 450,
            settings: {
                slidesToShow: 1,
                centerPadding: '30px',
                centerMode: true,
                dots: false,
                arrows: true,
            }
        },
    ]
});