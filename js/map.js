function nextMarker() {
	if( currentMarker==null ) {
		return false;
	}

	if( currentMarker['next']==0 ) {
		return false;
	}

	currentMarker.infowindow.close();
	google.maps.event.trigger( currentMarker['next'], 'click');
}

function prevMarker() {
	if( currentMarker==null ) {
		return false;
	}

	if( currentMarker['prev']==0 ) {
		return false;
	}

	currentMarker.infowindow.close();
	google.maps.event.trigger( currentMarker['prev'], 'click');
}

function buildNextPrevPointers(markers) {
	if( markers.length==0 ) {
		return;
	} else if ( markers.length==1 ) {
		markers[0]['mrk']['next'] = 0;
		markers[0]['mrk']['prev'] = 0;
	} else {
		markers[0]['mrk']['prev'] = markers[ markers.length-1 ]['mrk'];
		markers[ markers.length-1 ]['mrk']['next'] = markers[0]['mrk'];

		for(var i=1; i<markers.length; i++) {
			markers[i]['mrk']['prev'] = markers[i-1]['mrk'];
			markers[i-1]['mrk']['next'] = markers[i]['mrk'];
		}
	}
}

function showHideMarker(marker, visible) {
	if( visible ) {
		marker.setVisible(true);
	} else {
		marker.infowindow.close();
		marker.setVisible(false);
	}
}

function findCategory(cat, marker) {
	for(var i=0; i<marker.items.length; i++) {
		if( marker.items[i][0]==cat ) {
			return true;
		}
	}

	return false;
}

function findSituation(sit, marker) {
	for(var i=0; i<marker.items.length; i++) {
		if( marker.items[i][1]==sit ) {
			return true;
		}
	}

	return false;
}

function findInterest(inter, marker) {
	for(var i=0; i<marker.items.length; i++) {
		if( marker.items[i][2]==inter ) {
			return true;
		}
	}

	return false;
}

function findCatWithSit(cat, sit, marker) {
	for(var i=0; i<marker.items.length; i++) {
		if( marker.items[i][0]==cat &&
			marker.items[i][1]==sit ) {
			return true;
		}
	}

	return false;
}


function matchFiltersItem(marker, cats, sits) {
	var hasCat = false;
	var hasSit = false;

	if( sits.length==0 ) {
		hasSit = true;
	}

	if( cats.length==0 ) {
		hasCat = true;
	}

	if( hasSit && !hasCat ) {
		for(var i=0; i<cats.length; i++) {
			if( findCategory(cats[i], marker) ) {
				hasCat = true;
				break;
			}
		} // for
	} else if( !hasSit && hasCat ) {
		for(var i=0; i<sits.length; i++) {
			if( findSituation(sits[i], marker) ) {
				hasSit = true;
				break;
			}
		} // for
	} else {
		for(var i=0; i<cats.length; i++) {
			for(var j=0; j<sits.length; j++) {
				if( findCatWithSit(cats[i], sits[j], marker) ) {
					hasCat = true;
					hasSit = true;
					break;
				}
			}
		} // for
	}

	return hasCat && hasSit;
}

function matchFiltersInt(marker, ints) {
	if( ints.length==0 ) {
		return true;
	}

	for(var i=0; i<ints.length; i++) {
		if( findInterest(ints[i], marker) ) {
			return true;
		}
	} // for

	return false;
}

function showAllActive() {
	for(var i=0; i<activeMarkers.length; i++) {
		showHideMarker(activeMarkers[i].mrk, true);
	}
}

// funcoes menu mostrar
function showAll() {
	$('.checks').hide();
	activeMarkers = markers_settings;
	for(var i=0; i<activeMarkers.length; i++) {
		showHideMarker(activeMarkers[i].mrk, true);
	}
	buildNextPrevPointers( activeMarkers );
}

