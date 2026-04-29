module.exports = {
	getRandomRGBColor() {
		return `rgba(${Math.random() * 255}, ${Math.random() *
			255}, ${Math.random() * 255}, ${Math.random()})`;
	}
};
