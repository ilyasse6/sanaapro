# TadbirPro — Description complète du projet

## 1. Présentation générale

TadbirPro est une application web de gestion d’entreprise conçue pour centraliser, dans un même environnement, les activités commerciales, administratives, financières et opérationnelles d’une ou de plusieurs sociétés.

L’application couvre principalement :

- la gestion des entreprises et de leurs utilisateurs ;
- la gestion des clients et des rendez-vous ;
- les catalogues de services et de packs ;
- les devis, factures, bons de livraison et bons de commande ;
- la gestion des marchés publics et des cautions ;
- les fournisseurs et le stock ;
- la comptabilité simplifiée ;
- les employés et les congés ;
- les alertes, notifications et paramètres d’identité ;
- une API sécurisée destinée à de futures intégrations.

TadbirPro est une application **multi-entreprise (multi-tenant)** : chaque entreprise travaille dans son propre périmètre de données. Le Super Admin conserve une vision globale, tandis que les utilisateurs standards sont limités à l’entreprise à laquelle ils appartiennent.

Le projet vise actuellement un usage de gestion interne, de démonstration commerciale et de base évolutive pour un futur produit SaaS.

---

## 2. Objectifs du projet

### 2.1 Objectif principal

Réduire la dispersion des informations entre fichiers Excel, documents Word, échanges de messagerie et outils isolés, en proposant une plateforme unique pour suivre le cycle de gestion d’une entreprise.

### 2.2 Objectifs opérationnels

- Centraliser les données clients, documents commerciaux et opérations.
- Automatiser la génération des références et numéros de documents.
- Produire des documents PDF cohérents avec l’identité visuelle de chaque entreprise.
- Donner aux responsables une vision synthétique de l’activité.
- Donner aux comptables une vision financière adaptée à leur fonction.
- Contrôler les accès selon les profils et les permissions.
- Isoler les données de chaque entreprise.
- Faciliter le suivi des échéances : factures, cautions, rappels et stock.
- Préparer l’application à une utilisation via API ou application mobile.

### 2.3 Utilisateurs cibles

L’application s’adresse notamment :

- aux TPE et PME ;
- aux sociétés de services et de fourniture ;
- aux entreprises participant à des appels d’offres ;
- aux directions administratives et commerciales ;
- aux comptables ;
- aux responsables de stock ;
- aux gestionnaires de marchés ;
- aux structures souhaitant centraliser plusieurs entreprises sur une même plateforme.

---

## 3. Profils et responsabilités

### 3.1 Super Admin

Le Super Admin administre toute la plateforme.

Ses fonctions principales sont :

- consulter toutes les entreprises ;
- créer, modifier, activer ou désactiver une entreprise ;
- accéder à tous les utilisateurs ;
- gérer les profils, permissions et postes ;
- intervenir sur les paramètres de chaque entreprise ;
- sélectionner l’entreprise à configurer ;
- accéder aux données globales qui ne sont pas limitées par le tenant courant ;
- conserver l’accès aux fonctions d’administration système.

Les routes des entreprises, permissions, profils et postes sont protégées par un middleware spécifique. Un utilisateur non Super Admin qui tente d’ouvrir directement ces URL est redirigé vers l’accueil avec un message d’accès réservé.

### 3.2 Directeur

Le Directeur pilote l’activité de son entreprise.

Selon les permissions qui lui sont attribuées, il peut :

- gérer les utilisateurs de sa propre entreprise ;
- consulter et gérer les clients ;
- suivre les rendez-vous et les visites ;
- gérer les devis et les factures ;
- suivre les documents commerciaux ;
- consulter les indicateurs de l’entreprise ;
- administrer les modules métier autorisés ;
- configurer les alertes et l’identité de son entreprise.

Le Directeur n’a pas accès à l’administration système globale.

### 3.3 Comptable

Le Comptable dispose d’un tableau de bord spécifique contenant :

- le chiffre d’affaires facturé du mois ;
- les encaissements ;
- les décaissements ;
- le solde mensuel ;
- l’encours clients ;
- le montant des factures échues ;
- les échéances prioritaires ;
- les mouvements comptables récents ;
- la TVA facturée ;
- le nombre et le taux de factures réglées ;
- les raccourcis vers la facturation et la comptabilité.

Il peut également, selon ses droits :

- consulter et gérer les factures ;
- enregistrer des mouvements comptables ;
- consulter les clients et les devis ;
- consulter les bons de livraison et de commande ;
- consulter les marchés et cautions.

Toutes les données affichées au Comptable sont limitées à son entreprise.

### 3.4 Profils multiples

Un utilisateur peut être associé à plusieurs profils. L’application possède un mécanisme de sélection de profils actifs. Les permissions effectives sont calculées à partir des profils actifs, des permissions valides et du périmètre entreprise.

---

## 4. Architecture technique

### 4.1 Technologies principales

- **Backend :** PHP 8.2 ou version compatible.
- **Framework :** Laravel 11.
- **ORM :** Eloquent.
- **Authentification web :** Laravel Breeze et sessions.
- **Authentification API :** Laravel Sanctum.
- **Base de données :** MySQL/MariaDB en exploitation courante ; Laravel permet une adaptation à d’autres moteurs.
- **Frontend :** Blade, Bootstrap, JavaScript, Alpine.js et composants personnalisés.
- **Build frontend :** Vite.
- **CSS utilitaire disponible :** Tailwind CSS.
- **PDF :** `barryvdh/laravel-dompdf`.
- **Cartographie :** Leaflet avec des tuiles cartographiques externes.
- **Notifications :** notifications Laravel stockées en base et interrogées périodiquement par l’interface.

