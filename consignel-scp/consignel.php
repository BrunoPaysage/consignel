<?php
$donnee1 = $_REQUEST['var1']; // code undefined, code utilisateur ou code utilisateur combiné session
$donnee2 = $_REQUEST['var2']; // code undefined, code utilisateur ou code mot de passe combiné session
$donnee3 = $_REQUEST['var3']; // code undefined, code mot de passe ou code un truc en plus
$donnee4 = antitagnb($_REQUEST['var4']);
$donnee5 = antitaghtml($_REQUEST['var5']); // entrée de texte ou de fichier
if(($donnee1=="undefined") || ($donnee1=="")){$donnee1=1;}else{$donnee1 = preg_replace( '/\D*/', '', $donnee1);}; // pas de imput ni de numéro de session  garde les nombres de la chaine 
if(($donnee2=="undefined") || ($donnee2=="")){$donnee2=1;}else{$donnee2 = preg_replace( '/\D*/', '', $donnee2);}; // pas de code utilisateur ou de code session utilisateur  garde les nombres de la chaine 
if(($donnee3=="undefined") || ($donnee3=="")){$donnee3=1;}else{$donnee3 = preg_replace( '/\D*/', '', $donnee3);}; // pas de mot de passe ou de truc en plus garde les nombres de la chaine 
date_default_timezone_set('America/New_York');
$base = constante("base");
$baseutilisateurs = constante("baseutilisateurs");
$cheminfichier = tracelechemin("",$baseutilisateurs,".baseconsignel3");
if (!file_exists($cheminfichier)) {baseminimale();};

// vérification de l'utilisateur et du code
// Pas de code utilisateur
if($donnee1==1){echo (" , 0 , Inconnu , Inconnu , Inconnu , efface l'entrée");}; // session nulle demande remise à zéro et renvoi

// Code secret sans code utilisateur
if(($donnee1==$donnee3) and ($donnee2=="1")){
  echo (" , 0 , Inconnu , Inconnu , Inconnu , secret seul"); // session nulle code secret sans identifiant ne rien faire
};

// Code utilisateur sans code mot de passe
if(($donnee1==$donnee2) and ($donnee3==1)){
  $cheminfichier = tracelechemin("",$baseutilisateurs,".baseconsignel3");
  if (file_exists($cheminfichier)) { // vérification de l'utilisateur le fichier existe
    $existe = FALSE; // Testeur de boucle
    $fichierencours = fopen($cheminfichier, 'r'); // ouverture en lecture
    while (!feof($fichierencours) && !$existe) { // cherche dans les lignes
      $ligne = decryptelestockage(fgets($fichierencours, 1024)); // ligne par ligne
      if (preg_match('/\b' . preg_quote($donnee2) . '\b/u', $ligne)) { 
        list($var1, $var2, $var3, $var4, $var5) = explode(",", $ligne);
        // $var1 code utilisateur, $var2 code mot de passe, $var3 pseudo utilisateur, $var4 image ou avatar, $var5 localité consignel
        if ($var1==$donnee2){ // trouvé comme identifiant
          $existe = TRUE; // Valeur trouvée arrêt du while
        }; // fin du trouvé comme identifiant
      } // Fin de trouvé dans la ligne
    }; // Fin de cherche dans les lignes
    fclose($fichierencours); // fermeture du fichier
  }else{
    die("fichier inconnu Fichier non trouvé pas d'utilisateur 1"); // Fichier non trouvé pas d'utilisateur
  };
  if($existe==TRUE){ // L'identifiant a été trouvé
    $nombrealeatoire = mt_rand(1,9999); // prépare le numéro de session
    $avatar = substr($var4,1,6);
      if($avatar=="avatar"){ // image ou avatar prendre l'avatar
        $baseavatars = constante("baseavatars");
        $cheminfichierimage = $baseavatars.substr($var4,1);
      }else{ // image ou avatar prendre l'image
        $cheminfichierimage = tracelechemin($donnee2,$base,substr($var4,1));
        // insérer une fonction qui change le chemin et le nom de fichier de l'avatar personnel
      }; // fin de image ou avatar
    // hache le hache d'utilisateur avec le numéro de session idem motdepasse et clef
    $nomsession = codelenom($var1*$nombrealeatoire); 
    $codesession = codelenom($var2*$nombrealeatoire); 
    $clesession = $var1; 
    $today = getdate();  $heuresession = $today[hours]*60+$today[minutes];
    $chainecontenu = $nomsession.",".$codesession.",".$clesession.",".$nombrealeatoire.",".$heuresession;
    // Stoke le fichier temporaire de session avant mot de passe
    $cheminfich2 = tracelechemin("",$baseutilisateurs,".baseconsignel0");
    ajoutelesconnexions($cheminfich2,$chainecontenu);
    // renvoie les variables de session
    echo (" ,".$nombrealeatoire.",".$var3.",".$cheminfichierimage.",".$var5.", ");
  }; // session numéroté demande code secret
  // Fin de l'identifiant a été trouvé
  if($existe==FALSE){ // L'identifiant n'a pas été trouvé
    echo (" , 0 , Inconnu , Inconnu , Inconnu , utilisateur inconnu");
  };  // Fin de l'identifiant n'a pas été trouvé
}; // Fin de vérification de l'identité

// Code secret et code utilisateur --------------------------
if(($donnee1==$donnee2) || ($donnee1==$donnee3)){
// déjà traité soit utilisateur inconnu soit mot de passe inconnu soit les 2 inconnus
}else{
  $today = getdate();  $heureutilise = $today[hours]*60+$today[minutes];
  $cheminfichier = tracelechemin("",$baseutilisateurs,".baseconsignel0");
  if (file_exists($cheminfichier)) { // vérification de l'utilisateur le fichier existe
    $existe = FALSE; // Testeur de boucle
    $nettoyage = FALSE; // Testeur de session expiré
    $fichierencours = fopen($cheminfichier, 'r'); // ouverture en lecture
    while (!feof($fichierencours) && !$existe) { // cherche dans les lignes
      $ligne = decryptelestockage(fgets($fichierencours, 1024)); // ligne par ligne
      if (preg_match('/\b' . preg_quote($donnee2) . '\b/u', $ligne)) { 
        list($var1, $var2, $var3, $var4, $var5) = explode(",", $ligne);
        $var6 = $heureutilise - $var5;
        if (($var1==$donnee1."") and ($var2==$donnee2."")){ // trouvé identifié
          if (($var6>20) OR ($var5 > $heureutilise)){ // expiré au bout de 20 minutes et d'hier
            $nettoyage = TRUE; // besoin de nettoyage
          }else{ // non expiré
            $existe = TRUE; // Valeurs trouvée arrêt du while
          }; // fin du non expiré
        }; // fin du trouvé comme identifiant
      }; // Fin de trouvé dans la ligne
    }; // Fin de cherche dans les lignes
    fclose($fichierencours); // fermeture du fichier
  }else{
    die("fichier inconnu Fichier non trouvé pas d'utilisateur 2"); // Fichier non trouvé pas d'utilisateur
  };
  // Utilisateur correctement identifié avec son code
  if($existe==TRUE){ 
    if($donnee3==48){
      // identification initiale renvoie les variables de session
      // $va4 numéro aléatoir de session
      $oklocal=1; // renvoyer 1 pour utilisateur identifié
      $resumejson=resumecompte($var3); // 4 variables du résumé (disponible, par jour, dispomini31jours, dispomaxi31jours)
      $idtraprecedant = ""; //  identifiant transaction précédente
      echo (" ,".$var4.",".$oklocal.",".$resumejson.",".$idtraprecedant.", "); 
    }else{
      // identification secondaire demande de fichier ou transaction
      $lademande = ($donnee3/$var4);
      if($lademande==16887){  fichierperso($var3,"resume"); };
      if($lademande==6986){  fichierperso($var3,"quoi"); };
      if($lademande==86012){  fichierperso($var3,"mesvaleursref"); };
      if($lademande==116020){  fichierperso($var3,"mestransactions"); };
      if($lademande==118452){  
        if ($donnee5==""){ 
          fichierperso($var3,"mespropositions");  // echo "<br>donnee5 est vide<br>"; 
        }else{  
          $noteproposition = notetransaction($var3,"mespropositions",$donnee5); 
          echo $noteproposition;
        };
      }; // fin de "mespropositions"
      if($lademande==232828){  
        // demandeuneproposition
        if ($donnee4==""){ 
          echo "ERDP <br>donnee5 est vide<br>"; 
        }else{  
          $transactiontrouvee = cherchetransaction($var3,$donnee4); 
          echo $transactiontrouvee;
        }; 
      }; // fin de demandeuneproposition
      if($lademande==233615){ 
        // ,"accepteuneproposition"
        $transactionacceptee = acceptetransaction($var3,$donnee4); 
        echo $transactionacceptee;
      }; // fin de "accepteuneproposition"
      if($lademande==211873){ 
        // ,"annuleuneproposition"
        $transactionannulee = annuleproposition($var3,$donnee4); 
        echo $transactionannulee;
      }; // fin de "annuleuneproposition"
    };
  };  // Fin de utilisateur correctement identifié 
  if($existe==FALSE){ // L'utilisateur n'a pas été correctement identifié
    echo (" , 0 , Inconnu , Inconnu , Inconnu , utilisateur inconnu");
  };  // Fin de l'identifiant n'a pas été trouvé
  if($nettoyage == TRUE){ // Le fichier a besoin de nettoyage
    nettoyagerefsessions();
  };  // Fin de du nettoyage du fichier de session
};
// -----------------------


