// import Dexie from 'https://cdn.jsdelivr.net/npm/dexie@4.0.1/+esm';
import Dexie from 'dexie';
var db = new Dexie('pokemon1');

export function clearLocalStorage()  {
  db.delete().then (()=>db.open());
}
// db.delete().then (()=>db.open());

db.version(3).stores({
    savedTable: "id,name,owned",
    productTable: "++id,price,brand,category"
});
console.log('listing for ready event on db');
//
db.on('ready', async vipDB => {
    console.error("ready!");
    // db.savedTable.bulkPut([
    //             { id: 100, name: "my pokemon", owned: true},
    //             { id: 101, name: "unowned pokemon", owned: false}
    //         ]).then(
    //             (db) => {
    //                 console.log('db pokemon.saved saved, now counting');
    //                 db.savedTable.count().then(c => document.getElementById('count').innerHTML = c)
    //             });
    //
    const count = await vipDB.savedTable.count();
    if (count > 0) {
        console.error("Already populated, count: " + count);
    } else {
        console.error("empty db, populating...");
        const data = await loadData();
        let withId = await data.map( (x, id) => {
            x.id = id+1;
            x.owned = id < 3;
            return x;
        });
        console.error(data, withId);

        await vipDB.savedTable.bulkAdd(withId).then( (x) => console.log(x));
        console.log ("Done populating.", data[1]);
        // move to target

    }
    // document.getElementById('after-list').style.display = 'none';

});

db.open();
// db.productTable.count().then( (c) => {console.log(c); document.getElementById('count').innerText = c});
async function loadData() {
    let url = 'https://pokeapi.co/api/v2/pokemon?limit=6';
    console.log('fetching ' + url);
    const response = await fetch(url);
    // @todo: fetch all pages
    // add the id!

    return await response.json().then(data => data.results)
}

export default db;
