import 'whatwg-fetch';
const url = 'http://pandora:4000/hinsyu';
const loadTokuno = () => new Promise(resolve =>
{
	fetch(url).then(res => res.json()).then(json =>
	{
		resolve(json.map(({ hinsyu_code, hinmei }) =>
		{
			return { value: hinsyu_code, label: `${hinsyu_code} ${hinmei}` };
		}));
	})
	.catch(err =>
	{
		throw err;
	});
});
export default loadTokuno;