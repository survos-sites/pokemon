// import Dexie from 'https://cdn.jsdelivr.net/npm/dexie@4.0.1/+esm';
import Dexie from 'dexie';
var db = new Dexie('pokemon');

export function clearLocalStorage()  {
  // db.delete().then (()=>db.open());
}
// db.delete().then (()=>db.open());

db.version(3).stores({
    savedTable: "id,name,owned",
    productTable: "++id,price,brand,category"
});
db.on('ready', async vipDB => {
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
        console.log("Already populated, count: " + count);
    } else {
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
db.productTable.count().then( (c) => {console.log(c); document.getElementById('count').innerText = c});
async function loadData() {
    let url = 'https://pokeapi.co/api/v2/pokemon?limit=100';
    const response = await fetch(url);
    // @todo: fetch all pages
    // add the id!

    return await response.json().then(data => data.results)
}

export default db;

// import Dexie from 'dexie';
// var db = Dexie('pokemon');
//
// db.version(1).stores({
//     saved: 'id'
// });
//
//     db.on('ready', async vipDB => {
//         const count = await vipDB.productTable.count();
//         if (count > 0) {
//             console.log("Already populated, count: " + count);
//         } else {
//             db.saved.bulkPut([
//                 { id: 1, name: "test pokemon"}
//             ]).then(
//                 (db) => {
//                     console.log('db pokemon.saved saved, now counting');
//                     db.saved.count().then(c => document.getElementById('count').innerHTML = c)
//                 });
//             // const data = await loadData();
//             // const addPromise = await vipDB.productTable.bulkAdd(data).then( (x) => console.log(x));
//             // console.log ("Done populating.", data);
//         }
//     });
//
