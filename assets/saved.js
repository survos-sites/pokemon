// import Dexie from 'https://cdn.jsdelivr.net/npm/dexie@4.0.1/+esm';
import Dexie from 'dexie';
var db = new Dexie('pokemon');
// db.delete().then (()=>db.open());

db.version(1).stores({
    savedTable: "++id,name",
    productTable: "++id,price,brand,category"
});
console.log('async vipDB')
db.on('ready', async vipDB => {
    db.savedTable.bulkPut([
                { id: 1, name: "test pokemon"}
            ]).then(
                (db) => {
                    console.log('db pokemon.saved saved, now counting');
                    db.savedTable.count().then(c => document.getElementById('count').innerHTML = c)
                });

    const count = await vipDB.productTable.count();
    if (count > 0) {
        console.log("Already populated, count: " + count);
    } else {
        const data = await loadData();
        const addPromise = await vipDB.productTable.bulkAdd(data).then( (x) => console.log(x));
        console.log ("Done populating.", data);
    }
});

console.log('open db');
db.open();

console.log('count');
db.productTable.count().then( (c) => {console.log(c); document.getElementById('count').innerText = c});
async function loadData() {
    let url = 'https://dummyjson.com/products';
    const response = await fetch(url);
    return await response.json().then(data => data.products)
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
