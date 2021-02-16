$(document).on('change', '#location_departement', function () {
    let $departementfield = $('#location_departement')
    let $townfield = $('#location_town')
    let data = {}
    data[$departementfield.attr('id')] = $departementfield.val()

    let url = Routing.generate('get_Towns', {'departement': $departementfield.val()})
    axios.get(url).then(function (response) {
        let option = $('<option></option>').attr("value");

        $townfield.empty().append(option);
        let newOptions = response.data;
        $townfield.empty();
        $townfield.prepend($("<option></option>").text("choisisez votre ville"))
        $.each(newOptions, function (key, value) {
            $townfield.append($("<option></option>")
                .attr(value).text(response.data[key].name));
        })
    });
})
