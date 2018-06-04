import React from 'react';
import Select from 'react-select/lib/Async';
import loadTokuno from './loadTokunoApi'
const Selector = class extends React.Component
{
	render()
	{
		return (
			<div className="selector">
				<Select
					isMulti
					cacheOptions
					defaultOptions
					name="form-field-name"
					loadOptions={this.getOptions}
				/>
			</div>
		);
	}
	getOptions = input => loadTokuno();
};
export default Selector;