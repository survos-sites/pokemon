import { Controller } from '@hotwired/stimulus';

import db from '../db.js';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['list']
    static values = {
        max: Number,
    }

    savePokemon (pokenumber, button) {
        addPokemonToGrid(pokenumber);
        button.parentNode.parentNode.hideExpansion();
    }

    appendPokemon (pokenumber, row)
    {
        let checked = row.owned ? 'checked': '';
        let element = `
        <ons-list-item>
    <ons-switch class="right" ${checked} data-action="click->app#add" 
        data-app-id-param="${pokenumber}" >
</ons-switch>
          ${pokenumber} ${row.name}
        </ons-list-item>
      `;
        // console.log('adding item to list', element);
        this.listTarget.appendChild(window.ons.createElement(element));
    }


    connect() {
        console.warn("hi from " + this.identifier);


    }

    listTargetConnected()
    {
        console.log(ons.name);
        console.warn('listTargetConnected!');
        // local storage keys
        const URL = 'pokemon__url';
        const PREFIX = 'pokemon__';

        let nextPokenumber = 1;
        let storedPokemon;

        // first, get ours
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
            localStorage.setItem(URL, 'https://pokeapi.co/api/v2/pokemon?limit=100');
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