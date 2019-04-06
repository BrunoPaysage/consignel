/* Programme javascript pour l'application du moyen de compensation Consignel */
// $(document).ready(function(){ debuter(); });

/* Affichage et activation de la page d'utilisation */
function acceptetransaction(accepteouinon){
  $("#suiviappli").prepend("acceptetransaction("+accepteouinon+") <br>");
  var acceptation = accepteouinon;
  utilisateur = codequiutilise();
  if(utilisateur=="u0"){ $('#confirmationrecherche').attr("class","attente"); $('.menupref .suivant').html(".confirmation"); identification(); };
/* proposition venant de moi */
  if(acceptation == "actualisemoi"){ actualiselaproposition("maproposition"); }; /* fin du actualisemoi */
  if(acceptation == "annulemoi"){ chargemoi('annuleuneproposition'); }; /* fin du annulemoi */
/* proposition venant d'un autre */
  if(acceptation == "oui"){ chargemoi('accepteuneproposition'); }; /* fin du oui */
  if(acceptation == "non"){alert("non");}; /* fin du non */
  if(acceptation == "modifie"){ actualiselaproposition("pasmaproposition"); }; /* fin du modifie */
};

/* Affichage et activation de la page d'utilisation */
function activeutilisation(tableauretour){
  $("#suiviappli").prepend("activeutilisation(tableauretour) <br>");
  /* reçoit le tableau des variables retournées par le serveur tableauretour = $(".retourserveur").html().split(","); */
  var tableaudemarre=tableauretour;
  cachetout(); $(".alerte").html(""); 
  $('#preferences').attr("class","actif");
// ajouter la mise à jour des préférences après un dépassement de temps
changegraphsuivi(tableaudemarre[3],tableaudemarre[4],tableaudemarre[5],tableaudemarre[6]);
  if($('.menupref .suivant').text() == ".utilisation"){ $('.utilisation').show(); $('#inputactivite').focus(); };
  if($('.menupref .suivant').text() == ".confirmation"){ 
    $('.confirmation').show(); $('#confirmationrecherche').attr("class","actif"); $('#confirmationacceptetransaction').attr("class","actif"); 
    confirmationokinputcode(); 
  };
};

/* affiche la proposition dans la page utilisation */
function actualiselaproposition(dequi="mapropostion"){
  $("#suiviappli").prepend("actualiselaproposition(dequi) <br>");
  $("#offrechoisi").show();  $("#demandechoisi").show();
  var dansqui = ""; var dansqui2 ="";
  var codeitemchoisi = ""; var itemchoisi = ""; var quantite = 1; var unite = "h"; var consignel = 0; var argent = 0; var mlc = 0; var environnement = 0; var duree = 0; var social = 0; var foisparan = 0; var dureedevie = 0;
  if (dequi == "mapropostion"){ 
    dansqui = "offreconfirme" ; dansqui2 = "offrechoisi" ;
  }else{ 
    dansqui = "demandeconfirme" ; dansqui2 = "demandechoisi" ;
  };
  var nbdiv=$("#"+dansqui+" div[id^=act]").length; var lesdiv = $("#"+dansqui+" div[id^=act]"); var nomdudiv =""; var nomdudiv2 = "";
  for (i = 0; i < nbdiv; i++) {
    nomdudiv = "#"+$(lesdiv[i]).attr("id"); nomdudiv2= nomdudiv+"preciseact";
    codeitemchoisi = nomdudiv.substring(4); itemchoisi = $(nomdudiv+" .quoi").text(); quantite = $(nomdudiv+" .quantite").text(); unite = $(nomdudiv+" .unite").text(); consignel = $(nomdudiv2+" .consignel").text(); argent = $(nomdudiv2+" .argent").text(); mlc = $(nomdudiv2+" .mlc").text(); environnement = $(nomdudiv2+" .environnement").text(); duree = $(nomdudiv2+" .duree").text(); social = $(nomdudiv2+" .social").text(); foisparan = $(nomdudiv2+" .foisparan").text(); dureedevie = $(nomdudiv2+" .dureedevie").text(); 
    ajoutediv(dansqui2,"act",codeitemchoisi,itemchoisi,quantite,unite,consignel,argent,mlc,environnement,duree,social,foisparan,dureedevie) ;
  };
  if (dequi == "mapropostion"){ 
    dansqui = "demandeconfirme" ; dansqui2 = "demandechoisi" ;
  }else{ 
    dansqui = "offreconfirme" ; dansqui2 = "offrechoisi" ;
  };
  var nbdiv=$("#"+dansqui+" div[id^=act]").length; var lesdiv = $("#"+dansqui+" div[id^=act]"); var nomdudiv =""; var nomdudiv2 = "";
  for (i = 0; i < nbdiv; i++) {
    nomdudiv = "#"+$(lesdiv[i]).attr("id"); nomdudiv2= nomdudiv+"preciseact";
    codeitemchoisi = nomdudiv.substring(4); itemchoisi = $(nomdudiv+" .quoi").text(); quantite = $(nomdudiv+" .quantite").text(); unite = $(nomdudiv+" .unite").text(); consignel = $(nomdudiv2+" .consignel").text(); argent = $(nomdudiv2+" .argent").text(); mlc = $(nomdudiv2+" .mlc").text(); environnement = $(nomdudiv2+" .environnement").text(); duree = $(nomdudiv2+" .duree").text(); social = $(nomdudiv2+" .social").text(); foisparan = $(nomdudiv2+" .foisparan").text(); dureedevie = $(nomdudiv2+" .dureedevie").text(); 
    ajoutediv(dansqui2,"act",codeitemchoisi,itemchoisi,quantite,unite,consignel,argent,mlc,environnement,duree,social,foisparan,dureedevie) ;
  };
  $("#menuprefutilisation").click();
};

/* affiche le détail de la proposition (venant du serveur par demandefichier) */
function affichedetailproposition(noproposition, propositiondequi){
  $("#suiviappli").prepend("affichedetailproposition(noproposition) <br>"); 
  effaceconfirmation();
  var nomproposition = noproposition;
  var dequi = propositiondequi; 
  if (nomproposition == "demandeuneproposition"){
    $("#offreconfirme").html("demande "+$("#mstockdemandeuneproposition").text());
  }else{
    // contenu de la proposition
    var inputdemande = nettoieinputtra($("#confirmationinputcode").val()) ;
    var numproposetra = inputdemande.substring(14);
    var numtra = "tra"+inputdemande
    try {var objson = JSON.parse(nomproposition); }
    catch {return;}; // proposition mal écrite ou inexistante
    // $("#demandeconfirme").append( numtra+"<br>"); // nom de la transaction
    // $("#demandeconfirme").append( objson[numtra][0]+"<br>"); // numéro de l'offre
    var numoff = "off"+objson[numtra][2]+"_"+objson[numtra][0]; // id de l'offre
    // $("#demandeconfirme").append( objson[numoff][0]+"<br>"); // liste des act offerts
    // $("#demandeconfirme").append( objson[numoff]+"<br>"); // valeurs de l'offre
   var ou="#demandemontantsconfirme"; 
   if(dequi=="matransaction"){ou="#offremontantsconfirme";};
    afficheproposition(ou,numoff,objson[numoff],numproposetra);
    var tableauoff = objson[numoff][0].split("act");
    // $("#demandeconfirme").append( tableauoff.length+"<br>"); // nombre d'actes offerts (+1)
    var i; var nomact;
    for (i = 1; i < tableauoff.length; i++) { 
      nomact = "off"+objson[numtra][2]+"_"+"act"+tableauoff[i];
      // $("#demandeconfirme").append( nomact+"<br>"); // nom de l'act dans le json
      var ou="#demandeconfirme"; if(dequi=="matransaction"){ou="#offreconfirme";};
      afficheproposition(ou,tableauoff[i],objson[nomact]);
    };
    var numdem = "dem"+objson[numtra][2]+"_"+objson[numtra][1];
    // $("#demandeconfirme").append( objson[numdem][0]+"<br>"); // liste des actes demandés
    // $("#demandeconfirme").append( objson[numdem]+"<br>"); // valeurs de la demande
   var ou="#offremontantsconfirme"; if(dequi=="matransaction"){ou="#demandemontantsconfirme";};
    afficheproposition(ou,numdem,objson[numdem]);
    var tableaudem = objson[numdem][0].split("act");
    // $("#demandeconfirme").append( tableaudem.length+"<br>"); // nombre d'actes demandés (+1)
    var i;
    for (i = 1; i < tableaudem.length; i++) { 
      nomact = "dem"+objson[numtra][2]+"_"+"act"+tableaudem[i];
      // $("#demandeconfirme").append( nomact+"<br>");
   var ou="#offreconfirme"; if(dequi=="matransaction"){ou="#demandeconfirme";};
      afficheproposition(ou,tableaudem[i],objson[nomact]);
    };
    
  };
};

/* affiche les items de la proposition */
function afficheproposition(ou,id,valeurs,numproposetra){
  $("#suiviappli").prepend("afficheproposition(ou,id,variables) <br>");
  var oulocal=ou; var idlocal =id; var variableslocales = valeurs;
  var numid = idlocal.substring(17);
  var dateid =  idlocal.substring(3,16);
  var actid = idlocal.substring(3);
  var numproposetralocal=numproposetra;
  switch (oulocal) {
    case "#demandemontantsconfirme":
      $("#demandecompensationconfirme .montant").html(variableslocales[3]);
      $("#demandemontantsconfirme .argent").html(variableslocales[4]);
      $("#demandemontantsconfirme .mlc").html(variableslocales[5]);
      $("#demandemontantsconfirme .environnement").html(variableslocales[6]);
      $("#demandemontantsconfirme .duree").html(variableslocales[7]);
      $("#demandemontantsconfirme .social").html(variableslocales[8]);
      $("#demandemontantsconfirme .codedemande").html(numid);
      $("#demandemontantsconfirme .listeact").html(variableslocales[0]);
      $("#idtransactionproposition .codetransaction").html(numproposetralocal);
      $("#idtransactionproposition .codedate").html(dateid);
    break;
    case "#offremontantsconfirme":
      $("#offrecompensationconfirme .montant").html(variableslocales[3]);
      var resumetemp = $(".retourserveur").text();
      var paramgraph = resumetemp.split(",");
      var disponibletemp = Number(paramgraph[3])+ Number(variableslocales[3]);
      changegraphsuivi(disponibletemp,Number(paramgraph[4]),Number(paramgraph[5]),Number(paramgraph[6]));
      $("#offremontantsconfirme .argent").html(variableslocales[4]);
      $("#offremontantsconfirme .mlc").html(variableslocales[5]);
      $("#offremontantsconfirme .environnement").html(variableslocales[6]);
      $("#offremontantsconfirme .duree").html(variableslocales[7]);
      $("#offremontantsconfirme .social").html(variableslocales[8]);
      $("#offremontantsconfirme .codeoffre").html(numid);
      $("#offremontantsconfirme .listeact").html(variableslocales[0]);
      // $("#idtransactionconfirme .codetransaction").html(numid);
      // $("#idtransactionconfirme .codedate").html(dateid);
    break;
    case "#offreconfirme":
      ajoutediv2("offreconfirme","act",actid,variableslocales[0],variableslocales[1],variableslocales[2],variableslocales[3],variableslocales[4],variableslocales[5],variableslocales[6],variableslocales[7],variableslocales[8],1,1);
      $("#confirmationinputcode").focus();
    break;
    case "#demandeconfirme":
      ajoutediv2("demandeconfirme","act",actid,variableslocales[0],variableslocales[1],variableslocales[2],variableslocales[3],variableslocales[4],variableslocales[5],variableslocales[6],variableslocales[7],variableslocales[8],1,1);
      $("#confirmationinputcode").focus();
    break;
  };
};

/* repérage d'un paiement double troc ou d'un simple échange */
function queltypetroc(notransaction){
  $("#suiviappli").prepend("queltypetroc(notransaction) <br>");
  var noact= notransaction;
  var typetroc = "doubletroc";
  var listepaiement = constante("paiements"); 
  if (listepaiement.indexOf("_"+notransaction+"\"")==-1){typetroc = "simpletroc";};
  return typetroc;
}

function aidepropositions(){
  $("#suiviappli").prepend("aidepropositions() <br>");
  alert("aidepropositions à écrire ");
};

/* ajoute un div de proposition */
function ajoutediv(dansqui,prefixe,suffixe,itemchoisi,quantite,unite,consignel,argent,mlc,environnement,duree,social,foisparan,dureedevie) {
  /* dansqui = div offrechoisi ou div demandechoisi, prefixe = act, suffixe= code du nom de l'item, itemchoisi = description en clair, quantité et unité si besoin */
  $("#suiviappli").prepend("ajoutediv("+dansqui+" "+prefixe+" "+suffixe+" "+itemchoisi+" "+quantite+" "+unite+" "+consignel+" "+argent+" "+mlc+" "+environnement+" "+duree+" "+social+" "+foisparan+" "+dureedevie+") <br>");
  var dansquilocal=dansqui; prefixelocal=prefixe; var suffixelocal=suffixe; var choisi=itemchoisi;
  var typetroc = queltypetroc(suffixe);
  var quantitelocale=quantite;
  var unitelocale=unite;
  var consignellocal=consignel;
  var argentlocal=argent;
  var mlclocale=mlc;
  var environnementlocal=environnement;
  var dureelocale=duree;
  var sociallocal=social;
  var foisparanlocal=foisparan;
  var dureedevielocale=dureedevie;
  var iddudiv=demandeid(prefixelocal,suffixelocal); /* act , code de l'item etc */
  var codedetailactivite = "";
  var nouveaudiv=" <div id='"+iddudiv+"' class='"+typetroc+"'><span class='quoi' onclick='modifie("+iddudiv+",\"quoi\")' >"+choisi+"</span> <span class='quantite' onclick='modifie("+iddudiv+",\"quantite\")' >"+quantitelocale+"</span>&nbsp;<span class='unite' onclick='modifie("+iddudiv+",\"unite\")' >"+unitelocale+"</span> <span class='supprime' onclick='supprimediv("+iddudiv+")' > <small>&#128465;</small> </span> <span id='"+iddudiv+"preciseactivite' class='preciseactivite' "+"onclick=\" changeClass("+iddudiv+"preciseact,\'voit\',\'cache\') \" > &#9064; </span> <span id=\""+iddudiv+"preciseact\" class='cache'><span class='codedunom'>"+iddudiv+"</span><span class='codedetailactivite'>"+codedetailactivite+"</span> <span class='consignel'>"+consignellocal+"</span>&nbsp;&#8634; <br><span class='argent' onclick='modifie("+iddudiv+",\"argent\")'>"+argentlocal+"</span>&nbsp;$ <span class='mlc' onclick='modifie("+iddudiv+",\"mlc\")'>"+mlclocale+"</span>&nbsp;mlc <span class='environnement' onclick='modifie("+iddudiv+",\"environnement\")'>"+environnementlocal+"</span>&nbsp;*E <span class='duree' onclick='modifie("+iddudiv+",\"duree\")'>"+dureelocale+"</span>&nbsp;h <span class='social' onclick='modifie("+iddudiv+",\"social\")'>"+sociallocal+"</span>&nbsp;*S <span class='foisparan' onclick='modifie("+iddudiv+",\"foisparan\")'>"+foisparanlocal+"</span>&nbsp;/a <span class='dureedevie' onclick='modifie("+iddudiv+",\"dureedevie\")'>"+dureedevielocale+"</span>&nbsp;a </span></div>";
  $("#"+dansquilocal).prepend(nouveaudiv); 
  $("#"+iddudiv+" .quoi").html($("#"+iddudiv+" .quoi").text()); /* élimine les balises html du input*/
  modifie(iddudiv,"quantite");
};

