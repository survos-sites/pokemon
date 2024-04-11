import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

import './components/loader.js';

// import './db.js';

import 'https://cdn.jsdelivr.net/npm/onsenui@2.12.8/js/onsenui.min.js';
window.ons = ons; // make global
// console.log('ons');

import 'onsenui/css/onsenui.min.css'
import 'onsenui/css/onsen-css-components.min.css'
import 'onsenui/css/onsenui-fonts.min.css'
