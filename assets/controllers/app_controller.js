import { Controller } from '@hotwired/stimulus';

import db from '../db.js';
import Twig from 'twig';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['menu', 'title',
        'detail',
        'twigTemplate',
        'savedCount','message','menu','navigator']
    // ...

    connect() {
        super.connect();
        // db.tables.map(t => console.log(t));
        ons.ready( (x) => {
            console.warn("ons is ready, " + this.identifier)
        })
    }

    openMenu()
    {
        this.menuTarget.open();
    }

    clear()
    {
        this.menuTarget.close();
        db.delete().then (()=>db.open());
    }

    async test(e) {
        const id = e.params.id;
        console.log('fetching from database: ', id);
        db.savedTable.get(e.params.id)
            .then( (row) =>
            {
                console.error(row);
            })
            .catch( e=> console.error(e) );

        const data = db.savedTable.get(id).then(
            (data) => {
                console.assert(data, "Missing data for " + id);
                this.navigatorTarget.pushPage('p_detail', {data: data}).then(
                    (p) => {
                        if (this.hasTwigTemplateTarget) {
                            let template = Twig.twig({
                                data: this.twigTemplateTarget.innerHTML
                            });
                        let html = template.render(data);
                            if (this.hasDetailTarget) {
                                this.detailTarget.innerHTML = html;
                            } else {
                                console.error('no detail target');
                            }
                        }
                    }
                );

            }
        );

    }

    add(e)
    {
        console.log(e.params);
        db.savedTable.get(e.params.id)
            .then( (row) =>
                {
                    row.owned = e.target.closest("ons-switch").checked;
                    db.savedTable.put(row).then( () => this.updateSavedCount());
                })
            .catch( e=> console.error(e) );


    }


    messageTargetConnected(element) {
        // this.messageTarget.innerHTML = ''
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
            if (page) {
                this.navigatorTarget.bringPageTop(page, { animation: 'fade' });
                console.log('loading page ' + page);
            } else {
                console.error('missing page ', e.params);
            }

    }


    updateSavedCount()
    {
        db.savedTable.filter(n => n.owned).count().then ( count => {
            console.log('saved: ' + count);
            this.savedCountTarget.innerHTML = count;
        });

    }


}