### 4.2 Organisation Laravel

Le projet suit l’organisation classique de Laravel :

- `app/Http/Controllers` contient la logique des pages web et des API ;
- `app/Models` contient les entités Eloquent ;
- `app/Http/Middleware` contient les contrôles d’accès et de tenant ;
- `app/Support` contient les services transversaux ;
- `database/migrations` définit la structure de la base ;
- `database/seeders` initialise les profils, permissions et données de base ;
- `resources/views` contient les vues Blade ;
- `routes/web.php` contient les routes web ;
- `routes/api.php` expose l’API versionnée ;
- `storage/app/public` stocke les logos et documents téléversés.

### 4.3 Multi-tenant

Le multi-tenant repose sur :

- la colonne `id_entreprise` dans les tables métier ;
- le middleware `SetTenantFromAuthenticatedUser` ;
- le contexte `CurrentTenant` ;
- le scope global `TenantEntrepriseScope` ;
- le trait `BelongsToEntreprise`.

Pour un utilisateur standard, le middleware sélectionne son entreprise et les modèles concernés ajoutent automatiquement un filtre SQL sur `id_entreprise`.

Pour le Super Admin, aucun tenant n’est imposé par défaut. Certaines pages lui permettent de sélectionner explicitement l’entreprise à administrer.

À la création d’une donnée métier, le trait multi-tenant attribue automatiquement l’entreprise courante lorsqu’elle n’est pas fournie.

### 4.4 Permissions

Les permissions sont organisées autour de :

- modules ;
- actions telles que consulter, ajouter, modifier, activer et désactiver ;
- profils ;
- associations utilisateur-profil ;
- associations profil-permission ;
- périmètre global ou limité à une entreprise ;
- dates de début et de fin de validité.

Le contrôle est appliqué dans les contrôleurs, dans les vues et dans l’API.

---

## 5. Description fonctionnelle des modules

## 5.1 Authentification et compte personnel

### Fonctions

- connexion et déconnexion ;
- réinitialisation du mot de passe ;
- confirmation du mot de passe ;
- vérification d’adresse e-mail ;
- modification du profil personnel ;
- modification du mot de passe ;
- sélection des profils actifs.

### Perspective

- ajouter l’authentification à deux facteurs ;
- ajouter une politique de durée et de complexité des mots de passe ;
- afficher l’historique des connexions ;
- permettre la révocation individuelle des sessions et appareils.

### Limites

- absence actuelle de 2FA ;
- la gestion avancée des sessions et appareils n’est pas proposée ;
- les règles de sécurité dépendent encore fortement de la bonne configuration des profils et permissions.

---

## 5.2 Tableau de bord

### Fonctions

Le tableau de bord général présente des statistiques réelles issues de la base :

- factures du mois ;
- devis en attente ;
- niveau de stock ;
- clients actifs ;
- chiffre d’affaires ;
- panier moyen ;
- encaissements ;
- factures en attente ;
- répartition des paiements ;
- dernières factures ;
- articles à réapprovisionner ;
- actions rapides ;
- visites, rendez-vous et rappels selon les permissions.

Le profil Comptable reçoit une vue distincte centrée sur les données financières et les échéances.

### Perspective

- ajouter des filtres de période ;
- comparer plusieurs périodes ;
- permettre l’export Excel/PDF ;
- créer des widgets configurables par profil ;
- ajouter un budget prévisionnel et une comparaison réel/prévisionnel.

### Limites

- les indicateurs sont prédéfinis et non personnalisables ;
- il n’existe pas encore de moteur analytique ni de reporting multidimensionnel ;
- les chiffres dépendent de la qualité des statuts et mouvements saisis.

---

## 5.3 Gestion des entreprises

### Fonctions

- création d’une entreprise ;
- définition de la description, de l’abréviation et de la forme juridique ;
- modification ;
- activation et désactivation ;
- association des utilisateurs ;
- association des permissions par entreprise.

### Accès

Ce module est réservé au Super Admin, y compris en cas d’accès direct par URL.

### Perspective

- ajouter ICE, IF, RC, CNSS, adresse et coordonnées légales ;
- ajouter abonnements, plans tarifaires et quotas ;
- ajouter état de facturation SaaS ;
- permettre une personnalisation complète par entreprise.

### Limites

- les informations juridiques et fiscales de l’entreprise sont encore limitées ;
- il n’existe pas de gestion d’abonnement ou de facturation de la plateforme ;
- la suppression logique repose essentiellement sur le champ actif.

---

## 5.4 Gestion des utilisateurs

### Fonctions

- création d’un utilisateur ;
- nom, prénom, e-mail, téléphone et mot de passe ;
- affectation à une entreprise ;
- affectation d’un poste ;
- affectation d’un ou plusieurs profils ;
- activation et désactivation ;
- consultation des utilisateurs actifs et inactifs ;
- vérification d’unicité de l’e-mail.

### Règle multi-entreprise

- le Super Admin voit tous les utilisateurs ;
- les autres profils voient uniquement les utilisateurs de leur entreprise ;
- la création, modification, activation et désactivation respectent le même périmètre.

### Perspective

- import CSV/Excel ;
- invitation par e-mail ;
- journal des changements de droits ;
- gestion des équipes et services ;
- gestion des remplacements temporaires.

### Limites

- seuls les postes Directeur et Comptable sont actuellement proposés dans le formulaire principal ;
- pas de workflow d’invitation ou de validation hiérarchique ;
- pas de vue complète des sessions actives.