function ajoutediv2(dansqui,prefixe,suffixe,itemchoisi,quantite,unite,consignel,argent,mlc,environnement,duree,social,foisparan,dureedevie) {
  /* dansqui = div offrechoisi ou div demandechoisi, prefixe = act, suffixe= code du nom de l'item, itemchoisi = description en clair, quantité et unité si besoin */
  $("#suiviappli").prepend("ajoutediv2("+dansqui+" "+prefixe+" "+suffixe+" "+itemchoisi+" "+quantite+" "+unite+" "+consignel+" "+argent+" "+mlc+" "+environnement+" "+duree+" "+social+" "+foisparan+" "+dureedevie+") <br>");
  var dansquilocal=dansqui; prefixelocal=prefixe; var suffixelocal=suffixe; var choisi=itemchoisi;
  var typetroc = queltypetroc(suffixe);
  var quantitelocale=quantite;
  var unitelocale=unite;
  var consignellocal=consignel;
  var argentlocal=argent;
  var mlclocale=mlc;
  var environnementlocal=environnement;
  var dureelocale=duree;
  var sociallocal=social;
  var foisparanlocal=foisparan;
  var dureedevielocale=dureedevie;
  var iddudiv=demandeid(prefixelocal,suffixelocal); /* act , code de l'item etc */
  var codedetailactivite = "";
  var nouveaudiv=" <div id='"+iddudiv+"' class='"+typetroc+"'><span class='quoi' onclick='modifie2("+iddudiv+",\"quoi\")' >"+choisi+"</span> <span class='quantite' >"+quantitelocale+"</span>&nbsp;<span class='unite' >"+unitelocale+"</span> <span id='"+iddudiv+"preciseactivite' class='preciseactivite' "+"onclick=\" changeClass("+iddudiv+"preciseact,\'voit\',\'cache\') \" > &#9064; </span> <span id=\""+iddudiv+"preciseact\" class='cache'><span class='codedunom'>"+iddudiv+"</span><span class='codedetailactivite'>"+codedetailactivite+"</span> <span class='consignel'>"+consignellocal+"</span>&nbsp;&#8634; <br><span class='argent'>"+argentlocal+"</span>&nbsp;$ <span class='mlc'>"+mlclocale+"</span>&nbsp;mlc <span class='environnement'>"+environnementlocal+"</span>&nbsp;*E <span class='duree'>"+dureelocale+"</span>&nbsp;h <span class='social'>"+sociallocal+"</span>&nbsp;*S <span class='foisparan'>"+foisparanlocal+"</span>&nbsp;/a <span class='dureedevie'>"+dureedevielocale+"</span>&nbsp;a </span></div>";
  $("#"+dansquilocal).prepend(nouveaudiv); 
  $("#"+iddudiv+" .quoi").html($("#"+iddudiv+" .quoi").text()); /* élimine les balises html du input*/
};

/* arrête session avec délai */
function arretesession() {
  $("#suiviappli").prepend("arretesession() <br>");
  var elem = document.getElementById("arretesessionsuivi"); 
  var suiviprogres = 100;
  var demandearret = $(".arretesession .statut").html();
  var id = setInterval(frame, 50);
  function frame() {
    if (demandearret=="continue"){
      clearInterval(id);
      $(".arretesession").hide(); $("#menuprefutilisation").click();
    }else{
      if (suiviprogres == 0 || demandearret == 0) {
        clearInterval(id);
        effacelentete();
      } else {
        suiviprogres--; 
        demandearret=$(".arretesession .statut").html();
        $(".arretesession .statut").html(suiviprogres);
        elem.style.width = suiviprogres + '%'; 
      };
    };
  };
}; 

/* force arrête session immédiat */
function arretesessionclic(continuearrete) {
  $("#suiviappli").prepend("arretesessionclic() <br>");
  if(continuearrete=="continue"){
    $(".arretesession .statut").html("continue"); 
  }else{
    $(".arretesession .statut").html(0); 
  };
};

/* change le fichier des préférences dans localstorage avec préférences affichées */
function autoriselocalstorage(){
  var storagedisponible=testestorage(); /* vérification disponibilité navigateur retour oui/non */
  if (storagedisponible == "oui") {
    $("#suiviappli").prepend("autoriselocalstorage() <br>");
    var codeutilisateur = codequiutilise();
    var codeprefstorageutilisateur = "consignel"+codeutilisateur;
    var variable1=$("#localstoragepublic").prop('checked'); 
    var variable2=$("#localstoragemoi").prop('checked'); 
    var variable4="["+variable1+","+variable2+"]" ;
    variable4 = encryptepourlocalstorage(variable4);
// codage de variable4 ?
    localStorage.setItem( codeprefstorageutilisateur,variable4 ); 
    verifiepreflocalstorage();
  }else{
    $("#suiviappli").prepend("localstorage non supporté par ce navigateur <br>"); 
    $(".localstoragepublic").html("localstorage indisponible sur ce navigateur");
    $(".localstoragemoi").html("localstorage indisponible sur ce navigateur");
    $("#localstoragepublic")[0].checked = false;
    $("#localstoragemoi")[0].checked = false;
  };
}; 

/* passe le code qr au vert et engage la proposition sur le serveur */
function autorisationqr(){
  $("#suiviappli").prepend("autorisationqr() <br>");
  changeClass(validqr,'qr2','qr');
  if($('#validqr').attr("class")=="qr2"){ /* transaction autorisée */
    var utilisateur = codequiutilise();
    if(utilisateur=="u0"){$('#validqr').attr("class","qr1"); identification();};
    chargemoi('mespropositions');
  }else{ /* transaction non autorisée */
  }; /* fin de transaction autorisée ou pas */
};

/* cache les différentes pages du programme */
function cachetout(){
  $("#suiviappli").prepend("cachetout() <br>");
  $(".inscription").hide(); $(".arretesession").hide(); $(".utilisation").hide(); $(".confirmation").hide(); $(".preferences").hide(); 
};

/* graphique de suivi du compte */
function changegraphsuivi(disponible,unjour,dispomini,dispomaxi){ 
  $("#suiviappli").prepend("changegraphsuivi(disponible,unjour,dispomini,dispomaxi) <br>");
  var dispo=disponible;
  var dispo1jour=unjour;
  var dispo14jours=14*unjour;
  var dispo1an=365*unjour;
  if (dispo1an > constante("maximumcompte") ){ 
    var dispomax = constante("maximumcompte") ; 
    dispo=disponible   ;
    dispo14jours=14*dispomax/365 ;
    dispo1jour=dispomax/365 ;
    dispo1an=dispomax ;
  };
  var dispomin=0; 
  var pxmin=0; var pxmax=300; 
  var pxgauche=pxmin+(1-(dispo1an-dispo)/(dispo1an-dispomin))*(pxmax-pxmin); 
  var dispopx = ""+(Math.round(pxgauche)-1)+"px"; 
  $(".suivi .barredisponible").css({"margin-left":dispopx});
  var dispotext=""; if (dispo<dispo14jours){dispotext="&nbsp;<sup>"+dispo+"</sup>"}; 
  $(".suivi .barredisponible").html(dispotext);

  dispomin=dispomini; dispo=disponible; dispo1an=dispomaxi;
  pxmin=70-11+1; pxmax=230-11+1; 
  pxgauche=pxmin+(1-(dispo1an-dispo)/(dispo1an-dispomin))*(pxmax-pxmin); 
  if (pxgauche<-26){pxgauche=-26;}; if (pxgauche>306){pxgauche=306;}; 
  dispopx = ""+(Math.round(pxgauche)-1)+"px"; 
  $(".suivi .rond").css({"margin-left":dispopx});

  var pxetendue=pxmax-pxmin;
  /* var pxquartier=Math.round(pxetendue/6); */
  var pxquartier=Math.round(pxetendue/8);
  var pxcol=Math.round((pxgauche-pxmin)/pxquartier);

  /*var pxcoleval=".suivi"+pxcol; if (pxcol<0 || pxcol>6){pxcoleval=".couleurmin";}; var milieu=(disponible-(dispomaxi-dispomini)/2)-dispomini; var tolerance= dispo1jour/2; if ((milieu > -tolerance) && (milieu < tolerance)){pxcoleval=".couleurcentre";};couleurexterieur*/
  var pxcoleval=".couleurcentre";
  if (pxcol<0 || pxcol>8){pxcoleval=".couleurmin";};
  if (pxcol==0 || pxcol==8){pxcoleval=".suivi0";};
  if (pxcol==1 || pxcol==7){pxcoleval=".suivi1";};
  if (pxcol==2 || pxcol==6){pxcoleval=".suivi2";};
  if (pxcol==3 || pxcol==5){pxcoleval=".suivi3";};
  if (pxcol==4){pxcoleval=".couleurcentre";};
  if (pxgauche==-26){pxcoleval=".couleurexterieur";}; if (pxgauche==306){pxcoleval=".couleurexterieur";}; 
  var dispocol=$(pxcoleval).css("background-color") ;
  $(".suivi .rond").css({"background-color":dispocol});
};

/* met à jour le graphique de suivi du compte pour le test dans la page de préférences */
function changesuivi(){
  $("#suiviappli").prepend("changesuivi() <br>");
  var testd=$('#disponible').val(); var testj=$('#unjour').val(); var testmi=$('#dispomin').val(); var testma=$('#dispomax').val(); changegraphsuivi(testd,testj,testmi,testma); 
};

/* usage de l'offre choisie changeaideinputactivite("ø") "ȫ" "ǜ" etc */
function changeaideinputactivite(option){
  $("#suiviappli").prepend("changeaideinputactivite("+option+") <br>");
  var variablelocale=$("#changeaideinputactivite").html();
  var optionlocale=option;
  if (optionlocale== undefined ){
    if (variablelocale=="ø"){
      if(($("#stockecherchequoimini").text()).length > 5){
        optionlocale=("ȫ");
      }else{
        if(($("#mstockquoi").text()).length > 5){
          optionlocale=("ǜ");
        }else{
          optionlocale=("ø");
        };
      };
    };
    if (variablelocale=="ȫ"){
      if(($("#mstockquoi").text()).length > 5){
        optionlocale=("ǜ");
      }else{
        optionlocale=("ø");
      };
    };
    if (variablelocale=="ǜ"){optionlocale=("ø");};
    $("#suiviappli").prepend("transforme en "+optionlocale+"<br>");
  };
  $("#changeaideinputactivite").html(optionlocale);
//  if (optionlocale=="ø"){$("#inputactivite").autocomplete("option","source",[]);};
  if (optionlocale=="ø"){  var listeduinput = JSON.parse($("#listestockevaleursrefmini").text());
  $("#inputactivite").autocomplete("option","source",listeduinput); };
  if (optionlocale=="ȫ"){changedeliste("#inputactivite", "#stockecherchequoimini");};
  if (optionlocale=="ǜ"){changedeliste("#inputactivite", "#mstockquoi");};
};

/* changement de style par un clic */
function changeClass(elem, className1,className2){ 
elem.className = (elem.className == className1)?className2:className1; 
}

/* change la liste des autocomplete */
function changedeliste(quelinput, queldiv){
  var codeutilisateur = codequiutilise();
  $("#suiviappli").prepend(codeutilisateur+" "+"changedeliste( "+quelinput+", "+queldiv+") <br>");
  if(queldiv=="#stockecherchequoimini"){quelinput="#inputactivite"};
  var listedudiv = $(queldiv).text();
  if(listedudiv != "..."){
// session expirée renvoi utilisateur inconnu dans le div
if(listedudiv.substring(0,4)==" , 0"){listedudiv="..."; $(queldiv).html("...");};
};

// $(".alerte").html(queldiv);
if(listedudiv.length > 5){ /*div contenant plus que ... */
  try {var listeduinput = JSON.parse(decryptediv(listedudiv));}
  catch{var listeduinput = JSON.parse($("#stockevaleursrefmini").text()); alert("Le fichier du div "+queldiv+" est mal chargé"); $("#changeaideinputactivite").html("ø");};
  $(quelinput).autocomplete("option","source",listeduinput);
  if(queldiv[1]=="s"){$("#changeaideinputactivite").html("ȫ");};
  if(queldiv[1]=="m"){$("#changeaideinputactivite").html("ǜ");};
}else{
  $("#changeaideinputactivite").html("ø");
};
}; 

/* change les id d'un div de proposition */
function changelesid(numid){
  $("#suiviappli").prepend("changelesid("+numid+") <br>");
  var ancienid=numid.substring(0,numid.lastIndexOf("-"));
  var ancienunite=numid.substring(numid.lastIndexOf("-")+1,numid.length);
  /* récupère les variables dansqui,prefixe,suffixe,quoi */
  var quoi=$("#"+ancienid+" .quoi").html();
  var quantite=$("#"+ancienid+" .quantite").html();
  var unite=$("#"+ancienid+" .unite").html();
  var codedunom=$("#"+ancienid+" .codedunom").html();
  var prefixecode=codedunom.substring(0,3);
  var consignel=$("#"+ancienid+" .consignel").html();
  var argent=$("#"+ancienid+" .argent").html();
  var mlc=$("#"+ancienid+" .mlc").html();
  var environnement=$("#"+ancienid+" .environnement").html();
  var duree=$("#"+ancienid+" .duree").html();
  var social=$("#"+ancienid+" .social").html();
  var foisparan=$("#"+ancienid+" .foisparan").html();
  var dureedevie=$("#"+ancienid+" .dureedevie").html();
  var idparent=$("#"+ancienid).parent().attr("id");
  /* nouvelles variables dansqui,prefixe,suffixe,quoi */
  var codechoisi=codeact(quoi,unite);

  /* supprime l'ancien div */
  supprimediv("#"+ancienid); 
  /* vérification si des valeurs sont connues */
  var tabref=refdevaleur("e"+codechoisi);
  if (!tabref){ 
    /* inconnu dans les références *//* crée le nouveau div ("pas reconnu"); */
    /*si c,est le nom qu'on change utiliser les valeurs précédentes sinon */
    if(ancienunite==unite){
      ajoutediv(idparent,"act",codechoisi,quoi,quantite,unite,consignel,argent,mlc,environnement,duree,social,foisparan,dureedevie);
    }else{
      var codeunite="u"+codelenom(unite);/* cherche les valeurs de l'unité (codeunite); */
      tabref=refdevaleur(codeunite);
      if (!tabref){
        alert("Modifiez les valeurs unitaires, ce sont les valeurs pour une heure !");
        tabref=[ "h", 1, "h", -16.5, -15, 0, -28.2, 1, 0.75, 1, 1]; /* u104 changer pour choisir une valeur proche */
      };/* prend les valeurs pour une heure si unité inconnue */
      var nouvelid=demandeid("act",codechoisi);
      ajoutediv(idparent,"act",codechoisi,quoi,quantite,unite,tabref[3],tabref[4],tabref[5],tabref[6],tabref[7],tabref[8],tabref[9],tabref[10]);
    };
  }else{ 
    /* existe dans les références */
    /* crée le nouveau div avec les valeurs de référence ("reconnu"); */
    var nouvelid=demandeid("act",codechoisi);
    ajoutediv(idparent,"act",codechoisi,quoi,quantite,tabref[2],tabref[3],tabref[4],tabref[5],tabref[6],tabref[7],tabref[8],tabref[9],tabref[10]);
  };
  $("#"+nouvelid+"preciseactivite").click();
  /* changement offre demande si modification dans l'autre colonne */
  if(idparent=="offrechoisi"){inverseoffredemande('offre');}else{inverseoffredemande('demande');};
}; 

/* fonction changelesvaleurs lorsque l'unité est changée*/
function changelesvaleurs(numid){
  $("#suiviappli").prepend("changelesvaleurs("+numid+") <br>");
  var ancienid=numid;
  /* récupère les variables dansqui,prefixe,suffixe,quoi */
  var unite=$("#"+ancienid+" .unite").html();
  /* table des correspondances */
  /* correction des valeurs */
  var consignel=$("#"+ancienid+" .consignel").html();
  var argent=$("#"+ancienid+" .argent").html();
  var mlc=$("#"+ancienid+" .mlc").html();
  var environnement=$("#"+ancienid+" .environnement").html();
  var duree=$("#"+ancienid+" .duree").html();
  var social=$("#"+ancienid+" .social").html();
  var foisparan=$("#"+ancienid+" .foisparan").html();
  var dureedevie=$("#"+ancienid+" .dureedevie").html();
  var idparent=$("#"+ancienid).parent().attr("id");

}; /* fin de fonction changelesvaleurs */

