// Externalisation de nos script Js

// Fonction pour ajouter des champs
$('#add-image').click(function () {

    // Je récupère le numéro des futur champs que je vais créer
    // On remplace la ligne qui permet de récuprer le numéro des champs par l'input qui génère des #id
    // const index = $('#ad_images div.form-group').length;
    // .val() pour avoir la valeur générer - par défault = 0
    // '+' devant le $() pour indiquer que la valeur récuprer sera un nombre et non une string pour additionner les id et non les concaténés
    const index = +$('#widgets-counter').val();

    console.log(index);

    // Je récupère le prototype des entrées
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);

    // J'injecte ce code au sein de la div
    $('#ad_images').append(tmpl);

    // J'inject l'id dans la div avec une valeur index + 1
    $('#widgets-counter').val(index + 1);

    // Je gère le boutton supprimer
    handleDeleteButtons();
});

// Fonction pour gérer les buttons de suppression
function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {

        // On définit l'attribue data-target du button supprimer
        // * 'this' represente l'evenement html ici le button
        // * 'dataset' represente les attribues de this donc du button
        // * 'target' car on cible l'attribue 'data-target'
        const target = this.dataset.target;

        // On dde à supprimer le targer (div id =block_")
        $(target).remove();
    });
}

// Fonction qui doit prendre nen compte le nombre d'image déja présente au niveau de l'id générer
function updateCounter() {

    // on récupère les div.form-group dont l'id est ad_images
    // +$ pour préciser que l'on souhaite récuprer cette expression sous forme de nombres
    const count = +$('#ad_images div.form-group').length;

    // Mise a jour du compteur
    $('#widgets-counter').val(count);
}

// On appel la fonction pour compter le nombre de champs déja présent afin d'incrémenter l'id avec la bonne valeur
updateCounter();

// On appel la fonction du boutton supprimer dès le chargement de la page pour editer des images déja créer
handleDeleteButtons();