---

## 5.5 Profils, permissions et postes

### Fonctions

- création et gestion des profils ;
- création et gestion des permissions ;
- rattachement des permissions aux profils ;
- périmètre global ou par entreprise ;
- périodes de validité ;
- activation et désactivation ;
- gestion des postes.

### Accès

Ces modules sont réservés au Super Admin.

### Perspective

- proposer des modèles de rôles ;
- afficher une matrice visuelle rôles × permissions ;
- détecter les conflits de droits ;
- permettre la copie d’un profil ;
- ajouter un audit des changements.

### Limites

- le modèle est puissant mais complexe à administrer ;
- une mauvaise combinaison de permissions peut produire un comportement inattendu ;
- la couverture automatisée des scénarios de permissions reste insuffisante.

---

## 5.6 Gestion des clients

### Fonctions

- création et modification d’un client ;
- consultation des clients actifs ;
- désactivation et réactivation ;
- informations d’identité et de contact ;
- informations administratives et commerciales ;
- rattachement aux rendez-vous, devis, factures et bons de livraison ;
- API de consultation, création et mise à jour.

Un sous-module de diagnostic existe et permet d’enregistrer des informations détaillées liées au client.

### Perspective

- historique chronologique complet du client ;
- segmentation et scoring ;
- import/export ;
- relances automatiques ;
- détection des doublons ;
- consentements et gestion RGPD/CNDP ;
- portail client.

### Limites

- le diagnostic existant contient des champs très spécialisés liés aux traitements laser ; il constitue un héritage métier qui n’est pas générique pour toutes les entreprises ;
- l’API client ne couvre pas encore toutes les opérations ;
- l’historique commercial n’est pas encore présenté comme une timeline unifiée.

---

## 5.7 Rendez-vous, visites et rappels

### Fonctions

- calendrier de rendez-vous ;
- création, modification et désactivation ;
- association à un client ;
- association à des services et packs ;
- statuts et couleurs ;
- visites du jour et prochains rendez-vous sur le Home ;
- rappels personnels ;
- API pour consulter et gérer les rendez-vous.

### Perspective

- synchronisation Google Calendar et Outlook ;
- rappels e-mail, SMS ou WhatsApp ;
- gestion de ressources et salles ;
- confirmation par le client ;
- récurrence ;
- compte rendu de visite ;
- géolocalisation réelle des visites.

### Limites

- pas de synchronisation avec des calendriers externes ;
- pas de moteur de récurrence ;
- pas d’envoi SMS/WhatsApp natif ;
- la disponibilité des collaborateurs n’est pas calculée.

---

## 5.8 Catalogue de services et packs

### Fonctions

- gestion des prestations/services ;
- prix, durée, description et état actif ;
- création de packs ;
- rattachement de plusieurs services à un pack ;
- utilisation dans les rendez-vous et documents commerciaux.

### Perspective

- variantes et options ;
- tarifs par entreprise ou par catégorie client ;
- promotions ;
- historique des prix ;
- catalogue public.

### Limites

- pas de versionnement tarifaire ;
- pas de gestion avancée des unités, taxes ou grilles de prix ;
- pas de publication e-commerce.

---

## 5.9 Devis

### Fonctions

- création manuelle ;
- sélection du client ;
- lignes libres, prestations ou packs ;
- quantités, unités, prix et remises ;
- remise globale et TVA ;
- états brouillon, envoyé, accepté et refusé ;
- modification, duplication, désactivation et réactivation ;
- actions en lot ;
- génération PDF ;
- impression groupée ;
- conversion d’un devis en facture ;
- capture de l’identité visuelle au moment de la création ;
- API de consultation et téléchargement PDF.

### Perspective

- signature électronique ;
- validation en ligne par le client ;
- suivi des versions ;
- relances automatiques avant expiration ;
- marge et coût prévisionnel ;
- modèles de devis.

### Limites

- pas de signature électronique ;
- pas de portail client ;
- pas de versioning complet ;
- pas de calcul natif de marge ou coût de revient.

---

## 5.10 Factures

### Fonctions

- création directe ou conversion depuis un devis ;
- lignes, remises et TVA ;
- états brouillon, émise, envoyée, partiellement payée, payée, annulée et avoir ;
- modification, duplication, désactivation et réactivation ;
- actions en lot ;
- génération PDF et impression groupée ;
- changement de statut ;
- association aux mouvements comptables ;
- tableaux de bord et alertes d’échéance ;
- API de consultation et téléchargement PDF.

### Perspective

- échéanciers de paiement ;
- rapprochement automatique des règlements ;
- avoirs complets et partiels ;
- facturation récurrente ;
- numérotation configurable ;
- export comptable ;
- conformité fiscale et facture électronique.

### Limites

- la comptabilisation reste simplifiée ;
- un paiement client lié à une facture peut actuellement la faire passer à l’état payé sans moteur complet de rapprochement cumulatif ;
- pas de gestion avancée des acomptes ;
- pas de facture électronique réglementaire ;
- pas de verrouillage comptable après clôture.

---

## 5.11 Bons de livraison

### Fonctions

- création pour un client ;
- lignes de livraison ;
- lieu, commande, référence client et contact ;
- dates de réception et livraison ;
- états brouillon, validé et annulé ;
- duplication ;
- désactivation et réactivation ;
- aperçu en direct ;
- génération PDF ;
- capture de l’identité visuelle.

### Perspective

