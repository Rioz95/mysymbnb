// on recupère le bouton dee chargement
const btnCharger = document.getElementById("ajoutimage");
btnCharger.addEventListener("click", lanceParcourir, false);

//on recupère le champ d'upload
const fileUpload = document.getElementById("imageFile")
fileUpload.addEventListener("change", affichImage, false)

//On recupere le champ image qui affiche l'image
const imageAffichee = document.getElementById("imageAffichee");

function lanceParcourir() {
    fileUpload.click()
}

function affichImage() {
    const imageChargee = this.files[0];
    const urlImageChargee = URL.createObjectURL(imageChargee);
    imageAffichee.setAttribute("src", urlImageChargee);
}