$(function () {
    function removeAlert(){
        window.setTimeout(function() {
            $(".alert").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove();
            });
        }, 4000);
    }
    removeAlert();
    let $links = $("a[data-delete]");
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
                        $( ".here" ).prepend( "<div class='alert alert-success'>Photo supprimée</div>" );
                        removeAlert();
                    } else {
                        console.log(data.error);
                    }
                }).catch(e => alert(e))
            }
        }
    )

})
