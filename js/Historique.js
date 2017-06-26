$(document).ready(function(){

// $('.datepicker').live('focus', function(){
					// $(this).datepicker({dateFormat : "yy-mm-dd", 
								// autoSize : true, 
								// monthNames: [ "Janvier", "Février", "Mars", "April", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre" ],
								// dayNames: [ "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi" ],
								// dayNamesMin : [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ]
							// })
							// });
//===========================================================================================================================================================================
//===========================================================================================================================================================================
//==================================================================       CLASSE MENU      =================================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$('.menu, .menuAdministration').live('click', function(){
	var menu_id = this.id, page_contenu = "";
	$('.menu_administration_actions').fadeOut(100);
	if(menu_id == "men_ficheClient")
		page_contenu = "FicheClient/fiche_client.php";

	if(menu_id == "menu_administration" || menu_id == "menu_afficher_utilisateur" || menu_id == "menu_ajouter_utilisateur")
		$('.menu_administration_actions').slideDown(2000);
	if (page_contenu != "")
	{
		$('#divContainer').slideUp(1000);
		$('#divContainer').load(page_contenu);
		$('#divContainer').slideDown(1000);
	}
	else
		$('#divContainer').slideUp(2000).html('');
});

//===========================================================================================================================================================================
//===========================================================================================================================================================================
//=======================================================       CHANGEMENT DU CLIENT DE BORDEREAU      ======================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$(":radio[name=client_borderau]").live("change", function(){
	// alert(this.value);
	if (this.value == '0')
	{
		$('#client_existant').slideUp('slow');
		$('#nouveau_client').slideDown('slow');
	}
	else
	{
		$('#nouveau_client').slideUp('slow');
		$('#client_existant').slideDown('slow');
	}
});


//===========================================================================================================================================================================
//===========================================================================================================================================================================
//=======================================================       CHANGEMENT DU VOYAGE DE BORDEREAU      ======================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$("#voyage_bordereau").live("change", function(){
	if (this.value != '')
	{
		id_voyage = this.value;
		$('#conteneurs_bordereau').load('Bordereau/list_conteneurs_voyage.php', {id_voyage:id_voyage});
		$("#enregistrer_bordereau").show();
	}
	else
	{
		$("#enregistrer_bordereau").hide();
		$('#conteneurs_bordereau').html("");
	}
});


//===========================================================================================================================================================================
//===========================================================================================================================================================================
//==========================================================       CHANGEMENT DU BL DE LA FACTURE      ======================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$("#bl_bordereau").live("change", function(){
	if (this.value != '')
	{
		id_bl = this.value;
		$('#conteneurs_facture').load('Facture/list_conteneurs_facture.php', {id_bl:id_bl});
		$("#enregistrer_facture").show();
	}
	else
	{
		$("#enregistrer_facture").hide();
		$('#conteneurs_facture').html("");
	}
});



//===========================================================================================================================================================================
//===========================================================================================================================================================================
//===========================================================       ENREGISTREMENT DE BORDEREAU      ========================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$("#enregistrer_bordereau").live("click", function(){
	var bordereau = $("#numero_bordereau").val(), id_voyage = $("#voyage_bordereau").val(), listConteneurs = "0", messageClient = "", nbrSel = $("input[type='checkbox']:checked").length;
	var codeClient = 0, champs_vide = false, num_bordereau = 1, $messageErreurBordereau = "";
	if(bordereau == "")
		$("#numero_bordereau").addClass("input_vide");
	if(id_voyage == "")
		$("#voyage_bordereau").addClass("input_vide");
	if ($(":radio[name=client_borderau]:checked").val() == "0")
	{
		var nom = $(".nouveau_client_bordereau[name =nom]").val(), adresse = $(".nouveau_client_bordereau[name =adresse]").val();
		var tph = $(".nouveau_client_bordereau[name =tph]").val(), cin = $(".nouveau_client_bordereau[name =cin]").val();
		var mail = $(".nouveau_client_bordereau[name =e-mail]").val();
		if (nom == "")
		{
			champs_vide = true;
			$(".nouveau_client_bordereau[name =nom]").addClass("input_vide");
		}
		if (nom == "")
		{
			champs_vide = true;
			$(".nouveau_client_bordereau[name =nom]").addClass("input_vide");
		}
		if (adresse == "")
		{
			champs_vide = true;
			$(".nouveau_client_bordereau[name =adresse]").addClass("input_vide");
		}
		if (tph == "")
		{
			champs_vide = true;
			$(".nouveau_client_bordereau[name =tph]").addClass("input_vide");
		}
		if (cin == "")
		{
			champs_vide = true;
			$(".nouveau_client_bordereau[name =cin]").addClass("input_vide");
		}
		if (mail == "")
		{
			champs_vide = true;
			$(".nouveau_client_bordereau[name =e-mail]").addClass("input_vide");
		}
	}
	else
	{
		codeClient = $("#code_client_existant").val();
		$.ajax({
		   url: "Client/rechercher_client.php",
		   async: false,
		   type: 'POST',
		   data: {codeClient:codeClient},
		   success: function(retour){
						var tabRetour = retour.split('@@');
						codeClient = tabRetour[1];
						nomClient = tabRetour[2];
						if (nomClient != "0")
							$("#information_client").html(nomClient);
					},
		   error: function(){
					alert("ERREUR LORS DE LA RECHERCHE DU CLIENT");
					}
		 });
		
	}
	for (i = 0; i < nbrSel; i++)
		listConteneurs += "@@"+$("input[type='checkbox']:checked").eq(i).attr("id").replace("conteneur_bordereau","");
	if (bordereau != "" && listConteneurs != "0" && !champs_vide)
	{
		if ($(":radio[name=client_borderau]:checked").val() == "0")
			$.ajax({
			   url: "Client/enregistrer_client.php",
			   async: false,
			   type: 'POST',
			   data: {nom:nom, adresse:adresse, tph:tph, cin:cin, mail:mail},
			   success: function(retour){
							var tabRetour = retour.split('@@');
							codeClient = tabRetour[1];
						},
			   error: function(){
						alert("ERREUR LORS DE L'INSERTION DU NOUVEAU CLIENT");
						}
			});
			$.ajax({
			   url: "Bordereau/rechercher_bordereau.php",
			   async: false,
			   type: 'POST',
			   data: {bordereau:bordereau},
			   success: function(retour){
							var tabRetour = retour.split('@@');
							num_bordereau = tabRetour[1];
						},
			   error: function(){
						alert("ERREUR LORS DE LA RECHERCHE DU NUMERO DE BL");
						}
			 });
		if (codeClient != 0 && num_bordereau == 0)
			$.post('Bordereau/enregistrer_infos_bordereau.php',{bordereau:bordereau, listConteneurs:listConteneurs, id_voyage:id_voyage, codeClient:codeClient},function(data){
					var tabRetour = data.split('@@');
					var etatOp = tabRetour[1];
					if(etatOp == "1")
						alert("INFORMATIONS DU BL SONT ENREGISTRÉES AVEC SUCCÈS");
					else
						alert(data);
				});
		else
		{
			$messageErreurBordereau = (num_bordereau == 0) ? "MERCI DE VERIFIER LE CODE CLIENT":"LE NUMERO DU BL EXISTE DEJA";
			alert($messageErreurBordereau);
		}
	}
	else
		alert("MERCI DE REMPLIRE TOUS LES CHAMPS !!! ");
});


//===========================================================================================================================================================================
//===========================================================================================================================================================================
//============================================================      INFORMATION DU CLIENT PAR CODE       ====================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$('#code_client_existant').live("blur", function(){
	if (this.value != '' && $(":radio[name=client_borderau]:checked").val() == "1")
	{
		codeClient = this.value;
		$.ajax({
		   url: "Client/rechercher_client.php",
		   async: false,
		   type: 'POST',
		   data: {codeClient:codeClient},
		   success: function(retour){
						var tabRetour = retour.split('@@');
						codeClient = tabRetour[1];
						nomClient = tabRetour[2];
						if (nomClient != "0")
							$("#information_client").html(nomClient);
						else
							$("#information_client").html("LE CLIENT N'EXISTE PAS");
					},
		   error: function(){
					alert("ERREUR LORS DE LA RECHERCHE DU CLIENT");
					}
		 });
	}
// alert("Mahi");
});

$('#code_client_existant').live("keyup", function(){
	
	if (this.value != '' && $(":radio[name=client_borderau]:checked").val() == "1" && this.value.length == 4)
	{
		codeClient = this.value;
		$.ajax({
		   url: "Client/rechercher_client.php",
		   async: false,
		   type: 'POST',
		   data: {codeClient:codeClient},
		   success: function(retour){
						var tabRetour = retour.split('@@');
						codeClient = tabRetour[1];
						nomClient = tabRetour[2];
						if (nomClient != "0")
							$("#information_client").html(nomClient);
						else
							$("#information_client").html("LE CLIENT N'EXISTE PAS");
					},
		   error: function(){
					alert("ERREUR LORS DE LA RECHERCHE DU CLIENT");
					}
		 });
	}
	else
		$("#information_client").html("");
	
// alert("Mahi");
});


//===========================================================================================================================================================================
//===========================================================================================================================================================================
//============================================       FONCTION RECHERCHE DUPPLICATION DANS LA LISTE DES CONTENEURS      ======================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

function dupplicationConteneur(idTable){
	var nbrConteneur = $('#'+idTable+' input').length, retour_dupp = false;
	for (i = 0; i < nbrConteneur; i++)
	{
		for (j = i+1 ; j < nbrConteneur; j++)
			if($('#'+idTable+' input').eq(i).val() == $('#'+idTable+' input').eq(j).val() && $('#'+idTable+' input').eq(j).val() != '')
			{
				// Rechercher une couleur aléatoire
				var couleur = "rgb(";
				for(coul = 0; coul < 3; coul++){
					couleur += Math.floor(Math.random()*256)+",";
				};
				couleur = couleur.replace(/,$/,")");
				if ($('#'+idTable+' input').eq(i).parent().parent().hasClass('dupplicatat'))
					$('#'+idTable+' input').eq(j).parent().parent().css('background-color',$('#'+idTable+' input').eq(i).parent().parent().css('background-color'));
				else
				{
					$('#'+idTable+' input').eq(i).parent().parent().css('background-color',couleur).addClass('dupplicatat');
					$('#'+idTable+' input').eq(j).parent().parent().css('background-color',couleur).addClass('dupplicatat');
				}
				retour_dupp = true;
			}
	}
	$('.dupplicatat').removeClass('dupplicatat');
	return retour_dupp;
}

//===========================================================================================================================================================================
//===========================================================================================================================================================================
//====================================================       FONCTION FILTRE SUR LA LISTE DES VOYAGES     ===================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

function filtreListeVoyages(){
	var nbrElementInput = $("input.filtre_voyage").length, nbrElementSelect = $("select.filtre_voyage").length, ListClause = '', ListVals = '', ListNams = '';
	for(i = 0; i < nbrElementInput; i++)
		ListClause += ' and `voyage`.`'+$("input.filtre_voyage").eq(i).attr("name")+'` like "'+$("input.filtre_voyage").eq(i).val()+'%" ';
	for(i = 0; i < nbrElementSelect; i++)
	{
		if($("select.filtre_voyage").eq(i).val() != "")
			ListClause += ' and `voyage`.`'+$("select.filtre_voyage").eq(i).attr('name')+'` = "'+$("select.filtre_voyage").eq(i).val()+'" ';
	}

	$('#liste_des_voyages').html('<br><br>Chargement en cours .... <br><img src= "Img/loading.gif">');
	$.post('Voyage/liste_voyages.php',{ListClause:ListClause},function(data){
				$('#liste_des_voyages').html(data).slideDown(1000);
			})
	
}


//===========================================================================================================================================================================
//===========================================================================================================================================================================
//===============================================================       CHANGER LE FOND D'INPUT VIDE      ===================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

// $('.filtre_voyage').live('keyup', function(){
	// filtreListeVoyages();
// });							

$('.filtre_voyage').live('change', function(){
	filtreListeVoyages();
});							
//===========================================================================================================================================================================
//===========================================================================================================================================================================
//===============================================================       CHANGER LE FOND D'INPUT VIDE      ===================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$(".input_vide").live("keyup", function(){
	// var input_id = this.id;
	// $('#'+input_id).css('background-color','white').removeClass('input_vide');
	$(this).css('background-color','white').removeClass('input_vide');
});

$(".input_vide").live("change", function(){
	$(this).css('background-color','white').removeClass('input_vide');
});
//===========================================================================================================================================================================
//===========================================================================================================================================================================
//===========================================================       SUPRIMER UN CONTENEUR D'UN VOYAGE      ==================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$('.supprimer_conteneur').live('click',function(){
	var indice = this.name.replace('supprimer_conteneur',''), idTable = this.alt;
	var id = this.id;
	alert('jlkjlfksjs l');
	if ($('#'+idTable+' tr').length > 2)
	{
		$('#'+id).parent().parent().css('background-color','red');
		$("#dialog-message").html('Voulez-vous supprimer ce conteneur ?');
		$("#dialog-message").dialog({
									  modal: true,
									  resizable: false,
									  buttons: {
									  "Oui": function() {
											 $('#'+id).parent().parent().remove();
											 $( this ).dialog( "close" );
											},
									  "Non": function() {
											  $('#'+id).parent().parent().css('background-color','#E9DEBE');
											  $( this ).dialog( "close" );
											}
										  }
									});
			
	}
	else
	{
		$("#dialog-message").html('Le voyage doit contenir au moins un conteneur');
		$("#dialog-message").dialog({
									  modal: true,
									  resizable: false,
									  buttons: {
									  Ok: function() {
											  $( this ).dialog( "close" );
											}
										  }
									});
	}
});