// accepte la proposition de transaction
function acceptetransaction($var3,$notransaction){
  $noaccepteur = $var3; 
  $idtra = "tra".$notransaction; 
  $nomfichiertra = "tra".$notransaction.".json"; 
  $idtraacc = "acc".$notransaction; 
  $nomfichieracc = "acc".$notransaction.".json"; 
  $nomfichierann = "ann".$notransaction.".json"; 
  $nomfichiersuivi = substr($idtra,0,14)."-suivi.json";
  // vérification préliminaires
  // cherche si la transsaction a été annulée
  $cheminfichier = ouvrelechemin($idtra); // chemin dans base2 par date
  if (file_exists($cheminfichier.$nomfichierann)) {
    return "PANN - Cette proposition n'est pas disponible";
  };
  // cherche si la transaction a déjà été acceptée
  if (file_exists($cheminfichier.$nomfichieracc)) {
    $fichierencours = fopen($cheminfichier.$nomfichieracc, 'r');
    $ligne = decryptelestockage(fgets($fichierencours, 1024)); // ligne par ligne
    list($var41, $var42, $var43, $var44, $var45, $var46, $var47, $var48) = explode(",", $ligne);
    if ($var48 == "\"".$noaccepteur."\"\n"){
      return "TDAC - Proposition déjà acceptée par vous";
    }else{
      return "TNDI - Cette proposition n'est pas disponible";
    };
  };
  // cherche si la proposition existe et peut être acceptée par le demandeur
  if (file_exists($cheminfichier.$nomfichiersuivi)) { // vérification que la proposition existe
    $ligneexiste = FALSE; // Testeur de boucle ligne existe dans le fichier
    $fichierencours = fopen($cheminfichier.$nomfichiersuivi, 'r'); // ouverture en lecture
    while (!feof($fichierencours) && !$ligneexiste) { // cherche dans les lignes
      $ligne = decryptelestockage(fgets($fichierencours, 1024)); // ligne par ligne
      list($var31, $var32, $var33, $var34, $var35, $var36, $var37, $var38) = explode(",", $ligne);
      if ($var31 == "\"".$idtra."\""){
        $memetransaction = TRUE; // transaction trouvée
        $ligneexiste = TRUE;
        $var3chaine = "\"".$noaccepteur."\"\n"; // attention au " et à la fin de ligne
        if ($var38 == $var3chaine){ 
          return "TREF - PACT - C'est ma proposition ";  
        }else{ 
          if(testeexpiration($var31,$var36) == "expire"){
            return "AEXP - Proposition expirée"; 
          }else{
            if( testdestinataire($var35,$var3chaine) == "autorise"){
              // vérification disponibilité de consignel et du flux autorisé
            }else{
              return "TRIN - Transaction inconnue erreur destinataire " ;// destinataire non autorisé ;
            };
          }; // fin de trouvée d'un autre non expiré
        }; // fin de proposition trouvée et proposition d'un autre
      }else{
        $memetransaction = FALSE; // transaction non trouvée
      }; 
    }; // Fin de while cherche dans les lignes
  }else{
    return "DTMR - Vérifiez le numéro de la proposition"; // le fichier n'existe pas
  };
  // vérifications complémentaires avant d'enregistrer la transaction
  $resumecpt = resumecompte($var3); 
  $derniercompte = explode( ',', $resumecpt );
  $soldeconsigneldisponible = $derniercompte[0];
  $soldeconsignelparjour = $derniercompte[2];
  if($soldeconsignelparjour =="INF"){$soldeconsignelparjour = 10;}; // à faire remplacer par variable inscription
  
  // récupération du contenu de la transaction
  if (file_exists($cheminfichier.$nomfichiertra)) {
    $contenufichiertra = decryptelestockage(file_get_contents($cheminfichier.$nomfichiertra));
  }else{
    return "TRIN - Transaction inconnue erreur accès fichier transaction " ;
  };
  $jsonenphp = json_decode($contenufichiertra,true);
  if(json_last_error_msg() != "No error"){ return "DTNC - erreur reception proposition"; };
  $consigneldemande = preg_replace( "/\"/", "", $var37); 
  $paiement = paiement($jsonenphp,$idtra);
  if ($paiement[0] == "speculation"){ return "DTIN - erreur speculation"; };
  $consigneldemandepaiement = $paiement[1]; // propositions de dons en consignel du proposeur la dépense de l'accepteur  - $paiement[4] est déjà inclue dans le da↺ $consigneldemande 
  if ((($soldeconsignelparjour * 7) + $consigneldemande + $consigneldemandepaiement)<0 ){ return "DTCE - Refus dépense ↺onsignel excessive"; $transaction = ""; };
  if (($soldeconsigneldisponible + $consigneldemande + $consigneldemandepaiement)<0 ){ return "DTMC - Refus solde ↺onsignel insuffisant"; $transaction = ""; };
  
  //$soldemlcdisponible = ; à faire
  // $mlcdemandepaiement = $paiement[2] - $paiement[5];
  //  if (($soldemlcdisponible + $mlcdemandepaiement)<0){ echo "solde mlc insuffisant"; $transaction = ""; };
  
  // $soldedollardisponible = ;  à faire
  // $dollardemandepaiement = $paiement[3] - $paiement[6];
  //  if (($soldedollardisponible + $dollardemandepaiement)<0){ echo "solde dollar insuffisant"; $transaction = ""; };
  
  // fin des vérifications et des refus, enregistrement du suivi dans les fichiers
  // ajout au fichier traxxxxxxxx_xx-suivi.json dans la base des transactions
  $dateaccepte = date("Ymd_Hi");
  $noproposeur = preg_replace( "/\D/", "", $var38);
  $var38nombre= preg_replace( "/\D/", "", $var38);
  $var38chaine = "\"".preg_replace( "/\D/", "", $var38)."\"";
  $transactionsuivi = "\"".$idtraacc."\",".$var33.",".$var32.",\"".$dateaccepte."\",".$var38chaine.",".$var36.",".$var37.",".$var3chaine;
  ajouteaufichier($cheminfichier.$nomfichiersuivi, $transactionsuivi); 
  // ajout fichier accxxxxxxxx_xxxx_xxxxxxx.json dans la base des transactions
  ajouteaufichier($cheminfichier.$nomfichieracc, $transactionsuivi); 
  // ajout au fichier xxxxx-mesproposition.json dans la base de l'accepteur
  $base=constante("base");
  $nouveautraacc = inversetransaction($idtra,$contenufichiertra,$dateaccepte,$var38nombre);
  $cheminsansfichier = tracelechemin($noaccepteur,$base,$noaccepteur); 
  ajouteaufichier($cheminsansfichier."-mespropositions.json", $nouveautraacc."\n");
  // mise à jour fichier xxxxx-resume2dates.json dans la base de l'accepteur
  $dernieresidtra = ajouteaufichier2dates($cheminsansfichier."-resume2dates.json",$idtraacc);
  $idtraprecedente = $dernieresidtra[0];
  $anciennete = $dernieresidtra[4];
  $nojourancien = $dernieresidtra[8];
  $nojour = $dernieresidtra[9];
  // fichier de chainage des transaction bloc à écrire
  
  // fonction consignelsuivi renvoie les 2 valeurs de consignel du dac 
  $consigneldac = consignelsuivi($jsonenphp,$idtra);
  $consigneldacoffre = $consigneldac[1];
  $consigneldacdemande = $consigneldac[2];
  // Calcul nouveau solde accepteur $soldeconsigneldisponible
  $nouveausoldeconsignel = ($soldeconsigneldisponible + $consigneldemandepaiement + $consigneldacdemande);
  $maxcompteconsignel = constante("maxcompte");
  if( $nouveausoldeconsignel > $maxcompteconsignel  ){  $nouveausoldeconsignel = $maxcompteconsignel ; }; // zéro accumulation toxique
  // Mise à jour du fichier  suivi31jours dans la base de l'accepteur
  $cheminfichier = tracelechemin($noaccepteur,$base,$noaccepteur."-suivi31jours.json");
  $minimax = suivi31jours($cheminfichier, $nojourancien, $nojour, $nouveausoldeconsignel);
  // Mise à jour du fichier  gain365jours dans la base de l'accepteur
  $cheminfichier = tracelechemin($noaccepteur,$base,$noaccepteur."-gain365jours.json");
  $revenujournalier = gain365jours($cheminfichier, $nojourancien, $nojour, $consigneldemande + $consigneldemandepaiement, $anciennete);
  // Mise à jour du fichier -resume.json dans la base de l'accepteur
  $nouveauresumeacc = "".$nouveausoldeconsignel.",".$minimax[0].",".$revenujournalier.",".$minimax[1];
  $cheminfichier = tracelechemin($noaccepteur,$base,$noaccepteur."-resume.json");
  remplacefichier($cheminfichier, $nouveauresumeacc);
  // Mise à jour du fichier -suiviresume.json dans la base de l'accepteur
  $cheminfichier = tracelechemin($noaccepteur,$base,$noaccepteur."-suiviresume.json");  
  ajouteaufichier($cheminfichier,$idtraacc.",".$nouveauresumeacc."\n");
  // Mise à jour du fichier des fichiers de référence quoi.json et mesvaleursref.json dans la base de l'accepteur à faire
  
  // ajout au fichier xxxxx-mesproposition.json dans la base du proposeur
  $nouveauproacc = transactionaccann("acc",$idtra,$contenufichiertra,$dateaccepte,$noaccepteur);
  $cheminsansfichier = tracelechemin($noproposeur,$base,$noproposeur); 
  ajouteaufichier($cheminsansfichier."-mespropositions.json", $nouveauproacc."\n");
  // mise à jour fichier xxxxx-resume2dates.json dans la base du proposeur
  $dernieresidtra = ajouteaufichier2dates($cheminsansfichier."-resume2dates.json",$idtraacc);
  $idtraprecedenteproposeur = $dernieresidtra[0];
  $ancienneteproposeur = $dernieresidtra[4];
  $nojourancien = $dernieresidtra[8];
  $nojour = $dernieresidtra[9];
  // fichier de chainage des transaction bloc à écrire
  
  // Calcul nouveau solde proposeur $soldeconsigneldisponibleproposeur
  $resumecptproposeur = resumecompte($noproposeur); 
  $derniercompteproposeur = explode( ',', $resumecptproposeur );
  $soldeconsigneldisponibleproposeur = $derniercompteproposeur[0];
  $soldeconsignelparjourproposeur = $derniercompteproposeur[2];
  
  //   $paiement = paiement($jsonenphp,$idtra); déjà fait  
  $consigneloffrepaiement = $paiement[4] ; // dons en consignel de l'accepteur - les dons du proposeur- $paiement[1] ont déjà été comptés dans le da↺. 
  if($consigneloffrepaiement < 0){$consigneloffrepaiement = 0;}; //déjà déduit si négatif
  
  if($consigneldacoffre < 0){$consigneldacoffre = 0;}; //déjà déduit si négatif
  $nouveausoldeconsignelproposeur = ($soldeconsigneldisponibleproposeur + $consigneloffrepaiement + $consigneldacoffre);
  if( $nouveausoldeconsignelproposeur > $maxcompteconsignel  ){ $nouveausoldeconsignelproposeur = $maxcompteconsignel ; }; // zéro accumulation toxique
  // Mise à jour du fichier  suivi31jours dans la base du proposeur
  $cheminfichier = tracelechemin($noproposeur,$base,$noproposeur."-suivi31jours.json");
  $minimaxproposeur = suivi31jours($cheminfichier, $nojourancien, $nojour, $nouveausoldeconsignelproposeur);
  // Mise à jour du fichier  gain365jours dans la base du proposeur
  $cheminfichier = tracelechemin($noproposeur,$base,$noproposeur."-gain365jours.json");
  $revenujournalierproposeur = gain365jours($cheminfichier, $nojourancien, $nojour, $consigneldacoffre + $consigneloffrepaiement, $ancienneteproposeur);
  // Mise à jour du fichier -resume.json dans la base du proposeur
  $nouveauresumeaccproposeur = "".$nouveausoldeconsignelproposeur.",".$minimaxproposeur[0].",".$revenujournalierproposeur.",".$minimaxproposeur[1];
  $cheminfichier = tracelechemin($noproposeur,$base,$noproposeur."-resume.json");
  remplacefichier($cheminfichier, $nouveauresumeaccproposeur);
  // Mise à jour du fichier -suiviresume.json dans la base du proposeur
  $cheminfichier = tracelechemin($noproposeur,$base,$noproposeur."-suiviresume.json");  
  ajouteaufichier($cheminfichier,$idtraacc.",".$nouveauresumeaccproposeur."\n");
  // Mise à jour du fichier des fichiers de référence quoi.json et mesvaleursref.json dans la base du proposeur à faire
  
  // renvoi du nouveau résumé de l'accepteur
  return "TACC - ".$nouveauresumeacc;
};

