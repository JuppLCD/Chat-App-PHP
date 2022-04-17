const form = document.querySelector('.typing-area'),
	inputField = form.querySelector('.input-field'),
	sendBtn = form.querySelector('button'),
	chatBox = document.querySelector('.chat-box');

// UNIQUES ID
const outgoing_id = form.dataset.outgoing_id;
const incoming_id = form.querySelector('.incoming_id').value;

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

chatBox.onmouseenter = () => {
	chatBox.classList.add('active');
};

chatBox.onmouseleave = () => {
	chatBox.classList.remove('active');
};

function scrollToBottom() {
	chatBox.scrollTop = chatBox.scrollHeight;
}

// SOCKETS

const websocket_server = new WebSocket('ws://localhost:8080/');

websocket_server.onopen = function (e) {
	websocket_server.send(
		JSON.stringify({
			type: 'connection',
			in_file: 'CHAT',
			outgoing_id,
			incoming_id,
		})
	);
};

websocket_server.onerror = function (err) {
	console.log('err');
};

websocket_server.onmessage = function (res) {
	const data = JSON.parse(res.data);
	//  data.messages;

	switch (data.type) {
		case 'message':
			// Cuando llega un mensaje
			incomingMessages(data);

			break;
		case 'connection':
			// Cuando se conecta el usuario
			// En caso de que este en chat me traigo la lista de todos los mensajes entre estos usuarios

			// './../../php/get-chat.php';
			incomingMessages(data);
			break;
	}
};

// Events
sendBtn.onclick = sendMessage;
inputField.onkeyup = function (e) {
	if (e.keyCode == 13 && !e.shiftKey) {
		sendMessage();
	}
};

function sendMessage() {
	const chat_msg = inputField.value;

	websocket_server.send(
		JSON.stringify({
			type: 'message',
			in_file: 'CHAT',
			outgoing_id,
			chat_msg,
			incoming_id,
		})
	);
	inputField.value = '';
	scrollToBottom();
}

function incomingMessages(data) {
	if (chatBox.querySelector('.text')) {
		chatBox.removeChild(chatBox.querySelector('.text'));
	}
	chatBox.innerHTML += data.messages;
	if (!chatBox.classList.contains('active')) {
		scrollToBottom();
	}
}

function scrollToBottom() {
	chatBox.scrollTop = chatBox.scrollHeight;
}

scrollToBottom();
