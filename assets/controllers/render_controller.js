import {Controller} from '@hotwired/stimulus';
import Twig from 'twig';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['content']
    static values = {
        payload: {type: Object, default: {'x': 'y'}},
        twigTemplate: {type: String, default: 'twig template here!'},
        interval: {type: Number, default: 5},
        clicked: Boolean
    }

    // ...

    connect() {
        super.connect();
        console.error(this.payloadValue, this.twigTemplateValue);
        // cache?
        this.template = Twig.twig({
            data: this.twigTemplateValue
        });
        this.render(); // render the data.

    }

    render() {
        let rendered = this.template.render(this.payloadValue);
        console.error(rendered);
        this.contentTarget.innerHTML =
            rendered;
    }

}