// ajoute au fichier le chemin doit exister la chaine fichier doit inclure son retour chariot
function ajouteaufichier($cheminfichierinclu, $chainefichier){
  $fichierencours = fopen($cheminfichierinclu, 'a'); 
  $chainefichiercrypte = cryptepourstockage($chainefichier);
  fwrite($fichierencours, $chainefichiercrypte);
  fclose($fichierencours);
};

// ajoute au fichier le chemin doit exister la chaine fichier doit inclure son retour chariot
function ajouteaufichier2dates($cheminfichierinclu, $chainefichier){
  $file = $cheminfichierinclu;
  $dernieresidtra = json_decode(decryptelestockage(file_get_contents($file)),true);
  if(!$chainefichier){
    return $dernieresidtra;
  }else{
    if (!$dernieresidtra[0]){$dernieresidtra[3] = $chainefichier;};
    $dernieresidtra[0] = $dernieresidtra[1];
    $dernieresidtra[1] = $chainefichier;};
    $ancienjour = date_format(date_create(substr($dernieresidtra[3],3,8)),"z");
    $nouveaujour = date_format(date_create(substr($chainefichier,3,8)),"z");
    $ecartan = 0+ substr($chainefichier,3,4) - substr($dernieresidtra[3],3,4);
    if($ecartan == 0){ $anciennete = $nouveaujour - $ancienjour; };
    if($ecartan > 0 ){ $anciennete = $nouveaujour + 365 - $ancienjour ; };
    $dernieresidtra[4] = min([$anciennete,365]);
    $dernieresidtra[5] = $ecartan;
    $dernieresidtra[6] = $ancienjour;
    $dernieresidtra[7] = $nouveaujour;
    $dernieresidtra[8] = $dernieresidtra[9] ; 
    $dernieresidtra[9]  = date_format(date_create(date("Ymd")),"z") ;
    $nouveaudernieresidtra = cryptepourstockage(json_encode($dernieresidtra));
    file_put_contents($file, $nouveaudernieresidtra);
    return $dernieresidtra;
  };

// Ajoute la connexion le retour chariot est dans la fonction
function ajoutelesconnexions($cheminfich2,$chainecontenu) {
  $filename = $cheminfich2;
  $contenucrypte = cryptepourstockage($chainecontenu);
  $contenucrypte = $contenucrypte."\n";
  if (is_writable($filename)) {
    if (!$handle = fopen($filename, 'a')) {
      exit;
    };
    if (fwrite($handle, $contenucrypte) === FALSE) {
      exit;
    };
    fclose($handle);
  } else {
  };
}

