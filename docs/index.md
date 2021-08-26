# Plugin Jeedom AirQuality

<br/>

<img align="right" height="350" src="https://github.com/OlivierMongeot/airquality/blob/Master/docs/photos/alerte.JPG" class="img-responsive" alt="Pollen">Le plugin Air Quality & Pollen vous informe sur la qualité de l'air et les pollens presque partout dans le monde.

Il s'adresse essentiellement aux personnes allergiques, asthmatiques, sensibles et sportifs, mais aussi à tous ceux qui prennent attention à l'air qu'il respire. 

## Prévisions & live

Grâce aux informations des prévisons et données live, vous pouvez manager votre planning et vous prémunir des polluants ou pollens. 
Vous accèdez aux informations en direct mais aussi aux prévisions sur 5 jours pour la pollution et sur 2 jours pour les pollens.

Les polluants et pollens sont affichés par niveau décroissant pour simplifier la lisibilité. 

## Alertes

Les alertes s'affichent sur le widget, mais sont aussi préformatées et dispo dans une commande info, vous pouvez facilement les remonter dans vos SMS, Discord(Markdown) et Télégram(HTML) par exemple. Une explication détaillé se trouve plus bas.

Des messages d'alertes sont crées en fonctions des changements des données les déclenchements sont paramétrables individuelements.

Regardez le bouton 'Alertes' sur la page de configuration de votre équipement, c'est là que vous pouvez tout régler.

## Mobilité

<img align="left" height="350" src="https://github.com/OlivierMongeot/airquality/blob/Master/docs/photos/pollenmobile.JPG" class="img-responsive" alt="Pollen">Une option est dispo dans la nouvelle version, le 'FollowMe', cela fonctionne avec la wep app mobile de Jeedom, un bouton vous géolocalise et permet de mettre à jour les données en fonction (fonctionne en https uniquement).
><br/>
Cela vous permet d'avoir les infos locales partout où vous vous trouver (dans les limites des possibilités des API).

Avec cette option, le bouton refresh du desktop de l'équipement est désactivé, pour laisser la main à la page mobile.  

<br/><br/><br/><br/><br/>

## Historisation

J'ai repris le système de Jeedom pour l'historisation des données.

Les Mini, Maxi, Moyenne et Tendance sont affichés seule le timing est modifié pour l'adapter au plugin.(Mini, Maxi, Moyenne sur 10j et Tendance sur 12h)  

Cela historise aussi les données et donne accès la représentation graphique classique de Jeedom.

Vous pouvez activer ou pas l'ensemble en activant le bouton ci-dessous dans les paramétrages.
<br/><br/>
<p align="center">
  <img height="60" src="https://github.com/OlivierMongeot/airquality/blob/Master/docs/photos/tendance.JPG" class="img-responsive" alt="Pollen">
</p>
<br/>

## Les APIs

Les données sont récupérées par trois API gratuites: Openwheather, Ambee et navigator.geolocation(HTML)

Elles sont gratuites dans une certaine limite d'appels par jour. Dans l'applis, pour Ambee, le plugin est conçu pour être aux limites du gratuit, c'est à dire 100 appels/jour. Cepandant, certains utilisateurs on reçu un courrier de la Sté Ambee, leur stipulant que la version gratuite sera limité dans le temps.
Il faudrait alors basculer vers leur abonnement payant.  


Les prévisons de pollens sont mises à jour une fois par jour seulement, j'ai choisi 7h du matin, car seulement 24h sont en fait disponibles. Ce qui veux dire que pour le troisième jour de prévision, les infos vont jusqu'a 7h00 du matin et donc sont suceptiblent dévoluer en s'affinant.


<img align="right" height="300" src="https://github.com/OlivierMongeot/airquality/blob/Master/docs/photos/pollenzero.JPG" class="img-responsive" alt="Pollen">C'est un choix fait pour ne pas atteindre la limite de 100 appels/jour de l'API Ambee et donc que le plugin reste gratuit à l'usage. 

En effet, lors du refresh des prévisons pollens l'api compte 1 appel par heure de données fournie, donc l'appel forecast sur 48 heures coûte 48 appels sur les 100 journalier. Ce qui explique mon choix d'appel une fois par jour. 

Pour OpenWheather, la limite d'appel par jour est beaucoup plus haut, donc pas de soucis de quota. 

J'ai mis en place, un systeme de bridage qui vous empêche des rafraichissements trop important des données.

Les deux Api fonctionnent presque partout dans le monde et vous pouvez l'utilisez en vous géolocalisant automatiquement.

Les normes utilisées sont ceux de l'Agence européenne pour l'environnement 2021.

<br/>

# Configuration principale du plugin

Après avoir installé le plugin, il faut l’activer puis renseigner votre clef api.

Si vous avez déjà une clef pour le plugin Weather officiel de Jeedom, la clef s'importe dans le plugin en cliquant sur import. Vous n'avez donc pas besoin de nouvelle clef.    