//===========================================================================================================================================================================
//===========================================================================================================================================================================
//================================================================       DEPOTER UN CONTENEUR      ==========================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$('.depotage_conteneur').live('click',function(){
	var id_conteneur = this.id.replace('deptage_conteneur',''), id_element = this.id, id_voyage = $(this).parent().parent().parent().parent().attr('id').replace('ListeConteneursVoyage_','');
	$.post('Voyage/depotage_conteneur.php',{id_conteneur:id_conteneur},function(data){
				var tabRetour = data.split('|||');
				if (tabRetour[1] == 1)
				{
					// $('#'+id_element).remove();
					$('#div_list_conteneurs_detail_voyage'+id_voyage).load('Voyage/list_conteneurs_detail_voyage.php', {id_voyage:id_voyage, ve:'A'});
					$("#dialog-message").html('Depotage fait avec succés');
					$("#dialog-message").dialog({
									  modal: true,
									  resizable: false,
									  buttons: {
									  Ok: function() {
											  $( this ).dialog( "close" );
											}
										  }
									});
				}
				else
				{
					$("#dialog-message").html(data);
					$("#dialog-message").dialog({
									  modal: true,
									  resizable: false,
									  buttons: {
									  Ok: function() {
											  $( this ).dialog( "close" );
											}
										  }
									});
				}
			})
});


//===========================================================================================================================================================================
//===========================================================================================================================================================================
//==================================================================       MAJ VILLE VOYAGE     =============================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$('.maj_ville_voyage').live('click',function(){
	var id_voyage = $(this).attr('id').replace('maj_ville_voyage',''), nbrTd = $("#ligne_voyage"+id_voyage+" td").length;
	$('#ligne_voyage'+id_voyage).after('<tr id = "maj_voyage_numero_'+id_voyage+'" class = "trDetaille"><td colspan = "'+nbrTd+'" align ="center" id ="td_maj_voyage_numero_'+id_voyage+'"><div id = "div_maj_voyage_numero_'+id_voyage+'">Chargement en cours .... <br><img src= "Img/loading.gif"></div></td></tr>');
	$(this).removeClass('maj_ville_voyage').addClass('masque_maj_ville_voyage').attr('src', 'Img/maj1.jpg');
	$.post('Voyage/maj_ville_voyage.php',{id_voyage:id_voyage},function(data){
				$('#td_maj_voyage_numero_'+id_voyage).slideDown(1500);
				$('#div_maj_voyage_numero_'+id_voyage).html(data);
				$('#div_maj_voyage_numero_'+id_voyage).slideDown(1500);
			})
});

$('.masque_maj_ville_voyage').live('click',function(){
	var id_voyage = $(this).attr('id').replace('maj_ville_voyage',''), nbrTd = $("#ligne_voyage"+id_voyage+" td").length;
	$(this).removeClass('masque_maj_ville_voyage').addClass('maj_ville_voyage').attr('src', 'Img/maj.jpg');
	$('#maj_voyage_numero_'+id_voyage).remove();
	
});


//===========================================================================================================================================================================
//===========================================================================================================================================================================
//=============================================================       SIGNALER L'ARRIVER D'UN VOYAGE      ===================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$('.arriver_voyage').live('click', function(){
	var indice = this.id.replace('arriver_voyage','');
	$("#dialog-message").html('ÊTES VOUS SURE QUE LE VOYAGE EST ARRIVÉ ?');
	$("#dialog-message").dialog({
      modal: true,
	  resizable: false,
      buttons: {
        "Oui": function() {
			$.post('Voyage/arriver_voyage.php',{id_voyage:indice},function(data){
						var tabRetour = data.split('|||');
						if (tabRetour[1] == 1)
						{
							$('#arriver_voyage'+indice).remove();
							$('#maj_ville_voyage'+indice).remove();
							$("#dialog-message").html('LE VOYAGE EST SIGNALÉ ARRIVÉ AVEC SUCCÈS');
								$( "#dialog-message" ).dialog({
									  modal: true,
									  resizable: false,
									  buttons: {
										Ok: function() {
										  $( this ).dialog( "close" );
										}
									  }
									});
						}
						else
						{
							$("#dialog-message").html(data);
							$("#dialog-message").dialog({
														  modal: true,
														  resizable: false,
														  buttons: {
														  Ok: function() {
																  $( this ).dialog( "close" );
																}
															  }
														});
						}
					})
        },
		"Non": function() {
          $( this ).dialog( "close" );
        }
      }
    });
});

//===========================================================================================================================================================================
//===========================================================================================================================================================================
//=============================================================       AFFICHER LE DETAIL D'UN VOYAGE      ===================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$('.afficher_detail_voyage').live('click', function(){
	var indice = this.id.replace('detail_voyage',''), nbrTd = $("#ligne_voyage"+indice+" td").length;
	$('#ligne_voyage'+indice).after('<tr id = "detail_voyage_numero_'+indice+'" class = "trDetaille"><td colspan = "'+nbrTd+'" align ="center" id ="td_detail_voyage_numero_'+indice+'"><div id = "div_detail_voyage_numero_'+indice+'">Chargement en cours .... <br><img src= "Img/loading.gif"></div></td></tr>');
	$('#detail_voyage'+indice).attr("src", "Img/cacher.jpg").removeClass('afficher_detail_voyage').addClass('masquer_detail_voyage');
	$.post('Voyage/detail_voyage.php',{id_voyage:indice},function(data){
				$('#td_detail_voyage_numero_'+indice).slideDown(1500);
				$('#div_detail_voyage_numero_'+indice).html(data);
				$('#div_detail_voyage_numero_'+indice).slideDown(1500);
			})
});

//===========================================================================================================================================================================
//===========================================================================================================================================================================
//=============================================================       MASQUER LE DETAIL D'UN VOYAGE      ====================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$('.masquer_detail_voyage').live('click',function(){
	var indice = this.id.replace('detail_voyage','');
	$('#div_detail_voyage_numero_'+indice).slideUp(1500);
	$('#detail_voyage_numero_'+indice).remove();
	$(this).attr("src", "Img/afficher.jpg").removeClass('masquer_detail_voyage').addClass('afficher_detail_voyage');
});

//===========================================================================================================================================================================
//===========================================================================================================================================================================
//====================================================       AJOUTER CONTENEUR DURANT DETAIL VOYAGE     =====================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$('.ajouter_conteneur_detail').live('click',function(){
	var indice = this.id.replace('ajouter_conteneur_voyage',''), verif_tableau = $('#ListeConteneursVoyageDetail'+indice), nbrElementTableau = $('#ListeConteneursVoyageDetail'+indice+' tr').length;
	if (verif_tableau.length)
	{
		$('#ListeConteneursVoyageDetail'+indice+' tr').eq(nbrElementTableau - 2).after($('#ListeConteneursVoyageDetail'+indice+' tr').eq(nbrElementTableau - 2).clone(true));
		var indexNomElt = $('#ListeConteneursVoyageDetail'+indice+' input:text :last').attr('name');
		var ancienIndice = indexNomElt.replace("numero_conteneur", ""), nvIndice = parseInt(ancienIndice) + 1, nbrSel = $('#ListeConteneursVoyageDetail'+indice+' select').length;
		$('#ListeConteneursVoyageDetail'+indice+' input:text :last').attr({'name':'numero_conteneur'+nvIndice,
																		   'id':'numero_conteneur'+nvIndice
																		  });
		$('#ListeConteneursVoyageDetail'+indice+' select').eq(nbrSel-2).attr({'name':'statut_conteneur'+nvIndice,
																		   'id':'statut_conteneur'+nvIndice
																		  });
		$('#ListeConteneursVoyageDetail'+indice+' select:last').attr({'name':'dimension_conteneur'+nvIndice,
																		   'id':'dimension_conteneur'+nvIndice
																		  });
		$('#ListeConteneursVoyageDetail'+indice+' img:last').attr({'name':'supprimer_conteneur'+nvIndice,
																		   'id':'supprimer_conteneur'+nvIndice
																		  });
	}
	else
	{
		var id_tableau_detail_nouveau_conteneur = 'ListeConteneursVoyageDetail'+indice;
		$.post('Voyage/info_conteneur.php',{id_tableau_detail_nouveau_conteneur:id_tableau_detail_nouveau_conteneur, indice_elt:indice},function(data){
				$('#conteneur_a_ajouter_apres_detail'+indice).html(data).fadeIn('sleep');
				$('#ListeConteneursVoyageDetail'+indice).append('<tr><td colspan = "4" align = "center"><input type = "button" value= "Enregistrer" class = "Enregistrer_Nouveaux_Conteneurs_Voyage" name = "ListeConteneursVoyageDetail'+indice+'" ><input type = "button" value= "Annuler" class = "Annuler_Nouveaux_Conteneurs_Voyage" name = "ListeConteneursVoyageDetail'+indice+'" ></td></tr>');
			})
	}
		
});

//===========================================================================================================================================================================
//===========================================================================================================================================================================
//===========================================================       AJOUTER UN CONTENEUR A UN VOYAGE      ===================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$('#Ajouter_Conteneur_Voyage').live('click', function(){
	$('#ListeConteneursVoyage').append($('#ListeConteneursVoyage tr :last').clone(true));
	var anc_numero_conteneur = $('#ListeConteneursVoyage tr :last input :first').attr('name');
	var anc_statut_conteneur = anc_numero_conteneur.replace("numero","statut");
	var anc_dimension_conteneur = anc_numero_conteneur.replace("numero","dimension");
	var anc_supprimer_conteneur = anc_numero_conteneur.replace("numero","supprimer");
	var indice = parseInt(anc_numero_conteneur.replace("numero_conteneur",""))+1;
	$('#ListeConteneursVoyage tr :last').css('background-color','#E9DEBE');
	$('#ListeConteneursVoyage tr :last').attr({
								'id':'ligne_conteneur'+indice
								});
	$('#ListeConteneursVoyage tr :last #'+anc_numero_conteneur).attr({'name':'numero_conteneur'+indice,
								'id':'numero_conteneur'+indice,
								'value' : ''
								});
	$('#ListeConteneursVoyage tr :last #'+anc_statut_conteneur).attr({'name':'statut_conteneur'+indice,
								'id':'statut_conteneur'+indice
								});
	$('#ListeConteneursVoyage tr :last #'+anc_dimension_conteneur).attr({'name':'dimension_conteneur'+indice,
								'id':'dimension_conteneur'+indice
								});
	$('#ListeConteneursVoyage tr :last #'+anc_supprimer_conteneur).attr({'name':'supprimer_conteneur'+indice,
								'id':'supprimer_conteneur'+indice
								});
});

//===========================================================================================================================================================================
//===========================================================================================================================================================================
//==============================================================       ENREGISTRER UN VOYAGE      ===========================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$('#Enregistrer_Voyage').live('click', function(){
	var nbrConteneur = $('#ListeConteneursVoyage input').length, nbrInputVoyage = $('.info_voyage').length, result_conteneur = false, result_voyage = false, result_dupp = false;
	var informations_voyage = $('#type_voyage').val(), message_destine_client_text = "LA DATE PREVUE D'ARRIVÉE DOIT ÊTRE SUPERIEURE A LA DATE DE DEPART !!!!!<br>", champ_vide_voyage = false;
	var nbrConteneurVide = 0, ville_depart = $('#ville_depart').val(), ville_arrivee = $('#ville_arrivee').val();
	
	for (i = 0; i < nbrInputVoyage; i++)
	{
		if ($('.datepicker').eq(0).val() >= $('.datepicker').eq(1).val() && $('.datepicker').eq(0).val() != '' && $('.datepicker').eq(1).val() != '')
			result_voyage = true;
		if($('.info_voyage').eq(i).val() == '')
		{
			$('.info_voyage').eq(i).addClass('input_vide');
			result_voyage = true;
			champ_vide_voyage = true;
		}
		else
			informations_voyage += '@@'+$('.info_voyage').eq(i).val();
	}
	if (result_voyage && champ_vide_voyage)
		message_destine_client_text = 'MERCI DE REMPLIRE TOUS LES CHAMPS DU VOYAGE !!!!!<br>';
	if(!result_voyage && ville_depart == ville_arrivee)
	{
		result_voyage = true;
		message_destine_client_text = 'LA VILLE DU DEPART NE PEUT PAS ETRE VILLE ARRIVÉE !!!!!';
	}
	if (result_voyage)
		$('#message_destine_client').html('<span>'+message_destine_client_text+'</span>').fadeIn(2000);
	else
	{
		var numeros_conteneurs = "", dimensions_conteneurs = "", statuts_conteneurs = "", indexLigne = 0;
		for (i = 0; i < nbrConteneur; i++)
		{
			// detail des conteneurs
			if($('#ListeConteneursVoyage input').eq(i).val() == '')
			{
				$('#ListeConteneursVoyage input').eq(i).parent().parent().css('background-color','LightCoral');
				result_conteneur = true;
				nbrConteneurVide += 1;
			}
			else
			{
				indexLigne = $('#ListeConteneursVoyage input').eq(i).attr('id').replace("numero_conteneur","");
				if (numeros_conteneurs != "")
				{
					numeros_conteneurs += "@@";
					statuts_conteneurs += "@@";
					dimensions_conteneurs += "@@";
				}
				numeros_conteneurs += $('#numero_conteneur'+indexLigne).val();
				statuts_conteneurs += $('#statut_conteneur'+indexLigne).val();
				dimensions_conteneurs += $('#dimension_conteneur'+indexLigne).val();
			}
		}
		if (nbrConteneurVide == nbrConteneur)
			$('#message_destine_client').html("<span>LE VOYAGE DOIT CONTENIR AU MOINS UN CONTENEUR</span>").fadeIn(2000);
		else if (!result_conteneur ||(result_conteneur && confirm('Voulez vous continuez et annulez les lignes colorées ?')))
		{
			//Control des conteneurs saisis plusieurs fois dans le voyage
			
			result_dupp = dupplicationConteneur('ListeConteneursVoyage');
			if (result_dupp)
				$('#message_destine_client').html('<span>LA LISTE DES CONTENEURS CONTIENT DES DUPPLICATION, VOIR LES LIGNES QUI ONT LA MÊME COULEUR !!!!</span>').fadeIn('sleep');
			else
			$.post('Voyage/enregistrer_voyage.php',{numeros_conteneurs:numeros_conteneurs, statuts_conteneurs:statuts_conteneurs, dimensions_conteneurs:dimensions_conteneurs, informations_voyage:informations_voyage},function(data){
				var tabRetour = data.split('|||');
				if (tabRetour[1] == 1)
				{
					$('#message_destine_client').html('<span>LE INFORMATIONS DU VOYAGE SONT AJOUTÉS AVEC SUCCÈS !!!!</span>').fadeIn('sleep');
					$('#info_voyage').slideUp(1000).html('').load('Voyage/info_voyage.php').slideDown(2000);
				}
				else if(tabRetour[1] == 0)
					$('#message_destine_client').html('<span>LE VOYAGE EXISTE DÉJÀ <br> MERCI DE VERIFIER LES INFORMATIONS SAISIES !!!!</span>').fadeIn('sleep');
					else
						$('#message_destine_client').html('<span>'+data+'</span>').fadeIn('sleep');
			})
		}
	}

});

