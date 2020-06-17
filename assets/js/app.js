// Import Css
import '../css/app.scss';

// Import JQuery
var $ = require('jquery');

// Définition d'une variable global.$ pour rendre Jquery accessible sur tt les pages du site
global.$ = global.jQuery = $;

// Import de bootrsap et popper.js
require('bootstrap');

// Log pour vérfier si le script est bien charger sur nos page dans le console inspector du navigateur
//console.log('script charger');