/* fonction changelesvaleurs lorsque l'unité est changée*/
function changemodedemo(modeutilisation){
  $("#suiviappli").prepend("changemodedemo("+modeutilisation+") <br>");
  var modeutilisationlocal = modeutilisation ;
  var modecoche = $("#modedemo").prop("checked");
  if (modecoche === true){
  /* mode démo */
  $(".modedemocomment").html("Utilisez les menus");
  $(".retourserveur").html("u001 ,0,0,750,75,500,1000,,");
  utilisateurinconnu(); miseajourgraphique();
  }else{
  /* mode utilisateur identifié */
  $(".modedemocomment").html(" désactivé. Il faut s'identifier");
  $(".retourserveur").html(" , 0 , Inconnu , Inconnu , Inconnu , utilisateur inconnu");
  changegraphsuivi(182.5,1,0,365);
  $("#formulaireaccesutilisateur").focus();
 
  };
}; 

/* chargement des listes publiques pour les input */
function charge(nomdonnees){
  $("#suiviappli").prepend("charge("+nomdonnees+") ... ");
  $(".statut"+nomdonnees).html("...");
  var dansdiv="#stocke"+nomdonnees;
  var dansinput="#input"+nomdonnees;
  var fichierserveur=constante("localite")+nomdonnees+".json";
  var storageoui = $("#localstoragepublic").prop("checked");
  var storagedisponible=testestorage(); 
  var nomfichierlocal="consignel"+nomdonnees;
  var fichierlocaljson;
  var classcouleur = "";
  if(storagedisponible == "oui"){
    if(storageoui==0){
      /* disponible mais pas autorisé */
      localStorage.removeItem( nomfichierlocal );
      fichierlocaljson = undefined;
    }else{
      /* dispinible et autorisé */
      fichierlocaljson = decryptelocalstorage(localStorage.getItem( nomfichierlocal ));
      if(fichierlocaljson.length<5){fichierlocaljson = undefined;};
    };
  }else{ 
    /* pas de stockage disponible */
    fichierlocaljson = undefined;
  };
  if(!fichierlocaljson){/* rien dans localstorage */
    $("#suiviappli").prepend("ah ! depuis le serveur ");
    $.get(fichierserveur,
      function(responseTxt, statusTxt, xhr){
        if(statusTxt == "success") { 
          $("#suiviappli").prepend(" fichier "+nomdonnees+" arrivé depuis le serveur <br>");
          // responseTxt = decryptetransfert(responseTxt);
          $responseTxtcrypte = encryptepourdiv(responseTxt);
          $(dansdiv).text($responseTxtcrypte);
          $('.fichierspourtous .statut'+nomdonnees).html("<i class='eval4'> - "+nomdonnees+" chargé depuis le serveur" + " -</i>"); 
          $('.fichierspourtous .statut2'+nomdonnees).html("<i class='eval4"+classcouleur+"'> </i>") ; 
          changedeliste(dansinput, dansdiv); 
          if(storageoui==1 && storagedisponible == "oui"){ /* met dans localstorage */
            $responseTxtcrypte = encryptepourlocalstorage(responseTxt);
            localStorage.setItem(nomfichierlocal, $responseTxtcrypte); 
            $('.fichierspourtous .statut'+nomdonnees).html("<i class='eval3'> - "+nomdonnees+" chargé dans le stockage local" + " -</i>"); 
            $('.fichierspourtous .statut2'+nomdonnees).html("<i class='eval3"+classcouleur+"'> </i>") ; 
          }; /* fin de met dans localstorage */
        }; 
        if(statusTxt == "error") { 
          $('.fichierspourtous .statut'+nomdonnees).html("<i class='eval2'> - aide "+nomdonnees+" indisponible " + xhr.status + ": " + xhr.statusText+" -</i>"); 
          $('.fichierspourtous .statut2'+nomdonnees).html("<i class='eval2"+classcouleur+"'> </i>") ; 
        }; 
      } /* fin de la fontion de retour */
      ); /* fin du load */
  }; /* fin du pas dans localstorage */
  if(fichierlocaljson){
    /* c'est dans localstorage transfert dans le div */
    $("#suiviappli").prepend("fichier chargé depuis localstorage <br>");
    $(dansdiv).text(encryptepourdiv(fichierlocaljson));
    $('.fichierspourtous .statut'+nomdonnees).html("<i class='eval3'> - "+nomdonnees+" chargé depuis le stockage local" + " -</i>");
    $('.fichierspourtous .statut2'+nomdonnees).html("<i class='eval3"+classcouleur+"'> </i>") ; 
    changedeliste(dansinput, dansdiv);
  };/* fin du transfert dans le div depuis localstorage */
};/* fin de la fonction charge */

/* charge le input activité minimum */
function chargevaleursrefmini(){
var chargevaleursrefmini01 = $("#stockevaleursrefmini").text();
var chargevaleursrefmini01 = encryptepourdiv(chargevaleursrefmini01);
  $("#stockevaleursref").html(chargevaleursrefmini01);
  changeaideinputactivite("ȫ");
};

/* vide le localstorage et les div fichierspersonnels*/
function chargelesfichierspersonnels(){
  $("#suiviappli").prepend("chargelesfichierspersonnels() <br>");
  $(".fichierspersonnels .chargemoi").click();
};

/* vide le localstorage et les div fichierspourtous */
function chargelesfichierspourtous(){
  $("#suiviappli").prepend("chargelesfichierspourtous() <br>");
  $(".fichierspourtous .charge").click();
};

function chargemoitout(nomdonnees){
  $("#suiviappli").prepend("chargemoitout("+nomdonnees+") <br>");
  if (typeof nomdonnees=="string") { var tableau=[nomdonnees]; } else { var tableau=nomdonnees; };
  for (x in tableau) {
    chargemoi(tableau[x]);
  }; /* fin du for x */
};/* fin de la fonction chargemoitout */

/* chargement des fichiers de données personnels */
function chargemoi(nomdonnees){
  $("#suiviappli").prepend("chargemoi("+nomdonnees+") ... ");
  $(".statut"+nomdonnees).html("...");
  var dansdiv="#mstock"+nomdonnees;
  var dansspansuivi=".fichierspersonnels .statut"+nomdonnees;
  var dansspansuivi2=".fichierspersonnels .statut2"+nomdonnees;
  var storageoui = $("#localstoragemoi").prop("checked");
  var storagedisponible=testestorage(); 
  var codeutilisateur = codequiutilise();
  var nomfichierlocal=sansespace(codeutilisateur+nomdonnees);
  if(storagedisponible == "oui"){
    if(storageoui==0){
      /* disponible mais pas autorisé */
      localStorage.removeItem( nomfichierlocal );
      fichierlocaljson = undefined;
      /* var fichierlocaljson = decryptelocalstorage(localStorage.getItem( nomfichierlocal )); */
    }else{ 
      /* disponible et autorisé */
      var envoi = "non"; /* par défaut demande le fichier au lieu de l'envoyer */
      if (nomdonnees == "mespropositions" ){envoi = "oui";};
      if (nomdonnees == "demandeuneproposition" ){envoi = "oui";};
      if (nomdonnees == "accepteuneproposition" ){envoi = "oui";};
      if (nomdonnees == "annuleuneproposition" ){envoi = "oui";};
      if(envoi=="oui"){
        /* envoi de données */
        fichierlocaljson = undefined;
      }else{
        /*demande le fichier */
        fichierlocaljson = decryptelocalstorage(localStorage.getItem( nomfichierlocal ));
      };
    };
  }else{ 
    /* pas de stockage disponible */
    fichierlocaljson = undefined;
  };
  if(!fichierlocaljson){
    /* rien dans localstorage */
    $("#suiviappli").prepend("depuis le serveur <br>");
    demandefichier(dansdiv,nomdonnees,dansspansuivi,nomfichierlocal,dansspansuivi2);
  }; /* fin du pas dans localstorage */
  if(fichierlocaljson){
    if(fichierlocaljson=="[]"){
      /* il n'y a rien dans localstorage */
      $(dansdiv).text(fichierlocaljson);
      $("#suiviappli").prepend(nomdonnees+" est vide dans localstorage <br>");
      $(dansspansuivi).html("<i class='eval2'> - "+nomdonnees+" est vide dans stockage local" + " -</i>");
      $(dansspansuivi2).html("<i class='eval2'> </i>");
      $("#suiviappli").prepend("depuis le serveur <br>");
      demandefichier(dansdiv,nomdonnees,dansspansuivi,nomfichierlocal,dansspansuivi2);
      if(nomdonnees=="quoi"){ changedeliste("#inputactivite", dansdiv); };
    }else{
      /* c'est dans localstorage transfert dans lediv */
      $(dansdiv).html(encryptepourdiv(fichierlocaljson));
      $("#suiviappli").prepend(nomdonnees+" chargé depuis localstorage <br>");
      $(dansspansuivi).html("<i class='eval3'> - "+nomdonnees+" chargé depuis le stockage local" + " -</i>");
      $(dansspansuivi2).html("<i class='eval3'> </i>");
      if(nomdonnees=="quoi"){ changedeliste("#inputactivite", dansdiv); };
    };
  };/* fin du transfert dans le div depuis localstorage */
}; /* fin de la fonction chargemoi */

/* charge tous les fichiers publics */
function chargetout(nomdonnees){
  if (typeof nomdonnees=="string") { var tableau=[nomdonnees]; } else { var tableau=nomdonnees; };
  for (x in tableau) {
    $("#suiviappli").prepend("chargetout("+tableau[x]+") <br>");
    charge(tableau[x]);
  }; /* fin du for x */
};

/* chiffreladate en format 201809021-0723 longueur fixe*/
function chiffreladate(ladate){ 
  var d = ladate;
  $("#suiviappli").prepend("chiffreladate("+ladate+") <br>");
  var chiffreladate=""+d.getFullYear();
  var mois=d.getMonth()+1; if(mois<10){chiffreladate=chiffreladate+"0"+mois;}else{chiffreladate=chiffreladate+mois;};
  var jour=d.getDate(); if(jour<10){chiffreladate=chiffreladate+"0"+jour;}else{chiffreladate=chiffreladate+jour;};
  var heure=d.getHours(); if(heure<10){chiffreladate=chiffreladate+"_0"+heure;}else{chiffreladate=chiffreladate+"_"+heure;};
  var minutes=d.getMinutes(); if(minutes<10){chiffreladate=chiffreladate+"0"+minutes;}else{chiffreladate=chiffreladate+minutes;};
  return chiffreladate ;
}; /* fin de chiffreladate */

/* usage de l'offre choisie */
function choixactivite(itemchoisi){
  $("#suiviappli").prepend("choixactivite("+itemchoisi+") <br>");
  var choisi=nettoieinput(itemchoisi);
  var codechoisi=codelenom(itemchoisi);
  if(!itemchoisi){choisi=nettoieinput($("#inputactivite").val());};
  var etatoffredemande=$("#inverseoffredemande").text();
  var tabref=refdevaleur("e"+codechoisi);
  var justeunite=0;
  if(!tabref){var tabref=refdevaleur("u"+codechoisi); justeunite=1;};
  if (!tabref){ 
    /* inconnu dans les références */
    if(etatoffredemande==" - "){dansdiv="offrechoisi";}else{dansdiv="demandechoisi"};
    codechoisi=codeact(choisi,"h");
    ajoutediv(dansdiv,"act",codechoisi,choisi,1,"h",-33,-15,-15,-56.4,1,1,1,1) ;
  }else{ 
    /* existe dans les références */
    var dansdiv="offrechoisi";
    if(etatoffredemande==" - "){dansdiv="offrechoisi";}else{dansdiv="demandechoisi"};
    choisi=choisisansunite(choisi,tabref[1],tabref[2]);
    if (justeunite==1){choisi="..."};ajoutediv(dansdiv,"act",codechoisi,choisi,tabref[1],tabref[2],tabref[3],tabref[4],tabref[5],tabref[6],tabref[7],tabref[8],tabref[9],tabref[10]) ;
  };
  /* dansqui,prefixe,codeitemchoisi,itemchoisi,quantite,unite,consignel,argent,mlc,environnement,duree,social,foisparan,dureedevie */
};

/* fonction supprime la référence à l'unité dans le nom */
function choisisansunite(lenomchoisi,laquantite,lunite){
  $("#suiviappli").prepend("choisisansunite("+lenomchoisi+","+laquantite+","+lunite+") <br>");
  var mesure=laquantite+lunite;
  var lenomsansunite=lenomchoisi.substring(0,lenomchoisi.lastIndexOf(mesure)-1);
  return lenomsansunite;
};

/* click menu incription */
function clicmenuinscription(){
cachetout(); $('#suiviappli').prepend('clic menu ----- inscription choix arrêt session<br>'); $('.arretesession').show(); $('.suivi .statut').html('100'); arretesession(); 
};

/* click menu utilisation */
function clicmenuutilisation(){
var utilisateur = codequiutilise(); if (utilisateur == "u0 "){effacelentete();};
cachetout(); $('.utilisation').show(); $('#suiviappli').prepend('clic menu ----- utilisation <br>'); $('#inputactivite').focus();
};

/* click menu confirmatin */
function clicmenuconfirmation(){
var utilisateur = codequiutilise(); if (utilisateur == "u0 "){effacelentete();};
cachetout(); $('.confirmation').show(); $('#suiviappli').prepend('clic menu ----- confirmation <br>'); $('#confirmationinputcode').focus(); 
};

/* click menu préférences */
function clicmenupreferences(){
var utilisateur = codequiutilise(); if (utilisateur == "u0 "){effacelentete();};
cachetout(); $('.preferences').show(); $('#suiviappli').prepend('clic menu ----- preferences <br>');  
};

function clicpageweb(){
window.open(constante("app")+"index.html"); return false;
};
/* code l'activité nom et unité */

/* click sur graphique suivi DD */
function clicsuividd(elementclic=""){
var demande = elementclic; 
$("#suiviappli").prepend("clic graphiquesuiviDD "+elementclic+" <br>");
var tableauretour = tableauretourquiutilise();
if (demande == "suivibarre"){ alert(tableauretour[3]+" ↺ disponibles"); };
if (demande == "rond"){ alert(tableauretour[4]+" ↺ minimum récent\n"+tableauretour[5]+" ↺ autorisés par jour\n"+tableauretour[6]+" ↺ maximum récent"); };
};

function codeact(lenom,lunite){
  $("#suiviappli").prepend("codeact("+lenom+","+lunite+") <br>");
  var lenomact=nettoieinput(lenom)+" 1"+lunite;
  var lecodeact=codelenom(lenomact);
  return lecodeact;
};

/* fonction code l'activité */
function codedetailtransaction(){
  $("#suiviappli").prepend("codedetailtransaction() <br>");
};

/* code le nom */
function codelenom(variable){
  $("#suiviappli").prepend("codelenom(variable) <br>");
  var variablelocale=variable;
  var totalvariable=0;
  for (x in variablelocale) { 
    totalvariable += variablelocale.charCodeAt(x) * (x+1) ; 
  };
  return totalvariable;
};