function showPeople() {
	$('.checks').hide();
	activeMarkers = new Array();
	for(var i=0; i<markers_settings.length; i++) {
		var isPessoa = ( markers_settings[i]['type']=='P' );
		var mrk = markers_settings[i].mrk;
		showHideMarker( mrk, isPessoa );
		if( isPessoa ) {
			activeMarkers.push( markers_settings[i] );
		}
	}
	buildNextPrevPointers( activeMarkers );
}

function showInstitutions() {
	$('.checks').hide();
	activeMarkers = new Array();
	for(var i=0; i<markers_settings.length; i++) {
		var isInst = ( markers_settings[i]['type']=='I' );
		var mrk = markers_settings[i].mrk;
		if( isInst ) {
			activeMarkers.push( markers_settings[i] );
		}
		showHideMarker( mrk, isInst );
	}
	buildNextPrevPointers( activeMarkers );
}


// funcoes menu filtrar
function showFilterItem() {
	$('.checks').hide();
	$('#filtro_itens').toggle();
}

function showFilterInt() {
	$('.checks').hide();
	$('#filtro_ints').toggle();
}

function filterItem() {

	var checkedCats = Array();
	$('.filterItemCat:checked').each(function() {
		checkedCats.push( $(this).val() );
	});

	var checkedSits = Array();
	$('.filterItemSit:checked').each(function() {
		checkedSits.push( $(this).val() );
	});

	if( checkedCats.length==0 && checkedSits.length==0 ) {
		showAllActive();
		$('#filtro_itens .limpar').hide();
		return;
	} else {
		$('#filtro_itens .limpar').show();
	}

	for(var i=0; i<activeMarkers.length; i++) {
		var mrk = activeMarkers[i];

		var match = matchFiltersItem(mrk, checkedCats, checkedSits);
		showHideMarker(mrk.mrk, match);
	}

}

function filterInt() {
	
	var checkedCats = Array();
	
	$('.filtroInstCat:checked').each(function() {
		checkedCats.push( $(this).val() );
	});

	if( checkedCats.length==0 ) {
		showAllActive();
		$('#filtro_ints .limpar').hide();
		return;
	} else {
		$('#filtro_ints .limpar').show();
	}

	for(var i=0; i<activeMarkers.length; i++) {
		var mrk = activeMarkers[i];

		var matchCats = matchFiltersInt(mrk, checkedCats, Array() );
		showHideMarker(mrk.mrk, matchCats);
	}

}

var radiusShown = true;

function hideRadiusCircles() {
	radiusShown = !radiusShown;
	for(var i=0; i<radiusCircles.length; i++) {
		radiusCircles[i].setVisible(radiusShown);
	}
}

// eventos nos seletores
$(document).ready(function(){

	$('html').click(function(){
		$('.checks').hide();
		$('#filtro_texto').hide();
	});

	$('.checks, #filter-item, #filter-inst, #local').click(function(e) {
		e.stopPropagation(); 
	});

	// raios
	$('#raios').click(function(){
		hideRadiusCircles();
	});

	// localizacao
	$('#local').click(function(){
		$('.checks').hide();
		$('#filtro_texto').toggle();
	});
	
	// mostrar
	$('#show-all').click(function(){
		showAll();
		$(this).addClass('selected');
		$('#show-pessoas').removeClass('selected');	
		$('#show-inst').removeClass('selected');	
	});
	$('#show-pessoas').click(function(){
		showPeople();
		$(this).addClass('selected');
		$('#show-all').removeClass('selected');	
		$('#show-inst').removeClass('selected');	
	});
	$('#show-inst').click(function(){
		showInstitutions();
		$(this).addClass('selected');
		$('#show-pessoas').removeClass('selected');	
		$('#show-all').removeClass('selected');	
	});
	
	// filtrar
	$('#filter-item').click(function(){
		showFilterItem();
	});
	$('#filter-inst').click(function(){
		showFilterInt();
	});

});

function limparFiltroItem( ) {
	$('.filterItemCat').prop('checked', false);
	$('.filterItemSit').prop('checked', false);
	filterItem();
}

function limparFiltroInts( btn ) {
	$('.filtroInstCat').prop('checked', false);
	filterInt();
}