// annule une transaction 
function annuleproposition($var3,$notransaction){
  $demandeur = $var3;
  $statuttransaction = transactionstatut($demandeur, $notransaction);
  $statut = substr($statuttransaction,0,4);
  If ($statut == "PACT"){ 
    // Ce code autorise l'annulation de la transaction la ligne du fichier suivi suit au 7e caractère
    $idtra = "tra".$notransaction; // chemin du dossier par date
    $cheminfichier = testelechemin($idtra); // chemin dans base2 par date
    $nomfichiertra = "tra".$notransaction.".json";
    $idtraann = "ann".$notransaction; 
    $nomfichierann = "ann".$notransaction.".json";
    $contenufichiertra = substr($statuttransaction ,7); // transaction transférée par statuttransaction
    $jsonenphp = json_decode($contenufichiertra,true);
    if(json_last_error_msg() != "No error"){ return "DTNC - erreur reception proposition";  };         $nomfichiersuivi = substr($idtra,0,14)."-suivi.json";
    if (file_exists($cheminfichier.substr($idtra,0,14)."-suivi.json")) {
      $ligneexiste = FALSE;
      $fichierencours = fopen($cheminfichier.$nomfichiersuivi, 'r');
      while (!feof($fichierencours) && !$ligneexiste) {
        $ligne = decryptelestockage(fgets($fichierencours, 1024));
        list($var31, $var32, $var33, $var34, $var35, $var36, $var37, $var38) = explode(",", $ligne);
        if ($var31 == "\"".$idtra."\""){ $ligneexiste = TRUE; };
      };
    }else{
       return "TRIN - Transaction inconnue"; 
    };    
    $resumecpt = resumecompte($demandeur);
    $derniercompte = explode( ',', $resumecpt );
    $soldeconsigneldisponible = $derniercompte[0];
    $soldeconsignelparjour = $derniercompte[2];
  if($soldeconsignelparjour =="INF"){$soldeconsignelparjour = 10;}; // à faire remplacer par variable inscription
    $compensation = consignelsuivi($jsonenphp,$idtra);
    $consigne = 0;
    $dateaccepte = date("Ymd_Hi");
    $demandeur = preg_replace( "/\D/", "", $demandeur);
    $demandeurchaine = "\"".preg_replace( "/\D/", "", $demandeur)."\"";
    if($compensation[0] == "impact négatif"){ $consigne = -$compensation[1] ; };
    // rembourser le proposeur des dépenses engagées
    $nouveausolde = $soldeconsigneldisponible + $consigne;
    
    // ajout au fichier traxxxxxxxx_xx-suivi.json dans la base des transactions
    $transactionsuivi = "\"ann".$notransaction."\",".$var32.",".$var33.",\"".$dateaccepte."\",".$demandeurchaine.",".$var36.",".$var37.",".$var38;
    ajouteaufichier($cheminfichier.$nomfichiersuivi, $transactionsuivi); 
    // ajout fichier annxxxxxxxx_xxxx_xxxxxxx.json dans la base des transactions
    ajouteaufichier($cheminfichier.$nomfichierann, $transactionsuivi); 
    // ajout au fichier xxxxx-mesproposition.json dans la base du proposeur
    $base=constante("base");
    $nouveautraann = transactionaccann("ann",$idtra,$contenufichiertra,$dateaccepte,$demandeur);
    $cheminsansfichier = tracelechemin($demandeur,$base,$demandeur); 
    ajouteaufichier($cheminsansfichier."-mespropositions.json", $nouveautraann."\n");
    // mise à jour fichier xxxxx-resume2dates.json dans la base du proposeur
    $dernieresidtra = ajouteaufichier2dates($cheminsansfichier."-resume2dates.json",$idtraann);
    $idtraprecedente = $dernieresidtra[0];
    $anciennete = $dernieresidtra[4];
    $nojourancien = $dernieresidtra[8];
    $nojour = $dernieresidtra[9];
    // fichier de chainage des transaction bloc à écrire
    
    // Calcul nouveau solde proposeur $soldeconsigneldisponibleproposeur déjà fait $nouveausolde pas de paiement à faire déjà calculé dans impact

    // Mise à jour du fichier  suivi31jours dans la base du proposeur
    $minimax = suivi31jours($cheminsansfichier."-suivi31jours.json", $nojourancien, $nojour, $nouveausolde);
    // Mise à jour du fichier  gain365jours dans la base du proposeur
    $revenujournalier = gain365jours($cheminsansfichier."-gain365jours.json", $idtraprecedente, $idtra, $consigne, $anciennete);
    // Mise à jour du fichier -resume.json dans la base du proposeur
    $nouveauresumeann = "".$nouveausolde.",".$minimax[0].",".$revenujournalier.",".$minimax[1];
     remplacefichier($cheminsansfichier."-resume.json", $nouveauresumeann);
    // Mise à jour du fichier -suiviresume.json dans la base du proposeur
    ajouteaufichier($cheminsansfichier."-suiviresume.json",$idtraann.",".$nouveauresumeann."\n");
    // Mise à jour du fichier des fichiers de référence quoi.json et mesvaleursref.json dans la base du proposeur à faire
    // renvoi du nouveau résumé de l'accepteur
    return  "DABR - ".$nouveauresumeann; 
  };
  If ($statut == "PACC"){ return "TEST - impossible déjà acceptée ".$statut; };
  If ($statut == "PANN"){ return "TEST - impossible déjà annulée ".$statut; };
  If ($statut == "PEXP"){ return "TEST - impossible déjà expirée ".$statut; };
  If ($statut == "ADAC"){ return "TEST - impossible vous avez accepté cette proposition".$statut; };
  return "TNDI - Cette proposition n'est pas disponible";
}; // fin d'anulation de la transaction

// nettoie les entrées texte qui doivent avoir un format json et ne pas poser de problème javascript
function antitagnb($entree){
  if(($entree=="undefined") || ($entree=="")){
    $entree="";
  }else{
// décode si transfert codé
    $entree = decrypteletransfert($entree);
//$entree = preg_replace( "/(encode pour transfert )/", '', $entree);
// fin du décodage
// nettoyage de la demande de transaction
    $entree = preg_replace( '/[^\d_]/', '', $entree);
    return $entree;
  };
}; // 

// nettoie les entrées texte qui doivent avoir un format json et ne pas poser de problème javascript
function antitaghtml($entree){
  if(($entree=="undefined") || ($entree=="")){
    $entree="";
  }else{
// décode si transfert codé
    $entree = decrypteletransfert($entree);
// $entree = preg_replace( "/(encode pour transfert )/", '', $entree);
// fin du décodage
// protection contre les scripts Attention ne doit pas corrompre l'identifiant de transaction voir nettoiedemandetransaction()
    $entree = preg_replace( "/<|(&lt;)/", 'ᐸ', $entree);
    $entree = preg_replace( "/>|(&gt;)/", 'ᐳ', $entree);
    $entree = preg_replace( "/&|(&amp;)/", 'ୡ', $entree);
    $entree = preg_replace( "/\b(script)\b/", 'scr¡pt', $entree);
    $entree = preg_replace( "/\b(style)\b/", 'sty¦e', $entree);
    return $entree;
  };
}; 

// cherche la proposition de transaction
function cherchetransaction($var3,$notransaction){
  $demandeur = $var3;
  $statuttransaction = transactionstatut($demandeur, $notransaction);
  $debut = substr($statuttransaction,0,7);

// debut == "ADAC - "  Proposition déjà acceptée par vous 
// debut == "PACC - " Cette proposition faite par vous a déjà été acceptée
// debut  == "TNDI - " Cette proposition n'est pas disponible 
// debut  == "PANN - " Proposition déjà annulée par vous
// debut  == "PEXP - " Proposition de votre part expirée sans être acceptée
// debut  == "PACT - " Proposition de votre part active
// debut  == "DTAO - " Proposition par autre droit d'accepter
// debut  == "TRIN - " Transaction inconnue" ; }; // Transaction inconnue
// debut  == "TNDI - " Cette proposition n'est pas disponible " ; };
  return $statuttransaction;

  $notransactionlocal = "tra".$notransaction;
//  $cheminfichier = ouvrelechemin($notransactionlocal);
  $cheminfichier = testelechemin($notransactionlocal);
  $nomfichier = substr($notransactionlocal,0,14)."-suivi.json";
  if (file_exists($cheminfichier.$nomfichier)) {
    $transactionsuivi = $notransactionlocal;
$autretesteur = FALSE; // Testeur de truc spécial trouvé dans la ligne
$fichierencours = fopen($cheminfichier.$nomfichier, 'r'); // ouverture en lecture
$nbauteurstra = 0; // initialise le nombre d'auteurs ayant fait la même transaction
while (!feof($fichierencours)) { // cherche dans les lignes
$ligne = decryptelestockage(fgets($fichierencours, 1024)); // ligne par ligne
list($var21, $var22, $var23, $var24, $var25, $var26, $var27, $var28) = explode(",", $ligne);
if ($var21 == "\"".$notransactionlocal."\""){$memetransaction = TRUE;}else{$memetransaction = FALSE;}; 
$var3chaine = "\"".$var3."\"\n"; // attention au " et à la fin de ligne
if ($var28 == $var3chaine){$memeauteur = TRUE;}else{$memeauteur = FALSE;};
if ($memetransaction == TRUE){ // transaction existe
  if( (testdestinataire($var25,$var3chaine) == "autorise")||( $memeauteur == TRUE)){
    $notetra = "oui";
  }else{
    $notetra = "non";  
return "TRIN - Transaction inconnue erreur destinataire " ;// destinataire non autorisé
};

$nbauteurstra = $nbauteurstra + 1;
if ($memeauteur == TRUE) { // trouvé identique
  if ($nbauteurstra == 1){
// PACT ma proposition active PEXP ma proposition exiprée PACC ma proposition acceptée PANN ma proposition annulée
    $expiration = testeexpiration($var21,$var26);
if ($expiration == "expire"){ $debut= "PEXP - "; }; // C'est ma proposition expirée
$debut="PACT - ";
}else{
  if("\"acc".substr($var21,4)==$var21){ $debut= "PACC - ";};
  if("\"ann".substr($var21,4)==$var21){ $debut= "PANN - ";};
  $debut="DTAP - ";
};
// $nomfichier2 = $notransactionlocal.".json";
$nomfichier2 = $notransactionlocal.".json";
$fichierencours2 = fopen($cheminfichier.$nomfichier2, 'r');
$ligne2 = decryptelestockage(fgets($fichierencours2, 1024)); // ligne par ligne
$contenutransaction = $debut.$ligne2;
}else{
if ($nbauteurstra == 1){$debut="DTBR - ";}else{$debut="DTAP - ";}; // pas le même auteur
$nomfichier2 = $notransactionlocal.".json";
$fichierencours2 = fopen($cheminfichier.$nomfichier2, 'r');
$ligne2 = decryptelestockage(fgets($fichierencours2, 1024)); // ligne par ligne
// besoin de vérifier le disponible ici avant de le mettre dans contenu transaction
$contenutransaction = $debut.$ligne2;
};
};
}; // Fin de while cherche dans les lignes
fclose($fichierencours); // fermeture du fichier
if($contenutransaction == ""){
  $contenutransaction = "DTMR Transaction inconnue à cette heure à ".$_SERVER['HTTP_HOST'];
};
}else{
//demande transaction mal reçue pas de fichier
  $contenutransaction = "DTMR pas de transaction à cette date (ou heure) à ".$_SERVER['HTTP_HOST'];
};
return $contenutransaction;
};

