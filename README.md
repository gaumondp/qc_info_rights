# Info Rights

*La [version française](#documentation-info-rights) de la documentation suit le texte anglais*

### About
This extension add a new item to the Info Module in the BE with those features:

1. Shows BE rights for the pages (like in the Access Module) but for editors (as read-only)
2. Show a list of all BE Users (except if it begins with a "_" like "\_cli\_") with a filter and export as CSV(hidden by default)
3. Show a list of all BE User Groups with a filter and export to CSV (hidden by default),with an option that will return the list of members that are contained in these groups.

![Main functionnality](Documentation/Images/show-groups-members.jpg)

Main function is to show the actual BE right to non-admins:
![Main functionnality](Documentation/Images/access-and-rights.jpg)

BE users list with filters and CSV export button:
![BE users list with CSV button](Documentation/Images/backend-users-tab.jpg)


The extension has many options to hide columns or features.

We decided to hide the 2 tabs containing the lists of users and groups by default. See the TSconfig below to activate the tabs.

### Page TSconfig - default values

```
mod{
qcinforights{

    //This settings is to show or hide column under the tab access and right for owner,group, and everybody
    hideAccessRights{
      user = 0
      group = 0
      everybody = 0
    }

    //This settings is to show or hide tab (Access and right, Backend users group, and backend user lists)
    showTabAccess  = 1
    showTabUsers   = 0
    showTabGroups  = 0

    // This option is to display the members column in the ( Backend user groups list )
    showMembersColumn = 1

    //This settings is to show Exportation option for the backend users list or backend user groups list
    showExportUsers  = 1
    showExportGroups = 1

    //This settings is to show Administrator inside list of backend user
    showAdministratorUser = 1
}
```

### How to hide the module for a user or group

To hide Info Rights in the Info menu, insert this code in the TSconfig field:

```
page.mod.web_info.menu.function {
        #Hide Info Rights from the menu of the Info Module by setting it to zero (default is 1)
        Qc\QcInfoRights\Report\QcInfoRightsReport = 0
}
```

### Global options - CSV

With the Settings module, go to Extension configuration and edit those values:

Default values:

1. Quotes (basic.quote): " (quotation mark)
2. Delimiter (basic.delimiter): , (comma)

-----------
[Version française]
## Documentation Info Droits

### À propos
L'extension ajoute un nouvel item de menu au module Info en BE avec les fonctionnalités suivantes:

1. Affichage des droits BE des pages (comme dans le module Access) mais pour les édimestres (lecture seulement)
2. Affichage des Utilisateurs BE (sauf si le nom débute par "_" comme "\_cli\_) avec filtre et exportation en CSV (masqué par défaut)
3. Affichage des Groupes BE avec filtre et exportation en CSV (masqué par défaut), avec l'option qui permet d'afficher la liste des membres de groupes sélectionnés.

![Main functionnality](Documentation/Images/show-groups-members.jpg)

L'extension offre plusieurs options pour masquer des colonnes ou fonctionnalités.

Les onglets qui listent les utilisateurs et les groupes sont masqués par défaut. Voir le TSconfig qui suit pour activer ces onglets.

### Page TSconfig - valeurs par défaut

```
mod{
qcinforights{

    //Masquer ou afficher les colonnes (propriétaire, groupe, tout le monde) de l'onglet Accès et droits
    hideAccessRights{
      user = 0
      group = 0
      everybody = 0
    }

    //Masquer ou afficher chacun des onglet (Accès et droits, Liste utilisateurs BE et Liste des groupes BE)
    showTabAccess  = 1
    showTabUsers   = 0
    showTabGroups  = 0

    // Masquer ou afficher la colonne qui sert à afficher les membres de chaque groupe dans l'onglet ( Liste des groupes BE)
    showMembersColumn = 1

    //Masquer ou afficher les boutons d'exportation CSV. Ils sont visibles par défaut mais l'onglet qui les contient est masqué
    showExportUsers  = 1
    showExportGroups = 1

    //Masquer ou afficher les comptes BE des administrateurs. Visibles par défaut.
    showAdministratorUser = 1
}
```

### Masquer le module à un utilisateur ou un groupe

Pour masquer Info Droits par utilisateur ou par groupe, insérer le code TSconfig suivant:

```
page.mod.web_info.menu.function {
        #Désactiver Info Droits du menu du Module Info en indiquant zéro (1 par défaut)
        Qc\QcInfoRights\Report\QcInfoRightsReport = 0
}
```
### Options globales - CSV

Dans le module «Réglages», sous «Configure extensions» se trouvent 2 options relatives à l'exportation CSV.

Les valeurs par défaut sont:

1. Encadrement des valeurs (basic.quote): " (guillemet)
2. Délimiteur (basic.delimiter): , (virgule)
