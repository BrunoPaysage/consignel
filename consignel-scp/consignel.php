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
if($donnee1==1){echo cryptepourtransfert((" , 0 , Inconnu , Inconnu , Inconnu , efface l'entrée"));}; // session nulle demande remise à zéro et renvoi

// Code secret sans code utilisateur
if(($donnee1==$donnee3) and ($donnee2=="1")){
  echo cryptepourtransfert((" , 0 , Inconnu , Inconnu , Inconnu , secret seul")); // session nulle code secret sans identifiant ne rien faire
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
        list($var1, $var2, $var3, $var4, $var5, $var6) = explode(",", $ligne);
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
    
    $baseavatars = constante("baseavatars");
    $baseavatarperso=constante("baseavatarperso");
    $cheminsansfichier = tracelechemin($var6,$baseavatarperso,"");     
    
    if(!is_dir($cheminsansfichier)) {
        // le répertoire perso n'existe pas prendre l'avatar type
      if(substr($var4,1,6)=="avatar"){
          // si le fichier est par défaut est de tye avatar...png
        $cheminfichierimage = $baseavatars.substr($var4,1);
      }else{
          // sinon le fichier par défaut est un nom de fichier
        $cheminfichierimage = tracelechemin($donnee2,$base,substr($var4,1));
        }; // fin du si avatar ou lien fichier defaut
      }else{
        // le répertoire existe
        $lesfichiers = scandir($cheminsansfichier);
        $avatar=$lesfichiers[count($lesfichiers)-1]." ";
        if(substr($avatar,0,1)!="."){
          // fichier avatar dans image perso
          if(substr($avatar,-4)!=".txt"){
            $cheminfichierimage = substr($cheminsansfichier.$avatar,0,-1);
            $cheminfichierimage = file_get_contents($cheminfichierimage)."|"; 
          }else{
            $cheminfichierimage = $cheminsansfichier.$avatar; 
          };
        }else{
          // pas de fichier avatar dans image perso
          $cheminfichierimage = $baseavatars.substr($var4,1);
        };
      };   
      
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
      echo cryptepourtransfert((" ,".$nombrealeatoire.",".$var3.",".$cheminfichierimage.",".$var5.", "));
  }; // session numéroté demande code secret
  // Fin de l'identifiant a été trouvé
  if($existe==FALSE){ // L'identifiant n'a pas été trouvé
  echo cryptepourtransfert((" , 0 , Inconnu , Inconnu , Inconnu , utilisateur inconnu"));
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
    if(rand(0, 49)==0){$nettoyage = TRUE;}; // Nettoyage de fichiers forcé une fois sur +-50 accès valides
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
      // mise à jour revenu inconditionnel
      $revenu = revenuinconditionnel($var3); 
      // renvoi du résumé de compte
      $resumejson=resumecompte($var3); // 4 variables du résumé (disponible, par jour, dispomini31jours, dispomaxi31jours)
      $idtraprecedant = ""; //  identifiant transaction précédente
      echo cryptepourtransfert((" ,".$var4.",".$oklocal.",".$resumejson.",".$idtraprecedant.", ")); 
    }else{
      // identification secondaire demande de fichier ou transaction
      $lademande = ($donnee3/$var4);
      if($lademande==6986){ $quoi = fichierperso($var3,"quoi"); echo cryptepourtransfert($quoi); }; // fin de "quoi"
      if($lademande==16887){ $resume = fichierperso($var3,"resume"); echo cryptepourtransfert($resume); }; // fin de "resume"
      if($lademande==39629){  $monavatar = noteavatar($var3,$donnee4/$var4); echo cryptepourtransfert($monavatar);  };// fin de "monavatar"
      if($lademande==50625){ $mesprefserveur = serveurmoi($var3,$donnee4,"mesprefserveur"); echo cryptepourtransfert($mesprefserveur); 
      }; // fin de "serveurmoi"
      if($lademande==59570){ $demandeaqui = fichierperso($var3,"demandeaqui"); echo cryptepourtransfert($demandeaqui); }; // fin de "demandeaqui"
      if($lademande==61612){ $nouveaucompte=inscription($var3,$donnee5); echo cryptepourtransfert($nouveaucompte); }; // fin de "inscription"
      if($lademande==71900){  $monavatar = retireavatar($var3,$donnee4/$var4,$donnee5); echo cryptepourtransfert($monavatar);  };// fin de "retireavatar"
      if($lademande==86012){ $mesvaleursref = fichierperso($var3,"mesvaleursref"); echo cryptepourtransfert($mesvaleursref); }; // fin de "mesvaleursref"
      if($lademande==87558){ $noteproposition = notetransaction($var3,"mestransactions",$donnee5); echo cryptepourtransfert($noteproposition); }; // fin de "maproposition"
      if($lademande==116020){ $mestransactions = fichierperso($var3,"mestransactions"); echo cryptepourtransfert($mestransactions); }; // fin de "mestransactions"
      if($lademande==118535){ $mesopportunites = fichierperso($var3,"mesopportunites"); $mesopportunites = testemesopportunites($var3,$mesopportunites) ; echo cryptepourtransfert($mesopportunites); }; // fin de "mesopportunites"
      if($lademande==151695){ $oublieopportunite = retireopportunite($var3,$donnee4); echo cryptepourtransfert($oublieopportunite); 
      }; // fin de "oublieopportunite"
      if($lademande==211910){ $transactionrefusee = refusetransaction($var3,$donnee4); echo cryptepourtransfert($transactionrefusee); }; // fin de "refuseuneproposition"
      if($lademande==211873){ $transactionannulee = annuleproposition($var3,$donnee4); echo cryptepourtransfert($transactionannulee); }; // fin de "annuleuneproposition"
      if($lademande==232828){  
        if ($donnee4==""){ 
          echo cryptepourtransfert("ERDP <br>donnee5 est vide<br>"); 
        }else{  
          $transactiontrouvee = cherchetransaction($var3,$donnee4); echo cryptepourtransfert($transactiontrouvee);
        }; 
      }; // fin de "demandeuneproposition"
      if($lademande==233615){ $transactionacceptee = acceptetransaction($var3,$donnee4); echo ($transactionacceptee); }; // fin de "accepteuneproposition"
    };
  };  // Fin de utilisateur correctement identifié 
  if($existe==FALSE){ // L'utilisateur n'a pas été correctement identifié
  echo cryptepourtransfert(" , 0 , Inconnu , Inconnu , Inconnu , utilisateur inconnu");
  };  // Fin de l'identifiant n'a pas été trouvé
  if($nettoyage == TRUE){ // Le fichier a besoin de nettoyage
    nettoyagerefsessions();
  };  // Fin de du nettoyage du fichier de session
};
// -----------------------

