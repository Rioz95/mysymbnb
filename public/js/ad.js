$('#add-image').click(function () { // je vais recupéré le numéro de futur champs que je vais créer
    const index = + $('#widgets-counter').val();
    console.log(index);

    // je recupère le prototype des entrées
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);


    // j'injecte ce code aun seins de la div
    $('#ad_images').append(tmpl);
    $('#widgets-counter').val(index + 1);

    // je gère le boutons supprimer
    handleDeleteButtons();

});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {

        const target = this.dataset.target;

        $(target).remove();
    });
}

function updateCounter() {
    const count = + $('#ad_images div.form-group').length;
    $('#widgets-counter').val(count);
}


updateCounter();
handleDeleteButtons();
