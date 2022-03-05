import {Controller} from 'stimulus';
import Swal from 'sweetalert2';
import {useDispatch} from 'stimulus-use';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        title: String, text: String, icon: String, confirmButtonText: String, submitAsync: Boolean, url: String,
    }
    static targets = ['results'];

    connect() {
        useDispatch(this, {debug: true});
    }

    onSubmit(event) {
        event.preventDefault();

        Swal.fire({
            title: 'Neukunde',
            html: `
                <input type="text" id="firma" class="swal2-input" placeholder="Firma">
                <input type="text" id="street" class="swal2-input" placeholder="Strasse/Hausnummer">
                <input type="text" id="postcode" class="swal2-input" placeholder="PLZ">
                <input type="text" id="city" class="swal2-input" placeholder="Stadt">
                <input type="text" id="country" class="swal2-input" placeholder="Land">
                `,
            confirmButtonText: 'Anlegen',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            showCancelButton: true,
            focusConfirm: false,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const firma = Swal.getPopup().querySelector('#firma').value
                const street = Swal.getPopup().querySelector('#street').value
                const postcode = Swal.getPopup().querySelector('#postcode').value
                const city = Swal.getPopup().querySelector('#city').value
                const country = Swal.getPopup().querySelector('#country').value

                if (!firma || !street || !postcode || !city || !country) {
                    Swal.showValidationMessage(`Bitte die Daten vollständig ausfüllen`)
                }
                return this.submitForm(firma, street, postcode, city, country);
            }
        });

    }

    async submitForm(firma, street, postcode, city, country) {

        const response = await fetch(this.urlValue, {
            method: "POST",
            body: new URLSearchParams({firma: firma, street: street, postcode: postcode, city: city, country: country})
        });

        this.resultsTarget.innerHTML = await response.text();
        this.dispatch('async:submitted');

        $("select.flexselect").flexselect({hideDropdownOnEmptyInput: true});
        $("#end_customer_form_endCustomer_flexselect").val('');
    }
}