// accepte la proposition de transaction
function acceptetransaction($var3,$notransaction){
    $noaccepteur = $var3; 
    $demandeur = $var3;
    $statuttransaction = transactionstatut($demandeur, $notransaction);
    $debut = substr($statuttransaction,0,4);
    $idtra = "tra".$notransaction; 
    $nomfichiertra = "tra".$notransaction.".json"; 
    $idtraacc = "acc".$notransaction; 
    $nomfichieracc = "acc".$notransaction.".json"; 
    $nomfichierann = "ann".$notransaction.".json"; 
    $nomfichiersuivi = substr($idtra,0,14)."-suivi.json";
    // vérification préliminaires
    $cheminfichier = ouvrelechemin($idtra); // chemin dans base2 par date
    $ligneexiste = FALSE; // Testeur de boucle ligne existe dans le fichier
    $fichierencours = fopen($cheminfichier.$nomfichiersuivi, 'r'); // ouverture en lecture
    while (!feof($fichierencours) && !$ligneexiste) { // cherche dans les lignes
        $ligne = decryptelestockage(fgets($fichierencours, 1024)); // ligne par ligne
        list($var31, $var32, $var33, $var34, $var35, $var36, $var37, $var38) = explode(",", $ligne);
        if ($var31 == "\"".$idtra."\""){
            $ligneexiste = TRUE;
        }; // Fin de transaction trouvée
    }; // Fin de while cherche dans les lignes
    fclose($fichierencours); // fermeture du fichier
    
    // DTAO acceptation d'une proposition non encore acceptée
    //DTAR voir dans le else utiliser une proposition déjà acceptée mais ouverte à tous
    if($debut=="DTAO"){
        // vérifications complémentaires avant d'enregistrer la transaction
        $resumecpt = resumecompte($var3); 
        $derniercompte = explode( ',', $resumecpt );
        $soldeconsigneldisponible = $derniercompte[0];
        $soldeconsignelparjour = $derniercompte[2];
        if($soldeconsignelparjour =="INF"){$soldeconsignelparjour = 10;}; // à faire remplacer par variable inscription
        
        // récupération du contenu de la transaction
        if (file_exists($cheminfichier.$nomfichiertra)) {
            $contenufichiertra = decryptelestockage(file_get_contents($cheminfichier.$nomfichiertra));
            $datetra=substr($idtra,3,8);
            if($datetra<"20200801"){ $contenufichiertra=transactionformat202008($contenufichiertra); };        
        }else{
            return "TRIN - Transaction inconnue erreur accès fichier transaction " ;
        };
    
        // Confirmation d'inscription d'utilisateur unique
        //    $verifutilisateur = substr($contenufichiertra,strpos($contenufichiertra, "_act0001760145\"")); if(substr($verifutilisateur,0,4)=="_act"){$verifutilisateur="oui";}else{$verifutilisateur="non";};
        //    $confirmutilisateur = substr($contenufichiertra,strpos($contenufichiertra, "_act0001759799\"")); if(substr($confirmutilisateur,0,4)=="_act"){$confirmutilisateur="oui";}else{$confirmutilisateur="non";};
        //    if(($confirmutilisateur=="oui")&&($verifutilisateur=="oui")){ 
        //       return "TEST - Vérification à écrire";
        //       ajout des dates de vérification à écrire
        //    };
        
        $jsonenphp = json_decode($contenufichiertra,true);
        if(json_last_error_msg() != "No error"){ return "DTNC - erreur reception proposition"; };
        
        $consigneldemande = preg_replace( "/\"/", "", $var37); 
        $paiement = paiement($jsonenphp,$idtra);
        if ($paiement[0] == "speculation"){ return "DTIN - erreur speculation"; };
        $consigneldemandepaiement = $paiement[1]; // propositions de dons en consignel du proposeur la dépense de l'accepteur  - $paiement[4] est déjà inclue dans le da↺ $consigneldemande 
        $nbjoursenreserve=$soldeconsigneldisponible/$soldeconsignelparjour;
        $nbjoursselonreserve=7;
        if($nbjoursenreserve>30){$nbjoursselonreserve+=7;};
        if($nbjoursenreserve>60){$nbjoursselonreserve+=7;};
        if($nbjoursenreserve>90){$nbjoursselonreserve+=7;};
        if ((($soldeconsignelparjour * $nbjoursenreserve) + $consigneldemande + $consigneldemandepaiement)<0 ){ return "DTCE - Refus dépense ↺onsignel excessive"; $transaction = ""; };
        if (($soldeconsigneldisponible + $consigneldemande + $consigneldemandepaiement)<0 ){ return "DTMC - Refus solde ↺onsignel insuffisant"; $transaction = ""; };
        
        //$soldemlcdisponible = ; à faire
        // $mlcdemandepaiement = $paiement[2] - $paiement[5];
        //  if (($soldemlcdisponible + $mlcdemandepaiement)<0){  "solde mlc insuffisant"; $transaction = ""; };
        
        // $soldedollardisponible = ;  à faire
        // $dollardemandepaiement = $paiement[3] - $paiement[6];
        //  if (($soldedollardisponible + $dollardemandepaiement)<0){  "solde dollar insuffisant"; $transaction = ""; };
        
        // fin des vérifications et des refus, enregistrement du suivi dans les fichiers
        // ajout au fichier traxxxxxxxx_xx-suivi.json dans la base des transactions
        $dateaccepte = date("Ymd_Hi");
        if($var38=="\"DA↺\"\n"){
            $noproposeur = "\"DA↺\"\n";
        //      $var38nombre= preg_replace( "/\D/", "", $var35);
        }else{
            $noproposeur = preg_replace( "/\D/", "", $var38);
            $var38nombre= preg_replace( "/\D/", "", $var38);
            $var38chaine = "\"".preg_replace( "/\D/", "", $var38)."\"";
        };
        
        $transactionsuivi = "\"".$idtraacc."\",".$var33.",".$var32.",\"".$dateaccepte."\",\"".$noaccepteur."\",".$var36.",".$var37.",".$var38;
        ajouteaufichier($cheminfichier.$nomfichiersuivi, $transactionsuivi); 
    
        // ajout fichier accxxxxxxxx_xxxx_xxxxxxx.json dans la base des transactions
        ajouteaufichier($cheminfichier.$nomfichieracc, $transactionsuivi);
        
        // références pour base et pseudo
        $base=constante("base");
        $pseudoaccepteur=lepseudode($var3, "noid");    
        $pseudoaccepteurlettre=antitaglettre($pseudoaccepteur); 
        $pseudoproposeur=antitaglettre(lepseudode($var38nombre, "noid"));
        if($var38=="\"DA↺\"\n"){$pseudoproposeur="DA↺";};
     
        // ajout au fichier xxxxx-mestransactions.json dans la base de l'accepteur
        $nbjoursaccepteur=nbjoursserveur($noaccepteur);
        nettoyagetransactions($noaccepteur,$nbjoursaccepteur);
        if($nbjoursaccepteur>0){
            $nouveautraacc = inversetransaction($idtra,$contenufichiertra,$dateaccepte,$pseudoaccepteurlettre,$pseudoproposeur);
            $cheminsansfichier = tracelechemin($noaccepteur,$base,$noaccepteur); 
            $nouveautraacc2 = substr($nouveautraacc,1,-1).",\n";
            ajouteaufichier($cheminsansfichier."-mestransactions.json", $nouveautraacc2);
        };
        
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
        $dureepersoserveur=nbjoursserveur($noaccepteur);
        $nouveauresumeacc = "".$nouveausoldeconsignel.",".$minimax[0].",".$revenujournalier.",".$minimax[1].",".$dureepersoserveur;
        $cheminfichier = tracelechemin($noaccepteur,$base,$noaccepteur."-resume.json");
        remplacefichier($cheminfichier, $nouveauresumeacc);
        
        // Mise à jour du fichier -suiviresume.json dans la base de l'accepteur
        //    $nbjoursaccepteur=nbjoursserveur($noaccepteur);
        //    nettoyagetransactions($noaccepteur,$nbjoursaccepteur);
        if($nbjoursaccepteur>0){
            $cheminfichier = tracelechemin($noaccepteur,$base,$noaccepteur."-suiviresume.json");  
            ajouteaufichier($cheminfichier,$idtraacc.",".$nouveauresumeacc.",\n");
        };
        
        // mise à jour du fichier mesopportunites dans la base de l'accepteur et du proposeur
        //    $listeopportunite = retiredelaliste($noaccepteur,"mesopportunites",$nomfichiertra);
        $nomopportunte= "acc-".substr($nomfichieracc,3,-5)."-".substr($pseudoaccepteurlettre,1,-1);
        $listeopportunite = ajoutealaliste($noaccepteur,"mesopportunites","\"".$nomopportunte."\"");
        //    $listeopportunite = retiredelaliste($noproposeur,"mesopportunites",$nomfichiertra);
        $listeopportunite = ajoutealaliste($noproposeur,"mesopportunites","\"".$nomopportunte."\"");
    
        // mise à jour du fichier demandeaqui dans la base de l'accepteur et du proposeur
        $lepseudoproposeur = lepseudode($noproposeur);
        $lepseudoaccepteur = lepseudode($noaccepteur);
    
        //    $nbjoursaccepteur=nbjoursserveur($noaccepteur);
        //    nettoyagetransactions($noaccepteur,$nbjoursaccepteur);
        if($nbjoursaccepteur>0){
            $listedemandeaqui = ajoutealaliste($noaccepteur,"demandeaqui",$lepseudoproposeur);
        };
        
        $nbjoursproposeur=nbjoursserveur($noproposeur);
        nettoyagetransactions($noaccepteur,$nbjoursproposeur);
        if($nbjoursproposeur>0){
            $listedemandeaqui = ajoutealaliste($noproposeur,"demandeaqui",$lepseudoaccepteur);
        };
        
        if($var38<>"\"DA↺\"\n"){
            // Mise à jour des fichiers xxxxx-quoi.json et xxxxx-mesvaleursref.json dans la base de l'accepteur
            $lesactivites = extraitlesactivites($contenufichiertra);
            $cheminvaleurs=tracelechemin($noaccepteur,$base,$noaccepteur."-mesvaleursref.json",$defriche="ouvre");
            $ladescriptioncourte=ajoutealaliste($noaccepteur,"quoi",$lesactivites[1]);
            $listevaleursref=listevaleurs($cheminvaleurs,$lesactivites[0]);
            
            // Mise à jour des valeurs moyennes dans la base générale de référence des produits et services à faire
            
            
            $nbjoursproposeur=nbjoursserveur($noproposeur);
            nettoyagetransactions($noproposeur,$nbjoursproposeur);
            if($nbjoursproposeur>0){
                // Mise à jour du fichier xxxxx-mesvaleursref.json dans la base du proposeur
                $cheminvaleurs=tracelechemin($noproposeur,$base,$noproposeur."-mesvaleursref.json",$defriche="ouvre");
                // Ajout du fichier xxxxx-quoi.json dans la base du proposeur
                $ladescriptioncourte=ajoutealaliste($noproposeur,"quoi",$lesactivites[1]);
                $listevaleursref=listevaleurs($cheminvaleurs,$lesactivites[0]);
                // Ajout au fichier xxxxx-mestransactions.json dans la base du proposeur
                $pseudoaccepteur=lepseudode($noaccepteur, "noid");
                $nouveauproacc = transactionaccann("acc",$idtra,$contenufichiertra,$dateaccepte,$pseudoaccepteur);
                $cheminsansfichier = tracelechemin($noproposeur,$base,$noproposeur); 
                $nouveauproacc2 = substr($nouveauproacc,1,-1).",\n";
                ajouteaufichier($cheminsansfichier."-mestransactions.json", $nouveauproacc2);
            };
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
            // mise à jour du fichier accepteurs dans la base du proposeur
            $cheminfichier = tracelechemin($noproposeur,$base,$noproposeur."-accepteurs.json");
            if (strlen($nojour) == 3){ 
                $ladate = substr($dateaccepte,0,4).$nojour; 
            }else{
                if (strlen($nojour) == 2){ 
                    $ladate = substr($dateaccepte,0,4)."0".$nojour; 
                }else{
                    if (strlen($nojour) == 1){ $ladate = substr($dateaccepte,0,4)."00".$nojour; };
                };
            };
            $listeaccepteurs = accepteurs($cheminfichier, $noaccepteur, $ladate);
            // Mise à jour du fichier -resume.json dans la base du proposeur
            $nbjoursproposeur=nbjoursserveur($noproposeur);
            $nouveauresumeaccproposeur = "".$nouveausoldeconsignelproposeur.",".$minimaxproposeur[0].",".$revenujournalierproposeur.",".$minimaxproposeur[1].",".$nbjoursproposeur;
            $cheminfichier = tracelechemin($noproposeur,$base,$noproposeur."-resume.json");
            remplacefichier($cheminfichier, $nouveauresumeaccproposeur);
            // Mise à jour du fichier -suiviresume.json dans la base du proposeur
            $cheminfichier = tracelechemin($noproposeur,$base,$noproposeur."-suiviresume.json");  
            ajouteaufichier($cheminfichier,$idtraacc.",".$nouveauresumeaccproposeur."\n");
        }; // fin du si le proposeur n'est pas le dac
        
        // renvoi du nouveau résumé de l'accepteur
        return "TACC - ".$nouveauresumeacc;
        
    }else{
        if($debut=="DTAR"){
          // mise dans un dossier
          //  if(!is_dir($cheminfichier."acc".$notransaction."/")){ mkdir($cheminfichier."acc".$notransaction."/"); };
          //  ajouteaufichier($cheminfichier."acc".$notransaction."/".$noaccepteur."-".$dateaccepte.".json", $transactionsuivi);
          
          // transformation en proposition
          if (file_exists($cheminfichier.$nomfichiertra)) {
            $contenufichiertra = decryptelestockage(file_get_contents($cheminfichier.$nomfichiertra));
            $datetra=substr($idtra,3,8); 
            if($datetra<"20200801"){ $contenufichiertra=transactionformat202008($contenufichiertra); };
          }else{
            return "TRIN - Transaction inconnue erreur accès fichier transaction " ;
          };
          $dateaccepte = date("Ymd_Hi");
          $noproposeur = preg_replace( "/\D/", "", $var38);
      
      // vérification si la proposition $notra a été faite récement
      $noinitial=substr($idtra,16); 
      $notra="tra".$dateaccepte.$noinitial;
      $cheminfichier = ouvrelechemin($notra); // chemin dans base2 par date
      $nomfichiersuivi= substr($notra,0,14)."-suivi.json";
      if (file_exists($cheminfichier.$nomfichiersuivi)) {
        $ligneexiste = FALSE; // Testeur de boucle ligne existe dans le fichier
        $fichierencours = fopen($cheminfichier.$nomfichiersuivi, 'r'); // ouverture en lecture
        while (!feof($fichierencours) && !$ligneexiste) { // cherche dans les lignes
          $ligne = decryptelestockage(fgets($fichierencours, 1024)); // ligne par ligne
          list($var41, $var42, $var43, $var44, $var45, $var46, $var47, $var48) = explode(",", $ligne);
          list($var41a,$var41b,$var41c) = explode("_", $var41); 
          $var41c=substr($var41c,0,-1);
          $var48 = preg_replace( "/\D/", "", $var48);
          $noinitial2=substr($noinitial,1);
         if ($var41c == $noinitial2){ // même numéro d'offre
            if($var48 == $var3){ // même proposeur
              return "PTDE - ||".$idtra;
              $ligneexiste = TRUE;
            };
          }; // Fin de transaction trouvée
        }; // Fin de while cherche dans les lignes
        fclose($fichierencours); // fermeture du fichier
      };  
      
      $nouveaucontenutra = passedemande($idtra,$contenufichiertra,$dateaccepte,$noproposeur);
      $debut = strpos($nouveaucontenutra, "tra");
      $fin = strpos($nouveaucontenutra, "\" :");
      $idnouveautra = substr($nouveaucontenutra,$debut,$fin-$debut);  
      // Si la transaction existe renvoyer déjà enregistrée
      // sinon
      $noteproposition = notetransaction($var3,"mestransactions",$nouveaucontenutra);  
      if(substr($noteproposition,0,4)=="PEAA"){
        return "PTDD - ".substr($noteproposition,7)."||".$idnouveautra;
      }else{
        return $noteproposition;
      };
       // fin du si enregistrée ou pas
      
    }else{
      // ni DTAO ni DTAR
      return "TEST - Réponse serveur: Proposition innacceptable type ".$debut;
    };
  };
};

// garde les accepteurs dans la dernière année et retourne leur liste
function accepteurs($cheminfichier, $numaccepteur, $nouveaujour){
  $fichier = $cheminfichier;
  $accepteur = $numaccepteur;
  if(substr($accepteur,0,1)=="\""){$accepteur=substr($accepteur,1);$accepteur=substr($accepteur,0,-1);};
  $jour = $nouveaujour;
  $obsolete = $jour - 1000;
  $listeaccepteurs="";
  // ouvrir le fichier json
  $accepteursdates = json_decode(decryptelestockage(file_get_contents($fichier)),true);
  // mettre à jour avec la dernière entrée
  if($accepteur != "DA↺"){  $accepteursdates[$accepteur] = $jour; };
  // Nettoyage des dates obsoletes
  foreach ($accepteursdates as $cle => $valeur) {
    if($valeur<$obsolete){unset($accepteursdates[$cle]);}else{$listeaccepteurs = $listeaccepteurs.$cle.",";};
  };
  $listeaccepteurs = "[".substr($listeaccepteurs,0,-1)."]";

  // enregistre le fichier modifié
  $accepteurscryptes = cryptepourstockage(json_encode($accepteursdates));
  file_put_contents($fichier, $accepteurscryptes);
  // retourne lla liste des accepteurs
  return $listeaccepteurs;
};

// ajoute une chaine dans une liste triée de type tableau de chaines
function ajoutealaliste($var3,$nomfichier,$item){
  $contenufichier = "".fichierperso2($var3,$nomfichier);
  $base=constante("base");
  if($contenufichier[1]=="\n"){ 
    if($contenufichier==""){$virgule="";}else{$virgule=",\n";};
  }else{
    if($contenufichier==""){$virgule="";}else{$virgule=",";};
  };
  $contenufichier = substr($contenufichier,1,strlen($contenufichier)-2);
  $contenufichier = $contenufichier.$virgule."".$item."";
  $tableaucontenufichier = explode(",",$contenufichier);
  natsort($tableaucontenufichier);
  $tableaucontenufichier = array_unique($tableaucontenufichier);
  if( !$tableaucontenufichier[0] ){ unset( $tableaucontenufichier[0] ); }; 
  $contenufichier = implode(",",$tableaucontenufichier);
  $contenufichier = "[".$contenufichier."]";
  if($contenufichier[1]==","){ $contenufichier="[".substr($contenufichier,2);  };
  if($contenufichier[1]=="\n"){ if($contenufichier[2]==","){ $contenufichier="[\n".substr($contenufichier,3); }; };
  if($contenufichier[1]=="\n"){ if($contenufichier[2]=="\n"){ $contenufichier="[\n".substr($contenufichier,3); }; };
  $cheminfichierinclu = tracelechemin($var3,$base,$var3."-".$nomfichier.".json");
  ajouteaufichier($cheminfichierinclu, $contenufichier,"debut");
  return $contenufichier;
};

// ajoute au fichier le chemin doit exister la chaine fichier doit inclure son retour chariot
function ajouteaufichier($cheminfichierinclu, $chainefichier,$bout="fin"){
  if($bout=="debut"){
    $fichierencours = fopen($cheminfichierinclu, 'w'); 
  }else{
    $fichierencours = fopen($cheminfichierinclu, 'a'); 
  };
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
        $dernieresidtra[1] = $chainefichier;
    };
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
  };


