import fs from 'fs-extra';
import path from 'path';
import iconv from 'iconv-lite';
import csv from 'csv';
import readline from 'readline';
import _ from 'lodash';

const file = path.resolve(__dirname, '../統計用分類-品種CD（20170220現在）.csv');
const table = 'hinsyu';
const columns = [
	'hinsyu_code', 
	'hinmei', 
	'size', 
	'yoryo', 
	'yoryo_tani', 
	'irisu', 
	'middle_code', 
	'large_name', 
	'middle_name', 
	'small_name', 
	'syurui', 
	'keijo_bui', 
	'kako_ho', 
	'aki_1', 
	'aki_2', 
	'aki_3', 
	'aki_4', 
];
Promise.resolve()
.then(e => new Promise(resolve =>
{
	const stream = fs.createReadStream(file);
	const read = readline.createInterface({ input: stream });
	let lines = 0;
	read.on('line', line => 
	{
		lines++;
	})
	.on('close', () =>
	{
		resolve(lines);
	});
}))
.then(line =>
{
	console.log(line);
	let done = 0;
	let skip = 0;
	let lines = [];
	const blockSize = 10000;
	const nulls = Array(columns.length).fill(null);
	const valuesSQL = data => 
	{
		data = [ ...data, ...nulls ].slice(0, columns.length);
		data.push('datetime(\'now\')');
		data.push('datetime(\'now\')');
		const values = data.map(value => value === null ? 'null' : `'${value}'`);

		return `(${values.join(',')})`;
	};
	const stream = fs.createReadStream(file)
		.pipe(iconv.decodeStream('SJIS'))
		.pipe(iconv.encodeStream('UTF-8'))
		.pipe(csv.parse())
		.on('data', data =>
		{
			if (data[0] === '')
			{
				skip++;
				return;
			}

			lines.push(valuesSQL(data));
			done++;
			
			if (lines.length === blockSize)
			{
				stream.emit('block', lines);
				lines = [];
			}
		})
		.on('end', () =>
		{
			if (lines.length > 0)
			{
				stream.emit('block', lines);
			}
			console.log({ done, skip });
		})
		.on('block', lines =>
		{
			const sql = [
				`insert or replace ${table} (`, 
				columns.join(','), 
				')', 
			];
			console.log('block', lines.length);
		})
		;
});