// Contenu d'une transaction latransaction = contenutra( "cheminfichier"."traxxxx.json" )
function contenutra( $chemincomplet ){
  $fichierencours = fopen($chemincomplet, 'r');
  $contenutra = decryptelestockage(fgets($fichierencours, 1024));
  return $contenutra;
};

// Code la variable
function codelenom($variable){
  $variablelocale=$variable; $tableaucar=str_split($variablelocale);
  $nbcar=count($tableaucar); $totalvariable=0;
  for ($i = 1; $i <= $nbcar; $i++) { $totalvariable+=ord($tableaucar[$i-1])*(($i-1)*10+1); };
    return $totalvariable;
};

// renvoi des valeurs de compensation d'impact
function consignelsuivi($propositionenjson,$nompropostion){
  $consignelsuivi = ["test",0,0];
  $nooffretra = $propositionenjson[$nompropostion][0];
  $nodemandetra = $propositionenjson[$nompropostion][1];
  $nodatetra = $propositionenjson[$nompropostion][2];
  $offtra = "off".$nodatetra."_".$nooffretra;
  $consignelsuivi[1] =  $propositionenjson[$offtra][3]*$propositionenjson[$offtra][1];
  $demtra = "dem".$nodatetra."_".$nodemandetra;
  $consignelsuivi[2] = $propositionenjson[$demtra][3]*$propositionenjson[$demtra][1];
  $impact = $consignelsuivi[1] + $consignelsuivi[2];
  if ($impact == 0){ $consignelsuivi[0]= "zéro impact"; };
  if ($impact < 0){ $consignelsuivi[0]= "impact négatif"; };
  if ($impact > 0){ $consignelsuivi[0]= "impact positif"; };

  return $consignelsuivi;
};

// crypte pour stockage
function cryptepourstockage($chaineenclair){
  $chainecrypte = $chaineenclair;
  $chainecrypte = "encode pour stokage ".$chainecrypte;
  return $chainecrypte;
};

// crypte pour envoi
function cryptepourtransfert($chaineenclair){
  $chainecrypte = $chaineenclair;
  $chainecrypte = "encryptage serveur ".$chainecrypte;
  return $chainecrypte;
};

// decrypte le stockage
function decryptelestockage($chainecrypte){
  $chaineenclair = $chainecrypte;
  $chaineenclair = preg_replace( "/(encode pour stokage )/", '', $chaineenclair);
  return $chaineenclair;
};

// decrypte le transfert
function decrypteletransfert($chainecrypte){
  $chaineenclair = $chainecrypte;
  $chaineenclair = preg_replace( "/(encode pour transfert )/", '', $chaineenclair);
  return $chaineenclair;
};

// Vérifie le dernier état
function dernieretat($codecompte){
  $codecomptelocale=$codecompte; 
//vérifie dans fichier $codecompte et codelenom(resumefichier)
  $compilecompte = 0;
  return $compilecompte;
};

// Renvoi le contenu du fichier
function fichierperso($var3,$nomfichier){
  $identifiantlocal=$var3; 
  $nomfichierlocal=$nomfichier; 
  $base=constante("base");
  $cheminfichier = tracelechemin($identifiantlocal,$base,$identifiantlocal."-".$nomfichierlocal.".json");
  if (file_exists($cheminfichier)) { // vérification du résumé le fichier existe
    $fichierencours = fopen($cheminfichier, 'r'); // ouverture en lecture
    while (!feof($fichierencours) ) { // cherche dans les lignes
      $contenufichier = decryptelestockage(fgets($fichierencours,1024)); // fichier au complet
      echo($contenufichier."<br>");
    }; // Fin de cherche dans les lignes
    fclose($fichierencours); // fermeture du fichier
  }else{
  //   ne rien renvoyer Fichier non trouvé pas d'utilisateur
  };
  return $contenufichier;
};

// garde trace des gains et  retourne le montant journalier pour la durée demandée
function gain365jours($cheminfichier, $numancienjour, $numnouveaujour, $gain, $duree=365){
  $fichier = $cheminfichier;
  $ancienjour = $numancienjour;
  $nouveaujour = $numnouveaujour;
  $gainconsignel = json_decode(decryptelestockage(file_get_contents($fichier)),true);
  if( $ancienjour <> $nouveaujour){ unset($gainconsignel[$nouveaujour]) ; };
  if ($gain <= 0){  
  }else{
     if( $ancienjour == $nouveaujour){ 
      $gainconsignel[$nouveaujour] += $gain; 
    }else{
       $gainconsignel[$nouveaujour] = $gain;
    };
  }; 
  if ($nouveaujour  >  $ancienjour){ 
    for ($i = $ancienjour+1; $i <= $nouveaujour-1; $i++) { unset($gainconsignel[$i]); }; 
  }else{
    if($nouveaujour == $ancienjour){
    }else{
      for ($i = 0; $i <= $nouveaujour-1; $i++) { unset($gainconsignel[$i]); };    
      for ($i = $ancienjour+1; $i <= 365; $i++) { unset($gainconsignel[$i]); }; 
    }; 
  };  
  $gainjson = cryptepourstockage(json_encode($gainconsignel));
  file_put_contents($fichier, $gainjson);
  $revenuconsignel = round(array_sum( $gainconsignel )/$duree,2); 
  if( (!$gainconsignel) || ($revenuconsignel <10 ) ){(int)$revenuconsignel=10;}; 
  return $revenuconsignel;
};

// initialise la base de données
function initialisefichier($numerofichier="",$nombase="",$nomfichier=""){
  $base = constante($nombase);
  $numfichier= $numerofichier;
  $lefichier= $nomfichier;
  $basedemarrage=substr($base,0,strlen($base)-1)."-demarrage/";
  $cheminfichier = tracelechemin($numfichier,$base,"","ouvre");
  $cheminfichier1 = tracelechemin($numfichier,$basedemarrage,$lefichier);
  $cheminfichier2 = tracelechemin($numfichier,$base,$lefichier);
  copy($cheminfichier1,$cheminfichier2);
};

// nettoie les entrées texte qui doivent avoir un format json et ne pas poser de problème javascript
function inputvalide($entree){
  if(($entree=="undefined") || ($entree=="")){
    $entree="";
  }else{
    $entree = preg_replace( "/\b(script)\b/", 'scr¡pt', $entree);
    $entree = preg_replace( "/\b(style)\b/", 'sty¦e', $entree);
    $entree = preg_replace( "/ {2,}/", ' ', $entree);
    $entree = preg_replace( "/^ | $/", '', $entree);
    $entree = preg_replace( "/\"/", 'ʺ', $entree);
    $entree = preg_replace( "/\'/", '’', $entree);
    return $entree;
  };
}; 

// inverse l'offre et la demande d'une proposition
function inversetransaction($idtra,$contenufichiertra,$dateaccepte,$noproposeur){
  $idtra2 = substr($idtra,3,14);
  $nooffre = "off".$idtra2; $cherchenooffre = "/(".$nooffre.")/"; 
  $nodemande = "dem".$idtra2; $cherchenodemande = "/(".$nodemande.")/"; 
  $nointerim = "ttt".$idtra2; $chercheinterim = "/(".$nointerim.")/"; 
  $acclocal = preg_replace( $cherchenooffre, $nointerim , $contenufichiertra);
  $acclocal = preg_replace( $cherchenodemande, $nooffre , $acclocal);
  $acclocal = preg_replace( $chercheinterim, $nodemande , $acclocal);
  $notra = "acc".$idtra2; $cherchenotra = "/(tra".$idtra2.")/"; 
  $acclocal = preg_replace( $cherchenotra, $notra , $acclocal);
  $acclocalenphp = json_decode($acclocal,true);
  $notra = "acc".substr($idtra,3);
  $nooffretra = $acclocalenphp[$notra][0];
  $nodemandetra = $acclocalenphp[$notra][1];
  $acclocalenphp[$notra][0] = $nodemandetra;
  $acclocalenphp[$notra][1] = $nooffretra;
  $acclocalenphp[$notra][2] = $dateaccepte;
  $acclocalenphp[$notra][3] = $noproposeur;
  $acclocal = json_encode($acclocalenphp);
  return $acclocal;
};

