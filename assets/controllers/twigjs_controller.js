import { Controller } from '@hotwired/stimulus';

// now called from the TwigJsComponent Component, so it can pass in a Twig Template
// combination api-platform, inspection-bundle, dexie and twigjs
// loads data from API Platform to dexie, renders dexie data in twigjs

import db from '../db.js';
import Twig from 'twig';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['content'];
    static values = {
        twigTemplate: String,
        store: String,
        filter: Array, // array of objects, e.g. [ {status: 'queued'} ]
        order: Object // e.g. {dateAdded: 'DESC'}
    }

    connect() {
        console.warn("hi from " + this.identifier);
        console.error(this.twigTemplateValue);
        // compile the template
        this.template = Twig.twig({
            data: this.twigTemplateValue
        });

        this.contentConnected();

    }

    // because this can be loaded by Turbo or Onsen
    contentConnected()
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
            .then( html => this.element.innerHTML = html);

    }
}
