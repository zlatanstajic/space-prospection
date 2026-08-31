(function () {
	'use strict';

	var navigation = document.getElementById('navigation');

	if (!navigation) {
		return;
	}

	var button = document.createElement('button');
	button.id = 'mobile-navigation';
	button.type = 'button';
	button.setAttribute('aria-controls', 'navigation');
	button.setAttribute('aria-expanded', 'false');
	button.setAttribute('aria-label', 'Open navigation');
	navigation.parentNode.insertBefore(button, navigation);

	button.addEventListener('click', function () {
		var isOpen = button.getAttribute('aria-expanded') === 'true';

		button.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
		button.setAttribute('aria-label', isOpen ? 'Open navigation' : 'Close navigation');

		if (isOpen) {
			navigation.removeAttribute('style');
		} else {
			navigation.style.display = 'block';
		}
	});
}());
