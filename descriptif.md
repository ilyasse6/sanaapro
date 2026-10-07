# Descriptif du projet — Prowise / plateforme de gestion commerciale et opérationnelle

Ce document présente la **vision cible** du projet (alignée sur le cahier des charges fonctionnel « caution » et vos besoins métiers), l’**état actuel** de l’application Laravel existante, et les **améliorations et rectifications recommandées** pour la rendre pleinement fonctionnelle.

> **Note sur le PDF** : le fichier `Cahier des charges fonctionnel caution.pdf` n’a pas pu être lu textuellement dans l’environnement (contenu compressé). Les exigences liées aux **marchés** et **cautions** sont reprises à partir de votre synthèse et de la liste fonctionnelle fournie (création manuelle des marchés, lots, statuts, documents, cautions, tableau, alertes visuelles).

---

## 1. Contexte et objectifs

L’application vise des **sociétés du secteur privé ou public** qui gèrent :

- des **clients** et la relation commerciale ;
- des **devis** pouvant **se transformer en factures** ;
- des **catalogues** (produits / services) comme référentiel pour les calculs et documents ;
- la **comptabilité** (entrées, factures, dépenses, paiements, crédits) ;
- les **stocks** et les **fournisseurs** ;
- les **notifications** et **alertes** paramétrables ;
- les **employés** (RH, paie, congés) ;
- les **utilisateurs** et droits (déjà en place, à harmoniser avec le thème métier) ;
- le **suivi géographique** des déplacements (carte, trajet, kilométrage, budget carburant, consommation) ;
- la **gestion des marchés** et des **cautions** (appels d’offres, lots, documents, garanties), en cohérence avec le cahier des charges caution.

Le positionnement produit est celui d’une **ERP / suite de gestion** modulaire, avec une identité visuelle actuelle **Prowise** (bleu foncé / or).

---

## 2. Périmètre fonctionnel cible (par module)

### 2.1 Gestion des clients

- Fiche client B2B (raison sociale, contacts, ICE, RC, adresse, secteur, notes, etc.).
- Cycle de vie : actif / désactivé, historique des interactions utile à terme.

**État actuel** : module **partiellement aligné** (champs commerciaux, CRUD, permissions). Des résidus historiques (diagnostics, rendez-vous « visite ») peuvent coexister avec la vision purement commerciale : à clarifier ou retirer si non utilisés.

### 2.2 Gestion des devis → factures

- Création / édition de devis avec **lignes** issues du **catalogue** (produits ou services) ou saisie libre.
- **Simulation en ligne** des totaux (HT, remises, TVA) — déjà initiée côté devis.
- **Export PDF** du devis et, après transformation, de la **facture** (numérotation, statuts : brouillon, envoyé, accepté, facturé, payé…).
- Lien **devis ↔ facture** (une facture peut référencer un ou plusieurs devis selon règles métier).

**État actuel** : **devis** avec lignes, simulateur, permissions ; **pas de module facture** ni génération PDF dédiée ni workflow de conversion formalisé en base.

### 2.3 Gestion des stocks (catalogues — articles — produits)

- Référentiel **articles / produits** avec SKU, unités, prix d’achat / vente, seuils d’alerte.
- **Mouvements** de stock (entrées, sorties, inventaire, lien fournisseur).
- Cohérence avec le **catalogue** actuel (prestations / packs) : soit **fusion** du modèle (produit = ligne catalogue), soit **séparation** claire catalogue « commercial » vs stock « logistique ».

**État actuel** : **catalogue** (services + packs) sans **quantités physiques** ni mouvements de stock.

### 2.4 Comptabilité

- **Écritures / entrées** : recettes, charges.
- **Factures** (vente) et **dépenses** (achat / notes).
- **Paiements** et **crédits** (avoirs, reliquats, échéanciers).

**État actuel** : **non implémenté**. Nécessite un modèle comptable minimal (plan comptable simplifié ou journaux) et des rapports (balance, grand livre simplifié) selon ambition.

### 2.5 Gestion des fournisseurs

- Fiches fournisseurs, conditions, liens vers **articles** et **bons de commande** / réceptions.
- Impact sur **stock** et **comptabilité** (factures fournisseur).

**État actuel** : **non implémenté**.

### 2.6 Notifications et alertes

- **Paramétrage** des délais (ex. : alerte J−30 / J−7 avant échéance caution, congé, facture impayée).
- **Liste centralisée** des alertes (lu / non lu, type, lien vers l’entité).

**État actuel** : présence possible de **notifications** héritées du projet ; pas de module unifié « paramètres de durée + centre d’alertes » documenté pour l’ensemble des domaines (marchés, cautions, RH, compta).

### 2.7 Gestion des employés

- Informations RH, **paiement** (fiches, variables), **congés** (demandes, soldes, validation).

**État actuel** : les **utilisateurs** existent ; un vrai module **employé** (distinct du compte applicatif) avec paie et congés est **à créer** ou à mapper sur des tables existantes si vous les introduisez.

### 2.8 Gestion des utilisateurs

- Rôles, profils, postes, permissions (déjà en place).
- **Adapter** libellés et écrans au thème **entreprise / marchés** plutôt qu’héritage « établissement / spécialité » médical si ces notions ne servent plus.

