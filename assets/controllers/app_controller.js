// import { Controller } from '@hotwired/stimulus';
import MobileController from '@survos-mobile/mobile';
// import { ParentController } from '../vendor/survos/mobile-bundle/assets/src/controllers/mobile_controller.js';
// import db from '../db.js';
import Twig from 'twig';

// app_controller must extend from MobileController, and be called app, otherwise outlets won't work.

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends MobileController {
    static targets = [
        'menu',
        'detail',
        'title',
        'pageTitle',
        'tabbar',
        'tab',
        'twigTemplate',
        'savedCount','message','menu','navigator']
    // ...

    connect() {
        super.connect();
        console.log('hello from ' + this.identifier);
        // we can do this in app, mobilecontroller has no need for dexie
        // db.open().then(db =>
        //     db.savedTable.count().then( c => console.log(c)));
    }

    setDb(db) {
        super.setDb(db);
        this.updateSavedCount();
    }



    clear()
    {
        this.menuTarget.close();
        this.db.delete().then (()=>this.db.open());
    }

    async test(e) {
        const id = e.params.id;
        const data =this.db.table(e.params.store).get(id).then(
            (data) => {
                console.log(data, e.params);
                console.assert(data, "Missing data for " + id);
                this.navigatorTarget.pushPage('detail', {data: {id: id}}).then(
                    (p) => {
                        console.error(p);
                        // events?
                        return;
                        // p.data is the same as data
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

    add(e) {
        // get the row and toggle the 'owned' property
        this.db.savedTable.get(e.params.id)
            .then((row) => {
                row.owned = e.target.closest("ons-switch").checked;
                this.db.savedTable.put(row).then(() => this.updateSavedCount());
            })
            .catch(e => console.error(e));
    }


    log(x)
    {
        console.log(x);
    }


    messageTargetConnected(element)
    {
        // this.messageTarget.innerHTML = ''
    }

    tabbarTargetConnected(element)
    {
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
        if (!this.db) {
            return;
        }
        this.db.savedTable.filter(n => n.owned).count().then ( count => {
            // this.savedCountTarget.innerHTML = count;
            // console.error(count);
            // this.tabTargets.forEach(x => console.log(x.getAttribute('page')));
            let savedTab = this.tabTargets.find(x => x.getAttribute('page') === 'saved');
            // search children!  closest() returns ancestors.
            let badge = savedTab.querySelector('.tabbar__badge');
            badge.innerText=count;
            // db.savedTable.put(row).then(() => this.updateSavedCount());

        });

    }


}
