import { Controller } from '@hotwired/stimulus';

import db from '../db.js';
import Twig from 'twig';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = [
        'menu',
        'detail',
        'title',
        'tabbar',
        'twigTemplate',
        'savedCount','message','menu','navigator']
    // ...

    eventPreDebug(e)
    {
        let navigator = e.navigator;
        console.warn(e.type, e.currentPage.getAttribute('id'), e.detail, e.currentPage, e);
    }
    eventPostDebug(e)
    {
        // idea: dispatch a "{page}:{eventName}" and let the stimulus controller listen for it.
        let navigator = e.navigator;
        let enterPageName = e.enterPage.getAttribute('id');
        let leavePageName = '~';
        if (e.leavePage) {
            leavePageName = e.leavePage.getAttribute('id');
            let eventType = leavePageName + '.' + e.type;
            console.log('dispatching ' + eventType);
            document.dispatchEvent(new Event(eventType));
        }

        // this.dispatch("saved", { detail: { content:
        //         'saved content' } })

        console.error(e.type, 'left '+ leavePageName,
            'entering '+ enterPageName);
        let eventType = enterPageName + '.' + e.type;
        console.log('dispatching ' + eventType);
        document.dispatchEvent(new Event(eventType));

        if (enterPageName == 'saved') {
            // this.tabbarTarget.loadPage('saved');

        }
    }

    connect() {
        super.connect();
        console.log('hello from ' + this.identifier);
        db.open().then(db =>
            db.savedTable.count().then( c => console.log(c)));

        console.error(db.tables.forEach(t =>console.log(t.name)));
        db.savedTable.count().then( c => console.log(c));
        // db.tables.map(t => console.log(t));
        ons.ready( (x) => {
            console.warn("ons is ready, " + this.identifier)
            db.open();
        })
        this.navigatorTarget.addEventListener('prepush', this.eventPreDebug);
        this.navigatorTarget.addEventListener('prepop', this.eventPreDebug);
        this.navigatorTarget.addEventListener('postpush', this.eventPostDebug);
        this.navigatorTarget.addEventListener('postpop', this.eventPostDebug);
        // https://thoughtbot.com/blog/taking-the-most-out-of-stimulus

        // prechange happens on tabs only
        document.addEventListener('prechange', (e) => {
            console.warn(e);

            let target = e.target;
            let tabItem = e.detail.tabItem;
            // { target, tabItem }
            // console.log('target', target);
            // console.log('tabItem', tabItem);
            let pageName = tabItem.getAttribute('page');
            // console.log('prechange', target, tabItem, pageName);

            // this is the tabItem component, not an HTML element
            let tabPageName = tabItem.getAttribute('page');
            let eventType = tabPageName + '.' + e.type;
            console.log('dispatching ' + eventType);
            document.dispatchEvent(new Event(eventType));


            if (pageName == 'xxsaved') {
                console.log('prechange called for saved, disabled');
                const grid = document.querySelector('#grid');
                grid.innerHTML = '';


                db.savedTable.filter(n => n.owned).each( row => {
                    addPokemonToGrid(row, grid);
                    // this.savedCountTarget.innerHTML = count;
                });

                // db.savedTable.count().then ( c => this)
                // getAll().toArray().then ( x => {
                //     console.log(x);
                // });
                console.log('populate the grid.');
            }
            if (target.matches('#tabbar')) {
                document.querySelector('#home-toolbar .center').innerHTML = tabItem.getAttribute('label');
            }
        });



    }

    setTitle(title)
    {
        if (this.hasTitleTarget) {
            this.titleTarget.innerHTML = title;
        }
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
        const data = db.savedTable.get(id).then(
            (data) => {
                console.assert(data, "Missing data for " + id);
                this.navigatorTarget.pushPage('p_detail', {data: data}).then(
                    (p) => {
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

    add(e)
    {
        // get the row and toggle the 'owned' property
        db.savedTable.get(e.params.id)
            .then( (row) =>
                {
                    row.owned = e.target.closest("ons-switch").checked;
                    db.savedTable.put(row).then( () => this.updateSavedCount());
                })
            .catch( e=> console.error(e) );
    }

    log(x)
    {
        console.log(x);
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
            this.savedCountTarget.innerHTML = count;
        });

    }


}