- génération depuis un devis, une facture ou une commande ;
- signature du destinataire ;
- preuve de livraison ;
- gestion des livraisons partielles ;
- impact automatique sur le stock.

### Limites

- pas de workflow logistique complet ;
- pas de signature électronique ;
- pas d’impact automatique généralisé sur le stock ;
- pas de gestion native des reliquats.

---

## 5.12 Bons de commande

### Fonctions

- création d’un bon de commande fournisseur ;
- lignes de commande ;
- lieu et date de livraison souhaitée ;
- références internes et fournisseur ;
- contact fournisseur ;
- états brouillon, validé et annulé ;
- duplication ;
- désactivation et réactivation ;
- aperçu en direct ;
- génération PDF ;
- capture de l’identité visuelle.

### Perspective

- demandes d’achat et circuit d’approbation ;
- réception partielle ;
- rapprochement commande/réception/facture ;
- mise à jour automatique du stock ;
- suivi des engagements.

### Limites

- pas de circuit d’approbation multiniveau ;
- pas de rapprochement « three-way matching » ;
- pas de gestion complète des réceptions et reliquats ;
- intégration stock/fournisseur encore partielle.

---

## 5.13 Fournisseurs

### Fonctions

- création, modification et désactivation ;
- informations d’entreprise et de contact ;
- utilisation dans les bons de commande ;
- association à des mouvements comptables ;
- API de consultation.

### État d’exposition

Les routes et vues existent, mais le module a été retiré du menu principal dans la configuration actuelle. Il reste accessible si l’utilisateur connaît la route et possède les permissions nécessaires.

### Perspective

- évaluation fournisseur ;
- historique d’achats ;
- conditions de paiement ;
- pièces administratives ;
- comparaison des prix ;
- portail fournisseur.

### Limites

- pas de scoring ni d’évaluation ;
- pas de gestion documentaire fournisseur ;
- pas de workflow d’achat complet ;
- visibilité réduite car le menu est actuellement masqué.

---

## 5.14 Stock

### Fonctions

- création et modification d’articles ;
- référence, quantité et seuil d’alerte ;
- mouvements d’entrée et de sortie ;
- désactivation ;
- liste des articles sous seuil ;
- alertes de stock ;
- API de consultation des articles et mouvements.

### État d’exposition

Le module est implémenté mais n’est pas actuellement affiché dans le menu principal.

### Perspective

- entrepôts et emplacements ;
- inventaires ;
- valorisation FIFO/CUMP ;
- réservations ;
- lots et numéros de série ;
- génération automatique depuis achats et livraisons ;
- lecteurs code-barres.

### Limites

- pas de multi-entrepôt ;
- pas de valorisation comptable ;
- pas de réservation de stock ;
- intégration partielle avec bons de commande et livraison ;
- visibilité réduite dans l’interface actuelle.

---

## 5.15 Comptabilité

### Fonctions

- tableau de bord comptable ;
- mouvements d’entrée, dépense, paiement client, paiement fournisseur, crédit et salaire ;
- modes de paiement ;
- références externes ;
- association à un client, fournisseur, facture ou employé ;
- journal des mouvements ;
- statistiques dédiées au profil Comptable ;
- suivi des factures impayées.

### État d’exposition

Le module existe et le Comptable y accède par les raccourcis de son tableau de bord, mais l’entrée historique du menu commercial a été masquée.

### Perspective

- plan comptable ;
- écritures en partie double ;
- journaux, grand livre et balance ;
- rapprochement bancaire ;
- clôtures et exercices ;
- déclarations TVA ;
- exports vers logiciels comptables ;
- budgets et trésorerie prévisionnelle.

### Limites

- il s’agit d’une comptabilité de gestion simplifiée, pas d’un logiciel comptable certifié ;
- absence de partie double ;
- absence de comptes comptables et journaux réglementaires ;
- absence de rapprochement bancaire ;
- absence de clôture et de verrouillage d’exercice ;
- les montants dépendent des saisies manuelles et statuts de facture.

---

## 5.16 Marchés et cautions

### Fonctions

- création et suivi des marchés ;
- référence interne automatique ;
- statuts du marché ;
- maître d’ouvrage, montant, dates et notes ;
- lots ;
- documents joints ;
- cautions provisoires, définitives, retenues de garantie et autres ;
- dates d’échéance et alertes ;
- décision d’offre acceptée ou refusée ;
- date de départ en cas d’acceptation ;
- échéancier indicatif en quatre tranches de trois mois ;
- archivage ;
- import d’une consultation depuis `marchespublics.gov.ma` ;
- API de consultation.

L’import récupère notamment :

- référence de consultation ;
- objet ;
- acheteur public ;
- procédure ;
- catégorie ;
- allotissement ;
- date limite ;
- lieux et adresses ;
- informations de contact.

### Perspective

- checklist administrative ;
- suivi des pièces exigées ;
- workflow de validation interne ;
- calcul des marges et coûts ;
- alertes de dépôt ;
- suivi d’exécution réel ;
- renouvellement et mainlevée des cautions ;
- synchronisation officielle avec une API du portail si elle devient disponible.

### Limites

- l’import dépend de la structure HTML du portail public et peut cesser de fonctionner si cette structure change ;
- le portail peut refuser ou limiter les requêtes ;
- l’échéancier en quatre tranches est une règle interne fixe, pas un échéancier contractuel universel ;
- pas de workflow complet de préparation et dépôt d’offre ;
- pas de calcul automatique de rentabilité.

---

## 5.17 Analyse des appels d’offres

### Fonctions