// ajoute utilisateur
function ajouteutilisateur($numutilisateur,$detailutilisateur){
    $base = constante("base"); $avatar = constante("avatar"); $localite = constante("localite");
    $baseutilisateurs = constante("baseutilisateurs");
    $cheminfichier = tracelechemin("",$baseutilisateurs,".baseconsignel3");
    if($detailutilisateur=="nompublic"){ 
      $detailutilisateur= ",61612,\"inscription\",\"".$avatar."\",\"".$localite."\",61612,";
    };
    $inscriptionutilisateur = "\n".$numutilisateur.$detailutilisateur;
    $fichierencours = fopen($cheminfichier, 'a'); 
    fwrite($fichierencours, $inscriptionutilisateur);
    fclose($fichierencours);
    return "inscription".$numutilisateur;
  };

// annule une transaction 
function annuleproposition($var3,$notransaction,$prefixe="ann"){
    $nodemandeur = $var3;
    $demandeurchaine = "\"".$var3."\"";
    if($prefixe=="ann"){
        // arrivage direct de la demande d'anulation on procède à la vérification du statut
      $statuttransaction = transactionstatut($nodemandeur, $notransaction);
      $statut = substr($statuttransaction,0,4);
      $annexp = "ann";
      // statut PACT ou PEXP pour annulation
    }else{
        // suite à une vérification du statut sans préjuger du demandeur
      $statuttransaction = $prefixe;
      $statut = substr($statuttransaction,0,4);
      $annexp = "exp";
      if($statut == "TREF"){ $annexp = "ann"; };
      // statut AEXP ou TREF pour annulation au autre sans annulation possible
    };
    
    If (($statut == "PACT")||($statut == "PEXP")||($statut == "AEXP")||($statut == "TREF")){ 
        // Ce code autorise l'annulation de la transaction la ligne du fichier suivi suit au 7e caractère
        $idtra = "tra".$notransaction; // chemin du dossier par date
        
        $cheminfichier = testelechemin($idtra); // chemin dans base2 par date
        $nomfichiertra = "tra".$notransaction.".json";
        $idtraann = $annexp.$notransaction; 
        $nomfichierann = $annexp.$notransaction.".json";
        if($statut=="TREF"){$debuttra = strpos($statuttransaction, "{");}else{$debuttra=7;};
        $contenufichiertra = substr($statuttransaction ,$debuttra); // transaction transférée par statuttransaction
        $jsonenphp = json_decode($contenufichiertra,true);
        if(json_last_error_msg() != "No error"){ return "DTNC - erreur reception proposition " ; };         
        $nomfichiersuivi = substr($idtra,0,14)."-suivi.json";
        // récupération du suivi de la transaction
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
        
        $nodestinataire=preg_replace( "/\D/", "", $var35);
        $noproposeur=preg_replace( "/\D/", "", $var38);
        if($var35=="0" && $noproposeur!=$var3){ return "TEST - Vous n'êtes ni destinataire ni propriétaire de la transaction"; }; // déjà fait par transactionstatut() mais au cas
        
        $resumecpt = resumecompte($noproposeur);
        $derniercompte = explode( ',', $resumecpt );
        $soldeconsigneldisponible = $derniercompte[0];
        $soldeconsignelparjour = $derniercompte[2];
        
        if($soldeconsignelparjour =="INF"){$soldeconsignelparjour = constante("minimumviable");}; 
        $compensation = consignelsuivi($jsonenphp,$idtra);
        $consigne = 0;
        if($compensation[1] < 0 ){ $consigne = -$compensation[1] ; };
        // rembourser le proposeur des dépenses engagées au moment de la proposition
        $nouveausolde = $soldeconsigneldisponible + $consigne;
        
        $dateaccepte = date("Ymd_Hi");
        
        if($statut == "PACT"){ $destinatairechaine = $demandeurchaine; $nomdestinataire = lepseudode($noproposeur); }; // proposition faite par le demandeur et active $demandeurchaine;
        if($statut == "PEXP"){ $destinatairechaine = $demandeurchaine; $nomdestinataire = lepseudode($noproposeur); }; // proposition faite par le demandeur et expirée
        if($statut == "AEXP"){ $destinatairechaine = $var35; $nomdestinataire = lepseudode($nodestinataire); }; // proposition par un autre autorisée pour le demandeur mais expirée
        if($statut == "TREF"){ $destinatairechaine = $var35; $nomdestinataire = lepseudode($nodestinataire); }; // Transaction faite par un autre au demandeur et refusée par le demandeur
        
        // base des transactions
        // ajout au fichier traxxxxxxxx_xx-suivi.json dans la base des transactions $cheminfichier base2
        $transactionsuivi = "\"".$annexp.$notransaction."\",".$var32.",".$var33.",\"".$dateaccepte."\",".$destinatairechaine.",".$var36.",".$var37.",".$var38;
        ajouteaufichier($cheminfichier.$nomfichiersuivi, $transactionsuivi); 
        
        // ajout fichier ann(exp)xxxxxxxx_xxxx_xxxxxxx.json dans la base des transactions
        ajouteaufichier($cheminfichier.$nomfichierann, $transactionsuivi); 
        
        
        // base du proposeur
        // ajout au fichier xxxxx-mestransactions.json dans la base du proposeur
        $base=constante("base");
        $basedac=constante("basedac");
        if($var38=="\"DA↺\"\n"){
            $cheminsansfichier = substr(tracelechemin("",$basedac,""),0,-1); // DA↺
            $nomproposeur="DA↺";$noproposeur="DA↺";
        }else{
            $cheminsansfichier = tracelechemin($noproposeur,$base,$noproposeur); 
            $nomproposeur= lepseudode($noproposeur);
        };        
        $nouveautraref = transactionaccann($annexp,$idtra,$contenufichiertra,$dateaccepte,$nomdestinataire,$nomproposeur);
        $nouveautraref2 = substr($nouveautraref,1,-1).",\n";
        $nbjoursproposeur=nbjoursserveur($noproposeur); nettoyagetransactions($noproposeur,$nbjoursproposeur);
        if($nbjoursproposeur==""){ if($nomproposeur=="DA↺"){ $nbjoursproposeur=constante("memoiredac"); }; };

        if($nbjoursproposeur>0){ ajouteaufichier($cheminsansfichier."-mestransactions.json", $nouveautraref2); };
        
        // modification au fichier xxxxx-mesopportunites.json dans la base du proposeur
        if($nbjoursproposeur>0){ 
            $listeopportunite = retiredelaliste($noproposeur,"mesopportunites",$nomfichiertra);
            $listeopportunite = ajoutealaliste($noproposeur,"mesopportunites","\"".$nomfichierann."\"");
        };

        // mise à jour fichier xxxxx-resume2dates.json dans la base du proposeur
        $dernieresidtra = ajouteaufichier2dates($cheminsansfichier."-resume2dates.json",$nomfichierann);
        $idtraprecedente = $dernieresidtra[0];
        $anciennete = $dernieresidtra[4];
        $nojourancien = $dernieresidtra[8];
        $nojour = $dernieresidtra[9];
        
        // fichier de chainage des transactions bloc à écrire
                
        // Mise à jour du fichier  suivi31jours dans la base du proposeur
        $minimax = suivi31jours($cheminsansfichier."-suivi31jours.json", $nojourancien, $nojour, $nouveausolde);
        // Mise à jour du fichier  gain365jours dans la base du proposeur
        $revenujournalier = gain365jours($cheminsansfichier."-gain365jours.json", $nojourancien, $nojour, $consigne, $anciennete);
        // Mise à jour du fichier -resume.json dans la base du proposeur        
        $nouveauresumeann = "".$nouveausolde.",".$minimax[0].",".$revenujournalier.",".$minimax[1].",".$nbjoursproposeur;
        remplacefichier($cheminsansfichier."-resume.json", $nouveauresumeann);
        // Mise à jour du fichier -suiviresume.json dans la base du proposeur
        ajouteaufichier($cheminsansfichier."-suiviresume.json",$idtraann.",".$nouveauresumeann."\n");


        // base du destinataire
        if($nodestinataire != 0){
            // ajout au fichier xxxxx-mestransactions.json dans la base du destinataire
            $nbjoursdestinataire=nbjoursserveur($nodestinataire); nettoyagetransactions($nodestinataire,$nbjoursdestinataire);
            if($nbjoursdestinataire>0){
                $chemindestinataire=tracelechemin($nodestinataire,$base,$nodestinataire);
                ajouteaufichier($chemindestinataire."-mestransactions.json", $nouveautraref2);
            };
           
            // modification au fichier xxxxx-mesopportunites.json dans la base du destinataire
            $listeopportunite = retiredelaliste($nodestinataire,"mesopportunites",$nomfichiertra);
            $listeopportunite = ajoutealaliste($nodestinataire,"mesopportunites","\"".$nomfichierann."\"");
        };  
    
        
        //  cas particulier de refus au moment de l'inscription
        if($demandeurchaine != "0"){
            // à écrire si refus lors d'une opération d'inscription renvoi d'un message donnant la procédure de fermeture du compte
            $inscritutilisateur = substr($contenufichiertra,strpos($contenufichiertra, "_act0001644192\"")); if(substr($inscritutilisateur,0,4)=="_act"){ 
                $nomdemandeur= lepseudode($nodemandeur);
                $toto= supprimeutilisateur($nodemandeur,$nomdemandeur);
                $toto= supprimedossierutilisateur($nodemandeur);
                return "NURI - ".$contenufichiertra; // Demande d'annulation du compte à confirmer
            };
        };
        
        // renvoi du nouveau résumé du proposeur
        If ($statut == "PACT"){return  "DABR - ".$nouveauresumeann; };
        If ($statut == "PEXP"){return  "PEXP - ".$contenufichiertra; };
        If ($statut == "AEXP"){return  "AEXP - ".$contenufichiertra; };
        If ($statut == "TREF"){return  "TREF - ".$contenufichiertra; };
        
        // fin de PACT, PEXP, AEXP et TREF
    }else{
        // autres statut PACC, PANN, ADAC, TNDI
        If ($statut == "PACC"){ return "TEST - impossible déjà acceptée ".$statut; };
        If ($statut == "PANN"){ return "TEST - impossible déjà annulée ".$statut; };
        If ($statut == "ADAC"){ return "TEST - impossible vous avez accepté cette proposition".$statut; };
        return "TNDI - Cette proposition n'est pas disponible";
    }; 
}; // fin d'anulation de la transaction

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

// nettoie les entrées nombre qui doivent avoir uniquement des nombres
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

// nettoie les entrées nombre qui doivent avoir uniquement des nombres
function antitaglettre($entree){
  if(($entree=="undefined") || ($entree=="")){
    $entree="";
  }else{
// décode si transfert codé
    $entree = decrypteletransfert($entree);
//$entree = preg_replace( "/(encode pour transfert )/", '', $entree);
// fin du décodage
// nettoyage de la demande de transaction
    $entree = preg_replace( '/[^\D_]/', '', $entree);
    return $entree;
  };
}; // 

// cherche la proposition de transaction
function cherchetransaction($var3,$notransaction){
  $demandeur = $var3;
  $statuttransaction = transactionstatut($demandeur, $notransaction);

//  $debut = substr($statuttransaction,0,7);

// debut == "ADAC - "  Proposition déjà acceptée par vous 
// debut == "AEXP - "  Proposition par un autre autorisée pour vous mais expirée 
// debut == "PACC - " Cette proposition faite par vous a déjà été acceptée
// debut  == "TNDI - " Cette proposition n'est pas disponible 
// debut  == "PANN - " Proposition déjà annulée par vous
// debut  == "PEXP - " Proposition de votre part expirée sans être acceptée
// debut  == "PACT - " Proposition de votre part active
// debut  == "DTAO - " Proposition par autre droit d'accepter
// debut  == "TRIN - " Transaction inconnue" ; }; // Transaction inconnue
// debut  == "TNDI - " Cette proposition n'est pas disponible " ; };
  return $statuttransaction;
};

// Code la variable
function codelenom($variable){
  $variablelocale=$variable; $tableaucar=str_split($variablelocale);
  $nbcar=count($tableaucar); $totalvariable=0;
  for ($i = 1; $i <= $nbcar; $i++) { $totalvariable+=ord($tableaucar[$i-1])*(($i-1)*10+1); };
    return $totalvariable;
};

// renvoit le nombre d'utilisateurs
function compteinscrits(){
  $baseutilisateurs = constante("baseutilisateurs");
  $cheminfichier = tracelechemin("",$baseutilisateurs,".baseconsignel3");  $contenu = explode("\n",file_get_contents($cheminfichier));
  $nbincrits = count($contenu);
  return $nbincrits;
};

