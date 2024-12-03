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
    // static targets = [
    //     'menu',
    //     'detail',
    //     'title',
    //     'pageTitle',
    //     'tabbar',
    //     'tab',
    //     'twigTemplate',
    //     'savedCount','message','menu','navigator']
    // ...

    connect() {
        super.connect();
        console.log('hello from ' + this.identifier);
        // we can do this in app, mobilecontroller has no need for dexie
        // db.open().then(db =>
        //     db.savedTable.count().then( c => console.log(c)));
    }

    async setDb(db) {
        if (!db) {
            return; // don't set to null
        }
        await super.setDb(db);
        console.assert(db, "db not set in setDb!");
        // this.db = db; // redudant!
        // db && this.updateSavedCount();
    }



    clear()
    {
        this.menuTarget.close();
        // this.db.delete().then (()=>this.db.open());
        console.log('resetting database');
        window.db.delete().then(()=>window.db.open().then(()=> window.location.reload()));
    }

    async test(e) {
        const id = e.params.id;
        console.log(e.params);
        const data =window.db.table(e.params.store).get(id).then(
            (data) => {
                console.log(data, e.params);
                console.assert(data, "Missing data for " + id);
                this.navigatorTarget.pushPage('detail', {data: {id: id}}).then(
                    (p) => {
                        // console.error(p);
                        // // events?
                        // return;
                        // // p.data is the same as data
                        // if (this.hasTwigTemplateTarget) {
                        //     let template = Twig.twig({
                        //         data: this.twigTemplateTarget.innerHTML
                        //     });
                        // let html = template.render(data);
                        //     if (this.hasDetailTarget) {
                        //         this.detailTarget.innerHTML = html;
                        //     } else {
                        //         console.error('no detail target');
                        //     }
                        // }
                    }
                );

            }
        );

    }

    add(e) {
        // get the row and toggle the 'owned' property
        // console.assert(this.db, "missing db in app_controller");
        // this.db && this.db.savedTable.get(e.params.id)
        //     .then((row) => {
        //         // closest is ancestor
        //         row.owned = e.target.closest("ons-switch").checked;
        //         this.db.savedTable.put(row).then(() => this.updateSavedCount());
        //     })
        //     .catch(e => console.error(e));
        console.assert(window.db, "missing db in app_controller");
        window.db && window.db.savedTable.get(e.params.id)
            .then((row) => {
                // closest is ancestor
                row.owned = e.target.closest("ons-switch").checked;
                window.db.savedTable.put(row).then(() => this.updateSavedCount());
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
        // wait until the db is connected.
        // this.updateSavedCount();
    }

    clearLocalStorage() {
        localStorage.clear();
        console.log('clearning local storage via db.delete()');
        window.db.delete().then (()=>window.db.open());
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
        let db = this.getDb();
        db && db.savedTable.filter(n => n.owned).count().then ( count => {
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

    async isPopulated(table) {
        const count = await table.count();
        // console.log(count, table);
        return count > 0;
    }
    getFilter(refreshEvent) {
        let filter = { };
        if (refreshEvent === 'saved.prechange') {
            filter = { 'owned': true }
        }
        return filter;
    }



}
