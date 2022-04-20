const searchBar = document.querySelector('.search input'),
	searchIcon = document.querySelector('.search button'),
	usersList = document.querySelector('.users-list');

searchIcon.onclick = () => {
	searchBar.classList.toggle('show');
	searchIcon.classList.toggle('active');
	searchBar.focus();
	if (searchBar.classList.contains('active')) {
		searchBar.value = '';
		searchBar.classList.remove('active');
	}
};

function debounce(func, timeout = 500) {
	let timer;
	return (...args) => {
		clearTimeout(timer);
		timer = setTimeout(() => {
			func.apply(this, args);
		}, timeout);
	};
}

searchBar.onkeyup = debounce(() =>
	(() => {
		let searchTerm = searchBar.value;
		if (searchTerm != '') {
			searchBar.classList.add('active');
		} else {
			searchBar.classList.remove('active');
			fetch('./../../php/users.php', {
				method: 'GET',
			})
				.then((res) => res.text())
				.then((data) => {
					usersList.innerHTML = data;
				});
		}

		fetch('./../../php/search.php', {
			method: 'POST',
			body: new URLSearchParams('searchTerm=' + searchTerm),
			headers: { 'Content-type': 'application/x-www-form-urlencoded' },
		})
			.then((res) => res.text())
			.then((data) => {
				usersList.innerHTML = data;
			});
	})()
);

// SOCKETS
const outgoing_id = usersList.dataset.unique_id;

const websocket_server = new WebSocket('ws://localhost:8080/');

websocket_server.onopen = function (e) {
	websocket_server.send(
		JSON.stringify({
			type: 'connection',
			in_file: 'USERS',
			outgoing_id,
		})
	);
};

websocket_server.onerror = function (err) {
	console.log('err');
};

websocket_server.onmessage = function (res) {
	const data = JSON.parse(res.data);
	console.log(data);
	switch (data.type) {
		case 'message':
			const div = document.createElement('div');
			div.innerHTML = data.messages;

			const altUserIncoming = div.querySelector('img').alt;
			const messageUserIncoming = div.querySelector('.details p').textContent;

			console.log(usersList.querySelector(`a img[alt='${altUserIncoming}']`));
			const UserIncoming = usersList.querySelector(`a img[alt='${altUserIncoming}']`).parentNode.parentNode;

			console.log(UserIncoming);

			UserIncoming.parentNode.removeChild(UserIncoming);

			const newMessage =
				messageUserIncoming.length > 28 ? messageUserIncoming.split(0, 28) + '...' : messageUserIncoming;

			UserIncoming.querySelector('.details p').textContent = newMessage;

			usersList.insertAdjacentElement('afterbegin', UserIncoming);

			break;
		case 'connection':
			usersList.innerHTML = data.user_list;
			break;
	}
};