// confiance(qui,date) renvoit un tableau des accepteurs
function confiance( $x, $jour, $nbaccepteursmin, $nbaccepteursmax ){
  $entree=[];   
  $sortie = [];
  if( !is_array($x) ){ $entree[$x]=$x; }else{ foreach ($x as $cle => $valeur) { $entree[$cle] = $cle; }; };
  $obsolete = $jour - 1000;
  $sortie = [];
  $base=constante("base");
  foreach ($entree as $cle) {
  // pour chaque entrée
    $accepteursentree = [];
    $fichier = tracelechemin($entree[$cle],$base,$entree[$cle]."-accepteurs.json");
    $fichierajour = 1;
    $accepteursdates = json_decode(decryptelestockage(file_get_contents($fichier)),true);
    if(count($accepteursdates)>$nbaccepteursmax){ continue;};
    foreach ($accepteursdates as $cle => $valeur) {
    // pour chaque accepteur de l'élément entré
      if ($cle == $x) { continue; };
      if($valeur > $obsolete){
        // s'il n'est pas obsolète
        $sortie[$cle] += 1;
      }else{
        // s'il est obsolète
        unset($accepteursdates[$cle]); $fichierajour = 0;
      }; // fin de s'il est ou n'est pas obsolète
      if($fichierajour == 0){   
        // si le fichier n'est pas à jour
        $accepteurscryptes = cryptepourstockage(json_encode($accepteursdates));
        file_put_contents($fichier, $accepteurscryptes);
      };
      if(count($accepteursdates) < $confiance){   
       // s'il n'y a pas assez d'accepteurs annulation
        foreach ($accepteursdates as $cle => $valeur) {
          $sortie[$cle] -= 1 ; 
          if( $sortie[$cle] <= 0 ){ unset($sortie[$cle]); };
        };
      };
    }; // fin de pour chaque accepteur de l'élément entré
  }; // fin de pour chaque entrée
  return $sortie;
};

// niveau de confiance par le nombre d'accepteurs avec 2 degrés de séparation
function confianceinscription($numdemandeur, $jour ){
  $nbinscrits = compteinscrits();
  if($nbinscrits < 10){ return "oui"; };
  $nbaccepteurs = 5; // centralité de degré minimale
  if($nbinscrits < 30){ $nbaccepteurs = 3; if($nbinscrits < 20){ $nbaccepteurs = 2;  }; };
  $nbaccepteursmax=100; // centralité de degré excessive
  $degrecumul = [];
  unset($degre0);
  $degre0 = confiance( $numdemandeur,$jour, $nbaccepteurs, $nbaccepteursmax ); // confiance directe selon le nombre d'accepteurs requis
  foreach ($degre0 as $cle => $valeur) { 
    if (array_key_exists($cle, $degrecumul)) {
      $degrecumul[$cle] += $degre0[$cle]; unset($degre0[$cle]);
    }else{
      $degrecumul[$cle] += $degre0[$cle];
    }; 
  };
  if (count($degre0)==0) {  return "DINO - Vous avez trop ou pas assez de personnes acceptant vos propositions" ;};
  unset($degre1);
  $degre1 = confiance( $degre0 , $jour, $nbaccepteurs, $nbaccepteursmax ); // confiance avec 1 degrés de séparation
  unset($degre1[$numdemandeur]);
  foreach ($degre1 as $cle => $valeur) { 
    if (array_key_exists($cle, $degrecumul)) {
      $degrecumul[$cle] += $degre1[$cle]; unset($degre1[$cle]);
    }else{
      $degrecumul[$cle] += $degre1[$cle];
    }; 
  };
  if (count($degre1)==0) {  return "DINO - Pas assez de confiance a 1 degré de séparation" ;};
  unset($degre2);
  $degre2 = confiance( $degre1 , $jour, $nbaccepteurs, $nbaccepteursmax ); // confiance avec 2 degrés de séparation
  unset($degre2[$numdemandeur]);
  foreach ($degre2 as $cle => $valeur) { 
    if (array_key_exists($cle, $degrecumul)) {
      $degrecumul[$cle] += $degre2[$cle]; unset($degre2[$cle]);
    }else{
      $degrecumul[$cle] += $degre2[$cle];
    }; 
  };
  if (count($degre2)==0) {  return "DINO - Pas assez de confiance à 2 degrés de séparation" ;};
  return "oui";
};

// renvoi des valeurs de compensation d'impact
function consignelsuivi($propositionenjson,$nompropostion){
  $consignelsuivi = ["test",0,0];
  $nooffretra = $propositionenjson[$nompropostion]["sommaire"][0];
  $nodemandetra = $propositionenjson[$nompropostion]["sommaire"][1];
  $nodatetra = $propositionenjson[$nompropostion]["sommaire"][2];
  $offtra = "off".$nodatetra."_".$nooffretra;
  $consignelsuivi[1] =  $propositionenjson[$nompropostion][$offtra][3]*$propositionenjson[$nompropostion][$offtra][1];
  $demtra = "dem".$nodatetra."_".$nodemandetra;
  $consignelsuivi[2] = $propositionenjson[$nompropostion][$demtra][3]*$propositionenjson[$nompropostion][$demtra][1];
  $impact = $consignelsuivi[1] + $consignelsuivi[2];
  if ($impact == 0){ $consignelsuivi[0]= "zéro impact"; };
  if ($impact < 0){ $consignelsuivi[0]= "impact négatif"; };
  if ($impact > 0){ $consignelsuivi[0]= "impact positif"; };
  return $consignelsuivi;
};

// Contenu d'une transaction latransaction = contenutra( "cheminfichier"."traxxxx.json" )
function contenutra( $chemincomplet ){
  $fichierencours = fopen($chemincomplet, 'r');
  $contenutra = decryptelestockage(fgets($fichierencours, 2048));
  return $contenutra;
};

