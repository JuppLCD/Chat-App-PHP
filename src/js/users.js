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

setInterval(() => {
	fetch('./../../php/users.php', {
		method: 'GET',
	})
		.then((res) => res.text())
		.then((data) => {
			if (!searchBar.classList.contains('active')) {
				usersList.innerHTML = data;
			}
		});
}, 500);
