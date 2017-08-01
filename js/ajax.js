function alertDialog(message, cls) {
    cls = typeof cls !== 'undefined' ? cls : 0;
    if (cls)
        message = "<center class='alert-box " + cls + "'>" + message + "</center>";
    message = typeof message !== 'undefined' ? message : 0;
    if (message)
        $("#dialog-message").html(message);
    $("#dialog-message").dialog({
        modal: true,
        resizable: true,
        width: "400",
        height: "250",
        draggable: false,
        buttons: {
            Ok: function () {
                $("#dialog-message").html("");
                $(this).dialog("close");
            }
        }
    });
}
var idGroup = 0, retour_enr_cmp = '';
var parms_gle = new Object();

var tm_out_rec, tm_out_mgr, tm_out_vou, tm_out_sms, tm_out_data, tm_out_ftp;
$(document).ready(function () {
    lgSMS = "Fr";
    $("#username").focus();
//===========================================================================================================================================================================
//===========================================================================================================================================================================
//==================================================================       DATE PICKER      ==========================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

    $(document.body).on("focus", '.datetimepicker', function () {
        $(this).datetimepicker({
            dateFormat: "yy-mm-dd",
            minDate: 0,
            timeFormat: "HH:mm",
            showSecond: false,
            currentText: "Maintenant",
            closeText: "Valider",
            timeText: "Horaire",
            hourText: "Heur",
            minuteText: "Minute",
            prevText: "Précédent",
            nextText: "Suivant",
            monthNames: ["Janvier", "Février", "Mars", "April", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
            dayNames: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
            dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"]
        })
    });
    $(document.body).on("focus", '.datepicker', function () {
        $(this).datepicker({dateFormat: "yy-mm-dd",
            autoSize: true,
            defaultDate: 0,
            changeMonth: true,
            changeYear: true,
            yearRange: "2000:2017",
            firstDay: 1,
            maxDate: 0,
            currentText: "Maintenant",
            nextText: "Suivant",
            prevText: "Précédent",
            monthNames: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
            monthNamesShort: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jun", "Jul", "Aou", "Sep", "Oct", "Nov", "Dec"],
            dayNames: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
            dayNamesMin: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"]
        })

    });
//===========================================================================================================================================================================
//===========================================================================================================================================================================
//==================================================================       CLASSE DE CHIFFRES      ==========================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================

    $(document.body).on("change keypress", '.champVide', function (event) {
        $(this).removeClass("champVide");
        var id = $(this).attr("id");
        if ($("#span" + id).length)
            $("#span" + id).remove();
    });
    $(document.body).on("keypress", '.chiffre', function (event) {
// Compatibilité IE / Firefox
        if (!event && window.event) {
            event = window.event;
        }
// IE
        if ((event.keyCode < 48 || event.keyCode > 57) &&
                (event.keyCode != 7 && event.keyCode != 8 && event.keyCode != 9 && event.keyCode != 46 && event.keyCode != 37 && event.keyCode != 39 && event.keyCode != 36 && event.keyCode != 35)) {
            event.returnValue = false;
            event.cancelBubble = true;
        }
// DOM
        if ((event.which < 48 || event.which > 57) && (event.which != 0 && event.which != 8)) {
            event.preventDefault();
            event.stopPropagation();
        }
    });

    //$('.chiffrePoint').live('keypress', function(event){
    $(document.body).on("keypress", '.chiffrePoint', function (event) {
        // Compatibilité IE / Firefox
        if (!event && window.event) {
            event = window.event;
        }
        // IE
        // alertDialog ('Code '+event.keyCode+' Which :'+event.which);
        if ((event.keyCode < 48 || event.keyCode > 57) &&
                (event.keyCode != 7 && event.keyCode != 8 && event.keyCode != 9 && event.keyCode != 46 && event.keyCode != 37 && event.keyCode != 39 && event.keyCode != 36 && event.keyCode != 35 && event.keyCode != 46)) {
            event.returnValue = false;
            event.cancelBubble = true;
            // alertDialog('je suis dedans');
        }
        // DOM
        if ((event.which < 48 || event.which > 57) && (event.which != 0 && event.which != 8 && event.which != 46)) {
            event.preventDefault();
            event.stopPropagation();
            // alertDialog('je suis dans DOM');
        }
    });
//===========================================================================================================================================================================
//===========================================================================================================================================================================
//==================================================================       CHARGEMENT DES TYPES DONNEES      ================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================
    $(document.body).on("change", '#nature_kpi', function () {
        var idNature = $(this).val();
        $('#nature_kpi option[value=""]').remove();
        $.ajax({
            url: 'campagne/kpi_td.php',
            async: false,
            type: 'POST',
            data: {idNature: idNature},
            success: function (retour) {
                $("#type_kpi").html(retour);
                $("#unite_kpi").html('');
            },
            error: function () {

                alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
            }
        })
    });

    $(document.body).on("change", '#type_kpi', function () {
        var idTd = $(this).val();
        $('#type_kpi option[value=""]').remove();
        $.ajax({
            url: 'campagne/kpi_unite.php',
            async: false,
            type: 'POST',
            data: {idTd: idTd},
            success: function (retour) {
                $("#unite_kpi").html(retour);
            },
            error: function () {

                alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
            }
        })
    });



    $(document.body).on("click", '#idAffichageKPI', function () {
        var idCmp = $('#idCmpHidden').val(), idTd = $('#type_kpi').val(), unite = $('#unite_kpi').val(), send = true;
        if (idCmp == '') {
            send = false;
        }
        if (idTd == '') {
            $('#type_kpi').addClass('ChampVide');
            send = false;
        }
        if (unite == '') {
            $('#unite_kpi').addClass('ChampVide');
            send = false;
        }
        if (send)
            $.ajax({
                url: 'campagne/kpi_details.php',
                async: false,
                type: 'POST',
                data: {idCmp: idCmp, idTd: idTd, unite: unite},
                success: function (retour) {
                    $("#divStatsGlobal").html(retour);
                },
                error: function () {

                    alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
                }
            })
    });

    $(document.body).on("change", '#nature_stats_client', function () {
        var valeur = $(this).val(), options = '', first = '', first2 = '';
        $.each(jsn_type[valeur], function (index, value) {
            if (first == '')
                first = value;
            options += "<option value = '" + value + "'>" + index + "</option>";
        });
        $("#type_donnee_stats_client").html(options);
        options = '';
        $.each(jsn_compteur[first], function (index, value) {
            if (value == 'Total' || value == 'Consommation_total' || value == 'Data_attribut_profil_all' || value.substring(0, 3) == 'All')
                options += "<option value = '" + value + "' selected>" + index + "</option>";
            else
                options += "<option value = '" + value + "'>" + index + "</option>";
        });
        $("#compteurs_stats_client").html(options);
        $('#compteurs_stats_client').multipleSelect();
        options = '';
        var sel;
        $.each(jsn_unite[first], function (index, value) {
            sel = (index == 60 || index == 100 || index == 1048576) ? ' selected ' : '';
            options += "<option value = '" + index + "' " + sel + ">" + value + "</option>";
        });
        $("#unite_stats_client").html(options);
    });
    $(document.body).on("change", '#type_donnee_stats_client', function () {
        var valeur = $(this).val(), options = '', first = '';
        $.each(jsn_compteur[valeur], function (index, value) {
            if (first == '')
                first = value;
            if (value == 'Total' || value == 'Consommation_total' || value == 'Data_attribut_profil_all' || value.substring(0, 3) == 'All')
                options += "<option value = '" + value + "' selected>" + index + "</option>";
            else
                options += "<option value = '" + value + "'>" + index + "</option>";
        });
        $("#compteurs_stats_client").html(options);
        $('#compteurs_stats_client').multipleSelect();
        options = '';
        var sel;
        $.each(jsn_unite[valeur], function (index, value) {
            sel = (index == 60 || index == 100 || index == 1048576) ? ' selected ' : '';
            options += "<option value = '" + index + "' " + sel + ">" + value + "</option>";
        });
        $("#unite_stats_client").html(options);
    });

    $(document.body).on("change", '.selectNatureTrafic', function () {
        var url = $(this).hasClass('event') ? "campagne/declencheur/type_donnees.php" : "ciblage/type_donnees.php";
        var valeur = $(this).val(), id = $(this).attr("id").replace("idSelectNatureTrafic", "idSelectTypeDonnee");
        charger_liste(id, valeur, url);
    });

    function charger_liste(id, valeur, url) {
        $.ajax({
            url: url,
            async: false,
            type: 'POST',
            data: {id_nature: valeur},
            success: function (retour) {
                $("#" + id).html(retour);
            },
            error: function () {

                alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
            }
        })
    }


//===========================================================================================================================================================================
//===========================================================================================================================================================================
//==================================================================       GESTION MENU     =================================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================
    $(document.body).on("click", '#idModPWD', function () {
        $("#divPrincipale").html("<br><br><br><center><img src = 'img/loading.gif'></center>");
        $("#divPrincipale").load("admin/user/modif_pwd.php");
        return false;
    });
    $(document.body).on("click", '.menuVertical', function () {
        clearInterval(tm_out_rec);
        clearInterval(tm_out_vou);
        clearInterval(tm_out_sms);
        clearInterval(tm_out_mgr);
        clearInterval(tm_out_data);
        clearInterval(tm_out_ftp);
        var menuAct = $(this), href = $(this).attr("href");
        if (!menuAct.hasClass('lism'))
            $('.sous-menu').slideUp('normal');
        if (!menuAct.hasClass('has-sub')) {
            $("#divPrincipale").html("<br><br><br><center><img src = 'img/loading.gif'></center>");
            $("#divPrincipale").load(href + ".php");
        } else {
            var checkElement = $(this).next();
            if ((checkElement.is('ul')) && (checkElement.is(':visible'))) {
                $(this).closest('li').removeClass('active');
                checkElement.slideUp('normal');
            }
            if ((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
                $('#cssmenu ul ul:visible').slideUp('normal');
                checkElement.slideDown('normal');
            }
        }
        $(".menuVertical").each(
                function () {
                    $(this).removeClass("active");
                });
        menuAct.addClass("active");
        return false;
    });

    $(document.body).on("click", '.menu_racc', function () {
        clearInterval(tm_out_rec);
        clearInterval(tm_out_vou);
        clearInterval(tm_out_sms);
        clearInterval(tm_out_mgr);
        clearInterval(tm_out_data);
        clearInterval(tm_out_ftp);
        var href = $(this).attr("href").split('|');
        for (var i = 0; i < href.length; i++)
            $('#' + href[i]).click();
        return false;
    });


    $(document.body).on("click", '.menu_admin', function () {
        clearInterval(tm_out_rec);
        clearInterval(tm_out_vou);
        clearInterval(tm_out_sms);
        clearInterval(tm_out_mgr);
        clearInterval(tm_out_data);
        clearInterval(tm_out_ftp);
        var menuAct = $(this), href = $(this).attr("name");
        $("#divPrincipale").html("<br><br><br><center><img src = 'img/loading.gif'></center>");
        $("#divPrincipale").load(href + ".php");
        // $("#divPrincipale").load("admin/" + href + ".php");
        return false;
    });


    $(document.body).on("click", '.actionUser', function () {
        var Elt = $(this);
        if (Elt.hasClass('edit')) {
            idUser = Elt.attr('name').replace('editer', '');
            $("#divPrincipale").load("admin/user/creer.php", {user: idUser});
        } else if (Elt.hasClass('reset')) {
            idUser = Elt.attr('name').replace('reset', '');
            $.ajax({
                url: "admin/user/reset.php",
                async: false,
                type: 'POST',
                dateType: 'json',
                data: {user: idUser},
                success: function (retour) {
                    ret = JSON.parse(retour);
                    alertDialog(ret.message, 'success');
                }
            });
        } else {
            idUser = Elt.attr('name').replace('gestion', '');
            //$("#divPrincipale").load("admin/user/gestion.php", {user: idUser});
            $.ajax({
                url: "admin/user/gestion.php",
                async: false,
                type: 'POST',
                dateType: 'json',
                data: {user: idUser},
                success: function (retour) {
                    ret = JSON.parse(retour);
                    if (ret.exec == '1') {
                        Elt.attr('src', 'img/' + ret.src + '.png');
                        Elt.attr('title', ret.title);
                    }
                }
            });
        }
        return false;
    });

    $(document.body).on("click", '.aff_det_cmp_client', function () {
        $('.msq_det_cmp_client').each(function () {
            var Elt = $(this), idCmp = $(this).attr('name').replace('cmp', '');
            $('#ligne_det_' + idCmp).remove();
            Elt.removeClass('msq_det_cmp_client').addClass('aff_det_cmp_client').val('Afficher');
        });
        var Elt = $(this), numero = $('#idNumeroInfos').val(), idCmp = $(this).attr('name').replace('cmp', '');
        $('#ligne_campagne_' + idCmp).after('<tr style="background-color:#ababab;" id="ligne_det_' + idCmp + '"><td colspan="5" id="td_det_' + idCmp + '"><br><center><img src = "img/loading.gif"></center></td></tr>')
        $.ajax({
            url: "campagne/client/det_campagne.php",
            async: false,
            type: 'POST',
            dateType: 'json',
            data: {numero: numero, idCmp: idCmp},
            success: function (retour) {
                $('#td_det_' + idCmp).html(retour);
                Elt.removeClass('aff_det_cmp_client').addClass('msq_det_cmp_client').val('Masquer');
            }
        });
    });

    $(document.body).on("click", '.msq_det_cmp_client', function () {
        var Elt = $(this), idCmp = $(this).attr('name').replace('cmp', '');
        $('#ligne_det_' + idCmp).remove();
        Elt.removeClass('msq_det_cmp_client').addClass('aff_det_cmp_client').val('Afficher');
    });

    $(document.body).on("click", '.actionCron', function () {
        var Elt = $(this);
        type = Elt.attr('name');
//        inpt = $('input:radio:checked').attr('id');
//        console.log(inpt);
        var message = (Elt.attr('title') == 'Activer') ? 'Voulez vous activer le chargement ?' : 'Voulez vous désactiver le chargement ?';
        $("#dialog-message").html("<center class= 'alert-box warning'>" + message + "</center>");
        $("#dialog-message").dialog({
            modal: true,
            resizable: true,
            draggable: false,
            width: "400",
            height: "250",
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                $(":button:contains('Non')").focus(); // Set focus to the [Ok] button
            },
            buttons: [{text: "Oui", click: function () {
                        $.ajax({
                            url: "admin/plateforme/cron/gestionCron.php",
                            async: false,
                            type: 'POST',
                            dateType: 'json',
                            data: {type: type},
                            success: function (retour) {
                                ret = JSON.parse(retour);
                                if (ret.exec == '1') {
                                    $('#menu_21').click();
//                                    $('label[for='+inpt+'] > span > span').click();
                                    $("#dialog-message").dialog("close");
                                }
                            }
                        });
                    }
                },
                {text: "Non", click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });

        return false;
    });
    $(document.body).on("dblclick", '.spanEdit', function () {
        var Elt = $(this), txt = Elt.text(), id = Elt.attr('name'), valeur = $('span[name=valeur_' + id + ']').text();
        $("#dialog-message").html("<br>Libellé :<input type = 'text' value ='" + txt + "' width=40 id='libelle_" + id + "'/><br><br>\
            Valorisation : <input type = 'text' value ='" + valeur + "' size= '5' class='chiffrePoint' id='NewVal_" + id + "'/> UM");
        $("#dialog-message").dialog({
            modal: true,
            resizable: true,
            draggable: false,
            width: "400",
            height: "300",
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                $(":button:contains('Non')").focus(); // Set focus to the [Ok] button
            },
            buttons: [{text: "Enregistrer", click: function () {
                        var newName = $('#libelle_' + id).val();
                        var newVal = $('#NewVal_' + id).val();
                        $.ajax({
                            url: "admin/plateforme/compteurs/gestionCmpt.php",
                            async: false,
                            type: 'POST',
                            dateType: 'json',
                            data: {name: newName, valeur: newVal, idCmpt: id},
                            success: function (retour) {
                                ret = JSON.parse(retour);
                                if (ret.exec == '1') {
                                    Elt.text(newName);
                                    $('span[name=valeur_' + id + ']').text(newVal);
                                    $("#dialog-message").dialog("close");
                                    alertDialog('Mise a jour faite avec succès', 'success');
                                }
                            }
                        });
                    }
                },
                {text: "Non", click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });

    })

    $(document.body).on('change', '#idBroadCastCmp', function () {
        var valSel = $(this).val();
        if (valSel == 0) {
            $(".teasing").hide();
            $(".teasing .smsComms").removeClass('obligatoire');
        } else {

            $(".teasing .smsComms").removeClass('obligatoire');
            $(".teasing .smsComms").addClass('obligatoire');
            $(".teasing").show();
            if (valSel == 3) {
                $(".teasing.fr").hide();
                $("#idSmsCampagneFr").removeClass('obligatoire');
            }
            if (valSel == 4) {
                $(".teasing.ar").hide();
                $("#idSmsCampagneAr").removeClass('obligatoire');
            }
        }
    });

    $(document.body).on("click", '.tag', function () {
        $("#idSmsCampagne" + lgSMS).insertAtCaret($(this).attr('name'));
    });
    $(document.body).on("click", '.tagBns', function () {
        var id = $(this).parent().attr('id').replace('tags_', '');
        $("#idSMSBonus" + lgSMS + id).insertAtCaret($(this).attr('name'));
    });
    $(document.body).on("focusout", '.smsComms', function () {
        lgSMS = $(this).attr('id').replace('idSmsCampagne', '').replace('idSMSBonus', '').substring(0, 2);
        var Elt = $(this);
        Elt.attr('cols', 40);
        if (Elt.attr('name') != '') {
            spanName = Elt.attr('name') + 'Span';
            $('span[name=' + spanName + ']').hide();
        }
    });
    $(document.body).on("focus", '.smsComms', function () {
        var Elt = $(this);
        Elt.attr('cols', 70);
        if (Elt.attr('name') != '') {
            spanName = Elt.attr('name') + 'Span';
            $('span[name=' + spanName + ']').show();
        }
    });
    $(document.body).on("keyup", '.smsComms', function () {
        var Elt = $(this);
        if (Elt.attr('name') != '') {
            spanName = Elt.attr('name') + 'Span';
            nbcar = Elt.val().length;
//            var arregex = /[\u0600-\u06ff]/;
//            $('span[name=' + spanName + ']').html(nbcar + ' : ' + arregex.test(Elt.val()));
            div = (Elt.hasClass('arabic')) ? 70 : 160;
            rst = nbcar % div;
            nbs = (nbcar - rst) / div;
            if (rst > 0)
                nbs++;
            $('span[name=' + spanName + ']').html(nbcar + ' Caractéres | ' + nbs + ' SMS');
        }
    });

    $(document.body).on("click", '.actionCmpt', function () {
        var Elt = $(this), changes = new Object(), conf = new Object(), src = Elt.attr('src'), title = Elt.attr('title');
        var stat = ($(this).hasClass('stat')) ? 0 : 1, id = Elt.attr('name');
        changes['img/on1.png'] = 'img/off1.png';
        changes['img/off1.png'] = 'img/on1.png';
        changes['Activer'] = 'Désactiver';
        changes['Désactiver'] = 'Activer';
        conf['img/on1.png'] = 'Voulez vous désactivez le compteur ?';
        conf['img/off1.png'] = 'Voulez vous activez le compteur ?';
        $("#dialog-message").html('<span class="alert-box warning">' + conf[src] + '</span>');
        $("#dialog-message").dialog({
            modal: true,
            resizable: true,
            draggable: false,
            width: "400",
            height: "220",
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                $(":button:contains('Non')").focus(); // Set focus to the [Ok] button
            },
            buttons: [{text: "Oui", click: function () {
                        $.ajax({
                            url: "admin/plateforme/compteurs/gestionCmpt.php",
                            async: false,
                            type: 'POST',
                            dateType: 'json',
                            data: {idCmpt: id, stat: stat, action: title},
                            success: function (retour) {
                                ret = JSON.parse(retour);
                                if (ret.exec == '1') {
                                    Elt.attr('src', changes[src]);
                                    Elt.attr('title', changes[title]);
                                    $("#dialog-message").dialog("close");
                                    alertDialog('Mise a jour faite avec succès', 'success');
                                }
                            }
                        });
                    }
                },
                {text: "Non", click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });



        return false;
    });


    $(document.body).on("click", '#idEnregistrerWF', function () {
        var parms = new Object();
        $('.wfv').each(function () {
            parms[$(this).attr('name')] = $(this).val();
        });
        $.ajax({
            url: "admin/wf/save.php",
            async: false,
            type: 'POST',
            dateType: 'json',
            data: {wf: JSON.stringify(parms)},
            success: function (retour) {
                ret = JSON.parse(retour);
                if (ret.exec == '1') {
                    alertDialog('Mise a jour faite avec succès', 'success');
                }
            }
        });
    });

    $(document.body).on("click", '.addValidator', function () {
        var sel = $('#idTableAddWF tr:last select').val(), html = $('#idTableAddWF tr:last select').html();
        if ($('#idTableAddWF tr:last select > option').length > 1) {
            tr = $('#idTableAddWF tr').length - 1;
            $('#idTableAddWF').append('<tr><th align="right">Validateur N° ' + tr + '</th><td colspan=2><select name="' + tr + '" class="wfv">' + html + '</select></td></tr>');
            $('#idTableAddWF tr:last option[value=' + sel + ']').remove();
            $('#idTableAddWF tr:last select').width('204px');
        } else
            alertDialog('Impossible', 'error');
    });

    $(document.body).on("change", '.wfv', function () {
        var pos = $(this).attr('name') * 1;
        $('.wfv').each(function () {
            if ($(this).attr('name') * 1 > pos)
                $(this).parent().parent().remove();
        });

    });

    $(document.body).on("click", '.actionProfil', function () {
        var Elt = $(this);
        if (Elt.hasClass('edit')) {
            idProfil = Elt.attr('name').replace('editer', '');
            $("#divPrincipale").load("admin/profil/creer.php", {profil: idProfil});
        } else if (Elt.hasClass('img_action_profil')) {
            src = Elt.attr('src').replace('1.png', '').replace('img/', '');
            newSrc = (src == 'on') ? 'img/off1.png' : 'img/on1.png';
            Elt.attr('src', newSrc);
        } else if (Elt.hasClass('wf')) {
            idProfil = Elt.attr('name').replace('wf', '');
            $("#divPrincipale").load("admin/wf/creer.php", {profil: idProfil});
        } else {
            idProfil = Elt.attr('name').replace('gestion', '');
            //$("#divPrincipale").load("admin/user/gestion.php", {user: idUser});
            $.ajax({
                url: "admin/profil/gestion.php",
                async: false,
                type: 'POST',
                dateType: 'json',
                data: {profil: idProfil},
                success: function (retour) {
                    ret = JSON.parse(retour);
                    if (ret.exec == '1') {
                        Elt.attr('src', 'img/' + ret.src + '.png');
                        Elt.attr('title', ret.title);
                    }
                }
            });
        }
        return false;
    });
    $(document.body).on("click", '#idEnregistrerProfil', function () {
        var profil = new Object(), valid = true, Elt = $(this);
        if ($("#name_profil").val().trim() == '') {
            $("#name_profil").addClass('champVide').after("<span id='spanname_profil'> Champ obligatoire</span>");
            valid = false;
        } else
            profil['name_profil'] = $("#name_profil").val().trim();
        if (valid)
            $(".nv_profil").each(
                    function () {
                        id = $(this).attr("id");
                        profil[id] = $(this).attr('src').replace('1.png', '').replace('img/', '');
                    });
        profil['id'] = $("#id_profil").val().trim();
        if (valid)
            $.ajax({
                url: "admin/profil/save.php",
                async: false,
                type: 'POST',
                dateType: 'json',
                data: {profil: JSON.stringify(profil)},
                success: function (retour) {
                    ret = JSON.parse(retour);
                    if (ret.exec == '1') {
                        $("#spanRetour").html(ret.message).removeAttr('class').addClass('alert-box success');
                        Elt.remove();
                    } else
                        $("#spanRetour").html(ret.message).removeAttr('class').addClass('alert-box error');
                },
                error: function () {
                    alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
                }
            });
    });
    $(document.body).on("click", '#enregistrer_nouvel_user', function () {
        var user = new Object(), valid = true, Elt = $(this);
        $(".nv_user").each(
                function () {
                    id = $(this).attr("id");
                    user[id] = $(this).val();
                    if ($("#span" + id).length)
                        $("#span" + id).remove();
                    if ($(this).val().trim() == '') {
                        $(this).addClass('champVide').after("<span id='span" + id + "'> Champ obligatoire</span>");
                        valid = false;
                    }

                });
        if (valid)
            $.ajax({
                url: "admin/user/save.php",
                async: false,
                type: 'POST',
                dateType: 'json',
                data: {user: JSON.stringify(user)},
                success: function (retour) {
                    ret = jQuery.parseJSON(retour);
                    if (typeof ret == 'object') {
                        if (ret.exec == '1') {
                            $("#spanRetour").html(ret.message).removeAttr('class').addClass('alert-box success');
                            Elt.remove();
                            $(".nv_user").each(function () {
                                $(this).attr('disabled', true);
                            });
                        } else
                            $("#spanRetour").html(ret.message).removeAttr('class').addClass('alert-box error');
                    } else
                        alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
                },
                error: function () {
                    alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
                }
            });
    });
    $(document.body).on("change", '.cls_fl', function () {
        var elt = $(this), filename = $(this).val();
        id = $(this).attr('name');
        if (filename) {
            var file = event.target.files[0];
            var reader = new FileReader();
            reader.readAsText(file);
            reader.onload = function (event) {
                var num, vn = 0, ivn = 0, data;
                var csvData = event.target.result.replace(/\r/g, "");
                data = csvData.split("\n");
                if (data && data.length > 0) {
                    for (var i = 0; i < data.length - 1; i++) {
                        num = '222' + data[i];
                        num = parseInt(num.substr(num.length - 11));
                        if (num <= 22239999999 && num >= 22230000000)
                            vn++;
                        else
                            ivn++;
                    }
                    if (data[i].trim() != '') {
                        num = '222' + data[i];
                        num = parseInt(num.substr(num.length - 11));
                        if (num <= 22239999999 && num >= 22230000000)
                            vn++;
                        else
                            ivn++;
                    }
                    if (ivn) {
                        $('#' + id).html('').removeClass('notice');
                        alertDialog('Le fichier contient ' + ivn + ' numéros invalides', 'error');
                        elt.val('');
                    } else if (vn)
                        $('#' + id).html(filename + ' (' + vn + ' numéros)').addClass('notice');
                    else {
                        $('#' + id).html('').removeClass('notice');
                        alertDialog('Le fichier ne contient pas des numéros valides', 'error');
                        elt.val('');
                    }
                } else {
                    alertDialog('Fichier vide', 'error');
                }
            }
        } else
            $('#' + id).html(filename).removeClass('notice');
    });


    $(document.body).on("click", '#idEnrModPWD', function () {
        var user = new Object(), valid = true, Elt = $(this);
        $(".mod_pwd").each(
                function () {
                    id = $(this).attr('id');
                    if ($("#span" + id).length)
                        $("#span" + id).remove();
                    if ($(this).val() == '') {
                        valid = false;
                        $(this).addClass('champVide').after("<span id='span" + id + "'> Champ obligatoire</span>");
                    }
                    user[$(this).attr("id")] = $(this).val();
                });
        if (valid && user['nv_pwd'] != user['conf_pwd']) {
            valid = false;
            $("#conf_pwd").addClass('champVide').after("<span id='spanconf_pwd'> Différent du nouveau PWD</span>");
        }
        if (valid)
            $.ajax({
                url: "admin/user/modif_pwd.php",
                async: false,
                type: 'POST',
                dateType: 'json',
                data: {user: JSON.stringify(user)},
                success: function (retour) {
//                    ret = JSON.parse(retour);
                    ret = jQuery.parseJSON(retour);
                    if (typeof ret == 'object') {
                        if (ret.exec == '1') {
                            $("#spanRetour").html(ret.message).removeAttr('class').addClass('alert-box success');
                            Elt.remove();
                        } else
                            $("#spanRetour").html(ret.message).removeAttr('class').addClass('alert-box error');
                    } else
                        alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
                },
                error: function () {
                    alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
                }
            });
    });
    $(document.body).on("change", '.unite_periodique', function () {
        var valeur = $(this).val(), id = $(this).attr("id").replace("idUnitePeriodique", "");
        // alertDialog(id);
        $.ajax({
            url: "ciblage/periode.php",
            async: false,
            type: 'POST',
            data: {idUnite: valeur},
            success: function (retour) {
                var donnees = retour.split("|");
                // $("#idPeriodeFrom"+id).replaceWith('<SELECT id="idPeriodeFrom'+id+'" class = "critere"></select>');
                // $("#idPeriodeTo"+id).replaceWith('<SELECT id="idPeriodeTo'+id+'" class = "critere"></select>');
                $("#idPeriodeFrom" + id).html(donnees[1]);
                $("#idPeriodeTo" + id).html(donnees[4]);
                if (valeur == 'j') {
                    $("#idLibellePeriode" + id).text("  (31 Jours)");
                    // $("#idPeriodeFrom"+id).replaceWith('<input type="text" name="'+donnees[2]+'|'+donnees[3]+'" id="idPeriodeFrom'+id+'" class = "datepicker critere"/>');
                    // $("#idPeriodeTo"+id).replaceWith('<input type="text" name="'+donnees[2]+'|'+donnees[3]+'" id="idPeriodeTo'+id+'" class = "datepicker critere"/>');
                }
                if (valeur == 'm')
                    $("#idLibellePeriode" + id).text("  (12 Mois)");
                if (valeur == 's')
                    $("#idLibellePeriode" + id).text("  (4 Semaines)");
                if (valeur == 'a')
                    $("#idLibellePeriode" + id).text("  (4 Ans)");
            },
            error: function () {
                alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
            }
        });
    });
    $(document.body).on("click", '.ajouterCritere', function () {
        var Elt = $(this), idG = $(this).attr("id").replace("AjouterCritere", ""), idC = getIdCritere();
        var nat_tr = $("#idSelectNatureTrafic" + idG).val(), tp_dn = $("#idSelectTypeDonnee" + idG).val();
        var nat_tr_txt = $("#idSelectNatureTrafic" + idG + " :selected").text(), tp_dn_txt = $("#idSelectTypeDonnee" + idG + " :selected").text();
        if (nat_tr == "") {
            $("#idSelectNatureTrafic" + idG).addClass("champVide");
            alertDialog("Veuillez choisir la nature de trafic", "warning");
            return false;
        }
        var url = (Elt.hasClass('declencheur')) ? "campagne/declencheur/critere.php" : "ciblage/critere.php";
        var evnt = (Elt.hasClass('event')) ? 'event' : '';

        $.ajax({
            url: url,
            async: false,
            type: 'POST',
            data: {idGroup: idG, idCritere: idC, nat_tr: nat_tr, tp_dn: tp_dn, nat_tr_txt: nat_tr_txt, tp_dn_txt: tp_dn_txt, evnt: evnt},
            success: function (retour) {
                $("#critereContent" + idG).append(retour);
                if (Elt.hasClass('declencheur'))
                    $("#idSelectNatureTrafic" + idG + ' option:not(:selected)').remove();
            },
            error: function () {
                alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
            }
        });
    });

    function getIdBonus() {
        var idB = 0;
        $(".divCntBonus").each(function () {
            id = $(this).attr("id").replace('divCntBonus', '');
            if (id > idB)
                idB = parseInt(id);
        });
        return idB + 1;
    }

    $(document.body).on("click", '.ajouterDIV', function () {
        var idDiv = $(this).attr("name").replace('ajouterDiv', ''), idB = getIdBonus(), td = $("#type_declencheur").val();
        $.ajax({
            url: 'campagne/bonus/bonus.php',
            async: false,
            type: 'POST',
            data: {idBonus: idB, type_d: td, idDivCont: idDiv},
            success: function (retour) {
                $("#divGroupeBonus" + idDiv).append(retour);
            },
            error: function () {
                alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
            }
        });
    });


    $(document.body).on("change", '.selectNatureBonus', function () {
        var idSel = $(this).attr('id').replace('idSelectNatureBonus', ''), natBonus = $(this).val(),
                typeBonus = $('#idTypeBonus' + idSel).val(), id = ($(this).parents('.divBonus').attr('id').replace('Bonus', '')),
                idNatureTrafic = $('#idSelectNatureTrafic' + id).val(),
                typeDec = $('#type_declencheur').val();
        $('#idSelectNatureBonus' + idSel + ' option[value=""]').remove();
        $.ajax({
            url: 'campagne/bonus/type_bonus.php',
            async: false,
            type: 'POST',
            data: {natBonus: natBonus, typeBonus: typeBonus, idNatureTrafic: idNatureTrafic, typeDec: typeDec},
            success: function (retour) {
                ret = JSON.parse(retour);
                if (ret.exec == '1') {
                    $("#idSelectCompteur" + idSel).html(ret.cmp);
                    $("#idUniteCompteur" + idSel).html(ret.unite);
                    if (natBonus == '17')
                        $('#label' + idSel).text("Service :");
                    else
                        $('#label' + idSel).text("Compteur :");
                } else
                    alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
            },
            error: function () {
                alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
            }
        });
    });

    $(document.body).on("change", '.selectTypeBonus', function () {
        var idSel = $(this).attr('id').replace('idTypeBonus', ''), typeBonus = $(this).val(),
                id = ($(this).parents('.divBonus').attr('id').replace('Bonus', '')), idNatureTrafic = $('#idSelectNatureTrafic' + id).val();
        if (!idNatureTrafic) {
            $("#idTypeBonus" + idSel + " option:selected").prop("selected", false);
            $("#idTypeBonus" + idSel).attr('selectedIndex', '0');
            alertDialog("Merci de sélectionner un type de donnée", "error");
        } else
            $.ajax({
                url: 'campagne/bonus/nature_bonus.php',
                async: false,
                type: 'POST',
                data: {typeBonus: typeBonus, idNatureTrafic: idNatureTrafic},
                success: function (retour) {
                    ret = JSON.parse(retour);
                    if (ret.exec == '1') {
                        $("#idSelectNatureBonus" + idSel).html(ret.nature);
                        $("#idSelectCompteur" + idSel).html(ret.cmp);
                        $("#idUniteCompteur" + idSel).html(ret.unite);
                    } else
                        alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
                },
                error: function () {
                    alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
                }
            });
    });

    $(document.body).on("click", '.SupBonus', function () {
        var id = $(this).attr("name").replace('Bonus', '');
        $("#dialog-message").html("<center class= 'alert-box warning'>Voulez vous supprimer ce Bonus ? </center>");
        $("#dialog-message").dialog({
            modal: true,
            resizable: true,
            draggable: false,
            width: "400",
            height: "250",
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                $(":button:contains('Non')").focus(); // Set focus to the [Ok] button
            },
            buttons: [{text: "Oui", click: function () {
                        $("#divCntBonus" + id).remove();
                        $(this).dialog("close");
                    }
                },
                {text: "Non", click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });
    });

    $(document.body).on("click", '.SupprimerDIV', function () {
        var name = $(this).attr("name");
        $("#dialog-message").html("<center class= 'alert-box warning'>Voulez vous supprimer cet élément ? </center>");
        $("#dialog-message").dialog({
            modal: true,
            resizable: true,
            draggable: false,
            width: "400",
            height: "250",
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                $(":button:contains('Non')").focus(); // Set focus to the [Ok] button
            },
            buttons: [{text: "Oui", click: function () {
                        $("#" + name).remove();
                        $(this).dialog("close");
                    }
                },
                {text: "Non", click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });
    });
    $(document.body).on("click", '.AjouterGroupe', function () {
        var Elt = $(this), idG = getIdGroup();
//        idCnt = Elt.hasClass('declencheur') ? 'cntGrEvent' : 'cntGrCiblage';
        hEvent = Elt.hasClass('event') ? 1 : 0;
        if (Elt.hasClass('declencheur')) {
            idCnt = 'cntGrEvent';
            url = 'campagne/declencheur/groupe.php';
        } else {
            idCnt = 'cntGrCiblage';
            url = 'ciblage/groupe.php';
        }

        $.ajax({
            url: url,
            async: false,
            type: 'POST',
            data: {idGroup: idG, hEvent: hEvent},
            success: function (retour) {
                $("#" + idCnt).append(retour);
            },
            error: function () {
                alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
            }
        });
    });

    function fn_cmp_modif_1(idCmp) {
        $("#popup").load("campagne/generales.php", {idCmp: idCmp});
        $("#popup").dialog({
            modal: true,
            resizable: true,
            draggable: true,
            width: "1000",
            height: "650",
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                $(":button:contains('Annuler')").focus(); // Set focus to the [Ok] button
            },
            buttons: [{text: "Enregistrer", click: function () {
                        if (verifOngletGle()) {
                            parms_gle = new Object();
                            params_gle();
                            parms_gle['dmd'] = 'gle';
                            parms_gle['idCmp'] = idCmp;
                            var form = $('#fileUploadForm')[0];
                            var data = new FormData(form);
                            data.append('cmp', JSON.stringify(parms_gle));
                            $.ajax({
                                type: "POST",
                                async: true,
                                dataType: 'json',
                                enctype: 'multipart/form-data',
                                url: "campagne/modif_save.php",
                                data: data,
                                //data: {data: data, cmp: JSON.stringify(parms)},
                                processData: false,
                                contentType: false,
                                cache: false,
                                timeout: 600000,
                                success: function (data) {
                                    $("#popup").dialog("close");
//                        var ret = JSON.parse(data);
                                    if (data.exec == "1") {
                                        alertDialog(data.message, 'success');
                                        $('input[name="cmp' + idCmp + '"]').click();
                                        showDetaillCmp(idCmp);
                                    } else {
                                        alertDialog(data.message);
                                    }
                                },
                                error: function (e) {
                                    alertDialog(data);
                                }
                            });

                            $(this).dialog("close");
                        } else
                            console.log('Erreur');
                    }
                },
                {text: "Annuler", click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });
    }

    function fn_cmp_modif_2(idCmp) {
        $("#popup").load("campagne/ciblage_campagne.php", {idCmp: idCmp});
        $("#popup").dialog({
            modal: true,
            resizable: true,
            draggable: true,
            width: "1200",
            height: "550",
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                $(":button:contains('Annuler')").focus(); // Set focus to the [Ok] button
            },
            buttons: [{text: "Enregistrer", click: function () {
                        parms = new Object();
                        send = true;
                        parms['dmd'] = 'ciblage';
                        parms['idCmp'] = idCmp;
                        parms['idChoixCible'] = $('input:radio[name="ciblage_campagne_choix"]:checked').attr('id');
                        if (parms['idChoixCible'] == 'idChosirCible') {
                            parms['idCible'] = $('#listeCibleCampagne').val();
                            if (parms['idCible'] == '') {
                                send = false;
                                if (!$('#spanlisteCibleCampagne').length)
                                    $("#listeCibleCampagne").addClass('champVide').after("<span id= 'spanlisteCibleCampagne' class='alert-box error'>Choisir l'une des cibles</span>");
                            }
                        } else if (parms['idChoixCible'] == 'idComposerCible') {
                            parms['cible'] = collectCiblage();
                            if (!Object.keys(parms['cible']).length) {
                                send = false;
                                //alertDialog('Merci de composer une cible', 'warning');
                            }
                        }
                        if (send) {
                            $.ajax({
                                type: "POST",
                                async: false,
                                dataType: 'json',
                                url: "campagne/modif_save.php",
                                data: {cmp: JSON.stringify(parms)},
                                success: function (data) {
                                    $("#popup").dialog("close");
                                    if (data.exec == "1") {
                                        alertDialog(data.message, 'success');
                                        $('input[name="cmp' + idCmp + '"]').click();
                                        showDetaillCmp(idCmp);
                                        $("#sky-tab1").attr('checked', false);
                                        $("#sky-tab2").attr('checked', 'checked');
                                    } else {
                                        alertDialog(data.message);
                                    }
                                },
                                error: function (e) {
                                    alertDialog(data);
                                }
                            });
                            $(this).dialog("close");
                        }
                    }
                },
                {text: "Annuler", click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });
    }
    function fn_cmp_modif_3(idCmp) {
        alertDialog('En cours de développement', 'notice');
    }
    function fn_cmp_modif_5(idCmp) {
        alertDialog('Veuillez choisir onglet modifiable', 'warning');
    }
    function fn_cmp_modif_6(idCmp) {
        alertDialog('Veuillez choisir onglet modifiable', 'warning');
    }
    function fn_cmp_modif_4(idCmp) {
        $("#popup").load("campagne/budget.php", {idCmp: idCmp});
        $("#popup").dialog({
            modal: true,
            resizable: true,
            draggable: true,
            width: "1000",
            height: "550",
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                $(":button:contains('Annuler')").focus(); // Set focus to the [Ok] button
            },
            buttons: [{text: "Enregistrer", click: function () {
                        parms = new Object();
                        parms['dmd'] = 'budget';
                        parms['idCmp'] = idCmp;
                        $(".budget").each(function () {
                            parms[$(this).attr('name')] = $(this).val();
                        })
                        $.ajax({
                            type: "POST",
                            async: false,
                            dataType: 'json',
                            url: "campagne/modif_save.php",
                            data: {cmp: JSON.stringify(parms)},
                            success: function (data) {
                                $("#popup").dialog("close");
//                        var ret = JSON.parse(data);
                                if (data.exec == "1") {
                                    alertDialog(data.message, 'success');
                                    $('input[name="cmp' + idCmp + '"]').click();
                                    showDetaillCmp(idCmp);
                                    $("#sky-tab1").attr('checked', false);
                                    $("#sky-tab4").attr('checked', 'checked');
                                } else {
                                    alertDialog(data.message);
                                }
                            },
                            error: function (e) {
                                alertDialog(data);
                            }
                        });

                        $(this).dialog("close");
                    }
                },
                {text: "Annuler", click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });
    }

    $(document.body).on("click", '#btnModifCmp', function () {
        var id = $('input:radio[name="sky-tabs"]:checked').attr('id'), fn = id.replace('sky-tab', 'fn_cmp_modif_');
        idCmp = $(this).attr('name');
        eval(fn + "(" + idCmp + ")");
    });
//===========================================================================================================================================================================
//===========================================================================================================================================================================
//===========================================================       ENVOI des infos de la cible     =========================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================
    $(document.body).on("click", '.export_bl, .export_wl, .export_gt', function () {
        var idCmp = $(this).attr('name'), wl = $(this).attr('id');
        $("#hIdCible").val(idCmp);
        $("#hwl").val(wl);
        $("#formSendExport").submit();
    });

    $(document.body).on("click", '.executer_cible_sv', function () {
        var idCmp = $(this).attr('name').replace('executer', '');
        $("#dialog-message").html("<center><img src = 'img/loading.gif'></center>");
        $('#dialog-message').load('ciblage/executer_cible_sv.php', {idCmp: idCmp});
        $("#dialog-message").dialog({
            modal: true,
            width: "700",
            height: "500",
            resizable: true,
            draggable: true,
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
            },
            buttons: [
                {text: "OK", click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });
    });

    $(document.body).on("click", '.executer_cible', function () {
        var idCible = $(this).attr('name').replace('executer', '');
        $("#dialog-message").html("<center><img src = 'img/loading.gif'></center>");
        $('#dialog-message').load('ciblage/executer_cible.php', {idCible: idCible});
        $("#dialog-message").dialog({
            modal: true,
            width: "700",
            height: "500",
            resizable: true,
            draggable: true,
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
            },
            buttons: [
                {text: "OK", click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });
    });

    $(document.body).on("click", '.modifier_cible', function () {
        var btnSup = $(this), idCible = btnSup.attr("name").replace('modifier', '');
        $("#popup").load("ciblage/modifierCible.php", {idCible: idCible});
        $("#popup").dialog({
            modal: true,
            resizable: true,
            draggable: true,
            width: "1200",
            height: "550",
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                $(":button:contains('Non')").focus(); // Set focus to the [Ok] button
            },
            buttons: [{text: "Enregistrer", click: function () {
                        var tab_parms = collectCiblage();
//                        tab_parms["tp_dmd"] = 'modifier_ciblage';
                        $.ajax({
                            url: "ciblage/modifierCible.php",
                            async: false,
                            type: 'POST',
                            dataType: 'json',
                            data: {parms: JSON.stringify(tab_parms)},
                            success: function (retour) {
                                if (retour.exec == '1') {
                                    $('#popup').dialog("close");
                                    $("input.masquer_det_cible[name='cible" + idCible + "']").click();
                                    ;
                                    alertDialog(retour.message, 'success');
                                    $("input.afficher_det_cible[name='cible" + idCible + "']").click();
                                    ;
                                } else
                                    alertDialog(retour.message, 'error');
                            },
                            error: function () {
                                alertDialog("ERREUR LORS DE LA RECHERCHE DE LA PAGE", "error");
                            }
                        });

                    }
                },
                {text: "Annuler", click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });
    });

    $(document.body).on("click", '.supprimer_cible', function () {
        var btnSup = $(this), idCible = btnSup.attr("name").replace('supprimer', '');
        $("#dialog-message").html("Voulez vous supprimer la cible ?");
        $("#show_cible_" + idCible).addClass('supprimerLinge');
        $("#dialog-message").dialog({
            modal: true,
            resizable: true,
            draggable: false,
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                $(":button:contains('Non')").focus(); // Set focus to the [Ok] button
            },
            buttons: [{text: "Oui", click: function () {
                        $.ajax({
                            url: "ciblage/removeCible.php",
                            async: false,
                            type: 'POST',
                            dataType: 'json',
                            data: {idCible: idCible},
                            success: function (retour) {
                                if (retour.exec == '1') {
                                    $("#ligne_cible_" + idCible).remove();
                                    $("#show_cible_" + idCible).remove();
                                    $("#dialog-message").dialog("close");
                                    alertDialog(retour.message, 'success');
                                } else
                                    alertDialog(retour.message, 'error');
                            },
                            error: function () {
                                alertDialog("ERREUR LORS DE LA RECHERCHE DE LA PAGE", "error");
                            }
                        });
                    }
                },
                {text: "Non", click: function () {
                        $("#show_cible_" + idCible).removeClass('supprimerLinge');
                        $(this).dialog("close");
                    }
                }
            ]
        });
    });
    $(document.body).on("click", '#exportCiblage', function () {
        if (!fn_ciblage('exp'))
            return false;
    });
    $(document.body).on("click", '#enregistrerCiblage', function () {
        if ($("#idCibleName").val().trim() == '') {
            $("#idCibleName").addClass('champVide');
            alertDialog('Merci de saisir un nom pour la cible', 'warning');
        } else
            fn_ciblage('enregistrer');
    });
    $(document.body).on("click", '#idCalculerCible', function () {
        fn_ciblage('ciblage');
    });
    var tm_out;
    function fn_testChargementEnCours() {
        $.ajax({
            url: "ciblage/test_fin_ciblage.php",
            dataType: 'json',
            success: function (retour) {
                if (retour.exec == 0) {
                    clearInterval(tm_out);
                    $("#dialog-message").html("<center>les données sont téléchargées</center>");
                }
            }
        });
    }

    function collectCiblage() {
        var tab_parms = new Object(), send = true, message = "Vous devez ajouter au moins un critére", valCr;
        $("#cntGrCiblage .critere").removeClass('champVide');

        if ($("#cntGrCiblage .critere").length) {
            $("#cntGrCiblage .critere").each(
                    function () {
                        if (!$(this).hasClass('ignored') && ($(this).is('input') || $(this).is('select'))) {
                            valCr = ($(this).hasClass("champCiblage")) ? $(this).val().split(":")[0] : $(this).val();
                            tab_parms[$(this).attr("id")] = valCr;
                            if (!$(this).val()) {
                                $(this).addClass("champVide");
                                send = false;
                                message = "Merci de remplire tous les champs";
                            }
                        }
                    });
            if (!Object.keys(tab_parms).length) {
                message = "Ciblage Vide";
                console.log(message);
                send = false;
            }
            if (send) {
                tab_parms["associationGroupe"] = $("#cntGrCiblage input:checked[name=associationGroupe]").val();
                if ($("#idCibleName").length)
                    tab_parms["cibleName"] = $("#idCibleName").val();
                if ($("#idCibleHidden").length)
                    tab_parms["cibleId"] = $("#idCibleHidden").val();
                $("#cntGrCiblage input:checked.groupe").each(
                        function () {
                            tab_parms[$(this).attr("name")] = $(this).val();
                        });
                // vérifier si périodes sont correctes
                var code = '', p_from = '', p_to = '';
                $('.select_for_periode').each(function () {
                    code = $(this).attr('id').replace('idPeriodeFrom_', '').replace('idPeriodeTo_', '');
                    p_from = $("#idPeriodeFrom_" + code).val();
                    p_to = $("#idPeriodeTo_" + code).val();
                    if (p_to < p_from) {
                        send = false;
                        tab_parms = new Object();
                        if (!$("#idPeriodeFrom_" + code).hasClass('champVide')) {
                            $("#idPeriodeFrom_" + code).addClass('champVide');
                            $("#idPeriodeTo_" + code).addClass('champVide');
                            message = "Periode invalide";
                            alertDialog(message, 'error');
                        }
                    }
                });
            } else {
                tab_parms = new Object();
                alertDialog(message, 'warning');
            }
        }else
            alertDialog('Ciblage Vide', 'warning');
        return tab_parms;
    }

    function fn_ciblage(type) {
        var tab_parms = collectCiblage();
        if (Object.keys(tab_parms).length) {
            tab_parms["tp_dmd"] = type;
//$("#" + idDivRetour).html("<center><img src = 'img/loading.gif'></center>");
            $("#dialog-message").html("<center><img src = 'img/loading.gif'></center>");
            if (type == 'exp') {
                $('#idInputParms').val(JSON.stringify(tab_parms));
                $('#exportCiblage').submit();
                $("#dialog-message").dialog({
                    modal: true,
                    width: "700",
                    height: "500",
                    resizable: true,
                    draggable: true,
                    open: function (event, ui) {
                        $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                    },
                    buttons: [
                        {text: "OK", click: function () {
                                $(this).dialog("close");
                            }
                        }
                    ]
                });
                tm_out = setInterval(function () {
                    fn_testChargementEnCours();
                }, 1000);
            } else {
                $("#dialog-message").load('ciblage/calcule_cible.php', {parms: JSON.stringify(tab_parms)});
                $("#dialog-message").dialog({
                    modal: true,
                    width: "700",
                    height: "500",
                    resizable: true,
                    draggable: true,
                    open: function (event, ui) {
                        $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                    },
                    buttons: [
                        {text: "OK", click: function () {
                                $(this).dialog("close");
                            }
                        }
                    ]
                });
            }
            return true;
        }
        return false;
    }


//===========================================================================================================================================================================
//===========================================================================================================================================================================
//===========================================================       Changement de champ ciblage     =========================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================
    $(document.body).on("change", '.champCiblage', function () {
        var code = $(this).val(), id = $(this).attr('id').replace('idTypeCompteur_', '');
        $.ajax({
            url: "ciblage/operateur_categorie.php",
            async: false,
            type: 'POST',
            dataType: 'json',
            data: {code: code, id: id},
            success: function (retour) {
                if (retour.sucess == '1') {
                    $("#operateur_" + id).html(retour.operator);
                    $("#divValRecherchee_" + id).html(retour.divValRech);
                }
            },
            error: function () {
                alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
            }
        });
    });
//===========================================================================================================================================================================
//===========================================================================================================================================================================
//========================================================       Sélection des valeurs de ciblage     =======================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================


    $(document.body).on("click", '.choix_valeur', function () {
        var direct = $(this).attr("src").replace("img/", "").replace(".png", ""), critere = $(this).attr("name");
        var txtSel, idSel, opt;
        if (direct == "droite") {
            txtSel = $("#origine_" + critere + " :selected").text();
            idSel = $("#origine_" + critere).val();
            if (idSel) {
                opt = '<option value = "' + idSel + '" selected>' + txtSel + '</option>';
                $("#valeurCritere_" + critere).append(opt);
                $("#origine_" + critere + " option[value='" + idSel + "']").remove();
                $("#valeurCritere_" + critere + " > option").removeAttr("selected");
                $("#valeurCritere_" + critere + " option[value='" + idSel + "']").attr('selected', 'selected');
            }
        } else {
            txtSel = $("#valeurCritere_" + critere + " :selected").text();
            idSel = $("#valeurCritere_" + critere).val();
            if (idSel) {
                opt = '<option value = "' + idSel + '" selected>' + txtSel + '</option>';
                $("#origine_" + critere).append(opt);
                $("#valeurCritere_" + critere + " option[value='" + idSel + "']").remove();
                $("#origine_" + critere + " > option").removeAttr("selected");
                $("#origine_" + critere + " option[value='" + idSel + "']").attr('selected', 'selected');
            }
        }
    });
//===========================================================================================================================================================================
//===========================================================================================================================================================================
//========================================================       Changement valeur operateur chm ciblage     =======================================================================
//===========================================================================================================================================================================
//===========================================================================================================================================================================


    $(document.body).on("change", '.operateur_ch_ciblage', function () {
        var operateur = $(this).val(), id = $(this).attr("id").replace('operateur_', ''),
                code = $("#idTypeCompteur_" + id).val(), categorie = code.split(':')[1], multiple = "multiple";
        var mult = $("#valeurCritere_" + id).attr('multiple');
        if (operateur == 'in' || operateur == 'not in') {
            if (typeof mult === typeof undefined) {
                // $("#valeurCritere_" + id).addClass('multiple');
                $("#valeurCritere_" + id).attr('multiple', 'multiple');
            }
            $("#valeurCritere_" + id).multipleSelect();
        } else {
            if (typeof mult !== typeof undefined) {
                var NewSel = ($("#valeurCritere_" + id).clone());
                $("#divValRecherchee_" + id).html(NewSel);
//                        $("#valeurCritere_" + id).removeAttr('multiple').removeClass('multiple').show();
                $("#valeurCritere_" + id).removeAttr('multiple').show();

            }
        }

    });

    $(document.body).on("submit", '#formInfosNumero', function () {
        $("#idAfficherInfosNumero").click();
        return false;
    });
    $(document.body).on("click", '#idAfficherInfosNumero', function () {
        var numero = $('#idNumeroInfos').val();
        if ((numero.length == 8 && numero.substring(0, 1) == '3') || (numero.length == 11 && numero.substring(0, 4) == '2223')) {
            $("#informationClient").html("<center><img src = 'img/loading.gif'></center>");
            $("#informationClient").load('infos/infosClient.php', {stats: numero});
        } else {
            alertDialog("Merci de saisir un numéro correcte", 'warning');
        }
    });
    $(document.body).on("click", '#idAffichageStatsGlobal, #icon_exp_excel', function () {
        var tab_parms = new Object(), send = true;
        var entete = $("#nature_stats_client :selected").text();
        entete += " / " + $("#type_donnee_stats_client :selected").text();
        if ($("#compteurs_stats_client :selected").length == 1)
            entete += " / " + $("#compteurs_stats_client :selected").text();
        else
            entete += " / Multiple ";
        entete += " / " + $("#periodicite_stats_client :selected").text();
        tab_parms["id_button"] = this.id;
        tab_parms["nature"] = $("#nature_stats_client").val();
        tab_parms["type_donnee"] = $("#type_donnee_stats_client").val();
        tab_parms["compteurs"] = $("#compteurs_stats_client").val();
        tab_parms["periodicite"] = $("#periodicite_stats_client").val();
        tab_parms["unite"] = $("#unite_stats_client").val();
        tab_parms["unite_lib"] = $("#unite_stats_client :selected").text();
        tab_parms["entete"] = entete;
        if ($("#idNumeroInfos").length) {
            tab_parms["numero"] = $("#idNumeroInfos").val();
        }

        if (tab_parms["compteurs"] == null) {
            send = false;
        }

        $.each(tab_parms, function (key, value) {
            if ((value == '') || (key == 'numero' && !((value.length == 8 && value.substring(0, 1) == '3') || (value.length == 11 && value.substring(0, 4) == '2223'))))
                send = false;
        });
        if (send) {
            if (tab_parms["id_button"] == "icon_exp_excel") {
                $('#idInputParms').val(JSON.stringify(tab_parms));
                $('#frm_exp_excel').submit();
            } else {
// $("#divStatsGlobal").html("<center><h2>Chargement en cours .......</h2></center>");
                $("#divStatsGlobal").html("<center><img src = 'img/loading.gif'></center>");
                $.ajax({
                    url: "stats/stats.php",
                    async: true,
                    type: 'POST',
                    data: {parms: JSON.stringify(tab_parms)},
                    success: function (retour) {

                        $("#divStatsGlobal").html(retour);
                    },
                    error: function () {
                        alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
                    }
                });
            }
        } else
            alertDialog("Merci de sélectionner les stats voulus", 'warning');
    });
    $(document.body).on("click", '#idDeconnexion', function () {
        $("#dialog-message").html("<center class='alert-box warning'>Voulez vous se déconnecter ?</center>");
        $("#dialog-message").dialog({
            modal: true,
            resizable: true,
            draggable: false,
            width: "400",
            height: "250",
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                $(":button:contains('Non')").focus(); // Set focus to the [Ok] button
            },
            buttons: [{text: "Oui", click: function () {
                        $(this).dialog("close");
                        window.location = "conn/deconnexion.php";
                    }
                },
                {text: "Non", click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });
    });
    $(document.body).on("click", '.reduireDIV', function () {
        var name = 'divContent_' + $(this).attr('name'), imgName = 'img_reduire_' + $(this).attr('name');
        if ($(this).hasClass("reduire")) {
            $("div[name=" + name + "]").slideUp();
            $(this).removeClass("reduire").addClass('agrandire').attr('title', 'Agrandir');
            $("img[name=" + imgName + "]").attr("src", "img/plus.png");
            $("div[name=" + name + "] .critere").addClass('ignored');
        } else {
            $("div[name=" + name + "]").slideDown();
            $(this).removeClass("agrandire").addClass('reduire').attr('title', 'Réduire');
            $("img[name=" + imgName + "]").attr("src", "img/moins.png");
            $("div[name=" + name + "] .critere").removeClass('ignored');
        }
    });
    $(document.body).on("click", '.masquer_det_cible', function () {
        var elt = $(this), idCible = elt.attr("name").replace("cible", "");
        $('#show_cible_' + idCible).remove();
        elt.removeClass('masquer_det_cible');
        elt.addClass('afficher_det_cible');
        elt.val('Afficher');
    });
    $(document.body).on("click", '.afficher_det_cible', function () {
        var elt = $(this), idCible = elt.attr("name").replace("cible", "");
        $('#ligne_cible_' + idCible).after("<tr id='show_cible_" + idCible + "'><td id='show_cible_td_" + idCible + "' colspan=5><center><img src = 'img/loading.gif'></center></td></tr>");
        elt.addClass('masquer_det_cible').removeClass('afficher_det_cible').val('Masquer');
        $.ajax({
            url: "ciblage/showCible.php",
            async: true,
            type: 'POST',
            dataType: 'json',
            data: {idCible: idCible},
            success: function (retour) {
                if (retour.exec == "1") {
                    var val = retour.message;
                    $('#show_cible_' + idCible).html(val);
                }
            },
            error: function () {
                alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
            }
        });
    });

    function getIdGroup() {
        var idG = 0;
        $(".divGroupe").each(function () {
            id = $(this).attr("id").replace('groupe', '');
            if (id > idG)
                idG = parseInt(id);
        });
        return idG + 1;
    }

    function getIdCritere() {
        var idC = 0;
        $(".divCritere").each(function () {
            tab = $(this).attr("id").split('_');
            id = tab[2];
            if (id > idC)
                idC = parseInt(id);
        });
        return idC + 1;
    }
//***********************************************************************************************************
//****************************************** BONUS CAMPAGNE  ************************************************
//***********************************************************************************************************
    $(document.body).on("change", '.ShowSMSBonus', function () {
        var id = $(this).attr('id').replace('idShowSMSBonus', '');
        if ($(this).is(':checked')) {
            if (!$('#divGroupeBonus' + id + ' .divCntBonus').length) {
                $(this).attr('checked', false);
                alertDialog("Vous devez definir un bonus", 'warning');
            } else {
                $('#idSMSBonusAr' + id).slideDown(500);
                $('#idSMSBonusFr' + id).slideDown(500);
                $('#tags_' + id).slideDown(500);
            }
        } else {
            $('#idSMSBonusAr' + id).slideUp(500);
            $('#idSMSBonusFr' + id).slideUp(500);
            $('#tags_' + id).slideUp(500);
        }
    });

    $(document.body).on("change", '#type_declencheur', function () {
        var type = $(this).val(), idG = 1;
        if (type != 'fidelite') {
            idG = getIdGroup();
        }
        $("#div_detail_declencheur").load("campagne/declencheur/declencheur_type.php", {type: type, idGroup: idG});
    });

    $(document.body).on("change", 'input:radio[name="ciblage_campagne_choix"]', function () {
        if ($(this).attr('id') == "idChosirCible")
            $("#idDivCiblabeCampagne").load('campagne/liste_cible.php');
        else if ($(this).attr('id') == "idComposerCible") {
            idG = getIdGroup();
            $("#idDivCiblabeCampagne").load('ciblage/ciblage_corps.php', {enrg: ' disabled', idGroup: idG});
        } else
            $('#idDivCiblabeCampagne').html("<br><span class='alert-box success'>Tous le parc (actif + suspended)</span><br><br>");
    });

    function showCible(idCible) {
        $.ajax({
            url: "ciblage/showCible.php",
            async: true,
            type: 'POST',
            dataType: 'json',
            data: {idCible: idCible},
            success: function (retour) {
                if (retour.exec == "1") {
                    var val = retour.message;
                    $('#divShowCilbeCreationCampagne').html("<table width=100%><tr>" + val + "</tr></table>");
                }
            },
            error: function () {
                alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
            }
        });
    }

    $(document.body).on("change", '#listeCibleCampagne', function () {
        var idCible = $(this).val();
        if (idCible == '')
            $('#divShowCilbeCreationCampagne').html("");
        else
            showCible(idCible);
    });

//***********************************************************************************************************
//****************************************** ENREGISTRER CAMPAGNE  ******************************************
//***********************************************************************************************************

    $(document.body).on("click", '#idEnrgistrerCampagne', function () {
        var btnEnr = $(this);
        btnEnr.hide();
        retour_enr_cmp = '';
        if (!verifOngletGle()) {
            if (retour_enr_cmp == '')
                retour_enr_cmp = 'Merci de faire les corrections !!';
            alertDialog(retour_enr_cmp, 'error');
            $("#b1").click();
            btnEnr.show();
        } else if (!verifOngletCiblage()) {
            if (retour_enr_cmp == '')
                retour_enr_cmp = 'Merci de faire les corrections !!';
            alertDialog(retour_enr_cmp, 'error');
            $("#b2").click();
            btnEnr.show();
        } else if (!verifOngletBonus()) {
            if (retour_enr_cmp == '')
                retour_enr_cmp = 'Merci de faire les corrections !!';
            alertDialog(retour_enr_cmp, 'error');
            $("#b3").click();
            btnEnr.show();
        } else
            fn_enregistrer_campagne();
    });

    function showDetaillCmp(idCmp) {
        $('#ligne_campagne_' + idCmp).after('<tr id = "show_cible_' + idCmp + '"  class="detail-campagne" style="background-color:#ababab;" ><td colspan="7"><center><img src = "img/loading.gif"></center></td></tr>');
        $.ajax({
            url: "campagne/showCampagne.php",
            async: true,
            type: 'POST',
            dataType: 'text',
            data: {idCmp: idCmp},
            success: function (retour) {
                $('#show_cible_' + idCmp).html(retour);
                $('input[name="cmp' + idCmp + '"]').removeClass('afficher_det_cmp');
                $('input[name="cmp' + idCmp + '"]').addClass('masquer_det_cmp');
                $('input[name="cmp' + idCmp + '"]').val('Masquer');
//                        }
            },
            error: function () {
                alertDialog("ERREUR, MERCI DE CONTACTER VOTRE ADMINISTRATEUR", "error");
            }
        });
    }

    $(document.body).on("click", '.afficher_det_cmp', function () {
        var elt = $(this), idCmp = elt.attr("name").replace("cmp", "");
        $('.detail-campagne').each(function () {
            var eltS = $(this), idCmpS = eltS.attr("id").replace("show_cible_", "cmp");
            $('input[name=' + idCmpS + ']').removeClass('masquer_det_cmp').addClass('afficher_det_cmp').val('Afficher');
            eltS.remove();
        });
        showDetaillCmp(idCmp);
    });

    $(document.body).on("click", '.masquer_det_cmp', function () {
        var elt = $(this), idCmp = elt.attr("name").replace("cmp", "");
        $('#show_cible_' + idCmp).remove();
        elt.removeClass('masquer_det_cmp');
        elt.addClass('afficher_det_cmp');
        elt.val('Afficher');
    });

    $(document.body).on("click", '.gererCampagne', function () {
        var btnSup = $(this), name = btnSup.attr("name").split('_');
        var idCmp = name[1], action = name[0];
        switch (action) {
            case 'arreter':
                message = 'Motif d\'arrêt de la campagne';
                break;
            case 'rejeter':
                message = 'Motif de rejet de la campagne';
                break;
            case 'valider':
                message = 'Commentaire validation de la campagne';
                break;
            case 'pause':
                message = 'Motif mise en pause de la campagne';
                break;
            default :
                message = 'Motif';
        }
        var html = message + ' :<br><br><textarea cols ="30" rows ="3" id="motifGererCmp"></textarea>';
        $("#dialog-message").html(html);
        $("#dialog-message").dialog({
            modal: true,
            width: 400,
            height: 300,
            resizable: true,
            draggable: false,
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                $(":button:contains('Annuler')").focus(); // Set focus to the [Ok] button
            },
            buttons: [{text: "Enregistrer", click: function () {
                        var motif = $('#motifGererCmp').val();
                        if (motif != '')
                            $.ajax({
                                url: "campagne/gestionCampagne.php",
                                async: false,
                                type: 'POST',
                                dataType: 'json',
                                data: {idCmp: idCmp, action: action, motif: motif},
                                success: function (retour) {
                                    if (retour.exec == '1') {
                                        $("#dialog-message").dialog("close");
                                        alertDialog(retour.message, 'success');
                                        $('#cssmenu a.active').click();
                                    } else
                                        alertDialog(retour.message, 'error');
                                },
                                error: function () {
                                    alertDialog("ERREUR LORS DE LA RECHERCHE DE LA PAGE", 'error');
                                }
                            });
                        else {
                            $("#motifGererCmp").addClass('champVide').after("<br><span id= 'spanmotifGererCmp' class='alert-box error'>Merci de saisir un motif</span>");
                        }

                    }
                },
                {text: "Annuler", click: function () {
                        $("#show_cible_" + idCmp).removeClass('supprimerLinge');
                        $(this).dialog("close");
                    }
                }
            ]
        });
    });

    $(document.body).on("click", '.supprimer_campagne', function () {
        var btnSup = $(this), idCmp = btnSup.attr("name").replace('supprimer', '');
        $("#dialog-message").html("Voulez vous supprimer la campagne ?");
        $("#show_cible_" + idCmp).addClass('supprimerLinge');
        $("#dialog-message").dialog({
            modal: true,
            resizable: true,
            draggable: false,
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                $(":button:contains('Non')").focus(); // Set focus to the [Ok] button
            },
            buttons: [{text: "Oui", click: function () {
                        $.ajax({
                            url: "campagne/removeCampagne.php",
                            async: false,
                            type: 'POST',
                            dataType: 'json',
                            data: {idCmp: idCmp},
                            success: function (retour) {
                                if (retour.exec == '1') {
                                    $("#ligne_campagne_" + idCmp).remove();
                                    $("#show_cible_" + idCmp).remove();
                                    $("#dialog-message").dialog("close");
                                    alertDialog(retour.message, 'success');
                                } else
                                    alertDialog(retour.message, 'error');
                            },
                            error: function () {
                                alertDialog("ERREUR LORS DE LA RECHERCHE DE LA PAGE", 'error');
                            }
                        });
                    }
                },
                {text: "Non", click: function () {
                        $("#show_cible_" + idCmp).removeClass('supprimerLinge');
                        $(this).dialog("close");
                    }
                }
            ]
        });
    });
    $(document.body).on("click", '.valider_campagne', function () {
        var btnSup = $(this), idCmp = btnSup.attr("name").replace('valider', '');
        var html = 'Commentaire :<br><br><textarea cols ="30" rows ="3" id="motifGererCmp"></textarea>';
        $("#dialog-message").html(html);
//                $("#dialog-message").html("Voulez vous valider la campagne ?");
        $("#dialog-message").dialog({
            modal: true,
            width: 400,
            height: 300,
            resizable: true,
            draggable: false,
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                $(":button:contains('Annuler')").focus(); // Set focus to the [Ok] button
            },
            buttons: [{text: "Enregistrer", click: function () {
                        var motif = $('#motifGererCmp').val();
                        if (motif != '')
                            $.ajax({
                                url: "campagne/validerCampagne.php",
                                async: false,
                                type: 'POST',
                                dataType: 'json',
                                data: {idCmp: idCmp, motif: motif},
                                success: function (retour) {
                                    if (retour.exec == '1') {
                                        $("#ligne_campagne_" + idCmp).remove();
                                        $("#show_cible_" + idCmp).remove();
                                        $("#dialog-message").dialog("close");
                                        alertDialog(retour.message, 'success');
                                    } else
                                        alertDialog(retour.message, 'error');
                                },
                                error: function () {
                                    alertDialog("ERREUR LORS DE LA RECHERCHE DE LA PAGE", "error");
                                }
                            });
                        else {
                            $("#motifGererCmp").addClass('champVide').after("<br><span id= 'spanmotifGererCmp' class='alert-box error'>Merci de saisir un motif</span>");
                        }
                    }
                },
                {text: "Annuler", click: function () {
                        $(this).dialog("close");
                    }
                }
            ]
        });
    });
    $(document.body).on("click", '.leg_log', function () {
        idDiv = $(this).attr('name');
        content = $("#" + idDiv).html();
        $("#dialog-message").html(content);
        $("#dialog-message").dialog({
            modal: true,
            width: "600",
            height: "500",
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
                $(":button:contains('Non')").focus(); // Set focus to the [Ok] button
            },
            buttons: [{text: "Oui", click: function () {
                        $(this).dialog("close");
                    }
                }]
        });
    });

    $(document.body).on("click", '.ch_date', function () {
        var spanL = $(this), periode = spanL.text();
        if ((spanL.hasClass('missing'))) {
            url = 'admin/supervision/missingFiles.php';
            colspan = 12;
        } else {
            url = 'admin/supervision/liste_cdrs.php';
            colspan = 19;
        }
        if (spanL.hasClass('details')) {
            spanL.removeClass('details').addClass('moins');
            $("#ligne_tc_" + periode).after('<tr id="details' + periode + '"><td colspan="' + colspan + '"><br><center><img src = "img/loading.gif"></center></td></tr>');
            $.ajax({
                url: url,
                async: true,
                type: 'POST',
                dataType: 'html',
                data: {periode: periode},
                success: function (retour) {
                    $("#details" + periode).html('<td colspan="' + colspan + '" bgcolor="gray">' + retour + '</td>');
                },
                error: function () {
                    alertDialog("ERREUR LORS DE LA RECHERCHE DE LA PAGE", "error");
                }
            });
        } else {
            $("#details" + periode).remove();
            spanL.addClass('details').removeClass('moins');
        }
    });

    $(document.body).on("click", '.clsFile', function () {
        var jf = $(this).attr('name');
        var url = ($(this).hasClass('missing')) ? "admin/supervision/liste_cdrs_missing.php" : "admin/supervision/liste_cdrs_journee.php";
        $("#dialog-message").html("<center><img src = 'img/loading.gif'></center>");
        $("#dialog-message").load(url, {jf: jf});
        $("#dialog-message").dialog({
            modal: true,
            width: "1250",
            height: "620",
            resizable: true,
            draggable: true,
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide(); // Hide the [x] button
            },
            buttons: [
                {text: "OK", click: function () {
                        $("#dialog-message").html("");
                        $(this).dialog("close");
                    }
                }
            ]
        });
    });

//    $('#cssmenu ul ul li:odd').addClass('odd');
//    $('#cssmenu ul ul li:even').addClass('even');

    function verif_Bonus_glb(type) {
        var result = true;
        if ((type == 'fidelite') && !($('#cntBonus select').length)) {
            retour_enr_cmp = 'Vous devez avoir au moins un bonus';
            result = false;
        }
        $('#cntBonus select').each(function () {
            if ($(this).val() == "") {
                $(this).addClass('champVide');
                result = false;
            }
        });

        $('#cntBonus input:text').each(function () {
            if ($(this).val() == "") {
                $(this).addClass('champVide');
                result = false;
            }
        });

        $('.ShowSMSBonus:checked').each(function () {
            idTxt = $(this).attr('id').replace('idShowSMSBonus', '');
            if ($('#idSMSBonusAr' + idTxt).val().trim() == '') {
                $('#idSMSBonusAr' + idTxt).addClass('champVide');
                result = false;
            }
            if ($('#idSMSBonusFr' + idTxt).val().trim() == '') {
                $('#idSMSBonusFr' + idTxt).addClass('champVide');
                result = false;
            }
        });
        return result;
    }

    function verif_Declencheur_Bonus() {
        var result = true;
        $('#cntGrEvent select').each(function () {
            if ($(this).val() == "") {
                $(this).addClass('champVide');
                result = false;
            }
        });
        if (!result)
            return false;
        $('#cntGrEvent input:text').each(function () {
            if ($(this).val() == "") {
                $(this).addClass('champVide');
                result = false;
            }
        });
        if (!result)
            return false;

        if (!$('#cntGrEvent .divCritere').length) {
            result = false;
            alertDialog('Vous devez avoir au moins un critère de déclenchement de bonus', 'warning');
        }
        if (!result)
            return false;

//                if ($('#cntBonus .divCntBonus').length)
//                    return true;
        result = verif_Bonus_glb('decl');
        if (!result)
            return false;
        var nbrBnsGr = 0;
        $('.divGroupe.dgDeclencheur').each(function () {
            var idG = $(this).attr('id');
            var nbBG = $('#' + idG + ' .divCntBonus').length;
            if (!nbBG)
                result = false;
            else
                nbrBnsGr += nbBG;
        });
//                if (!$('.divCntBonus').length) {
//                    retour_enr_cmp = 'Vous devez avoir au moins un bonus';
//                    result = false;
//                }
        var nbrBnsCmp = $('.divCntBonus').length;
        if (nbrBnsCmp == 0 || (nbrBnsGr == nbrBnsCmp && !result)) {
            retour_enr_cmp = 'Vous devez avoir au moins un bonus par groupe, ou bien un bonus global';
            return false;
        } else
            return true;

//                return result;
    }

    function verifOngletBonus() {
        //Get the type of Bonus
        var tb = $('#type_declencheur').val();
        if (tb == "")
            return true;
        else if (tb == 'fidelite')
            return verif_Bonus_glb('fidelite');
        else
            return verif_Declencheur_Bonus();
    }

    function verifOngletCiblage() {
        var idCible = $('input:radio[name="ciblage_campagne_choix"]:checked').attr('id');
        if (idCible == 'idSansCible')
            return true;
        else if (idCible == 'idChosirCible') {
            if ($("#listeCibleCampagne").val() == '') {
                $("#listeCibleCampagne").addClass('champVide').after("<span id= 'spanlisteCibleCampagne' class='alert-box error'>Choisir l'une des cibles</span>");
                var message = "Merci de choisir une cible !!";
                alertDialog(message, 'warning');
                return false;
            } else
                return true;
        } else {
            var ret = verifCiblage();
            if (!ret.send) {
                var message = ret.message;
                alertDialog(message, 'warning');
                return false;
            } else
                return true;
        }
    }

    function verifCiblage() {
        var retour = new Object();
        retour['send'] = true;
        retour['message'] = '';
        if ($("#cntGrCiblage .critere").length) {
            $("#cntGrCiblage .critere").each(
                    function () {
                        if (!$(this).val()) {
                            $(this).addClass("champVide");
                            retour['send'] = false;
                            retour['message'] = "Merci de remplire tous les champs";
                        }
                    });
            if (retour['send']) {   // vérifier si périodes sont correctes
                var code = '', p_from = '', p_to = '';
                $('#cntGrCiblage .select_for_periode').each(function () {
                    code = $(this).attr('id').replace('idPeriodeFrom_', '').replace('idPeriodeTo_', '');
                    p_from = $("#idPeriodeFrom_" + code).val();
                    p_to = $("#idPeriodeTo_" + code).val();
                    if (p_to < p_from) {
                        retour['send'] = false;
                        if (!$("#idPeriodeFrom_" + code).hasClass('champVide')) {
                            $("#idPeriodeFrom_" + code).addClass('champVide');
                            $("#idPeriodeTo_" + code).addClass('champVide');
                            retour['message'] = "Periode invalide";
                        }
                    }
                });
            }
        } else {
            retour['send'] = false;
            retour['message'] = "Vous devez ajouter au moins un critére";
        }
        return retour;
    }


    function verifOngletGle() {
        var send = true, lnMinSMS = 20;
        if ($("#idNomCampagne").val().length < 10) {
            $("#idNomCampagne").addClass("champVide");
            if (!$("#spanidNomCampagne").length)
                $("#idNomCampagne").after("<span id= 'spanidNomCampagne' class='alert-box error'>Le nom doit contenir au moins 10 caractéres</span>");
            send = false;
        }

        if ($("#idDtFromCampagne").val() != '' && $("#idDtFromCampagne").val().length != 16) {
            $("#idDtFromCampagne").addClass("champVide");
            if (!$("#spanidDtFromCampagne").length)
                $("#idDtFromCampagne").after("<span id= 'spanidDtFromCampagne' class='alert-box error'>Date début incorrecte !!</span>");
            send = false;
        }


        if ($("#idDtToCampagne").val().length != 16) {
            $("#idDtToCampagne").addClass("champVide");
            if (!$("#spanidDtToCampagne").length)
                $("#idDtToCampagne").after("<span id= 'spanidDtToCampagne' class='alert-box error'>Date fin incorrecte !!</span>");
            send = false;
        }

        if ($("#idSmsCampagneAr").hasClass("obligatoire") && $("#idSmsCampagneAr").val().length < lnMinSMS) {
            $("#idSmsCampagneAr").addClass("champVide");
            if (!$("#spanidSmsCampagneAr").length)
                $("#idSmsCampagneAr").after("<br><span id= 'spanidSmsCampagneAr' class='alert-box error'>SMS doit contenir au mois " + lnMinSMS + " caractéres !!</span>");
            send = false;
        }

        if ($("#idSmsCampagneFr").hasClass("obligatoire") && $("#idSmsCampagneFr").val().length < lnMinSMS) {
            $("#idSmsCampagneFr").addClass("champVide");
            if (!$("#spanidSmsCampagneFr").length)
                $("#idSmsCampagneFr").after("<br><span id= 'spanidSmsCampagneFr' class='alert-box error'>SMS doit contenir au mois " + lnMinSMS + " caractéres !!</span>");
            send = false;
        }
        return send;
    }

    function fn_infos_ciblage() {
        var tab_parms = new Object();
        if ($('input:radio[name="ciblage_campagne_choix"]:checked').attr('id') == 'idChosirCible')
            tab_parms["cible_id"] = $("#listeCibleCampagne").val();
        else if ($('input:radio[name="ciblage_campagne_choix"]:checked').attr('id') == 'idSansCible')
            tab_parms["cible_id"] = 0;
        else {
            tab_parms["associationGroupe"] = $("#cntGrCiblage input:checked[name=associationGroupe]").val();
            tab_parms["cibleName"] = $("#idCibleName").val();
            $("#cntGrCiblage input:checked.groupe").each(
                    function () {
                        tab_parms[$(this).attr("name")] = $(this).val();
                    });
            if ($("#cntGrCiblage .critere").length) {
                send = true;
                $("#cntGrCiblage .critere").each(
                        function () {
                            valCr = ($(this).hasClass("champCiblage")) ? $(this).val().split(":")[0] : $(this).val();
                            if ($(this).hasClass("multiple")) {
                                var valCr = "";
                                $("#" + $(this).attr("id") + " >option").each(function () {
                                    if (valCr == "")
                                        valCr = "('" + $(this).val() + "'";
                                    else
                                        valCr += ", '" + $(this).val() + "'";
                                });
                                valCr += ")";
                            }
                            tab_parms[$(this).attr("id")] = valCr;
                        });
            }
        }
        return tab_parms;
    }


    function params_gle() {
        parms_gle['cmp_nom'] = $("#idNomCampagne").val();
        parms_gle['cmp_dt_from'] = $("#idDtFromCampagne").val();
        parms_gle['cmp_dt_fin'] = $("#idDtToCampagne").val();
        //parms_gle['cmp_broadcast'] = $('input:radio[name="broadcast"]:checked').val();
        parms_gle['cmp_broadcast'] = $('#idBroadCastCmp').val();
        parms_gle['cmp_teasingAr'] = $("#idSmsCampagneAr").hasClass('obligatoire') ? $("#idSmsCampagneAr").val() : '';
        parms_gle['cmp_teasingFr'] = $("#idSmsCampagneFr").hasClass('obligatoire') ? $("#idSmsCampagneFr").val() : '';
        parms_gle['cmp_objectif'] = $("#idObjectifCampagne").val();
    }

    function params_budget() {
        $('input.budget').each(function () {
            parms_gle[$(this).attr('name')] = ($(this).val() == '') ? 0 : $(this).val();
        })
    }
    function params_sms_bns_glb() {
        parms_gle['cmp_sms_bonusAr'] = '';
        parms_gle['cmp_sms_bonusFr'] = '';
        if ($("#idShowSMSBonusbnsgeneral").length && $("#idShowSMSBonusbnsgeneral").is(':checked')) {
            parms_gle['cmp_sms_bonusAr'] = $("#idSMSBonusArbnsgeneral").val();
            parms_gle['cmp_sms_bonusFr'] = $("#idSMSBonusFrbnsgeneral").val();
        } else if ($("#idShowSMSBonus1000").length && $("#idShowSMSBonus1000").is(':checked')) {
            parms_gle['cmp_sms_bonusAr'] = $("#idSMSBonusAr1000").val();
            parms_gle['cmp_sms_bonusFr'] = $("#idSMSBonusFr1000").val();
        }
    }
    function fn_infos_generale() {
        parms_gle = new Object();
        params_gle();
        params_sms_bns_glb();
        params_budget();
        return parms_gle;
    }

    function fn_infos_bonus() {
        var parms = new Object();
        parms['type_dcl'] = $("#type_declencheur").val();
        parms['dcl_grp'] = new Object();
        $('#cntGrEvent .dgDeclencheur').each(function () {
            idG = $(this).attr('id'), id = idG.replace('groupe', '');
            parms['dcl_grp'][id] = new Object();
            parms['dcl_grp'][id]['nature'] = $('#idSelectNatureTrafic' + id).val();
            if ($('#idShowSMSBonus' + id).is(':checked')) {
                parms['dcl_grp'][id]['sms_bonusAr'] = $('#idSMSBonusAr' + id).val();
                parms['dcl_grp'][id]['sms_bonusFr'] = $('#idSMSBonusFr' + id).val();
            } else {
                parms['dcl_grp'][id]['sms_bonusAr'] = '';
                parms['dcl_grp'][id]['sms_bonusFr'] = '';
            }
            parms['dcl_grp'][id]['type'] = $('#idSelectTypeDonnee' + id).val();
            parms['dcl_grp'][id]['dcl'] = new Object();
            $('#' + idG + ' .divCritere').each(function () {
                idc = $(this).attr('id').replace('critere', '');
                parms['dcl_grp'][id]['dcl'][idc] = new Object();
                parms['dcl_grp'][id]['dcl'][idc]['code'] = $('#idTypeCompteur' + idc).val().split(':')[0];
                parms['dcl_grp'][id]['dcl'][idc]['type_donnee'] = $('#natureType' + idc).val().split('_')[1];
                parms['dcl_grp'][id]['dcl'][idc]['operateur'] = $('#operateur' + idc).val();
                parms['dcl_grp'][id]['dcl'][idc]['valeur'] = $('#valeurCritere' + idc).val();
                parms['dcl_grp'][id]['dcl'][idc]['unite'] = $('#untieValeur' + idc).val();

            });
            parms['dcl_grp'][id]['bns'] = new Object();
            $('#' + idG + ' .divCntBonus').each(function () {
                idBonus = $(this).attr('id').replace('divCntBonus', '');
                parms['dcl_grp'][id]['bns'][idBonus] = new Object();
                parms['dcl_grp'][id]['bns'][idBonus]['type'] = $('#idTypeBonus' + idBonus).val();
                parms['dcl_grp'][id]['bns'][idBonus]['nature'] = $('#idSelectNatureBonus' + idBonus).val();
                parms['dcl_grp'][id]['bns'][idBonus]['code_bonus'] = $('#idSelectCompteur' + idBonus).val();
                parms['dcl_grp'][id]['bns'][idBonus]['valeur'] = $('#idValeurBonus' + idBonus).val();
                parms['dcl_grp'][id]['bns'][idBonus]['ch_ref'] = '';
                parms['dcl_grp'][id]['bns'][idBonus]['unite'] = $('#idUniteCompteur' + idBonus).val();
            });

        });
        parms['bnsGlb'] = new Object();
        $('#cntBonus .divCntBonus').each(function () {
            id = $(this).attr('id').replace('divCntBonus', '');
            parms['bnsGlb'][id] = new Object();
            parms['bnsGlb'][id]['type'] = $('#idTypeBonus' + id).val();
            parms['bnsGlb'][id]['nature'] = $('#idSelectNatureBonus' + id).val();
            parms['bnsGlb'][id]['code_bonus'] = $('#idSelectCompteur' + id).val();
            parms['bnsGlb'][id]['valeur'] = $('#idValeurBonus' + id).val();
            parms['bnsGlb'][id]['ch_ref'] = '';
            parms['bnsGlb'][id]['unite'] = $('#idUniteCompteur' + id).val();
        });
        return parms;
    }


    function fn_enregistrer_campagne() {
        var parms = new Object();
        parms['glb'] = fn_infos_generale();
        parms['cbl'] = fn_infos_ciblage();
        parms['bns'] = fn_infos_bonus();
        var form = $('#fileUploadForm')[0];
        var data = new FormData(form);
        data.append('cmp', JSON.stringify(parms));
        $("#dialog-message").html("<center><br><br>Enregistrement en cours !!!<br><img src = 'img/loading.gif'></center>");
        alertDialog();
        $.ajax({
            type: "POST",
            async: true,
            dataType: 'json',
            enctype: 'multipart/form-data',
            url: "campagne/enregistrer_campagne.php",
            data: data,
            //data: {data: data, cmp: JSON.stringify(parms)},
            processData: false,
            contentType: false,
            cache: false,
            timeout: 600000,
            success: function (data) {
                $("#dialog-message").dialog("close");
//                        var ret = JSON.parse(data);
                if (data.exec == "1") {
                    alertDialog(data.message, 'success');
                } else {
                    alertDialog(data.message, 'error');
                    $('#idEnrgistrerCampagne').show();
                }
            },
            error: function (e) {
                $("#dialog-message").dialog("close");
                $("#dialog-message").html(data);
                $('#idEnrgistrerCampagne').show();
                alertDialog();
            }
        });
    }




});



function fnDataTable(clm, order, ob, id) {
    cls = typeof id !== 'undefined' ? '#' + id : '.dataTable';
    if (ob == 'profil') {
        search = false;
        blen = false;
        pagin = false;
        info = false;
    } else {
        search = true;
        blen = true;
        pagin = true;
        info = true;
    }
    $(cls).DataTable({
        "pagingType": 'full',
        "searching": search,
        "bLengthChange": blen,
        "paging": pagin,
        "info": info,
        language: {
            "sSearch": "Rechercher : ",
            "lengthMenu": "Lister _MENU_ lignes",
            "info": "Liste de _START_ à _END_ du _TOTAL_ " + ob + "s",
            "infoEmpty": "Aucun " + ob + " trouvé !",
            "infoFiltered": "   (filtrée parmis _MAX_ " + ob + "s)",
            "zeroRecords": "Aucun " + ob,
            "thousands": " ",
            paginate: {
                first: '« Début',
                previous: '‹ Précédent',
                next: 'Suviant ›',
                last: 'Fin »'
            }
        },
        buttons: [
            'excel'
        ],
        "order": [[clm, order]]
    });
}

$.fn.insertAtCaret = function (myValue) {
    return this.each(function () {
        //IE support
        if (document.selection) {
            this.focus();
            sel = document.selection.createRange();
            sel.text = myValue;
            this.focus();
        }
        //MOZILLA / NETSCAPE support
        else if (this.selectionStart || this.selectionStart == '0') {
            var startPos = this.selectionStart;
            var endPos = this.selectionEnd;
            var scrollTop = this.scrollTop;
            this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos, this.value.length);
            this.focus();
            this.selectionStart = startPos + myValue.length;
            this.selectionEnd = startPos + myValue.length;
            this.scrollTop = scrollTop;
        } else {
            this.value += myValue;
            this.focus();
        }
    });
};