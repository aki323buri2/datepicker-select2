import React from 'react';
import { findDOMNode } from 'react-dom';
import moment from 'moment';
import classnames from 'classnames';
import './Datepicker.scss';
const weekdays = [
	'日', '月', '火', '水', '木', '金', '土',
]; 
const format = 'YYYY-MM-DD';
const parseFormats = [
	format, 
	'Y-M-D', 
	'Y.M.D', 
	'Y/M/D', 
	'M-D', 
	'M.D', 
	'M/D', 
	'YYYYMMDD', 
	'YYMMDD', 
	'MMDD', 
]; 
const today = moment();
const Datepicker = class extends React.Component
{
	constructor(props)
	{
		super(props);
		this.state = {
			start: moment().startOf('month'), 
			selected: null, 
			value: '', 
			invalid: true, 
			active: false, 
		};
	}
	dom() 
	{
		return findDOMNode(this);
	}
	componentDidMount()
	{
		this.input = this.dom().querySelector('.datepicker-input .input');
		this.calendar = this.dom().querySelector('.calendar');
	}
	render()
	{
		const { start, selected, value, invalid, active } = this.state;
		const year = start.year();
		const month = start.month() + 1;
		const first = start.clone().day('sunday');
		const last = start.clone().endOf('month').day('saturday');
		const days = Array(last.diff(first, 'days') + 1).fill(0).map((v, i) => 
		{
			const day = first.clone().add(i, 'days');
			day.today = day.isSame(today, 'day');
			day.disabled = day.isSame(start, 'month') === false;
			day.selected = day.isSame(selected, 'day');
			return day;
		});
		return (
			<div className="datepicker">
				<div className="datepicker-input">
					<p className="control has-icons-left">
						<input type="text"
							className={classnames([
								'input', 
							], {
								'is-invalid': invalid, 
							})}
							value={value}
							onChange={this.onChange}
							onBlur={this.onBlur}
						/>
						<span
							className="icon is-small is-left linked"
							onClick={this.onButtonClick}
						>
							<i className="fas fa-calendar"></i>
						</span>
					</p>
				</div>
				<div 
					className={classnames([
						'calendar', 
					], {
						active, 
					})}
				>
					<div className="calendar-month">
						<div className="prev" onClick={e => this.monthAdd(-1)}>&lt;</div>
						<div className="month">{year}年{month}月</div>
						<div className="next" onClick={e => this.monthAdd( 1)}>&gt;</div>
					</div>
					<div className="calendar-weekdays">
					{weekdays.map((weekday, i) => 
						<div key={i}
							className={classnames('weekday', `w${i}`)}
						>
							{weekday}
						</div>
					)}
					</div>
					<div className="calendar-days">
					{days.map((day, i) => 
						<div key={i}
							className={classnames('day', `w${day.day()}`, {
								'is-today': day.today, 
								'is-disabled': day.disabled, 
								'is-selected': day.selected, 
							})}
							onClick={e => this.onSelect(day)}
						>
							{day.date()}
						</div>
					)}
					</div>
				</div>
			</div>
		);
	}
	parseValue(value)
	{
		return moment(value, parseFormats, true);
	}
	onChange = e =>
	{
		const value = e.target.value;
		const invalid = !this.parseValue(value).isValid();
		this.setState({ value, invalid });
	}
	onBlur = e =>
	{
		const value = e.target.value;
		const parse = this.parseValue(value);
		if (parse.isValid())
		{
			this.setState({
				value: parse.format(format),
				invalid: !parse.isValid()
			});
		}
	}
	onButtonClick = e =>
	{
		this.setState({ active: true });
	}
	monthAdd = add =>
	{
		this.setState({
			start: this.state.start.add(add, 'month').startOf('month')
		});
	}
	onSelect = day =>
	{
		this.setState({
			selected: day, 
			value: day.format(format), 
			invalid: false, 
		});		
		setTimeout(() => this.input.focus(), 50);
		setTimeout(() => this.setState({ active: false }), 50);
	}
};
export default Datepicker;