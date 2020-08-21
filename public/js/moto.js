$(function () {
    const $link = $('a.js-like');
    $link.on("click", function (e) {
        // affichage ou non du gif coeur
        $('#heart').toggle();
        //changement du text du boutton
        $("button.favorite").text($('#heart').attr('style') === "display: none;" ? "Ajouter aux favoris" : "Retirer des favoris");
            // Suppresion de la redirection vers le href du lien
            e.preventDefault();
            const url = this.href;
            const $countlike = $('td.likeCount');
            axios.get(url).then(function (response) {
                $countlike.text(response.data.likes);
                window.alert(response.data.message);
            })
    });
});