//===========================================================================================================================================================================
//===========================================================================================================================================================================
//==================================================       ENREGISTRER DES NOUVEAUX CONTENEURS D'UN VOYAGE      =============================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$('.Enregistrer_Nouveaux_Conteneurs_Voyage').live('click', function(){
	var tableId = this.name, id_voyage = tableId.replace('ListeConteneursVoyageDetail',''), nbrConteneur = $('#'+tableId+' input:text').length, result_conteneur = false, result_dupp = false;
	var numeros_conteneurs = "", dimensions_conteneurs = "", statuts_conteneurs = "", indexLigne = 0;
	for (i = 0; i < nbrConteneur; i++)
	{
		// detail des conteneurs
		if($('#'+tableId+' input:text').eq(i).val() == '')
		{
			$('#'+tableId+' input:text').eq(i).parent().parent().css('background-color','LightCoral');
			result_conteneur = true;			
		}
		else
		{
			indexLigne = $('#'+tableId+' input:text').eq(i).attr('name').replace("numero_conteneur","");
			if (numeros_conteneurs != "")
			{
				numeros_conteneurs += "@@";
				statuts_conteneurs += "@@";
				dimensions_conteneurs += "@@";
			}
			numeros_conteneurs += $('#'+tableId+' input:text').eq(i).val();
			statuts_conteneurs += $('#'+tableId+' select').eq(i*2).val();
			dimensions_conteneurs += $('#'+tableId+' select').eq(i*2+1).val();
		}
	}
	if (!result_conteneur ||(result_conteneur && confirm('Voulez vous continuez et annulez les lignes colorées ?')))
	{
		//Control des conteneurs saisis plusieurs fois dans le voyage
		
		result_dupp = dupplicationConteneur(tableId);
		if (result_dupp)
		{
			$("#dialog-message").html('LA LISTE DES CONTENEURS CONTIENT DES DUPPLICATION, VOIR LES LIGNES QUI ONT LA MÊME COULEUR !!!!');
			$("#dialog-message").dialog({
										  modal: true,
										  resizable: false,
										  buttons: {
										  Ok: function() {
												  $( this ).dialog( "close" );
												}
											  }
										});
		}
		$.post('Voyage/enregistrer_conteneur.php',{numeros_conteneurs:numeros_conteneurs, statuts_conteneurs:statuts_conteneurs, dimensions_conteneurs:dimensions_conteneurs, id_voyage:id_voyage},function(data){
			var tabRetour = data.split('@@'), expression = tabRetour[1];
			switch(expression)
			{
				case '0':
					$("#dialog-message").html('LE VOYAGE N\'EXISTE PAS');
					break;
				case '1':
					$("#dialog-message").html('LES CONTENEURS SONT AJOUTÉS AVEC SUCCÈS !!!!');
					$('#div_list_conteneurs_detail_voyage'+id_voyage).load('Voyage/list_conteneurs_detail_voyage.php', {id_voyage:id_voyage, ve:'E'});
					break;
				case '2':
					$("#dialog-message").html('LE CONTENEUR EST AJOUTÉ AVEC SUCCÈS !!!!');
					$('#div_list_conteneurs_detail_voyage'+id_voyage).load('Voyage/list_conteneurs_detail_voyage.php', {id_voyage:id_voyage, ve:'E'});
					break;
				default:
					$("#dialog-message").html(data);
			}
			$("#dialog-message").dialog({
										  modal: true,
										  resizable: false,
										  buttons: {
										  Ok: function() {
												  $( this ).dialog( "close" );
												}
											  }
										});
		})
	}
});


//===========================================================================================================================================================================
//===========================================================================================================================================================================
//==================================================       ANNULER DES NOUVEAUX CONTENEURS D'UN VOYAGE      =================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$('.Annuler_Nouveaux_Conteneurs_Voyage').live('click', function(){
	var tableId = this.name;
	if (confirm('Voulez vous supprimer le tableau des nouveaux conteneurs ?'))
		$('#'+tableId).remove();
		

});

//===========================================================================================================================================================================
//===========================================================================================================================================================================
//==========================================================       ENREGISTRER NOUVEL UTILISATEUR      ======================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$('.EnregistrerUtilisateur').live('click', function(){
	$('#message_error_saisi_utilisateur').html();
	var nom = $('#nomNouveauUtilisateur').val(), result_saisi = false, message_destine_client = 'MERCI DE REMPLIRE TOUS LES CHAMPS !!!';
	var prenom = $('#prenomNouveauUtilisateur').val();
	var login = $('#loginNouveauUtilisateur').val();
	var pwd = $('#pwdNouveauUtilisateur').val();
	var pwd1 = $('#pwd1NouveauUtilisateur').val();
	var profil = $('#profilNouveauUtilisateur').val, id_button =this.id, page_destination = '';
	if (nom == "")
	{
		result_saisi = true;
		$('#nomNouveauUtilisateur').addClass('input_vide');
	}
	if (prenom == "")
	{
		result_saisi = true;
		$('#prenomNouveauUtilisateur').addClass('input_vide');
	}
	if (login == "")
	{
		result_saisi = true;
		$('#loginNouveauUtilisateur').addClass('input_vide');
	}
	if (pwd == "")
	{
		result_saisi = true;
		$('#pwdNouveauUtilisateur').addClass('input_vide');
	}
	if (pwd1 == "")
	{
		result_saisi = true;
		$('#pwd1NouveauUtilisateur').addClass('input_vide');
	}
	if (!result_saisi && pwd != pwd1)
	{
		result_saisi = true;
		message_destine_client = 'LE PWD DOIT ETRE EGAL AVEC PWD1';
	}
	if (!result_saisi && pwd.length < 4)
	{
		result_saisi = true;
		message_destine_client = 'LE MOT DE PASSE DOIT CONTENIR AU MOINS "4" CARACTERES';
	}
	var message_client1 = 'VOTRE PROFIL A ÉTÉ MODIFIÉ AVEC SUCCÈS !!!';
	if(id_button == 'EnregistrerNouvelUtilisateur')
	{
		page_destination = 'Utilisateur/enregistrer_utilisateur.php';
		message_client1 = 'L\'UTILISATEUR EST AJOUTÉ AVEC SUCCÈS !!!';
	}
	else
		page_destination = 'Utilisateur/enregistrer_modification_utilisateur.php';
	if(!result_saisi)
		$.post(page_destination,{nom:nom, prenom:prenom, login:login, pwd:pwd, profil:profil},function(data){
			var tabRetour = data.split('@@');
			if (tabRetour[1] == 1)
			{
				$('#message_error_saisi_utilisateur').html('<span>'+message_client1+'</span>');
				if (id_button == 'EnregistrerNouvelUtilisateur')
					$('#contenu_info_utilisateur').load('Utilisateur/info_utilisateur.php');
				else
					$('#contenu_info_utilisateur').html('');
			}
			else if (tabRetour[1] == 0)
					$('#message_error_saisi_utilisateur').html('<span>LE LOGIN EXISTE DÉJÀ !!! MERCI DE CHOISIR UN AUTRE </span>');
				else
				{
					$("#dialog-message").html(data);
					$("#dialog-message").dialog({
												  modal: true,
												  resizable: false,
												  buttons: {
												  Ok: function() {
														  $( this ).dialog( "close" );
														}
													  }
												});
				}
		})
	else
		$('#message_error_saisi_utilisateur').html('<span>'+message_destine_client+'</span>');
	
});


//===========================================================================================================================================================================
//===========================================================================================================================================================================
//========================================================       GESTION DES UTILISATEURS      ==============================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

$('.gestion_utilisateur').live('click', function(){
	var id_user = this.alt, action = "", id_element = this.id, imageName = "";
	if($(this).hasClass('editer_utilisateur'))
		action = "editer";
	if($(this).hasClass('desactiver_utilisateur'))
		action = 2;
	if($(this).hasClass('activer_utilisateur'))
		action = 1;
	$.post('Utilisateur/action_utilisateur.php',{id_user:id_user, action:action},function(data){
			var tabRetour = data.split('@@');
			if (tabRetour[1] == 1)
			{
				$('#messageListeUtilisateurs').html('<span>L\'UTILISATEUR MIS A JOURS AVEC SUCCÈS !!!</span>');
				$('#listeUtilisateurs').load('Utilisateur/list_utilisateur.php');
				
			}
			else
			{
				$("#dialog-message").html(data);
				$("#dialog-message").dialog({
											  modal: true,
											  resizable: false,
											  buttons: {
											  Ok: function() {
													  $( this ).dialog( "close" );
													}
												  }
											});
			}
		})

});

	
});