// supprime les références aux sessions obsoletes
function nettoyagerefsessions(){
  $today = getdate();  $heureutilise = $today[hours]*60+$today[minutes];
$heureobsolete = $heureutilise-20; // sessions obsolete 20 minutes
$baseutilisateurs = constante("baseutilisateurs");
$cheminfichier = tracelechemin("",$baseutilisateurs,".baseconsignel0");
if (file_exists($cheminfichier)) { // vérification fichier sessions obsoletes existe
$fichierencours = fopen($cheminfichier, 'r+'); // ouverture en lecture ecriture autorisée pointeur au début
while (!feof($fichierencours) ) { // cherche dans les lignes
$ligne = fgets($fichierencours, 1024); // ligne par ligne
$lignedenclair = decryptelestockage($ligne);
list($var1, $var2, $var3, $var4, $var5) = explode(",", $lignedenclair); // $var5 heure session
if (($var5 < $heureobsolete) || ($var5 > $heureutilise)){ // trouvé date obsolete
  file_put_contents($cheminfichier, str_replace($ligne, "", file_get_contents($cheminfichier)));
}; // fin du trouvé obsolete
}; // Fin de cherche dans les lignes
fclose($fichierencours); // fermeture du fichier
}else{
die("fichier inconnu Fichier non trouvé pas de fichier session "); // Fichier non trouvé pas de fichier session
};
};

// note la proposition de transaction
function notetransaction($var3,$nomfichier,$contenufichier){
  // $var3 pour l'utilisateur chiffré, $nomfichier ex mespropositions, $contenu fichier json
  $identifiantlocal = $var3; 
  $nomfichierlocal = $nomfichier; 
  $chainejson = $contenufichier; 

  // vérification préliminaires
  $debut = strpos($chainejson, "tra");
  $fin = strpos($chainejson, "\" :");
  $idtra = substr($chainejson,$debut,$fin-$debut);
  $jsonenphp = json_decode($chainejson,true);
  if(json_last_error_msg() != "No error"){ return "DTNC - erreur reception proposition"; };
  $paiement = paiement($jsonenphp,$idtra);
  if ($paiement[0] == "speculation"){ return "DTIN - erreur speculation"; };
  $consigneloffrepaiement = 0; // -$paiement[1]; propositions de paiements en consignel du proposeur à déduire au moment de la proposition déjà compté par le da↺
  $cheminfichier = ouvrelechemin($idtra); // chemin dans base2 par date
  $nomfichier = substr($idtra,0,14)."-suivi.json";
  $transaction = $chainejson;
  if (file_exists($cheminfichier.$nomfichier)) { // vérification que la proposition existe
    $transactionsuivi = $idtra;
    $ligneexiste = FALSE; // Testeur de boucle ligne existe dans le fichier
    $autretesteur = FALSE; // Testeur de truc spécial trouvé dans la ligne
    $fichierencours = fopen($cheminfichier.$nomfichier, 'r'); // ouverture en lecture
    while (!feof($fichierencours) && !$ligneexiste) { // cherche dans les lignes
      $ligne = decryptelestockage(fgets($fichierencours, 1024)); // ligne par ligne
      list($var11, $var12, $var13, $var14, $var15, $var16, $var17, $var18) = explode(",", $ligne);
      if ($var11 == "\"".$transactionsuivi."\""){$memetransaction = TRUE;}else{$memetransaction = FALSE;}; 
      $var3chaine = "\"".$var3."\"\n"; // attention au " et à la fin de ligne
      if ($var18 == $var3chaine){$memeauteur = TRUE;}else{$memeauteur = FALSE;};
      if (($memetransaction == TRUE) && ($memeauteur == TRUE) ){ // trouvé identique
        return "PDEN - Proposition déjà enregistrée"; $transaction = ""; 
        $ligneexiste = TRUE;
      }else{
      };
    }; // Fin de while cherche dans les lignes
    fclose($fichierencours); // fermeture du fichier
  }else{
  // le fichier n'existe pas $notetra = "oui"; par défaut
  };
  // vérifications complémentaires
  $resumecpt = resumecompte($var3); 
  $derniercompte = explode( ',', $resumecpt );
  $soldeconsigneldisponible = $derniercompte[0];
  $soldeconsignelparjour = $derniercompte[2];
  if($soldeconsignelparjour =="INF"){$soldeconsignelparjour = 10;}; // à faire remplacer par variable inscription
  // $soldedollardisponible = ; $soldemlcdisponible = ; à faire
  $idoff = "off".$jsonenphp[$idtra][2]."_".$jsonenphp[$idtra][0]; // identification de l'offre
  $consigneloffre = $jsonenphp[$idoff][3]; 
  if($consigneloffre > 0){$consigneloffre=0;}; // Le plus ne sera versé que si la proposition est acceptée. Le moins est déduit immédiatement. Il sera remboursé si la proposition est annulée.
  
  // $dollaroffre = $jsonenphp[$idoff][4]; $mlcoffre = $jsonenphp[$idoff][5];
  
  $iddem = "dem".$jsonenphp[$idtra][2]."_".$jsonenphp[$idtra][1]; // identification de la demande
  $consigneldemande = $jsonenphp[$iddem][3]; // $dollaroffre = $jsonenphp[$idoff][4]; $mlcoffre = $jsonenphp[$idoff][5];
  if ((($soldeconsignelparjour * 7) + $consigneloffre + $consigneloffrepaiement)<0){ return "DTCE - Refus dépense ↺onsignel excessive" ;
; $transaction = "";  };
  if (($soldeconsigneldisponible + $consigneloffre + $consigneloffrepaiement)<0){ return "DTMC - Refus solde ↺onsignel insuffisant"; $transaction = "";  };
  
  //  if (($soldedollardisponible + $dollaroffre)<0){ echo "solde dollar insuffisant"; $transaction = "";  };
  //  if (($soldemlcdisponible + $mlcoffre)<0){ echo "solde mlc insuffisant"; $transaction = "";  };
  
  // fichier de détail de la transaction dans consignelbase2
  $transactionindex = $chainejson."\n"; 
  $cheminfichier = ouvrelechemin($idtra); 
  $nomfichier = $idtra.".json";
  ajouteaufichier($cheminfichier.$nomfichier, $transactionindex);
  // fichier des de suivi des transactions dans l'heure courante  dans consignelbase2
  $transactionsuivi = "\"".$idtra."\",\"".implode("\",\"", $jsonenphp[$idtra])."\",\"".$consigneldemande."\",\"".$var3."\"\n";
  $nomfichier = substr($idtra,0,14)."-suivi.json";
  ajouteaufichier($cheminfichier.$nomfichier, $transactionsuivi);
  // fichier des transactions dans le compte de l'utilisateur
  $transaction = preg_replace( "/(],\")|(] ,\")/", "],\n\"", $transaction);
  $transaction = preg_replace( "/^({ )/", "", $transaction);
  $transaction = preg_replace( "/(] })/", "],\n", $transaction);
  $base=constante("base");
  $cheminfichier = tracelechemin($identifiantlocal,$base,$identifiantlocal."-".$nomfichierlocal.".json");  
  ajouteaufichier($cheminfichier,$transaction);
  // fichier de l'ordre des transaction
  $cheminfichier = tracelechemin($identifiantlocal,$base,$identifiantlocal."-resume2dates.json");  
  $dernieresidtra = ajouteaufichier2dates($cheminfichier,$idtra);
  $idtraprecedente = $dernieresidtra[0];
  $anciennete = $dernieresidtra[4];
  $nojourancien = $dernieresidtra[8];
  $nojour = $dernieresidtra[9];
  
  // fichier de chainage des transaction bloc à écrire
  
  // met à jour le solde de consignel
  $nouveausoldeconsignel = ($derniercompte[0]+$consigneloffre +$consigneloffrepaiement);
  // note le solde de consignel sur les 31 derniers jours donne le solde minimum = $minimax[0], et maximum = $minimax[1];
  $cheminfichier = tracelechemin($identifiantlocal,$base,$identifiantlocal."-suivi31jours.json");
  $minimax = suivi31jours($cheminfichier, $nojourancien, $nojour, $nouveausoldeconsignel);
  // vérifier si et comment on met à jour les gains dans la proposition
  $cheminfichier = tracelechemin($identifiantlocal,$base,$identifiantlocal."-gain365jours.json");
  $revenujournalier = gain365jours($cheminfichier, $nojourancien, $nojour, $consigneloffre+$consigneloffrepaiement, $anciennete);
  // vérifier le gain 365jours
  
  // met à jour le résumé de compte
  $nouveauresume = "".$nouveausoldeconsignel.",".$minimax[0].",".$revenujournalier.",".$minimax[1];
  $cheminfichier = tracelechemin($identifiantlocal,$base,$identifiantlocal."-resume.json");  
  remplacefichier($cheminfichier, $nouveauresume);
  // met à jour l'archivage des résumés de compte consignel
  $cheminfichier = tracelechemin($identifiantlocal,$base,$identifiantlocal."-suiviresume.json");  
  ajouteaufichier($cheminfichier,$idtra.",".$nouveauresume."\n");
  // envoi le retour à l'utilisateur - La proposition est en attente d'acceptation
  return "PEAA - ".$nouveauresume;
};

