import { Controller } from '@hotwired/stimulus';

// now called from the TwigJsComponent Component, so it can pass in a Twig Template

import db from '../db.js';
import Twig from 'twig';
/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['list'];
    static outlets = ['app'];
    static values = {
        max: Number,
        twigTemplate: String
    }


    connect() {
        console.warn("hi from " + this.identifier);

        console.log(this.hasAppOutlet, this.appOutlets.length);
        this.appOutlets.forEach(appOutlet => appOutlet.log('hola!'));
        this.appOutlet.setTitle('abc');

        document.addEventListener(this.identifier + '.prechange', ( e => {
            console.log(e);
            console.log('i heard an event! ' + e.type);
        }));


        console.error(this.twigTemplateValue);
        this.template = Twig.twig({
            data: this.twigTemplateValue
        });
        this.listTargetConnected();
        // .then( template => console.log(template))
        //     .catch(e => console.error(e));


    }

    listTargetConnected()
    {

        let storeName = 'savedTable';
        let filter = {'owned':true}
        // like api platform, get the data based on the parameters

        let table = db.table(storeName);
        table = table.filter(row => row['owned'] === true);
        table.toArray().then( (data) => {
            data.forEach( (row) => {
                console.log(row);
                // nextPokenumber++;
            })
        })

        db.savedTable.toArray()
            .then( rows => this.template.render({rows: rows}))
            .then( html => this.listTarget.innerHTML = html);
        return;

        const URL = 'pokemon__url';
        const PREFIX = 'pokemon__';

        let nextPokenumber = 1;
        let storedPokemon;

        db.savedTable.toArray().then( (data) => {
            data.forEach( (row) => {
                this.appendPokemon(row.id, row  );
                // nextPokenumber++;
            })
        })

        if (0)
        // this is the database of all pokemon
        while ((storedPokemon = localStorage.getItem(PREFIX + nextPokenumber)) !== null) {
            // console.log(`got ${storedPokemon} from local with key ${PREFIX + nextPokenumber}`);
            this.appendPokemon(nextPokenumber, storedPokemon);
            nextPokenumber++;
        }

        // we could do this, or we could save it locally, set it in PWA, etc.
        if (!localStorage.getItem(URL)) {
            localStorage.setItem(URL, 'https://pokeapi.co/api/v2/pokemon?limit=12');
        }

        const get = async () => {
            // do the API call and get JSON response
            const response = await fetch(localStorage.getItem(URL));
            const json = await response.json();

            const newPokemon = json.results.map(e => e.name);

            newPokemon.forEach((name, i) => {
                this.appendPokemon(nextPokenumber, name);
                if (nextPokenumber > this.maxValue) {
                    return;
                }

                const key = PREFIX + nextPokenumber;
                console.log(`i${i} Storing ${name} as ${key}`);
                localStorage.setItem(key, name)
                nextPokenumber++;
            });

            localStorage.setItem(URL, json.next);

            // hide the spinner when all the pages have been loaded
            if (!localStorage.getItem(URL)) {
                document.querySelector('#after-list').style.display = 'none';
            }
        };

        // get the first set of results as soon as the page is initialised
        // get();

        // at the bottom of the list get the next set of results and append them
        this.element.onInfiniteScroll = (done) => {
            if (localStorage.getItem(URL)) {
                setTimeout(() => {
                    get();
                    done();
                }, 200);
            }
        };
    }
}

function save() {
    console.log('save');
}
