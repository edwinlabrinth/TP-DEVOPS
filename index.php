<?php
require __DIR__ . '/autoload.php';

function lire(string $question): string
{
    echo $question;
    $ligne = fgets(STDIN);
    if ($ligne === false) {
        echo "\nAu revoir !\n";
        exit(0);
    }
    return trim($ligne);
}

function lireEntier(string $question): int
{
    $valeur = filter_var(lire($question), FILTER_VALIDATE_INT);
    if ($valeur === false) {
        throw new InvalidArgumentException("Veuillez saisir un nombre entier.");
    }
    return $valeur;
}

function lireReel(string $question): float
{
    $valeur = filter_var(str_replace(',', '.', lire($question)), FILTER_VALIDATE_FLOAT);
    if ($valeur === false) {
        throw new InvalidArgumentException("Veuillez saisir un nombre.");
    }
    return $valeur;
}

function afficherProduits(array $produits): void
{
    if (empty($produits)) {
        echo "  (aucun produit)\n";
        return;
    }
    foreach ($produits as $p) {
        printf(
            "  %-8s %-20s %8.2f DH  x %4d  = %10.2f DH\n",
            $p->getReference(),
            $p->getNom(),
            $p->getPrix(),
            $p->getQuantite(),
            $p->valeurStock()
        );
    }
}

$stock = new Stock();
$numeroCommande = 0;

while (true) {
    echo "\n===== GESTION DE STOCK =====\n";
    echo "1. Ajouter un produit\n";
    echo "2. Lister le stock\n";
    echo "3. Reapprovisionner\n";
    echo "4. Nouvelle commande\n";
    echo "5. Produits en rupture ou sous seuil\n";
    echo "0. Quitter\n";
    $choix = lire("Votre choix : ");

    try {
        switch ($choix) {
            case '1':
                $reference = lire("Reference : ");
                $nom = lire("Nom : ");
                $prix = lireReel("Prix : ");
                $quantite = lireEntier("Quantite initiale : ");
                $stock->ajouter(new Produit($reference, $nom, $prix, $quantite));
                echo "Produit $reference ajoute.\n";
                break;

            case '2':
                echo "\n--- Stock ({$stock->compter()} produit(s)) ---\n";
                afficherProduits($stock->tous());
                printf("Valeur totale du stock : %.2f DH\n", $stock->valeurTotale());
                break;

            case '3':
                $produit = $stock->trouver(lire("Reference du produit : "));
                if ($produit === null) {
                    echo "Produit introuvable.\n";
                    break;
                }
                $produit->ajouterQuantite(lireEntier("Quantite a ajouter : "));
                echo "Nouveau stock de {$produit->getNom()} : {$produit->getQuantite()}\n";
                break;

            case '4':
                $commande = new Commande(++$numeroCommande);
                echo "Commande n°$numeroCommande (laisser la reference vide pour terminer)\n";
                while (($reference = lire("Reference du produit : ")) !== '') {
                    $produit = $stock->trouver($reference);
                    if ($produit === null) {
                        echo "Produit introuvable.\n";
                        continue;
                    }
                    try {
                        $commande->ajouterLigne($produit, lireEntier("Quantite : "));
                        echo "Ligne ajoutee.\n";
                    } catch (InvalidArgumentException $e) {
                        echo "Erreur : {$e->getMessage()}\n";
                    }
                }
                echo "\n" . $commande->afficher();
                if (strtolower(lire("Valider la commande ? (o/n) : ")) === 'o') {
                    $commande->valider();
                    echo "Commande validee, stock mis a jour.\n";
                } else {
                    echo "Commande annulee.\n";
                }
                break;

            case '5':
                echo "\n--- Produits en rupture ---\n";
                afficherProduits($stock->produitsEnRupture());
                $seuil = lireEntier("Seuil : ");
                echo "\n--- Produits sous le seuil de $seuil ---\n";
                afficherProduits($stock->produitsSousSeuil($seuil));
                break;

            case '0':
                echo "Au revoir !\n";
                exit(0);

            default:
                echo "Choix invalide.\n";
        }
    } catch (Exception $e) {
        echo "Erreur : {$e->getMessage()}\n";
    }
}