// crée un nouveau chemin de répertoire en fonction de la transaction
function ouvrelechemin($nomtransaction){
  $chemin = $nomtransaction;
  $basehistorique = constante("basehistorique");
  $chemin = $basehistorique.substr($nomtransaction, 3, 4)."/"; if(!is_dir($chemin)){ mkdir($chemin); };
  $chemin .= substr($nomtransaction, 7, 2)."/"; if(!is_dir($chemin)){ mkdir($chemin); };
  $chemin .= substr($nomtransaction, 9, 2)."/"; if(!is_dir($chemin)){ mkdir($chemin); };
  $chemin .= substr($nomtransaction, 12, 2)."/"; if(!is_dir($chemin)){ mkdir($chemin); };
  return $chemin;
};

// teste si la proposition de transaction comporte un achat ou vente de monnaie
function paiement($propositionenjson,$nompropostion){
  $paiement = ["test",0,0,0,0,0,0];
  $nooffretra = $propositionenjson[$nompropostion][0];
  $nodemandetra = $propositionenjson[$nompropostion][1];
  $nodatetra = $propositionenjson[$nompropostion][2];

  $offtra = "off".$nodatetra."_".$nooffretra;
  $listeactsoff = $propositionenjson[$offtra][0];
  $tableaulisteactsoff = explode("act", $listeactsoff);
  $nombreactes = count($tableaulisteactsoff);
  $monnaiedansoffre = 0;
  $typetroc=""; $noact = "";
  $i;
  for ($i = 1; $i < $nombreactes; $i++) {
    $noact = "".substr($tableaulisteactsoff[$i],3);
    $typetroc = queltypetroc($noact) ;
    if ($typetroc != "simpletroc"){
      $monnaiedansoffre = 1; 
      $idact = "off".$nodatetra."_act".$tableaulisteactsoff[$i] ;
      $idactquantite = $propositionenjson[$idact][1];
//      print_r($idact." ".$idactquantite."<br>");
      if($typetroc == "↺"){$paiement[1] += $idactquantite;};
      if($typetroc == "mlc"){$paiement[2] += $idactquantite;};
      if($typetroc =="$"){$paiement[3] += $idactquantite;};
    };
  };
  if ($monnaiedansoffre == 0){ $paiement[0]= "nonspeculation"; };

  $demtra = "dem".$nodatetra."_".$nodemandetra;
  $listeactsdem = $propositionenjson[$demtra][0];
  $tableaulisteactsdem = explode("act", $listeactsdem);
  $nombreactes = count($tableaulisteactsdem);
  $monnaiedansdemande = 0;
  $typetroc=""; $noact = "";
  $i=0;
  for ($i = 1; $i < $nombreactes; $i++) {
    $noact = "".substr($tableaulisteactsdem[$i],3);
    $typetroc = queltypetroc($noact) ;
    if ($typetroc != "simpletroc"){
      $monnaiedansdemande = 1; 
      $idact = "dem".$nodatetra."_act".$tableaulisteactsdem[$i] ;
      $idactquantite = $propositionenjson[$idact][1];
//      print_r($idact." ".$idactquantite."<br>");
      if($typetroc=="↺"){$paiement[4] += $idactquantite;};
      if($typetroc=="mlc"){$paiement[5] += $idactquantite;};
      if($typetroc=="$"){$paiement[6] += $idactquantite;};
    };
  };
  if ($monnaiedansdemande == 0){ $paiement[0]= "nonspeculation"; };
  if ($monnaiedansoffre + $monnaiedansdemande == 2){ $paiement[0]= "speculation"; };

  return $paiement;
};

// range les transactions dans les dossiers
function rangetransaction(){
};

// remplace le contenu du fichier
function remplacefichier($cheminfichierinclu, $chainefichier){
  $fichierencours = fopen($cheminfichierinclu, 'w'); 
// $chainefichiercrypte = cryptepourstockage($chainefichier);
  $chainefichiercrypte = $chainefichier;
  fwrite($fichierencours, $chainefichiercrypte);
  fclose($fichierencours);
};

// Renvoi le résumé du compte
function resumecompte($var3){
  $identifiantlocal=$var3; 
  $base=constante("base");
  $cheminfichier = tracelechemin($identifiantlocal,$base,$identifiantlocal."-resume.json");
  if (file_exists($cheminfichier)) { // vérification du résumé le fichier existe
    $fichierencours = fopen($cheminfichier, 'r'); // ouverture en lecture
    $resumeducompte = decryptelestockage(fgets($fichierencours, 1024)); // ligne par ligne
    fclose($fichierencours); // fermeture du fichier
  }else{
    // ouvrir avec le compte initial
      $resumeducompte = constante("ouverturecompte");
    //    ajouteaufichier($cheminfichier, $resumeducompte);
  };
  return $resumeducompte;
};

// mise en réserve des valeurs dans un tableau de 31 jours retourne [mini,max]
function suivi31jours($cheminfichier, $numancienjour, $numnouveaujour, $solde){
  $fichier = $cheminfichier;
  $ancienjour = $numancienjour;
  $nouveaujour = $numnouveaujour;
  $gainconsignel = json_decode(decryptelestockage(file_get_contents($fichier)),true);
  if( $ancienjour <> $nouveaujour){ unset($gainconsignel[$nouveaujour]) ; };
  $gainconsignel[$nouveaujour] = round($solde,2);
  if ($nouveaujour  >  $ancienjour){ 
    for ($i = $ancienjour+1; $i <= $nouveaujour-1; $i++) { unset($gainconsignel[$i]); }; 
  }else{
    if($nouveaujour == $ancienjour){
    }else{
      for ($i = 0; $i <= $nouveaujour-1; $i++) { unset($gainconsignel[$i]); };    
      for ($i = $ancienjour+1; $i <= 365; $i++) { unset($gainconsignel[$i]); }; 
    }; 
  };  
  $jourmoins31 = $nouveaujour - 31;
  foreach ($gainconsignel as $key => $value)  {
    if ($nouveaujour >= 31){
      if($key <= $jourmoins31){ unset($gainconsignel[$key]); };
      if($key > $nouveaujour){ unset($gainconsignel[$key]); };
    }else{
      if($key > $nouveaujour){ 
        if($key < 365-30+$nouveaujour){ unset($gainconsignel[$key]);  };
      };
    };
  };
  $gainjson = cryptepourstockage(json_encode($gainconsignel));
  file_put_contents($fichier, $gainjson);
  $miniconsignel = min ( $gainconsignel ); 
  $maxconsignel = max ( $gainconsignel ); 
  return [$miniconsignel,$maxconsignel];
};

// pour tester un chemin de répertoire en fonction de la transaction
function testelechemin($nomtransaction){
      $chemin = $nomtransaction;
      $basehistorique = constante("basehistorique");
      $chemin = $basehistorique.substr($nomtransaction, 3, 4)."/"; 
      $chemin .= substr($nomtransaction, 7, 2)."/"; 
      $chemin .= substr($nomtransaction, 9, 2)."/"; 
      $chemin .= substr($nomtransaction, 12, 2)."/";
      return $chemin;
    };

// pour tester si la proposition est encore valide
function testeexpiration($notransaction,$nombredejours){
      $datetransaction = substr($notransaction,4,8); $datetransactionunix = strtotime($datetransaction);
      $nbjour = preg_replace( "/\"/", "", $nombredejours); 
      $datedujour = date("Ymd"); $datedujournunix = strtotime($datedujour);
      $differencedateenjours = ($datedujournunix - $datetransactionunix)/86400;
      if($differencedateenjours > $nbjour){$expiration = "expire";}else{$expiration = "pasexpire";};
      return $expiration;
    };

// pour tester si la proposition est encore valide
function testdestinataire($pourqui,$demandeur){
      $desti = preg_replace( "/\D/", "", $pourqui) ; $tesqui = preg_replace( "/\D/", "", $demandeur) ;
      $autorise = "nonautorise" ; 
      if($desti == "0"){ $autorise = "autorise" ; };
      if($tesqui == $desti){ $autorise = "autorise" ; };
      return $autorise;
    };

// renvoie le chemin d'accès en fonction de l'identifiant 
function tracelechemin($numerofichier,$sousrep,$nomfichier,$defriche="") {
      $nofichier=$numerofichier;
      $accesf[0]=strlen($nofichier);
      $numrep = 0;
      $chemin = "";  $ouvrechemin = "";
      if( $accesf[0] % 2 == 1 ){ $decal=1; }else{ $decal=0; };
      for ($x = $accesf[0]; $x >0 ; $x--) {
        if($x % 2 != 1){
          $numrep +=1;
          $accesf[$numrep] = substr($nofichier, $x-$decal, 2);
          $chemin = $chemin.$accesf[$numrep]."/";
          $ouvrechemin = $sousrep.$chemin; if($defriche=="ouvre"){ if(!is_dir($ouvrechemin)){ mkdir($ouvrechemin); };  };
        };
      };
      if( $accesf[0] % 2 == 1 ){
        $accesf[1] = substr($nofichier, 0, 1);
      }else{
        $accesf[1] = substr($nofichier, 0, 2); 
      };
      $chemin = $chemin.$accesf[1]."/";
      $ouvrechemin = $sousrep.$chemin;  if($defriche=="ouvre"){  if(!is_dir($ouvrechemin)){ mkdir($ouvrechemin); };  };
      return $ouvrechemin.$nomfichier;
    };

