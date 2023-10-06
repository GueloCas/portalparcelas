export default val => {
	if (['', '0', 'false'].indexOf(val) !== -1)
		val = false
	return val
}