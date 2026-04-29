function preview(image, target, type = "image") {
	if (image) {
		const reader = new FileReader();
		reader.readAsDataURL(image);
		reader.onload = () => {
			if (type == "image") {
				target.src = reader.result;
			} else {
				target.style.background = `url(${reader.result}) 50%`;
			}
		};
	}
}

window.preview = preview;