/* Fait lla liste des identifiant des activité soit dans la demande soit de l'offre */
function codeoffredemande(){
  var laliste=""; 
  var consignel = $("#offrecompensation .montant").text(); // valeurs du DA↺ moi
  var argent = $("#offremontants .argent").text();
  var mlc = $("#offremontants .mlc").text();
  var environnement = $("#offremontants .environnement").text();
  var duree = $("#offremontants .duree").text();
  var social = $("#offremontants .social").text();
  var listeact = nettoieinput($("#offremontants .listeact").text());

  var codedate=$("#idtransaction .codedate").text();

  laliste="[\""+listeact+"\",1,\"u\","+consignel+","+argent+","+mlc+","+environnement+","+duree+","+social+"]";
  var codelaliste=codelenom(laliste);
  laliste="\"off"+codedate+"_"+codelaliste+"\" : "+laliste+" "

  $("#monoffre").html(laliste);
  $("#offremontants .codeoffre").html(codelaliste); 
  var laliste="";
  var consignel = $("#demandecompensation .montant").text();
  var argent = $("#demandemontants .argent").text();
  var mlc = $("#demandemontants .mlc").text();
  var environnement = $("#demandemontants .environnement").text();
  var duree = $("#demandemontants .duree").text();
  var social = $("#demandemontants .social").text();
  var listeact = nettoieinput($("#demandemontants .listeact").text());
  laliste="[\""+listeact+"\",1,\"u\","+consignel+","+argent+","+mlc+","+environnement+","+duree+","+social+"]";
  var codelaliste=codelenom(laliste);
  laliste="\"dem"+codedate+"_"+codelaliste+"\" : "+laliste+" "
  $("#mademande").html(laliste);
  $("#demandemontants .codedemande").html(codelaliste); 
}; /* fin de codeoffredemande */

/* code la transaction */
function codetransaction(){
  $("#suiviappli").prepend("codetransaction() <br>");
  var loffre=$("#monoffre").text();
  var codeoffre=$("#offremontants .codeoffre").text();
  var lademande=$("#mademande").text();
  var codedemande=$("#demandemontants .codedemande").text();
  var ladate=$("#idtransaction .codedate").text();
  var demandeaqui=codelenom(nettoieinput($("#demandeaqui").val()));
  var dureeexpire=($("#dureeexpire").val());
  var latransaction= "[\""+codeoffre+"\",\""+codedemande+"\",\""+ladate+"\",\""+demandeaqui+"\",\""+dureeexpire+"\"]";
  var chaineretour = $(".retourserveur").text();
  var codelatransaction=codelenom(latransaction+chaineretour);
  $("#suiviappli").prepend("codetransaction("+latransaction+" --> "+codelatransaction+") <br>");
  $("#idtransaction .codetransaction").html(codelatransaction);
  var matransaction="\"tra"+ladate+"_"+codelatransaction+"\" : "+latransaction+",<br>"+loffre+",<br>"+lademande+"";
  $("#matransaction").html(matransaction);
};

/* retourne le code utilisateur actif */
function codequiutilise(){
  var quiutilise="u0"; var estidentifie =""
  var tableauretour = $(".retourserveur").text().split(",");
  if(tableauretour[0]=="..."){
  quiutilise="u0"; estidentifie="Non identifié";
  }else{
  quiutilise=tableauretour[0]; estidentifie="Utilisateur identifié";
  };
  $("#suiviappli").prepend("codequiutilise() ... "+estidentifie +"<br>");
  return quiutilise;
}; /* fin codequiutilise */

function confirmationokinputcode(){
  $("#suiviappli").prepend("confirmationokinputcode() <br>");
  var entree=nettoieinputtra($('#confirmationinputcode').val()); 
  if (entree == "" ){verifieurlpropose(); entree=nettoieinputtra($('#confirmationinputcode').val()); $('#confirmationinputcode').focus();};
  utilisateur = codequiutilise();
  if(utilisateur=="u0"){ $('#confirmationrecherche').attr("class","attente"); $('.menupref .suivant').html(".confirmation"); identification(); };
  $("#confirmationinputcode").css('color', '');
  effacedemandeproposition();
  chargemoi('demandeuneproposition'); 
};

function confirmationchangeaide(){
  $("#suiviappli").prepend("confirmationchangeaide() <br>");
  alert("confirmationchangeaide à écrire ");
};

/* au chargement de la page initialise l'application */
function debuter(){
/* masque le champs secret pour l'inscription */
$(".secret").hide();
$("#confirmationacceptetransaction .matransaction").hide();

/* connection des listes triables */
$("#suiviappli").prepend("function() .sortable<br>");
$( "#poubelle" ).sortable({ connectWith: ".listedeschoix", receive: function(event,ui){suivisortable(ui.item.html(),ui.sender.attr('id'),this.id);} }).disableSelection(); 
$( "#poubelle2" ).sortable({ connectWith: ".listedeschoix", receive: function(event,ui){suivisortable(ui.item.html(),ui.sender.attr('id'),this.id);} }).disableSelection(); 
$( "#choixenattente" ).sortable({ connectWith: ".listedeschoix", receive: function(event,ui){suivisortable(ui.item.html(),ui.sender.attr('id'),this.id);} }).disableSelection();
$( "#offrechoisi" ).sortable({ connectWith: ".listedeschoix", receive: function(event,ui){suivisortable(ui.item.html(),ui.sender.attr('id'),this.id);} }).disableSelection();
$( "#demandechoisi" ).sortable({ connectWith: ".listedeschoix", receive: function(event,ui){suivisortable(ui.item.html(),ui.sender.attr('id'),this.id);} }).disableSelection();

$("#suiviappli").prepend("document().ready<br>");
chargevaleursrefmini();
$('.utilisation').show(); $('#inputactivite').focus();
if (valeururl("var1").length > 15){cachetout(); $(".confirmation").show(); verifieurlpropose(); confirmationokinputcode();}; 


$("#suiviappli").prepend("function() .autocomplete<br>");
/* autocomplete le input avec fichier json listequoimini, listefaire, listequoi, listepourqui, listeparqui */
$( function() { 
var listequoimini = JSON.parse($("#listestockevaleursrefmini").text());
$( "#inputactivite" ).autocomplete({ source: listequoimini, select: function (event, ui) { choixactivite(ui.item.label);}, }); }); 
var listefaire = [];
$( "#inputcherchefaire" ).autocomplete({ source: listefaire, select: function (event, ui) { $("#inputcherchefaire").val(ui.item.label); proposechoix();}, }); 
var listequoi = [];
$( "#inputcherchequoi" ).autocomplete({ source: listequoi, select: function (event, ui) { $("#inputcherchequoi").val(ui.item.label); proposechoix();}, }); 
listepourqui = [];
$( "#inputcherchepourqui" ).autocomplete({ source: listepourqui, select: function (event, ui) { $("#inputcherchepourqui").val(ui.item.label); proposechoix();}, }); 
var listeparqui = [];
$( "#inputchercheparqui" ).autocomplete({ source: listeparqui, select: function (event, ui) { $("#inputchercheparqui").val(ui.item.label); proposechoix();}, }); 
var listeconfirmation = [];
$( "#confirmationinputcode" ).autocomplete({ source: listeconfirmation, select: function (event, ui) { $("#confirmationinputcode").val(ui.item.label); proposechoix();}, }); 

/* ajout des onclick sur le html menupref */
$("h2.localisation").click(function() { clicpageweb();  });
$("#menuprefinscription").click(function() { clicmenuinscription(); });
$("#menuprefutilisation").click(function() { clicmenuutilisation(); });
$("#menuprefconfirmation").click(function() { clicmenuconfirmation(); });
$("#menuprefpreferences").click(function() { clicmenupreferences(); });


/* ajout des onclick sur le html suivi indicateur de développement durable */
$(".suivicompte .rond").click(function() { clicsuividd("rond"); });
$(".suivicompte .barredisponible").click(function() { clicsuividd("suivibarre"); });

/* ajout des onchange sur le html incription et arretesession*/
$("#formulaireaccesutilisateur").change(function() { valideutilisateur(nettoieinput($("#formulaireaccesutilisateur").val())); });
$("#formulaireaccespass").change(function() { valideutilisateur(nettoieinput($("#formulaireaccespass").val())); });
$("#formulairearretcontinue").click(function() { arretesessionclic("continue"); });
$("#formulairearretarrete").click(function() { arretesessionclic("arrete"); });
$("#modedemo").change(function() { changemodedemo(); });
/* boutons pour les testeurs */
$(".inscriptiontesteurs span").click(function() { var text = $( this ).text(); $("#formulaireaccesutilisateur").val(text); valideutilisateur(nettoieinput($("#formulaireaccesutilisateur").val())); });
$(".inscriptiontesteurssecrets span").click(function() { var text = $( this ).text(); $("#formulaireaccespass").val(text); valideutilisateur(nettoieinput($("#formulaireaccespass").val())); });

/* ajout des onclick et comportement input sur le html recherche */
$("#rechercheoptionvoitcache").click(function() { changeClass(rechercheoption,'voit','cache'); });
$("#inputcherchefaire").keypress(function(){ if (event.keyCode==13){ proposechoix(); $("#inputcherchequoi").focus(); }; }); 
$("#inputcherchequoi").keypress(function(){ if (event.keyCode==13){ proposechoix(); $("#inputcherchepourqui").focus(); }; }); 
$("#inputcherchepourqui").keypress(function(){ if (event.keyCode==13){ proposechoix(); $("#inputchercheparqui").focus(); }; }); 
$("#inputchercheparqui").keypress(function(){ if (event.keyCode==13){ proposechoix(); $("#inputactivite").focus(); }; }); 
$("#changeaideinputactivite").click(function() { changeaideinputactivite(); });
$("#inverseoffredemande").click(function() { inverseoffredemande(); });
$("#inputactivite").keypress(function(){ if (event.keyCode==13){ okinputactivite(); }; }); 
$("#rechercheokinputactivite").click(function() { okinputactivite(); });

/* ajout des onclick et comportement input sur le html confirmation */
$("#changeaideinputconfirmation").click(function() { changeaideinputconfirmation(); });
$("#aidepropositions").click(function() { aidepropositions(); });
$("#confirmationinputcode").change(function(){ confirmationokinputcode(); }); 
$("#confirmationokinputcode").click(function() { confirmationokinputcode(); });
$("#acceptetransactionactualiser").click(function() { acceptetransaction("actualisemoi"); });
$("#acceptetransactionannuler").click(function() { acceptetransaction("annulemoi"); });
$("#acceptetransactionoui").click(function() { acceptetransaction("oui"); });
$("#acceptetransactionmodifie").click(function() { acceptetransaction("modifie"); });
$("#acceptetransactionnon").click(function() { acceptetransaction("non"); });


/* ajout des onclick pour inverser offre et demande */
$("#utilisationchoisioffre").click(function() { inverseoffredemande("offre"); });
$("#utilisationchoisidemande").click(function() { inverseoffredemande("demande"); });

/* ajout des onclick pour demande à qui */
$("#demandeaqui").focus(function() { supprimeautorisationqr(); });
$("#demandeaqui").blur(function() { validedemandeaqui(); });

/* ajout des onclick du DA&#8634; */
$("#offrecompensationdetails").click(function() { changeClass(offremontants,'voit','cache'); });
$("#offrecompensationconfirmedetails").click(function() { changeClass(offremontantsconfirme,'voit','cache'); });
$("#demandecompensationdetails").click(function() { changeClass(demandemontants,'voit','cache'); });
$("#demandecompensationconfirmedetails").click(function() { changeClass(demandemontantsconfirme,'voit','cache'); });

/* ajout des onclick preferences */
$(".testindicateurdd input").change(function() { changesuivi(); });
$("#dureeexpire").change(function() { validdureeexpire(); });
$("#fluxconsignel").change(function() { validfluxconsignel(); });
$("#localstoragepublic").change(function() { autoriselocalstorage(); });
$("#localstoragemoi").change(function() { autoriselocalstorage(); });
$("#fichierspourtouspoubelle").click(function() { videlocalstorage(["cherchequoimini","cherchefaire","cherchequoi","chercheparqui","cherchepourqui","valeursref"]); videlediv(".stockedansdiv"); videautocomplete(); chargevaleursrefmini(); });
$("#fichierspourtouscharge").click(function() { chargetout(["cherchequoimini","cherchefaire","cherchequoi","chercheparqui","cherchepourqui","valeursref"]); });
$("#chargecherchequoimini").click(function() { charge("cherchequoimini"); });
$("#chargecherchefaire").click(function() { charge("cherchefaire"); });
$("#chargecherchequoi").click(function() { charge("cherchequoi"); });
$("#chargecherchepourqui").click(function() { charge("cherchepourqui"); });
$("#chargechercheparqui").click(function() { charge("chercheparqui"); });
$("#chargevaleursref").click(function() { charge("valeursref"); });

$("#fichierspersonnelspoubelle").click(function() { videlocalstorageperso(["resume","quoi","mesvaleursref","mespropositions","demandeuneproposition"]); videlediv(".mstockdansdiv"); videautocomplete(); chargevaleursrefmini(); });
$("#fichierspersonnelschargemoi").click(function() { chargemoitout(["resume","quoi","mesvaleursref","mespropositions","demandeuneproposition"]); });
$("#chargemoiresume").click(function() { chargemoi("resume"); });
$("#chargemoiquoi").click(function() { chargemoi("quoi"); });
$("#chargemoimesvaleursref").click(function() { chargemoi("mesvaleursref"); });
$("#chargemoimespropositions").click(function() { chargemoi("mespropositions"); });
$("#chargemoidemandeuneproposition").click(function() { chargemoi("demandeuneproposition"); });

/* ajout des onclick developpement et suivi */
$("#developpementetsuivi").click(function() { changeClass(stockage,'voit','cache'); });

/* ajout des onclick validation qr */
$("#validqr").click(function() { autorisationqr(); });

/* 
$("#xx").click(function() { xx(xx); });
$("#xx").click(function() { xx(xx); });
$("#xx").click(function() { xx(xx); });
$("#xx").click(function() { xx(xx); });
$("#xx").click(function() { xx(xx); });
$("#xx").click(function() { xx(xx); });
 */

}; /* fin de debuter */

/* decrypte les fichiers locaux */
function decryptediv(contenucrypte){
  $("#suiviappli").prepend("decryptediv(contenucrypte) <br>");
  var contenulocalcrypte = contenucrypte;
  var contenuenclair = decryptelocalstorage(contenulocalcrypte);
  return contenuenclair;
};

/* decrypte les fichiers locaux */
function decryptelocalstorage(contenucrypte){
  $("#suiviappli").prepend("decryptelocalstorage(contenucrypte) <br>");
  if(!contenucrypte){
    var decodelocal = "[]";
  }else{
    var decodelocal = contenucrypte;
  };
  var clef1= nettoieinput($("#formulaireaccesutilisateur").val()).length;
  var clef2= codelenom(nettoieinput($("#formulaireaccespass").val())) % 1000;
  var clefexterne=clef1+clef2; 
  /* doit dépendre d'un input utilisateur ou d'un retour serveur
  traitement selon votre méthode
  pas de codage pendant le développement du programme juste un test */
  var remplaceo = new RegExp('ᒠ', 'g'); decodelocal = decodelocal.replace(remplaceo, 'o'); 
  var remplaceO = new RegExp('ᑘ', 'g'); decodelocal = decodelocal.replace(remplaceO, 'O'); 
  var remplacer = new RegExp('ᑡ', 'g'); decodelocal = decodelocal.replace(remplacer, 'r'); 
  var remplaceR = new RegExp('ᑤ', 'g'); decodelocal = decodelocal.replace(remplaceR, 'R'); 
  var remplacea = new RegExp('ᑥ', 'g'); decodelocal = decodelocal.replace(remplacea, 'a'); 
  var remplaceA = new RegExp('ᑞ', 'g'); decodelocal = decodelocal.replace(remplaceA, 'A'); 
  var remplacee = new RegExp('ᑯ', 'g'); decodelocal = decodelocal.replace(remplacee, 'e'); 
  var remplaceE = new RegExp('ᑝ', 'g'); decodelocal = decodelocal.replace(remplaceE, 'E'); 
  var remplace1 = new RegExp('ᑒ', 'g'); decodelocal = decodelocal.replace(remplace1, '1'); 
  var contenuenclair=decodelocal;
  /* fin de traitement selon votre méthode */
  return contenuenclair;
};

