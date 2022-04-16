const form = document.querySelector('.typing-area'),
	incoming_id = form.querySelector('.incoming_id').value,
	inputField = form.querySelector('.input-field'),
	sendBtn = form.querySelector('button'),
	chatBox = document.querySelector('.chat-box');

form.onsubmit = (e) => {
	e.preventDefault();
};

inputField.focus();
inputField.onkeyup = () => {
	if (inputField.value != '') {
		sendBtn.classList.add('active');
	} else {
		sendBtn.classList.remove('active');
	}
};

sendBtn.onclick = () => {
	let formData = new FormData(form);

	fetch('./../../php/insert-chat.php', {
		method: 'POST',
		body: formData,
	}).then((res) => {
		if (res.ok) {
			inputField.value = '';
			scrollToBottom();
		}
	});
};
chatBox.onmouseenter = () => {
	chatBox.classList.add('active');
};

chatBox.onmouseleave = () => {
	chatBox.classList.remove('active');
};

setInterval(() => {
	fetch('./../../php/get-chat.php', {
		method: 'POST',
		body: new URLSearchParams('incoming_id=' + incoming_id),
		headers: { 'Content-type': 'application/x-www-form-urlencoded' },
	})
		.then((res) => res.text())
		.then((data) => {
			chatBox.innerHTML = data;
			if (!chatBox.classList.contains('active')) {
				scrollToBottom();
			}
		});
}, 500);

function scrollToBottom() {
	chatBox.scrollTop = chatBox.scrollHeight;
}
