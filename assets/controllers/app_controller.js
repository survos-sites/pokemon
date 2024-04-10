import { Controller } from '@hotwired/stimulus';

import db from '../db.js';
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['menu', 'title','savedCount','message','menu','navigator']
    // ...

    connect() {
        super.connect();
        db.tables.map(t => console.log(t));
        // this.menuTarget.open();

    }

    add(e)
    {
        console.log(e.params);
        db.savedTable.get(e.params.id).then( (row) =>
        {
            row.owned = e.target.closest("ons-switch").checked;
            db.savedTable.put(row).then( () => this.updateSavedCount());
        }).catch( e=>{
            console.error(e);
        });


    }


    messageTargetConnected(element) {
            this.messageTarget.innerHTML = 'message here...'
    }

    savedCountTargetConnected(element) {
        this.updateSavedCount();
    }

    clearLocalStorage() {
        localStorage.clear();
        db.delete().then (()=>db.open());
        ons.notification.alert('Cleared local storage');
    };

    loadPage(e)
    {
            this.menuTarget.close();
            let page = e.params.route;
            this.navigatorTarget.bringPageTop(page, { animation: 'fade' });
            console.log('loading page ' + page);

    }


    updateSavedCount()
    {
        db.savedTable.filter(n => n.owned).count().then ( count => {
            console.log('saved: ' + count);
            this.savedCountTarget.innerHTML = count;
        });

    }


}
