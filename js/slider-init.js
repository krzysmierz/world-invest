document.addEventListener('DOMContentLoaded', function () {
  new Swiper('.mySwiper', {
    loop: true,
    slidesPerView: "auto",
    slidesPerGroup: 1,
    spaceBetween: 24,
	centeredSlides: false,
    autoplay: {
      delay: 12500
    }
  });
});