- saisie d’une URL ;
- import hybride des données d’une consultation lorsque l’URL provient du portail marocain ;
- génération de lots, offres, classements, prix de référence, moyennes et écarts ;
- récapitulatif des gagnants ;
- conservation temporaire du résultat dans la session.

### Nature actuelle du module

Ce module est une **démonstration/simulation**. Les montants des concurrents, classements et entreprises sont générés de manière pseudo-aléatoire déterministe. Même lorsqu’une consultation réelle est importée, l’analyse concurrentielle reste simulée.

Il ne faut donc pas utiliser ces résultats comme source officielle pour une décision commerciale, juridique ou financière.

### Perspective

- importer des résultats d’ouverture réels ;
- construire une base historique des attributaires ;
- analyser les prix par catégorie et région ;
- produire des recommandations explicables ;
- calculer la probabilité de succès ;
- intégrer une source de données autorisée et juridiquement exploitable.

### Limites

- données concurrentielles simulées ;
- aucune intelligence artificielle prédictive réelle ;
- aucune garantie sur l’exhaustivité ;
- dépendance à l’import HTML ;
- nécessité de vérifier les conditions d’utilisation et la réglementation relative aux données publiques.

---

## 5.18 Employés et congés

### Fonctions

- création et modification des employés ;
- fiche détaillée ;
- activation/désactivation ;
- matricule et poste ;
- saisie de congés ;
- changement de statut des demandes ;
- API de consultation.

### État d’exposition

Le module est implémenté mais n’est pas affiché dans le menu principal actuel.

### Perspective

- pointage ;
- contrats et documents RH ;
- compteurs de congés ;
- validation multiniveau ;
- paie ;
- évaluations ;
- notes de frais.

### Limites

- pas de paie ;
- pas de pointage ;
- pas de calcul avancé des droits à congé ;
- pas de coffre-fort documentaire RH ;
- visibilité réduite dans l’interface actuelle.

---

## 5.19 Suivi des déplacements

### Fonctions

- carte Leaflet ;
- affichage de collaborateurs ;
- position, véhicule, mission, vitesse, distance et estimation d’arrivée ;
- endpoint API de snapshot.

### Nature actuelle du module

Les coordonnées, missions, véhicules et indicateurs sont actuellement des données de démonstration. Le module prend éventuellement les deux premiers employés actifs pour les noms, mais ne reçoit pas de positions GPS réelles.

### Perspective

- application mobile de remontée GPS ;
- consentement et règles de confidentialité ;
- historique des trajets ;
- zones géographiques et alertes ;
- missions réelles ;
- consommation et frais de déplacement.

### Limites

- pas de tracking GPS réel ;
- positions et véhicules simulés ;
- dépendance à des tuiles cartographiques et scripts externes ;
- enjeux importants de consentement, sécurité et protection des données ;
- module masqué du menu principal.

---

## 5.20 Alertes et notifications

### Fonctions

- alertes d’échéance de cautions ;
- alertes de factures impayées ;
- alertes de stock sous seuil ;
- rappels personnels ;
- notifications stockées en base ;
- liste des notifications ;
- marquage comme lu ;
- interrogation périodique des notifications non lues ;
- réglage du nombre de jours avant alerte.

### Perspective

- e-mail, SMS et WhatsApp ;
- notifications temps réel WebSocket ;
- préférences individuelles ;
- escalade automatique ;
- résumé quotidien ou hebdomadaire.

### Limites

- pas de diffusion temps réel native ;
- pas de canaux SMS/WhatsApp intégrés ;
- règles d’alertes encore limitées ;
- la page Alertes n’est pas exposée directement dans le menu actuel, contrairement aux Paramètres.

---

## 5.21 Paramètres et identité visuelle

### Fonctions

- logo du menu ;
- logo des documents ;
- couleurs d’accent et de texte ;
- nom commercial ;
- adresse ;
- téléphone ;
- e-mail ;
- mention TVA ;
- paramètres d’alertes ;
- sélection de l’entreprise à configurer par le Super Admin.

Les documents conservent un instantané de l’identité visuelle afin qu’un changement futur de logo ou de coordonnées ne modifie pas automatiquement les anciens documents.

### Perspective

- modèles de documents ;
- choix de police ;
- mentions légales par type de document ;
- signatures et cachets ;
- thèmes d’interface par entreprise.

### Limites

- options graphiques encore limitées ;
- stockage local des logos, nécessitant un disque persistant en hébergement ;
- pas d’éditeur visuel avancé de document.

---

## 5.22 API REST

### Fonctions

L’API est versionnée sous `/api/v1` et utilise Sanctum.

Elle couvre :

- connexion, utilisateur courant et déconnexion ;
- clients ;
- rendez-vous ;
- devis et factures ;
- PDF de devis et factures ;
- fournisseurs ;
- stock ;
- marchés ;
- employés ;
- snapshot de tracking.

Les routes sont limitées par :

- token Sanctum ;
- throttling ;
- permissions module/action ;
- contexte multi-tenant.

### Perspective

- documentation OpenAPI/Swagger ;
- pagination et filtres normalisés ;
- webhooks ;
- versioning contractuel ;
- API complète d’écriture pour tous les modules ;
- SDK mobile.

### Limites

- couverture fonctionnelle incomplète ;
- absence de documentation OpenAPI ;
- formats d’erreurs à uniformiser ;
- absence de tests contractuels complets ;
- certaines ressources sont uniquement consultables.

---

## 5.23 Journaux, audit et maintenance

### Fonctions

