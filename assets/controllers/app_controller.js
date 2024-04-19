import { Controller } from '@hotwired/stimulus';
// import { ParentController } from '../vendor/survos/mobile-bundle/assets/src/controllers/mobile_controller.js';
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
        'pageTitle',
        'tabbar',
        'twigTemplate',
        'savedCount','message','menu','navigator']
    // ...

    eventPreDebug(e)
    {
        let navigator = e.navigator;
        console.warn(e.type, e.currentPage.getAttribute('id'), e.detail, e.currentPage, e);
    }
    eventPostDispatch(e)
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
        this.navigatorTarget.addEventListener('postpush', this.eventPostDispatch);
        this.navigatorTarget.addEventListener('postpop', this.eventPostDispatch);
        // https://thoughtbot.com/blog/taking-the-most-out-of-stimulus

        // prechange happens on tabs only
        document.addEventListener('prechange', (e) => {
            console.warn(e.type);

            let target = e.target;
            let tabItem = e.detail.tabItem;
            // { target, tabItem }
            console.log('target', target, target.dataset);
            console.log('tabItem', tabItem);

            // this should be in the gallery controller, which can then dispatch it to app

            // document.querySelector('ons-carousel').addEventListener('postchange',
            //     (e) => {
            //     let activeIndex = e.carousel.activeIndex;
            //     let item = e.carousel.querySelectorAll('ons-carousel-item')[activeIndex];
            //     console.log(item);
            //     this.titleTarget.innerHTML = item.dataset['title'];
            // })


            if (tabItem) {
                // console.log('prechange', target, tabItem, pageName);

                // this is the tabItem component, not an HTML element
                let title = tabItem.getAttribute('label');
                console.warn(title);
                this.titleTarget.innerHTML = tabItem.getAttribute('label');
                let tabPageName = tabItem.getAttribute('page');
                let eventType = tabPageName + '.' + e.type;
                console.log('dispatching ' + eventType);
                document.dispatchEvent(new Event(eventType));
            }

            // update the tabbar title?
            // document.querySelector('#home-toolbar .center').innerHTML = tabItem.getAttribute('label');

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
                this.navigatorTarget.pushPage('detail', {data: data}).then(
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
