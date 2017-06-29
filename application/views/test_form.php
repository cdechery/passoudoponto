<script type="text/javascript" src="http://maps.google.com/maps/api/js?libraries=places"></script>
<script type="text/javascript">
//<![CDATA[
var map; // Global declaration of the map
var userLocMarker = null;
var placesService;
var placesAutocomplete;

function updateFormLatLng(lat, lng) {
	document.userData.lat.value = lat;
	document.userData.lng.value = lng;
}

function createMarker( markerOptions ) {
	if( userLocMarker!=null ) {
		return false;
	}

	var marker = new google.maps.Marker( markerOptions );
	marker.set("content", "Sua localização");

	userLocMarker = marker;
	google.maps.event.addListener(marker, "dragend", function(event) {
		updateFormLatLng(event.latLng.lat(), event.latLng.lng());
	});
		
	updateFormLatLng( marker.getPosition().lat(),
		marker.getPosition().lng() );
}

function initialize() {
	
	var myLatlng = new google.maps.LatLng( <?php echo $params['mapa']['default_loc']?> );
	var myOptions = {
  		zoom: 13,
		center: myLatlng,
  		mapTypeId: google.maps.MapTypeId.ROADMAP}
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
	google.maps.event.addListener(map, "dblclick", function(event) {
		if( userLocMarker!=null ) {
			userLocMarker.setMap(null);
			userLocMarker = null;
		}
		createMarker({ map: map, position:event.latLng, draggable: true });
	});
} // initialize

window.onload = initialize;
</script>
<div>
<form name="userData" action="<?php echo base_url()?>ocorrencia/insert" method="post">
<input type="hidden" name="lat">
<input type="hidden" name="lng">
<input type="hidden" name="usuario_id" value="1">
<div id="map_canvas" style="width: 250px; height: 250px"></div>
<br>
Linha do Ônibus: <input type="text" name="nr_onibus"><br>
Número de Ordem: <input type="text" name="nr_ordem"><br>
Tipo: <select name="tipo">
<option value="" selected></option>
<?php
	foreach ($tipos as $tipo) {
		echo '<option value="'.$tipo->id.'">'.$tipo->nome.'</option>\n';
	}
?>
</select><br>
<input type="submit">
</form>
</div>