import { Controller } from '@hotwired/stimulus';

import db from '../../assets/saved.js';
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['menu', 'title','savedCount','message']
    // ...

    connect() {
        super.connect();
        db.tables.map(t => console.log(t));

    }

    messageTargetConnected(element) {
        db.savedTable.count().then ( count => {
            console.log('saved: ' + count);
            this.messageTarget.innerHTML = 'xxx' + count;
        });
    }

    savedCountTargetConnected(element) {
        db.savedTable.count().then ( count => {
            console.log('saved: ' + count);
            this.savedCountTarget.innerHTML = count;
        });
    }


}
