$(function () {
    function hideOrSeeInfos(sel1,sel2) {
        sel1.on('click', function () {
            $(this).toggleClass('btn-dark btn-primary')
            sel2.toggle('slow');
        })
    }

    hideOrSeeInfos($('#motosenventes'),$('#annoncesmoto'));
    hideOrSeeInfos($('#motosfavorites'),$('#favoritesliste'));
    hideOrSeeInfos($('#piecesenventes'),$('#annoncespieces'));
    hideOrSeeInfos($('#sujetouverts'),$('#sujet'));
    hideOrSeeInfos($('#commentaireposte'),$('#commentaires'));

});
