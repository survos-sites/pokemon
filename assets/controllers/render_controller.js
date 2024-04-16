import {Controller} from '@hotwired/stimulus';
import Twig from 'twig';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['content','title'];
    static outlets = ['app']; // access app_controller
    static values = {
        payload: {type: Object, default: {'x': 'y'}},
        twigTemplates: {type: String, default: '[]'},
        interval: {type: Number, default: 5},
        clicked: Boolean
    }

    // ...

    connect() {
        super.connect();
        return;

        this.appOutlets.forEach(alertOutlet => alertOutlet.log('hola!'));

        let blocks = JSON.parse(this.twigTemplatesValue);
        // cache?
        this.contentTemplate = Twig.twig({data: blocks.content});
        this.titleTemplate = Twig.twig({data: blocks.title});
        let navigator = document.getElementById('navigator');
        // get the data passed to the page during pushPage
        let data = navigator.topPage.data;
        this.clicked();
        // @todo: outlet to app

        // if (this.hasContentTemplate) {
        //     this.contentTarget.innerHTML = this.contentTemplate.render(data);
        // }
        // if (this.hasTitleTemplate) {
        //     this.titleTarget.innerHTML =this.titleTemplate.render(data);
        // }

    }

    clicked() {
        console.log(this.hasAppOutlet, this.appOutlets.length);
        if (this.hasAppOutlet) {
            this.appOutlet.log("something clicked")
            // this.alertOutlets.forEach( alert => alert.alert("something") )
        }
    }

    render() {
        // we need to get the data associated with the current page
    }

}
