/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

import React from 'react';
import ReactDOM from 'react-dom';
import CompEx from './compex';
import CompDev from './compdev';

const cont = document.getElementById('exemple');
const cont_dev = document.getElementById('exemple-device');
if(cont){
	const dev = cont.dataset.devices;
	const stat = cont.dataset.status;
	ReactDOM.render(<CompEx devices={dev} status={stat} />, cont);
	
}
else if(cont_dev){
	const dev = cont_dev.dataset.device;
        ReactDOM.render(<CompDev device={dev}/>, cont_dev);
}
