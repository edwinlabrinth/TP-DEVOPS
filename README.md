# Gestion de Stock - Groupe 2

**Description**
Ce projet est une application PHP en ligne de commande conçue pour gérer le stock d'un magasin[cite: 1]. L'application permet de gérer les produits, les quantités, la valeur du stock et les commandes en appliquant les concepts de la Programmation Orientée Objet (POO) en PHP[cite: 1].

**Contrat d'interface**
Le développement est strictement réparti selon les rôles suivants pour permettre une intégration fluide :
* **Partie A** : Création des fichiers `src/Produit.php` et `tests/test_produit.php`[cite: 1]. Implémentation de la classe `Produit` qui gère les propriétés privées (référence, nom, prix, quantité) et inclut les méthodes d'ajout ou de retrait de quantité ainsi que le calcul de la valeur du stock[cite: 1].
* **Partie B** : Création des fichiers `src/Stock.php` et `tests/test_stock.php`[cite: 1]. Implémentation de la classe `Stock` qui maintient un tableau privé de produits, avec des méthodes pour ajouter, chercher, filtrer les produits (en rupture ou sous un seuil) et calculer la valeur totale du stock[cite: 1].
* **Partie C** : Création des fichiers `src/Commande.php`, `tests/test_commande.php` et `index.php`[cite: 2]. Implémentation de la classe `Commande` pour gérer les lignes d'achat et la validation, accompagnée du fichier `index.php` générant le menu interactif reliant les trois classes[cite: 2].

## Équipe
LACHHAB Mohammed - A
HAYOUN Ferdaous - B
SLIMANI Aimad Eddine - C