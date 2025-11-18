import { Validate } from "./Validate.js";
import { Requests } from "./Requests.js";

const InsertButton = document.getElementById('insert');

$('#cpf').inputmask({ "mask": ["999.999.999-99", "99.999.999/9999-99"] });

InsertButton.addEventListener('click', async () => {
    const IsValid = Validate
        .SetForm('form')
        .Validate();
});


const Salvar = document.getElementById('salvar');

Salvar.addEventListener('click', async () => {
    Validate.SetForm('form').Validate();
    const response = Requests.SetForm('form').Post('/cliente/insert');
    console.log(response);
});