**État actuel** : **solide** ; à **nettoyer** (modules et menus inutiles) et à **réaligner** avec la terminologie cible.

### 2.9 Tracking carte (déplacements employés)

- Carte avec **trajet**, **kilométrage**, **budget carburant**, **taux de consommation**.
- Nécessite **consentement**, **RGPD**, et choix technique (API cartographique, app mobile ou saisie web).

**État actuel** : **non implémenté**.

### 2.10 Gestion des marchés (cahier des charges — synthèse image / PDF)

Fonctions attendues :

| Fonction | Description |
|----------|-------------|
| Création manuelle des marchés | Saisie et suivi des marchés / contrats publics ou privés. |
| Création des lots | Découpage du marché en lots (périmètre, montants, statuts par lot si besoin). |
| Statuts du marché | Workflow (ex. : en cours, attribué, exécution, clôturé…). |
| Bloc documents | Pièces jointes et versioning simple (CCTP, offre, avenants…). |
| Ajout manuel des cautions | Saisie des garanties (cautionnement, retenue de garantie, etc.). |
| Tableau des cautions | Vue liste / tableau filtrable (montants, dates, banques, statuts). |
| Alertes visuelles de base | Badges, couleurs, rappels avant échéances (restitution caution, renouvellement…). |

**État actuel** : **non implémenté** ; c’est le cœur du PDF « caution » à développer en priorité si le métier est marchés publics / appels d’offres.

---

## 3. État récapitulatif de l’existant (code)

| Domaine | Statut |
|---------|--------|
| Authentification, utilisateurs, permissions, modules | En place |
| Clients (B2B) | En place, à consolider |
| Rendez-vous / visites | En place (héritage), à décider : garder ou renommer / retirer |
| Catalogues (prestations / packs) | En place |
| Devis + lignes + simulateur | En place |
| Factures, comptabilité | Absent |
| Stocks, fournisseurs | Absent |
| Marchés, lots, cautions, documents | Absent |
| Notifications paramétrables + centre d’alertes | Partiel / à définir |
| Employés, paie, congés | Absent |
| Tracking carte / carburant | Absent |

---

## 4. Améliorations et rectifications recommandées (pour un produit fonctionnel)

### 4.1 Cohérence métier et données

1. **Modèle de données unique** pour « offre commerciale » : décider si **catalogue** = **produit stock** ou si l’on duplique (référence catalogue → article stock).
2. Introduire des **statuts** et **numérotations** officielles pour devis / factures (conformité légale selon pays — au Maroc, règles de facturation à valider avec un expert).
3. Pour les **marchés** : tables `marches`, `marche_lots`, `marche_documents`, `cautions` avec dates d’échéance et flags d’alerte.

### 4.2 Technique (Laravel)

1. **Migrations** incrémentales et **seeders** de permissions pour chaque nouveau module (comme pour Devis / Catalogues).
2. **PDF** : intégrer une librairie éprouvée (ex. DomPDF, Snappy/wkhtmltopdf, ou API externe) avec **templates** Blade dédiés devis / facture.
3. **Files** : stockage des documents de marché via `storage` + contrôle d’accès par permission.
4. **Queues** pour notifications / rappels (échéances cautions, factures) si volume important.
5. **Tests** automatisés sur les flux critiques (conversion devis → facture, calcul TVA, désactivation client).

### 4.3 Expérience utilisateur et thème

1. **Menu** : regrouper par domaines (Commercial, Opérations, Finance, RH, Marchés).
2. **Dashboard** : widgets par module (échéances cautions, stock bas, factures impayées).
3. Unifier le **thème Prowise** sur tous les écrans encore hérités de l’ancien métier.

### 4.4 Juridique et exploitation

1. **RGPD** pour le tracking géographique des employés (finalité, durée de conservation, opt-in).
2. **Sauvegardes** BDD et fichiers avant mise en production des modules financiers.
3. **Journal d’audit** sur actions sensibles (suppression, changement de montants, statuts de facture).

### 4.5 Feuille de route suggérée (ordre logique)

1. **Marchés + cautions + documents + alertes** (alignement direct avec le PDF et valeur métier forte).
2. **Facturation** (PDF + statuts + lien devis) et socle **comptabilité** minimal.
3. **Fournisseurs + stock** (mouvements et lien catalogue).
4. **Employés / congés / paie** (ou intégration export vers outil paie).
5. **Notifications paramétrables** transverses.
6. **Tracking carte** (projet distinct ou phase 2, forte dépendance mobile / API).

---

## 5. Conclusion

Le projet actuel constitue une **base Laravel solide** (utilisateurs, permissions, clients, catalogues, devis). Pour répondre au **cahier des charges caution** et à votre vision **ERP multi-secteurs**, il faut **étendre massivement** le domaine **marchés / cautions**, ajouter la **chaîne de facturation et comptable**, la **logistique** (stock / fournisseurs), le **RH**, le **paramétrage d’alertes**, et le **suivi des déplacements**. Ce document sert de **référence** pour prioriser les développements et les arbitrages produit (notamment fusion catalogue / stock et périmètre exact de la comptabilité).

---

*Document généré pour le dépôt « company » — à faire évoluer au fil des sprints et des retours métier.*