// Contenusuivi renvoit les 8 valeurs si la transaction existe $suivi=contenusuivi($var3,$notransaction)
function contenusuivi($var3,$notransaction){
  $idtra = "tra".$notransaction; 
  $nomfichiertra = "tra".$idtra.".json"; 
  $nomfichiersuivi = substr($idtra,0,14)."-suivi.json";
  $cheminfichier = ouvrelechemin($idtra); // chemin dans base2 par date
  $ligneexiste = FALSE; // Testeur de boucle ligne existe dans le fichier
  $fichierencours = fopen($cheminfichier.$nomfichiersuivi, 'r'); // ouverture en lecture
  while (!feof($fichierencours) && !$ligneexiste) { // cherche dans les lignes
    $ligne = decryptelestockage(fgets($fichierencours, 1024)); // ligne par ligne
    list($var31, $var32, $var33, $var34, $var35, $var36, $var37, $var38) = explode(",", $ligne);
    if ($var31 == "\"".$idtra."\""){
     $ligneexiste = TRUE;
    }; // Fin de transaction trouvée
  }; // Fin de while cherche dans les lignes
  fclose($fichierencours); // fermeture du fichier
  if($ligneexiste == TRUE){
    return $ligne;
  };
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

// Fait la liste des act d'une transaction  et retourne le tableau
function extraitlesactivites($contenufichiertra){
  $lesactivites=[];
  $description="";
  $lesdescriptions="";
  $chainedecoupe = $contenufichiertra;
  $debut   = '_act';
  $fin   = ']';
  $pos = strpos($chainedecoupe, $debut);
  while ($pos) {
    $chainedecoupe = substr($chainedecoupe,$pos);
    $posfin = strpos($chainedecoupe, $fin);
    $chaineavant = "\"e".substr($chainedecoupe,7,$posfin-6);
    $debut2  = strpos($chaineavant, '["');
    $fin2 = strpos($chaineavant, ",");
    $tableaudecoupe = explode("\"", $chaineavant);
    $description=substr($chaineavant,$debut2+2,$fin2-$debut2-3)." 1".
    $tableaudecoupe[5];
    $tableaudecoupe[3]=$description;
    $chaineavant=implode("\"", $tableaudecoupe);
    print_r( "description: $description<br>");
    $lesdescriptions =$lesdescriptions."\"".$description."\",\n";
    $chainedecoupe = substr($chainedecoupe,$posfin);
    $lesactivites[]=$chaineavant;
    $pos = strpos($chainedecoupe, $debut);
  };
  return [$lesactivites,$lesdescriptions];
};

// Renvoi le contenu du fichier ligne par ligne
function fichierperso($var3,$nomfichier){
  $identifiantlocal=$var3; 
  $nomfichierlocal=$nomfichier; 
  $base=constante("base");
  $cheminfichier = tracelechemin($identifiantlocal,$base,$identifiantlocal."-".$nomfichierlocal.".json");
  $contenufichier ="";
  $findeligne="<br>";
  if($nomfichier=="mestransactions"){$findeligne=" ";};
  if (file_exists($cheminfichier)) { // vérification si le fichier existe
    $fichierencours = fopen($cheminfichier, 'r'); // ouverture en lecture
    $sommaire="";
    $laligneprecedente = "";
    while (!feof($fichierencours) ) { // cherche dans les lignes
      $laligne = decryptelestockage(fgets($fichierencours,2048));
      if($nomfichier=="mestransactions"){
//        $laligne = preg_replace( "/( )/", "", $laligne);
        $pos1=strpos($laligne,"{");
        $pos2=strpos($laligne,"\"");
        $pos3=strpos($laligne,":");
        $pos4=strpos($laligne,"\"sommaire\"");
        $pos5=strrpos($laligne,"]");
        $pos6=strrpos($laligne,",");
        $pos7=strrpos($laligne,"}");
        $debuttra=substr($laligne,1,3);
        if($laligneprecedente != "" ){
            $laligne2="".$laligneprecedente ;
            if($debuttra == "off" || $debuttra == "dem"){
               $laligneprecedente = $laligneprecedente.substr($laligne,0,-1);
            }else{
                $pos2=strpos($laligne2,"\"");
                $pos3=strpos($laligne2,":");
                $pos5=strrpos($laligne2,"]");
                // sans { ni sommaire en lignes regroupée de la transaction et ses éléments 2-|
                $debut="".substr($laligne2,$pos2,$pos3); $sommaire=":{\"sommaire\":"; $fin=substr($laligne2,$pos3+1,$pos5-$pos3)."},\n ";
                $contenufichier = $contenufichier.$debut.$sommaire.$fin;
                $laligneprecedente="";
            };
            continue;
         };
        if($pos1=="FALSE"){
            // commence par {
            if($pos4>0){
                // avec sommaire 3-|
                $debut="".substr($laligne,$pos2,$pos3); $sommaire="";  $fin=substr($laligne,$pos3+1,$pos5-$pos3)."},\n";

                $laligne=$debut.$sommaire.$fin;
            }else{
                // sans { ni sommaire 1-|
                $debut="".substr($laligne,$pos2,$pos3); $sommaire="{\"sommaire\":";  $fin=substr($laligne,$pos3+1,$pos7-$pos3).",\n";
                $laligne=$debut.$sommaire.$fin;
            };
        }else{
          //  ne commence pas par  {  (avec ou sans sommaire)
          if($pos4>0){
            // avec sommaire 4-|
            $laligne="".$laligne."";
          }else{
//                $debut="pataf.... ".$debuttra."   "; $sommaire=$laligne;  $fin="| \n";
//                $laligne=$debut.$sommaire.$fin;
//                $pos6=strrpos($laligne,",");
                $laligneprecedente .= substr($laligne,0,$pos6+1)."" ; continue;
          };
        };
      };
      $contenufichier = $contenufichier.$laligne.$findeligne; // ligne par ligne
    }; // Fin de cherche dans les lignes
    fclose($fichierencours); // fermeture du fichier
  }else{
    $contenufichier = "NULL - ".$nomfichierlocal;
  };
  if($nomfichier=="mestransactions"){        
    $pos6=strrpos($contenufichier,",");
    $contenufichier=substr($contenufichier,0,$pos6)."\n}" ;
    $contenufichier = preg_replace( "/( \")/", "\"", $contenufichier);
    $contenufichier = preg_replace( "/(\n )/", "\n", $contenufichier);
    $contenufichier = preg_replace( "/( :)/", ":", $contenufichier);
    $contenufichier = preg_replace( "/( \[)/", "[", $contenufichier);
    $contenufichier = preg_replace( "/( \,)/", ",", $contenufichier);
    $contenufichier = preg_replace( "/( })/", "}", $contenufichier);
    $contenufichier = preg_replace( "/( {)/", "{", $contenufichier);
    // ajoute suiviresume
      $cheminfichier2 = tracelechemin($identifiantlocal,$base,$identifiantlocal."-suiviresume.json");
  $contenufichier2 ="\n\"suiviresume\": {\n";
  if (file_exists($cheminfichier2)) { // vérification si le fichier existe
    $fichierencours2 = fopen($cheminfichier2, 'r'); // ouverture en lecture
    $laligneprec="";
    while (!feof($fichierencours2) ) { // cherche dans les lignes
        $laligne = decryptelestockage(fgets($fichierencours2,2048));
        $derniercar=substr($laligne, -1);
        if($derniercar="\n"){$laligne = substr($laligne,0,-1);};
        if($laligne!=""){
$tableaulaligne = explode(",", $laligne);
$laligne = "".$tableaulaligne[0].",".$tableaulaligne[1].",".$tableaulaligne[2].",".$tableaulaligne[3].",".$tableaulaligne[4];
        if($laligne!=$laligneprec){
            $pos6=strpos($laligne,",");
            $reftra="\"".substr($laligne,0,$pos6)."\"";
            $contenufichier2 = $contenufichier2.$reftra.":[".$reftra.",".substr($laligne,$pos6+1)."],\n"; // ligne par ligne
        };
        };
        $laligneprec=$laligne;
    }; // Fin de cherche dans les lignes
    fclose($fichierencours2); // fermeture du fichier
    $pos6=strrpos($contenufichier2,",");
    $contenufichier2=substr($contenufichier2,0,$pos6)."\n}\n" ;

  }else{
    $contenufichier2 = "NULL - pas de fichier suiviresume.json";
  };

    $contenufichier="{\n \"lestra\":{\n".$contenufichier.",".$contenufichier2."}\n" ;
  };
  return $contenufichier;
};

// Renvoi tout le contenu du fichier dans une chaine
function fichierperso2($var3,$nomfichier){
  $identifiantlocal=$var3; 
  $nomfichierlocal=$nomfichier; 
  $base=constante("base");
  $cheminfichier = tracelechemin($identifiantlocal,$base,$identifiantlocal."-".$nomfichierlocal.".json");
  if (file_exists($cheminfichier)) { // vérification si le fichier existe
    $fichierencours = fopen($cheminfichier, 'r'); // ouverture en lecture
      $contenufichier = decryptelestockage(file_get_contents($cheminfichier.$nomfichiertra)); // fichier au complet 
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

// inscription change le compte
function inscription($var3,$contenufichier){
  $identifiantlocal = $var3; 
  $chaineinscription = substr($contenufichier,2,strlen($contenufichier)-4); 
  $avatar = constante("avatar"); $localite = constante("localite");
  list($nompublic, $numpublic, $numprive, $numsecret) = explode(",", $chaineinscription);
  $etatutilisateur = testeutilisateurunique($numprive,$nompublic);
  // $nompublic garde les majuscules
  $detailutilisateur =",".$numsecret.",".$nompublic.",\"".$avatar."\",\"".$localite."\",".$identifiantlocal.",";
  if($etatutilisateur =="inconnu"){
    // change le fichier .consignel3
    ajouteutilisateur($numprive,$detailutilisateur);
    supprimeutilisateur($var3,61612);
    // initialise le compte
    initialisefichier("".$numprive."","base","".$numprive."-resume.json");

    return "NUCC - ";
  }else{
    if($etatutilisateur =="numpriveconnu"){ return "DIMF - Demande d'inscription l'identifiant privé est déjà utilisé"; };
    if($etatutilisateur =="nompublicconnu"){ return "DIMF - Demande d'inscription le nom public est déjà utilisé"; };
    return "TEST - Le nom n'est pas unique ".$etatutilisateur;
  };
};

// inverse l'offre et la demande d'une proposition
function inversetransaction($idtra,$contenufichiertra,$dateaccepte,$destinataire,$proposeur){
  $idtra2 = substr($idtra,3,14);
  $acclocal = $contenufichiertra;
//  $nooffre = "off".$idtra2; $cherchenooffre = "/(".$nooffre.")/"; 
//  $nodemande = "dem".$idtra2; $cherchenodemande = "/(".$nodemande.")/"; 
//  $nointerim = "ttt".$idtra2; $chercheinterim = "/(".$nointerim.")/"; 
//  $acclocal = preg_replace( $cherchenooffre, $nointerim , $contenufichiertra);
//  $acclocal = preg_replace( $cherchenodemande, $nooffre , $acclocal);
//  $acclocal = preg_replace( $chercheinterim, $nodemande , $acclocal);
  $notra = "acc".$idtra2; 
  $cherchenotra = "/(tra".$idtra2.")/"; 
  $acclocal = preg_replace( $cherchenotra, $notra , $acclocal);
//  $datetra=substr($idtra,3,8);
//  if($datetra<"20200801"){ $acclocal=transactionformat202008($acclocal); };

  $acclocalenphp = json_decode($acclocal,true);
  $notra = "acc".substr($idtra,3);
//  $nooffretra = $acclocalenphp[$notra]["sommaire"][0];
//  $nodemandetra = $acclocalenphp[$notra]["sommaire"][1];
//  $acclocalenphp[$notra]["sommaire"][0] = $nodemandetra;
//  $acclocalenphp[$notra]["sommaire"][1] = $nooffretra;
  $acclocalenphp[$notra]["sommaire"][2] = $dateaccepte;
  if(substr($destinataire,0,1)=="\""){$destinataire=substr($destinataire,1); $destinataire=substr($destinataire,0,-1);};
  $acclocalenphp[$notra]["sommaire"][3] = $destinataire;
  if(substr($proposeur,0,1)=="\""){$proposeur=substr($proposeur,1); $proposeur=substr($proposeur,0,-1);};
  $acclocalenphp[$notra]["sommaire"][5] = $proposeur;
  $acclocal = json_encode($acclocalenphp);
  return $acclocal;
};

// retourne le pseudo d'un identifiant
function lepseudode($iddupseudo, $option="noid"){
  $baseutilisateurs = constante("baseutilisateurs");
  $cheminfichier =  tracelechemin("",$baseutilisateurs,".baseconsignel3");  
  if (file_exists($cheminfichier)) { // vérification de l'utilisateur le fichier existe
    $existe = FALSE; // Testeur de boucle
    $fichierencours = fopen($cheminfichier, 'r'); // ouverture en lecture
    while (!feof($fichierencours) && !$existe) { // cherche dans les lignes
      $ligne = decryptelestockage(fgets($fichierencours, 1024)); // ligne par ligne
      if (preg_match('/\b' . preg_quote($donnee2) . '\b/u', $ligne)) { 
        list($var61, $var62, $var63, $var64, $var65, $var66) = explode(",", $ligne);
        // $var1 code utilisateur, $var2 code mot de passe, $var3 pseudo utilisateur, $var4 image ou avatar, $var5 localité consignel
        if ($var61==$iddupseudo){ // trouvé comme identifiant
          $existe = TRUE; // Valeur trouvée arrêt du while
        }; // fin du trouvé comme identifiant
      } // Fin de trouvé dans la ligne
    }; // Fin de cherche dans les lignes
    fclose($fichierencours); // fermeture du fichier
  };
  if($existe == TRUE){
    if($option=="noid"){return $var63;};
    if($option=="nopseudo"){return $var66;};
  };
};

// retourne l'identifiant à partir du code du pseudo
function leiddupseudo($iddupseudo){
  $baseutilisateurs = constante("baseutilisateurs");
  $cheminfichier =  tracelechemin("",$baseutilisateurs,".baseconsignel3");  
  if (file_exists($cheminfichier)) { // vérification de l'utilisateur le fichier existe
    $existe = FALSE; // Testeur de boucle
    $fichierencours = fopen($cheminfichier, 'r'); // ouverture en lecture
    while (!feof($fichierencours) && !$existe) { // cherche dans les lignes
      $ligne = decryptelestockage(fgets($fichierencours, 1024)); // ligne par ligne
      if (preg_match('/\b' . preg_quote($donnee2) . '\b/u', $ligne)) { 
        list($var61, $var62, $var63, $var64, $var65, $var66) = explode(",", $ligne);
        // $var1 code utilisateur, $var2 code mot de passe, $var3 pseudo utilisateur, $var4 image ou avatar, $var5 localité consignel $var6 code de $var3
        if (($var66==$iddupseudo)||($var61==$iddupseudo)){ // trouvé
          $iddestinataire = $var61; $existe = TRUE; // Valeur trouvée arrêt du while
        }; // fin du trouvé comme identifiant
      } // Fin de trouvé dans la ligne
    }; // Fin de cherche dans les lignes
    fclose($fichierencours); // fermeture du fichier
    if($existe == TRUE){return $iddestinataire;}else{return "inconnu";};
  };
};

// Ajoute les valeurs des act de la transaction à la liste mesvaleursref
function listevaleurs($cheminfichier, $valeursaajouter){
  $fichier = $cheminfichier;
  if (file_exists($cheminfichier)) {
   $contenu = json_decode(decryptelestockage(file_get_contents($fichier)),true);
 } else {
  file_put_contents($fichier, '{  }');
  $contenu = json_decode(file_get_contents($fichier),true);
};
$nbact=count($valeursaajouter)-1;
for ($i = 0; $i <= $nbact; $i++) { 
  $leschaines= explode("\"", $valeursaajouter[$i]);
  $laclef = $leschaines[1];
  $valeur = json_decode(substr($valeursaajouter[$i],strpos($valeursaajouter[$i],"[")+0) ,true);
  if(array_key_exists($leschaines[1], $contenu)){
    $contenu[$laclef] = $valeur;
  }else{
    $contenu[$laclef] = $valeur;
  };
};
$contenucryptes = cryptepourstockage(json_encode($contenu));
file_put_contents($fichier, $contenucryptes);
return $contenu;
};

// supprime les références aux sessions obsoletes
function nettoyagerefsessions(){
  $today = getdate();  
  $heureutilise = $today[hours]*60+$today[minutes];
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

// supprime les références aux sessions obsoletes
function nettoyagetransactions($var3,$nbjours){
  $baseutilisateurs = constante("base");
  $cheminfichier = tracelechemin($var3,$baseutilisateurs,$var3."-mestransactions.json");
  if (file_exists($cheminfichier)) { // vérification fichier sessions obsoletes existe
      if($nbjours=="0"){
        $deniertiret=strrpos($cheminfichier, "-mestransactions.json");
        $cheminfichier2= tracelechemin($var3,$baseutilisateurs,$var3."-demandeaqui.json");
//        $cheminfichier3= tracelechemin($var3,$baseutilisateurs,$var3."-mesopportunites.json");
        $cheminfichier4= tracelechemin($var3,$baseutilisateurs,$var3."-mesvaleursref.json");
        $cheminfichier5= tracelechemin($var3,$baseutilisateurs,$var3."-quoi.json");
        $cheminfichier6= tracelechemin($var3,$baseutilisateurs,$var3."-suiviresume.json");
        unlink($cheminfichier);
        unlink($cheminfichier2);
//        unlink($cheminfichier3);
        unlink($cheminfichier4);
        unlink($cheminfichier5);
        unlink($cheminfichier6);
       }else{
        $today = getdate(); $aujourdhui = date("Ymd");
        $debut =  getdate(mktime(0, 0, 0, date("m")  , date("d")-$nbjours-1, date("Y")));
        if($debut["mon"]<10){$debut["mon"]="0".$debut["mon"];};
        if($debut["mday"]<10){$debut["mday"]="0".$debut["mday"];};
        $debutdate = $debut["year"].$debut["mon"].$debut["mday"];
        $fichierencours = fopen($cheminfichier, 'r+'); // ouverture en lecture ecriture autorisée pointeur au début
        $ligne = fgets($fichierencours, 2048); // ligne par ligne
        $lignedenclair = decryptelestockage($ligne);
        $fin=strpos($lignedenclair, "_");
        $dateligne = substr($lignedenclair,$fin-8,8);
        // le nombre de jours est différent de 0 tester le premier enregistrement pour sortie rapide
        if($debutdate < $dateligne){
          // le premier enregistrement est encore valide abandonner
          fclose($fichierencours);     //  return "TEST - travail déjà fait ".$debutdate." ".$dateligne;
        }else{
          // le premier enregistrement est obsolete nettoyer le fichier
          $lefichier ="";
          while (!feof($fichierencours) ) { // cherche dans les lignes
            $ligne = fgets($fichierencours, 2048); // ligne par ligne
            $lignedenclair = decryptelestockage($ligne);
            $fin=strpos($lignedenclair, "_");
            $dateligne = substr($lignedenclair,$fin-8,8);
            if ($dateligne > $debutdate){ $lefichier =$lefichier.$ligne; }; // fin du trouvé pas obsolete
          }; // Fin de cherche dans les lignes
          fclose($fichierencours); // fermeture du fichier
          $nouveaufichier = fopen($cheminfichier, "w");
          fwrite($nouveaufichier, $lefichier);        
//          return "TEST - fichier nettoyé".$passe." ".$debutdate;
        };
      };// fin de si plusieurs jours
  }else{
    // Fichier non trouvé pas de fichier mestransactions
//            return $var3."TEST - pas de fichier";
  };

// nettoyage de suiviresume
  $cheminfichier = tracelechemin($var3,$baseutilisateurs,$var3."-suiviresume.json");
  if (file_exists($cheminfichier)) { // vérification fichier sessions obsoletes existe
      if($nbjours=="0"){
        //déjà fait avec le nettoyage de mestransactions
       }else{
        $fichierencours = fopen($cheminfichier, 'r+'); // ouverture en lecture ecriture autorisée pointeur au début
        $ligne = fgets($fichierencours, 1024); // ligne par ligne
        $lignedenclair = decryptelestockage($ligne);
        $fin=strpos($lignedenclair, "_");
        $dateligne = substr($lignedenclair,$fin-8,8);
        // le nombre de jours est différent de 0 tester le premier enregistrement pour sortie rapide
        if($debutdate < $dateligne){
          // le premier enregistrement est encore valide abandonner
          fclose($fichierencours);     //  return "TEST - travail déjà fait ".$debutdate." ".$dateligne;
        }else{
          // le premier enregistrement est obsolete nettoyer le fichier
          $lefichier ="";
          while (!feof($fichierencours) ) { // cherche dans les lignes
            $ligne = fgets($fichierencours, 2048); // ligne par ligne
            $lignedenclair = decryptelestockage($ligne);
            $fin=strpos($lignedenclair, "_");
            $dateligne = substr($lignedenclair,$fin-8,8);
            if ($dateligne > $debutdate){ $lefichier =$lefichier.$ligne; }; // fin du trouvé pas obsolete
          }; // Fin de cherche dans les lignes
          fclose($fichierencours); // fermeture du fichier
          $nouveaufichier = fopen($cheminfichier, "w");
          fwrite($nouveaufichier, $lefichier);        
//          return "TEST - fichier nettoyé".$passe." ".$debutdate;
        };
      };// fin de si plusieurs jours
  }else{
    // Fichier non trouvé pas de fichier suiviresume
//            return $var3."TEST - pas de fichier";
  }; // fin de nettoyage suiviresume
};

// note un nouvel avatar
function noteavatar($var3,$nopseudo){
  if(isset($_SERVER['CONTENT_LENGTH']) && $_SERVER['CONTENT_LENGTH'] > 0) {
    $httpContent = fopen('php://input', 'r');
    $data = stream_get_contents($httpContent);
    fclose($httpContent);
    
    $data=antitaghtml($data);
    $data2=json_decode($data, true);
    if(json_last_error_msg() != "No error"){ return "ERAV - Erreur reception avatar"; };
    list($typedata, $data2) = explode(';', $data2["avatar"]);
    list($typedata, $extension) = explode('/', $typedata);
    // vérification extension
    $data2 = substr($data2, strpos($data2, ',') + 1);
    $type = strtolower($extension); // jpg, png, gif
    if (!in_array($type, [ 'jpg', 'jpeg', 'png' ])) { return "ERAV - Fichier image non autorisé. Uniquement jpg et png";  }
    // vérification fichier en base64
    $data2 = base64_decode($data2);
    if ($data === false) {  return "ERAV - Transfert d'image non autorisé. Uniquement base64";   }
    $poids=strlen ( $data2);
    if($poids>40000){return "ERAV - Fichier image non autorisé. Trop gros"; };
    $baseavatarperso=constante("baseavatarperso");

//    $cheminsansfichier = tracelechemin($var3,$baseavatarperso,$var3);    
    $cheminsansfichier = tracelechemin($nopseudo,$baseavatarperso,$nopseudo,"ouvre");    
    array_map('unlink', glob($cheminsansfichier.'avatar.*'));
    $cheminavatarperso=$cheminsansfichier."avatar.".$extension;
    file_put_contents($cheminavatarperso, $data2);
    return "AVAM - ".substr($cheminavatarperso,1);
  }else{return "ERAV - Désolé pas d'avatar sur serveur";};
};

// renvoi nbjoursserveur autorisé par l'utilisateur
function nbjoursserveur($var3){
    $resumecpt=resumecompte($var3);
    $derniercompte = explode( ",", "".$resumecpt );
    $dureepersoserveur =$derniercompte[4];
    return $derniercompte[4];
};

// note la proposition de transaction
function notetransaction($var3,$nomfichier,$contenufichier){
    // $var3 pour l'utilisateur chiffré, $nomfichier ex mestransactions, $contenu fichier json
    $identifiantlocal = $var3; 
    $nomfichierlocal = $nomfichier; 
    $chainejson = $contenufichier; 
    
    // vérification préliminaires
    $debut = strpos($chainejson, "tra");
    $fin = strpos($chainejson, "\" :");
    $idtra = substr($chainejson,$debut,$fin-$debut);
    $jsonenphp = json_decode($chainejson,true);
    if(json_last_error_msg() != "No error"){ return "DTNC - erreur reception proposition"; };
    // Demande au DA↺ non autorisée
    if($jsonenphp[$idtra]["sommaire"][3]==182097){ return "DTRD - Proposition non enregistré. Le DA↺ ne peut pas être destinataire d'une proposition"; };
    // Demande à soi même non autorisée
    if("nonautorise"==testdestinatairedepot($jsonenphp[$idtra]["sommaire"][3],$var3)){
        return "DTRA - Proposition non enregistré. Le destinataire ne peut pas être soi-même";
    };
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
            if (($memetransaction == TRUE) && ($memeauteur == TRUE) ){ 
                // trouvé identique
                return "PDEN - Proposition déjà enregistrée"; $transaction = ""; 
                $ligneexiste = TRUE;
            }else{
            };
        }; // Fin de while cherche dans les lignes
        fclose($fichierencours); // fermeture du fichier
    }else{
        // le fichier de transactions pour l'heure en cours n'existe pas on peut continuer
    };
    // vérifications complémentaires
    $resumecpt = resumecompte($var3); 
    $derniercompte = explode( ',', $resumecpt );
    $soldeconsigneldisponible = $derniercompte[0];
    $soldeconsignelparjour = $derniercompte[2];
    if($soldeconsignelparjour =="INF"){$soldeconsignelparjour = 10;}; // à faire remplacer par variable inscription

    // $soldedollardisponible = ; $soldemlcdisponible = ; à faire
    $idoff = "off".$jsonenphp[$idtra]["sommaire"][2]."_".$jsonenphp[$idtra]["sommaire"][0]; // identification de l'offre
    $consigneloffre = $jsonenphp[$idtra][$idoff][3]; 
    if($consigneloffre > 0){$consigneloffre=0;}; // Le plus ne sera versé que si la proposition est acceptée. Le moins est déduit immédiatement. Il sera remboursé si la proposition est annulée.
    
    // $dollaroffre = $jsonenphp[$idoff][4]; $mlcoffre = $jsonenphp[$idoff][5];
    
    $iddem = "dem".$jsonenphp[$idtra]["sommaire"][2]."_".$jsonenphp[$idtra]["sommaire"][1]; // identification de la demande
  
    // détection de demande d'inscription ou de vérification d'utilisateur unique
    $debutverifutilisateur = strpos($chainejson, "_act0001760145\""); 
    $verifutilisateuroffdem = substr($chainejson,$debutverifutilisateur-16,3);   
    $debutinscritutilisateur = strpos($chainejson, "_act0001644192\""); 
    $inscritutilisateuroffdem = substr($chainejson,$debutinscritutilisateur-16,3);   
    $debutconfirmutilisateur = strpos($chainejson, "_act0001759799\""); 
    $confirmutilisateuroffdem = substr($chainejson,$debutconfirmutilisateur-16,3);  
    // demande d'inscription d'utilisateur unique 
    if( ($verifutilisateuroffdem == "off" ) && ( $inscritutilisateuroffdem == "dem" ) && !$debutconfirmutilisateur ){
        // identification du destinataire de l'offre 
        if($jsonenphp[$idtra]["sommaire"][3]=="0"){
            return "DIMF - Demande d'inscription il manque A qui (nom public du nouvel utilisateur)";
            $destinataire = "0";  
        }else{
            $destinataire = leiddupseudo($jsonenphp[$idtra]["sommaire"][3]); 
            if($destinataire=="inconnu"){
                if($verifutilisateuroffdem=="off"){
                    $numutilisateur= $jsonenphp[$idtra]["sommaire"][3]; 
                    // vérification si le proposeur est autorisé à faire l'inscription en fonction de la toile de confiance avec numéro demandeur et date annéejour
                    $nojour = date_format(date_create(substr($idtra,3,8)),"z");
                    if (strlen($nojour) == 3){ 
                        $ladate = substr($dateaccepte,0,4).$nojour; 
                    }else{
                        if (strlen($nojour) == 2){ 
                            $ladate = substr($dateaccepte,0,4)."0".$nojour; 
                        }else{
                            if (strlen($nojour) == 1){ $ladate = substr($dateaccepte,0,4)."00".$nojour; };
                        };
                     };
                     $jourinscription = substr($idtra,3,4).$ladate;
                     $verificateurautorise = confianceinscription($identifiantlocal , $jourinscription );
                     if($verificateurautorise != "oui"){ return "DINO - Vous ne pouvez pas inscrire un nouvel utilisateur (".$verificateurautorise.")"; };        
                     // L'ajout de l'utilisateur est autorisé
                     $ajout=ajouteutilisateur($numutilisateur,"nompublic");          
                }else{
                    return "DTDI - Destinataire inconnu" ; 
                };
            }else{
                // Destinataire connu
                return "DIMF - Demande d'inscription le nom public est déjà utilisé"; 
            }; 
        };
    };
    // demande de vérification d'utilisateur unique
    if( $debutconfirmutilisateur && $debutverifutilisateur && !$debutinscritutilisateur ){
        if( ($verifutilisateuroffdem == "off" ) && ( $confirmutilisateuroffdem == "dem" ) ){
            // ok ne rien faire ici mais détecter dans l'acceptation
        }else{
            return "DIMF - C'est le demandeur qui doit faire la vérification de confirmation d'utilisateur unique";
        };
    };
    if( $debutinscritutilisateur && $debutconfirmutilisateur ){
        return "DIMF - Impossible de faire une inscription et une confirmation en même temps";
    };
    if( $debutinscritutilisateur && ( $verifutilisateuroffdem != "off" )  ){
        return "DIMF - Demande mal formulée pour l'inscription";
    };
    if( $debutconfirmutilisateur && ( !$debutverifutilisateur )  ){
        return "DIMF - Demande mal formulée pour la confirmation d'inscription";
    };
    $consigneldemande = $jsonenphp[$idtra][$iddem][3]; // $dollaroffre = $jsonenphp[$idoff][4]; $mlcoffre = $jsonenphp[$idoff][5];
    $nbjoursenreserve=$soldeconsigneldisponible/$soldeconsignelparjour;
    $nbjoursselonreserve=7;
    if($nbjoursenreserve>30){$nbjoursselonreserve+=7;};
    if($nbjoursenreserve>60){$nbjoursselonreserve+=7;};
    if($nbjoursenreserve>90){$nbjoursselonreserve+=7;};
    if ((($soldeconsignelparjour * $nbjoursenreserve) + $consigneloffre + $consigneloffrepaiement)<0){ return "DTCE - Refus dépense ↺onsignel excessive" ; $transaction = "";  };
    if (($soldeconsigneldisponible + $consigneloffre + $consigneloffrepaiement)<0){ return "DTMC - Refus solde ↺onsignel insuffisant"; $transaction = "";  };
    
    //  if (($soldedollardisponible + $dollaroffre)<0){  "solde dollar insuffisant"; $transaction = "";  };
    //  if (($soldemlcdisponible + $mlcoffre)<0){  "solde mlc insuffisant"; $transaction = "";  };
    
    // fichier de détail de la transaction dans consignelbase2
    $transactionindex = $chainejson."\n"; 
    $cheminfichier = ouvrelechemin($idtra); 
    $nomfichier = $idtra.".json";
    ajouteaufichier($cheminfichier.$nomfichier, $transactionindex);
    
    // fichier des de suivi des transactions dans l'heure courante  dans consignelbase2
    $transactionsuivi = "\"".$idtra."\",\"".implode("\",\"", $jsonenphp[$idtra]["sommaire"])."\",\"".$consigneldemande."\",\"".$var3."\"\n";
    $nomfichier = substr($idtra,0,14)."-suivi.json";
    ajouteaufichier($cheminfichier.$nomfichier, $transactionsuivi);
    // fichier des transactions dans le compte de l'utilisateur
    //  $transaction = preg_replace( "/(],\")|(] ,\")/", "],\n\"", $transaction);
    //  $transaction = preg_replace( "/^({ )/", "", $transaction);
    //  $transaction = preg_replace( "/(] })/", "],\n", $transaction);
    $transaction2 = substr($transaction,1,-1).",\n";
    $base=constante("base");
    // ajoute au fichier -mestransactions du proposeur
    $nbjoursdemandeur=nbjoursserveur($identifiantlocal);
    nettoyagetransactions($identifiantlocal,$nbjoursdemandeur);
    if($nbjoursdemandeur>0){
        $cheminfichier = tracelechemin($identifiantlocal,$base,$identifiantlocal."-".$nomfichierlocal.".json");  // -mestransactions.json
        ajouteaufichier($cheminfichier,$transaction2);
    };
    // fichier de l'ordre des transactions
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
    $dureepersoserveur=nbjoursserveur($identifiantlocal);
    $nouveauresume = "".$nouveausoldeconsignel.",".$minimax[0].",".$revenujournalier.",".$minimax[1].",".$dureepersoserveur;
    $cheminfichier = tracelechemin($identifiantlocal,$base,$identifiantlocal."-resume.json");  
    remplacefichier($cheminfichier, $nouveauresume);

    if($nbjoursdemandeur>0){
        // si le proposeur l'autorise
        // ajout à la liste des opportunités du proposeur
        $listeopportunite = ajoutealaliste($identifiantlocal,"mesopportunites","\"".$idtra.".json\"");
        
        // met à jour l'archivage des résumés de compte consignel
        $cheminfichier = tracelechemin($identifiantlocal,$base,$identifiantlocal."-suiviresume.json");  
        ajouteaufichier($cheminfichier,$idtra.",".$nouveauresume."\n");
    };
    
    // ajout de la proposition dans les opportunités si c'est un utilisateur identifié
    $destinataire = leiddupseudo($jsonenphp[$idtra][3]); 
    if($destinataire != 0){ $laliste = ajoutealaliste($destinataire,"mesopportunites","\"".$idtra.".json\"" ); };
    
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
  $nooffretra = $propositionenjson[$nompropostion]["sommaire"][0];
  $nodemandetra = $propositionenjson[$nompropostion]["sommaire"][1];
  $nodatetra = $propositionenjson[$nompropostion]["sommaire"][2];
  $offtra = "off".$nodatetra."_".$nooffretra;
  $listeactsoff = $propositionenjson[$nompropostion][$offtra][0];
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
      $idactquantite = $propositionenjson[$nompropostion][$idact][1];
//      print_r($idact." ".$idactquantite."<br>");
      if($typetroc == "↺"){$paiement[1] += $idactquantite;};
      if($typetroc == "mlc"){$paiement[2] += $idactquantite;};
      if($typetroc =="$"){$paiement[3] += $idactquantite;};
    };
  };
  if ($monnaiedansoffre == 0){ $paiement[0]= "nonspeculation"; };

  $demtra = "dem".$nodatetra."_".$nodemandetra;
  $listeactsdem = $propositionenjson[$nompropostion][$demtra][0];
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
      $idactquantite = $propositionenjson[$nompropostion][$idact][1];
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

// transforme une transaction à tous en proposition du demandeur au proposeur
function passedemande($idtra,$contenufichiertra,$dateaccepte,$noproposeur){
  $dateinitiale = substr($idtra,3,14);
  $nooffre = "off".$dateinitiale; $cherchenooffre = "/(".$nooffre.")/"; 
  $nodemande = "dem".$dateinitiale; $cherchenodemande = "/(".$nodemande.")/"; 
  $nointerim = "ttt".$dateinitiale; $chercheinterim = "/(".$nointerim.")/"; 
  $contenu = preg_replace( $cherchenooffre, $nointerim , $contenufichiertra);
  $contenu = preg_replace( $cherchenodemande, $nooffre , $contenu);
  $contenu = preg_replace( $chercheinterim, $nodemande , $contenu);
  $changedate = $dateaccepte."_"; $cherchenotra = "/(".$dateinitiale.")/"; 
  $contenu = preg_replace( $cherchenotra, $changedate , $contenu);
  $noinitial=substr($idtra,16);
  $notra="tra".$dateaccepte.$noinitial;
  $contenuenphp = json_decode($contenu,true);
  $nooffretra = $contenuenphp[$notra]["sommaire"][0];
  $nodemandetra = $contenuenphp[$notra]["sommaire"][1];
  $contenuenphp[$notra]["sommaire"][0] = $nodemandetra;
  $contenuenphp[$notra]["sommaire"][1] = $nooffretra;
  $contenuenphp[$notra]["sommaire"][2] = $dateaccepte;
  $contenuenphp[$notra]["sommaire"][3] = $noproposeur;
  $contenuenphp[$notra]["sommaire"][4] = "30";
  $contenu = json_encode($contenuenphp);
  $cherche2pt = "/(:)/";  $contenu = preg_replace( $cherche2pt, " :" , $contenu);
  return $contenu;
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

// refuse la proposition de transaction
function refusetransaction($var3,$notransaction){
  $noaccepteur = $var3; 
  $demandeur = $var3;
  $statuttransaction = transactionstatut($demandeur, $notransaction);
  $debut = substr($statuttransaction,0,4);
  $ligne = contenusuivi($var3,$notransaction); 
  list($var31, $var32, $var33, $var34, $var35, $var36, $var37, $var38) = explode(",", $ligne);
  $destinataire = leiddupseudo($demandeur); // identification du destinataire de l'offre

  if($debut=="DTAO"){
    if($var35 == "\"0\""){
        return "TREF - Transaction refusée. Cette proposition reste ouverte à tous";
    };
    if(($var35 == "\"".$var3."\"")||($demandeur==$var3)){
      $idtra = "tra".$notransaction;
      $nomfichiertra = $idtra.".json";
      $listeopportunite = retiredelaliste($var3,"mesopportunites",$nomfichiertra);
      if($var38 =="\"DA↺\"\n"){$proposeur="001"; };
      $proposeur = preg_replace( "/\D/", "", $var38);
      $propositionrefuse = annuleproposition($proposeur,$notransaction,"TREF - ".substr($statuttransaction,7)); // annuleproposition renvoie "TREF - ".substr($statuttransaction,7);
      return $propositionrefuse;
    }else{
      return "TEST - Réponse serveur: Cette proposition reste ouverte à tous TREF -";
    };
  } else {
    return "TEST - Réponse serveur: Proposition non disponible pour refus type: ".$debut;
  };
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
    if($var3=="DA↺"){$base=constante("basedac"); $identifiantlocal=""; };
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

// retire avatar personnel et remplace par avatar par défaut
function retireavatar($var3,$nopseudo,$fichieravatar){
  $baseavatarperso=constante("baseavatarperso");
  $cheminsansfichier = tracelechemin($nopseudo,$baseavatarperso,"","ouvre");    
  array_map('unlink', glob($cheminsansfichier.$nopseudo.'avatar.*'));
  $baseavatars = constante("baseavatars");
  if($fichieravatar=="\"avatarannule.png\""){
    retiredir($cheminsansfichier);
    $nomavatars = constante("avatar");
    $cheminavatar = $baseavatars.$nomavatars;
  }else{
    $nomavatars = substr($fichieravatar,1,-1);
    $cheminavatar = $baseavatars.$nomavatars;
    file_put_contents($cheminsansfichier.$nopseudo."avatar.txt", $cheminavatar);
  };
  return "ANAV - ".substr($cheminavatar,1);
};

// retire de la liste
function retiredelaliste($var3,$nomfichier,$item){
  $contenufichier = "".fichierperso2($var3,$nomfichier);
  $retireregex = "/(".$item.")/";
  $contenufichier = preg_replace( $retireregex, "", $contenufichier);
  $contenufichier = preg_replace( "/(\"\",)/", "", $contenufichier);
  $contenufichier = preg_replace( "/(,\"\")/", "", $contenufichier);
  $base=constante("base");
  $cheminfichierinclu = tracelechemin($var3,$base,$var3."-".$nomfichier.".json");
  ajouteaufichier($cheminfichierinclu, $contenufichier,"debut");
  return $contenufichier;
};

// retire le chemin vide
function retiredir($lechemin=""){
  $chemin=$lechemin;
  rmdir ($chemin);
  $chemin=substr($chemin, 0, -1);
  rmdir ($chemin);
  $chemin=substr($chemin, 0, -1);
  rmdir ($chemin);
  $chemin=substr($chemin, 0, -1);
  rmdir ($chemin);

};

// retire un item de la liste des opportunitées
function retireopportunite($var3,$donnee4){
//  $listeopportunite = retiredelaliste($var3,"mesopportunites","tra".$donnee4.".json");
//  $listeopportunite = retiredelaliste($var3,"mesopportunites","dac".$donnee4.".json");
//  $listeopportunite = retiredelaliste($var3,"mesopportunites","acc".$donnee4.".json");
//  $listeopportunite = retiredelaliste($var3,"mesopportunites","exp".$donnee4.".json");
  $contenufichier = "".fichierperso2($var3,"mesopportunites");
  $debutdate=strpos($contenufichier,$donnee4);
  $avant=substr($contenufichier,1,$debutdate);
  $apres=substr($contenufichier,$debutdate);
  $debutnom=substr(strrchr($avant, "\""),1,-1);
  $finnom=substr($contenufichier,$debutdate,strpos($apres, "\""));
  $mesopportunite = retiredelaliste($var3,"mesopportunites",$debutnom.$finnom);
  return $mesopportunite;
};

// mise à jour du compte avec le revenu inconditionnel
function revenuinconditionnel($var3){
  $base = constante("base");
  $revenubase = constante("minimumviableparjour");
  $cheminfichier = tracelechemin($var3,$base,$var3);  
  $ladate = date("Ymd_Hi");
  $dernieresidtra = ajouteaufichier2dates($cheminfichier."-resume2dates.json","dac".$ladate."_".$var3."");
  if($dernieresidtra[9] == $dernieresidtra[8]){ 
    return 0;
  }else{
    if($dernieresidtra[8] == null ){ $dernieresidtra[8] = $dernieresidtra[9]-1 ;};
    if($dernieresidtra[9] > $dernieresidtra[8]){ 
      $nbjourrevenuinconditionnel = $dernieresidtra[9] - $dernieresidtra[8] ; 
    }else{ 
      $nbjourrevenuinconditionnel = $dernieresidtra[9] + 365 - $dernieresidtra[8] ; 
    };
    if($nbjourrevenuinconditionnel > 31){$nbjourrevenuinconditionnel=31;};
    $revenuinconditionnel = $nbjourrevenuinconditionnel * $revenubase;
    // fait la proposition de transaction
    $mesactoff= "\"off".$ladate.'_act0002220560" : ["don du minimum viable",'.$revenuinconditionnel.',"↺",0,0,0,0,0,0,0,0]';
    $mesactdem= "\"dem".$ladate.'_act000537234" : ["rien",0,"↺",0,0,0,0,0,0,0,0]';
    $mesact=  $mesactoff.",".$mesactdem;
    $codeoffre=codelenom($mesactoff);    
    $codedemande=codelenom($mesactdem);
    $loffre="\"off".$ladate."_".$codeoffre."\" : [\"act0002220560\",1,\"u\",".$revenuinconditionnel.",0,0,0,0,0,0,0],";
    $lademande="\"dem".$ladate."_".$codedemande."\" : [\"act000537234\",1,\"u\",0,0,0,0,0,0,0,0]";
    $demandeaqui=$var3;
    $dureeexpire=31;
    $latransaction= "{ \"sommaire\":[\"".$codeoffre."\",\"".$codedemande."\",\"".$ladate."\",\"".$demandeaqui."\",\"".$dureeexpire."\",\"DA↺\"]";
    $chaineretour = "revenuinconditionnel";
    $codelatransaction=codelenom($latransaction.$chaineretour);
    $matransaction="\"tra".$ladate."_".$codelatransaction."\" : ".$latransaction.",".$loffre.$lademande."";
    $transactionjson="{ ".$matransaction.",".$mesact." } ";  
    $cheminfichier = ouvrelechemin("tra".$ladate."_".$codelatransaction."");
    ajouteaufichier($cheminfichier."tra".$ladate."_".$codelatransaction.".json", "".$transactionjson." }\n");
    // note la transaction dans le suivi
    $transactionjson = "\"tra".$ladate."_".$codelatransaction."\",\"".$codeoffre."\",\"".$codedemande."\",\"".$ladate."\",\"".$var3."\",\"31\",\"0\",\"DA↺\"";
    ajouteaufichier($cheminfichier."tra".substr($ladate,0,11)."-suivi.json", "".$transactionjson."\n");
    // pas d'accetation automatique de la proposition de transaction besoin accord de l'utilisateur
    // ajoute la proposition dans la liste des opportunités de l'utilisateur
    $laliste = "";
    $item= "\"dac".$ladate."_".$codelatransaction.".json\"";
    $laliste = ajoutealaliste($var3,"mesopportunites",$item );

    return $nbjourrevenuinconditionnel;
  }; // fin du choix de mise à jour du revenu inconditionnel
};

// Préférences de stockage des fichiers de l'utilisateur sur le serveur
function serveurmoi($var3,$donnee4,$test=""){
    $resumecpt = resumecompte($var3); 
    $derniercompte = explode( ",", "".$resumecpt );
    $nbjours =$derniercompte[4];
    if($donnee4==""){
        if(!$nbjours){ $derniercompte[4]=0; $resumecpt=implode(",", $derniercompte); }; 
    }else{
        $derniercompte[4]=$donnee4;
        $resumecpt=implode(",", $derniercompte);
        $base = constante("base");
        $cheminfichier = tracelechemin($var3,$base,$var3."-resume.json");
        remplacefichier($cheminfichier, $resumecpt);
    }; 
    if($test==""){
        return $nbjours;
//        return $derniercompte[4];
    }else{
        return "RPRF - ".$resumecpt;
    };
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

// supprime utilisateur avec son numéro et son nom public
function supprimeutilisateur($var3,$nompublic){
  $baseutilisateurs = constante("baseutilisateurs");
  $cheminfichier = tracelechemin("",$baseutilisateurs,".baseconsignel3");
  $fichierencours = fopen($cheminfichier, 'r+'); // ouverture en lecture ecriture autorisée pointeur au début
  while (!feof($fichierencours) ) { // cherche dans les lignes
    $ligne = fgets($fichierencours, 1024); // ligne par ligne
    $lignedenclair = decryptelestockage($ligne);
    list($numutil, $numpass, $nompublic2, $avatar, $localite,$nompublic2) = explode(",", $lignedenclair);   
    if (($numutil == $var3) && ($nompublic == $nompublic2)){ 
      file_put_contents($cheminfichier, str_replace($ligne, "", file_get_contents($cheminfichier)));
      }; // fin du trouvé obsolete
  }; // Fin de cherche dans les lignes
  fclose($fichierencours); // fermeture du fichier
};

// supprime le dossier de l'utilisateur supprimedossierutilisateur("00000","base");
function supprimedossierutilisateur($numerodossier=""){
  $base = constante("base");
  $numdossier = "".$numerodossier;
  $chemindossier = substr(tracelechemin($numdossier,$base,"",""), 0, -1);
  if(is_dir($chemindossier)){
    $repertoire = opendir($chemindossier);
    while (false !== ($fichier = readdir($repertoire))) {
      $cheminfichier = $chemindossier."/".$fichier; 
      if ($fichier != ".." AND $fichier != "." AND !is_dir($fichier)) { unlink($cheminfichier);  }; 
    }; //fin de la boucle sur le répertoire pour effacer les fichiers
    closedir($repertoire);
    rmdir($chemindossier); // efface répertoire
    $dernierslash = strrpos($chemindossier, "/",-2); $chemindossier = substr($chemindossier,0,$dernierslash);
    if($chemindossier."/" != $base){rmdir($chemindossier);}; // efface répertoire parent
    $dernierslash = strrpos($chemindossier, "/",-2); $chemindossier = substr($chemindossier,0,$dernierslash);
    if($chemindossier."/" != $base){rmdir($chemindossier);};  // efface répertoire grand parent
  };
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

// pour tester si le destinataire de la proposition est autorisé pour acceptation d'une proposistion
function testdestinataire($pourqui,$demandeur){
  $desti = preg_replace( "/\D/", "", $pourqui) ; $tesqui = preg_replace( "/\D/", "", $demandeur) ;
  if($desti == "0"){ return "autorise" ; };
  if($tesqui == $desti){ return "autorise" ; };
  if(lepseudode($tesqui, "nopseudo") == $desti){ return "autorise" ; };
  return "nonautorise" ; 
};
// pour tester si le destinataire de la proposition est autorisé pour dépot d'une proposistion
function testdestinatairedepot($pourqui,$demandeur){
  $desti = preg_replace( "/\D/", "", $pourqui) ; $tesqui = preg_replace( "/\D/", "", $demandeur) ;
  if($desti == "0"){ return "autorise" ; };
  if($tesqui == $desti){ return "nonautorise" ; };
  if(lepseudode($tesqui, "nopseudo") == $desti){ return "nonautorise" ; };
  return "autorise" ; 
};

// teste l'actualité des opportunites
function testemesopportunites($var3,$mesopportunites){
  if(substr($mesopportunites,0,4)=="NULL"){return "OPPV - Manque d'opportunités personnalisées";};
  $mesopportunites=substr($mesopportunites,0,-4);
  if($mesopportunites==""){return "OPPV - Manque d'opportunités personnalisées";};
  $jsonenphp = json_decode($mesopportunites,true);
  if(json_last_error_msg() != "No error"){ return "TEST - erreur du serveur d'opportunités ".$mesopportunites; };
//  $nbentrees=count($jsonenphp);
//  for ($i = 0; $i <= $nbentrees-1; $i++) { 
//    $jsonenphp[$i]=$jsonenphp[$i]; 
//  };    
  $phpenjson= json_encode($jsonenphp);
  return $phpenjson;
};

// teste si l'utilisateur est inscrit dans la base
function testeutilisateurunique($numprive,$nompublic){
  $nompublic="\"".$nompublic."\"";
  $baseutilisateurs = constante("baseutilisateurs");
  $cheminfichier = tracelechemin("",$baseutilisateurs,".baseconsignel3");
  $fichierencours = fopen($cheminfichier, 'r'); // ouverture en lecture seule pointeur début fichier
  $etatutilisateur = "inconnu";
  $existe = FALSE; // Testeur de boucle 
  while (!feof($fichierencours)  && !$existe ) { // cherche dans les lignes
    $ligne = fgets($fichierencours, 1024); // ligne par ligne
    $lignedenclair = decryptelestockage($ligne);
    list($numutil, $numpass, $nompublic2, $avatar, $localite,$numpublic) = explode(",", $lignedenclair);   
    if ($numprive == $numutil){ $etatutilisateur = "numpriveconnu"; $existe = TRUE; }; // numprivé trouvé
    if ($nompublic == $nompublic2){ $etatutilisateur = "nompublicconnu"; $existe = TRUE; }; // nompublic trouvé
  }; // Fin de cherche dans les lignes
  fclose($fichierencours); // fermeture du fichier
  return $etatutilisateur;
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
      if (substr($chemin,0,1)=="/"){$chemin=substr($chemin,1);};
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

// renvoie la proposition d'avant le 20200801 de transaction avec le format d'après
function transactionformat202008($chainejson){
  $pos = strpos($chainejson, "[");
  $debut=substr($chainejson,0,$pos-1);
  $fin=substr($chainejson,$pos);
  return $debut." { \"sommaire\":".$fin." }";
};

// renvoie la proposition acceptée ou annulée
function transactionaccann($prefixe,$idtra,$contenufichiertra,$dateaccepte,$noaccepteur,$nomproposeur=""){
  $notra = $prefixe.substr($idtra,3);
  $datetra=substr($idtra,3,8);
  $acclocal = $contenufichiertra;
  $cherchenotra = "/(".$idtra.")/"; 
  $acclocal = preg_replace( $cherchenotra, $notra , $acclocal);
  if($datetra<"20200801"){ $acclocal=transactionformat202008($acclocal); };
  $acclocalenphp = json_decode($acclocal,true);
  $acclocalenphp[$notra]["sommaire"][2] = $dateaccepte;
  $acclocalenphp[$notra]["sommaire"][3] = $noaccepteur;
  if(!$acclocalenphp[$notra]["sommaire"][5]){
    if($nomproposeur!=""){$acclocalenphp[$notra]["sommaire"][] = $nomproposeur;};
  };
  $acclocal = json_encode($acclocalenphp);
  return $acclocal;
};

// retourne le statut de la transaction ADAC - PACC - PANN - PEXP - AEXP - PACT - DTAO - TNDI ... sans vérification complémentaire
function transactionstatut($demandeur, $notransaction){
  $nodemandeur = $demandeur; 
  $idtra = "tra".$notransaction; 
  $cheminfichier = testelechemin($idtra); // chemin dans base2 par date
  $debut = "";
  // l'orde des tests est important
  if (file_exists($cheminfichier."acc".$notransaction.".json")) { 
    $fichierencours = fopen($cheminfichier."acc".$notransaction.".json", 'r');
    $ligne = decryptelestockage(fgets($fichierencours, 1024)); // une seule ligne
    list($var41, $var42, $var43, $var44, $var45, $var46, $var47, $var48) = explode(",", $ligne);
    $proposeur = preg_replace( "/\D/", "", $var48);
    if($var48=="\"DA↺\"\n"){$pseudo = "\"DA↺\"\n";}else{$pseudo = lepseudode($proposeur);};
    if ($var45 == "\"".$nodemandeur."\""){ return "ADAC - ".$pseudo."\"".contenutra($cheminfichier.$idtra.".json"); }; // Proposition déjà acceptée par vous
    if ($var48 == "\"".$nodemandeur."\"\n"){ return "PACC - ".contenutra($cheminfichier.$idtra.".json"); }; // Cette proposition faite par vous a déjà été acceptée
    
//    $nodestinataire = pourqui($cheminfichier."acc".$notransaction.".json");
    if (file_exists($cheminfichier."tra".$notransaction.".json")) { 
      $fichierencours2 = fopen($cheminfichier."tra".$notransaction.".json", 'r');
      $ligne2 = decryptelestockage(fgets($fichierencours2, 1024)); // une seule ligne
      list($var41b, $var42b, $var43b, $var44b, $var45b) = explode(",", $ligne2);
    };
    // si la proposition est unique renvoyer TNDI
    if($var44b=="\"0\""){
      // $destinataire="pourtous";
      // Test expiration
      $dureeexpire = substr($var45b, 1,-2);
      if(testeexpiration($var41,$dureeexpire)=="pasexpire"){
        // vérifier la disponibilité en stock
        $disponible="oui";
        if($disponible=="oui"){
          
          return "DTAR - ".$pseudo."\"".contenutra($cheminfichier.$idtra.".json");     // Demande de transaction autorisée à tous réutilisable Envoi multiple du lien  

        };
      };
    };// si la proposition est non attribuée non expirée et disponible renvoyer DTAR
    return "TNDI - Cette proposition n'est pas disponible";
    
  }; // ADAC - PACC - TNDI
  if (file_exists($cheminfichier."ann".$notransaction.".json")) { 
    $fichierencours = fopen($cheminfichier."ann".$notransaction.".json", 'r');
    $ligne = decryptelestockage(fgets($fichierencours, 1024)); // une seule ligne
    list($var41, $var42, $var43, $var44, $var45, $var46, $var47, $var48) = explode(",", $ligne);
    if (($var48 == "\"".$nodemandeur."\"\n")&&($var45 == "\"".$nodemandeur."\"")){ return "PANN - ".contenutra($cheminfichier.$idtra.".json"); }; // Proposition déjà annulée par vous
    if ($var45 == "\"".$nodemandeur."\""){ 
      if($var48 == "\"DA↺\"\n"){$pseudo="DA↺";}else{$pseudo=lepseudode(substr($var48,1,-2));};
      return "TREM - ".$pseudo."\"".contenutra($cheminfichier.$idtra.".json"); 
    }; // Proposition refusée par vous
    // retire de la liste des opportunités du demandeur
    retireopportunite($nodemandeur,substr($notransaction,1,-1));
    return "TNDI - Cette proposition n'est pas disponible ";
  }; // PANN - TNDI -
  if (file_exists($cheminfichier."exp".$notransaction.".json")) { 
    $fichierencours = fopen($cheminfichier."exp".$notransaction.".json", 'r');
    $ligne = decryptelestockage(fgets($fichierencours, 1024)); // une seule ligne
    list($var41, $var42, $var43, $var44, $var45, $var46, $var47, $var48) = explode(",", $ligne);
    if ($var48 == "\"".$nodemandeur."\"\n"){ return "PEXP - ".contenutra($cheminfichier.$idtra.".json"); }; // Proposition de votre part expirée sans être acceptée
    if ($var48 == $var3){ return "PEXP - ".contenutra($cheminfichier.$idtra.".json"); }; // Proposition de votre part expirée sans être acceptée
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
      $proposeur = preg_replace( "/\D/", "", $var48);      
      if ($var41 == "\"".$idtra."\""){
        $memetransaction = TRUE; // transaction trouvée
        $ligneexiste = TRUE;
        $expiration = testeexpiration($var41,$var46);
        $propositionexpire ="";
        if ($var48 == "\"".$nodemandeur."\"\n"){ 
          if ($expiration == "expire"){ 
            $propositionexpire = annuleproposition($proposeur,$notransaction,"PEXP - ".contenutra($cheminfichier.$idtra.".json"));
            return $propositionexpire ; 
          }; // C'est ma proposition expirée 
          if ($expiration == "pasexpire"){ return "PACT - ".contenutra($cheminfichier.$idtra.".json"); }; // C'est ma proposition active
        }else{
          $testdestinataire = testdestinataire($var45,$nodemandeur);
          if ($testdestinataire == "nonautorise"){ return "TNDI - Cette proposition n'est pas disponible "; }; // Cette proposition n'est pas disponible
          if ($expiration == "expire"){ 
            $propositionexpire = annuleproposition($proposeur,$notransaction,"AEXP - ".contenutra($cheminfichier.$idtra.".json"));
            return $propositionexpire ;
          }; // La proposition est expirée 
          if($var48=="\"DA↺\"\n"){$pseudo = "\"DA↺\"\n";}else{$pseudo = lepseudode($proposeur);};
          if ($testdestinataire == "autorise"){ 
           if(lepseudode($nodemandeur)=="\"inscription\""){
            return "NUCI - ".$pseudo."\"".contenutra($cheminfichier.$idtra.".json"); // changer .baseconsignel3
          };
          return "DTAO - ".$pseudo."\"".contenutra($cheminfichier.$idtra.".json"); 
          }; // J'ai le droit d'accepter cette proposition mais attention à disponibilité"; };
        };
      }; // fin de transaction trouvée
    }; // fin du while
  }; // PEXP - PACT - DTAO - TNDI
  return "TRIN - Transaction inconnue";
};

// définition des constantes selon la localité pour les calculs
function constante($nom){
  if($nom == "paiements"){ return '["$_18702","$_25343","mlc_41642",mlc_51083","↺_629160","↺_721781","↺_2220560"]'; };
  if($nom == "minimumviable"){ return 15; };
  if($nom == "minimumviableparjour"){ return 37.4; }; // minimumviable * 52semaines * 17,5h / 365j
  if($nom == "salairehmoyen"){ return 30; };
  if($nom == "coefsalairemoyen"){ return 2; };
  if($nom == "coefsalaireindecent"){ return 20; }; //  revenuindecent = dureeactiv * minimumviable * coefsalaireindecent;
  if($nom == "maxcompte"){ return 54600; }; //  = salairehmoyen * 52semaines * 35h;
  if($nom == "base"){ return "../consignel-base/"; }; // pour utilisation depuis le php
  if($nom == "basedac"){ return "../consignel-base/1/"; }; // pour utilisation depuis le php
  if($nom == "baseutilisateurs"){ return "../consignel-base/0"; };
  if($nom == "basehistorique"){ return "../consignel-base/2/"; };
  if($nom == "baseavatars"){ return "../consignel-app/"; }; 
  if($nom == "baseavatarperso"){ return "../consignel-base/"; }; // pour utilisation depuis le php
  if($nom == "avatar"){ return "avatar01.png"; };
  if($nom == "baselocalite"){ return "../localite/"; }; 
  if($nom == "localite"){ return "Marieville"; }; 
  if($nom == "ouverturecompte"){ return '"200,100,20,300,0"'; };
  if($nom == "memoiredac"){ return 720; }; // mémoire des transactions refusées nombre de jours
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


?>