$(document).ready(function(){

$('.datepicker').datepicker({dateFormat : "yy-mm-dd", 
							autoSize : true, 
							monthNames: [ "Janvier", "Février", "Mars", "April", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre" ],
							dayNames: [ "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi" ],
							dayNamesMin : [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ]
					});

function slidesUpDivs(){
	$('#DivConsultationNumPatient').slideUp('slow');
	$('#DivConsultationNvPatient').slideUp('slow');
	$('#DivConsultationPatient').slideUp('slow');
	$('#DivFactureAnalyse').slideUp('slow');
	$('#DivListRdVPatient').slideUp('slow');
	$('#DivFichePatient').slideUp('slow');
	$('#DivModifPatient').slideUp('slow');
	$('#DivListPatient').slideUp('slow');
	$('#DivRdVPatient').slideUp('slow');
	$('#DivNvPatient').slideUp('slow');
	$('#facture_rdv').slideUp('slow');
};
					
function AppFiltreFacture(){
	var numero_dossier = $('#filtre_facture_numero').val(), numero_facture = $('#filtre_facture_numero_facture').val(), assureur = $('#filtre_facture_assureur').val(), societe = $('#filtre_facture_societe').val();
	var date_facture = $('#filtre__facture_date_facture').val(), service = $('#filtre_facture_service').val(), docteur = $('#filtre_facture_docteur').val(), caissier = $('#filtre_facture_caissier').val(), etat = $('#filtre_facture_etat').val(), type = $('#filtre_facture_type').val();
	// alert (numero+' '+telephone+' '+nom+' '+assureur+' '+wilaya+' '+arrive+' '+visite);
	$.post('res_filtre_facture.php',{numero_dossier:numero_dossier, numero_facture:numero_facture, assureur:assureur, societe:societe, date_facture:date_facture, service:service, docteur:docteur, caissier:caissier, etat:etat, type:type},function(data){
		$('#DivListFactures').html(data);
	});

};

function AppFiltrePatient(){
		var numero = $('#filtre_numero').val(), telephone = $('#filtre_telephone').val(), nom = $('#filtre_nom').val(), assureur = $('#filtre_assureur').val();
		var wilaya = $('#filtre_wilaya').val(), arrive = $('#filtre_arrive').val(), visite = $('#filtre_der_visite').val();
		// alert (numero+' '+telephone+' '+nom+' '+assureur+' '+wilaya+' '+arrive+' '+visite);
		$.post('res_filtre_patient.php',{numero:numero, telephone:telephone, nom:nom, assureur:assureur, wilaya:wilaya, arrive:arrive, visite:visite},function(data){
			$('#table_patient').html(data);
		});
};

function AppFiltreRdV(){

	var num_dossier_patient_rdv = $('#filtre_num_dossier_patient_rdv').val(), num_ordre_rdv = $('#filtre_num_ordre_rdv').val(), telephone_rdv = $('#filtre_telephone_rdv').val(), nom_rdv = $('#filtre_nom_rdv').val(), etat_rdv = $('#filtre_etat_rdv').val();
	var op_date_rdv = $('#filtre_op_date_rdv').val(), date_rdv = $('#filtre_date_rdv').val();
	var service_rdv = $('#filtre_service_rdv').val(), docteur_rdv = $('#filtre_docteur_rdv').val();
	// alert (numero+' '+telephone+' '+nom+' '+assureur+' '+wilaya+' '+arrive+' '+visite);
	$.post('res_filtre_patient.php',{num_dossier_patient_rdv:num_dossier_patient_rdv, num_ordre_rdv:num_ordre_rdv, telephone_rdv:telephone_rdv, nom_rdv:nom_rdv, etat_rdv:etat_rdv, op_date_rdv:op_date_rdv, date_rdv:date_rdv, service_rdv:service_rdv, docteur_rdv:docteur_rdv},function(data){
		$('#table_list_rdv_patient').html(data);
	});
	

};


$('.filtre_patient').keyup(function(){
	AppFiltrePatient();
});

$('#valider_filtre').click(function(){
	AppFiltrePatient();
});

$('.filtre_patient').change(function(){
	AppFiltrePatient();
});

$('.filtre_facture').keyup(function(){
	AppFiltreFacture();
});

$('#valider_filtre_facture').click(function(){
	AppFiltreFacture();
});

$('.filtre_facture').change(function(){
	AppFiltreFacture();
});

$('.filtre_rdv_patient').keyup(function(){
	AppFiltreRdV();
});

$('#valider_filtre_rdv').click(function(){
	AppFiltreRdV();
});

$('.filtre_rdv_patient').change(function(){
	if(this.name == 'service_rdv')
	{
		var serviceId = $('select[name=service_rdv] option:selected').val();
		$.ajax({
			   url: "../Utilisateur/res_filtre_utilisateur.php",
			   async: false,
			   type: 'POST',
			   data: {serviceId:serviceId, tous:'non'},
			   success: function(retour){
							$('#filtre_docteur_rdv').html(retour);
						},
			   error: function(){
						alert("Erreur lors de la mise en archive de l'extrait n°"+id+".");
						}
			 });
	}
	AppFiltreRdV();
});



$('#NvPatient').click(function(){
	slidesUpDivs();
	$('#DivNvPatient').slideDown('slow');
	// ImpressionFacture('Mahi||1||Normale||10000||Cash||20||8000||2000');
});



$('.CompteurCars').live('keypress', function (){
 var nbrCarsSup = 0, nbrCarsRest =0;
 if (this.name == 'anamnese')
	nbrCarsSup = 1000;
else
	nbrCarsSup = 400;
	
 nbrCarsRest = nbrCarsSup - parseInt(this.value.length)-1;
 
 if (nbrCarsRest < 0)
 {
	alert ('Le champ est rempli');
	return false;
 }
 
// $('#nbrCar'+this.name+'Cons').text('Nbr Car restant = '+nbrCarsRest);

});



$('.CompteurCars').live('keyup', function (){
 // alert (this.name+'    '+this.value.length);
  var nbrCarsSup = 0, nbrCarsRest =0;
 if (this.name == 'anamnese')
	nbrCarsSup = 1000;
else
	nbrCarsSup = 400;
 nbrCarsRest = nbrCarsSup-parseInt(this.value.length);
 
	$('#nbrCar'+this.name+'Cons').text('Nbr Cars restant = '+nbrCarsRest);

});


$('.chiffre').live('keypress',function (event) {
// Compatibilité IE / Firefox
if(!event&&window.event) {
event=window.event;
}
// IE
// alert ('Code '+event.keyCode+' Which :'+event.which);
if((event.keyCode < 48 || event.keyCode > 57) && 
(event.keyCode != 7 && event.keyCode != 8 && event.keyCode != 9 && event.keyCode != 46 && event.keyCode != 37 && event.keyCode != 39 && event.keyCode != 36 && event.keyCode != 35)){
event.returnValue = false;
event.cancelBubble = true;
// alert('je suis dedans');
}
// DOM
if((event.which < 48 || event.which > 57)  && (event.which != 0 && event.which != 8)){
event.preventDefault();
event.stopPropagation();
// alert('je suis dans DOM');
}
});



// Section des actions sur patient

$('.payer').live('click', function(){
	var patientId = this.alt, idRdV = this.name, encaissement = 'oui';
	// alert (patientId+'        '+idRdV);
	$.post('res_filtre_patient.php',{patientAfactuer:patientId, encaissement:encaissement, idRendezVous:idRdV},function(retour){
				$('#factre_client_avant_rentree'+patientId).html(retour);
				// $(':radio[name=consultation_mode'+patientId+']').attr('name','mode_consultation'+patientId);
				// alert(retour);
			 });
	$('#factre_client_avant_rentree'+patientId).slideDown(1000);
	$('img[alt='+patientId+']').removeClass('payer').addClass('masquerPayer');
	// AppFiltreRdV();
});



$('.masquerPayer').live('click', function(){
	var patientId = this.alt;
	$('#factre_client_avant_rentree'+patientId).slideUp(1000);
	$('img[alt='+patientId+']').removeClass('masquerPayer').addClass('payer');
});


$('.masquerConsultation').live('click', function(){
	var consultationId = this.id;
	$('#ligne_'+consultationId).slideUp(1000);
	$('td[id='+consultationId+']').removeClass('masquerConsultation').addClass('afficherConsultation');
});



$('.afficherConsultation').live('click', function(){
	var consultationId = this.id;
	// alert(consultationId);
	$('#ligne_'+consultationId).slideDown(1000);
	$('td[id='+consultationId+']').removeClass('afficherConsultation').addClass('masquerConsultation');
});



$('.consultation').live('click', function(){
	var patientId = this.name;
	var envoyeur = '', diabetique = '', tabac = '', oh = '', allergies = '', facture = '';
	// alert (patientId);
	slidesUpDivs();	
	$('#DivConsultationPatient').slideDown('slow');
	$('#DivConsultationNumPatient').slideDown('slow');
	$('#NvClientNon').attr('checked',true);
	// alert($('input[name = "numero_patient"]').val());
	// alert('Avant post : envoyeur = '+envoyeur+' , diabetique = '+diabetique+' , tabac = '+tabac+' , oh = '+oh+' , allergies = '+allergies);
	$.post('res_filtre_patient.php',{numero_patient:patientId},function(retour){
			var tabRetour = retour.split('||');
			envoyeur = tabRetour[2], diabetique = tabRetour[3], tabac = tabRetour[4], oh = tabRetour[5], allergies = tabRetour[6], facture = tabRetour[7];
			var status_n = tabRetour[8], status_c = tabRetour[9], status_b = tabRetour[10], status_a = tabRetour[11], status_p = tabRetour[12], status_l = tabRetour[13], status_rcf = tabRetour[14], status_o = tabRetour[15], ordreRdV = tabRetour[16];
			var hta = tabRetour[17], autre = tabRetour[18];
			// alert('Dans post : envoyeur = '+envoyeur+' , diabetique = '+diabetique+' , tabac = '+tabac+' , oh = '+oh+' , allergies = '+allergies);
			if (parseInt(tabRetour[1]) == 0)
				$('input[name = "numero_rdv_patient"]').val('Aucun');
			else
				{
					$('input[name = "numero_rdv_patient"]').val(tabRetour[1]);
					$('input[name = "numero_ordre_rdv_patient"]').val(ordreRdV);
					$('img[name = "AnnulerPaiementConsultation"]').attr({'name':tabRetour[1]});
				}
			$('#nomPatient').html(tabRetour[0]);
			$('input[name = "paiementConsultationRdv"]').val(facture);
			$('input[name = "numero_patient"]').val(patientId);
			$('input[name = "diabetiqueConsultation"]').val(diabetique);
			$('input[name = "envoyeur_patient"]').val(envoyeur);
			$('input[name = "tabacConsultation"]').val(tabac);
			$('input[name = "ohConsultation"]').val(oh);
			$('textarea[name = "allergies"]').val(allergies);
			$('textarea[name = "status_n"]').val(status_n);
			$('textarea[name = "status_c"]').val(status_c);
			$('textarea[name = "status_b"]').val(status_b);
			$('textarea[name = "status_a"]').val(status_a);
			$('textarea[name = "status_p"]').val(status_p);
			$('textarea[name = "status_l"]').val(status_l);
			$('textarea[name = "status_rcf"]').val(status_rcf);
			$('textarea[name = "status_o"]').val(status_o);
			$('textarea[name = "hta"]').val(hta);
			$('textarea[name = "autre"]').val(autre);
		});
	$('img[alt=Historique]').attr({
			 'name' : 'afficher',
			 'src' : '../Img/afficher.jpg'
		});
	$('#Historque_patient').html('');
});



$('.modifier').live('click', function(){
	var patientIdMod = this.name;
	// alert (patientIdMod);
	slidesUpDivs();
	$('#DivModifPatient').slideDown('slow');
	$('#DivModifPatient').html('INFORMATION DU PATIENT À MODIFIER');
	$.ajax({type: "POST", url: 'res_filtre_patient.php', data : 'patientIdMod= '+patientIdMod,success: function(retour){
		$("#DivModifPatient").empty().append(retour);
		}
		});
});



$('.rendezVous').live('click', function(){
	var patientIdRdV = this.name;
	// alert (patientIdRdV);
	$('input[name=facture]').attr('checked', false);
	slidesUpDivs();
	$.post('res_filtre_patient.php',{patientIdRdV:patientIdRdV},function(retour){
			var tableau = retour.split('|');
			$('label[name=nomPatientRdV]').text(tableau[0]);
			$('label[name=telephonePatientRdV]').text(tableau[1]);
			var dateRdV = tableau[2], heurRdV = tableau[6];
			if (dateRdV.length < 3)
			{
				$('#MessageRdV').text('CHOISIR UNE DATE POUR LE RENDEZ VOUS');
				$('input[name = datePatientRdV]').val('');
				$('#facture_apres').attr('checked', true);
			}
			else
			{
				$('#MessageRdV').text('LE PATIENT À DÈJÀ UN RENDEZ VOUS LE : '+dateRdV);
				$('input[name = datePatientRdV]').val(dateRdV);
				$('input[name = heurPatientRdV]').val(heurRdV);
				$('textarea[name=remarquePatientRdV]').val(tableau[3]);
				$('#facture_'+tableau[4]).attr('checked', true);
			}
				
		});
	$('input[name=idPatientRdV]').val(patientIdRdV);
	$('textarea[name=remarquePatientRdV]').val('');
	$('input[name=datePatientRdV]').val();
	$('#DivRdVPatient').slideDown('slow');
	// $('#facture_'+type_facture).attr('checked', 'checked');
	
});



$('.facturerExamen').live('click', function(){
	var patientIdFactureExamen = this.name;
	// alert (patientIdFactureExamen);
	slidesUpDivs();
	$('#DivFactureAnalyse').slideDown('slow');
	$.post('../Facture/res_filtre_facture.php',{patientIdFactureExamen:patientIdFactureExamen},function(retour){
			var tableau = retour.split('||');
			// alert(retour);
			$('label[name=nomPatientExamen]').text(tableau[0]);
			$('input[name=idPatientExamen]').val(tableau[1]);
			$('input[name=pPatientExamen]').val(tableau[2]);
			var montantPatient = $('input[name=prixExamen]').val() - (parseInt(tableau[2])/100)*$('input[name=prixExamen]').val();
			var montantApayer = $('input[name=prixExamen]').val() - montantPatient;
			$('input[name=mrPatientExamen]').val(montantPatient);
			$('input[name=mpPatientExamen]').val(montantApayer);
		});
	
});


$('.go').live('click', function(){
	var patientIdGo = this.name;
	// alert (patientId);
	$.post('res_filtre_patient.php',{patientIdGo:patientIdGo},function(retour){
			AppFiltreRdV();
			alert (retour);
		});
	
});


$('.cancel').live('click', function(){
	var patientIdCancel = this.name;
	// alert ($('img[alt=cancel'+patientIdCancel+']').attr(name));
	$('img[alt=cancel'+patientIdCancel+']').parent().parent().parent().css('background-color','red');
	if (confirm("Voulez-vous annulez le rendez vous du patient ?"))
		{
		$.post('res_filtre_patient.php',{patientIdCancel:patientIdCancel},function(retour){
			AppFiltreRdV();
			alert (retour);
		});
		}
	else
		$('img[alt=cancel'+patientIdCancel+']').parent().parent().parent().css('background-color','#E9DEBE');
	
});


$('.annulerFactureRdv').live('click', function(){
	var patientIdCancel = this.name;
	if (this.alt == "ActiverFacture") //Annuler le paiement durant la consultation => n'a pas annuler le rdv
	{
		if (confirm("Voulez-vous activez la facture du patient ?"))
			{
			$.post('../Facture/res_filtre_facture.php',{patientIdActiverFacture:patientIdCancel},function(retour){
				alert (retour);
				$('img[alt=ActiverFacture][name='+patientIdCancel+']').attr({'src' : "../img/cancel.gif",
															  'alt' : "AnnulerFacture"
															  });
				$('input[name = "paiementConsultationRdv"]').val('Paiement effectué');
				$('label[name = "AnnulerPaiementConsultation"]').text('ANNULER PAIEMENT');
			});
			}
	
	}
	else if (this.alt == "AnnulerFacture") //Annuler le paiement durant la consultation => n'a pas annuler le rdv
	{
		if (confirm("Voulez-vous annulez la facture du patient ?"))
			{
			$.post('../Facture/res_filtre_facture.php',{patientIdCancelFacture:patientIdCancel},function(retour){
				alert (retour);
				$('img[alt=AnnulerFacture][name='+patientIdCancel+']').attr({'src' : "../img/activer1.jpg",
															  'alt' : "ActiverFacture"
															  });
				$('input[name = "paiementConsultationRdv"]').val('Paiement annulé');
				$('label[name = "AnnulerPaiementConsultation"]').text('RÉACTIVER PAIEMENT');
			});
			}
	
	}
	else // Annuler le paiement et le rendez vous
	{
		$('img[alt=AnnulerFactureRdV'+patientIdCancel+']').parent().parent().parent().css('background-color','red');
		if (confirm("Voulez-vous annulez la facture et le rendez vous du patient ?"))
			{
			$.post('res_filtre_facture.php',{patientIdCancel:patientIdCancel},function(retour){
				AppFiltreFacture();
				alert (retour);
			});
			}
		else
			$('img[alt=AnnulerFactureRdV'+patientIdCancel+']').parent().parent().parent().css('background-color','#E9DEBE');
	}
});


$('.absence').live('click', function(){
	var patientIdAbsence = this.name;
	// alert ($('img[alt=cancel'+patientIdCancel+']').attr(name));
	$('img[alt=absence'+patientIdAbsence+']').parent().parent().parent().css('background-color','red');
	if (confirm("Voulez vous mettre le rendez vous du patient manqué ?"))
		{
		$.post('res_filtre_patient.php',{patientIdAbsence:patientIdAbsence},function(retour){
			AppFiltreRdV();
			alert (retour);
		});
		}
	else
		$('img[alt=absence'+patientIdAbsence+']').parent().parent().parent().css('background-color','#E9DEBE');
	
});


$('.detail').live('click', function(){
	if (this.name == 'afficher')
		{
			var patient_consultant_historique = $('input[name=numero_patient]').val();
			if (patient_consultant_historique.length < 1)
				alert ('Saisir le numéro du dossier');
			else
			{
				$(this).attr({
								 'name' : 'cacher',
								 'src' : '../Img/cacher.jpg'
							});
				$.post('res_filtre_patient.php',{patient_consultant_historique:patient_consultant_historique},function(retour){
						$('#Historque_patient').html(retour).slideDown('slow');
					});
			}
		
		}
	else
		{
			$(this).attr({
						     'name' : 'afficher',
						     'src' : '../Img/afficher.jpg'
						});
			$('#Historque_patient').slideUp('slow').html('');
		}
	
});



// Fiche patient

$('.fiche').live('click', function(){

	var patientIdFiche = this.name;
	slidesUpDivs();
	$.post('fiche_patient.php',{patientIdFiche:patientIdFiche},function(retour){
						$('#DivFichePatient').html(retour).slideDown('slow');
					});

});


$('.activer_rdv').live('click', function(){
	var patientIdActiver = this.name;
	// alert ($('img[alt=cancel'+patientIdActiver+']').attr(name));
	$('img[alt=Activer'+patientIdActiver+']').parent().parent().parent().css('background-color','ForestGreen');
	if (confirm("Voulez-vous réactivez le rendez vous du patient ?"))
		{
		$.post('res_filtre_patient.php',{patientIdActiver:patientIdActiver},function(retour){
			AppFiltreRdV();
			alert (retour);
		});
		}
	else
		$('img[alt=Activer'+patientIdActiver+']').parent().parent().parent().css('background-color','#E9DEBE');
	
});
// Fin de la section actions des patients


$('#ListPatient').click(function(){
	slidesUpDivs();
	$('#DivListPatient').slideDown('slow');
	AppFiltrePatient();
});



$('#NvConsultation').click(function(){
	slidesUpDivs();
	$('#DivConsultationPatient').slideDown('slow');
	$('#DivConsultationNumPatient').slideDown('slow');
	$('#NvClientNon').attr('checked',true);
});



$('#ListRdVPatient').click(function(){
	slidesUpDivs();
	$('#DivListRdVPatient').slideDown('slow');
	AppFiltreRdV();
});


$('#EnregistrerRdV').click(function(){
	var dI = new Date();
	var datePatientRdV = $('input[name=datePatientRdV]').val(), Annee = dI.getFullYear(), Mois = parseInt(dI.getMonth())+1, Jour = dI.getDate();
	if (Mois < 10)
	{
		Mois = '0' + Mois;
	}
	if (Jour < 10)
	{
		Jour = '0' + Jour;
	}
	var dateJour = Annee+'-'+Mois+'-'+Jour;
	if (datePatientRdV =='' || datePatientRdV.length <10)
	{
		$('#MessageRdV').text('Merci de remplire le champ date RdV');
		alert('Merci de remplire le champ DATE RDV avec une date correcte'+dateJour);
	}
	else if(datePatientRdV < dateJour)
		alert('La date du rendez vous doit être après la date du jour '+dateJour);
	else
		{
			var idPatientRdV = $('input[name=idPatientRdV]').val(), heurPatientRdV = $('input[name=heurPatientRdV]').val(), remarquePatientRdV = $('textarea[name=remarquePatientRdV]').val(), maj = 'N';
			var serviceRdV = $('select[name=serviceRdV] option:selected').val(), docteurRdV = $('select[name=docteurRdV] option:selected').val(), factureRdV = $('input[name=facture]:checked').val();
			$.post('res_filtre_patient.php',{idPatientRdV:idPatientRdV, datePatientRdV:datePatientRdV, heurPatientRdV:heurPatientRdV, remarquePatientRdV:remarquePatientRdV, serviceRdV:serviceRdV, docteurRdV:docteurRdV, factureRdV:factureRdV, maj:maj},function(retour){
						if(retour.length == 10 || retour.length == 11)
							{
								if(confirm('Le client à déjà un RdV le '+retour+', Voulez vous le mettre à jour'))
									{
									 maj ='O';
									 $.post('res_filtre_patient.php',{idPatientRdV:idPatientRdV, datePatientRdV:datePatientRdV, heurPatientRdV:heurPatientRdV, remarquePatientRdV:remarquePatientRdV, serviceRdV:serviceRdV, docteurRdV:docteurRdV, factureRdV:factureRdV, maj:maj},function(retour){
										// $('#MessageRdV').text(retour);
										alert(retour);
									 });
									}
								else
									alert('Le nouveau RdV est abandonné');
							}
						else
							{
								var donnees = retour.split('||'), reponse = donnees[1], etatRdV = 'oui', patient = 0;
								if (reponse.length == 2 && factureRdV == 'oui')
									InsererPaiementFacturePatient(idPatientRdV, etatRdV, patient, "");
								
								alert(donnees[2]);
							}
					});
					
		}
	
});



function InsererPaiementFacturePatient(idPatientPayant, rdvEtat, rendezVousPatientId, nomPatient){
	var montPatient = $('input[name=total_facture'+idPatientPayant+']').val(), montAssureur = $('input[name=montant_restant'+idPatientPayant+']').val();
	var montTotal = parseInt(montPatient) + parseInt(montAssureur), modePaiement = $('select[name=mode_paiement'+idPatientPayant+'] option:selected').val();
	// if (rdvEtat == "apres")
		// mode_consultation = $('input[name=mode_consultation'+idPatientPayant+']:checked').val();
	// else
		mode_consultation = $('input[name=consultation_mode'+idPatientPayant+']:checked').val();
		
	$.post('facture_patient.php',{idPatientPayant:idPatientPayant, rendezVousPatientId:rendezVousPatientId, montPatient:montPatient, montAssureur:montAssureur, montTotal:montTotal, modePaiement:modePaiement, rdvEtat:rdvEtat, mode_consultation:mode_consultation},function(retour){
			// $('#MessageRdV').text(retour);
			if (retour.length > 3)
				alert(retour);
			else if ($('input[name=NbrCopieImpression'+idPatientPayant+']').val() > 0)
					ImpressionFacturePatient(idPatientPayant, nomPatient);
		 });

}


function InsererPaiementFacturePatientExamen(){
	var idPatientPayantExamen = $('input[name=idPatientExamen]').val(), idExamen = $('select[name=examenExamen] option:selected').val();
	var montPatient = $('input[name=mpPatientExamen]').val(), montAssureur = $('input[name=mrPatientExamen]').val();
	var montTotal = parseInt(montPatient) + parseInt(montAssureur), modePaiement = $('select[name=modePaiementExamen] option:selected').val();
	$.post('facture_patient.php',{idPatientPayantExamen:idPatientPayantExamen, idExamen:idExamen, montPatient:montPatient, montAssureur:montAssureur, montTotal:montTotal, modePaiement:modePaiement},function(retour){
			// $('#MessageRdV').text(retour);
			if (retour.length > 3)
			{
				if (confirm('Le patient à dèjà payé le même examen le : '+retour+', Voulez vous insérez de nouveau un autre paiement ?'))
				{
					$.post('facture_patient.php',{idPatientPayantExamen:idPatientPayantExamen, idExamen:idExamen, montPatient:montPatient, montAssureur:montAssureur, montTotal:montTotal, modePaiement:modePaiement, confirmation:'Oui'},function(retour){});
				}
			}
			else
			{
				if(parseInt($('input[name=mpPatientExamen]').val()) >0)
				{
					// ImpressionFacture(retour, factde);
				}
				alert('Le paiement fait avec succès');
				
			}
		 });

}



$('.imrimer_facture').live('click', function(){
	var idFactPatientImprimer = this.name, factde = this.alt;
	// alert(idFactPatientImprimer);
	$.post('res_filtre_facture.php',{idFactPatientImprimer:idFactPatientImprimer},function(retour){
			ImpressionFacture(retour, factde);
		 });

});

function EnteteFacture(nomPatientImpression)
{
	var dI = new Date();
	var Annee = dI.getFullYear(), Mois = dI.getMonth()+1, Jour = dI.getDate(), Heur = dI.getHours(), Min = dI.getMinutes(), Secs = dI.getSeconds();
	if (Mois < 10)
		Mois = '0'+Mois;
	if (Jour < 10)
		Jour = '0'+Jour;
	if (Heur < 10)
		Heur = '0'+Heur;
	if (Min < 10)
		Min = '0'+Min;
	if (Secs < 10)
		Secs = '0'+Secs;
		
	w=open("",'popup','width=600, height=600, toolbar=no, scrollbars=no, resizable=yes');	
	w.document.write("<HEAD>");
	w.document.write("<meta charset=\"UTF-8\">");
	w.document.write("<TITLE>PATIENT : "+nomPatientImpression+"</TITLE></HEAD>");
	w.document.write("<BODY style=\"margin:0 0 0 0; padding: 0 0 0 0; min-height : 100%;\">");
		w.document.write("<table width ='100%' height = \"25%\" border = '0'>");
		w.document.write("<tr><td width = '50%' height = '8%' align= 'left'><b>Dr. CHERIF AHMED</b></td><td rowspan='5' valign ='top' width ='15%'><img src ='../Img/logoOrl.jpg'/></td><td align= 'right'><b>الدكتور : اشريف احمد</b></td></tr>");
		w.document.write("<tr><td height = '8%' align= 'left'><b>Mohamed Lemine Cherif</b></td>		<td align= 'right'><b>محمد لمين اشريف</b></td></tr>");
		w.document.write("<tr><td height = '8%' align= 'left'>Spécialiste en <b>ORL</b> (Nez - Gorge - Oreilles)</td>		<td align= 'right'>(اخصائي (الأنف ـ الأذن ـ الحنجرة</td></tr>");
		w.document.write("<tr><td height = '8%' align= 'left'>Diplômé de l'université de Lausanne</td>		<td align= 'right'>خريج جامعة لوزان سويسرا</td></tr>");
		w.document.write("<tr><td colspan ='3' align = 'right'><hr/>Le  : "+Jour+'/'+Mois+'/'+Annee+"</td></tr></table>");
	
}

function PiedFacture()
{
	w.document.write("<style> .divPied {position:absolute; bottom:0px; right:5px; left:5px; border: 0px solid black; border-radius:25px; text-align:center; font-size:18px;}</style><div class ='divPied'>");
	w.document.write("<hr/><h5>هاتف: 31 31 25 45 222+ - جوال: 62 06 36 22 222+ - ص ب: 536 - تفرغ زينة ـ انواكشوط ـ موريتانيا<br>");
	w.document.write("Tél: 45 25 31 31 - Mob: 22 36 06 62 - BP: 536 - NOT 64B TVZ Nouakchott Mauritanie</h5></center>");
	w.document.write("</div>");
	w.document.write("</BODY>");
	w.document.close();
	w.print();
}

function ImpressionFacture(infoFacture, objet)
{
	var retourTabs = infoFacture.split('||');
	var namePatient = retourTabs[0], idPatient = retourTabs[1], mCPatient = retourTabs[2], mTFPatient = retourTabs[3], mPFPatient = retourTabs[4], pPatient = retourTabs[5], mRestantAssureur = retourTabs[6], mPPatient = retourTabs[7], nomExamen = retourTabs[8];
	EnteteFacture(namePatient);
	w.document.write("<br><center>REÇU DE PAIEMENT<br><h2>PATIENT : "+namePatient+"</h2><br><br>");
	w.document.write("<table border='1' width ='60%'>");
	if(objet == 'RdV')
	{
		w.document.write("<tr><td>MODE CONSULTATION </td><td>"+mCPatient+"</td></tr>");
		w.document.write("<tr><td>PRIX CONSULTATION  </td><td align= 'right'>"+mTFPatient+"</td></tr>");
	}
	else
	{
		w.document.write("<tr><td>EXAMEN </td><td>"+nomExamen+"</td></tr>");
		w.document.write("<tr><td>PRIX EXAMEN  </td><td align= 'right'>"+mTFPatient+"</td></tr>");
	}
		w.document.write("<tr><td>MODE PAIEMENT </td><td>"+mPFPatient+"</td></tr>");
		w.document.write("<tr><td>% PATIENT </td><td align= 'right'>"+pPatient+"%</td></tr>");
		w.document.write("<tr><td>MONTANT RESTANT </td><td align= 'right'>"+mRestantAssureur+"</td></tr>");
		w.document.write("<tr><td><b>MONTANT À PAYER </b></td><td align= 'right'><b>"+mPPatient+"</b></td></tr>");
	w.document.write("</table></center>");
		// w.document.write($('#facture_rdv').html());
	PiedFacture();
}



$('.imprimerOrdonnace').live('click', function(){
	var idPatientOrdImprimer = this.alt;
	// alert(idPatientOrdImprimer);
	$.post('fiche_patient.php',{idPatientOrdImprimer:idPatientOrdImprimer},function(retour){
			// $('#MessageRdV').text(retour);
			var retourTabs = retour.split('||');
			var namePatient = retourTabs[0], ordPatient = retourTabs[1], assurPatient = retourTabs[2], socPatient = retourTabs[3], cartePatient = retourTabs[4];
			ImpressionOrdonnacePatient(namePatient, ordPatient, assurPatient, socPatient, cartePatient);
		 });

});

function ImpressionOrdonnacePatient(nomPatient, ordonnancePatient)
{
	EnteteFacture(nomPatient);
	w.document.write("<center><h2>ORDONNANCE</h2></center><br>");
	w.document.write("<b>PATIENT : </b><i>"+nomPatient+"</i><br><br>");
		// w.document.write("<br><div style ='position:absolute; left:10px; right:10px'>"+ordonnancePatient+"</div><br>");
	var tabOrd = ordonnancePatient.split("\n");
	var nbrMed = tabOrd.length, nvOrd = "";
	for (var i = 1; i<= nbrMed; i++)
		nvOrd += i+"- "+tabOrd[i-1]+"<br>";
		w.document.write("<div style ='position:absolute; left:15px; right:10px'>"+nvOrd+"</div><br>");
		// w.document.write($('#facture_rdv').html());
	PiedFacture();
}

function ImpressionExamenPatient(nomPatient, examenPatient, remarque, renseignement)
{
	EnteteFacture(nomPatient);
	w.document.write("<center><h2>EXAMEN COMPLEMENTAIRE</h2></center><br>");
	w.document.write("<b>PATIENT : </b><i>"+nomPatient+"</i><br>");
		w.document.write("<br>"+examenPatient+"<br>");
		w.document.write("<br>"+remarque+"<br>");
		w.document.write("<br>"+renseignement+"<br>");
	PiedFacture();
}

function ImpressionFacturePatient(idPatientImpression, nomPatientImpression)
{
	EnteteFacture(nomPatientImpression);
			w.document.write("<center>REÇU DE PAIEMENT<br><h2>PATIENT : "+nomPatientImpression+"</h2><br>");
			w.document.write("<table border='1' width ='60%'>");
				w.document.write("<tr><td>MODE CONSULTATION :</td><td>"+$(":radio[name=consultation_mode"+idPatientImpression+"]:checked").val()+"</td></tr>");
				w.document.write("<tr><td>PRIX CONSULTATION : </td><td>"+$('input[name=montant'+idPatientImpression+']').val()+"</td></tr>");
				w.document.write("<tr><td>MODE PAIEMENT : </td><td>"+$('select[name=mode_paiement'+idPatientImpression+'] option:selected').text()+"</td></tr>");
				w.document.write("<tr><td>% PATIENT :</td><td>"+$('input[name=taux'+idPatientImpression+']').val()+"%</td></tr>");
				w.document.write("<tr><td>MONTANT RESTANT :</td><td>"+$('input[name=montant_restant'+idPatientImpression+']').val()+"</td></tr>");
				w.document.write("<tr><td><b>MONTANT À PAYER : </b></td><td><b>"+$('input[name=total_facture'+idPatientImpression+']').val()+"</b></td></tr>");
			w.document.write("</table></center>");
				// w.document.write($('#facture_rdv').html());
	PiedFacture();	
}


$('.encaissement').live('click', function (){
	if(this.id == "EnregistrerFactureExamen")
		InsererPaiementFacturePatientExamen();
	else
	{
		var nomId = this.name;
		var infPatient = nomId.split('||'), etatRdV = 'apres';
		InsererPaiementFacturePatient(infPatient[1], etatRdV, infPatient[2], infPatient[0]);
		AppFiltreRdV();
	}	// alert('Contenu Impression de : '+$('input[name=NbrCopieImpression'+infPatient[1]+']').val()+' copie(s)');
	
});



$(':radio[name=assure_nv_patient]').change(function(){
	// alert(this.value);
	if (this.value == 'Oui')
		$('#DivAssureur').slideDown('slow');
	else
		$('#DivAssureur').slideUp('slow');
	});

	
	
$(':radio[name=NvClient]').change(function(){
	// alert(this.value);
	if (this.value == 'oui')
		{
			$('#DivConsultationNvPatient').slideDown('slow');
			$('#DivConsultationNumPatient').slideUp('slow');
		}
	else
		{
			$('#DivConsultationNvPatient').slideUp('slow');
			$('#DivConsultationNumPatient').slideDown('slow');
		}
	});

	
	
$(':radio[name=assure_consultation]').change(function(){
	// alert(this.value);
	if (this.value == 'Oui')
		$('#DivAssureurConsultation').slideDown('slow');
	else
		$('#DivAssureurConsultation').slideUp('slow');
	});
	
	
	
$(':radio[name=assure_modification]').live('change', function(){
	// alert(this.value);
	if (this.value == 'Oui')
		$('#DivAssureurModification').slideDown('slow');
	else
		$('#DivAssureurModification').slideUp('slow');
	});
	

	
	
$(':radio[name=rdv]').change(function(){
	// alert(this.value);
	if (this.value == 'oui')
		$('#RdVPatient').slideDown('slow');
	else
		$('#RdVPatient').slideUp('slow');
	});
	
	
	
$(':radio[name=analyse]').change(function(){
	// alert(this.value);
	if (this.value == 'oui')
		$('#AnalysePatient').fadeIn(1000);
	else
		$('#AnalysePatient').fadeOut(1000);
	});	
	


function verifier_gratuiter_rdv(patientAveriffier){

var nvRdv = $('input[name=datePatientRdV]').val(), docteurRdv = $('select[name=docteurRdV] option:selected').val();
// alert(nvRdv+' : '+serviceRdv+' : '+docteurRdv);
if (nvRdv == '')
{
	alert('Merci de remplire le champs date rendez vous');
	$('#facture_apres').attr('checked', true);
}
else
{
	$.post('res_filtre_patient.php',{verRDVgratuit:patientAveriffier, nvRdv:nvRdv, docteurRdv:docteurRdv},function(retour){
		if (retour.length > 5)
		{
			$('#facture_apres').attr('checked', true);
			alert(retour);
		}
	 });
	$('#facture_rdv').slideUp(1000);
}

}	


$('input[name=datePatientRdV]').change(function(){
	// alert('Changement date rdv');
	if ($('input[name=facture]:checked').val() == 'non')
	{
		var patientAfactuer = $('input[name=idPatientRdV]').val();
		verifier_gratuiter_rdv(patientAfactuer);
	}
	// else
		// alert($('input[name=facture]:checked').val());
});


$('select[name=docteur]').live('change',function(){
	// alert('Changement date rdv');
	if ($('input[name=facture]:checked').val() == 'non')
	{
		var patientAfactuer = $('input[name=idPatientRdV]').val();
		verifier_gratuiter_rdv(patientAfactuer);
	}
	// else
		// alert($('input[name=facture]:checked').val());
});


$(':radio[name=facture]').change(function(){
	// alert(this.value);
	var patientAfactuer = $('input[name=idPatientRdV]').val();
	if (this.value == 'oui')
		{
			var docteur =$('select[name=docteurRdV] option:selected').val();
			$.post('res_filtre_patient.php',{patientAfactuer:patientAfactuer, docteur:docteur},function(retour){
				$('#facture_rdv').html(retour);
				// alert(retour);
			 });
			$('#facture_rdv').slideDown(1000);
		}
	else if(this.value == 'non')
		verifier_gratuiter_rdv(patientAfactuer);
	else
		$('#facture_rdv').slideUp(1000);
	});	
	
	
	
	
$(".consultation_mode").live('change',function(){
	var nomElt = this.name;
	var indice = nomElt.replace('consultation_mode','');
	var ModeConsultationApayer = this.value, taux = $('input[name=taux'+indice+']').val(), docteur = $('select[name=docteurRdV] option:selected').val();
	var modeMin = ModeConsultationApayer.toLowerCase();
	// alert (indice);
	// alert (ModeConsultationApayer);
	$.post('facture_patient.php',{ModeConsultationApayer:modeMin, taux:taux, docteur:docteur},function(retour){
			// alert(retour);
			var elem = retour.split('||');
			montant = elem[0];
			Montant_restant = elem[1];
			Total = elem[2];
			$('input[name=montant'+indice+']').val(montant);
			$('input[name=montant_restant'+indice+']').val(Montant_restant);
			$('input[name=total_facture'+indice+']').val(Total);
		});
	});
	
	
$(":radio[name^='mode_consultation']").live('change',function(){
	var nomElt = this.name;
	var indice = nomElt.replace('mode_consultation','');
	var ModeConsultationApayer = this.value, taux = $('input[name=taux'+indice+']').val();
	var modeMin = ModeConsultationApayer.toLowerCase();
	$.post('facture_patient.php',{ModeConsultationPatientApayer:modeMin, taux:taux, idPatient:indice},function(retour){
			// alert(retour);
			var elem = retour.split('||');
			montant = elem[0];
			Montant_restant = elem[1];
			Total = elem[2];
			$('input[name=montant'+indice+']').val(montant);
			$('input[name=montant_restant'+indice+']').val(Montant_restant);
			$('input[name=total_facture'+indice+']').val(Total);
		});
	});

	
// Enregistrer la consultation du patient dans la BDD
	
$('input[name=enregistrer_consultation]').live('click', function (){
	var patient_consultant = $('input[name=numero_patient]').val(), rdv_patient_consultation = $('input[name=numero_rdv_patient]').val(), motif = $('select[name=motif]').val(), motif1 = $('select[name=motif1]').val(), motif2 = $('select[name=motif2]').val(), exa = $('select[name=examenComplementairePourLaConsultation]').val();
	var envoyeur = $('input[name=envoyeur_patient]').val(), tabac = $('input[name=tabacConsultation]').val(), oh = $('input[name=ohConsultation]').val();
	var status_n = $('textarea[name=status_n]').val(), status_c = $('textarea[name=status_c]').val(), status_b = $('textarea[name=status_b]').val(), status_a = $('textarea[name=status_a]').val(), status_p = $('textarea[name=status_p]').val(), status_l = $('textarea[name=status_l]').val(), status_rcf = $('textarea[name=status_rcf]').val(), status_o = $('textarea[name=status_o]').val();
	var analys = $('select[name=analyseComplementairePourLaConsultation]').val(), taille = $('input[name=taille]').val(), poids = $('input[name=poids]').val();
	var allergies = $('textarea[name=allergies]').val(), anamnese = $('textarea[name=anamnese]').val(), remarque = $('textarea[name=remarqueCons]').val(), diagnostic = $('textarea[name=diagnostic]').val(), ordonnance = $('textarea[name=ordonnance]').val();
	var remarque_examen = '', renseignment_clinique = '', hta = $('textarea[name=hta]').val(), autre = $('textarea[name=autre]').val();
	 
	var analyses = '';
	if (analys == 1)
	{
		$("input[type='checkbox'].choixAnalysSuplementaire:checked").each(
		function() {
           analyses = analyses+', '+$(this).val();
		   });
	}
	var longueur = analyses.length;
	var analyse = (longueur > 1) ? analyses.substring(2, longueur) : '0';
	
	var examens = '';
		$("input[type='checkbox'].choixExamenComplementaire:checked").each(
		function() {
		   examens = examens+', '+$(this).val();
		   });
	var longueur = examens.length;
	var examen = (longueur > 1) ? examens.substring(2, longueur) : '0';
	if(parseInt(exa) > 0)
	{
		remarque_examen = $('input[name="remarque_examen"]').val();
		renseignment_clinique = $('input[name="renseignment_clinique"]').val();
	}
	
	var np =$('label[name =nomPatientPendantConsultation]').text(), examenPatient = $('select[name=examenComplementairePourLaConsultation] option:selected').text();
	var nvOrd = ordonnance;
	for (var i=0;i<15;i++)
	{ 
		nvOrd = nvOrd.replace(".", "<br>");
	}
	// alert (patient_consultant +'  '+motif+'  '+examen+'  '+analyse+'  '+taille+'  '+poids+'  '+remarque+'  '+diagnostic+'  '+ordonnance);
	$.post('res_filtre_patient1.php',{patient_consultant:patient_consultant, rdv_patient_consultation:rdv_patient_consultation, motif:motif, motif1:motif1, motif2:motif2, examen:examen, remarque_examen:remarque_examen, renseignment_clinique:renseignment_clinique, analyse:analyse, taille:taille, poids:poids, envoyeur:envoyeur, tabac:tabac, oh:oh, allergies:allergies, anamnese:anamnese, remarque:remarque, diagnostic:diagnostic, ordonnance:ordonnance, status_n:status_n, status_c:status_c, status_b:status_b, status_a:status_a, status_p:status_p, status_l:status_l, status_rcf:status_rcf, status_o:status_o, hta:hta, autre:autre},function(retour){
				// $('#facture_rdv').html(retour);
				if (retour.length < 5)
					if(confirm('La consultation est déjà enregistrée, voulez vous la mettre à jours '))
						$.post('res_filtre_patient1.php',{maj:'oui', patient_consultant:patient_consultant, rdv_patient_consultation:rdv_patient_consultation, motif:motif, motif1:motif1, motif2:motif2, examen:examen, remarque_examen:remarque_examen, renseignment_clinique:renseignment_clinique, analyse:analyse, taille:taille, poids:poids, envoyeur:envoyeur, tabac:tabac, oh:oh, allergies:allergies, anamnese:anamnese, remarque:remarque, diagnostic:diagnostic, ordonnance:ordonnance, status_n:status_n, status_c:status_c, status_b:status_b, status_a:status_a, status_p:status_p, status_l:status_l, status_rcf:status_rcf, status_o:status_o, hta:hta, autre:autre},function(retour){
								alert(retour);
							});
					else
						alert('La modification est abandonnée');
				else
					{
						alert(retour);
						if(parseInt(examen) > 0)
							ImpressionExamenPatient(np, examenPatient, remarque_examen, renseignment_clinique);
						if(ordonnance.length > 0)
							ImpressionOrdonnacePatient(np, nvOrd);
					}
		$('input[name=enregistrer_consultation]').attr('value','Enregistrer Modification');
		$('img[alt=AnnulerFacture]').removeClass('annulerFactureRdv');
		});
});




// Enregistrer nouveau patient

$('input[name=enregistrer_nv_patient]').live('click', function (){
	
	var nom_nv_patient = $('input[name=nom_nv_patient]').val(), prenom_nv_patient = $('input[name = prenom_nv_patient]').val(),	naissance_nv_patient = $('input[name = naissance_nv_patient]').val();
	var	arrivee_nv_patient =$('input[name = arrivee_nv_patient]').val(), telephone_nv_patient = $('input[name = telephone_nv_patient]').val(), telephone2_nv_patient = $('input[name = telephone2_nv_patient]').val();
	var	email_nv_patient =$('input[name = email_nv_patient]').val(), sexe_nv_patient =$('input[name = sexe_nv_patient]:checked').val(), age_nv_patient =$('input[name = age_nv_patient]:checked').val();
	var	diabetique_nv_patient =$('input[name = diabetique_nv_patient]:checked').val(), wilaya_nv_patient =$('select[name = wilaya_nv_patient] option:selected').val(), localite_nv_patient =$('input[name = localite_nv_patient]').val();
	var	assure_nv_patient =$('input[name = assure_nv_patient]:checked').val();
	var	remarque_nv_patient =$('textarea[name = remarque_nv_patient]').val();
	var val_submit = true;
	if (nom_nv_patient == '')
	{
		$('input[name=nom_nv_patient]').css('background-color','red');
		val_submit = false;
	}
	if (prenom_nv_patient == '')
	{
		$('input[name = prenom_nv_patient]').css('background-color','red');
		val_submit = false;
	}
		
	if(telephone_nv_patient == '' && telephone2_nv_patient == '')
	{
		$('input[name = telephone_nv_patient]').css('background-color','gold');
		$('input[name = telephone2_nv_patient]').css('background-color','gold');
		val_submit = false;
	}
	
	if (arrivee_nv_patient == '')
	{
		$('input[name = arrivee_nv_patient]').css('background-color','red');
		val_submit = false;
	}
	
	if (!val_submit)
		alert ('Remplire tous les champs obligatoire');
	else
	{
		var nom_des_variables_nv_patient = 'nom¤prenom¤date_naissance¤date_arrivee¤telephone¤telephone2¤email¤sexe¤age¤diabetique¤wilaya¤localite¤assure¤remarque';
		
		var val_des_variables_nv_patient = nom_nv_patient+'¤'+prenom_nv_patient+'¤'+naissance_nv_patient+'¤'+arrivee_nv_patient+'¤'+telephone_nv_patient+'¤'+telephone2_nv_patient+'¤'+email_nv_patient+'¤'+sexe_nv_patient+'¤'+age_nv_patient+'¤'+diabetique_nv_patient+'¤'+wilaya_nv_patient+'¤'+localite_nv_patient+'¤'+assure_nv_patient+'¤'+remarque_nv_patient;
		
		if (assure_nv_patient == 'Oui')
		{
			var carteAssurance_nv_patient =$('input[name = carteAssurance_nv_patient]').val(), cin_nv_patient =$('input[name = cin_nv_patient]').val(), assureur_nv_patient =$('select[name = assureur_nv_patient] option:selected').val(), societe_nv_patient =$('select[name = societe_nv_patient] option:selected').val();
			nom_des_variables_nv_patient +='¤assureur¤societe¤n_carte_assurance¤cin';
			val_des_variables_nv_patient +='¤'+assureur_nv_patient+'¤'+societe_nv_patient+'¤'+carteAssurance_nv_patient+'¤'+cin_nv_patient;
		}
		$.post('res_filtre_patient.php',{noms:nom_des_variables_nv_patient, vals:val_des_variables_nv_patient},function(retour){
				// $('#facture_rdv').html(retour);
				alert(retour);
		});
	}
});




// Enregister la modification sur un patient

$('input[name=enregistrer_modifier_patient]').live('click', function (){
	
	var nom_nv_patient = $('input[name=nom_modification]').val(), prenom_nv_patient = $('input[name = prenom_modification]').val(),	naissance_nv_patient = $('input[name = naissance_modification]').val();
	var	arrivee_nv_patient =$('input[name = arrivee_modification]').val(), telephone_nv_patient = $('input[name = telephone_modification]').val(), telephone2_nv_patient = $('input[name = telephone2_modification]').val();
	var	email_nv_patient =$('input[name = email_modification]').val(), sexe_nv_patient =$('input[name = sexe_modification]:checked').val(), age_nv_patient =$('input[name = age_modification]:checked').val();
	var	diabetique_nv_patient =$('input[name = diabetique_modification]:checked').val(), wilaya_nv_patient =$('select[name = wilaya_modification] option:selected').val(), localite_nv_patient =$('input[name = localite_modification]').val();
	var	assure_nv_patient =$('input[name = assure_modification]:checked').val(), id_mod = $('input[name = id_modification]').val();
	var	remarque_nv_patient =$('textarea[name = remarque_modification]').val();
	var val_submit = true, id_mod = $('input[name=id_modification]').val();
	if (nom_nv_patient == '')
	{
		$('input[name=nom_modification]').css('background-color','red');
		val_submit = false;
	}
	if (prenom_nv_patient == '')
	{
		$('input[name = prenom_modification]').css('background-color','red');
		val_submit = false;
	}
		
	if(telephone_nv_patient == '' && telephone2_nv_patient == '')
	{
		$('input[name = telephone_modification]').css('background-color','gold');
		$('input[name = telephone2_modification]').css('background-color','gold');
		val_submit = false;
	}
	
	if (arrivee_nv_patient == '')
	{
		$('input[name = arrivee_modification]').css('background-color','red');
		val_submit = false;
	}
	
	if (!val_submit)
		alert ('Remplire tous les champs obligatoire');
	else
	{
		var nom_des_variables_nv_patient = 'nom¤prenom¤date_naissance¤date_arrivee¤telephone¤telephone2¤email¤sexe¤age¤diabetique¤wilaya¤localite¤assure¤remarque¤assureur¤societe¤n_carte_assurance¤cin';
		var val_des_variables_nv_patient = nom_nv_patient+'¤'+prenom_nv_patient+'¤'+naissance_nv_patient+'¤'+arrivee_nv_patient+'¤'+telephone_nv_patient+'¤'+telephone2_nv_patient+'¤'+email_nv_patient+'¤'+sexe_nv_patient+'¤'+age_nv_patient+'¤'+diabetique_nv_patient+'¤'+wilaya_nv_patient+'¤'+localite_nv_patient+'¤'+assure_nv_patient+'¤'+remarque_nv_patient;
		var carteAssurance_nv_patient = '', cin_nv_patient = '', assureur_nv_patient = '', societe_nv_patient = '';
		
		if (assure_nv_patient == 'Oui')
			carteAssurance_nv_patient =$('input[name = carteAssurance_modification]').val(), cin_nv_patient =$('input[name = cin_modification]').val(), assureur_nv_patient =$('select[name = assureur_modification] option:selected').val(), societe_nv_patient =$('select[name=societe_modification] option:selected').val();
		val_des_variables_nv_patient +='¤'+assureur_nv_patient+'¤'+societe_nv_patient+'¤'+carteAssurance_nv_patient+'¤'+cin_nv_patient;
		$.post('res_filtre_patient.php',{id_mod:id_mod, noms_mod:nom_des_variables_nv_patient, vals:val_des_variables_nv_patient},function(retour){
				// $('#facture_rdv').html(retour);
				alert(retour);
		});
	}
});




$('input[name=annuler_nv_patient]').live('click', function(){
	if (confirm('Voulez vous annuler la création de ce patient ?'))
	$.post('info_patient.php',{},function(retour){
				// $('#facture_rdv').html(retour);
				$('#DivNvPatient').html(retour);
		});
});


$('select[name=assureur_modification]').live('change', function(){
	var assureurId = $('select[name=assureur_modification] option:selected').val();
	$.post('res_patient_modification.php',{assureurId:assureurId},function(retour){
				$('select[name=societe_modification]').html(retour);
		});
});

$('select[name=assureur_nv_patient]').live('change', function(){
	var assureurId = $('select[name=assureur_nv_patient] option:selected').val();
	$.post('res_patient_modification.php',{assureurId:assureurId},function(retour){
				$('select[name=societe_nv_patient]').html(retour);
		});
});



// $('select[name=profil]').live('change', function(){
	// var serviceId = $('select[name=service] option:selected').val(), profilId=$('select[name=profil] option:selected').val();
	// alert(serviceId+'    '+profilId);
	// if (profilId == 3)
	// $.post('res_filtre_utilisateur.php',{serviceId:serviceId},function(retour){
				// $('select[name=docteur]').html(retour);
				// $('#docteur_secretaire').slideDown('slow');
		// });
	// else
		// $('#docteur_secretaire').slideUp('slow');
		
// });



$('#filtre_facture_assureur').live('change', function(){
	var assureurId = $('#filtre_facture_assureur option:selected').val();
	// alert('Assureur Id est : '+assureurId);
	$.post('res_filtre_facture.php',{assureurId:assureurId},function(retour){
				$('#filtre_facture_societe').html(retour);
		});
});


$('#filtre_facture_service').live('change', function(){
	var serviceId = $('#filtre_facture_service option:selected').val();
	// alert('Service Id est : '+serviceId);
	$.post('res_filtre_facture.php',{serviceId:serviceId},function(retour){
				$('#filtre_facture_docteur').html(retour);
		});
		
});

$('select[name=service]').live('change', function(){
	var serviceId = $('select[name=service] option:selected').val(), profilId=$('select[name=profil] option:selected').val();
	// alert(serviceId+'    '+profilId);
	// if (profilId == 3)
	$.post('res_filtre_utilisateur.php',{serviceId:serviceId},function(retour){
				$('select[name=docteur]').html(retour);
				// $('#docteur_secretaire').slideDown('slow');
		});
	// else
		// $('#docteur_secretaire').slideUp('slow');
		
});

$('select[name=serviceRdV]').live('change', function(){
	var serviceId = $('select[name=serviceRdV] option:selected').val();
	// alert(serviceId+'    '+profilId);
	// if (profilId == 3)
	$.post('../Utilisateur/res_filtre_utilisateur.php',{serviceId:serviceId, tous:'non'},function(retour){
				$('select[name=docteurRdV]').html(retour);
				// $('#docteur_secretaire').slideDown('slow');
		});
	// else
		// $('#docteur_secretaire').slideUp('slow');
		
});

$('select[name=examenComplementairePourLaConsultation]').live('change', function(){
	var examenId = $('select[name=examenComplementairePourLaConsultation] option:selected').val();
	// alert('examenId    : '+examenId);
	if (examenId == 0)
		$('.detailExamenComplementaire').slideUp('slow');
	else
		$('.detailExamenComplementaire').slideDown('slow');
});


$('select[name=analyseComplementairePourLaConsultation]').live('change', function(){
	var analyseId = $('select[name=analyseComplementairePourLaConsultation] option:selected').val();
	alert('analyseId    : '+analyseId);
	if (analyseId == 0)
		$('.AnalyseComplementairePendantLaConsultation').slideUp('slow');
	else
		$('.AnalyseComplementairePendantLaConsultation').slideDown('slow');
});

$('select[name=examenExamen]').live('change', function(){
	var idExamen = $('select[name=examenExamen] option:selected').val();
	// alert(serviceId+'    '+profilId);
	$.post('../Facture/res_filtre_facture.php',{prixExamen:idExamen},function(retour){
			var  longueur = retour.length;
			// var ret =retour.substring(1, longueur);
			var tabs =retour.split("||");
			var ret = tabs[1];
			$('input[name=prixExamen]').val(ret);
			// var montantPatient = $('input[name=prixExamen]').val() - (parseInt(tableau[2])/100)*$('input[name=prixExamen]').val();
			var montantPatient = $('input[name=prixExamen]').val() - ($('input[name=pPatientExamen]').val()/100)*$('input[name=prixExamen]').val();
			var montantApayer = $('input[name=prixExamen]').val() - montantPatient;
			$('input[name=mrPatientExamen]').val(montantPatient);
			$('input[name=mpPatientExamen]').val(montantApayer);	
		});
	
});


$('input[name=annuler_modifier_patient]').live('click', function(){
	if (confirm('Voulez vous annuler la modification de ce patient ?'))
	$.post('info_patient.php',{},function(retour){
				$('#DivNvPatient').html(retour);
		});
});




$('input[name=annuler_consultation]').live('click', function(){
	slidesUpDivs();
	$('#DivListRdVPatient').slideDown('slow');

});

});



$(document).ready(function(){
// alert('Mahi');
// $('input').fadeOut(5000);
// $('input').fadeIn(5000);
$('.datepicker').datepicker({dateFormat : "yy-mm-dd",
 //                           isRTL :true,
                            autoSize : true,
                            monthNames: [ "Janvier", "Février", "Mars", "vpril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre" ],
                            monthNamesShort: [ "Jan", "Fev", "Mar", "Avr", "Mai", "Jun", "Jul", "Aoû", "Sep", "Oct", "Nov", "Déc" ],
                            dayNames: [ "Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi" ],
                            dayNamesMin : [ "Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa" ]
                            });

function KeyVide(event) {
// Compatibilité IE / Firefox
if(!event&&window.event) {
event=window.event;
}
// IE
 alert ('keypress ');
if(event.keyCode) {
event.returnValue = false;
event.cancelBubble = true;
}
// DOM
if(event.which){
event.preventDefault();
event.stopPropagation();
}
}


$('.detailFacture').click(function(){
 var ordre = this.alt, nomImage = this.name, source = this.src;
 // alert (ordre +' '+ nomImage+' '+ source);
 
 if (ordre == 'afficher')
  {
	$('#'+nomImage).show();
	// alert (
	$('.detailFacture[name='+nomImage+']').attr({
						     'alt' : 'cacher',
						     'src' : '../Img/cacher.jpg'
						});
	$('.detailFacture[name='+nomImage+']').parent().parent().css('background-color','LightCoral');
	$.post('detail_facture.php',{idFacture:nomImage},function(data){
		$('#Div'+nomImage).html(data);
		 });
  }
 else
  {
   $('#'+nomImage).hide();
   $('.detailFacture[name='+nomImage+']').attr({
												'alt' : 'afficher',
												'src' : '../Img/afficher.jpg'
												});
   $('.detailFacture[name='+nomImage+']').parent().parent().css('background-color','white');
  }

});


$('.supprimer').click(function(){
	var indice = this.alt.replace('Supprimer','');
	// alert(indice);
	if ($('#ArticleFactures tr').length>2)
		{
			$('.supprimer[alt=Supprimer'+indice+']').parent().parent().css('background-color','red');
			if (confirm("Voulez-vous supprimer la ligne ?"))
				{				  
                                  if ($('.supprimer[alt=Supprimer'+indice+']').attr('name') == 'Facture'+indice)
                                      {                                         
                                          $('.supprimer[alt=Supprimer'+indice+']').parent().parent().parent().remove();
                                          var totalLigne = document.getElementsByTagName('label'), totalFacture = 0,reg=new RegExp("( )", "g");
                                            for (i=0; i<totalLigne.length; i++)
                                                {
						totalFacture += parseInt(totalLigne[i].innerHTML.replace(reg,''));
                                                }
					// alert('Total Facture : '+nombreEnMillier(totalFacture));
                                            document.getElementById('TotalFacClient').innerHTML = nombreEnMillier(totalFacture)+' UM';
                                      }
                                  else
                                    $('.supprimer[alt=Supprimer'+indice+']').parent().parent().parent().remove();
				}
			else
				$('.supprimer[alt=Supprimer'+indice+']').parent().parent().css('background-color','white');
		}
	else
		alert ('La facture ne contient qu\'une seul ligne et ne peut pas être supprimée');
});

$('#Enregistrer_Produit_Facture').click(function(){

if ($('#TotalFacClient').text() == "0 UM")
	alert('La facture ne peut pas être 0 UM ');
else
	{
	 var nbrTotal=$('label').length, result = true;
	 // alert ($('label').eq(0).text());
	 
	 for (i=0; i<nbrTotal; i++)
		{
		 if($('label').eq(i).text()=='0')
			{
			 // alert ($('label').eq(i).attr('name')+' EST VIDE !!!');
			 $('label').eq(i).parent().css('background-color','LightCoral');
			 result = false;			
		// totalFacture += parseInt(totalLigne[i].innerHTML.replace(reg,''));
	       }
	    }
	if (result ||(!result && confirm('Voulez vous continuez et annulez les lignes colorées ?')))
	  {
	    var reg=new RegExp("( )", "g"), total_facture = parseInt($('#TotalFacClient').text().replace(reg,''));
		var reference = $('input[name=reference]').val(), client = $('select[name=client]').val(), mode_paiement = $('select[name=mode_paiement]').val();
		
		// alert('Ref : '+reference+' total_facture : '+total_facture+' client : '+client+' mode_paiement : '+mode_paiement);
		
		// Enregistrement de la facture
		var totalsFacture = '', nbrLigne = 0, categories = '', produits = '', prixs = '', quantites = '';
                nbrTotal=$('label').length;
		for (i=0; i<nbrTotal; i++)
		{
		 if($('label').eq(i).text()!='0')
			{
			 $('label').eq(i).parent().css('background-color','Tan');
			 categories += $('.categorie select').eq(i).val()+'|';
			 produits += $('.produit select').eq(i).val()+'|';
			 prixs += $('.prix input').eq(i).val()+'|';
			 quantites += $('.quantite input').eq(i).val()+'|';
			 totalsFacture += parseInt($('label').eq(i).text().replace(reg,''))+'|';
			 // indices += '|'+$('label').eq(i).attr('name').replace('total','');
			 nbrLigne += 1;
	       }
	    }
		alert ('categories : '+categories+' produits : '+produits+' prixs : '+prixs+' quantites : '+quantites+' totalsFacture : '+totalsFacture+' nbrLigne : '+nbrLigne);
		$.post('enregistrer_facture.php',{reference:reference, total_facture:total_facture, client:client, mode_paiement:mode_paiement, totalsFacture:totalsFacture, categories:categories, produits:produits, quantites:quantites, prixs:prixs, nbrLigne:nbrLigne},function(data){
		alert(data);
		 })
		// $('form').submit();
		
	  }
    }
});

$('#Ajouter_Produit_Facture').click(function(){
	$('#ArticleFactures').append($('tr :last').clone(true));
	// alert('Anc nom '+$('tr :last select :first').attr('name'));
	var anc_cat=$('tr :last select :first').attr('name');
	var anc_prod=anc_cat.replace("categorie","produit");
	var anc_prix=anc_cat.replace("categorie","prix");
	var anc_qtt=anc_cat.replace("categorie","quantite");
	var anc_total=anc_cat.replace("categorie","total");
	var indice=parseInt(anc_cat.replace("categorie",""))+1;
	$('tr :last select :first').attr({'name' : 'categorie'+indice,
									'id' : 'categorie'+indice});
	$('tr :last #'+anc_prod).attr({'name':'produit'+indice,
								'id':'produit'+indice
								});
	$('tr :last #'+anc_prod+'_'+anc_prix).attr({'name':'produit'+indice+'_'+'prix'+indice,
								'id':'produit'+indice+'_'+'prix'+indice
								});
	$('tr :last #'+anc_prix).attr({'name':'prix'+indice,
								'id':'prix'+indice,
								'value' : '0'
								});
	$('tr :last #'+anc_qtt).attr({'name':'quantite'+indice,
								'id':'quantite'+indice,
								'value' : '0'
								});
	$('tr :last #'+anc_total).attr({'name':'total'+indice,
								'id':'total'+indice
								});
	$('tr :last .supprimer').attr({'alt':'Supprimer'+indice,
                                       'name':'Facture'+indice
								});
	document.getElementById('total'+indice).innerHTML = '0';						
	// $('tr :last select :second').attr('id','produit'+indice);
	// alert('NV nom '+$('tr :last select :first').attr('name'));
	// $.post('../Js/AjaxQueryLigne.php',{},function(data){
	// $('#ArticleFactures').append(data);
	// });
});
	

$('#Enregistrer_Produit_Affecter').click(function(){

    var nbrTotal=$('input[name^=quantite]').length, result = true, reference = $('input[name=reference]').val(), nbrLigne = 0, categories = '', produits = '', quantites = '', entiteDu = $('#du').val(), entiteAu = $('#au').val();
    alert (entiteDu+'       '+entiteAu);
    
    if (entiteDu == entiteAu)
        alert('L\'affectation ne peut pas se faire sur la même entité');
    else
        {
            for (i=0; i<nbrTotal; i++)
               {
               if($('input[name^=quantite]').eq(i).val()=='0')
                   {
                   alert ($('input[name^=quantite]').eq(i).attr('name')+' EST VIDE !!!');
                    $('input[name^=quantite]').eq(i).parent().parent().css('background-color','LightCoral');
                    result = false;			
                   // totalFacture += parseInt(totalLigne[i].innerHTML.replace(reg,''));
                   }
               }
            if (result ||(!result && confirm('Voulez vous continuez et annulez les lignes colorées ?')))
             {
               // alert('Ref : '+reference+' total_facture : '+total_facture+' client : '+client+' mode_paiement : '+mode_paiement);
               // Enregistrement de la facture

               for (i=0; i<nbrTotal; i++)
               {
                if($('input[name^=quantite]').eq(i).val()!='0')
                   {
                       $('input[name^=quantite]').eq(i).parent().parent().css('background-color','Tan');
                       categories += $('.categorie select').eq(i).val()+'|';
                       produits += $('.produit select').eq(i).val()+'|';
                       quantites += $('.quantite input').eq(i).val()+'|';
                       nbrLigne += 1;
                   }
               }
               //alert ('categories : '+categories+' produits : '+produits+' quantites : '+quantites+' nbrLigne : '+nbrLigne);
               $.post('enregistrer_.php',{EntiteDu:EntiteDu, EntiteAu:EntiteAu, reference:reference, categories:categories, produits:produits, quantites:quantites, nbrLigne:nbrLigne},function(data){
               alert(data);
                })
               // $('form').submit();
             }
        }
});

$('#Ajouter_Produit_Affecter').click(function(){
	$('#ArticleFactures').append($('tr :last').clone(true));
	// alert('Anc nom '+$('tr :last select :first').attr('name'));
	var anc_cat=$('tr :last select :first').attr('name');
	var anc_prod=anc_cat.replace("categorie","produit");
	var anc_qtt=anc_cat.replace("categorie","quantite");
        var anc_prix=anc_cat.replace("categorie","prix");
	var indice=parseInt(anc_cat.replace("categorie",""))+1;
	$('tr :last select :first').attr({'name' : 'categorie'+indice,
					'id' : 'categorie'+indice
                                        });
	$('tr :last #'+anc_prod).attr({'name':'produit'+indice,
					'id':'produit'+indice
                                    });
	$('tr :last #'+anc_qtt).attr({'name':'quantite'+indice,
                                    'id':'quantite'+indice,
                                    'value' : '0'
                                    });
        $('tr :last #'+anc_prod+'_'+anc_prix).attr({'name':'produit'+indice+'_'+'prix'+indice,
								'id':'produit'+indice+'_'+'prix'+indice
								});
	$('tr :last .supprimer').attr({'alt':'Supprimer'+indice,
                                       'name':'Afecter'+indice
                                        });
	
});

});

function nombreEnMillier(nbr)
{
		var nombre = ''+nbr;
		var retour = '';
		var count=0;
		for(var i=nombre.length-1 ; i>=0 ; i--)
		{
			if(count!=0 && count % 3 == 0)
				retour = nombre[i]+' '+retour ;
			else
				retour = nombre[i]+retour ;
			count++;
		}
		return retour;
}