const form = document.querySelector('.signup form'),
	continueBtn = form.querySelector('.button input'),
	errorText = form.querySelector('.error-text');

form.onsubmit = (e) => {
	e.preventDefault();
};

continueBtn.onclick = () => {
	let formData = new FormData(form);

	fetch('./../../php/signup.php', {
		method: 'POST',
		body: formData,
	})
		.then((res) => res.json())
		.then((data) => {
			if (data.result === 'success') {
				location.href = 'users.php';
			} else {
				errorText.style.display = 'block';
				errorText.textContent = data.result.error_msg;
			}
		})
		.catch((err) => {
			errorText.style.display = 'block';
			errorText.textContent = data.result.error_msg;
		});
};