/* decrypte les fichiers provenant du transfert depuis le serveur */
function decryptetransfert(contenucrypte){
  $("#suiviappli").prepend("decryptetransfert(contenucrypte) <br>");
  if(!contenucrypte){
    var decryptetransfertlocal = "[]";
  }else{
    var decryptetransfertlocal = contenucrypte;
  };
  var tableauretour= $(".retourserveur").html().split(",");
  var codesession=tableauretour[1]; 
  /* traitement selon la méthode correllée entre utilisateur et serveur */
  /* pas de codage pendant le développement du programme */
  var remplacecode = new RegExp('(encryptage serveur )', 'g');
  var contenuenclair=decryptetransfertlocal.replace(remplacecode, '');
  /* fin de traitement selon votre méthode */
  return contenuenclair;
};

/* fournit un id différent de ceux qui existent */
function demandeid(prefixe,suffixe) {
  $("#suiviappli").prepend("demandeid(prefixe,suffixe) <br>");
  var i; var nouvelleid= "";
  for (i = 0; i < 1000; i++) {
    /* début de recherche du premier id inutilisé */
    if(i<10){var nouveaunumtext="00"+i};
    if(i>9&&i<100){var nouveaunumtext="0"+i};
    if(i>99){var nouveaunumtext=i};
    nouvelleid=prefixe+nouveaunumtext+suffixe;
    if ($("#"+nouvelleid).length==0) {/* test si le nouvel id existe */
      i=1000;
    }else{
    }; /* fin du test si le nouvel id existe */
  }; /* fin de recherche du premier id inutilisé */
  return (nouvelleid) ;
}; 

/* demande ou envoi de fichier au serveur */
function demandefichier(queldiv,nomdonnees,quelspansuivi,quelfichierlocal,quelspansuivi2){
  $("#suiviappli").prepend("demandefichier("+queldiv+", "+nomdonnees+", "+quelspansuivi+", "+quelfichierlocal+", "+quelspansuivi2+" ) ... ");
  var retourdansdiv = queldiv;
  var demandefich = nomdonnees;
  var dansspansuivi = quelspansuivi;
  var dansspansuivi2 = quelspansuivi2;
  var nomfichierlocal = quelfichierlocal;
  var nomutil2= nettoieinput($("#formulaireaccesutilisateur").val());
  var nomutil3= nettoieinput($("#formulaireaccespass").val());
  var tableauretour= $(".retourserveur").html().split(",");
  if(tableauretour.length==1 || nomutil3=="" || tableauretour[1]==0){
    $("#suiviappli").prepend("Pas de code de session fichier "+nomdonnees+" non demandé au serveur <br>");/* si le serveur n'a pas encore renvoyé de code de session */
    $(".appentete .nomutilisateur").html("... mode démo ... utilisateur inconnu");
    var quiutilise = codequiutilise();
    return; /* on ne demande pas les fichiers pour un utilisateur non identifié */
  }else{
    $("#suiviappli").prepend("encodage de la demande de fichier "+nomdonnees+" pour le serveur <br>");
    /* le code de session existe ("codedesession "+tableauretour[1]);*/
    var nomcode = codelenom((codelenom(nomutil2)*tableauretour[1])+""); /* chiffre le nom d'utilisateur avec le code session */
    var nomcode2 = codelenom(codelenom(nomutil3)*tableauretour[1]+""); /* chiffre le mot de passe avec le code session */
    var nomcode3 = codelenom(demandefich)*tableauretour[1]; /* chiffre la demande de fichier avec le code de session */
    var nomcode4 = codelenom(nomutil2+nomutil3); /* chiffre le code d'acces local */
  };
  
  var var4="";
  if((nomdonnees=="demandeuneproposition") || (nomdonnees=="accepteuneproposition") || (nomdonnees=="annuleuneproposition")){
    var nodemande = nettoieinputtra($("#confirmationinputcode").val()) ;
    if (nodemande.length <= 14){
      $("#confirmationinputcode").css('color', 'red');
      $("#confirmationinputcode").focus();
      return;/* pas de demande à faire */
    }else{
      var4 = nodemande;
      var4 = encryptepourtransfert(var4);
      var4="&var4="+var4;
    };
  };
  var var5="";
  if(nomdonnees=="mespropositions"){
    if ($("#matransaction").text()=="..."){
      /* pas de transaction à transférer */
    }else{
      var5="{ "+$("#matransaction").text()+","+$("#mesact").text() +" } ";
      var5 = encryptepourtransfert(var5);
      var5="&var5="+var5
    };
  };

  var demandeauserveur = "var1=" + nomcode + "&var2=" + nomcode2 + "&var3=" + nomcode3 +var4+var5 ; 
  $("#suiviappli").prepend("script php demandeauserveur envoyé au serveur <br>");
  $.get(constante("php"), demandeauserveur , function(responseTxt, statusTxt, xhr){
    if(statusTxt == "success") {
      $("#suiviappli").prepend("fichier "+nomdonnees+" arrivé depuis le serveur<br>");
      responseTxt = decryptetransfert(responseTxt);
      var testebr = responseTxt.indexOf("<br>"); 
      var testretour = responseTxt.substring(0,4);
      var contenuretour =  responseTxt.substring(7);
      menudetailproposition("pasmatransaction"); 
      $("#acceptetransactionstatut").html(testretour);
      switch (testretour) {
        case "PEAA":
        alert("Présentez le code qr"); $('#validqr').attr("class","qr3"); // en attente scan par client
        // reçoit nouveau résumé de compte et change le graphique
        var nouveauresume = responseTxt.substring(7);
        $(".retourserveur").html( Number(tableauretour[0])+","+Number(tableauretour[1])+","+Number(tableauretour[2])+","+nouveauresume+",,");
        // changegraphsuivi(disponible,unjour,dispomini,dispomaxi);
        var paramgraph = nouveauresume.split(","); 
        changegraphsuivi(paramgraph[0],paramgraph[1],paramgraph[2],paramgraph[3]);
        break;
        case "PDEN":
        alert("Présentez le code qr"); //ne rien faire proposition déjà enregistrée
        break;
        case "TACC":
        $("#acceptetransactionstatut").html("Transaction acceptée");
        menudetailproposition("pasmatransactionfermee"); affichedetailproposition("","pasmatransaction"); 
        $(".retourserveur").html( Number(tableauretour[0])+","+Number(tableauretour[1])+","+Number(tableauretour[2])+","+contenuretour+",,");
        var paramgraph = contenuretour.split(","); 
        changegraphsuivi(paramgraph[0],paramgraph[1],paramgraph[2],paramgraph[3]);
        break; 
        case "ERDP":
        $('#confirmationinputcode').focus(); break; // ("Manque le code de la transaction");  
        case "NULL":
        alert("NULL fichier inconnu "+demandefich);
        break;
        case "<?ph":
        $('.alerte').html("<br><i class='eval2'>Vérification d'utilisateur indisponible sur le serveur</i><br>");
        /* suite à écrire pour utiliser en local sans vérification serveur de l'utilisateur */
        break;
        case " , 0":
        alert("session trop longue");
        supprimeautorisationqr(); $('#validqr').attr("class","qr1");
        $(".appentete .alerte").html("retour fichier, 0 , session trop longue");
        $('#preferences').attr("class","attente"); identification();
        break;
        case "DTMR":
        $("#acceptetransactionstatut").html("Cette transaction n'existe pas");
        responseTxt = responseTxt.substring(4); affichedetailproposition(responseTxt);
        alert("Cette transaction n'existe pas"); break;
        case "DTAO":
        $("#acceptetransactionstatut").html("Transaction disponible");
        responseTxt = responseTxt.substring(7);
        menudetailproposition("pasmatransaction"); affichedetailproposition(responseTxt);
        break; 
        case "PACT":
        $("#acceptetransactionstatut").html("Ma proposition est encore active");
        menudetailproposition("matransaction"); affichedetailproposition(contenuretour,"matransaction");
        break; 
        case "ADAC":
        $("#acceptetransactionstatut").html("J'ai déja accepté cette proposition");
        menudetailproposition("pasmatransactionfermee"); affichedetailproposition(contenuretour,""); 
        break; 
        case "PACC":
        responseTxt = responseTxt.substring(7);
        $("#acceptetransactionstatut").html("Ma proposition a déjà été acceptée");
        menudetailproposition("matransactionfermee"); affichedetailproposition(responseTxt,"matransaction"); 
        break; 
        case "DABR":
        $("#acceptetransactionstatut").html("Ma proposition a été annulée");
        menudetailproposition("matransactionfermee"); affichedetailproposition("","matransaction"); 
        $(".retourserveur").html( Number(tableauretour[0])+","+Number(tableauretour[1])+","+Number(tableauretour[2])+","+contenuretour+",,");
        var paramgraph = contenuretour.split(","); 
        changegraphsuivi(paramgraph[0],paramgraph[1],paramgraph[2],paramgraph[3]);
        break; 
        case "DTMC":
        responseTxt = responseTxt.substring(4); affichedetailproposition(responseTxt);
        alert("Non enregistré - Manque de ↺onsignel pour faire la transaction"); break; 
        case "DTCE":
        responseTxt = responseTxt.substring(4); affichedetailproposition(responseTxt);
        alert("Non enregistré dépense de ↺onsignel supérieure à 7 jours"); break; 
        case "DTNO":
        responseTxt = responseTxt.substring(4); affichedetailproposition(responseTxt);
        alert("Non enregistré transaction non autorisée erreur calcul ↺onsignel"); break; 
        case "DTRT":
        responseTxt = responseTxt.substring(4); affichedetailproposition(responseTxt);
        alert("Non enregistré transaction non autorisée genre réécrire l'histoire"); break; 
        case "DTNC":
        responseTxt = responseTxt.substring(4); propositionrefusee(responseTxt);
        alert("Proposition non conforme. Relancer la page et reformulez la proposition"); break; 
        case "DTIN":
        responseTxt = responseTxt.substring(4); propositionrefusee(responseTxt);
        alert("Proposition interdite. Spéculation, etc."); break; 
        case "DTAP":
        responseTxt = responseTxt.substring(7); affichedetailproposition(responseTxt);
        $("#acceptetransactionstatut").html("Il y a plusieurs propositions appelées ainsi");
        alert("Il y a plusieurs proposition avec le même identifiant"); break; 
        case "TDAC":
        $("#acceptetransactionstatut").html("Ancienne version du logiciel j'ai déjà accepté la transaction");
        propositionrefusee(contenuretour);
        break; 
        case "TREF":
        $("#acceptetransactionstatut").html("Transaction refusée"); propositionrefusee(contenuretour);
        break; 
        case "TRIN":
        $("#acceptetransactionstatut").html("Transaction inconnue"); propositionrefusee(contenuretour);
        break; 
        case "PEXP":
        $("#acceptetransactionstatut").html("Ma proposition est expirée");
        menudetailproposition("matransactionfermee"); affichedetailproposition(responseTxt,"matransaction"); 
        break; 
        case "AEXP":
        $("#acceptetransactionstatut").html("Cette proposition est expirée");
        menudetailproposition("pasmatransactionfermee"); affichedetailproposition(contenuretour,""); 
        break; 
        case "TNDI":
        $("#acceptetransactionstatut").html("Proposition non disponible"); propositionrefusee(contenuretour);
        break; 
        case "PANN":
        $("#acceptetransactionstatut").html("Ma transaction est annulée");
        menudetailproposition("matransactionfermee"); affichedetailproposition(contenuretour,"matransaction");  
        break; 
        case "0000":
        responseTxt = responseTxt.substring(4);
        alert("Travail en cours sur le php "+responseTxt); break; 
        case "TEST":
        responseTxt = responseTxt.substring(4);
        alert("Problème php "+responseTxt); break; 
        case "DTBR":
        $("#acceptetransactionstatut").html("Demande de transaction bien reçue");
        responseTxt = responseTxt.substring(7);
        $("#confirmationacceptetransaction").attr("class","actif"); affichedetailproposition(responseTxt);
        // "Demande de transaction bien reçue");   
        break;
        default :
        /* Fichier demandé au serveur et chargé */
        $(retourdansdiv).html(encryptepourdiv(responseTxt));
        $(dansspansuivi).html("<i class='eval4'> - "+demandefich+" chargé depuis le serveur" + " -</i>"); 
        $(dansspansuivi2).html("<i class='eval4'>&nbsp;</i>"); 
        if(responseTxt.length ==1){
          $(dansspansuivi).html("<i class='eval2'> - "+demandefich+" fichier vide sur le serveur" + " -</i>"); 
          $(dansspansuivi2).html("<i class='eval2'>&nbsp;</i>"); 
        };
        if(nomdonnees=="quoi"){ changedeliste("#inputactivite", "#mstockquoi");};
        var storageoui = $("#localstoragemoi").prop("checked");
        var storagedisponible=testestorage(); 
        if(storageoui==1 && storagedisponible == "oui"){ /* met le div dans localstorage */
          $("#suiviappli").prepend("stokage local du fichier "+nomdonnees+" arrivé du serveur <br>");
        if(responseTxt.length ==1){
          $(dansspansuivi).html("<i class='eval2'> - "+demandefich+" fichier vide sur le serveur et dans le stokage local" + " -</i>"); 
          $(dansspansuivi2).html("<i class='eval2'>&nbsp;</i>"); 
        };
          localStorage.setItem(nomfichierlocal, encryptepourlocalstorage(responseTxt)); 
          $(dansspansuivi).html("<i class='eval3'> - "+demandefich+" chargé depuis le serveur et mis dans le stockage local" + " -</i>"); 
          $(dansspansuivi2).html("<i class='eval3'>&nbsp;</i>"); 
        };
        break;
      }; /* Fin du switch */
    }; /* Fin de la fonction de retour succès */
    if(statusTxt == "error") { 
      $('.alerte').html("<br><i class='eval0'> - Serveur indisponible " + xhr.status + ": " + xhr.statusText+" -</i>");
    };
  }); /* Fin du .load */
}; 

/* efface l'entête utilisateur inconnu */
function effacelentete(){
  $("#suiviappli").prepend("effacelentete() <br>");
  $('.appentete .localisation .lieu').html(""); 
  $('.appentete .utilisateur .nomutilisateur').html("... mode démo ..."); 
  $('.appentete .localisation img.utilisateur').attr('src', constante("app")+"photoidentite.jpg");
  $('.alerte').html(""); 
  changegraphsuivi(182.5,1,0,365);
  videlediv(".stockedansdiv"); videlediv(".mstockdansdiv"); videautocomplete(); 
  supprimeautorisationqr();
  effaceutilisation(); miseajourdesvaleurs();
  utilisateurinconnu();
  verifiepreflocalstorage();
  cachetout(); $(".inscription").show(); $(".secret").hide();
  $('#formulaireacces')[0].reset();
  $("#formulaireaccesutilisateur").focus(); 
};

/* nettoyage interface */
function effaceoffresdemandes(){
  $("#suiviappli").prepend("effaceoffresdemandes() <br>");
  $("#offrechoisi").html("");
  $("#demandechoisi div:not('.utilisateur')").html("");
  modifieqr("?var1=0&var2=0");
};

/* nettoyage interface */
function effacedemandeproposition(){
  $("#suiviappli").prepend("effacedemandeproposition() <br>");
  $("#offreconfirme").html("");
  $("#demandeconfirme").html("");
};

/* nettoyage interface */
function effaceutilisationrechercheinput(){
  $("#suiviappli").prepend("effaceutilisationrechercheinput() <br>");
  $(".utilisation .recherche input").val("");
};

/* nettoyage interface */
function effaceutilisation(){
  $("#suiviappli").prepend("effaceutilisation() <br>");
  effaceoffresdemandes();
  effaceutilisationrechercheinput();
  videlespan(".compensation");
  videlespan(".demandecompensation");
  $("#demandeaqui").val("");
};

