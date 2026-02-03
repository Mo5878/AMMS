document.addEventListener('DOMContentLoaded', function () {
    const carousel = document.getElementById('verticalCarousel');
    if (!carousel) return;

    const slidesWrapper = carousel.querySelector('.slides');
    const slides = Array.from(carousel.querySelectorAll('.slide'));
    const prev = carousel.querySelector('.vc-prev');
    const next = carousel.querySelector('.vc-next');
    const dotsContainer = carousel.querySelector('.vc-dots');
    let index = 0;
    const total = slides.length;
    const delay = 3500;
    let timer = null;

    // set wrapper height equal to slide count for translate calculation
    function update() {
        slidesWrapper.style.transform = `translateY(-${index * 100}%)`;
        Array.from(dotsContainer.children).forEach((d, i) => d.classList.toggle('active', i === index));
    }

    // create dots
    slides.forEach((s, i) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.setAttribute('aria-label', `Go to slide ${i+1}`);
        btn.addEventListener('click', () => {
            stop();
            index = i;
            update();
            start();
        });
        if (i === 0) btn.classList.add('active');
        dotsContainer.appendChild(btn);
    });

    function start() {
        stop();
        timer = setInterval(() => {
            index = (index + 1) % total;
            update();
        }, delay);
    }

    function stop() {
        if (timer) clearInterval(timer);
        timer = null;
    }

    prev.addEventListener('click', () => { stop(); index = (index - 1 + total) % total; update(); start(); });
    next.addEventListener('click', () => { stop(); index = (index + 1) % total; update(); start(); });

    carousel.addEventListener('mouseenter', stop);
    carousel.addEventListener('mouseleave', start);

    // ensure slidesWrapper has proper height stacking via CSS transform; set initial
    slidesWrapper.style.height = `${total * 100}%`;
    slides.forEach(s => s.style.height = `${100/total}%`);
    update();
    start();
});