- journaux techniques Laravel ;
- journaux dédiés à certaines opérations métier ;
- page de consultation de logs ;
- middleware de journalisation d’accès ;
- commande `php artisan app:reset` permettant de supprimer les données métier tout en conservant les comptes Super Admin ;
- nettoyage des fichiers téléversés lors du reset.

### Perspective

- journal d’audit métier immuable ;
- identification systématique des anciennes et nouvelles valeurs ;
- export des audits ;
- supervision centralisée ;
- métriques et alertes techniques.

### Limites

- les logs ne constituent pas encore une piste d’audit réglementaire complète ;
- la rétention et la rotation doivent être configurées en production ;
- la commande de reset est destructive et doit être strictement protégée.

---

## 6. Parcours métier principaux

### 6.1 Parcours commercial

1. Création ou sélection d’un client.
2. Création d’un rendez-vous ou d’une visite.
3. Sélection de prestations ou packs.
4. Création du devis.
5. Envoi et changement de statut du devis.
6. Acceptation du devis.
7. Conversion en facture.
8. Génération du PDF.
9. Enregistrement du paiement.
10. Suivi comptable et relance des échéances.

### 6.2 Parcours de vente et livraison

1. Création du client.
2. Établissement du devis ou de la facture.
3. Création du bon de livraison.
4. Validation et génération PDF.
5. Livraison et suivi manuel du statut.

### 6.3 Parcours achat

1. Création du fournisseur.
2. Création du bon de commande.
3. Validation et envoi du PDF.
4. Saisie manuelle de la réception ou du mouvement de stock.
5. Saisie du paiement fournisseur en comptabilité.

Ce parcours reste partiellement intégré : la réception, le stock et le paiement ne sont pas encore liés par un workflow automatique complet.

### 6.4 Parcours marché public

1. Import d’une consultation ou création manuelle.
2. Vérification des données essentielles.
3. Ajout des lots.
4. Ajout des cautions.
5. Ajout des documents.
6. Suivi de la date limite.
7. Décision sur l’offre.
8. Suivi indicatif de l’exécution en cas d’acceptation.

---

## 7. Données et stockage

### 7.1 Base de données

Les principales entités sont :

- entreprises ;
- utilisateurs ;
- profils ;
- permissions ;
- clients ;
- diagnostics ;
- services et packs ;
- rendez-vous ;
- devis et lignes ;
- factures et lignes ;
- bons de livraison et lignes ;
- bons de commande et lignes ;
- fournisseurs ;
- articles et mouvements de stock ;
- marchés, lots, documents et cautions ;
- employés et congés ;
- mouvements comptables ;
- paramètres ;
- notifications ;
- rappels personnels.

### 7.2 Fichiers

Les logos et documents sont stockés sur le disque public Laravel. En production, il faut :

- créer le lien `public/storage` ;
- garantir les droits d’écriture ;
- utiliser un volume persistant ;
- sauvegarder régulièrement les fichiers ;
- envisager un stockage objet compatible S3.

### 7.3 Protection des données

L’application traite des données personnelles :

- identité et contact des utilisateurs ;
- identité et contact des clients ;
- données d’employés ;
- historiques de rendez-vous ;
- informations potentiellement sensibles dans les diagnostics ;
- données de géolocalisation dans une future version du tracking.

Une mise en production doit donc inclure :

- politique de confidentialité ;
- gestion des finalités et durées de conservation ;
- habilitations strictes ;
- sauvegardes chiffrées ;
- procédure d’exercice des droits ;
- conformité à la loi marocaine 09-08 et exigences CNDP applicables.

---

## 8. Déploiement et exploitation

### 8.1 Prérequis

- PHP 8.2 ou supérieur ;
- Composer ;
- MySQL/MariaDB ;
- extensions PHP requises par Laravel et DomPDF ;
- Node.js/NPM pour compiler les assets ;
- serveur web pointant vers `public/`.

### 8.2 Étapes essentielles

1. Installer les dépendances Composer.
2. Compiler les assets frontend.
3. créer le fichier `.env`.
4. définir `APP_KEY`.
5. configurer la base de données.
6. exécuter les migrations et seeders nécessaires.
7. créer le lien de stockage.
8. donner les droits à `storage` et `bootstrap/cache`.
9. mettre en cache la configuration et les routes.
10. configurer HTTPS, sauvegardes et tâches planifiées.

### 8.3 Points d’attention pour Railway ou un PaaS

- définir obligatoirement `APP_KEY` ;
- fournir toutes les variables de base de données ;
- définir `APP_URL` avec le domaine public ;
- utiliser un volume persistant pour les fichiers téléversés ;
- lancer les migrations lors du déploiement ;
- ne jamais versionner `.env` ;
- désactiver `APP_DEBUG` en production.

---

## 9. Limites transversales actuelles

### 9.1 Couverture de tests

Les tests présents concernent surtout l’authentification et le profil utilisateur. Les modules métier, le multi-tenant et les permissions ne disposent pas encore d’une couverture automatisée suffisante.

Il manque notamment :

- tests d’isolation entre entreprises ;
- tests de permissions par rôle ;
- tests des documents et calculs ;
- tests de l’API métier ;
- tests des imports ;
- tests de bout en bout.

### 9.2 Cohérence des contrôles d’accès

Le projet combine :

- scopes globaux ;
- contrôles dans les contrôleurs ;
- conditions Blade ;
- middleware ;
- permissions par profil.

Cette défense en profondeur est utile, mais augmente le risque d’incohérence. Une politique centralisée avec Policies et Gates Laravel serait plus facile à tester et maintenir.