/* nettoyage interface */
function effaceconfirmation(){
  $("#suiviappli").prepend("effaceutilisation() <br>");
  $("#offreconfirmation").html("");
  $("#demandeconfirmation").html("");
  videlespan(".compensation .confirmation");
  videlespan(".demandecompensation .confirmation");
};

/* affiche l'entête utilisateur, stocke identité en attendant la validation du mot de passe */
function enattendantlemotdepasse(tableauretour){
  $("#suiviappli").prepend("enattendantlemotdepasse(tableauretour) <br>");
  /* reçoit le tableau des variables retournées par le serveur tableauretour = $(".retourserveur").html().split(","); */
  tableauretour[3] = tableauretour[3].substring(1,(tableauretour[3].length)-1) ;
  tableauretour[4] = tableauretour[4].substring(1,(tableauretour[4].length)-1) ;
  $('.secret').show(); $("#formulaireaccespass").focus(); /* rendre visible le input du mot de passe et focus dessus */
  $('.appentete .localisation .lieu').html(tableauretour[4]); /* localisation consignel dans entête application */
  $('.appentete .utilisateur .nomutilisateur').html(tableauretour[2]);/* pseudo d'utilisateur dans entête application */
  $('.appentete .localisation img.utilisateur').attr('src', tableauretour[3]); /* photo ou avatar dans entête application */
  $("#appentetesession").html(tableauretour[1]); /* numéro de session dans div caché */
};

/* encrypte pour mettre dans un div */
function encryptepourdiv(contenuenclair){
  $("#suiviappli").prepend("encryptepourdiv(contenuenclair) <br>");
  var contenulocalenclair = contenuenclair;
  var contenucrypte = encryptepourlocalstorage(contenulocalenclair);
  return contenucrypte;
};

/* encrypte les fichiers locaux */
function encryptepourlocalstorage(contenuenclair){
  $("#suiviappli").prepend("encryptepourlocalstorage(+contenuenclair+) <br>");
  if(!contenuenclair){
    var clairlocal = "[]";
  }else{
    var clairlocal=contenuenclair;
  };
  if((typeof clairlocal) =="object"){ clairlocal = JSON.stringify(clairlocal); };
  var clef1= nettoieinput($("#formulaireaccesutilisateur").val()).length;
  var clef2= codelenom(nettoieinput($("#formulaireaccespass").val())) % 1000;
var clefexterne=clef1+clef2; // doit dépendre d'un input utilisateur ou d'un retour serveur
// traitement selon votre méthode
// pas de codage pendant le développement du programme juste un test
var remplacee = new RegExp('e', 'g'); clairlocal = clairlocal.replace(remplacee, 'ᑯ');
var remplaceE = new RegExp('E', 'g'); clairlocal = clairlocal.replace(remplaceE, 'ᑝ');
var remplacea = new RegExp('a', 'g'); clairlocal = clairlocal.replace(remplacea, 'ᑥ');
var remplaceA = new RegExp('A', 'g'); clairlocal = clairlocal.replace(remplaceA, 'ᑞ');
var remplacer = new RegExp('r', 'g'); clairlocal = clairlocal.replace(remplacer, 'ᑡ');
var remplaceR = new RegExp('R', 'g'); clairlocal = clairlocal.replace(remplaceR, 'ᑤ');
var remplaceo = new RegExp('o', 'g'); clairlocal = clairlocal.replace(remplaceo, 'ᒠ');
var remplaceO = new RegExp('O', 'g'); clairlocal = clairlocal.replace(remplaceO, 'ᑘ');
var remplace1 = new RegExp('1', 'g'); clairlocal = clairlocal.replace(remplace1, 'ᑒ'); 
var contenuencode=clairlocal;
// fin de traitement selon votre méthode
return contenuencode;
};

/* encrypte les fichiers pour le transfert vers le serveur */
function encryptepourtransfert(contenuenclair){
  $("#suiviappli").prepend("encryptepourtransfert(contenuenclair) <br>");
  if(!contenuenclair){
    var clairlocal = "[]";
  }else{
    var clairlocal=contenuenclair;
  };
  var tableauretour= $(".retourserveur").html().split(",");
  var codesession=tableauretour[1]; 
// traitement selon la méthode correllée entre utilisateur et serveur
// pas de codage pendant le développement du programme
var contenuencode="encode pour transfert "+clairlocal;
// fin de traitement selon votre méthode
return contenuencode;
};

/* affiche la page d'identification */
function identification(){
  $("#suiviappli").prepend("identification() <br>");
  $(".utilisation").hide();
  $(".inscription").show(); $(".detection").hide(); $(".secret").hide(); $(".validation").show();
  utilisateurinconnu();
  videinput("#formulaireaccesutilisateur"); videinput("#formulaireaccespass"); 
  $("#formulaireaccesutilisateur").focus();
};

/* inverse l'envoi vers l'offre ou la demande */
function inverseoffredemande(option){
  $("#suiviappli").prepend("inverseoffredemande(option) <br>");
  var variablelocale=$("#inverseoffredemande").html();
  var optionlocale=option;
  if (optionlocale=="offre"){variablelocale=(" + ");};
  if (optionlocale=="demande"){variablelocale=(" - ");};
  if(variablelocale==" - "){$("#inverseoffredemande").html(" + ");};
  if(variablelocale==" + "){$("#inverseoffredemande").html(" - ");};
  $("#inputactivite").val(""); $("#inputactivite").focus();
};

/* Fait la liste des identifiant des activité soit dans la demande soit de l'offre */
function listelesact(dansdiv){
  $("#suiviappli").prepend("listelesact("+dansdiv+") <br>");
  var d = new Date(); var datechiffre = chiffreladate(d);
  $("#idtransaction .date").html(d);
  $("#idtransaction .codedate").html(datechiffre);
  var dansdivlocal=dansdiv;
  var lesoffres = $(dansdivlocal+" div[id^=act]");
  var laliste="";
  var nomdudiv ="";
  var nbdiv=$(dansdivlocal+" div[id^=act]").length;
  var lesdiv = $(dansdivlocal+" div[id^=act]");
  for (i = 0; i < nbdiv; i++) {
    nomdudiv = "#"+$(lesdiv[i]).attr("id");
    laliste = laliste +""+ $(nomdudiv+" .codedunom").text() + $(nomdudiv+" .codedetailactivite").text()+"" ;
  };
  return laliste; 
};

/* Fait la liste des activité détaillées */
function listelesactdetails(span1,span2){
  $("#suiviappli").prepend("listelesactdetails("+span1+" , "+span2+") <br>");
  var lesoffres = span1.substring(3,span1.length);
  var lesdemandes = span2;
  var lesacts = (""+lesoffres+lesdemandes).split("act");
  var nboffres = lesoffres.split("act").length;
  var nbdemandes = lesdemandes.split("act").length;
  var nbacts=lesacts.length;
  var laliste=""; 
  var ladate=$("#idtransaction .codedate").text();
  var idact="";
  var offdem="off";
  for (i = 0; i < nbacts; i++) {
    var idact="act"+lesacts[i];
    if(i == nboffres){offdem = "dem"};
    laliste += "\""+offdem+ladate+"_"+idact+"\" : [" ;
    laliste += "\""+$("#"+idact+" .quoi").text()+"\",";
    laliste += ""+$("#"+idact+" .quantite").text()+",";
    laliste += "\""+$("#"+idact+" .unite").text()+"\",";
    laliste += ""+$("#"+idact+" .consignel").text()+",";
    laliste += ""+$("#"+idact+" .argent").text()+",";
    laliste += ""+$("#"+idact+" .mlc").text()+",";
    laliste += ""+$("#"+idact+" .environnement").text()+",";
    laliste += ""+$("#"+idact+" .duree").text()+",";
    laliste += ""+$("#"+idact+" .social").text()+",";
    laliste += ""+$("#"+idact+" .foisparan").text()+",";
    laliste += ""+$("#"+idact+" .dureedevie").text()+"]";
    if(i==(nbacts -1)){laliste +="<br>"}else{laliste +=",<br>"};
  };
  return laliste; 
};

/* affiche le menu selon l'origine de la transaction à confirmer*/
function menudetailproposition(menutransaction){
var menutra = menutransaction;
$('#confirmationacceptetransaction').attr("class","actif"); 
$("#confirmationacceptetransaction div").hide();
$("#acceptetransactionstatut").show();
if(menutra == "matransaction"){$("#confirmationacceptetransaction .matransaction").show();};
if(menutra == "matransactionfermee"){$("#confirmationacceptetransaction .matransaction").show(); $("#acceptetransactionannuler").hide(); };
if(menutra == "pasmatransaction"){$("#confirmationacceptetransaction .pasmatransaction ").show(); $("#confirmationacceptetransaction .pasmatransaction button").show(); };
if(menutra == "pasmatransactionfermee"){$("#confirmationacceptetransaction .pasmatransaction").show();  $("#acceptetransactionoui").hide();  $("#acceptetransactionnon").hide(); };
if(menutra == "attente"){$('#confirmationacceptetransaction').attr("class","attente");};
};

/* Fait la mise à jour des totaux des différentes valeurs */
function miseajourdesvaleurs(){
  $("#suiviappli").prepend("miseajourdesvaleurs() <br>");
  $("#offrecompensation .montant").html(totalmontants("#offrechoisi"," .consignel"));
  miseajourgraphique();
  $("#demandecompensation .montant").html(totalmontants("#demandechoisi"," .consignel"));
  $("#offremontants .argent").html(totalmontants("#offrechoisi"," .argent"));
  $("#offremontants .mlc").html(totalmontants("#offrechoisi"," .mlc"));
  $("#offremontants .environnement").html(totalmontants("#offrechoisi"," .environnement"));
  $("#offremontants .duree").html(totalmontants("#offrechoisi"," .duree"));
  $("#offremontants .social").html(totalmontants("#offrechoisi"," .social"));
  $("#demandemontants .argent").html(totalmontants("#demandechoisi"," .argent"));
  $("#demandemontants .mlc").html(totalmontants("#demandechoisi"," .mlc"));
  $("#demandemontants .environnement").html(totalmontants("#demandechoisi"," .environnement"));
  $("#demandemontants .duree").html(totalmontants("#demandechoisi"," .duree"));
  $("#demandemontants .social").html(totalmontants("#demandechoisi"," .social"));
  $("#offremontants .listeact").html(listelesact("#offrechoisi"));
  $("#demandemontants .listeact").html(listelesact("#demandechoisi"));
  $("#mesact").html(listelesactdetails($("#offremontants .listeact").text(),$("#demandemontants .listeact").text()));
  codeoffredemande();
  var voirqr=1; /* mise à jour du qr */
  supprimeautorisationqr();
  if($("#offremontants .listeact").text()==""){voirqr=0;};
  if($("#demandemontants .listeact").text()==""){voirqr=0;};
  var speculation = testspeculation(); 
  if (speculation == "oui"){ alert("Zéro spéculation - Déplacez ou supprimez le paiement"); voirqr=0;}; 
  var datetrans = $("#idtransaction .codedate").html();
  var oksolde = oksoldedisponible();
  if (oksolde == "non"){voirqr=0;};
if(voirqr==1){ // offre et demande existent
  codetransaction();
  var codetra="\?var1\="+datetrans+"_"+$("#idtransaction .codetransaction").text();
  modifieqr(codetra);
}else{
  modifieqr();
};/* fin de modification du qr */
};

/* fonction mise à jour du graphique après la mise à jour des valeurs */
function miseajourgraphique(){
  var consignel = Number($("#offrecompensation .montant").text());
  var tableauretour = tableauretourquiutilise();
  var disponible = Number(tableauretour[3])+consignel;
  var dispomini = Number(tableauretour[5]); 
  var dispomaxi = Number(tableauretour[6]); 
//  $("#suiviappli").prepend("miseajourgraphique("+disponible+" "+Number(tableauretour[4])+" "+dispomini+" "+dispomaxi+") <br>");
  $("#suiviappli").prepend("miseajourgraphique() <br>");
  changegraphsuivi(disponible,Number(tableauretour[4]),dispomini,dispomaxi);
};

/* fonction modifie(numdiv,spanclass,) */
function modifie(numdiv,spanclass) {
  if (typeof numdiv=="string") { var numdivlocal=numdiv; } else { var numdivlocal=numdiv.id; };
  $("#suiviappli").prepend("modifie("+numdivlocal+","+spanclass+") <br>");
  /* miseajourdesvaleurs(); */
  supprimeautorisationqr();
  var spanclasslocal=spanclass; 
  var numdivinput=""+(numdivlocal+spanclass+"input");/* récupère le span pour input */
  var var1="";/* variable pour récupération de la nouvelle valeur du input nouvelle valeur*/
  var typeinput="type='text'"; var styleinput=""; var validinput1=""; var validinput2=""; var testemodifinput=""; var testelesid=""; miseajourvaleurs="";
  var var0=$("#"+numdivlocal+" ."+spanclasslocal).html(); /* valeur précédente */
  if ((spanclasslocal=="quoi") || (spanclasslocal=="unite")){ 
    /* qualification de l'activité */
    if(spanclasslocal=="quoi"){
      var lenom=var0; var lunite=$("#"+numdivlocal+" .unite").html();
    }else{
      /* changement d'unité */
      var lunite=var0; var lenom=$("#"+numdivlocal+" .quoi").html();
    };
    testelesid=" if(var1!='"+var0+"'){changelesid(\'"+numdivlocal+"-"+lunite+"\');}; ";
    validinput1=" nettoieinput("; validinput2=")";
  }else{ 
    /* quantification de l'activité */
    typeinput="type='number'"; styleinput=" style='width: 5em'";
    var numdivtext="\'"+numdivlocal+"\'";
    var quelmodif=" $('.alerte').html("+spanclasslocal+"); ";
    miseajourvaleurs=" valeurconsignel("+numdivtext+"); miseajourdesvaleurs(); ";
    if(spanclasslocal=="quantite"){miseajourvaleurs = miseajourvaleurs+" videinput('#inputactivite'); $('#inputactivite').focus();"};
  };/* fin de description de l'activité */
  /* valeurs par défaut si le input est vide */
  if (spanclasslocal =="quantite"){ testemodifinput="if (var1=='') {var1='1';}; if (var1<0) {var1=-var1;};"; }else{ testemodifinput="if (var1=='') {var1='0';}; "; };
  if ((spanclasslocal=="quoi") || (spanclasslocal=="unite")){ testemodifinput="if (var1=='') {var1='"+var0+"';}; "; };
  var onblurinput="var1="+validinput1+"$('#"+numdivinput+"').val()"+validinput2+"; "+testemodifinput+" $('#"+numdivlocal+" ."+spanclasslocal+"').html(var1) ; $('#"+numdivinput+"').remove(); $('#"+numdivlocal+" .supprime').hide() ;"+testelesid+" "+miseajourvaleurs+" "; /* finalise la modification */
  var onkeypressrinput=" if (event.keyCode==13){"+onblurinput+"}; "; /*finalise la modification */
  var nouvelinput="<input "+typeinput+" id="+numdivinput+" onblur=\" "+ onblurinput +" \" onkeypress=\""+onkeypressrinput+"\" "+styleinput+">";
  $("#"+numdivlocal+" ."+spanclasslocal).before(nouvelinput); /* insere un input avant le span */
  $("#"+numdivinput).val($("#"+numdivlocal+" ."+spanclasslocal).html()); /* transfère le span dans le input*/
  $("#"+numdivlocal+" ."+spanclasslocal).html(""); /* vide le span */
  $("#"+numdivinput).focus(); /* se place dans le input */
  $("#"+numdivlocal+" .supprime").show() ; /* affiche l'option de la poubelle cachée par onblur */
};

function modifie2(){alert("Pour modifier des éléments appuyez sur le bouton Négocions");};

