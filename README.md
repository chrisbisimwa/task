# Suivi Hebdomadaire des Tâches Employés

Ce projet Laravel permet d’envoyer automatiquement des rappels aux employés pour le suivi de leurs tâches hebdomadaires, via WhatsApp, email (et d’autres canaux à venir). Il propose :

- Génération de liens de suivi sécurisés et personnalisés
- Suivi des accès aux liens et relances automatisées
- Historique détaillé des notifications et des accès
- Système de feedback employé après saisie
- Interface d’administration avancée

## Fonctionnalités principales

- **Envoi automatique de notifications** (WhatsApp, email…) chaque semaine
- **Lien de suivi unique** pour chaque employé, valable 24h (ou selon configuration)
- **Historique complet** des notifications envoyées (succès/échecs, détail du canal…)
- **Tableaux de bord admin** pour visualiser, filtrer, exporter tous les historiques
- **Notifications personnalisées** selon le type de tâche ou le profil employé

## Installation

1. **Cloner le dépôt**
   ```bash
   git clone <url-du-repo>
   cd <nom-du-repo>
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   npm install && npm run dev
   ```

3. **Configurer l’environnement**
   - Copier `.env.example` en `.env`
   - Remplir les variables nécessaires (base de données, WhatsApp, email…)

4. **Lancer les migrations**
   ```bash
   php artisan migrate
   ```


6. **Configurer les notifications WhatsApp**
   - Obtenir un token WhatsApp Business API
   - Renseigner `WHATSAPP_TOKEN` et `WHATSAPP_PHONE_NUMBER_ID` dans `.env`

7. **(Optionnel) Installer Laravel Excel pour export**
   ```bash
   composer require maatwebsite/excel
   ```

## Utilisation

- **Envoi des liens de suivi :**
  ```bash
  php artisan send:task-follow-up-links
  ```
  (À planifier en cron chaque semaine)

- **Relance automatique des employés n’ayant pas accédé**
  ```bash
  php artisan tasks:remind-unreached-employees
  ```

- **Tableaux de bord admin**
  - Accéder à `/admin/notifications` pour l’historique des notifications
  - Accéder à `/admin/access-tokens` pour la gestion des accès
  - Accéder à `/admin/feedbacks` pour les retours des employés

## Structure des principales tables

- **employees** : informations collaborateurs
- **tasks** : tâches hebdomadaires
- **access_tokens** : liens sécurisés, suivi des accès
- **notification_logs** : historique détaillé des notifications
- **feedbacks** : retours/commentaires des employés

## Personnalisation

- **Canaux de notification** : WhatsApp, email, SMS, Slack (extensible)
- **Templates de messages personnalisables** (langue, format, contenu dynamique)
- **Gestion des rappels selon le type de tâche ou le profil employé**

## Sécurité

- Les liens de suivi sont valables temporairement et invalidés après usage ou expiration.
- Accès admin protégé par authentification et middleware.
- Toutes les actions sensibles sont tracées.

## Roadmap (suggestions d’évolution)

- Intégration d’autres canaux (SMS, Slack…)
- Système de relance configurable (fréquence, nombre maximum…)
- Statistiques avancées (taux d’accès, taux de complétion, satisfaction…)
- Support multi-langues complet
- API pour intégrations externes
- **Suivi des accès** : qui a consulté ou non son lien
- **Relances automatiques** pour les employés n’ayant pas accédé à leur lien
- **Système de feedback** : commentaire et note à la fin du suivi par l’employé

## Contribuer

Les PR et suggestions sont les bienvenues. Merci de soumettre vos idées dans les issues GitHub.

---

**Auteur** : Christian BISIMWA
**Licence** : MIT