// renvoie la proposition acceptée ou annulée
function transactionaccann($prefixe,$idtra,$contenufichiertra,$dateaccepte,$noaccepteur){
  $notra = $prefixe.substr($idtra,3);
  $acclocal = $contenufichiertra;
  $cherchenotra = "/(".$idtra.")/"; 
  $acclocal = preg_replace( $cherchenotra, $notra , $acclocal);
  $acclocalenphp = json_decode($acclocal,true);
  $acclocalenphp[$notra][2] = $dateaccepte;
  $acclocalenphp[$notra][3] = $noaccepteur;
  $acclocal = json_encode($acclocalenphp);
  return $acclocal;
};

// retourne le statut de la transaction ADAC - PACC - PANN - PEXP - AEXP - PACT - DTAO - TNDI ... sans vérification complémentaire
function transactionstatut($demandeur, $notransaction){
  $nodemandeur = $demandeur; 
  $idtra = "tra".$notransaction; 
  $cheminfichier = testelechemin($idtra); // chemin dans base2 par date
  // l'orde des tests est important
  $debut = "";
  if (file_exists($cheminfichier."acc".$notransaction.".json")) { 
    $fichierencours = fopen($cheminfichier."acc".$notransaction.".json", 'r');
    $ligne = decryptelestockage(fgets($fichierencours, 1024)); // une seule ligne
    list($var41, $var42, $var43, $var44, $var45, $var46, $var47, $var48) = explode(",", $ligne);
    if ($var48 == "\"".$nodemandeur."\"\n"){ return "ADAC - ".contenutra($cheminfichier.$idtra.".json"); }; // Proposition déjà acceptée par vous
    if ($var45 == "\"".$nodemandeur."\""){ return "PACC - ".contenutra($cheminfichier.$idtra.".json"); }; // Cette proposition faite par vous a déjà été acceptée
    return "TNDI - Cette proposition n'est pas disponible";
  }; // ADAC - PACC - TNDI
  if (file_exists($cheminfichier."ann".$notransaction.".json")) { 
    $fichierencours = fopen($cheminfichier."ann".$notransaction.".json", 'r');
    $ligne = decryptelestockage(fgets($fichierencours, 1024)); // une seule ligne
    list($var41, $var42, $var43, $var44, $var45, $var46, $var47, $var48) = explode(",", $ligne);
    if ($var48 == "\"".$nodemandeur."\"\n"){ return "PANN - ".contenutra($cheminfichier.$idtra.".json"); }; // Proposition déjà annulée par vous
    return "TNDI - Cette proposition n'est pas disponible";
  }; // PANN - TNDI -
  if (file_exists($cheminfichier."exp".$notransaction.".json")) { 
    $fichierencours = fopen($cheminfichier."exp".$notransaction.".json", 'r');
    $ligne = decryptelestockage(fgets($fichierencours, 1024)); // une seule ligne
    list($var41, $var42, $var43, $var44, $var45, $var46, $var47, $var48) = explode(",", $ligne);
    if ($var48 == "\"".$nodemandeur."\"\n"){ return "PEXP - ".contenutra($cheminfichier.$idtra.".json"); }; // Proposition de votre part expirée sans être acceptée
    if ($var45 == "\"".$nodemandeur."\""){ return "AEXP - ".contenutra($cheminfichier.$idtra.".json"); }; // Cette proposition est expirée
    if ($var45 == "\"0\""){ return "AEXP - ".contenutra($cheminfichier.$idtra.".json"); }; // Cette proposition est expirée
    return "TNDI - Cette proposition n'est pas disponible";
  }; // PEXP - AEXP - TNDI -
  if (file_exists($cheminfichier.substr($idtra,0,14)."-suivi.json")) { 
    // Vérifications dans le fichier -suivi.json proposition de qui pour qui
    // manque vérification plusieurs fois la même ligne DTAP - plusieurs fois même propostion
    $ligneexiste = FALSE;
    $fichierencours = fopen($cheminfichier.substr($idtra,0,14)."-suivi.json", 'r');
    while (!feof($fichierencours) && !$ligneexiste) {
      $ligne = decryptelestockage(fgets($fichierencours, 1024));
      list($var41, $var42, $var43, $var44, $var45, $var46, $var47, $var48) = explode(",", $ligne);
      if ($var41 == "\"".$idtra."\""){
        $memetransaction = TRUE; // transaction trouvée
        $ligneexiste = TRUE;
        $expiration = testeexpiration($var41,$var46);
        if ($var48 == "\"".$nodemandeur."\"\n"){ 
          if ($expiration == "expire"){ expire($demandeur,$notransaction) ;return "PEXP - ".contenutra($cheminfichier.$idtra.".json"); }; // C'est ma proposition expirée 
          // vérifier et faire le traitement d'expiration avant de renvoyer PEXP il y a un problème dans la mise à jour des fichiers
          if ($expiration == "pasexpire"){ return "PACT - ".contenutra($cheminfichier.$idtra.".json"); }; // C'est ma proposition active
        }else{
            $testdestinataire = testdestinataire($var45,$nodemandeur);
          if ($testdestinataire == "autorise"){ return "DTAO - ".contenutra($cheminfichier.$idtra.".json"); }; // J'ai le droit d'accepter cette proposition mais attention à disponibilité"; };
          if ($testdestinataire == "nonautorise"){ return "TNDI - Cette proposition n'est pas disponible "; }; // Cette proposition n'est pas disponible
        };
      }; // fin de transaction trouvée
    }; // fin du while
  }; // PEXP - PACT - DTAO - TNDI
  return "TRIN - Transaction inconnue";
};

// retourne doubletroc = achat ou vente de monnaie; simple troc = troc ou achat ou vente de produits
function queltypetroc($noact){
      $typetroc = "doubletroc";
      $listepaiement = constante("paiements");
      if (strpos($listepaiement, "_".$noact."\"") === FALSE){
        $typetroc = "simpletroc";
      }else{
        if (strpos($listepaiement, "↺_".$noact."\"") != FALSE){return "↺";};
        if (strpos($listepaiement, "mlc_".$noact."\"") != FALSE){return "mlc";};
        if (strpos($listepaiement, "$_".$noact."\"") != FALSE){return "$";};
      };
      return $typetroc;
    };

// définition des constantes selon la localité pour les calculs
function constante($nom){
  if($nom == "paiements"){ return '["$_18702","$_25343","mlc_41642",mlc_51083","↺_629160","↺_721781"]'; };
  if($nom == "ouverturecompte"){ return '"200,100,20,300"'; };
  if($nom == "minimumviable"){ return 15; };
  if($nom == "minimumviableparjour"){ return 37.4; }; // minimumviable * 52semaines * 17,5h / 365j
  if($nom == "salairehmoyen"){ return 30; };
  if($nom == "coefsalairemoyen"){ return 2; };
  if($nom == "coefsalaireindecent"){ return 20; }; //  revenuindecent = dureeactiv * minimumviable * coefsalaireindecent;
  if($nom == "maxcompte"){ return 54600; }; //  = salairehmoyen * 52semaines * 35h;
  if($nom == "base"){ return "../consignel-base/"; }; // pour utilisation depuis le php
  if($nom == "baseutilisateurs"){ return "../consignel-base/0/"; };
  if($nom == "basehistorique"){ return "../consignel-base/2/"; };
  if($nom == "baseavatars"){ return "../consignel-app/"; }; 
  if($nom == "baselocalite"){ return "../localite/"; }; 
};

function baseminimale(){
  initialisefichier("","base",".htaccess");
  initialisefichier("0","base",".baseconsignel0");
  initialisefichier("0","base",".baseconsignel3");
  initialisefichier("2","base","");
  initialisefichier("3535","base","3535-resume.json");
  initialisefichier("11495","base","11495-resume.json");
  initialisefichier("","baselocalite","cherchefaire.json");
  initialisefichier("","baselocalite","chercheparqui.json");
  initialisefichier("","baselocalite","cherchepourqui.json");
  initialisefichier("","baselocalite","cherchequoi.json");
  initialisefichier("","baselocalite","valeursref.json");
};

// inscription d'un nouvel utilisateur
// function nouvelutilisateur(){
// vérification d'utilisateur vivant
// vérification d'utilisateur unique
// inscription des codes de vérification d'utilisateurs dans consignel-base/0/.baseconsignel3
// ouvrelecompte($var3)
// };

?>