function modifieqr(nouveautexte){
  $("#suiviappli").prepend("modifieqr("+nouveautexte+") <br>");
  var siteconsignel=constante("siteweb");
  if(!nouveautexte){
    var nouveautextelocal=siteconsignel; $(".qr").hide();
  }else{
    var nouveautextelocal=siteconsignel+nouveautexte; $(".qr").show();
  };
  qrcode.makeCode(nouveautextelocal);
  $(".qr .qrcodetexte").html(nouveautextelocal);
};

/* Précaution anti-script sur une entrée input */
function nettoieinput(valinput){
  var remplacescript = new RegExp('\\b(script)\\b', 'gi');
  valinput = valinput.replace(remplacescript, 'scr¡pt');
  var remplacescript = new RegExp('\\b(style)\\b', 'gi');
  valinput = valinput.replace(remplacescript, 'sty¦e');
  var remplace2blancs = new RegExp(' {2,}', 'g');
  valinput = valinput.replace(remplace2blancs, ' ');
  var supprimeboutsblancs = new RegExp('^ | $', 'g');
  valinput = valinput.replace(supprimeboutsblancs, '');
  var remplaceet = new RegExp('&', 'g'); 
  valinput = valinput.replace(remplaceet, 'ୡ'); 
  var remplaceinf = new RegExp('<', 'g'); 
  valinput = valinput.replace(remplaceinf, 'ᐸ'); 
  var remplacesup = new RegExp('>', 'g'); 
  valinput = valinput.replace(remplacesup, 'ᐳ'); 
  var remplacelesguillemets = new RegExp('"', 'g');
  valinput = valinput.replace(remplacelesguillemets, 'ʺ');
  var remplaceappostrophes = new RegExp("'", 'g');
  valinput = valinput.replace(remplaceappostrophes, '’');
$("#suiviappli").prepend("nettoieinput(valinput) <br>");
  return valinput;
};

/* Précaution anti-script sur une entrée input */
function nettoieinputnb(valinput){
  var gardenchiffresettiret = new RegExp('[^\\d-]', 'gi');
  valinput = valinput.replace(gardenchiffresettiret, '');
  $("#suiviappli").prepend("nettoieinputnb(valinput) <br>");
  return valinput;
};

/* Précaution anti-script sur une entrée input demande transaction */
function nettoieinputtra(valinput){
  var gardenchiffresettiret = new RegExp('[^\\d_]', 'gi');
  valinput = valinput.replace(gardenchiffresettiret, '');
  $("#suiviappli").prepend("nettoieinputtra("+valinput+") <br>");
  return valinput;
};

function okinputactivite(){
  $("#suiviappli").prepend("okinputactivite() <br>");
  var entree=nettoieinput($('#inputactivite').val()); 
  if(entree==""){ 
    $("#inputcherchefaire").val(""); $("#inputcherchequoi").val(""); $("#inputcherchepourqui").val(""); ;$("#inputchercheparqui").val(""); 
  }else {
    $("#suiviappli").prepend("avant envoi choix activite(entree) entree: "+entree+" <br>");
    choixactivite(entree);
  }; 
};

/* test si le solde en consignel est suffisant */
function oksoldedisponible(){
  $("#suiviappli").prepend("testsoldedisponible() <br>");
  var ok="oui";
  var consignel = Number($("#offrecompensation .montant").text());
  var tableauretour = tableauretourquiutilise();
  if ((Number(tableauretour[3])+consignel)<0){
    ok="non"; 
    $("#offrechoisi div:nth-child(1) .quantite").html("0"); 
    alert("Oups pas assez de ↺onsignel disponible.");
  };
  var maxconsignelnbjour = Number(nettoieinputnb($("#fluxconsignel").val()));
  if ((Number(tableauretour[4])*maxconsignelnbjour+consignel)<0){
    ok="non"; 
    $("#offrechoisi div:nth-child(1) .quantite").html("0"); 
    alert("Oups dépense de ↺onsignel supérieure à "+maxconsignelnbjour+" jours de consommation responsable.");
  };
  return ok;
};

/* prépare le input avec le choixfaire, le choixquoi etc */
function proposechoix(){
  $("#suiviappli").prepend("proposechoix() <br>");
  var var1 = nettoieinput($("#inputcherchefaire").val());
  var var2 = nettoieinput($("#inputcherchequoi").val()); if (var1 !== "" && var2 !== ""){var2 = " "+ var2;};
  var var3 = nettoieinput($("#inputcherchepourqui").val()); if (var3 !== ""){var3 = " "+ var3;};
  var var4 = nettoieinput($("#inputchercheparqui").val()); if (var4 !== ""){var4 = " "+ var4;};
  var variablelocale = nettoieinput(var1+var2+var3+var4);
  $("#inputactivite").val(variablelocale);
};

/* proposition acceptée avec nouveau résumé en retour du serveur */
function propositionacceptee(responseduserveur){
  $("#confirmationinputcode").val("2019"); 
  $("#confirmationokinputcode").click();
  //  changegraphsuivi(disponible,dispomini,unjour,dispomaxi);
  // reçoit nouveau résumé de compte et change le graphique
  var nouveauresume = responseduserveur;
  var tableauretour = tableauretourquiutilise();
  $(".retourserveur").html( tableauretour[0]+","+tableauretour[1]+","+tableauretour[2]+","+nouveauresume+",,");
  // changegraphsuivi(disponible,unjour,dispomini,dispomaxi);
  var paramgraph = nouveauresume.split(","); 
   changegraphsuivi(Number(paramgraph[0]),Number(paramgraph[1]),Number(paramgraph[2]),Number(paramgraph[3]));
   effaceconfirmation();
  // mise à jour du stockage local à faire
};

/* proposition annulée par l'auteur */
function propositionannulee(responseduserveur){
};

/* proposition refusée avec message du serveur */
function propositionrefusee(responseduserveur){
  menudetailproposition("attente");
};

/* proposition négociée */
function propositionnegociee(){
};

/* retourne le tableau des valeurs pour l'élément enum */
function refdevaleur(codeitem){
  var identif=codeitem;
  if($("#stockevaleursref").text().length > 5){
    var obj = JSON.parse(decryptelocalstorage($("#stockevaleursref").text())); var ref1 = obj[identif]; 
  };
  if($("#mstockmesvaleursref").text().length > 5){
    var obj = JSON.parse(decryptelocalstorage($("#mstockmesvaleursref").text())); var ref2 = obj[identif]; 
  };
  if (!ref2){ 
    /* pas de ref perso */
    if (!ref1){ 
      /* pas de ref generale */
      return undefined;
    }else{ 
      /* pas de ref perso mais ref générale */
      return ref1;
    };
  }else{ 
    /* pas de ref perso */
    if (ref1){ 
      /* mais ref generale */
      return ref2;
    }else{ 
      /* pas de ref perso ni ref générale */
      return undefined;
    };
  };
};

/* sans espace dans le nom */
function sansespace(chaine){
  var chainelocale=chaine;
  var remplaceblanc = new RegExp(' ', 'g');
  chainelocale = chainelocale.replace(remplaceblanc, '');
  return chainelocale;
};

/* function suivitransfertliste(transfert,origine,destination){$('.informations').html(transfert+" provient de "+origine+" arrive dans "+destination)}; */
function suivisortable(suivisortable1,suivisortable2,suivisortable3){ 
  $("#suiviappli").prepend("suivisortable(suivisortable1,suivisortable2,"+suivisortable3+") <br>");
  /* (suivisortable1+" - "+suivisortable2+" - "+suivisortable3); // passage d'un div à l'autre */
  if(suivisortable3=="poubelle"){$('#poubelle').animate({width: "50px"}, 250);$('#poubelle').animate({width: "5px"}, 250);};
  if(suivisortable3=="poubelle2"){$('#poubelle2').animate({width: "50px"}, 250);$('#poubelle2').animate({width: "5px"}, 250);};
  setTimeout(function(){videlespoubelles();},250); 
  miseajourdesvaleurs(); 
}; /* pour l'instant animation du vidage de poubelle */

function supprimediv(numdiv) {
  $("#suiviappli").prepend("supprimediv(numdiv) <br>");
  $(numdiv).remove(); miseajourdesvaleurs();
};

/* passe le code qr en orange lors d'une modification de la proposition */
function supprimeautorisationqr(){
  $("#suiviappli").prepend("supprimeautorisationqr() <br>");
  $('#validqr').attr("class","qr");
  $("#idtransaction .codetransaction").html("");
};

/* synchronise les fichiers personnels après activation de l'utilisateur */
function synchroniseutilisateur(tableauretour){
  $("#suiviappli").prepend("(tableauretour "+tableauretour+") <br>");

  var tableausynchro=tableauretour;
  var fichiersynchro = 0; 
  var enlocal = $().html().split(",");
  if (enlocal[0]==tableausynchro[3]){ fichiersynchro = 1 ;};/* identité des soldes en consignel */
  /* vérifie les préférences de l'utilisateur*/
  /*$(".alerte").html(tableausynchro[0]+" "+tableausynchro[1]+" "+tableausynchro[2]+" "+tableausynchro[3]+" "+tableausynchro[4]+" "+tableausynchro[5]+" "+tableausynchro[6]+" "); */
  /* code utilisateur = tableausynchro[0] code session = tableausynchro[1] */
  /* si l'utilisateur veut aucun fichier local ne rien faire */
  /* si l'utilisateur utilise local storage vérifier le résumé de compte avec celui du serveur */
  /* si les deux résumés de compte sont identiques charger les données à partir de local storage */
  /* si local storage est vide charger les données à partir du serveur */
  /* sinon les résumés de compte ne sont pas identiques */
  /* faire la conciliation entre le serveur et local storage */
  /* élimine les données obsoletes */
  /* stocke les données dans le divmstokeresumemoi etc */
  /* stocke les données dans localstorage */
  return fichiersynchro;
};

/* retourne le tableauretour de qui utilise sous forme objet */
function tableauretourquiutilise(){
  var tableauretour = $(".retourserveur").text().split(",");
  if((tableauretour[0]=="...")||(tableauretour[0]=="u0")){
    tableauretour = $("#stockagepreferences .retourpasdeserveur").text().split(",");
  }else{
    tableauretour = $("#stockagepreferences .retourserveur").text().split(",");
  };
  return tableauretour;
}; /* fin codequiutilise */

/* test de dépassement de la compensation en consignel par le dac */
function testdepassement(consignel){
  $("#suiviappli").prepend("testdepassement("+consignel+") <br>");
  var consignellocal=consignel;
  var maximumparjour=16*15;
  var consigneldejadonnee=0;
  if(consignellocal + consigneldejadonnee > maximumparjour){consignellocal=maximumparjour;};
  return consignellocal;
};

/* teste si des intermédiaires d'échange ($ mlc ↺) sont échangés */
/* blabla */
function testspeculation(){
  $("#suiviappli").prepend("testspeculation() <br>");
  var param = $("#mesact").text();
  var offredebut = param.indexOf("off"); 
  var demandedebut = param.lastIndexOf("dem");
  var offre = param.substr(offredebut,demandedebut-offredebut-2);
  var actesoffredebut =  offre.indexOf("act"); 
  var actesoffre=  offre.substr(actesoffredebut);
  var actesoffrefin =  actesoffre.indexOf("\"");
  var actesoffre =  actesoffre.substr(0,actesoffrefin);
  var listeactesoffre = actesoffre.split("act");
  var nombreactes = listeactesoffre.length;
  var monnaiedansoffre = 0;
  var typetroc="";
  var i;
  for (i = 1; i < nombreactes; i++) {
    typetroc = queltypetroc(listeactesoffre[i].substr(3)) ;
    if (typetroc == "doubletroc"){monnaiedansoffre = 1; i = nombreactes;};
  };
  if (monnaiedansoffre == 0){ return "non"};
  var demande = param.substr(demandedebut-1,param.length);
  var actesdemandedebut =  demande.indexOf("act"); 
  var actesdemande=  demande.substr(actesdemandedebut);
  var actesdemandefin =  actesdemande.indexOf("\"");
  var actesdemande =  actesdemande.substr(0,actesdemandefin);
  var listeactesdem = actesdemande.split("act");
  var nombreactes = listeactesdem.length;
  var monnaiedansdemande = 0;
  var typetroc="";
  var i;
  for (i = 1; i < nombreactes; i++) {
    typetroc = queltypetroc(listeactesdem[i].substr(3)) ;
    if (typetroc == "doubletroc"){monnaiedansdemande = 1; i = nombreactes;};
  };
  if (monnaiedansdemande == 0){ return "non"};
  if (monnaiedansoffre+monnaiedansdemande == 2){ return "oui"};
};

/* test si le navigateur accepte localstorage */
function testestorage(){
  var localStorage; 
  var storagedisponible="oui";
  try {
    window.localStorage.setItem('LocalStorageOK','1'); window.localStorage.removeItem('LocalStorageOK'); localStorage = window.localStorage;
  } 
  catch(err) {
    if ( (err.code == 18) || (err.code == 22) ) {storagedisponible="non";};
  };
  return storagedisponible;
};

/* Fait le total du consignel et des valeurs */
function totalmontants(dansdiv, quelspan){
  $("#suiviappli").prepend("totalmontants("+dansdiv+" "+quelspan+") <br>");
  var dansdivlocal=dansdiv;
  var lesoffres = $(dansdivlocal+" div[id^=act]");
  var total=0;
  var nomdudiv ="";
  var nbdiv=$(dansdivlocal+" div[id^=act]").length;
  var lesdiv = $(dansdivlocal+" div[id^=act]");
  for (i = 0; i < nbdiv; i++) {
    nomdudiv = "#"+$(lesdiv[i]).attr("id");
    total = total + Number($(nomdudiv+" .quantite").text()) * Number($(nomdudiv+quelspan).text()) ;
    total = Math.round(total*100)/100;
  };
  return total; 
};

/* fonction type */
function toto(variable){
  $("#suiviappli").prepend("toto(variable) <br>");
  var variablelocale=variable;
};

function utilisateurinconnu(){
  $(".retourserveur").html("u0, 0 , Inconnu , Inconnu , Inconnu , utilisateur inconnu");
};

/* extrait les variables de l'url */
function valeururl(nomdevaleur){
var regleregex = new RegExp( '[\?&]' + nomdevaleur + '=([^&]+)', 'i' ) ;
var valeurtrouvee = regleregex.exec( window.top.location.search ) ;
if ( valeurtrouvee && valeurtrouvee.length > 1 ){
return nettoieinput(decodeURIComponent( valeurtrouvee[1] )) ;
}else{
  var d = new Date(); var datechiffre = chiffreladate(d);
return datechiffre ;
};
};

