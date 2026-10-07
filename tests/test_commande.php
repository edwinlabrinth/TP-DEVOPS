<?php
// Tests de la classe Commande (lances par tests/run.php qui fournit verifier())

$clavier = new Produit('C001', 'Clavier', 150, 10);
$souris  = new Produit('C002', 'Souris', 50, 5);

// Commande neuve
$commande = new Commande(1);
verifier($commande->estValidee() === false, 'Une nouvelle commande n\'est pas validee');
verifier(abs($commande->total()) < 0.001, 'Le total d\'une commande vide vaut 0');

// ajouterLigne() et total()
$commande->ajouterLigne($clavier, 2);
$commande->ajouterLigne($souris, 3);
verifier(abs($commande->total() - 450) < 0.001, 'Total = 2 x 150 + 3 x 50 = 450');

$commande->ajouterLigne($clavier, 1);
verifier(abs($commande->total() - 600) < 0.001, 'Ajouter le meme produit cumule la quantite (total 600)');

// ajouterLigne() : quantite non positive
$exception = false;
try { $commande->ajouterLigne($souris, 0); } catch (InvalidArgumentException $e) { $exception = true; }
verifier($exception, 'ajouterLigne() refuse une quantite nulle');

$exception = false;
try { $commande->ajouterLigne($souris, -2); } catch (InvalidArgumentException $e) { $exception = true; }
verifier($exception, 'ajouterLigne() refuse une quantite negative');

// ajouterLigne() : quantite superieure au stock (3 souris deja commandees, 5 en stock)
$exception = false;
try { $commande->ajouterLigne($souris, 3); } catch (InvalidArgumentException $e) { $exception = true; }
verifier($exception, 'ajouterLigne() refuse une quantite qui depasse le stock');

// afficher()
$facture = $commande->afficher();
verifier(str_contains($facture, 'Clavier') && str_contains($facture, 'Souris'), 'La facture liste les produits');
verifier(str_contains($facture, '600.00'), 'La facture affiche le total');

// valider() retire les quantites du stock
$commande->valider();
verifier($commande->estValidee() === true, 'La commande est validee apres valider()');
verifier($clavier->getQuantite() === 7, 'Apres validation, il reste 7 claviers (10 - 3)');
verifier($souris->getQuantite() === 2, 'Apres validation, il reste 2 souris (5 - 3)');

// valider() : commande deja validee
$exception = false;
try { $commande->valider(); } catch (Exception $e) { $exception = true; }
verifier($exception, 'valider() refuse une commande deja validee');
verifier($clavier->getQuantite() === 7, 'Une double validation ne retire pas le stock deux fois');

// valider() : commande vide
$commandeVide = new Commande(2);
$exception = false;
try { $commandeVide->valider(); } catch (Exception $e) { $exception = true; }
verifier($exception, 'valider() refuse une commande vide');
verifier($commandeVide->estValidee() === false, 'Une commande vide reste non validee');
// getNumero() et statut dans la facture
verifier($commande->getNumero() === 1, 'getNumero() retourne le numero de la commande');
verifier(str_contains($commande->afficher(), 'Statut : validee'), 'La facture indique que la commande est validee');