### 9.3 Risques multi-tenant à corriger

Le multi-tenant protège la majorité des modèles principaux, mais plusieurs cas doivent être renforcés avant une utilisation SaaS avec des entreprises indépendantes :

- les tables enfants `marche_lots` et `marche_documents` ne possèdent pas leur propre tenant et certaines actions utilisent directement le route-model binding ;
- plusieurs validations `exists:clients,id`, `exists:factures,id` ou similaires ne vérifient pas explicitement que l’identifiant appartient à l’entreprise courante ;
- les utilisateurs ne disposent pas d’un scope tenant global et leur isolation dépend de contrôles manuels dans `UserController` ;
- le trait multi-tenant choisit la première entreprise disponible lorsqu’aucun utilisateur et aucun contexte ne sont présents, ce qui est risqué dans une commande ou un futur Job ;
- les pivots `user_profiles` et `profile_permissions` ne disposent pas partout de contraintes empêchant les doublons ;
- le Super Admin fonctionne volontairement sans scope global et chaque écran global doit donc être contrôlé avec attention.

Les références sont générées par entreprise, mais certaines contraintes d’unicité restent globales en base. Deux entreprises peuvent donc générer le même numéro métier tout en rencontrant une collision SQL. Cela concerne potentiellement :

- les numéros de devis ;
- les numéros de factures ;
- les bons de commande ;
- les bons de livraison ;
- les références d’articles ;
- les matricules d’employés ;
- les références de marchés.

La solution recommandée consiste à utiliser des contraintes uniques composites du type `id_entreprise + numero` et à tester systématiquement les associations entrantes dans le tenant courant.

### 9.4 Risques de sécurité identifiés

Les points suivants doivent être traités en priorité avant une production contenant des données sensibles :

1. **Consultation des logs :** l’endpoint de logs utilise un token avec une valeur par défaut et le middleware journalise le token reçu ainsi que le token attendu. Un secret ne doit jamais être écrit dans les journaux.
2. **Données sensibles journalisées :** le contrôleur de diagnostic écrit actuellement le contenu du formulaire médical dans les logs.
3. **Import d’URL :** le contrôle de l’hôte repose sur la présence de `marchespublics.gov.ma` dans le nom d’hôte. Il faut imposer un hôte exact, HTTPS, limiter les redirections et bloquer les IP privées afin de réduire le risque SSRF.
4. **Documents de marché :** ils sont stockés sur le disque public et accessibles par URL directe. Un stockage privé avec contrôleur de téléchargement autorisé est préférable.
5. **Fichiers SVG :** les logos acceptent SVG sur un disque public. Il faut supprimer ce format ou le nettoyer afin d’éviter du contenu actif indésirable.
6. **Types de documents :** les documents de marché ne disposent pas d’une liste de formats suffisamment restrictive ni d’un contrôle antivirus.
7. **Configuration d’exemple :** `.env.example` doit éviter toute clé d’application réutilisable, `APP_DEBUG=true` ou configuration MySQL trop permissive.
8. **API :** le login API n’a pas de throttling explicite, les jetons Sanctum n’expirent pas et aucune capacité spécifique n’est attribuée aux jetons.
9. **Entreprise désactivée :** la désactivation d’une entreprise ne bloque pas automatiquement tous ses utilisateurs actifs.

### 9.5 Routes et code résiduels

Certaines routes générées ou déclarées pointent vers des méthodes absentes :

- `Route::resource('entreprises')` expose notamment `create`, `show`, `edit` et `destroy`, absentes du contrôleur actuel ;
- `Route::resource('utilisateurs')` génère des routes vers `create`, `show`, `edit` et `destroy`, alors que l’interface principale fonctionne surtout avec l’index et des modales ;
- des routes de droits utilisateur référencent `storeDroits` et `getUserDroits`, absentes du contrôleur ;
- la route de mise à jour des profils existe sans méthode publique `update` correspondante.

L’inscription publique `/register` constitue également un reliquat : son contrôleur référence un modèle `Hopital` et un ancien schéma qui ne correspondent plus au projet actuel. Elle doit être supprimée ou complètement réécrite.

D’autres vestiges sont présents :

- modèle et e-mail liés à une ancienne activité médicale ;
- menu Participants commenté ;
- liens Activités/Staff ou Normes sans module complet ;
- vues historiques qui ne sont plus reliées au parcours principal ;
- identité partagée entre les noms TadbirPro et Prowise.

Enfin, les vues de postes sont rangées dans un dossier `Poste` avec une majuscule alors que certains appels utilisent `poste`. Cela peut fonctionner sur macOS mais échouer sur un serveur Linux sensible à la casse.

### 9.6 Authentification et notifications

- la vérification d’e-mail est routée, mais le modèle utilisateur n’impose pas actuellement `MustVerifyEmail` et les modules métier ne sont pas protégés par `verified` ;
- l’interface dite « Face ID » ouvre la caméra mais ne réalise pas une authentification biométrique fonctionnelle ;
- les notifications Laravel peuvent être listées et marquées comme lues, mais aucun producteur métier actif n’a été identifié ;
- les alertes sont calculées lors de l’ouverture des pages et ne sont pas envoyées proactivement ;
- aucune tâche planifiée ni aucun Job métier ne génère actuellement de relance ;
- l’adresse de notification e-mail secondaire est privilégiée sans repli systématique documenté vers l’e-mail principal.

### 9.7 Modules de démonstration

Les fonctions suivantes ne doivent pas être considérées comme des données réelles :

- analyse concurrentielle des appels d’offres ;
- géolocalisation et véhicules du tracking.

