<script>
	function showPlace(place) {
		var places = document.getElementsByClassName('place');
		for (var i = 0; i < places.length; i++) {
			places[i].style.display = 'none';
		}
		document.getElementById(place).style.display = 'block';
	}
	window.onload = function() {
		showPlace('inicio');
	};
</script>
