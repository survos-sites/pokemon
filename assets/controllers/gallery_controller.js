import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
// /* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        activeIndex: Number,
    }
    static targets = ['carousel'];
    static outlets = ['app'];


    connect() {
        super.connect();
        // listen for changes to update the title.
        console.log('hi from ' + this.identifier);
    }

    carouselConnected(e)
    {
        // this is the item within the gallery, not the gallery page itself.
        e.element.addEventListener('postchange', (e) => {
            console.log(e);
            console.log('i heard an event! ' + e.type);
            let activeIndex = e.carousel.activeIndex;
            let item = e.carousel.querySelectorAll('ons-carousel-item')[activeIndex];
            console.log(item);
            this.appOutlets.forEach(appOutlet => appOutlet.log('hola!'));
            this.appOutlet.setTitle(item.dataset['title']);

        })

    }

    // ...
}