### 9.8 Intégrations partielles

Les enchaînements suivants ne sont pas totalement automatisés :

- bon de commande → réception → stock → facture fournisseur ;
- bon de livraison → sortie de stock ;
- facture → paiements partiels → solde exact ;
- marché accepté → suivi contractuel complet ;
- employé → congé → paie.

### 9.9 Exploitation

- pas de configuration Docker dédiée versionnée ;
- pas de pipeline CI/CD visible ;
- pas de supervision centralisée ;
- dépendance au stockage local pour les fichiers ;
- dépendances externes pour certaines cartes et bibliothèques frontend.

Le frontend combine Bootstrap, Tailwind, Alpine.js, jQuery et plusieurs bibliothèques chargées depuis des CDN. Cette coexistence augmente le poids, la complexité et les risques de conflit. Plusieurs scripts externes ne disposent pas d’intégrité SRI.

Les files d’attente et tables de cache existent, mais aucun Job métier significatif n’est actuellement utilisé. Le scheduler applicatif est pratiquement vide.

### 9.10 Seeders et données initiales

- plusieurs seeders utilisent `create` et ne sont pas idempotents ;
- une seconde exécution peut provoquer des doublons ou des violations d’unicité ;
- des mots de passe de comptes initiaux sont présents en clair dans les seeders ;
- les seeders de catalogue et de données de démonstration ne sont pas tous appelés par `DatabaseSeeder`.

Les mots de passe de démonstration doivent être remplacés au premier déploiement et ne doivent jamais être réutilisés en production.

### 9.11 Documentation

Le `README.md` actuel est encore le README standard Laravel et ne décrit pas TadbirPro. Le présent document constitue la première description fonctionnelle et technique complète, mais il devra être maintenu à chaque évolution.

---

## 10. Perspectives d’évolution

## 10.1 Court terme

Priorités recommandées :

1. Désactiver ou sécuriser l’endpoint de logs et purger les secrets/données sensibles déjà journalisés.
2. Fermer ou réécrire l’inscription publique.
3. Corriger les routes qui pointent vers des méthodes absentes.
4. Protéger les lots, documents et toutes les associations inter-tenant.
5. Remplacer les contraintes uniques globales par des contraintes composites tenant/référence.
6. Sécuriser l’import d’URL et les fichiers téléversés.
7. Ajouter des tests d’isolation multi-tenant et de permissions.
8. Corriger et fiabiliser le rapprochement des paiements partiels.
9. Documenter l’installation et le déploiement dans le README.
10. Ajouter OpenAPI pour l’API.
11. Rendre explicite dans l’interface le caractère simulé de l’analyse AO et du tracking.
12. Réactiver uniquement les modules réellement destinés aux clients dans le menu.
13. Ajouter sauvegardes automatiques et volume persistant.
14. Mettre en place une journalisation d’audit.

## 10.2 Moyen terme

- workflow d’achat complet ;
- workflow de livraison et stock ;
- échéanciers de paiement ;
- relances automatiques ;
- exports Excel et comptables ;
- tableaux de bord filtrables ;
- portail client ;
- signature électronique ;
- notifications multicanales ;
- application mobile utilisant l’API.

## 10.3 Long terme

- transformation en SaaS avec abonnements ;
- facturation électronique conforme ;
- comptabilité en partie double ;
- intégrations bancaires ;
- connecteurs CRM/ERP ;
- marketplace de modules ;
- analyse réelle des marchés publics à partir de données autorisées ;
- intelligence décisionnelle et prévisions ;
- tracking mobile réel conforme aux exigences de confidentialité.

---

## 11. Recommandations avant une mise en production

Avant d’utiliser TadbirPro avec des données réelles, il est recommandé de :

- exécuter une revue de sécurité ;
- ajouter des tests multi-tenant ;
- vérifier toutes les permissions ;
- désactiver les modules de démonstration non nécessaires ;
- clarifier les responsabilités de chaque rôle ;
- mettre en place sauvegardes et restauration ;
- chiffrer les communications avec HTTPS ;
- utiliser des secrets forts ;
- configurer la rotation des logs ;
- installer un volume persistant ;
- vérifier la conformité CNDP ;
- documenter la procédure d’incident ;
- tester la restauration d’une sauvegarde ;
- former les utilisateurs ;
- valider les calculs financiers avec un professionnel comptable.

---

## 12. Conclusion

TadbirPro dispose déjà d’une base fonctionnelle large : CRM, agenda, documents commerciaux, marchés, stock, fournisseurs, comptabilité simplifiée, ressources humaines, alertes, multi-tenant, permissions et API.

Ses points forts actuels sont :

- la centralisation des modules ;
- l’isolation par entreprise ;
- les rôles et permissions ;
- les documents PDF personnalisables ;
- le suivi des marchés et cautions ;
- le tableau de bord spécifique au Comptable ;
- la disponibilité d’une API ;
- une interface adaptée à la marque Prowise.

Ses principaux axes de consolidation sont :

- les tests ;
- la sécurité multi-tenant ;
- l’audit ;
- le rapprochement comptable ;
- l’intégration entre achats, stock et livraisons ;
- la distinction claire entre fonctions réelles et simulations ;
- l’industrialisation du déploiement.

Le projet peut servir de solution de gestion interne et de démonstrateur avancé. Pour devenir un produit SaaS mature ou un outil comptable réglementaire, il devra compléter ses workflows, renforcer ses tests et son audit, et formaliser ses exigences de conformité et d’exploitation.

