import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values={
        owned: Boolean
    }
    // ...
    connect() {
        super.connect();
        // for some reason, twigjs is rendering the literal 'checkedvalue' instead of the appropriate value. this hack sets the switch
        if (this.ownedValue) {
            this.element.checked = true;
        }
    }
}
