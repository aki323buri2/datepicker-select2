import 'babel-polyfill';
import React from 'react';
import { render } from 'react-dom';
import './main.scss';
Promise.resolve().then(e =>
{
	render(<App/>, document.body.appendChild(document.createElement('div')));
});
import Appbar from './Appbar';
import Datepicker from './Datepicker';
import Selector from './Selector';
const App = ({
}) => (
	<div className="app">
		<Appbar/>
		<div className="container box">
			<div className="fields date-span">
				<div className="title">計上日 : </div>
				<Datepicker/>
				<div className="delimiter">～</div>
				<Datepicker/>
			</div>
			<div className="fields date-span">
				<div className="title">伝票日付 : </div>
				<Datepicker/>
				<div className="delimiter">～</div>
				<Datepicker/>
			</div>
			<div className="fields tokuno-selector">
				<div className="title">得意先CD : </div>
				<div style={{ width: 400 }}>
					<Selector/>
				</div>
			</div>
		</div>
	</div>
);