Pour obtenir une clef api OpenWheather il faut aller [ici](https://home.openweathermap.org), créer un compte gratuit et ensuite il faut copier votre clef api dans la zone prévue sur la page "Configuration du Plugin".

Pour obtenir la clef api ambee il faut aller [ici](https://api-dashboard.getambee.com/#/signup), c'est à peu près le même principe qu'avant : vous récupérez une clef gratuite que vous coller dans la configuration du plugin".


# Configuration principale de l'équipement

Vous avez quatre choix de localisation : 

1. Par ville  : vous rentrez le nom d'une ville et son code pays, vous testez si l'API reconnait bien cette ville, puis vous enregister.
2. Par longitute & latitude : Si vous avez dèjà des coordonnées, vous pouvez les rentrées ici, cela permet aussi de retrouver le nom du lieu avec les coordonnées.
3. Par géolocalisation automatique du navigateur : rapide et pratique si vous accèdez à votre jeedom en déplacement ou en vacances, il se base sur les coordonnées du navigateur.
4. Grâce aux infos localisations Jeedom si elle sont déjà présentes dans la configuration générale du soft


Pour la localisation 'Par ville', vous devez vérifier que la ville est bien trouvée par l'appli en cliquant sur vérifier avant de sauvegarger. 
'Par longitude & latitude', pareil, vous devez vérifier que les coordonnées que vous entrez soient valables.

Vous pouvez également activer, le glissement automatique du carroussel, pour cela mettez Animation en 'activer' 

Ensuite choisissez Polluant ou Pollen. 

Vous pouvez créer plusieurs équipement pour plusieurs villes pour les données AQI. Par contre, pour les pollens, en faisant cela, vous dépassez directement le quota journalier. 

Vous devrez alors choisir une version payante de l'API Ambee.


## Utilisation

En cliquant sur les moyennes vous accèder au graphique représentant ces données (fonction native de Jeedom)

Le graphique du bas donne le mini et maxi prévu par jour de la semaine pour chaque élément analysé.

Pour l'AQI, tous les polluants sont affichés par défault, il vous suffit de désactiver l'option "Afficher" dans la partie Commandes de votre équipement pour ne plus l'afficher.   

Pour les pollens, tous sont affichés par défaut, le plugin les classe automatiquement par ordre décroissant de risque.

Les pollens non détéctés (à zéro) sont affichés à part dans un tableau à la fin du caroussel comme la photo ci-dessus ou il peuvent ne pas être affichés en réglant la molette ci-dessous :

<p align="center">
  <img height="70" src="https://github.com/OlivierMongeot/airquality/blob/Master/docs/photos/displaypollenrange.JPG">
</p>

Il existe une version mobile qui reprend pratiquement exactement la vue du dashboard.  

# Important

L'AQI en Europe est désormais calculée de 1 (Bon) à 6 (Extrèmement mauvais)

J'ai utilisé les code couleur officiel pour l'affichage : [voir ici](https://fr.wikipedia.org/wiki/Indice_de_qualit%C3%A9_de_l%27air)

Pour les pollens les niveaux de danger principaux sont donnés par l'API Ambee (de 1 Risque nul à 4 Risque très élevé).

Vous pouvez régler les alertes par polluant dans la configuration : 

<p align="center">
<img height="300" src="https://github.com/OlivierMongeot/airquality/blob/Master/docs/photos/plancherAqi.JPG">
</p>

J'ai calé les niveaux alertes par pollens, les calculs sont basés sur un Mémoire de l’École Nationale de la Santé Publique et une étude de l'ENSP (pdf dans le repertoire docs) :

Les niveaux d'alertes moyen sont basées sur des seuils d'environs 40 particules/m³, ce qui correspond au plancher de niveau de sensibilité de la plupart des personnes allergiques. Certains sujets hypersensibles, sont touchés dès 5 part/m³.  

Mais comme tout le monde est différent, vous pouvez régler les alertes selon vos caractéristiques.

<p align="center">
<img height="400" src="https://github.com/OlivierMongeot/airquality/blob/Master/docs/photos/rangesPollen.JPG">
</p>

# Note

Le rafraîchissement des données AQI s’effectue toutes les 30 minutes et le forecast trois fois par jour à 7h00, 12h00 et 18h00

Le rafraîchissement des données Pollen s’effectue toutes les 60 minutes et le forecast tous les matin à 7h00

Ce plugin est simplement informatif, il ne remplace en aucun cas pas d'éventuels indications données par votre médecin ou tout autre corps médicale.



# Gestion des alertes 

Pour connecter le plugin à Telegram, Discord ou un téléphone(sms), c'est le même principe :

Ex pour les pollens vers Telegram : 
- Il faut avoir le plugin Telegram installé auparavant.
- Le principe :  Vous allez récupérez le message d'alerte toutes les heures, juste après la mise à jour des infos qui est à la minute 0 de chaque heure et l'envoyer vers votre bot Telegram
 
## Créez un nouveau scénarion Jeedom :

- Declenchement -> Programation  : ``` */2 * * * * ```     soit toute les 2 minutes (la durée de vie d'un message)
- Dans l'onglet Scénario :  
-   Ajouter un bloc SI/ALORS/SINON
-   Insérez un SI : ``` #[nom_de_votre_objet][nom_de_votre_equipement_pollen][Alerte Pollen]# !='' ``` (cherchez avec la recherche simplifié)
-   Pas d’espace entre les '' et un != pas de == (Ce qui va déclencher un message Telegram, seulement si un message est disponible.)
-   ALORS -> action  inserez ```#[nom_de_votre_objet][nom_de_votre_equipement_telegram][votre bot]#```
-   Puis dans message vous allez cherchez la commande du plugin qui s'appelle : Markdown Pollen
-   Cela donne : ```#[nom_de_votre_objet][nom_de_votre_equipement_pollen][Markdown Pollen]#```
-   Option : vide 

Cela devrait marcher, le cas échéant, faites moi remonter vos problèmes.


Exemple Message Telegram :

<p align="center">
<img height="300" src="https://github.com/OlivierMongeot/airquality/blob/Master/docs/photos/telegram.jpg">
</p>


Exemple Message Discord :

<p align="center">
<img height="60" src="https://github.com/OlivierMongeot/airquality/blob/Master/docs/photos/discord.JPG">
</p>