/* calcul de la valeur du consignel DAC */
function valeurconsignel(quelact){
  $("#suiviappli").prepend("valeurconsignel("+quelact+") <br>");
  var nomdudiv = "#"+quelact;
  var minimumviable = constante("minimumviable"); /* salaire horaire minimum pour la viabilité durable */
  var valrefenviro = constante("valrefenviro"); /* valeur absolue environnementale de la région pour 1 $, 1 mlc ou 1 ↺ */
  var coefsalairemoyen = constante("coefsalairemoyen"); /* calcul du salaire moyen par rapport au minimumviable*/
  var coefsalaireindecent = constante("coefsalaireindecent"); /* calcul du salaire maximum accpetable*/
  var valabsargent = Math.abs( Number($(nomdudiv+" .argent").text()) + Number($(nomdudiv+" .mlc").text()) ); /* valeur cumulée des $ et des mlc de l'activité dont on garde la valeur absolue */
  var valenviron =Number($(nomdudiv+" .environnement").text()) ; /* valeur de l'environnement de l'activité */
var coefgainenvironnement = 1.1; /* gain environnemental 10% */
  var dureeactiv =Number($(nomdudiv+" .duree").text()) ; /* durée de l'activité */
  var valsocial =Number($(nomdudiv+" .social").text()) ; /* valeur sociale de l'activité */
  var valconsignel =0; 
  var valeurtravail = 0;
  valeurtravail = valeurtravail + (dureeactiv * minimumviable) ;
  valeurtravail = valeurtravail + (minimumviable * (valsocial+1));
  var compensationeconomique = ( - coefsalairemoyen * dureeactiv * minimumviable + (dureeactiv * (minimumviable + (minimumviable * (valsocial+1)))));
  var revenuindecent = dureeactiv * minimumviable * coefsalaireindecent;
  if( valabsargent < valeurtravail ){ 
    /*si la valeur absolue de l'argent ($+mlc) est inférieure à la valeur correspondant à la durée de l'activité pondérée par la valeur sociale */
    if(valenviron<0){ /* si la valeur environnementale est négative pas de compensation */
      valconsignel =0;
    }else{ /* la valeur environnemental est positive compensation pour paiement insuffisant par rapport à la durée pondérée par la valeur sociale */
      valconsignel = compensationeconomique ;
    };
  }else{ 
    /* la valeur absolue de l'argent ($+mlc) est supérieure à la valeur correspondant à la durée de l,activité pondérée par la valeur sociale */
    if(valabsargent > revenuindecent ){ 
      /* si la valeur absolue de l'argent ($+mlc) est supérieure à la valeur correspondant à 20 fois la durée de l'activité pondérée par la valeur sociale compenser en retirant l'excédent en ↺onsignél */
      valconsignel = - (valabsargent - revenuindecent) ;
    }else{ /* pas de paiement excessif ni insuffisant pas de compensation */
      valconsignel =0;
    };
  };
  /* ajout de la partie du calcul correspondant à l'environnement */
var compenseenvironnement =0;
if( valrefenviro == 0 ){ compenseenvironnement = valenviron ;}else{ compenseenvironnement = coefgainenvironnement * valenviron / valrefenviro; };
valconsignel = valconsignel + compenseenvironnement;
  /* arrondi à 2 chiffres après la virgule */
  valconsignel = Math.round( valconsignel*100 )/100 ;
  /* changement de la valeur dans le span du consignel */
  valconsignel=testdepassement(valconsignel);
  $(nomdudiv+" .consignel").html(valconsignel);
};

/* validation de l'utilisateur */
function valideutilisateur(nomutilisateur){
  $("#suiviappli").prepend("valideutilisateur(nomutilisateur) <br>");
  var nomutil = nomutilisateur;
  var nomutil2= nettoieinput($("#formulaireaccesutilisateur").val());
  var nomutil3= nettoieinput($("#formulaireaccespass").val());
  var tableauretour= $(".retourserveur").html().split(",");
  if(tableauretour.length==1 || nomutil3=="" || tableauretour[1]==0){
// si le serveur n'a pas encore renvoyé de code de session //chiffre les trois champs input
 // met l'indicateur à 0
if(nomutil){ var nomcode = codelenom(nomutil)}; //chiffre le nom d'utilisateur 
if(nomutil2){ var nomcode2 = codelenom(nomutil2)}; //chiffre le nom d'utilisateur sinon chiffre undefined
if(nomutil3){ var nomcode3 = codelenom(nomutil3)}; //chiffre le mot de passe sinon chiffre undefined
}else{
// le code de session existe ("codedesession "+tableauretour[1]); 
var nomcode = codelenom((codelenom(nomutil2)*tableauretour[1])+""); //chiffre le nom d'utilisateur avec le code session
var nomcode2 = codelenom(codelenom(nomutil3)*tableauretour[1]+""); //chiffre le mot de passe avec le code session
var nomcode3 = codelenom("0"); //chiffre la demande de fichier avec le code de session
var nomcode4 = codelenom(nomutil2+nomutil3); //chiffre le code d'acces local
};
var demandeauserveur = "var1=" + nomcode + "&var2=" + nomcode2 + "&var3=" + nomcode3 ; // prépare la demande au serveur // envoi la demande au serveur
var retourdansdiv = ".retourserveur";
$.get(constante("php"), demandeauserveur , function(responseTxt, statusTxt, xhr){
  /* truc à faire dans tous les cas $('.test').append("<br>... Données traitées par la fonction de retour<br>"); */
  if(statusTxt == "success") {
    /* le chargement est fait par le .load dans le div .retourserveur et dans la variable reponseTxt */
    if (responseTxt.indexOf("?php")==1) {
      $('.alerte').html("<br><i class='eval2'>Vérification d'utilisateur indisponible sur le serveur</i><br>"); 
      /* suite à écrire pour utiliser en local sans vérification serveur de l'utilisateur */
      return;
    }else{
    //    responseTxt = decryptetransfert(responseTxt);
    $(retourdansdiv).html(responseTxt);
    }; 
// séparation des variables renvoyées dans le div .retourserveur par le serveur
try { var tableauretour = $(".retourserveur").html().split(","); }
  catch(err) {$(".retourserveur").html(" , 0 , Inconnu , Inconnu , Inconnu , utilisateur inconnu"); tableauretour = $(".retourserveur").html().split(",");};
/* Utilisateur inconnu sur le serveur echo (" , 0 , Inconnu , Inconnu , Inconnu , utilisateur inconnu") */
tableauretour[2] = tableauretour[2].substring(1,(tableauretour[2].length)-1) ;
// peut contenir le pseudo, inconnu ou 1 si l'utilisateur est identifié par le serveur 
if(tableauretour[2] =="Inconnu"){
  effacelentete();/* reste sur la page d'inscription à faire accès au test par le menu */

if(tableauretour[0] == " "){
  tableauretour[0]="u0"; $(".retourserveur").prepend(tableauretour[0]); /* ajout identifiant local utilisateur inconnu si ce n'est pas encore fait mauvaise identification d'utilisateur*/
  };
//  effaceutilisation();/* efface les traces des préparations de transactions précédentes */
  /* Chargement des valeurs locales de test */
}else{ /* Utilisateur connu */
  /* Utilisateur connu sur le serveur sans mot de passe echo (" ,".$nombrealeatoire.",".$var3.",".$cheminfichierimage.",".$var5.", ") */
  /* (vide,numerosession,pseudoutilisateur,cheminimageouavatar,localiteconsignel,vide) */
  if(($("#appentetesession").html())==tableauretour[1]){ 
    /* début de si utilisateur connu et mot de passe connu */
    tableauretour[0]="u"+nomcode4; 
    $(".retourserveur").html(tableauretour[0]+$(".retourserveur").text()); /* identifiant local u+code(utilisateur+pass) */
    verifiepreflocalstorage();
    activeutilisation(tableauretour);
  }else{ /* début de si utilisateur connu et mot de passe inconnu */
// if(tableauretour[0] != "u0"){
// tableauretour[0]="u0"; $(".retourserveur").prepend(tableauretour[0]); /* ajout identifiant local si ce n,est pas encore fait mauvais mot de passe */
// };
    enattendantlemotdepasse(tableauretour);
  }; /* fin de si utilisateur connu et mot de passe inconnu */
  /* garder identifiants de session à faire*/
}; /* Fin de utilisateur connu */
}; /* Fin de la fonction de retour succès */
if(statusTxt == "error") { $('.alerte').html("<br><i class='eval0'> - Serveur indisponible " + xhr.status + ": " + xhr.statusText+" -</i>");};
}); /* Fin du .load */
}; 

/* validation de la durée d'expiration */
function validdureeexpire(){
var dureeexpirelocale = $("#dureeexpire").val();
if((dureeexpirelocale=="")||(dureeexpirelocale<1)||(dureeexpirelocale>365)){$("#dureeexpire").val("1")};
miseajourdesvaleurs();
}; 

/* validation de la durée d'expiration */
function validfluxconsignel(){
var fluxconsignellocal = $("#fluxconsignel").val();
alert("fonction validfluxconsignel à écrire");
}; 

/* validation de la durée d'expiration */
function validedemandeaqui(){
  $("#suiviappli").prepend("validedemandeaqui() <br>");
var demandeaquilocale = $("#demandeaqui").val();
demandeaquilocale = nettoieinput(demandeaquilocale);
$("#demandeaqui").val(demandeaquilocale) ;
miseajourdesvaleurs();
}; 

/* mise à jour préférences avec localstorage */
function verifiepreflocalstorage(){
  $("#suiviappli").prepend("verifiepreflocalstorage() <br>");
  var codeutilisateur = codequiutilise();
  var codeprefstorageutilisateur = "consignel"+codeutilisateur;
  $("#suiviappli").prepend("verifiepreflocalstorage() pour codeprefstorageutilisateur <br>");
  var storagedisponible=testestorage(); 
  if(storagedisponible != "oui"){
    var fichierlocaljson=undefined;
    autoriselocalstorage() // pas de localstorage
  }else{
    var fichierlocaljson=decryptelocalstorage(localStorage.getItem( codeprefstorageutilisateur )); 
//    var fichierlocaljson=localStorage.getItem( codeprefstorageutilisateur ); 
  };
if(!fichierlocaljson){ 
  /* aller chercher les informations sur le serveur("pas de preferences locales pour "+codeprefstorageutilisateur); */
}; /* fin du si le fichier local n'existe pas */
if(fichierlocaljson){ /* si le fichier local de préférence existe */
  var fichierlocaljson2= JSON.parse(fichierlocaljson);
  if(fichierlocaljson2[0]==0 || fichierlocaljson2[0]==false){ $("#localstoragepublic").val("non"); $("#localstoragepublic").prop('checked', false); $(".localstoragepublic").html('non autorisé'); videfichierspourtous(); };
  if(fichierlocaljson2[0]==1 || fichierlocaljson2[0]==true){ $("#localstoragepublic").val("oui"); $("#localstoragepublic").prop('checked', true); $(".localstoragepublic").html('autorisé'); chargelesfichierspourtous(); };

  if(fichierlocaljson2[1]==0 || fichierlocaljson2[1]==false){ $("#localstoragemoi").val("non"); $("#localstoragemoi").prop('checked', false); $(".localstoragemoi").html('non autorisé'); videfichierspersonnels(); };
  if(fichierlocaljson2[1]==1 || fichierlocaljson2[1]==true){ $("#localstoragemoi").val("oui"); $("#localstoragemoi").prop('checked', true); $(".localstoragemoi").html('autorisé'); chargelesfichierspersonnels(); };

}; /* fin du si le fichier local existe */
}; 

/* utilise l'url pour le input de confirmation */
function verifieurlpropose(){
  $("#suiviappli").prepend("verifieurlpropose() <br>");
  var var1local = valeururl("var1");
  if (var1local.length > 15){
    $("#confirmationinputcode").val(var1local); 
    var utilisateur = codequiutilise();
    if(utilisateur=="u0"){ 
      $('#confirmationrecherche').attr("class","attente"); $('.menupref .suivant').html(".confirmation"); 
    };
  }else{
    $("#confirmationinputcode").val(var1local+"_"); 
  };
};
/* mise à jour de la valeur du consignel en fonction des autres valeurs */

/* vide autocomplete du input*/
function videautocomplete() {
  $("#suiviappli").prepend("videautocomplete() <br>");
  $(".recherche input").autocomplete("option","source",[]);
  $("#changeaideinputactivite").html("ø");
  var listeduinput = JSON.parse($("#listestockevaleursrefmini").text());
  $("#inputactivite").autocomplete("option","source",listeduinput);
};

/* vide le localstorage et les div fichierspersonnels*/
function videfichierspersonnels(){
  $("#suiviappli").prepend("videfichierspersonnels() <br>");
  $(".fichierspersonnels .poubelle").click();
};

/* vide le localstorage et les div fichierspourtous */
function videfichierspourtous(){
  $("#suiviappli").prepend("videfichierspourtous() <br>");
  $(".fichierspourtous .poubelle").click();
};

/* vide le input */
function videinput(balise){
  $("#suiviappli").prepend("videinput("+balise+") <br>");
  var balise2=balise; $(balise2).val("");
};

/* vide ledivselon la balise envoyée "#poubelle" ".stockedansdiv" */
function videlediv(balise){
  $("#suiviappli").prepend("videlediv("+balise+") <br>");
  var balise2=balise; $(balise2).html("...");
  if(balise2==".stockedansdiv"){chargevaleursrefmini(); };
};

/* vide ledivselon la balise envoyée "#poubelle" ".stockedansdiv" */
function videlespan(balise){
  $("#suiviappli").prepend("videlespan("+balise+") <br>");
  var balise2=balise; $(balise2+" span").html("");
};

/* vide le localstorage */
function videlocalstorage(nomdonnees){
  var storagedisponible=testestorage(); 
  if (storagedisponible != "oui") { 
    $("#suiviappli").prepend("localstorage non supporté par ce navigateur <br>"); return;
  };
  if ((typeof nomdonnees)=="string") { var tableau=[nomdonnees]; } else { var tableau=nomdonnees; };
  $("#suiviappli").prepend("videlocalstorage("+nomdonnees+") <br>");
  for (x in tableau) {
    var dansdiv="#stocke"+tableau[x];
    var nomfichierlocal=sansespace("consignel"+tableau[x]);
    localStorage.removeItem(nomfichierlocal); 
    $(".fichierspourtous .statut"+tableau[x]).html(localStorage.getItem(nomfichierlocal));
    $(".fichierspourtous .statut2"+tableau[x]).html("&nbsp;");
  }; /* fin du for x */
  $("#suiviappli").prepend("videlocalstorage("+nomdonnees+") <br>");
};

/* vide le localstorageperso */
function videlocalstorageperso(nomdonnees){
  var storagedisponible=testestorage(); if (storagedisponible != "oui") {return};
  if (typeof(Storage) == "undefined") {$("#suiviappli").prepend("localstorage non supporté par ce navigateur <br>"); return;};
  $("#suiviappli").prepend("videlocalstorageperso("+nomdonnees+") <br>");
  if (typeof nomdonnees=="string") { var tableau=[nomdonnees]; } else { var tableau=nomdonnees; };
  for (x in tableau) {
    var dansdiv="#mstock"+tableau[x];
    var dansspansuivi=".fichierspersonnels .statut"+tableau[x];
    var dansspansuivi2=".fichierspersonnels .statut2"+tableau[x];
    var codeutilisateur = codequiutilise();
    var nomfichierlocal=sansespace(codeutilisateur+tableau[x]);
    localStorage.removeItem(nomfichierlocal); 
    $(dansspansuivi).html(localStorage.getItem(nomfichierlocal));
    $(dansspansuivi2).html("&nbsp;");
    $(dansdiv).html("");
  }; /* fin du for x */
};

/* vide #poubelle et #poubelle2 */
function videlespoubelles(){$("#poubelle").html("");$("#poubelle2").html("");miseajourdesvaleurs();};

// définition des constantes selon la localité pour les calculs
function constante($nom){
if($nom == "paiements"){ return '["$_18702","$_25343","mlc_41642",mlc_51083","↺_629160","↺_721781"]'; };
if($nom == "ouverturecompte"){ return '"150,90,30,360"'; };
if($nom == "localite"){ return "localite/"; };
if($nom == "siteweb"){ 
  return nettoieinput(window.location.protocol+"//"+window.location.host + window.location.pathname) ;  /* automatique */
  /*  return "www.designvegetal.com/projets/consignel/index.html";  /* forcé */
};
if($nom == "php"){ return "consignel-scp/consignel.php"; };
if($nom == "app"){ return "consignel-app/"; };
if($nom == "scp"){ return "consignel-scp/"; };
if($nom == "minimumviable"){ return 15; }; /* salaire horaire minimum pour la viabilité durable (Montérégie 2018) */
if($nom == "valrefenviro"){ return 1.88; }; /* valeur absolue environnementale de la région pour 1 $, 1 mlc ou 1 ↺ */
if($nom == "coefsalairemoyen"){ return 2; }; /* calcul du salaire moyen par rapport au minimumviable*/
if($nom == "coefsalaireindecent"){ return 20; }; /* calcul du salaire maximum accepetable*/
if($nom == "maximumcompte"){ return 54600; }; /* maximum d'accumulation du compte */
if($nom == "coefgainenvironnement"){ return 1.1; };  /* gain environnemental 10% */

};

