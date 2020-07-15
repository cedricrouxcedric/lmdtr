$(function () {
    let $links = $("a[data-delete]");
    console.log('ready');
    $links.on("click", function (e) {
            // Suppresion de la redirection vers le href du lien
            e.preventDefault();
            // Demande de confirmation
            if (confirm("Voulez-vous supprimer cette photo ?")) {
                // On envoie une requete ajax vers le href du lien avec la méthode DELETE
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        'X-Requested-With': "XMLHttpRequest",
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({"_token": this.dataset.token})
                }).then(
                    // Recuperation de la reponse en JSON
                    response => response.json()
                ).then(data => {
                    if (data.success) {
                        this.parentElement.remove();
                    } else {
                        console.log(data.error);
                    }
                }).catch(e => alert(e))
            }
        }
    )
})
