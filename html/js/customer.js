import { Validate } from "./Validate.js";
import { Requests } from "./Requests.js";
import { ValidationRules } from "./ValidationRules.js";

document.addEventListener("DOMContentLoaded", () => {
  const cpfInput = document.getElementById("cpf");
  const celularInput = document.getElementById("celular");

  // aplica máscaras e validações específicas
  if (cpfInput) ValidationRules.aplicarMascaraCPF(cpfInput);
  if (celularInput) ValidationRules.aplicarMascaraCelular(celularInput);

  // evento salvar
  const Salvar = document.getElementById("salvar");
  Salvar.addEventListener("click", async () => {
    Validate.SetForm('form').Validate();
    const response = await Requests.SetForm('form').Post('/cliente/insert');
    console.log(response);
  });
});
