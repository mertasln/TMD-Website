import { component } from 'picoapp';

export default component((node, ctx) => {
    const links = document.querySelectorAll('.slider-nav');
    const sliderRows = document.querySelectorAll('.slider-rows');

    for (let i = 0; i < links.length; i++) {
        links[i].addEventListener('click', e => {
            e.preventDefault();
            document.querySelector('.slider-nav.active').classList.remove('active');
            links[i].classList.add('active');
            for (let x = 0; x < sliderRows.length; x++) { 
                sliderRows[x].classList.add('hide');
            }
            document.getElementById(links[i].getAttribute('data-collection')).classList.remove('hide');
        });
    }
});