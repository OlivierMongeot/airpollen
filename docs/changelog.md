# Changelog AQI & Pollen

# 26/06/2021

- Version pre-beta du plugin pour tests

# 12/07/2021

- Ajout bouton : ne pas afficher les pollens à zéro
- commandes inutiles effacées en cas de switch Pollen/Aqi
- Messages d'alertes paramétrables 

# 18/07/2021

- Messages d'alertes affichés sur le widget
- Correction de bugs
- Formatage des alertes pour SMS et telegram dispo dans 2 nouvelles commandes info 

# 19/07/2021

- Bouton afficher tendances/min/max/moyenne pour toutes les infos 
- Affichage des 3 familles de pollens sur 4 au total par niveaux décroissant. 
- Formatage des alertes en Markdown et Html pour pre-connection à Discord, Telegram ou autres (nouvelles commande info dispo : markdownPollution et markdownPollen)
- Affinage des niveaux d'alertes 
- Décalage alerte Pollen / AQI de 1 min pour ne pas avoir toutes les alertes en même temps

# 20/07/2021
- Affichage des 4 familles 
- Bouton afficher/historiser les tendances/min/max/moyenne pour toutes les infos
- Affinage des niveaux d’alertes pollen et AQI
- Décalage alerte Pollen / AQI de 1 min pour ne pas avoir toutes les alertes en même temps
- Synchro des réglages Alerte & Affichage : vous affichez les éléments sur le slide en fonction de sa valeur mini, même principe que pour les alertes
- Affichage de la 4ème famille de pollen sur le header si elle est > 0
- Affichage des polluants classés par ordre décroissant d’indice de pollution comme pour les pollens

# 22/07/2021

- Redesing de la configuration 'Paramétrage des alertes'
- Synchro 'Alerte et affichage' au choix pour les polluants (au lieu d'obligatoire)
- Reglage du niveau mini d'affichage des pollens de zéro à 100 (au lieu de zéro ou tous) 
- Debug CRON forecast Pollen ajout fonction de reflexion
- Affichage 'Pas d'indice en alerte' au lieu de 'Indices en alerte 0 / 15'
- Affichage 'Aucun pollen actif' au lieu de 'Pollens actifs 0 / 15'
- Optimisation / allègement du code 

# 28/07/2021

- ajout mode mobile Follow Me 
- refactoring 

# 9/08/2021 

- Déclenchement des refreshs aléatoire fixé à la sauvegarde suite aux mails reçu de Ambee pour décaler les calls des utilisateurs

# 17/08/2021 

- Possibilité de ne pas afficher les forecasts pour un affichage plus simple 
- Debug affichage icones telegram







